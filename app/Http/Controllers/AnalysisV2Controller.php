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
            $callbackUrl = url(app()->getLocale() . '/analysis/v2/' . $analysis->slug . '/webhook');
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

    public function swapSpeakers(Request $request, Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);

        $resultData = $analysis->result_data ?? [];
        $transcription = $resultData['transcription'] ?? [];

        // Swap roles in transcription
        foreach ($transcription as &$item) {
            $currentSpeaker = $item['speaker'] ?? '';
            if (strcasecmp($currentSpeaker, 'Dosen') === 0 || strcasecmp($currentSpeaker, 'Supervisor') === 0) {
                $item['speaker'] = 'Mahasiswa';
            } elseif (strcasecmp($currentSpeaker, 'Mahasiswa') === 0 || strcasecmp($currentSpeaker, 'Student') === 0) {
                $item['speaker'] = 'Dosen';
            }
        }
        unset($item);

        $resultData['transcription'] = $transcription;

        // Swap nodes and edges in graph_data if present
        $graphData = $analysis->graph_data ?? [];
        if (!empty($graphData['nodes']) && is_array($graphData['nodes'])) {
            foreach ($graphData['nodes'] as &$node) {
                if (isset($node['id'])) {
                    if ($node['id'] === 'Dosen') {
                        $node['id'] = 'Mahasiswa';
                        $node['label'] = 'Mahasiswa';
                    } elseif ($node['id'] === 'Mahasiswa' || $node['id'] === 'Mhs') {
                        $node['id'] = 'Dosen';
                        $node['label'] = 'Dosen';
                    }
                }
            }
            unset($node);
        }

        if (!empty($graphData['edges']) && is_array($graphData['edges'])) {
            foreach ($graphData['edges'] as &$edge) {
                if (isset($edge['from'])) {
                    if ($edge['from'] === 'Dosen') {
                        $edge['from'] = 'Mahasiswa';
                    } elseif ($edge['from'] === 'Mahasiswa' || $edge['from'] === 'Mhs') {
                        $edge['from'] = 'Dosen';
                    }
                }
                if (isset($edge['to'])) {
                    if ($edge['to'] === 'Dosen') {
                        $edge['to'] = 'Mahasiswa';
                    } elseif ($edge['to'] === 'Mahasiswa' || $edge['to'] === 'Mhs') {
                        $edge['to'] = 'Dosen';
                    }
                }
            }
            unset($edge);
        }

        $analysis->update([
            'result_data' => $resultData,
            'graph_data' => $graphData,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Peran pembicara berhasil ditukar.',
                'transcription' => $transcription,
                'graph_data' => $graphData,
            ]);
        }

        return back()->with('success', 'Peran pembicara (Dosen ↔ Mahasiswa) berhasil ditukar.');
    }

    public function result(Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);
        if (!$analysis->isCompleted()) {
            return redirect()->route('analysis.v2.processing', $analysis->slug);
        }

        $transcription = $analysis->result_data['transcription'] ?? [];

        // Format timestamp dan perbaiki/buat text_html dengan intonasi inline (V1 parity)
        foreach ($transcription as &$item) {
            if (empty($item['timestamp'])) {
                $startSec = $item['start'] ?? ($item['start_sec'] ?? 0);
                $endSec = $item['end'] ?? ($item['end_sec'] ?? 0);
                $m1 = floor($startSec / 60);
                $s1 = floor($startSec % 60);
                $m2 = floor($endSec / 60);
                $s2 = floor($endSec % 60);
                $item['timestamp'] = sprintf("%02d:%02d - %02d:%02d", $m1, $s1, $m2, $s2);
            }

            $speaker = $item['speaker'] ?? 'Unknown';
            $isDosen = (strcasecmp($speaker, 'Dosen') === 0 || strcasecmp($speaker, 'Supervisor') === 0);

            // Tentukan tipe intonasi jika belum ada
            if (empty($item['intonation_type'])) {
                if (!empty($item['intonation_markers'][0]['type'])) {
                    $item['intonation_type'] = $item['intonation_markers'][0]['type'];
                } else {
                    $item['intonation_type'] = $isDosen ? 'down' : 'up';
                }
            }
            $intType = strtolower($item['intonation_type']);

            // Tentukan alasan & relasi intonasi
            $intReason = $intType === 'down'
                ? 'Intonasi menurun menandakan penegasan instruksi atau arahan akademik yang pasti dari Dosen.'
                : ($intType === 'up'
                    ? 'Intonasi menaik menandakan nada eksploratif, klarifikasi, atau pertanyaan bimbingan.'
                    : 'Intonasi netral menandakan pertukaran informasi akademis umum.');
            if (!empty($item['intonation_markers'][0]['reason'])) {
                $intReason = $item['intonation_markers'][0]['reason'];
            }

            $intRelation = !empty($item['relation']) 
                ? $item['relation'] 
                : (!empty($item['advice_relation']) 
                    ? $item['advice_relation'] 
                    : ($isDosen ? 'Arahan direktif dosen membimbing arah riset mahasiswa.' : 'Tanggapan dialogis mahasiswa terhadap arahan dosen.'));
            if (!empty($item['intonation_markers'][0]['relation'])) {
                $intRelation = $item['intonation_markers'][0]['relation'];
            }

            // Pastikan intonation_markers terisi minimal 1 marker
            if (empty($item['intonation_markers']) || !is_array($item['intonation_markers'])) {
                $item['intonation_markers'] = [
                    [
                        'id' => 'MARKER_1',
                        'type' => $intType,
                        'reason' => $intReason,
                        'relation' => $intRelation
                    ]
                ];
            }

            // Bangun text_html yang konsisten dengan V1
            $rawText = $item['text_html'] ?? ($item['text'] ?? '');
            
            // Generate icon inline button untuk intonasi
            $iconSvg = '';
            if ($intType === 'up') {
                $iconSvg = '<svg class="inline-block w-4 h-4 text-blue-500 mx-1 align-middle hover:scale-125 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>';
            } elseif ($intType === 'down') {
                $iconSvg = '<svg class="inline-block w-4 h-4 text-red-500 mx-1 align-middle hover:scale-125 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>';
            } else {
                $iconSvg = '<svg class="inline-block w-4 h-4 text-gray-400 mx-1 align-middle hover:scale-125 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14"></path></svg>';
            }

            $jsonTitle = htmlspecialchars(json_encode('Intonasi ' . ($intType === 'up' ? 'Naik (High pitch)' : ($intType === 'down' ? 'Turun (Low pitch)' : 'Netral'))), ENT_QUOTES);
            $jsonType = htmlspecialchars(json_encode($intType), ENT_QUOTES);
            $jsonReason = htmlspecialchars(json_encode($intReason), ENT_QUOTES);
            $jsonRelation = htmlspecialchars(json_encode($intRelation), ENT_QUOTES);

            $inlineButton = "<button type=\"button\" @click=\"openInsight({$jsonTitle}, {$jsonType}, {$jsonReason}, {$jsonRelation})\" class=\"inline-flex items-center justify-center p-0.5 hover:bg-gray-100 border border-transparent hover:border-gray-200 rounded-lg transition-all focus:outline-none cursor-pointer align-middle\" title=\"Klik untuk melihat detail intonasi & relasi\">{$iconSvg}</button>";

            // Jika rawText sudah memiliki [MARKER_...]
            if (preg_match('/\[MARKER_\d+\]/i', $rawText)) {
                $formattedHtml = preg_replace('/\[MARKER_\d+\]/i', $inlineButton, $rawText);
            } else {
                // Sisipkan inline button di tengah kalimat (setelah tanda koma/titik pertama) atau di akhir kalimat
                if (preg_match('/([,;])\s+/u', $rawText, $match, PREG_OFFSET_CAPTURE)) {
                    $pos = $match[0][1] + strlen($match[1][0]);
                    $formattedHtml = substr($rawText, 0, $pos) . ' ' . $inlineButton . ' ' . substr($rawText, $pos);
                } elseif (preg_match('/([.?!])$/u', trim($rawText), $match, PREG_OFFSET_CAPTURE)) {
                    $trimmed = trim($rawText);
                    $formattedHtml = substr($trimmed, 0, -1) . ' ' . $inlineButton . substr($trimmed, -1);
                } else {
                    $formattedHtml = $rawText . ' ' . $inlineButton;
                }
            }

            $item['text_html'] = $formattedHtml;
        }
        unset($item);

        // Hitung statistik komprehensif untuk tampilan analitik V1
        $totalSegments = count($transcription);
        $dosenSegments = 0;
        $mhsSegments = 0;
        $adviceCounts = [];
        $modeCounts = [];
        $intonationCounts = ['up' => 0, 'down' => 0, 'neutral' => 0];

        foreach ($transcription as $item) {
            $speaker = $item['speaker'] ?? 'Unknown';
            if (strcasecmp($speaker, 'Dosen') === 0 || strcasecmp($speaker, 'Supervisor') === 0) {
                $dosenSegments++;
            } elseif (strcasecmp($speaker, 'Mahasiswa') === 0 || strcasecmp($speaker, 'Student') === 0) {
                $mhsSegments++;
            }

            if (!empty($item['advice_giving'])) {
                $adv = $item['advice_giving'];
                $adviceCounts[$adv] = ($adviceCounts[$adv] ?? 0) + 1;
            }

            if (!empty($item['modes_of_interaction'])) {
                $mode = $item['modes_of_interaction'];
                $modeCounts[$mode] = ($modeCounts[$mode] ?? 0) + 1;
            }

            $intType = strtolower($item['intonation_type'] ?? ($item['intonation_markers'][0]['type'] ?? 'neutral'));
            if (in_array($intType, ['up', 'down', 'neutral'])) {
                $intonationCounts[$intType]++;
            } else {
                $intonationCounts['neutral']++;
            }
        }

        // Tentukan nilai sintesis summary Hasil Analisis Akhir (V1 parity)
        $topAdvice = !empty($adviceCounts) ? array_keys($adviceCounts, max($adviceCounts))[0] : null;
        $topMode = !empty($modeCounts) ? array_keys($modeCounts, max($modeCounts))[0] : null;
        
        $kategoriAdvice = 'Saran Bimbingan Akademik';
        if ($topAdvice === 'arahan_eksplisit') {
            $kategoriAdvice = 'Instruksi Direktif Dosen';
        } elseif ($topAdvice === 'bimbingan_bertahap') {
            $kategoriAdvice = 'Saran Akademik Terarah';
        } elseif ($topAdvice === 'dukungan_keputusan') {
            $kategoriAdvice = 'Dukungan Keputusan Kolaboratif';
        } elseif ($topAdvice === 'jawaban_tegas') {
            $kategoriAdvice = 'Jawaban Tegas & Penegasan Riset';
        } elseif ($topAdvice === 'petunjuk_kontekstual') {
            $kategoriAdvice = 'Petunjuk Kontekstual Metodologi';
        }

        $karakterRelasi = 'Kondusif & Seimbang';
        if ($topMode === 'power_over' || $topMode === 'otoritas') {
            $karakterRelasi = 'Relasi Kuasa Direktif Dosen';
        } elseif ($topMode === 'power_gaining') {
            $karakterRelasi = 'Koperatif & Dialogis';
        } elseif ($topMode === 'power_maintaining') {
            $karakterRelasi = 'Dialogis Kondusif';
        }

        $intonasiDominan = 'Intonasi Seimbang';
        if ($intonationCounts['down'] > $intonationCounts['up']) {
            $intonasiDominan = 'Intonasi Turun Dominan';
        } elseif ($intonationCounts['up'] > $intonationCounts['down']) {
            $intonasiDominan = 'Intonasi Naik Dominan';
        }

        // Fallback or existing summary from result_data
        $existingSummary = $analysis->result_data['summary'] ?? [];
        $summary = [
            'kategori_advice' => $existingSummary['kategori_advice'] ?? $kategoriAdvice,
            'karakter_relasi' => $existingSummary['karakter_relasi'] ?? $karakterRelasi,
            'intonasi_dominan' => $existingSummary['intonasi_dominan'] ?? $intonasiDominan,
            'ranah_pembicaraan' => $existingSummary['ranah_pembicaraan'] ?? 'Bimbingan Skripsi & Pendalaman Metodologi Penelitian',
            'arah_tujuan' => $existingSummary['arah_tujuan'] ?? ($topAdvice ? 'Dosen mengarahkan mahasiswa dengan fokus pada penyempurnaan landasan teori, ketepatan metode penelitian, dan perumusan simpulan yang valid.' : 'Membimbing mahasiswa dalam memahami kerangka konseptual serta sistematika penulisan karya ilmiah.'),
            'saran_perbaikan' => $existingSummary['saran_perbaikan'] ?? 'Mahasiswa disarankan menindaklanjuti catatan revisi Dosen, mengonfirmasi instrumen riset, dan menyusun target mingguan agar jadwal bimbingan berjalan optimal.',
        ];

        $stats = [
            'total_segments' => $totalSegments,
            'dosen_segments' => $dosenSegments,
            'mhs_segments' => $mhsSegments,
            'dosen_ratio' => $totalSegments > 0 ? round(($dosenSegments / $totalSegments) * 100) : 0,
            'mhs_ratio' => $totalSegments > 0 ? round(($mhsSegments / $totalSegments) * 100) : 0,
            'advice_counts' => $adviceCounts,
            'mode_counts' => $modeCounts,
            'intonation_counts' => $intonationCounts,
        ];

        return view('analysis.v2.result', compact('analysis', 'stats', 'transcription', 'summary'));
    }
}