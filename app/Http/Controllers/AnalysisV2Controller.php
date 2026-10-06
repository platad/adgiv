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

        // KASUS PENYEBAB 1: DULU DISINI KOSONG. SEKARANG KITA KIRIM AUDIO KE VPS!
        try {
            $client = new \GuzzleHttp\Client(['timeout' => 30, 'verify' => false]);
            // Pastikan domain VPS ini aktif
            $vpsUrl = 'https://vps.temaniskripsi.id/api/v2/transcribe';
            $callbackUrl = 'https://temaniskripsi.id/api/analysis/v2/' . $analysis->slug . '/webhook';
            $originalFileName = $file->getClientOriginalName();

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
                        'name'     => 'slug',
                        'contents' => $analysis->slug
                    ],
                    [
                        'name'     => 'language',
                        'contents' => $validated['locale']
                    ]
                ]
            ]);
            Log::info('V2 Audio successfully sent to VPS.', ['slug' => $analysis->slug]);
        } catch (\Exception $e) {
            Log::error('Failed to send V2 audio to VPS: ' . $e->getMessage());
        }

        return redirect()->route('analysis.v2.processing', ['analysis' => $analysis->slug, 'locale' => app()->getLocale()]);
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