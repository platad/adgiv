<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Models\AnalysisFeedback;
use App\Services\AudioStorageService;
use App\Contracts\AI\MultiModalAnalysisInterface;
use App\Http\Requests\StoreAnalysisRequest;
use App\Http\Requests\StoreAnalysisFeedbackRequest;
use App\Actions\ParseAdviceGivingAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnalysisController extends Controller
{
    public function __construct(
        private readonly AudioStorageService $audioStorage,
        private readonly MultiModalAnalysisInterface $aiService,
        private readonly ParseAdviceGivingAction $parseAdviceAction
    ) {}

    public function create()
    {
        return view('analysis.create');
    }

    public function initialize(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $analysis = Analysis::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'audio_path' => 'client-cached',
            'status' => 'pending',
            'result_data' => ['chunks' => []],
        ]);

        return response()->json([
            'status' => 'success',
            'analysis_id' => $analysis->id
        ]);
    }

    public function storeChunk(Request $request, $id)
    {
        $analysis = Analysis::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'audio_chunk' => ['required', 'file'],
        ]);

        $file = $request->file('audio_chunk');
        $tempPath = $file->store('temp', 'local');
        $absolutePath = Storage::disk('local')->path($tempPath);

        try {
            $rawResult = $this->aiService->analyzeAudio($absolutePath);
            
            @unlink($absolutePath);

            $resultData = $analysis->result_data ?? [];
            if (!isset($resultData['chunks'])) {
                $resultData['chunks'] = [];
            }
            $resultData['chunks'][] = $rawResult;

            $analysis->update([
                'result_data' => $resultData
            ]);

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            @unlink($absolutePath);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function store(StoreAnalysisRequest $request)
    {
        $audioPath = $this->audioStorage->storeAudio($request->file('audio'));

        $analysis = Analysis::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'audio_path' => $audioPath,
            'status' => 'pending',
            'result_data' => ['chunks' => []]
        ]);

        return redirect()->route('analysis.result', $analysis->id);
    }

    public function processing($id)
    {
        $analysis = Analysis::where('user_id', Auth::id())->findOrFail($id);
        
        return view('analysis.processing', compact('analysis'));
    }

    public function processAudio($id)
    {
        $analysis = Analysis::where('user_id', Auth::id())->findOrFail($id);
        
        try {
            $analysis->update(['status' => 'processing']);
            
            $resultData = $analysis->result_data ?? [];
            $chunks = $resultData['chunks'] ?? [];

            if (empty($chunks)) {
                throw new \RuntimeException('Tidak ditemukan data potongan audio untuk diproses.');
            }

            $synthesizedResult = $this->aiService->synthesizeChunks($chunks);
            $finalResult = $this->parseAdviceAction->execute($synthesizedResult);
            $finalResult['total_chunks'] = count($chunks);
            $analysis->update([
                'status' => 'completed',
                'result_data' => $finalResult
            ]);

            return response()->json(['status' => 'success', 'redirect' => route('analysis.result', $analysis->id)]);
        } catch (\Exception $e) {
            $analysis->update(['status' => 'failed']);
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function result($id)
    {
        $analysis = Analysis::with('feedback')->where('user_id', Auth::id())->findOrFail($id);
        
        if ($analysis->status !== 'completed') {
            return redirect()->route('analysis.processing', $analysis->id);
        }
        
        return view('analysis.result', compact('analysis'));
    }

    public function feedback(StoreAnalysisFeedbackRequest $request, $id)
    {
        $analysis = Analysis::where('user_id', Auth::id())->findOrFail($id);

        $feedback = AnalysisFeedback::updateOrCreate(
            ['analysis_id' => $analysis->id],
            [
                'user_id' => Auth::id(),
                'is_accurate' => $request->is_accurate,
                'comments' => $request->comments
            ]
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Terima kasih atas penilaian Anda. Metrik sistem telah diperbarui.',
                'feedback' => $feedback
            ]);
        }

        return back()->with('success', 'Terima kasih atas penilaian Anda. Metrik sistem telah diperbarui.');
    }

    public function lineFeedback(Request $request, $id)
    {
        $request->validate([
            'index' => 'required|integer',
            'type' => 'required|in:up,down,none'
        ]);

        $analysis = Analysis::where('user_id', Auth::id())->findOrFail($id);
        $resultData = $analysis->result_data;

        if (!isset($resultData['transcription'][$request->index])) {
            return response()->json(['status' => 'error', 'message' => 'Line not found.'], 404);
        }

        $currentFeedback = $resultData['transcription'][$request->index]['user_feedback'] ?? 'none';
        
        if ($currentFeedback === $request->type) {
            $resultData['transcription'][$request->index]['user_feedback'] = 'none'; // toggle off
            unset($resultData['transcription'][$request->index]['feedback_at']);
        } else {
            $resultData['transcription'][$request->index]['user_feedback'] = $request->type;
            $resultData['transcription'][$request->index]['feedback_at'] = now()->toIso8601String();
        }

        $analysis->update(['result_data' => $resultData]);

        return response()->json([
            'status' => 'success',
            'message' => 'Feedback berhasil disimpan.',
            'user_feedback' => $resultData['transcription'][$request->index]['user_feedback']
        ]);
    }

    public function printReport($id)
    {
        $analysis = Analysis::where('user_id', Auth::id())->findOrFail($id);
        
        if ($analysis->status !== 'completed') {
            return redirect()->route('analysis.result', $id)
                ->with('error', 'Laporan belum siap dicetak. Silakan tunggu hingga analisis selesai.');
        }

        return view('analysis.print', compact('analysis'));
    }

    public function destroy($id)
    {
        $analysis = Analysis::where('user_id', Auth::id())->findOrFail($id);
        
        if ($analysis->audio_path) {
            Storage::delete($analysis->audio_path);
        }

        $analysis->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Analisis berhasil dihapus.'
        ]);
    }
}
