<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AnalysisV2Controller extends Controller
{
    public function methodology()
    {
        return view('analysis.v2.methodology');
    }

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

        $graphInterpretation = $analysis->result_data['graph_interpretation'] ?? null;

        return view('analysis.v2.result', compact('analysis', 'stats', 'transcription', 'summary', 'graphInterpretation'));
    }

    public function interpretGraph(Request $request, Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);

        $request->validate([
            'image' => 'required|string', // base64 data URL
        ]);

        $imageBase64 = $request->input('image');
        // Pastikan format base64 data URL valid
        if (!str_starts_with($imageBase64, 'data:image/')) {
            $imageBase64 = 'data:image/png;base64,' . $imageBase64;
        }

        $apiKey = config('services.openai.key') ?: env('OPENAI_API_KEY');
        if (empty($apiKey)) {
            return response()->json([
                'status' => 'error',
                'message' => 'API Key OpenAI belum dikonfigurasi di server (services.openai.key / OPENAI_API_KEY).'
            ], 500);
        }

        // Kumpulkan data kontekstual: Dinamika Intonasi, Summary, dan Graf
        $resultData = $analysis->result_data ?? [];
        $transcription = $resultData['transcription'] ?? [];
        $graphData = $analysis->graph_data ?? [];
        $existingSummary = $resultData['summary'] ?? [];

        // Hitung intonasi per baris
        $intonationFlow = [];
        $upCount = 0;
        $downCount = 0;
        foreach ($transcription as $idx => $t) {
            $speaker = $t['speaker'] ?? 'Unknown';
            $type = strtolower($t['intonation_type'] ?? ($t['intonation_markers'][0]['type'] ?? 'neutral'));
            if ($type === 'up') $upCount++;
            if ($type === 'down') $downCount++;
            $intonationFlow[] = "Baris " . ($idx + 1) . " [{$speaker}]: " . strtoupper($type) . " - " . mb_strimwidth(strip_tags($t['text'] ?? ''), 0, 60, '...');
        }

        $dynamicsCharacter = ($downCount > $upCount) 
            ? 'Direktif & Korektif (Dominan Turun)' 
            : (($upCount > $downCount) ? 'Dialogis & Koperatif (Dominan Naik)' : 'Kondusif & Seimbang');

        $systemPrompt = <<<PROMPT
Anda adalah Pakar Analisis Wacana Kritis (Critical Discourse Analysis) dan Linguistik Interaksional Bimbingan Akademik Tingkat Mahir.
Tugas Anda adalah menafsirkan visualisasi peta graf interaksi hubungan wacana (node & edge network) yang dikirimkan dalam bentuk gambar.

Kombinasikan pemahaman gambar graf tersebut secara holistik dengan data kontekstual yang diberikan:
1. Dinamika Intonasi Per Baris (Pitch Dynamics & Pitch Contours).
2. Karakter Aliran Wacana (Interpretasi Aliran).
3. Hasil Analisis Akhir (Kategori Advice, Karakter Relasi, Ranah Bicara, Umpan Balik, & Rekomendasi).

Berikan analisis mendalam, tajam, terstruktur, dan akademis dengan struktur JSON berikut:
{
    "ringkasan_eksekutif": "Ringkasan tajam 2-3 kalimat mengenai konfigurasi kekuasaan dan dinamika bimbingan dalam graf ini.",
    "topologi_relasi": "Penjelasan detail mengenai peran node Dosen, Mahasiswa, dan simpul-simpul wacana (Advice & Relasi Kuasa) serta tebal-tipisnya relasi penghubung (edges).",
    "korelasi_intonasi": "Bagaimana dinamika intonasi per baris (naik/turun) memperkuat atau melunakkan relasi kuasa yang terlihat pada peta graf.",
    "makna_pedagogis": "Makna pedagogis dan dampak interaksi terhadap perkembangan substansi skripsi/riset mahasiswa.",
    "rekomendasi_supervisi": "Saran konkrit bagi Dosen dan Mahasiswa untuk mengoptimalkan sesi bimbingan selanjutnya.",
    "mermaid_code": "flowchart TD\\n    A[Topik Awal: ...] --> B[Diskusi: ...]\\n    B --> C{Pencabangan/Evaluasi}\\n    C -->|Revisi/Arahan| D[...]\\n    C -->|Keputusan/Solusi| E[...]"
}
Panduan khusus untuk 'mermaid_code':
- Buat flowchart Mermaid valid bertipe 'flowchart TD' yang merangkum KRONOLOGI & ALUR PEMBAHASAN dari transkripsi (misal: dimulai dari topik teknologi/metode apa yang pertama kali dibuka, kendala yang dihadapi, lalu bercabang ke alternatif solusi atau instruksi revisi dosen, hingga simpulan akhir).
- PENTING: Jangan gunakan tanda kurung atau simbol kurawal di dalam teks label node tanpa diapit tanda kutip ganda, contoh aman: nodeA[\"Topik Awal: Evaluasi Model\"] --> nodeB[\"Pembahasan: Arsitektur Transformer\"].
- Pastikan jawaban HANYA berupa JSON valid tanpa markdown fence.
PROMPT;

        $userContext = "DATA KONTEKSTUAL PERCAKAPAN:\n";
        $userContext .= "- Judul Bimbingan: " . $analysis->title . "\n";
        $userContext .= "- Total Segmen/Baris: " . count($transcription) . "\n";
        $userContext .= "- Karakter Aliran Dinamika: " . $dynamicsCharacter . " (Naik: {$upCount}, Turun: {$downCount})\n";
        $userContext .= "- Kategori Advice: " . ($existingSummary['kategori_advice'] ?? 'Saran Bimbingan Akademik') . "\n";
        $userContext .= "- Karakter Relasi: " . ($existingSummary['karakter_relasi'] ?? 'Kondusif & Seimbang') . "\n";
        $userContext .= "- Ranah Bicara: " . ($existingSummary['ranah_pembicaraan'] ?? 'Bimbingan Metodologi Skripsi') . "\n";
        $userContext .= "- Sample Aliran Intonasi Baris:\n" . implode("\n", array_slice($intonationFlow, 0, 15)) . "\n\n";
        
        // Kirimkan rangkuman transkrip baris per baris agar AI memahami alur kronologis topik
        $userContext .= "TRANSKRIPSI PERCAKAPAN (URUTAN WAKTU & TOPIK):\n";
        foreach (array_slice($transcription, 0, 30) as $idx => $tr) {
            $spk = $tr['speaker'] ?? 'Unknown';
            $tx = strip_tags($tr['text'] ?? ($tr['text_html'] ?? ''));
            $userContext .= "Baris " . ($idx + 1) . " [{$spk}]: " . mb_strimwidth($tx, 0, 120, '...') . "\n";
        }
        $userContext .= "\nPeriksa gambar topologi graf terlampir serta transkripsi di atas, lalu hasilkan analisis teks dan diagram alur pembahasan Mermaid sesuai panduan.";

        try {
            $response = Http::withToken($apiKey)
                ->timeout(70)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => 'gpt-4o',
                    'temperature' => 0.4,
                    'max_tokens' => 2500,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt
                        ],
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $userContext
                                ],
                                [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => $imageBase64,
                                        'detail' => 'high'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);

            if ($response->failed()) {
                Log::error('[AnalysisV2Controller@interpretGraph] OpenAI Error: ' . $response->body());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Gagal memproses gambar graf di model OpenAI: ' . ($response->json('error.message') ?? 'Terjadi kesalahan pada layanan AI.')
                ], 500);
            }

            $rawContent = $response->json('choices.0.message.content') ?? '{}';
            $cleaned = preg_replace('/```json\s*/i', '', $rawContent);
            $cleaned = preg_replace('/```\s*/', '', $cleaned);
            $parsed = json_decode(trim($cleaned), true);

            if (!$parsed || !is_array($parsed)) {
                $parsed = [
                    'ringkasan_eksekutif' => trim($cleaned),
                    'topologi_relasi' => 'Peta graf memperlihatkan interaksi aktif antara Dosen dan Mahasiswa dengan dominasi arahan bimbingan akademik terstruktur.',
                    'korelasi_intonasi' => 'Dinamika intonasi sejalan dengan penegasan instruksi serta klarifikasi konsep riset.',
                    'makna_pedagogis' => 'Proses pembimbingan berfokus pada pendalaman instrumen dan metodologi penelitian.',
                    'rekomendasi_supervisi' => 'Pertahankan ritme dialogis dan tindak lanjuti revisi secara berkala.'
                ];
            }

            // Bersihkan mermaid_code jika dibungkus codeblock
            if (!empty($parsed['mermaid_code'])) {
                $parsed['mermaid_code'] = trim(preg_replace('/^```(?:mermaid)?|```$/m', '', $parsed['mermaid_code']));
            } else {
                // Default fallback jika model tidak menyertakan mermaid_code
                $parsed['mermaid_code'] = "flowchart TD\n    A[\"Pembukaan Bimbingan\"] --> B[\"Penyampaian Progres Riset\"]\n    B --> C{\"Evaluasi & Feedback Dosen\"}\n    C -->|\"Arahan Eksplisit\"| D[\"Revisi Metodologi & Instrumen\"]\n    C -->|\"Dukungan Keputusan\"| E[\"Persetujuan Arah Penelitian\"]\n    D --> F[\"Kesepakatan Target Bimbingan Berikutnya\"]\n    E --> F";
            }

            // Simpan hasil interpretasi ke dalam result_data analysis di database
            $resultData['graph_interpretation'] = [
                'data' => $parsed,
                'analyzed_at' => now()->toIso8601String(),
                'model' => 'gpt-4o',
            ];

            $analysis->update([
                'result_data' => $resultData
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Interpretasi graf & alur pembahasan berhasil disintesis dan disimpan.',
                'interpretation' => $parsed,
                'analyzed_at' => now()->translatedFormat('d M Y, H:i')
            ]);

        } catch (\Throwable $e) {
            Log::error('[AnalysisV2Controller@interpretGraph] Exception: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }
}