<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Models\AnalysisChunk;
use App\Models\AnalysisLog;
use App\Models\AnalysisFeedback;
use App\Contracts\AI\MultiModalAnalysisInterface;
use App\Actions\ParseAdviceGivingAction;
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
            'locale' => ['required', 'string', 'in:id,en,zh'],
            'audio'  => ['required', 'file', 'mimes:wav,mp3,mp4,mpeg,mpga,m4a,webm,ogg,flac,aac', 'max:20480'],
        ]);

        // Bahasa yang DIPILIH user — bukan bahasa browser
        $locale = $validated['locale'];
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
            $callbackUrl = 'https://temaniskripsi.id/api/webhook/' . $analysis->slug;
            $originalFileName = $request->file('audio')->getClientOriginalName();

            $response = $client->request('POST', $vpsUrl, [
                'multipart' => [
                    [
                        'name'     => 'file',
                        'contents' => fopen(Storage::disk('local')->path($path), 'r'),
                        'filename' => $originalFileName
                    ],
                    [
                        'name'     => 'callback_url',
                        'contents' => $callbackUrl
                    ],
                    [
                        'name'     => 'language',
                        'contents' => $locale
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
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);

        if ($analysis->isCompleted()) {
            return redirect()->route('analysis.result', ['analysis' => $analysis->slug]);
        }

        $analysis->load('chunks', 'logs');
        
        $initialBatchHistory = $analysis->result_data['batch_history'] ?? [];
        $resumeStartIdx = 0;
        if (!empty($initialBatchHistory)) {
            $lastBatch = end($initialBatchHistory);
            $resumeStartIdx = $lastBatch['end_idx'] + 1;
        }

        return view('analysis.processing', compact('analysis', 'initialBatchHistory', 'resumeStartIdx'));
    }

    public function getAudio(Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);
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
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);
        
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
        $resultData = $analysis->result_data;
        return response()->json([
            'status' => $analysis->status,
            'is_completed' => $analysis->isCompleted(),
            'result_data' => $resultData,
            'vps_message' => $resultData['vps_message'] ?? '',
            'total_segments' => $resultData['total_segments'] ?? 0,
            'total_duration_sec' => $resultData['total_duration_sec'] ?? 0,
            'progress' => $resultData['progress'] ?? 0,
            'vps_logs' => $resultData['vps_logs'] ?? [],
        ]);
    }

    public function webhookResult(Request $request, Analysis $analysis, MultiModalAnalysisInterface $aiService, ParseAdviceGivingAction $parseAction)
    {
        $status = $request->input('status', 'completed');
        $transcription = $request->input('transcription', []);
        $progress = $request->input('progress', 0);
        $message = $request->input('message', '');
        $logs = $request->input('logs', []);
        $error = $request->input('error');
        $totalSegments = $request->input('total_segments', 0);
        $totalDuration = $request->input('total_duration_sec', 0);

        if (!empty($logs) && is_array($logs)) {
            $analysisLogs = AnalysisLog::where('analysis_id', $analysis->id)->get();
            $existingKeys = $analysisLogs->map(function ($l) {
                return md5($l->type . '|' . json_encode($l->meta));
            })->toArray();

            foreach ($logs as $log) {
                $logTime = $log['time'] ?? '';
                $logMsg = $log['msg'] ?? '';
                $logKey = md5($logMsg . $logTime);

                if (!in_array($logKey, $existingKeys)) {
                    AnalysisLog::info(
                        $analysis->id,
                        'vps_progress',
                        $logMsg,
                        [
                            'vps_time' => $logTime,
                            'raw' => $log
                        ]
                    );
                }
            }
        }

        // === SIMPAN PROGRESS KE DATABASE ===
        $analysis->update([
            'result_data' => [
                'total_chunks' => 1,
                'progress' => $progress,
                'transcription' => $transcription,
                'vps_message' => $message,
                'vps_logs' => $logs,
                'total_segments' => $totalSegments,
                'total_duration_sec' => $totalDuration,
            ]
        ]);

        if (empty($transcription) && $status !== 'progress') {
            AnalysisLog::error($analysis->id, 'webhook_failed', 'VPS mengirim webhook tanpa data transkripsi atau error.', [
                'payload' => $request->all(),
                'error' => $error
            ]);
            $analysis->update(['status' => 'failed']);
            return response()->json(['status' => 'error', 'message' => 'No transcription data'], 400);
        }

        if ($status === 'progress') {
            // Progress update — tetap simpan transcription jika ada (partial results)
            if (!empty($transcription)) {
                $analysis->update([
                    'result_data' => [
                        'total_chunks' => 1,
                        'transcription' => $transcription,
                        'progress' => $progress,
                        'vps_message' => $message,
                        'vps_logs' => $logs,
                        'total_segments' => $totalSegments,
                        'total_duration_sec' => $totalDuration,
                    ]
                ]);
            }
            return response()->json(['status' => 'success_progress']);
        }

        try {
            $analysis->update([
                'status' => 'analyzing', // NEW: We transition to analyzing so frontend knows it's time to process AI chunks
                'result_data' => [
                    'total_chunks' => 1,
                    'transcription' => $transcription,
                    'progress' => 80,
                    'vps_message' => 'VPS Selesai. Memulai Analisis AI...',
                    'vps_logs' => $logs,
                    'total_segments' => $totalSegments,
                    'total_duration_sec' => $totalDuration,
                ]
            ]);

            AnalysisLog::success($analysis->id, 'webhook_success', 'Transkripsi berhasil diproses via Webhook. Menunggu frontend memulai chunk AI.');

        } catch (\Exception $e) {
            Log::error('AI Synthesis Error on Webhook: ' . $e->getMessage());

            $analysis->update([
                'status' => 'failed',
                'result_data' => [
                    'total_chunks' => 1,
                    'transcription' => $transcription,
                    'progress' => 80,
                    'vps_message' => 'Gagal menyimpan transkripsi',
                    'vps_logs' => $logs,
                    'total_segments' => $totalSegments,
                    'total_duration_sec' => $totalDuration,
                ]
            ]);
            AnalysisLog::error($analysis->id, 'webhook_failed', 'Transkripsi berhasil, namun gagal menyimpan: ' . $e->getMessage());
        }

        return response()->json(['status' => 'success']);
    }

    public function processChunk(Request $request, Analysis $analysis, MultiModalAnalysisInterface $aiService, ParseAdviceGivingAction $parseAction)
    {
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);

        $request->validate([
            'start_idx' => 'required|integer|min:0',
            'end_idx' => 'required|integer|min:0',
        ]);

        $startIdx = (int) $request->input('start_idx');
        $endIdx = (int) $request->input('end_idx');

        $resultData = $analysis->result_data ?? [];
        $transcription = $resultData['transcription'] ?? [];

        // Extract chunk
        $chunk = array_slice($transcription, $startIdx, $endIdx - $startIdx + 1);
        if (empty($chunk)) {
            return response()->json(['status' => 'success', 'message' => 'No segments in chunk']);
        }

        try {
            // Process chunk with OpenAI
            $rawAiResponse = $aiService->synthesizeChunks($chunk, $analysis->locale);
            $finalData = $parseAction->execute($rawAiResponse);

            // Merge parsed data back into the main transcription array
            $enrichedChunk = $finalData['transcription'] ?? $chunk;
            
            // Loop through enriched chunk and replace original elements
            // We use standard numerical indexing since we sliced
            foreach ($enrichedChunk as $idx => $enrichedItem) {
                $originalIdx = $startIdx + $idx;
                if (isset($transcription[$originalIdx])) {
                    $transcription[$originalIdx] = array_merge($transcription[$originalIdx], $enrichedItem);
                }
            }

            // Save debug info to batch history
            $batchHistory = $resultData['batch_history'] ?? [];
            $batchHistory[] = [
                'start_idx' => $startIdx,
                'end_idx' => $endIdx,
                'timestamp' => now()->toIso8601String(),
                'system_prompt' => $rawAiResponse['debug']['system_prompt'] ?? '',
                'user_prompt' => $rawAiResponse['debug']['user_prompt'] ?? '',
                'raw_response' => $rawAiResponse['debug']['raw_response'] ?? ''
            ];
            $resultData['batch_history'] = $batchHistory;

            // Update result data
            $resultData['transcription'] = $transcription;
            
            // We can just keep the last chunk's summary
            if (!empty($finalData['summary'])) {
                $resultData['summary'] = $finalData['summary'];
            }

            $analysis->update(['result_data' => $resultData]);

            return response()->json([
                'status' => 'success', 
                'processed' => count($enrichedChunk),
                'batch_history' => $batchHistory
            ]);
        } catch (\Exception $e) {
            Log::error('AI Synthesis Error on Chunk: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function finalizeAnalysis(Request $request, Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);

        $resultData = $analysis->result_data ?? [];
        $resultData['progress'] = 100;
        
        $analysis->update([
            'status' => 'completed',
            'result_data' => $resultData
        ]);

        AnalysisLog::success($analysis->id, 'ai_completed', 'Analisis AI via batch frontend berhasil diselesaikan.');

        return response()->json(['status' => 'success']);
    }

    public function result(Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);

        if (!$analysis->isCompleted()) {
            return redirect()->route('analysis.processing', $analysis->slug);
        }

        $analysis->load('feedback', 'chunks', 'logs');
        return view('analysis.result', compact('analysis'));
    }

    public function feedback(Request $request, Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);

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
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);

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
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);

        if (!$analysis->isCompleted()) {
            return redirect()->route('analysis.result', ['analysis' => $analysis->slug])
                ->with('error', 'Laporan belum siap dicetak.');
        }

        return view('analysis.print', compact('analysis'));
    }

    public function destroy(Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);

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
