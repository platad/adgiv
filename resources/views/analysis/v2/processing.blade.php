<x-layouts.app title="Processing V2">
    <div class="max-w-3xl mx-auto px-4 py-16 animate-fade-in text-center">
        
        <div class="mb-10">
            <div class="w-24 h-24 bg-purple-50 text-purple-600 rounded-full flex items-center justify-center mx-auto mb-6 relative">
                <!-- Ping animation -->
                <div class="absolute inset-0 rounded-full bg-purple-400 opacity-20 animate-ping"></div>
                <i data-lucide="cpu" class="w-12 h-12"></i>
            </div>
            
            <h1 class="text-3xl font-black text-gray-950 uppercase tracking-tight mb-3">
                Memproses Audio V2
            </h1>
            <p class="text-gray-500 font-medium" id="status-text">
                Menghubungkan ke Sistem Cloud AI...
            </p>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-100 max-w-xl mx-auto relative overflow-hidden">
            <div class="flex justify-between text-xs font-bold uppercase tracking-widest text-gray-400 mb-3">
                <span>Progress</span>
                <span id="progress-text" class="text-purple-600">0%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden relative">
                <div id="progress-bar" class="bg-gradient-to-r from-purple-500 to-indigo-600 h-4 rounded-full transition-all duration-500" style="width: 0%"></div>
            </div>
        </div>

    </div>

    <x-slot name="scripts">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slug = "{{ $analysis->slug }}";
            // Ganti ke URL VPS jika sudah online
            // const wsUrl = "wss://vps.temaniskripsi.id/api/v2/ws/" + slug;
            const wsUrl = "ws://localhost:8001/api/v2/ws/" + slug; 
            
            let ws;
            let retryCount = 0;

            function connectWS() {
                ws = new WebSocket(wsUrl);

                ws.onopen = function() {
                    document.getElementById('status-text').innerText = "Terhubung ke Mesin Analisis AI. Memulai proses...";
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
                        }

                        if (data.status === 'completed') {
                            document.getElementById('status-text').innerText = "Selesai! Menyimpan data...";
                            setTimeout(() => {
                                window.location.href = "/{{ app()->getLocale() }}/analysis/v2/" + slug + "/result";
                            }, 1000);
                        }
                    } catch (e) {
                        console.error("WS Parse Error", e);
                    }
                };

                ws.onclose = function(e) {
                    // Retry logic if connection drops
                    if (retryCount < 5) {
                        retryCount++;
                        document.getElementById('status-text').innerText = `Koneksi terputus. Menghubungkan ulang... (${retryCount}/5)`;
                        setTimeout(connectWS, 2000);
                    } else {
                        document.getElementById('status-text').innerText = "Gagal terhubung ke Cloud AI secara real-time. Proses mungkin berjalan di latar belakang.";
                    }
                };
            }

            connectWS();
        });
    </script>
    </x-slot>
</x-layouts.app>
