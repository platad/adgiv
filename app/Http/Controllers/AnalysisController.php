<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Models\AnalysisChunk;
use App\Models\AnalysisLog;
use App\Models\AnalysisFeedback;
use App\Services\AI\OpenAiMultiModalService;
use App\Actions\ParseAdviceGivingAction;
use App\Contracts\AI\MultiModalAnalysisInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalysisController extends Controller
{
    public function __construct(
        private readonly MultiModalAnalysisInterface $aiService,
        private readonly ParseAdviceGivingAction     $parseAdviceAction
    ) {}

    public function create()
    {
        return view('analysis.create');
    }

    public function initialize(Request $request)
    {
        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'locale'           => ['nullable', 'string', 'in:id,en,zh'],
            'total_chunks'     => ['required', 'integer', 'min:1'],
            'duration_seconds' => ['required', 'numeric', 'min:0.1'],
        ]);

        $locale = $validated['locale'] ?? 'id';
        $userId = Auth::id();
        
        $analysis = Analysis::create([
            'user_id'                => $userId,
            'title'                  => $validated['title'],
            'locale'                 => $locale,
            'audio_path'             => 'client-side-sliced',
            'audio_duration_seconds' => $validated['duration_seconds'],
            'total_chunks'           => $validated['total_chunks'],
            'processed_chunks'       => 0,
            'status'                 => 'processing',
            'model_used'             => config('services.openai.audio_model', 'gpt-audio-1.5'),
            'synthesis_model'        => config('services.openai.synthesis_model', 'gpt-4o-mini'),
        ]);

        AnalysisLog::success($analysis->id, 'session_created',
            "Sesi Analisis dibuat. Total potongan: {$validated['total_chunks']}, Durasi: {$validated['duration_seconds']}s",
            [
                'total_chunks' => $validated['total_chunks'],
                'duration_seconds' => $validated['duration_seconds'],
            ]
        );

        return response()->json([
            'status' => 'success',
            'slug'   => $analysis->slug,
            'redirect' => route('analysis.processing', $analysis->slug)
        ]);
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

    public function processChunk(Request $request, Analysis $analysis)
    {
        Log::info('[DEBUG] processChunk reached for slug: ' . $analysis->slug . ' by Auth: ' . Auth::id() . ' Analysis User: ' . $analysis->user_id);
        abort_if($analysis->user_id != Auth::id(), 403);

        $validated = $request->validate([
            'audio' => ['required', 'file', 'mimes:wav,mp3,webm,ogg', 'max:51200'], // max 50MB per chunk
            'chunk_index' => ['required', 'integer', 'min:1'],
        ]);

        $file = $request->file('audio');
        $chunkIndex = $validated['chunk_index'];
        
        $path = $file->storeAs(
            "audio/{$analysis->user_id}/{$analysis->slug}",
            "chunk_{$chunkIndex}.wav",
            'local'
        );

        $chunk = AnalysisChunk::updateOrCreate(
            [
                'analysis_id' => $analysis->id,
                'chunk_index' => $chunkIndex,
            ],
            [
                'total_chunks' => $analysis->total_chunks,
                'chunk_path' => $path,
                'chunk_size_bytes' => $file->getSize(),
                'status' => 'running',
                'model_used' => $analysis->model_used,
                'started_at' => now(),
            ]
        );

        $locale = $analysis->locale ?? 'id';
        $startTime = microtime(true);
        $systemPrompt = $this->aiService->getConfig()->getSystemPrompt($locale);
        $chunk->update(['prompt_used' => $systemPrompt]);

        try {
            set_time_limit(300); // Mencegah server cPanel mematikan proses jika AI merespons agak lama
            $absolutePath = Storage::disk('local')->path($path);
            Log::info('[DEBUG] Processing absolute path: ' . $absolutePath);
            $result = $this->callOpenAiWithRetry($absolutePath, $locale, 3);
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            $chunk->update([
                'status'       => 'done',
                'result_data'  => $result,
                'raw_response' => json_encode($result),
                'duration_ms'  => $durationMs,
                'completed_at' => now(),
            ]);

            $analysis->update([
                'processed_chunks' => AnalysisChunk::where('analysis_id', $analysis->id)->where('status', 'done')->count(),
            ]);

            return response()->json([
                'status' => 'success',
                'chunk_index' => $chunkIndex,
                'result' => $result
            ]);

        } catch (\Throwable $e) {
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);
            $chunk->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'duration_ms'   => $durationMs,
            ]);

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'chunk_index' => $chunkIndex
            ], 500);
        }
    }

    public function finalize(Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id(), 403);
        
        $chunks = AnalysisChunk::where('analysis_id', $analysis->id)
            ->where('status', 'done')
            ->orderBy('chunk_index')
            ->pluck('result_data')
            ->filter()
            ->values()
            ->toArray();

        if (empty($chunks)) {
            return response()->json(['status' => 'error', 'message' => 'Tidak ada chunk yang berhasil.'], 400);
        }

        $locale = $analysis->locale ?? 'id';
        
        try {
            set_time_limit(300);
            $synthesizedResult = $this->aiService->synthesizeChunks($chunks, $locale);
            $finalResult = $this->parseAdviceAction->execute($synthesizedResult);
            $finalResult['total_chunks'] = count($chunks);

            $analysis->update([
                'status'      => 'completed',
                'result_data' => $finalResult,
            ]);

            return response()->json([
                'status' => 'success',
                'redirect' => route('analysis.result', $analysis->slug)
            ]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function callOpenAiWithRetry(string $chunkPath, string $locale, int $maxRetries): array
    {
        $lastException = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                return $this->aiService->analyzeAudio($chunkPath, $locale);
            } catch (\Throwable $e) {
                $lastException = $e;
                Log::warning("[SSE] OpenAI attempt {$attempt} gagal: " . $e->getMessage());
                if ($attempt < $maxRetries) {
                    sleep(2 ** $attempt);
                }
            }
        }

        throw $lastException;
    }



    public function resumeChunk(Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id(), 403);
        abort_unless($analysis->isFailed() || $analysis->isResumable(), 422);

        DB::statement('CALL sp_reset_failed_chunks(?)', [$analysis->id]);

        return redirect()->route('analysis.processing', $analysis->slug)
            ->with('info', 'Analisis dilanjutkan dari potongan yang gagal.');
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
