<x-layouts.app title="Memproses Analisis V2 — {{ $analysis->title }}">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 animate-fade-in">
        
        {{-- Header (Standardized with V1 & Multi-language Support) --}}
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-xs font-bold text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-widest mb-3">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-1.5"></i>
                <span class="lang-id">Kembali ke Dashboard</span>
                <span class="lang-en">Back to Dashboard</span>
                <span class="lang-zh">返回控制面板</span>
            </a>

            <div class="flex items-center gap-3 mb-2">
                <div class="w-2.5 h-2.5 rounded-full bg-purple-600 animate-pulse" id="status-dot"></div>
                <span class="text-xs font-black text-purple-600 uppercase tracking-widest" id="status-badge-text">
                    <span class="lang-id">Sedang Menganalisis</span>
                    <span class="lang-en">Processing Analysis</span>
                    <span class="lang-zh">正在处理分析</span>
                </span>
                <span class="px-2.5 py-0.5 rounded-full bg-purple-100 text-purple-700 text-[0.65rem] font-bold">
                    Multi-Task RoBERTa & Graf
                </span>
            </div>

            <h1 class="text-2xl font-black text-gray-900 truncate tracking-tight uppercase">{{ $analysis->title }}</h1>

            <div class="flex flex-wrap gap-4 mt-2 text-xs text-gray-400 font-bold uppercase tracking-widest">
                <span>
                    <span class="lang-id">Bahasa:</span>
                    <span class="lang-en">Language:</span>
                    <span class="lang-zh">语言:</span>
                    <span class="text-gray-700 font-semibold">{{ ['id' => '🇮🇩 Indonesia', 'en' => '🇬🇧 English', 'zh' => '🇨🇳 中文'][$analysis->locale] ?? $analysis->locale }}</span>
                </span>
                <span>
                    <span class="lang-id">ID Sesi:</span>
                    <span class="lang-en">Session ID:</span>
                    <span class="lang-zh">会话编号:</span>
                    <span class="text-gray-700 font-mono">{{ $analysis->slug }}</span>
                </span>
            </div>
        </div>

        {{-- Progress Bar --}}
        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden mb-8 shadow-inner">
            <div id="progress-bar" class="bg-gradient-to-r from-purple-500 to-indigo-600 h-full rounded-full transition-all duration-700" style="width: 0%"></div>
        </div>

        {{-- Main Execution Card --}}
        <div class="bg-white border border-gray-100 rounded-[2.5rem] shadow-xl shadow-gray-100/60 p-8 md:p-12">
            
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 pb-6 border-b border-gray-50">
                <div class="flex items-center gap-4">
                    <div id="status-icon-wrapper" class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 transition-colors">
                        {{-- Clean SVG spinner by default --}}
                        <svg id="status-icon-spinner" class="w-6 h-6 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <div id="status-icon-check" class="hidden">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 mb-1">Status Pemrosesan AI</p>
                        <h2 class="text-lg font-black text-gray-900" id="status-text">Menyiapkan koneksi ke Cloud AI...</h2>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 mb-1">Progres Keseluruhan</p>
                    <p class="text-3xl font-black text-purple-600" id="progress-text">0%</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Status Tahapan Pipeline --}}
                <div class="space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-4">Pipeline Execution</h3>
                    
                    {{-- Step 1 --}}
                    <div class="flex items-start gap-4">
                        <div class="w-9 h-9 rounded-full bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-600 shrink-0 transition-colors" id="step1-icon">
                            <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-bold text-gray-900" id="step1-title">Kompresi & Format Audio</p>
                            <p class="text-xs font-medium text-gray-500 mt-0.5" id="step1-desc">Format audio optimal 16kHz mono sedang disiapkan...</p>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="flex items-start gap-4">
                        <div class="w-9 h-9 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 shrink-0 transition-colors" id="step2-icon">
                            <i data-lucide="mic" class="w-4 h-4"></i>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-bold text-gray-900" id="step2-title">WhisperX & Pemisahan Pembicara (Diarization)</p>
                            <p class="text-xs font-medium text-gray-500 mt-0.5" id="step2-desc">Menunggu giliran transkripsi percakapan...</p>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="flex items-start gap-4">
                        <div class="w-9 h-9 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 shrink-0 transition-colors" id="step3-icon">
                            <i data-lucide="network" class="w-4 h-4"></i>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-bold text-gray-900" id="step3-title">RoBERTa Multi-Task & Pemodelan Graf Wacana</p>
                            <p class="text-xs font-medium text-gray-500 mt-0.5" id="step3-desc">Menunggu klasifikasi peran, tindak tutur, dan relasi kuasa...</p>
                        </div>
                    </div>
                </div>

                {{-- Informasi Panel Tambahan & Speaker Feedback --}}
                <div class="flex flex-col gap-4">
                    {{-- Speaker Detection Live Feedback Box --}}
                    <div id="speaker-detected-box" class="hidden bg-purple-50/70 border border-purple-200 rounded-3xl p-6 transition-all duration-500 animate-fade-in">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-2xl bg-purple-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-purple-600/20">
                                <i data-lucide="users" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <span class="px-2 py-0.5 rounded-full bg-purple-200 text-purple-800 text-[0.6rem] font-black uppercase tracking-wider">Terdeteksi Otomatis</span>
                                <h4 class="text-sm font-black text-purple-950 mt-0.5">2 Pembicara Teridentifikasi</h4>
                            </div>
                        </div>
                        <p class="text-xs text-purple-800 leading-relaxed font-medium mb-3">
                            Sistem AI telah memisahkan percakapan antara <strong class="font-bold">Dosen</strong> dan <strong class="font-bold">Mahasiswa</strong>. Anda dapat menukar peran ini di halaman hasil jika terbalik.
                        </p>
                        <div class="flex items-center gap-2 text-[0.7rem] font-bold text-purple-700 bg-white/80 p-2.5 rounded-xl border border-purple-100">
                            <i data-lucide="info" class="w-4 h-4 shrink-0 text-purple-500"></i>
                            <span>Pemisahan suara berhasil. Melanjutkan ke pemetaan relasi wacana...</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-3xl p-6 border border-gray-100 flex flex-col justify-center text-center">
                        <div class="w-12 h-12 mx-auto rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center mb-3">
                            <i data-lucide="shield-check" class="w-6 h-6"></i>
                        </div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-900 mb-1">Analisis Multi-Task Real-Time</h4>
                        <p class="text-xs text-gray-500 font-medium leading-relaxed">
                            Sistem menggunakan saluran WebSocket langsung ke Cloud AI. Halaman ini akan secara otomatis membawa Anda ke layar hasil setelah seluruh proses analisis wacana dan graf selesai 100%.
                        </p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

    <x-slot name="scripts">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slug = "{{ $analysis->slug }}";
            const wsUrl = "wss://vps.temaniskripsi.id/api/v2/ws/" + slug;
            
            let ws;
            let retryCount = 0;

            function updateStep(stepNum, status, desc) {
                const icon = document.getElementById('step' + stepNum + '-icon');
                const title = document.getElementById('step' + stepNum + '-title');
                const descEl = document.getElementById('step' + stepNum + '-desc');
                
                if (descEl) descEl.innerText = desc;
                
                if (status === 'active') {
                    if (icon) {
                        icon.className = "w-9 h-9 rounded-full bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-600 shrink-0 transition-colors";
                        icon.innerHTML = '<svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
                    }
                    if (title) title.className = "text-sm font-black text-purple-700";
                } else if (status === 'done') {
                    if (icon) {
                        icon.className = "w-9 h-9 rounded-full bg-green-50 border border-green-200 flex items-center justify-center text-green-600 shrink-0 transition-colors";
                        icon.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
                    }
                    if (title) title.className = "text-sm font-bold text-gray-900";
                } else if (status === 'error') {
                    if (icon) {
                        icon.className = "w-9 h-9 rounded-full bg-red-50 border border-red-200 flex items-center justify-center text-red-600 shrink-0 transition-colors";
                        icon.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>';
                    }
                    if (title) title.className = "text-sm font-bold text-red-600";
                }
            }

            function connectWS() {
                ws = new WebSocket(wsUrl);

                ws.onopen = function() {
                    document.getElementById('status-text').innerText = "Terhubung dengan aman ke Cloud AI";
                    updateStep(1, 'active', 'Koneksi stabil. Menyiapkan audio...');
                    ws.send(JSON.stringify({action: "start_processing"}));
                };

                ws.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);
                        
                        if (data.progress !== undefined) {
                            document.getElementById('progress-bar').style.width = data.progress + '%';
                            document.getElementById('progress-text').innerText = data.progress + '%';
                            
                            // Map progress to steps
                            if (data.progress > 0 && data.progress < 40) {
                                updateStep(1, 'done', 'Format audio optimal siap.');
                                updateStep(2, 'active', 'Memecah audio dan membedakan pembicara...');
                            } else if (data.progress >= 40 && data.progress < 80) {
                                updateStep(1, 'done', 'Format audio optimal siap.');
                                updateStep(2, 'done', 'Transkripsi & diarization selesai.');
                                updateStep(3, 'active', 'Menganalisis relasi kuasa & membentuk graf...');
                                const speakerBox = document.getElementById('speaker-detected-box');
                                if (speakerBox && speakerBox.classList.contains('hidden')) {
                                    speakerBox.classList.remove('hidden');
                                    if (window.lucide) window.lucide.createIcons();
                                }
                            } else if (data.progress >= 80) {
                                updateStep(1, 'done', 'Format audio optimal siap.');
                                updateStep(2, 'done', 'Transkripsi & diarization selesai.');
                                updateStep(3, 'done', 'Klasifikasi multi-task dan pembentukan graf selesai.');
                                const speakerBox = document.getElementById('speaker-detected-box');
                                if (speakerBox && speakerBox.classList.contains('hidden')) {
                                    speakerBox.classList.remove('hidden');
                                    if (window.lucide) window.lucide.createIcons();
                                }
                            }
                        }
                        
                        if (data.message) {
                            document.getElementById('status-text').innerText = data.message;
                        }

                        if (data.status === 'completed') {
                            document.getElementById('status-text').innerText = "Analisis selesai! Mengalihkan ke hasil...";
                            
                            // Swap spinner with checkmark icon without spinning
                            const iconWrapper = document.getElementById('status-icon-wrapper');
                            const spinner = document.getElementById('status-icon-spinner');
                            const check = document.getElementById('status-icon-check');
                            
                            if (iconWrapper) {
                                iconWrapper.className = "w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center shrink-0 transition-colors";
                            }
                            if (spinner) spinner.classList.add('hidden');
                            if (check) check.classList.remove('hidden');

                            const badgeText = document.getElementById('status-badge-text');
                            if (badgeText) {
                                badgeText.innerHTML = '<span class="text-green-600">Selesai</span>';
                            }
                            const dot = document.getElementById('status-dot');
                            if (dot) {
                                dot.className = "w-2.5 h-2.5 rounded-full bg-green-600";
                            }
                            
                            setTimeout(() => {
                                window.location.href = "/{{ app()->getLocale() }}/analysis/v2/" + slug + "/result";
                            }, 1200);
                        }
                    } catch (e) {
                        console.error("WS Parse Error", e);
                    }
                };

                ws.onclose = function(e) {
                    if (retryCount < 5) {
                        retryCount++;
                        document.getElementById('status-text').innerText = `Menghubungkan ulang... (${retryCount}/5)`;
                        updateStep(1, 'error', 'Koneksi terputus. Mencoba ulang...');
                        setTimeout(connectWS, 2000);
                    } else {
                        document.getElementById('status-text').innerText = "Proses Berjalan di Latar Belakang";
                        const iconWrapper = document.getElementById('status-icon-wrapper');
                        if (iconWrapper) {
                            iconWrapper.className = "w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0";
                            iconWrapper.innerHTML = '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10" stroke-width="2"></circle><path stroke-width="2" d="M12 6v6l4 2"></path></svg>';
                        }
                        updateStep(1, 'error', 'Koneksi real-time terputus. Anda dapat me-refresh halaman ini.');
                    }
                };
            }

            connectWS();
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
    </x-slot>
</x-layouts.app>