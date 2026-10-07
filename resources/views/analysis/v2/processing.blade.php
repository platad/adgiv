<x-layouts.app title="Processing V2">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 animate-fade-in">
        
        {{-- Header --}}
        <div class="mb-8">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-bold text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-widest mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                <span class="lang-id">Kembali ke Dashboard</span>
                <span class="lang-en">Back to Dashboard</span>
                <span class="lang-zh">返回控制面板</span>
            </a>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase flex items-center gap-3">
                <i data-lucide="cpu" class="w-8 h-8 text-purple-600"></i>
                <span class="lang-id">Pemrosesan V2 Aktif</span>
                <span class="lang-en">V2 Processing Active</span>
                <span class="lang-zh">V2 处理中</span>
            </h1>
        </div>

        {{-- Progress Bar --}}
        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden mb-8 shadow-inner">
            <div id="progress-bar" class="bg-gradient-to-r from-purple-500 to-indigo-600 h-full rounded-full transition-all duration-700" style="width: 0%"></div>
        </div>

        {{-- Main Content Full Width Card --}}
        <div class="bg-white border border-gray-100 rounded-[2.5rem] shadow-xl shadow-gray-100/60 p-8 md:p-12">
            
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-gray-50">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center">
                        <i id="status-icon" data-lucide="loader-2" class="w-6 h-6 animate-spin"></i>
                    </div>
                    <div>
                        <p class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 mb-1">Status Pemrosesan AI</p>
                        <h2 class="text-lg font-black text-gray-900" id="status-text">Menyiapkan koneksi...</h2>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 mb-1">Progres Keseluruhan</p>
                    <p class="text-3xl font-black text-purple-600" id="progress-text">0%</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Status Tahapan --}}
                <div class="space-y-6">
                    <h3 class="text-sm font-black uppercase tracking-widest text-gray-400 mb-4">Pipeline Execution</h3>
                    
                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400" id="step1-icon">
                            <i data-lucide="radio" class="w-4 h-4"></i>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-bold text-gray-900" id="step1-title">Koneksi & Verifikasi Audio</p>
                            <p class="text-xs font-medium text-gray-500 mt-1" id="step1-desc">Menunggu inisialisasi...</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400" id="step2-icon">
                            <i data-lucide="mic" class="w-4 h-4"></i>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-bold text-gray-900" id="step2-title">WhisperX Diarization</p>
                            <p class="text-xs font-medium text-gray-500 mt-1" id="step2-desc">Menunggu transkripsi audio...</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-8 h-8 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400" id="step3-icon">
                            <i data-lucide="network" class="w-4 h-4"></i>
                        </div>
                        <div class="pt-1">
                            <p class="text-sm font-bold text-gray-900" id="step3-title">RoBERTa Multi-Task Classification</p>
                            <p class="text-xs font-medium text-gray-500 mt-1" id="step3-desc">Menunggu klasifikasi peran dan mode...</p>
                        </div>
                    </div>
                </div>

                {{-- Informasi Panel Tambahan --}}
                <div class="bg-gray-50 rounded-3xl p-8 border border-gray-100 flex flex-col justify-center text-center">
                    <div class="w-16 h-16 mx-auto rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center mb-4">
                        <i data-lucide="shield-check" class="w-8 h-8"></i>
                    </div>
                    <h4 class="text-sm font-bold text-gray-900 mb-2">Analisis Real-time Bypass Aktif</h4>
                    <p class="text-xs text-gray-500 font-medium leading-relaxed">
                        Sistem menggunakan arsitektur WebSocket langsung ke Cloud AI. Halaman ini akan secara otomatis membawa Anda ke layar hasil setelah seluruh proses analisis wacana dan pembentukan graf interaksi selesai 100%.
                    </p>
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
                
                descEl.innerText = desc;
                
                if (status === 'active') {
                    icon.className = "w-8 h-8 rounded-full bg-purple-50 border border-purple-200 flex items-center justify-center text-purple-600";
                    icon.innerHTML = '<svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>';
                    title.className = "text-sm font-black text-purple-700";
                } else if (status === 'done') {
                    icon.className = "w-8 h-8 rounded-full bg-green-50 border border-green-200 flex items-center justify-center text-green-600";
                    icon.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i>';
                    title.className = "text-sm font-bold text-gray-900";
                } else if (status === 'error') {
                    icon.className = "w-8 h-8 rounded-full bg-red-50 border border-red-200 flex items-center justify-center text-red-600";
                    icon.innerHTML = '<i data-lucide="x" class="w-4 h-4"></i>';
                    title.className = "text-sm font-bold text-red-600";
                }
                
                if(window.lucide) window.lucide.createIcons();
            }

            function connectWS() {
                ws = new WebSocket(wsUrl);

                ws.onopen = function() {
                    document.getElementById('status-text').innerText = "Terhubung dengan aman ke Cloud AI";
                    updateStep(1, 'active', 'Koneksi stabil. Menyiapkan engine...');
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
                                updateStep(1, 'done', 'Koneksi berhasil.');
                                updateStep(2, 'active', 'Memecah audio dan membedakan pembicara...');
                            } else if (data.progress >= 40 && data.progress < 80) {
                                updateStep(2, 'done', 'Transkripsi & diarization selesai.');
                                updateStep(3, 'active', 'Menganalisis relasi kuasa dengan RoBERTa...');
                            } else if (data.progress >= 80) {
                                updateStep(3, 'done', 'Klasifikasi multi-task selesai.');
                            }
                        }
                        
                        if (data.message) {
                            document.getElementById('status-text').innerText = data.message;
                        }

                        if (data.status === 'completed') {
                            document.getElementById('status-text').innerText = "Menyimpan Hasil ke Database...";
                            const icon = document.getElementById('status-icon');
                            icon.parentElement.className = "w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center";
                            icon.setAttribute('data-lucide', 'check-circle');
                            if(window.lucide) window.lucide.createIcons();
                            
                            setTimeout(() => {
                                window.location.href = "/{{ app()->getLocale() }}/analysis/v2/" + slug + "/result";
                            }, 1500);
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
                        const icon = document.getElementById('status-icon');
                        icon.parentElement.className = "w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center";
                        icon.setAttribute('data-lucide', 'clock');
                        if(window.lucide) window.lucide.createIcons();
                        
                        updateStep(1, 'error', 'Koneksi real-time gagal, harap muat ulang halaman nanti.');
                    }
                };
            }

            connectWS();
        });
    </script>
    </x-slot>
</x-layouts.app>