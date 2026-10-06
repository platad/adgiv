<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AnalysisV2Controller extends Controller
{
    public function create()
    {
        return view('analysis.v2.create');
    }

    public function initialize(Request $request)
    {
        $validated = $request->validate([
            'title'  => ['required', 'string', 'max:255'],
            'locale' => ['required', 'string', 'in:id,en,zh'],
            'audio'  => ['required', 'file', 'mimes:wav,mp3,m4a', 'max:20480'],
        ]);

        $analysis = Analysis::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'locale' => $validated['locale'],
            'version' => 2, // Marker V2
            'status' => 'processing',
        ]);

        $file = $request->file('audio');
        $path = $file->storeAs("audio/" . Auth::id() . "/{$analysis->slug}", "full_audio." . $file->getClientOriginalExtension(), 'local');
        $analysis->update(['audio_path' => $path]);

        // Note: VPS akan menarik file ini, atau kita bisa passing URL-nya
        return redirect()->route('analysis.v2.processing', $analysis->slug);
    }

    public function processing(Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);
        if ($analysis->isCompleted()) {
            return redirect()->route('analysis.v2.result', $analysis->slug);
        }
        return view('analysis.v2.processing', compact('analysis'));
    }

    public function webhookResult(Request $request, Analysis $analysis)
    {
        // Endpoint ini dipanggil oleh VPS setelah RoBERTa selesai
        $transcription = $request->input('transcription', []);
        $graphData = $request->input('graph_data', []);
        
        $analysis->update([
            'status' => 'completed',
            'result_data' => ['transcription' => $transcription],
            'graph_data' => $graphData
        ]);

        return response()->json(['status' => 'success']);
    }

    public function result(Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);
        if (!$analysis->isCompleted()) {
            return redirect()->route('analysis.v2.processing', $analysis->slug);
        }
        return view('analysis.v2.result', compact('analysis'));
    }
}