<x-layouts.app title="Processing V2">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 animate-fade-in">
        
        <div class="max-w-3xl mx-auto text-center">
            <div class="mb-10">
                <div class="w-24 h-24 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-6 relative">
                    <!-- Ping animation -->
                    <div class="absolute inset-0 rounded-full bg-purple-400 opacity-20 animate-ping"></div>
                    <i data-lucide="cpu" class="w-12 h-12"></i>
                </div>
                
                <h1 class="text-3xl font-black text-gray-950 uppercase tracking-tight mb-3">
                    <span class="lang-id">Memproses Audio V2</span>
                    <span class="lang-en">Processing Audio V2</span>
                    <span class="lang-zh">处理音频 V2</span>
                </h1>
                <p class="text-gray-500 font-medium" id="status-text">
                    <span class="lang-id">Menghubungkan ke Sistem Cloud AI...</span>
                    <span class="lang-en">Connecting to Cloud AI System...</span>
                    <span class="lang-zh">正在连接云 AI 系统...</span>
                </p>
            </div>

            <div class="bg-white rounded-[2.5rem] p-8 md:p-10 shadow-xl shadow-gray-200/40 border border-gray-100 relative overflow-hidden text-left">
                <div class="flex justify-between text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">
                    <span class="lang-id">Progres</span>
                    <span id="progress-text" class="text-gray-900">0%</span>
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
                    document.getElementById('status-text').innerText = msg;
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
                            document.getElementById('status-text').innerText = data.message;
                            addLog(data.message);
                        }

                        if (data.status === 'completed') {
                            const msg = "Selesai! Menyimpan data...";
                            document.getElementById('status-text').innerText = msg;
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
                        document.getElementById('status-text').innerText = msg;
                        addLog(msg);
                        setTimeout(connectWS, 2000);
                    } else {
                        const msg = "Gagal terhubung ke Cloud AI secara real-time. Proses mungkin berjalan di latar belakang.";
                        document.getElementById('status-text').innerText = msg;
                        addLog(msg);
                    }
                };
            }

            connectWS();
        });
    </script>
    </x-slot>
</x-layouts.app>
