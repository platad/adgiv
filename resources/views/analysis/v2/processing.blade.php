<x-layouts.app title="Processing V2">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 animate-fade-in">
        
        <div class="mb-10 text-center">
            <div class="w-24 h-24 bg-gray-50 text-gray-900 rounded-full flex items-center justify-center mx-auto mb-6">
                <i data-lucide="cpu" class="w-12 h-12"></i>
            </div>
            
            <h1 class="text-3xl font-black text-gray-900 uppercase tracking-tight mb-3">
                Memproses Audio V2
            </h1>
            <p class="text-gray-500 font-medium" id="status-text">
                Menghubungkan ke Sistem Cloud AI...
            </p>
        </div>

        <!-- Card tanpa max-w-3xl agar melebar penuh menyesuaikan max-w-7xl container, sama seperti create.blade.php -->
        <div class="bg-white rounded-[2.5rem] p-8 md:p-10 border border-gray-100 shadow-xl shadow-gray-200/40 relative overflow-hidden">
            <div class="flex justify-between text-xs font-bold uppercase tracking-widest text-gray-400 mb-4">
                <span>Progress</span>
                <span id="progress-text" class="text-gray-900">0%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden relative mb-6">
                <div id="progress-bar" class="bg-gray-900 h-4 rounded-full transition-all duration-500" style="width: 0%"></div>
            </div>

            <!-- Terminal/Console log box for realtime details -->
            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100 h-48 overflow-y-auto font-mono text-xs text-gray-600" id="log-container">
                <div class="text-gray-400 mb-2">// Server Log</div>
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
