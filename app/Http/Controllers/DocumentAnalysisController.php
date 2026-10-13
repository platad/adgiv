<?php

namespace App\Http\Controllers;

use App\Models\DocumentAnalysis;
use App\Services\Document\PdfTextExtractorService;
use App\Services\Document\OpenAiDocumentAnalysisService;
use App\Services\Document\DocumentAudioService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentAnalysisController extends Controller
{
    /**
     * Tampilkan form upload dokumen PDF.
     */
    public function create()
    {
        return view('document_analysis.create');
    }

    /**
     * Inisialisasi unggahan PDF dan siapkan sesi analisis.
     */
    public function initialize(Request $request)
    {
        $validated = $request->validate([
            'title'    => ['required', 'string', 'max:255'],
            'locale'   => ['required', 'string', 'in:id,en,zh'],
            'pdf_file' => ['required', 'file', 'mimes:pdf', 'max:10240'], // Maksimal 1 file PDF < 10MB
        ], [
            'pdf_file.required' => 'Silakan pilih berkas PDF untuk dianalisis.',
            'pdf_file.mimes'    => 'Format berkas harus berupa dokumen PDF (.pdf).',
            'pdf_file.max'      => 'Ukuran berkas PDF tidak boleh melebihi 10 MB.',
        ]);

        $file = $request->file('pdf_file');
        $userId = Auth::id();
        $originalName = $file->getClientOriginalName();
        $fileSize = $file->getSize();

        // Buat record model terlebih dahulu untuk menghasilkan unique slug
        $doc = new DocumentAnalysis();
        $doc->user_id = $userId;
        $doc->title = $validated['title'];
        $doc->locale = $validated['locale'];
        $doc->file_name = $originalName;
        $doc->file_size = $fileSize;
        $doc->pdf_path = '';
        $doc->status = 'pending';
        $doc->save();

        // Simpan file PDF ke storage lokal yang aman
        $storagePath = "documents/{$userId}/{$doc->slug}/" . time() . "_{$originalName}";
        Storage::disk('local')->put($storagePath, file_get_contents($file->getRealPath()));

        $doc->update(['pdf_path' => $storagePath]);

        Log::info('[DocumentAnalysis] Berkas PDF berhasil diunggah', [
            'id' => $doc->id,
            'slug' => $doc->slug,
            'size' => $fileSize
        ]);

        return redirect()->route('document_analysis.processing', [
            'documentAnalysis' => $doc->slug,
            'locale' => app()->getLocale()
        ]);
    }

    /**
     * Halaman antarmuka visual proses analisis dokumen.
     */
    public function processing(DocumentAnalysis $documentAnalysis)
    {
        abort_if($documentAnalysis->user_id !== Auth::id() && !Auth::user()->is_admin, 403);

        if ($documentAnalysis->isCompleted()) {
            return redirect()->route('document_analysis.result', [
                'documentAnalysis' => $documentAnalysis->slug,
                'locale' => app()->getLocale()
            ]);
        }

        return view('document_analysis.processing', compact('documentAnalysis'));
    }

    /**
     * Eksekusi pipeline analisis PDF secara bertahap via asynchronous client trigger.
     */
    public function execute(
        DocumentAnalysis $documentAnalysis,
        PdfTextExtractorService $extractor,
        OpenAiDocumentAnalysisService $analyzer,
        DocumentAudioService $audioService
    ) {
        abort_if($documentAnalysis->user_id !== Auth::id() && !Auth::user()->is_admin, 403);

        if ($documentAnalysis->isCompleted()) {
            return response()->json([
                'success' => true,
                'status' => 'completed',
                'redirect_url' => route('document_analysis.result', [
                    'documentAnalysis' => $documentAnalysis->slug,
                    'locale' => app()->getLocale()
                ])
            ]);
        }

        try {
            // --- TAHAP 1: EKSTRAKSI TEKS PDF ---
            $documentAnalysis->update(['status' => 'extracting']);
            $absolutePdfPath = Storage::disk('local')->path($documentAnalysis->pdf_path);

            $extracted = $extractor->extract($absolutePdfPath);
            $documentAnalysis->update([
                'total_pages' => $extracted['page_count'],
                'word_count'  => $extracted['word_count'],
            ]);

            // --- TAHAP 2: ANALISIS AI MULTI-TASK (GPT-4o-mini) ---
            $documentAnalysis->update(['status' => 'analyzing']);
            $aiResult = $analyzer->analyze(
                $extracted['text'],
                $documentAnalysis->title,
                $documentAnalysis->locale
            );

            // Simpan hasil analisis ke DB
            $documentAnalysis->update([
                'executive_summary' => $aiResult['executive_summary'],
                'key_conclusions'   => $aiResult['key_conclusions'],
                'recommendations'   => $aiResult['recommendations'],
                'audio_script'      => $aiResult['audio_script'],
            ]);

            // --- TAHAP 3: SINTESIS BRIEFING AUDIO (OpenAI TTS) ---
            $documentAnalysis->update(['status' => 'synthesizing_audio']);
            $audioUrl = $audioService->generateBriefingAudio(
                $aiResult['audio_script'],
                $documentAnalysis->locale
            );

            $documentAnalysis->update([
                'briefing_audio_path' => $audioUrl,
                'status'              => 'completed',
                'error_message'       => null,
            ]);

            Log::info('[DocumentAnalysis] Analisis dokumen selesai sukses', [
                'slug' => $documentAnalysis->slug,
                'pages' => $extracted['page_count']
            ]);

            return response()->json([
                'success' => true,
                'status' => 'completed',
                'redirect_url' => route('document_analysis.result', [
                    'documentAnalysis' => $documentAnalysis->slug,
                    'locale' => app()->getLocale()
                ])
            ]);

        } catch (\Throwable $e) {
            Log::error('[DocumentAnalysis] Eksekusi analisis dokumen gagal', [
                'slug' => $documentAnalysis->slug,
                'error' => $e->getMessage()
            ]);

            $documentAnalysis->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'status' => 'failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint status polling untuk update realtime antarmuka.
     */
    public function status(DocumentAnalysis $documentAnalysis)
    {
        abort_if($documentAnalysis->user_id !== Auth::id() && !Auth::user()->is_admin, 403);

        return response()->json([
            'status' => $documentAnalysis->status,
            'pages' => $documentAnalysis->total_pages,
            'words' => $documentAnalysis->word_count,
            'error' => $documentAnalysis->error_message,
            'is_completed' => $documentAnalysis->isCompleted(),
            'is_failed' => $documentAnalysis->isFailed(),
            'redirect_url' => $documentAnalysis->isCompleted() ? route('document_analysis.result', [
                'documentAnalysis' => $documentAnalysis->slug,
                'locale' => app()->getLocale()
            ]) : null
        ]);
    }

    /**
     * Tampilkan hasil analisis dokumen bersandingan dengan preview PDF.
     */
    public function result(DocumentAnalysis $documentAnalysis)
    {
        abort_if($documentAnalysis->user_id !== Auth::id() && !Auth::user()->is_admin, 403);

        if (!$documentAnalysis->isCompleted()) {
            return redirect()->route('document_analysis.processing', [
                'documentAnalysis' => $documentAnalysis->slug,
                'locale' => app()->getLocale()
            ]);
        }

        return view('document_analysis.result', compact('documentAnalysis'));
    }

    /**
     * Stream PDF dokumen secara inline untuk iframe preview.
     */
    public function servePdf(DocumentAnalysis $documentAnalysis)
    {
        abort_if($documentAnalysis->user_id !== Auth::id() && !Auth::user()->is_admin, 403);

        if (!Storage::disk('local')->exists($documentAnalysis->pdf_path)) {
            abort(404, 'Berkas PDF tidak ditemukan pada server.');
        }

        $absolutePath = Storage::disk('local')->path($documentAnalysis->pdf_path);

        return response()->file($absolutePath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . addslashes($documentAnalysis->file_name) . '"',
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }

    /**
     * Hapus sesi analisis dokumen dan file terkait.
     */
    public function destroy(DocumentAnalysis $documentAnalysis)
    {
        abort_if($documentAnalysis->user_id !== Auth::id() && !Auth::user()->is_admin, 403);

        if (Storage::disk('local')->exists($documentAnalysis->pdf_path)) {
            Storage::disk('local')->delete($documentAnalysis->pdf_path);
        }

        $documentAnalysis->delete();

        return redirect()->route('dashboard', ['locale' => app()->getLocale()])
            ->with('status', 'Sesi analisis dokumen berhasil dihapus.');
    }
}
