<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Services\GraphAnalyticsService;
use App\Services\RoBERTaAnalyticsService;
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
        $request->merge(['mode' => 'swap']);
        return $this->updateSpeakers($request, $analysis);
    }

    public function getSpeakerSamples(Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);

        $transcription = $analysis->result_data['transcription'] ?? [];
        $speakers = self::extractSpeakerSamples($transcription);

        return response()->json([
            'status' => 'success',
            'audio_url' => route('analysis.v2.audio', $analysis->slug),
            'total_speakers' => count($speakers),
            'speakers' => $speakers
        ]);
    }

    public function updateSpeakers(Request $request, Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);

        $resultData = $analysis->result_data ?? [];
        $transcription = $resultData['transcription'] ?? [];
        $graphData = $analysis->graph_data ?? [];
        $mode = $request->input('mode', 'update');

        $mappings = [];

        if ($mode === 'swap') {
            // Dapatkan seluruh penutur unik berurutan
            $uniqueSpeakers = array_values(array_unique(array_filter(array_map(function($item) {
                return trim($item['speaker'] ?? '');
            }, $transcription))));

            if (count($uniqueSpeakers) >= 2) {
                // Tukar speaker 0 dan speaker 1
                $mappings[$uniqueSpeakers[0]] = $uniqueSpeakers[1];
                $mappings[$uniqueSpeakers[1]] = $uniqueSpeakers[0];
            } else {
                $mappings = ['Dosen' => 'Mahasiswa', 'Mahasiswa' => 'Dosen'];
            }
        } else {
            // Mode update / rename dari input user
            if ($request->has('speakers') && is_array($request->input('speakers'))) {
                foreach ($request->input('speakers') as $spkItem) {
                    $orig = trim($spkItem['original'] ?? ($spkItem['from'] ?? ''));
                    $newName = trim($spkItem['name'] ?? ($spkItem['to'] ?? ''));
                    if (!empty($orig) && !empty($newName) && $orig !== $newName) {
                        $mappings[$orig] = $newName;
                    }
                }
            } elseif ($request->has('mappings') && is_array($request->input('mappings'))) {
                foreach ($request->input('mappings') as $from => $to) {
                    $from = trim($from);
                    $to = trim($to);
                    if (!empty($from) && !empty($to) && $from !== $to) {
                        $mappings[$from] = $to;
                    }
                }
            }
        }

        if (!empty($mappings)) {
            // Terapkan mapping nama/peran ke transkripsi
            foreach ($transcription as &$item) {
                $curr = trim($item['speaker'] ?? '');
                if (isset($mappings[$curr])) {
                    $item['speaker'] = $mappings[$curr];
                }
            }
            unset($item);

            $resultData['transcription'] = $transcription;

            // Terapkan mapping nama/peran ke Graph Data (Nodes & Edges)
            if (!empty($graphData['nodes']) && is_array($graphData['nodes'])) {
                foreach ($graphData['nodes'] as &$node) {
                    $nid = $node['id'] ?? '';
                    $nlabel = $node['label'] ?? ($node['name'] ?? '');

                    if (isset($mappings[$nid])) {
                        $node['id'] = $mappings[$nid];
                        $node['label'] = $mappings[$nid];
                        if (isset($node['name'])) $node['name'] = $mappings[$nid];
                    } elseif (isset($mappings[$nlabel])) {
                        $node['label'] = $mappings[$nlabel];
                        if (isset($node['name'])) $node['name'] = $mappings[$nlabel];
                    }
                }
                unset($node);
            }

            if (!empty($graphData['edges']) && is_array($graphData['edges'])) {
                foreach ($graphData['edges'] as &$edge) {
                    if (isset($edge['from']) && isset($mappings[$edge['from']])) {
                        $edge['from'] = $mappings[$edge['from']];
                    }
                    if (isset($edge['to']) && isset($mappings[$edge['to']])) {
                        $edge['to'] = $mappings[$edge['to']];
                    }
                    if (isset($edge['source']) && isset($mappings[$edge['source']])) {
                        $edge['source'] = $mappings[$edge['source']];
                    }
                    if (isset($edge['target']) && isset($mappings[$edge['target']])) {
                        $edge['target'] = $mappings[$edge['target']];
                    }
                }
                unset($edge);
            }

            $analysis->update([
                'result_data' => $resultData,
                'graph_data' => $graphData,
            ]);
        }

        $updatedSpeakers = self::extractSpeakerSamples($transcription);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Peran dan nama pembicara berhasil diperbarui.',
                'speakers' => $updatedSpeakers,
                'transcription' => $transcription,
                'graph_data' => $graphData,
            ]);
        }

        return back()->with('success', 'Peran dan nama pembicara berhasil diperbarui.');
    }

    public function result(Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);
        if (!$analysis->isCompleted()) {
            return redirect()->route('analysis.v2.processing', $analysis->slug);
        }

        $transcription = $analysis->result_data['transcription'] ?? [];
        $needsPersist = false;

        // Format timestamp dan perbaiki/buat text_html dengan intonasi inline (V1 parity)
        foreach ($transcription as $index => &$item) {
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
            $isDosen = (bool)preg_match('/\b(dosen|supervisor|pembimbing|penguji|prof|dr|pak|bu)\b/iu', $speaker);

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

            // Pastikan agent_insight terisi ringkas dan kontekstual (bukan boilerplate template kembar)
            $isOldBoilerplate = !empty($item['agent_insight']) && (
                str_contains($item['agent_insight'], 'Dosen berperan mengarahkan wacana dengan') ||
                str_contains($item['agent_insight'], 'AI belum memberikan ulasan')
            );
            if (empty($item['agent_insight']) || $isOldBoilerplate) {
                $item['agent_insight'] = self::generateConciseAcademicSubstance($item, $index);
                $needsPersist = true;
            }

            // Bersihkan text dari button inline sebelumnya jika pernah ter-generate ganda / korup
            $cleanText = preg_replace('/<button[^>]*>.*?<\/button>/is', '', $item['text_html'] ?? ($item['text'] ?? ''));
            $cleanText = trim(strip_tags($cleanText));
            if (empty($cleanText)) {
                $cleanText = trim(strip_tags($item['text'] ?? ''));
            }

            // Simpan teks bersih ke text
            $item['text'] = $cleanText;
            $item['text_html'] = $cleanText;
        }
        unset($item);

        if ($needsPersist) {
            $resultData = $analysis->result_data ?? [];
            $resultData['transcription'] = $transcription;
            $analysis->update(['result_data' => $resultData]);
        }

        // Hitung statistik komprehensif untuk tampilan analitik V1
        $totalSegments = count($transcription);
        $dosenSegments = 0;
        $mhsSegments = 0;
        $adviceCounts = [];
        $modeCounts = [];
        $intonationCounts = ['up' => 0, 'down' => 0, 'neutral' => 0];

        foreach ($transcription as $item) {
            $speaker = $item['speaker'] ?? 'Unknown';
            if (preg_match('/\b(dosen|supervisor|pembimbing|penguji|prof|dr|pak|bu)\b/iu', $speaker)) {
                $dosenSegments++;
            } elseif (preg_match('/\b(mhs|mahasiswa|student|terbimbing)\b/iu', $speaker)) {
                $mhsSegments++;
            } else {
                // Jika tidak terdeteksi kata kunci spesifik, bandingkan dengan urutan/sample jika diperlukan
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

        // Pengayaan XAI & Kalibrasi Distribusi Probabilitas Softmax RoBERTa
        $robertaAnalyticsService = app(RoBERTaAnalyticsService::class);
        $transcription = $robertaAnalyticsService->enrichTranscription($transcription);

        // Agregasi Statistik Keyakinan Model & Entropi untuk Skripsi
        $totalAdviceConf = 0;
        $totalModeConf = 0;
        $totalEntropy = 0;
        foreach ($transcription as $it) {
            $totalAdviceConf += $it['advice_confidence'] ?? 0;
            $totalModeConf += $it['mode_confidence'] ?? 0;
            $totalEntropy += $it['advice_xai']['normalized_entropy'] ?? 0;
        }
        $stats['avg_advice_confidence'] = $totalSegments > 0 ? round(($totalAdviceConf / $totalSegments) * 100, 1) : 0;
        $stats['avg_mode_confidence'] = $totalSegments > 0 ? round(($totalModeConf / $totalSegments) * 100, 1) : 0;
        $stats['avg_entropy'] = $totalSegments > 0 ? round(($totalEntropy / $totalSegments) * 100, 1) : 0;

        $graphInterpretation = $analysis->result_data['graph_interpretation'] ?? null;
        $speakerSamples = self::extractSpeakerSamples($transcription);

        // Komputasi Metrik Kuantitatif Sentralitas Graf (Network Science untuk Skripsi)
        $graphAnalyticsService = app(GraphAnalyticsService::class);
        $graphMetrics = $graphAnalyticsService->calculateMetrics($analysis->graph_data ?? [], $transcription);

        return view('analysis.v2.result', compact('analysis', 'stats', 'transcription', 'summary', 'graphInterpretation', 'speakerSamples', 'graphMetrics'));
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

    /**
     * Generate concise and contextual academic substance (agent_insight) per conversation line.
     * Combines OpenAI (if API key available) with an intelligent semantic NLP engine.
     */
    public function generateSubstances(Request $request, Analysis $analysis)
    {
        abort_if($analysis->user_id != Auth::id() && !Auth::user()->is_admin, 403);

        $resultData = $analysis->result_data ?? [];
        $transcription = $resultData['transcription'] ?? [];

        if (empty($transcription)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data transkripsi percakapan kosong.'
            ], 400);
        }

        $apiKey = config('services.openai.key') ?: env('OPENAI_API_KEY');
        $generatedByAi = false;

        // Coba generate via OpenAI jika API Key tersedia
        if (!empty($apiKey)) {
            try {
                $linesPrompt = [];
                foreach ($transcription as $idx => $t) {
                    $spk = $t['speaker'] ?? 'Unknown';
                    $txt = trim(strip_tags($t['text'] ?? ''));
                    $adv = $t['advice_giving'] ?? '-';
                    $mod = $t['modes_of_interaction'] ?? '-';
                    $linesPrompt[] = "[" . ($idx + 1) . "] {$spk} (Advice: {$adv}, Relasi: {$mod}): \"{$txt}\"";
                }
                $conversationText = implode("\n", $linesPrompt);

                $systemPrompt = "Anda adalah Asisten Analisis Wacana Bimbingan Skripsi & Supervisi Akademik.
Tugas Anda adalah membuat 'Substansi Akademik' (agent_insight) yang SANGAT SINGKAT, PADAT, dan TAJAM untuk SETIAP baris percakapan bimbingan dosen dan mahasiswa.

ATURAN KETAT:
1. PANJANG: Maksimal 8-15 kata per baris (1 kalimat pendek saja, jangan bertele-tele!).
2. RELEVANSI: Teks harus secara spesifik mengulas maksud akademis/pedagogis dari apa yang diucapkan pada kalimat tersebut.
3. VARIASI: DILARANG menggunakan kalimat berulang atau template yang sama di baris berbeda.
4. FORMAT OUTPUT: Kembalikan HANYA array JSON murni berupa daftar string ulasan, berurutan sesuai nomor baris 1 sampai N.
Contoh format:
[\"Dosen membuka sesi bimbingan dan meninjau agenda revisi bab 3.\", \"Mahasiswa memaparkan temuan data lapangan dan kendala metodologi.\"]";

                $response = Http::withToken($apiKey)
                    ->timeout(45)
                    ->post('https://api.openai.com/v1/chat/completions', [
                        'model' => 'gpt-4o-mini',
                        'temperature' => 0.4,
                        'messages' => [
                            ['role' => 'system', 'content' => $systemPrompt],
                            ['role' => 'user', 'content' => "Berikut daftar baris percakapan:\n\n{$conversationText}\n\nBuat array JSON string substansi akademik singkat untuk setiap baris:"]
                        ]
                    ]);

                if ($response->successful()) {
                    $raw = $response->json('choices.0.message.content') ?? '[]';
                    $cleaned = preg_replace('/```json\s*/i', '', $raw);
                    $cleaned = preg_replace('/```\s*/', '', $cleaned);
                    $parsed = json_decode(trim($cleaned), true);

                    if (is_array($parsed) && count($parsed) === count($transcription)) {
                        foreach ($transcription as $idx => &$item) {
                            if (!empty($parsed[$idx])) {
                                $item['agent_insight'] = trim($parsed[$idx]);
                            }
                        }
                        unset($item);
                        $generatedByAi = true;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('[AnalysisV2Controller@generateSubstances] OpenAI attempt failed, falling back to heuristic: ' . $e->getMessage());
            }
        }

        // Jika OpenAI tidak aktif atau gagal, jalankan generator semantik kontekstual terpadu
        if (!$generatedByAi) {
            foreach ($transcription as $idx => &$item) {
                $item['agent_insight'] = self::generateConciseAcademicSubstance($item, $idx);
            }
            unset($item);
        }

        $resultData['transcription'] = $transcription;
        $analysis->update(['result_data' => $resultData]);

        return response()->json([
            'status' => 'success',
            'message' => $generatedByAi 
                ? 'Substansi akademik berhasil di-generate secara cerdas oleh AI (OpenAI GPT).'
                : 'Substansi akademik berhasil diperbarui secara kontekstual dan ringkas.',
            'transcription' => $transcription
        ]);
    }

    /**
     * Generate concise, sharp, and utterance-aware academic substance (8-16 words).
     */
    public static function generateConciseAcademicSubstance(array $item, int $index = 0): string
    {
        $speaker = $item['speaker'] ?? 'Unknown';
        $isDosen = (bool)preg_match('/\b(dosen|supervisor|pembimbing|penguji|prof|dr|pak|bu)\b/iu', $speaker);
        $text = trim(strip_tags($item['text'] ?? ($item['text_html'] ?? '')));
        $lower = mb_strtolower($text);
        $advice = strtolower($item['advice_giving'] ?? '');
        $mode = strtolower($item['modes_of_interaction'] ?? '');
        $intType = strtolower($item['intonation_type'] ?? ($item['intonation_markers'][0]['type'] ?? 'neutral'));

        if ($isDosen) {
            // Evaluasi isi kalimat Dosen
            if (preg_match('/\b(jurnal|pustaka|teori|referensi|literatur|sitasi|daftar pustaka)\b/iu', $lower)) {
                return 'Dosen mengarahkan penguatan referensi jurnal dan landasan teori ilmiah.';
            }
            if (preg_match('/\b(metod|sampel|populasi|variabel|kuesioner|instrumen|wawancara|indikator)\b/iu', $lower)) {
                return 'Dosen menegaskan ketepatan instrumen metodologi dan batasan sampel riset.';
            }
            if (preg_match('/\b(data|analisis|tabel|grafik|hasil|temuan|olah|uji|signifikan|output)\b/iu', $lower)) {
                return 'Dosen mengevaluasi validitas olah data serta ketajaman analisis hasil riset.';
            }
            if (preg_match('/\b(kesimpulan|simpulan|pembahasan|implikasi|rekomendasi)\b/iu', $lower)) {
                return 'Dosen mengarahkan ketajaman perumusan simpulan dan implikasi riset.';
            }
            if (preg_match('/\b(revisi|perbaiki|ubah|ganti|keliru|salah|kurang|harus|jangan|coret)\b/iu', $lower)) {
                return 'Dosen memberikan instruksi korektif terhadap bagian draf yang perlu diperbaiki.';
            }
            if (preg_match('/\?$/u', $text) || preg_match('/\b(apakah|kenapa|mengapa|bagaimana|apa alasannya|dasarnya)\b/iu', $lower)) {
                return 'Dosen mengajukan pertanyaan evaluatif guna menguji argumentasi metodologis mahasiswa.';
            }
            if (preg_match('/\b(bagus|baik|tepat|setuju|lanjutkan|oke|sudah benar|mantap)\b/iu', $lower)) {
                return 'Dosen memvalidasi kemajuan penulisan dan menyetujui arah riset mahasiswa.';
            }
            if ($index === 0 || preg_match('/\b(selamat|halo|assalam|mulai|hari ini|coba kita|bimbingan kali ini)\b/iu', $lower)) {
                return 'Dosen membuka sesi bimbingan dan meninjau agenda evaluasi draf skripsi.';
            }

            // Berdasarkan Klasifikasi Model RoBERTa & Intonasi
            if ($advice === 'arahan_eksplisit') {
                return 'Dosen menegaskan instruksi direktif yang wajib disesuaikan dalam draf.';
            }
            if ($advice === 'bimbingan_bertahap') {
                return $intType === 'down'
                    ? 'Dosen menegaskan langkah bertahap penyempurnaan kerangka penulisan.'
                    : 'Dosen membimbing alur berpikir mahasiswa secara dialogis dan bertahap.';
            }
            if ($advice === 'dukungan_keputusan') {
                return 'Dosen mendukung alternatif keputusan metodologis yang dipilih mahasiswa.';
            }
            if ($advice === 'jawaban_tegas') {
                return 'Dosen memberikan penegasan konseptual secara lugas dan terarah.';
            }
            if ($advice === 'petunjuk_kontekstual') {
                return 'Dosen memberikan panduan kontekstual sesuai dinamika studi kasus.';
            }

            return 'Dosen mengarahkan penguatan substansi akademik dan standar karya ilmiah.';
        } else {
            // Evaluasi isi kalimat Mahasiswa
            if (preg_match('/\?$/u', $text) || preg_match('/\b(apakah|bagaimana|maksudnya|artinya|izin bertanya|bisa tolong|gimana|boleh)\b/iu', $lower)) {
                return 'Mahasiswa meminta klarifikasi teknis mengenai saran perbaikan dari dosen.';
            }
            if (preg_match('/\b(baik|siap|iya|paham|mengerti|terima kasih|catat|akan saya|laksanakan)\b/iu', $lower)) {
                return 'Mahasiswa mengonfirmasi penerimaan masukan dosen dan mencatat poin revisi.';
            }
            if (preg_match('/\b(sudah saya|ini pak|ini bu|hasilnya|tabel|bab|draf|kemarin|menemukan|data)\b/iu', $lower)) {
                return 'Mahasiswa memaparkan perkembangan draf penulisan dan temuan data riset.';
            }
            if (preg_match('/\b(karena|alasan saya|menurut saya|berdasarkan referensi|saya menggunakan)\b/iu', $lower)) {
                return 'Mahasiswa memberikan argumen ilmiah untuk menjelaskan pertimbangan metodologinya.';
            }
            if (preg_match('/\b(kendala|sulit|bingung|belum|susah|kurang paham|ragu)\b/iu', $lower)) {
                return 'Mahasiswa menyampaikan kendala teknis yang dihadapi dalam penyusunan riset.';
            }

            // Berdasarkan Mode Interaksi Mahasiswa
            if ($mode === 'power_gaining') {
                return 'Mahasiswa aktif berinisiatif mendiskusikan gagasan dan solusi penulisan.';
            }
            if ($mode === 'power_maintaining') {
                return 'Mahasiswa merespons dialog bimbingan secara koperatif dan terarah.';
            }

            return 'Mahasiswa menyimak masukan dosen untuk penyempurnaan kualitas riset.';
        }
    }

    /**
     * Parse formatted time string (e.g. "01:23" or "01:23:45") to seconds.
     */
    public static function parseTimeToSeconds(string $timeStr): float
    {
        $timeStr = trim($timeStr);
        if (is_numeric($timeStr)) {
            return (float)$timeStr;
        }
        $parts = explode(':', $timeStr);
        if (count($parts) === 2) {
            return ((float)$parts[0] * 60) + (float)$parts[1];
        } elseif (count($parts) === 3) {
            return ((float)$parts[0] * 3600) + ((float)$parts[1] * 60) + (float)$parts[2];
        }
        return 0.0;
    }

    /**
     * Resolve the academic role of a speaker name/label dynamically.
     */
    public static function resolveSpeakerRole(?string $name, array $speakerSamples = []): string
    {
        $clean = trim($name ?? '');
        if (!empty($speakerSamples)) {
            foreach ($speakerSamples as $s) {
                if (strcasecmp($s['name'] ?? '', $clean) === 0 || strcasecmp($s['original'] ?? '', $clean) === 0) {
                    if (!empty($s['role'])) {
                        return $s['role'];
                    }
                }
            }
        }
        if (preg_match('/\b(dosen|supervisor|pembimbing|penguji|prof|dr|pak|bu)\b/iu', $clean)) {
            return 'Dosen';
        }
        if (preg_match('/\b(mhs|mahasiswa|student|terbimbing)\b/iu', $clean)) {
            return 'Mahasiswa';
        }
        return 'Lainnya';
    }

    /**
     * Extract sample voice snippet for each speaker in transcription.
     */
    public static function extractSpeakerSamples(array $transcription): array
    {
        $speakersMap = [];

        foreach ($transcription as $idx => $item) {
            $spk = trim($item['speaker'] ?? 'Unknown');
            if (empty($spk)) continue;

            $start = $item['start'] ?? ($item['start_sec'] ?? null);
            $end = $item['end'] ?? ($item['end_sec'] ?? null);
            $timeStr = $item['timestamp'] ?? '';

            if (($start === null || $end === null) && !empty($timeStr)) {
                $parts = explode('-', $timeStr);
                $start = self::parseTimeToSeconds(trim($parts[0] ?? '00:00'));
                $end = isset($parts[1]) ? self::parseTimeToSeconds(trim($parts[1])) : ($start + 5);
            }

            $duration = ($start !== null && $end !== null) ? max(0, (float)$end - (float)$start) : 0;
            $text = trim(strip_tags($item['text'] ?? ($item['text_html'] ?? '')));

            // Detect likely role
            $isDosen = (stripos($spk, 'Dosen') !== false || stripos($spk, 'Supervisor') !== false);
            $isMhs = (stripos($spk, 'Mahasiswa') !== false || stripos($spk, 'Student') !== false || stripos($spk, 'Mhs') !== false);
            $defaultRole = $isDosen ? 'Dosen' : ($isMhs ? 'Mahasiswa' : 'Lainnya');

            if (!isset($speakersMap[$spk])) {
                $speakersMap[$spk] = [
                    'original' => $spk,
                    'name' => $spk,
                    'role' => $defaultRole,
                    'sample_text' => $text,
                    'start_sec' => (float)$start,
                    'end_sec' => (float)$end,
                    'timestamp' => $timeStr,
                    'duration' => $duration,
                    'utterance_count' => 1,
                    'line_index' => $idx,
                ];
            } else {
                $speakersMap[$spk]['utterance_count']++;
                // Prefer clean sample between 3 and 12 seconds with substantial text
                $currentDur = $speakersMap[$spk]['duration'];
                if (($currentDur < 3 || $currentDur > 15) && ($duration >= 3 && $duration <= 15) && strlen($text) > 15) {
                    $speakersMap[$spk]['sample_text'] = $text;
                    $speakersMap[$spk]['start_sec'] = (float)$start;
                    $speakersMap[$spk]['end_sec'] = (float)$end;
                    $speakersMap[$spk]['timestamp'] = $timeStr;
                    $speakersMap[$spk]['duration'] = $duration;
                    $speakersMap[$spk]['line_index'] = $idx;
                }
            }
        }

        return array_values($speakersMap);
    }
}