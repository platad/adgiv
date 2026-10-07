<x-layouts.app title="Processing V2">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 animate-fade-in">
        
        {{-- Header (Matching create.blade.php layout perfectly) --}}
        <div class="mb-8">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-bold text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-widest mb-4">
                <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                <span class="lang-id">Kembali ke Dashboard</span>
                <span class="lang-en">Back to Dashboard</span>
                <span class="lang-zh">返回控制面板</span>
            </a>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase flex items-center gap-3">
                <i data-lucide="cpu" class="w-8 h-8 text-purple-600"></i>
                <span class="lang-id">Memproses Audio V2</span>
                <span class="lang-en">Processing Audio V2</span>
                <span class="lang-zh">处理音频 V2</span>
            </h1>
            <p class="text-gray-500 font-medium mt-2" id="status-text">
                <span class="lang-id">Menghubungkan ke Sistem Cloud AI...</span>
                <span class="lang-en">Connecting to Cloud AI System...</span>
                <span class="lang-zh">正在连接云 AI 系统...</span>
            </p>
        </div>

        {{-- Processing Card (Full width, no max-w-3xl constraint) --}}
        <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-gray-200/40 border border-gray-100 relative overflow-hidden">
            
            <div class="flex justify-between items-end mb-4">
                <span class="text-xs font-black uppercase tracking-widest text-gray-400">
                    <span class="lang-id">Progres Analisis</span>
                    <span class="lang-en">Analysis Progress</span>
                    <span class="lang-zh">分析进度</span>
                </span>
                <span id="progress-text" class="text-2xl font-black text-purple-600">0%</span>
            </div>
            
            <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden relative mb-8">
                <div id="progress-bar" class="bg-gradient-to-r from-purple-600 to-indigo-600 h-4 rounded-full transition-all duration-500" style="width: 0%"></div>
            </div>

            <!-- Terminal/Console log box for realtime details -->
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 h-64 overflow-y-auto font-mono text-xs text-gray-600 shadow-inner" id="log-container">
                <div class="text-gray-400 mb-2">// System AI Log</div>
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
            const logContainer = document.getElementById('log-container');

            function addLog(msg) {
                const div = document.createElement('div');
                div.className = 'mb-1';
                const time = new Date().toLocaleTimeString();
                div.innerHTML = `<span class="text-gray-400">[${time}]</span> ${msg}`;
                logContainer.appendChild(div);
                logContainer.scrollTop = logContainer.scrollHeight;
            }

            function connectWS() {
                ws = new WebSocket(wsUrl);

                ws.onopen = function() {
                    const msg = "Terhubung ke Mesin Analisis AI. Memulai proses...";
                    document.getElementById('status-text').innerHTML = `<span class="text-purple-600 font-bold">${msg}</span>`;
                    addLog(msg);
                    ws.send(JSON.stringify({action: "start_processing"}));
                };

                ws.onmessage = function(event) {
                    try {
                        const data = JSON.parse(event.data);
                        
                        if (data.progress !== undefined) {
                            document.getElementById('progress-bar').style.width = data.progress + '%';
                            document.getElementById('progress-text').innerText = data.progress + '%';
                        }
                        if (data.message) {
                            document.getElementById('status-text').innerHTML = `<span class="text-purple-600 font-bold">${data.message}</span>`;
                            addLog(data.message);
                        }

                        if (data.status === 'completed') {
                            const msg = "Selesai! Menyimpan data...";
                            document.getElementById('status-text').innerHTML = `<span class="text-green-600 font-bold">${msg}</span>`;
                            addLog(msg);
                            setTimeout(() => {
                                window.location.href = "/{{ app()->getLocale() }}/analysis/v2/" + slug + "/result";
                            }, 1000);
                        }
                    } catch (e) {
                        console.error("WS Parse Error", e);
                    }
                };

                ws.onclose = function(e) {
                    if (retryCount < 5) {
                        retryCount++;
                        const msg = `Koneksi terputus. Menghubungkan ulang... (${retryCount}/5)`;
                        document.getElementById('status-text').innerHTML = `<span class="text-amber-600 font-bold">${msg}</span>`;
                        addLog(msg);
                        setTimeout(connectWS, 2000);
                    } else {
                        const msg = "Gagal terhubung ke Cloud AI secara real-time. Proses mungkin berjalan di latar belakang.";
                        document.getElementById('status-text').innerHTML = `<span class="text-red-600 font-bold">${msg}</span>`;
                        addLog(msg);
                    }
                };
            }

            connectWS();
        });
    </script>
    </x-slot>
</x-layouts.app>
