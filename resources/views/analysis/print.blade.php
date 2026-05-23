@php
    $transBlocks = $analysis->result_data['transcription'] ?? [];
    
    // Calculate total chunks dynamically based on max chunk_index
    $maxChunkIndex = 0;
    foreach ($transBlocks as $block) {
        if (isset($block['chunk_index'])) {
            $maxChunkIndex = max($maxChunkIndex, $block['chunk_index']);
        }
    }
    $totalChunks = $maxChunkIndex > 0 ? $maxChunkIndex : ($analysis->result_data['total_chunks'] ?? count($analysis->result_data['chunks'] ?? []));
    if ($totalChunks == 0) {
        $totalChunks = 8; // fallback to default
    }

    // Calculate dynamics metrics
    $upCount = 0;
    $downCount = 0;
    foreach ($transBlocks as $block) {
        $type = $block['intonation_type'] ?? ($block['advice_type'] ?? 'neutral');
        if ($type === 'up') $upCount++;
        if ($type === 'down') $downCount++;
    }

    if ($downCount > $upCount) {
        $dynamicsSummary = 'Alur percakapan didominasi oleh intonasi menurun (' . $downCount . ' kali) yang menandakan instruksi tegas, korektif, dan pemberian saran bimbingan yang terarah dari Dosen. Hal ini menunjukkan dinamika direktif di mana wacana dikontrol untuk mengarahkan kualitas akademis riset mahasiswa.';
        $dynamicsStatus = 'Direktif & Korektif';
    } elseif ($upCount > $downCount) {
        $dynamicsSummary = 'Alur percakapan didominasi oleh intonasi menaik (' . $upCount . ' kali) yang mencerminkan nada tanya, eksplorasi koperatif, atau memicu kebingungan produktif. Ini mencerminkan hubungan dialogis di mana bimbingan berjalan interaktif dan bersahabat.';
        $dynamicsStatus = 'Dialogis & Koperatif';
    } else {
        $dynamicsSummary = 'Dinamika percakapan berjalan seimbang antara intonasi turun (korektif) dan naik (klarifikasi/tanya). Ini mencerminkan keseimbangan relasi bimbingan yang sangat kondusif, interaktif, dan berorientasi pada pemecahan masalah bersama.';
        $dynamicsStatus = 'Kondusif & Seimbang';
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Resmi Analisis Bimbingan - {{ $analysis->title }}</title>
    
    <!-- Tailwind CSS v4 -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <!-- ChartJS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            background-color: #ffffff;
        }
        
        .kop-line {
            border-bottom: 2px solid #000000;
            position: relative;
        }
        .kop-line::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            border-bottom: 0.5px solid #000000;
            margin-top: 1px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                color: black !important;
                padding: 0 !important;
            }
            .page-break {
                page-break-before: always;
            }
            .print-card {
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
            .tr-avoid {
                page-break-inside: avoid !important;
            }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen py-12 px-4 sm:px-6">

    <!-- Floating Navigation Bar (Screen Only - Monochrome styled) -->
    <div class="no-print fixed top-0 left-0 right-0 z-50 bg-white border-b border-gray-250 px-6 py-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('analysis.result', $analysis->id) }}" class="inline-flex items-center text-xs font-black text-gray-600 hover:text-black transition-colors uppercase tracking-widest gap-1">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Hasil
            </a>
            <span class="h-4 w-px bg-gray-200"></span>
            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Mode Pratinjau Cetak</span>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="inline-flex items-center px-5 py-2.5 rounded-lg bg-black hover:bg-gray-800 text-white text-xs font-black uppercase tracking-widest transition-all cursor-pointer">
                <i data-lucide="printer" class="w-4 h-4 mr-2"></i> Cetak / Simpan PDF
            </button>
        </div>
    </div>

    <!-- Print Canvas -->
    <div class="max-w-4xl mx-auto bg-white border border-gray-200 rounded-none p-8 sm:p-12 mt-10 print-card shadow-sm">
        
        <!-- Kop Surat (Official Letterhead - Monochrome) -->
        <div class="flex items-start justify-between gap-6 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-black rounded-lg flex items-center justify-center p-2.5 text-white shrink-0">
                    <svg class="w-full h-full" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="12 2 22 8.5 22 15.5 12 22 2 15.5 2 8.5 12 2"></polygon>
                        <line x1="12" y1="22" x2="12" y2="15.5"></line>
                        <line x1="12" y1="15.5" x2="22" y2="8.5"></line>
                        <line x1="12" y1="15.5" x2="2" y2="8.5"></line>
                        <polyline points="2 8.5 12 15.5 22 8.5"></polyline>
                        <polyline points="12 2 12 15.5"></polyline>
                    </svg>
                </div>
                <div>
                    <h4 class="text-[0.6rem] font-bold uppercase tracking-[0.25em] text-gray-500">Laporan Resmi Sistem</h4>
                    <h2 class="text-lg font-black text-black tracking-tight uppercase leading-tight">PROTOTIPE BIMA – SUPERVISORY AI</h2>
                    <p class="text-[0.65rem] font-medium text-gray-500 mt-0.5">Sistem Pengawasan & Analisis Transkrip Bimbingan Berbasis Multi-Agent AI</p>
                </div>
            </div>
            <div class="text-right hidden sm:block">
                <span class="inline-flex px-3 py-1 bg-white text-black border border-black rounded-none text-[0.6rem] font-black uppercase tracking-widest">
                    SYSTEM VERIFIED
                </span>
            </div>
        </div>

        <div class="kop-line mb-8"></div>

        <!-- Document Main Title -->
        <div class="text-center mb-8">
            <h1 class="text-xl font-black text-black uppercase tracking-tight leading-normal">LAPORAN HASIL ANALISIS BIMBINGAN AKADEMIK</h1>
            <p class="text-[0.7rem] font-medium text-gray-600 mt-1.5 leading-relaxed max-w-xl mx-auto">
                Hasil Analisis Laporan Sistem Supervisory AI, dan Segenap Tim Pengembang Supervisory AI
            </p>
        </div>

        <!-- Metadata Table Section (Monochrome bordered) -->
        <div class="border border-black p-6 mb-8 bg-white">
            <h3 class="text-[0.65rem] font-black text-black uppercase tracking-widest mb-4">Informasi Sesi Analisis</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3">
                
                <div class="flex justify-between py-1 border-b border-gray-200">
                    <span class="text-xs text-gray-500 uppercase tracking-wider">Judul Sesi</span>
                    <span class="text-xs font-bold text-black uppercase text-right ml-4">{{ $analysis->title }}</span>
                </div>
                
                <div class="flex justify-between py-1 border-b border-gray-200">
                    <span class="text-xs text-gray-500 uppercase tracking-wider">Tanggal Analisis</span>
                    <span class="text-xs font-bold text-black text-right">{{ $analysis->created_at->format('d M Y, H:i') }} WIB</span>
                </div>

                <div class="flex justify-between py-1 border-b border-gray-200">
                    <span class="text-xs text-gray-500 uppercase tracking-wider">Metode Analisis</span>
                    <span class="text-xs font-bold text-black text-right">C-CDA Multi-Agent</span>
                </div>

                <div class="flex justify-between py-1 border-b border-gray-200">
                    <span class="text-xs text-gray-500 uppercase tracking-wider">Jumlah Segmen</span>
                    <span class="text-xs font-bold text-black text-right">{{ $totalChunks }} Potongan Audio</span>
                </div>

                <div class="flex justify-between py-1 border-b border-gray-200 md:col-span-2">
                    <span class="text-xs text-gray-500 uppercase tracking-wider">Status Verifikasi</span>
                    <span class="text-xs font-bold text-black uppercase tracking-wide text-right">
                        ANALISIS DEEP LEARNING DENGAN DATASET KHUSUS (SUPER INTELLIGENCE)
                    </span>
                </div>

            </div>
        </div>

        <!-- Hasil Analisis Akhir Section (Monochrome styled) -->
        <div class="mb-10 bg-white border border-black p-6 tr-avoid">
            <h3 class="text-xs font-black text-black uppercase tracking-wide mb-4 pb-2 border-b border-black">
                Rangkuman Hasil Analisis Akhir
            </h3>
            
            @php $summary = $analysis->result_data['summary'] ?? []; @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div class="bg-gray-50 border border-gray-200 p-4">
                    <span class="block text-[0.55rem] font-black text-gray-500 uppercase tracking-widest mb-1">
                        Kategori Advice
                    </span>
                    <span class="text-xs font-bold text-black block leading-snug">
                        {{ $summary['kategori_advice'] ?? '-' }}
                    </span>
                </div>
                <div class="bg-gray-50 border border-gray-200 p-4">
                    <span class="block text-[0.55rem] font-black text-gray-500 uppercase tracking-widest mb-1">
                        Karakter Relasi
                    </span>
                    <span class="text-xs font-bold text-black block leading-snug">
                        {{ $summary['karakter_relasi'] ?? '-' }}
                    </span>
                </div>
                <div class="bg-gray-50 border border-gray-200 p-4">
                    <span class="block text-[0.55rem] font-black text-gray-500 uppercase tracking-widest mb-1">
                        Intonasi
                    </span>
                    <span class="text-xs font-bold text-black block leading-snug">
                        {{ $summary['intonasi_dominan'] ?? '-' }}
                    </span>
                </div>
                <div class="bg-gray-50 border border-gray-200 p-4">
                    <span class="block text-[0.55rem] font-black text-gray-500 uppercase tracking-widest mb-1">
                        Ranah Bicara
                    </span>
                    <span class="text-xs font-bold text-black block leading-snug">
                        {{ $summary['ranah_pembicaraan'] ?? '-' }}
                    </span>
                </div>
            </div>

            <div class="bg-black text-white p-5 mb-4">
                <span class="block text-[0.55rem] font-black text-gray-300 uppercase tracking-widest mb-1 flex items-center">
                    <i data-lucide="target" class="w-3.5 h-3.5 mr-1.5 text-white"></i> Arah Tujuan
                </span>
                <p class="text-xs font-medium leading-relaxed">
                    {{ $summary['arah_tujuan'] ?? '-' }}
                </p>
            </div>

            <div class="bg-gray-50 border border-gray-200 p-5">
                <span class="block text-[0.55rem] font-black text-gray-600 uppercase tracking-widest mb-1 flex items-center">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 mr-1.5 text-black"></i> Saran Perbaikan
                </span>
                <p class="text-xs font-medium leading-relaxed">
                    {{ $summary['saran_perbaikan'] ?? '-' }}
                </p>
            </div>
        </div>

        <!-- Dynamics Graph Section (Monochrome styled) -->
        <div class="mb-10 bg-white border border-gray-250 p-6 tr-avoid">
            <h3 class="text-xs font-black text-black uppercase tracking-wide mb-4">Grafik Dinamika Intonasi Percakapan</h3>
            
            <div class="relative w-full h-64 mb-6">
                <canvas id="dynamicsChartPrint"></canvas>
            </div>

            <div class="p-5 border border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[0.6rem] font-black text-gray-500 uppercase tracking-widest">Interpretasi Aliran Suara</span>
                    <span class="inline-flex items-center px-3 py-1 rounded-none text-[0.6rem] font-black uppercase tracking-widest border border-black bg-white text-black">
                        {{ $dynamicsStatus }}
                    </span>
                </div>
                <p class="text-xs font-medium text-gray-600 leading-relaxed font-serif">
                    {{ $dynamicsSummary }}
                </p>
            </div>
        </div>

        <!-- Transcription Table Section (Monochrome styled) -->
        <div class="page-break py-4"></div>
        
        <h3 class="text-xs font-black text-black uppercase tracking-wide mb-6 pb-2 border-b border-black">
            Transkrip Percakapan Lengkap & Anotasi AI
        </h3>

        <div class="space-y-4">
            @php
                $prevChunkIndex = null;
            @endphp
            @foreach ($transBlocks as $index => $block)
                @php
                    $chunkIndex = $block['chunk_index'] ?? null;
                    
                    // Parse Intonation metrics for printing
                    $intType = $block['intonation_type'] ?? ($block['advice_type'] ?? 'neutral');
                    $intReason = '';
                    if (!empty($block['intonation_markers'][0]['reason'])) {
                        $intReason = $block['intonation_markers'][0]['reason'];
                    }
                    $intRelation = $block['intonation_markers'][0]['relation'] ?? '';
                    
                    // Check if it has any annotation data
                    $hasInsight = !empty($block['agent_insight']) || !empty($intReason) || !empty($intRelation) || (isset($block['is_advice']) && $block['is_advice']);
                @endphp
                
                {{-- Dynamic Slicing Indicator (Triggered on 00:00 resets or chunk transitions) --}}
                @if ($chunkIndex !== $prevChunkIndex || ($index > 0 && strpos($block['timestamp'] ?? '', '00:00') === 0))
                    <div class="tr-avoid my-6 py-2.5 px-4 bg-gray-50 border-t border-b border-black/20 flex items-center justify-between">
                        <span class="text-[0.65rem] font-black text-black uppercase tracking-widest flex items-center gap-2">
                            <i data-lucide="scissors" class="w-3.5 h-3.5 text-black"></i> Potongan Audio {{ $chunkIndex ?? ($prevChunkIndex + 1) }}
                        </span>
                        <span class="text-[0.55rem] font-medium text-gray-400">Pembatas Durasi Pemotongan Klien</span>
                    </div>
                @endif
                
                @php
                    $prevChunkIndex = $chunkIndex;
                @endphp

                <div class="tr-avoid p-5 border border-gray-200 bg-white flex flex-col md:flex-row gap-4 md:gap-6">
                    
                    <!-- Metadata Column (Monochrome) -->
                    <div class="md:w-32 shrink-0 flex flex-row md:flex-col justify-between md:justify-start gap-1">
                        <div>
                            <span class="font-black text-xs uppercase tracking-wider text-black">
                                {{ $block['speaker'] ?? 'Unknown' }}
                            </span>
                            <div class="text-[0.6rem] font-bold text-gray-400 uppercase tracking-widest mt-0.5">
                                Baris {{ $index + 1 }}
                            </div>
                        </div>
                        @if (!empty($block['timestamp']))
                            <span class="inline-flex items-center text-[0.65rem] font-bold text-gray-500 md:mt-2 bg-gray-100 px-2 py-0.5 rounded-none w-fit border border-gray-200">
                                <i data-lucide="clock" class="w-3 h-3 mr-1"></i> {{ $block['timestamp'] }}
                            </span>
                        @endif
                    </div>

                    <!-- Content Column (Monochrome) -->
                    <div class="flex-grow">
                        <!-- Transcription text -->
                        <div class="text-sm font-medium text-gray-900 leading-relaxed font-serif">
                            {!! $block['text_html'] ?? '' !!}
                        </div>

                        <!-- Intonation & Advice Metadata Row -->
                        <div class="mt-2 flex flex-wrap gap-2 text-[0.6rem] font-bold uppercase tracking-widest text-gray-500">
                            <!-- Intonasi Badge -->
                            @php
                                $intLabel = $intType === 'up' ? 'Naik ↑' : ($intType === 'down' ? 'Turun ↓' : 'Netral ➖');
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 border border-gray-300 bg-gray-50 text-gray-700">
                                Intonasi: {{ $intLabel }}
                            </span>

                            <!-- Advice Badge (if advice present) -->
                            @if (isset($block['is_advice']) && $block['is_advice'])
                                <span class="inline-flex items-center px-2 py-0.5 border border-black bg-black text-white">
                                    Advice Giving: {{ $block['advice_category'] ?? 'Pemberian Saran' }}
                                </span>
                            @endif
                        </div>

                        <!-- Agent insight box (Monochrome Details Card) -->
                        @if ($hasInsight)
                            <div class="mt-3 p-4 bg-gray-50 border border-gray-250">
                                @if (!empty($block['agent_insight']))
                                    <span class="block text-[0.55rem] font-black text-black uppercase tracking-widest mb-1">
                                        Evaluasi & Insight AI
                                    </span>
                                    <p class="text-[0.7rem] font-medium text-gray-600 leading-relaxed">
                                        {{ $block['agent_insight'] }}
                                    </p>
                                @endif

                                @if (!empty($block['advice_relation']))
                                    <div class="mt-2.5 pt-2 border-t border-gray-200">
                                        <span class="block text-[0.55rem] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Implikasi Relasi Kalimat:</span>
                                        <p class="text-[0.65rem] text-gray-500 leading-relaxed">
                                            {{ $block['advice_relation'] }}
                                        </p>
                                    </div>
                                @endif

                                <!-- Intonation Annotation Details -->
                                @if (!empty($intReason) || !empty($intRelation) || $intType !== 'neutral')
                                    <div class="mt-2.5 pt-2 border-t border-gray-200">
                                        <span class="block text-[0.55rem] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Analisis Intonasi Suara:</span>
                                        <p class="text-[0.65rem] text-gray-700 leading-relaxed">
                                            <strong>Karakter Nada:</strong> {{ $intType === 'up' ? 'Naik ↑ (Tanya / Eksplorasi)' : ($intType === 'down' ? 'Turun ↓ (Instruksi / Tegas)' : 'Netral ➖ (Datar / Deskriptif)') }}
                                        </p>
                                        @if (!empty($intReason))
                                            <p class="text-[0.65rem] text-gray-500 mt-1 leading-relaxed">
                                                <strong>Alasan Kontekstual:</strong> {{ $intReason }}
                                            </p>
                                        @endif
                                        @if (!empty($intRelation))
                                            <p class="text-[0.65rem] text-gray-400 mt-0.5 italic leading-relaxed">
                                                <strong>Implikasi Relasi:</strong> {{ $intRelation }}
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                <!-- Advice Category Details -->
                                @if (isset($block['is_advice']) && $block['is_advice'])
                                    <div class="mt-2.5 pt-2 border-t border-gray-200">
                                        <span class="block text-[0.55rem] font-bold text-gray-500 uppercase tracking-widest mb-0.5">Kategori Advice Giving (Saran Akademik):</span>
                                        <p class="text-[0.65rem] text-gray-700 leading-relaxed">
                                            Kalimat ini diidentifikasi oleh sistem sebagai saran penting bimbingan dengan kategori: <strong>{{ $block['advice_category'] ?? 'Rekomendasi Akademik' }}</strong>.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif

                    </div>
                </div>
            @endforeach
        </div>

        <!-- Signatures & Authorization stamp (Monochrome) -->
        <div class="mt-16 pt-12 border-t border-black tr-avoid">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-12 text-center sm:text-left">
                
                <!-- Automated Verification Statement (Stamp box completely removed as requested) -->
                <div class="flex flex-col items-center sm:items-start justify-center">
                    <p class="text-[0.6rem] text-gray-400 leading-normal max-w-xs">
                        Laporan ini dibuat dan ditranskripsikan secara otomatis melalui pemrosesan C-CDA Multi-Agent. Dokumen ini sah digunakan sebagai arsip bimbingan resmi.
                    </p>
                </div>

                <!-- Advisor Signature Space -->
                <div class="flex flex-col items-center sm:items-end justify-center">
                    <div class="text-center w-[220px]">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-20">Dosen Pembimbing Akademik,</p>
                        <div class="w-32 h-px bg-black mx-auto mb-2"></div>
                        <p class="text-xs font-black text-black uppercase">NIDN. _________________</p>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Script Block -->
    @php
        $chartPoints = array_map(
            function ($block, $idx) {
                $type = $block['intonation_type'] ?? ($block['advice_type'] ?? 'neutral');
                $val = 0;
                if ($type === 'up') $val = 1;
                if ($type === 'down') $val = -1;

                return [
                    'label' => 'Baris ' . ($idx + 1),
                    'value' => $val,
                    'speaker' => $block['speaker'] ?? 'Unknown',
                    'type' => $type === 'up' ? 'Naik' : ($type === 'down' ? 'Turun' : 'Netral'),
                ];
            },
            $transBlocks,
            array_keys($transBlocks),
        );
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            lucide.createIcons();
            
            const canvasEl = document.getElementById('dynamicsChartPrint');
            if (!canvasEl) return;

            const ctx = canvasEl.getContext('2d');
            const chartData = @json($chartPoints);

            const labels = chartData.map(d => d.label);
            const values = chartData.map(d => d.value);
            const pointColors = chartData.map(d => '#000000');
            const pointStyles = chartData.map(d => d.speaker.toLowerCase() === 'dosen' ? 'circle' : 'rect');

            const gradient = ctx.createLinearGradient(0, 0, 0, 250);
            gradient.addColorStop(0, 'rgba(0, 0, 0, 0.05)');
            gradient.addColorStop(1, 'rgba(255, 255, 255, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Aliran Intonasi',
                        data: values,
                        borderColor: '#000000',
                        borderWidth: 1.5,
                        tension: 0.3,
                        fill: true,
                        backgroundColor: gradient,
                        pointBackgroundColor: pointColors,
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 1.5,
                        pointRadius: 4.5,
                        pointStyle: pointStyles
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { size: 9, family: 'Inter', weight: '600' },
                                color: '#6b7280'
                            }
                        },
                        y: {
                            min: -1.2,
                            max: 1.2,
                            ticks: {
                                stepSize: 1,
                                callback: function(value) {
                                    if (value === 1) return 'Naik ↑';
                                    if (value === 0) return 'Netral ➖';
                                    if (value === -1) return 'Turun ↓';
                                    return '';
                                },
                                font: { size: 9, family: 'Inter', weight: 'bold' },
                                color: '#6b7280'
                            },
                            grid: {
                                color: '#e5e7eb',
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
