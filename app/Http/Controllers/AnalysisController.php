<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Models\AnalysisChunk;
use App\Models\AnalysisLog;
use App\Models\AnalysisFeedback;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalysisController extends Controller
{

    public function create()
    {
        return view('analysis.create');
    }

    public function initialize(Request $request)
    {
        $validated = $request->validate([
            'title'  => ['required', 'string', 'max:255'],
            'locale' => ['nullable', 'string', 'in:id,en,zh'],
            'audio'  => ['required', 'file', 'mimes:wav,mp3,webm,ogg,aac,m4a,flac', 'max:10240'],
        ]);

        $locale = $validated['locale'] ?? 'id';
        $userId = Auth::id();
        
        $analysis = Analysis::create([
            'user_id'                => $userId,
            'title'                  => $validated['title'],
            'locale'                 => $locale,
            'audio_path'             => '',
            'audio_duration_seconds' => 0,
            'total_chunks'           => 1,
            'processed_chunks'       => 0,
            'status'                 => 'processing',
            'model_used'             => 'vps-faster-whisper',
            'synthesis_model'        => 'none',
        ]);

        $file = $request->file('audio');
        $path = $file->storeAs(
            "audio/{$userId}/{$analysis->slug}",
            "full_audio." . $file->getClientOriginalExtension(),
            'local'
        );

        $analysis->update(['audio_path' => $path]);

        AnalysisLog::success($analysis->id, 'session_created',
            "Sesi Analisis dibuat. File utuh berhasil diunggah.",
            [
                'file_path' => $path,
                'file_size' => $file->getSize()
            ]
        );

        try {
            $client = new Client(['timeout' => 30]);
            $vpsUrl = 'https://vps.temaniskripsi.id/api/transcribe';
            $callbackUrl = route('analysis.webhook', ['locale' => 'en', 'analysis' => $analysis->slug]);

            $response = $client->request('POST', $vpsUrl, [
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => fopen(Storage::disk('local')->path($path), 'r'),
                        'filename' => "audio." . $file->getClientOriginalExtension()
                    ],
                    [
                        'name'     => 'callback_url',
                        'contents' => $callbackUrl
                    ]
                ]
            ]);

            AnalysisLog::info($analysis->id, 'vps_triggered', 'Perintah transkripsi background berhasil dikirim ke VPS.');
        } catch (\Exception $e) {
            AnalysisLog::error($analysis->id, 'vps_error', 'Gagal mengirim perintah ke VPS: ' . $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'success',
                'slug'   => $analysis->slug,
                'redirect' => route('analysis.processing', $analysis->slug)
            ]);
        }

        return redirect()->route('analysis.processing', $analysis->slug);
    }

    public function processing(Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id(), 403);

        if ($analysis->isCompleted()) {
            return redirect()->route('analysis.result', $analysis->slug);
        }

        $analysis->load('chunks', 'logs');

        return view('analysis.processing', compact('analysis'));
    }

    public function getAudio(Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id(), 403);
        $path = Storage::disk('local')->path($analysis->audio_path);
        
        if (!file_exists($path)) {
            abort(404, 'Audio file not found.');
        }

        return response()->file($path, [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, OPTIONS',
        ]);
    }

    public function saveResult(Request $request, Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id(), 403);
        
        $transcription = $request->input('transcription', []);
        
        $analysis->update([
            'status' => 'completed',
            'result_data' => [
                'total_chunks' => 1,
                'transcription' => $transcription
            ]
        ]);
        
        return response()->json(['status' => 'success']);
    }

    public function checkStatus(Analysis $analysis)
    {
        return response()->json([
            'status' => $analysis->status,
            'is_completed' => $analysis->isCompleted(),
            'result_data' => $analysis->result_data
        ]);
    }

    public function webhookResult(Request $request, Analysis $analysis)
    {
        $status = $request->input('status', 'completed'); // 'progress' or 'completed'
        $transcription = $request->input('transcription', []);
        
        if (empty($transcription) && $status !== 'progress') {
            AnalysisLog::error($analysis->id, 'webhook_failed', 'VPS mengirim webhook tanpa data transkripsi atau error.', ['payload' => $request->all()]);
            $analysis->update(['status' => 'failed']);
            return response()->json(['status' => 'error', 'message' => 'No transcription data'], 400);
        }

        if ($status === 'progress') {
            // Update result data but keep status processing
            $analysis->update([
                'result_data' => [
                    'total_chunks' => 1,
                    'transcription' => $transcription
                ]
            ]);
            return response()->json(['status' => 'success_progress']);
        }

        // Jika completed
        $analysis->update([
            'status' => 'completed',
            'result_data' => [
                'total_chunks' => 1,
                'transcription' => $transcription
            ]
        ]);
        
        AnalysisLog::success($analysis->id, 'webhook_success', 'Transkripsi berhasil diterima dari VPS via Webhook secara penuh.');
        
        return response()->json(['status' => 'success']);
    }

    public function result(Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id(), 403);

        if (!$analysis->isCompleted()) {
            return redirect()->route('analysis.processing', $analysis->slug);
        }

        $analysis->load('feedback', 'chunks', 'logs');
        return view('analysis.result', compact('analysis'));
    }

    public function feedback(Request $request, Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id(), 403);

        $request->validate([
            'is_accurate' => ['required', 'boolean'],
            'comments'    => ['nullable', 'string', 'max:2000'],
        ]);

        $feedback = AnalysisFeedback::updateOrCreate(
            ['analysis_id' => $analysis->id],
            [
                'user_id'     => Auth::id(),
                'is_accurate' => $request->is_accurate,
                'comments'    => $request->comments,
            ]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['status' => 'success', 'feedback' => $feedback]);
        }
        return back()->with('success', 'Terima kasih atas penilaian Anda.');
    }

    public function lineFeedback(Request $request, Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id(), 403);

        $request->validate([
            'index' => 'required|integer',
            'type'  => 'required|in:up,down,none',
        ]);

        $resultData = $analysis->result_data;
        if (!isset($resultData['transcription'][$request->index])) {
            return response()->json(['status' => 'error', 'message' => 'Line not found.'], 404);
        }

        $currentFeedback = $resultData['transcription'][$request->index]['user_feedback'] ?? 'none';
        if ($currentFeedback === $request->type) {
            $resultData['transcription'][$request->index]['user_feedback'] = 'none';
            unset($resultData['transcription'][$request->index]['feedback_at']);
        } else {
            $resultData['transcription'][$request->index]['user_feedback'] = $request->type;
            $resultData['transcription'][$request->index]['feedback_at']   = now()->toIso8601String();
        }

        $analysis->update(['result_data' => $resultData]);

        return response()->json([
            'status'        => 'success',
            'user_feedback' => $resultData['transcription'][$request->index]['user_feedback'],
        ]);
    }

    public function printReport(Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id(), 403);

        if (!$analysis->isCompleted()) {
            return redirect()->route('analysis.result', $analysis->slug)
                ->with('error', 'Laporan belum siap dicetak.');
        }

        return view('analysis.print', compact('analysis'));
    }

    public function destroy(Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id(), 403);

        if ($analysis->audio_path && Storage::disk('local')->exists($analysis->audio_path)) {
            Storage::disk('local')->delete($analysis->audio_path);
        }

        $analysis->delete();

        $this->cleanupPendingFiles();

        return response()->json(['status' => 'success', 'message' => 'Analisis berhasil dihapus.']);
    }

    private function cleanupPendingFiles(): void
    {
        $pendingFiles = DB::table('pending_file_deletions')
            ->where('is_processed', false)
            ->limit(50)
            ->get();

        foreach ($pendingFiles as $pending) {
            try {
                if (file_exists($pending->file_path)) {
                    @unlink($pending->file_path);
                }
                DB::table('pending_file_deletions')
                    ->where('id', $pending->id)
                    ->update(['is_processed' => true, 'processed_at' => now()]);
            } catch (\Throwable $e) {
                Log::warning('[Cleanup] Gagal hapus file: ' . $pending->file_path);
            }
        }
    }
}
