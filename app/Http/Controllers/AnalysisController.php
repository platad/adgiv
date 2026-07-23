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
            'audio_path'             => '', // Akan diupdate setelah simpan
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

    public function processAudio(Request $request, Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id(), 403);

        // Mencegah PHP timeout saat menunggu proses Whisper yang lama di VPS
        set_time_limit(0); 

        return response()->stream(function () use ($analysis) {
            $client = new Client();
            $audioPath = Storage::disk('local')->path($analysis->audio_path);
            
            try {
                $response = $client->request('POST', 'http://vps.temaniskripsi.id/api/transcribe', [
                    'multipart' => [
                        [
                            'name'     => 'file',
                            'contents' => fopen($audioPath, 'r'),
                            'filename' => basename($audioPath)
                        ]
                    ],
                    'stream' => true,
                    'connect_timeout' => 15,
                    'timeout' => 0,
                ]);

                $body = $response->getBody();
                $transcription = [];
                
                while (!$body->eof()) {
                    $line = '';
                    while (!$body->eof()) {
                        $char = $body->read(1);
                        $line .= $char;
                        if ($char === "\n") break;
                    }
                    
                    if (trim($line) === '') continue;
                    
                    echo $line;
                    flush();
                    
                    $data = json_decode($line, true);
                    if ($data && isset($data['status']) && $data['status'] === 'processing') {
                        $startMin = floor($data['start'] / 60);
                        $startSec = floor($data['start'] % 60);
                        $endMin = floor($data['end'] / 60);
                        $endSec = floor($data['end'] % 60);
                        
                        $transcription[] = [
                            'text_html' => htmlspecialchars($data['text']),
                            'speaker' => 'Unknown',
                            'timestamp' => sprintf('%02d:%02d - %02d:%02d', $startMin, $startSec, $endMin, $endSec),
                        ];
                    }
                }
                
                // Jika selesai tanpa error, simpan data ke database
                $analysis->update([
                    'status' => 'completed',
                    'result_data' => [
                        'total_chunks' => 1,
                        'transcription' => $transcription
                    ]
                ]);
                
            } catch (\Exception $e) {
                echo json_encode(['status' => 'error', 'message' => $e->getMessage()]) . "\n";
                flush();
                
                $analysis->update([
                    'status' => 'failed'
                ]);
            }
        }, 200, [
            'Content-Type' => 'application/x-ndjson',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no', // Disable buffering for Nginx
        ]);
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
