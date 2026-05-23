<x-layouts.app title="Dashboard">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in">
        
        {{-- Header & Metrics --}}
        <div class="mb-10">
            <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase mb-2">Selamat Datang, {{ Auth::user()->name }}</h1>
            <p class="text-gray-500 font-medium mb-8">Pusat Analisis Komunikasi Bimbingan Akademik</p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-bima-red/10 text-bima-red flex items-center justify-center">
                        <i data-lucide="activity" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <p class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400">Total Analisa Anda</p>
                        <p class="text-2xl font-black text-gray-900">{{ $history->count() }}</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <p class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400">Akurasi Sistem Global</p>
                        <p class="text-2xl font-black text-gray-900">{{ $accuracyRate }}%</p>
                        <p class="text-xs text-gray-400 font-medium">Dari {{ $totalFeedbacks }} feedback pakar</p>
                    </div>
                </div>

                <div class="flex items-center justify-center">
                    <a href="{{ route('analysis.create') }}" class="w-full flex items-center justify-center gap-3 bg-bima-red hover:bg-bima-red-dark text-white p-6 rounded-3xl shadow-lg shadow-red-500/20 transition-all hover:scale-105 group">
                        <i data-lucide="mic" class="w-6 h-6 group-hover:animate-pulse"></i>
                        <span class="font-bold text-lg uppercase tracking-wider">Mulai Analisa Baru</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Realtime Chart Section --}}
        <div class="grid grid-cols-1 gap-8 mb-10">
            <div class="bg-white rounded-[2.5rem] p-6 md:p-8 text-gray-900 border border-gray-100 shadow-sm relative overflow-hidden">
                <div class="absolute -top-40 -right-40 w-96 h-96 bg-bima-red/[0.03] rounded-full blur-[120px] pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div>
                        <h2 class="text-base font-black uppercase tracking-widest flex items-center gap-2">
                            Monitor Evaluasi Kalimat (Realtime)
                        </h2>
                        <p class="text-xs text-gray-500 mt-1 font-medium">Metrik tingkat akurasi klasifikasi bimbingan Supervisory AI per kalimat percakapan secara global.</p>
                    </div>
                    
                    <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 rounded-2xl px-4 py-2.5">
                        <div class="text-right">
                            <span class="block text-[0.55rem] font-bold text-gray-500 uppercase tracking-widest">Rata-rata Akurasi</span>
                            <span class="text-xl font-black text-gray-900" id="accuracyPercentageText">{{ $sentenceAccuracy }}%</span>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-bima-red/10 text-bima-red flex items-center justify-center">
                            <i data-lucide="trending-up" class="w-5 h-5"></i>
                        </div>
                    </div>
                </div>

                <div class="h-64 md:h-80 w-full relative">
                    <canvas id="realtimeAccuracyChart"></canvas>
                </div>
            </div>
        </div>

        {{-- History Table Card --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden" 
             x-data="{ 
                 items: [], 
                 currentPage: 1, 
                 lastPage: 1, 
                 total: 0, 
                 loading: false,
                 showDeleteModal: false,
                 deleteTargetId: null,
                 deleteTargetTitle: '',
                 deleting: false,
                 
                 async fetchHistory(page = 1) {
                     this.loading = true;
                     try {
                         let res = await fetch(`/dashboard/history-data?page=${page}`);
                         let data = await res.json();
                         this.items = data.items;
                         this.currentPage = data.current_page;
                         this.lastPage = data.last_page;
                         this.total = data.total;
                     } catch (e) {
                         console.error(e);
                     } finally {
                         this.loading = false;
                     }
                 },

                 confirmDelete(id, title) {
                     this.deleteTargetId = id;
                     this.deleteTargetTitle = title;
                     this.showDeleteModal = true;
                 },

                 async deleteAnalysis() {
                     if (this.deleting) return;
                     this.deleting = true;
                     try {
                         let res = await fetch(`/analysis/${this.deleteTargetId}`, {
                             method: 'DELETE',
                             headers: {
                                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                 'Content-Type': 'application/json'
                             }
                         });
                         let data = await res.json();
                         if (data.status === 'success') {
                             this.showDeleteModal = false;
                             let nextPage = this.currentPage;
                             if (this.items.length === 1 && this.currentPage > 1) {
                                 nextPage--;
                             }
                             this.fetchHistory(nextPage);
                         }
                     } catch (e) {
                         console.error(e);
                     } finally {
                         this.deleting = false;
                     }
                 }
             }"
             x-init="fetchHistory(1)">
            
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <h2 class="text-sm font-black uppercase tracking-widest text-gray-800">Riwayat Analisa</h2>
            </div>
            
            <!-- Skeleton Loader -->
            <div x-show="loading && items.length === 0" class="p-6 space-y-4" style="display: none;">
                <div class="h-10 bg-gray-100 rounded-xl animate-pulse w-full"></div>
                <div class="h-10 bg-gray-100 rounded-xl animate-pulse w-full"></div>
                <div class="h-10 bg-gray-100 rounded-xl animate-pulse w-full"></div>
            </div>

            <!-- Empty State -->
            <div x-show="!loading && total === 0" class="p-12 text-center" style="display: none;">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5M9 5h6"/></svg>
                </div>
                <p class="text-gray-500 font-medium">Belum ada riwayat analisa.</p>
            </div>

            <!-- Table View (Desktop Only) -->
            <div x-show="total > 0" class="hidden sm:block overflow-x-auto" style="display: none;">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50/50 text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest">
                            <th class="p-6">Judul Sesi</th>
                            <th class="p-6">Tanggal Analisis</th>
                            <th class="p-6">Status</th>
                            <th class="p-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="item in items" :key="item.id">
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <!-- Judul -->
                                <td class="p-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 flex-shrink-0">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                                        </div>
                                        <div>
                                            <span class="font-bold text-gray-950 block text-sm" x-text="item.title"></span>
                                            <span class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider block sm:hidden" x-text="item.created_at_formatted"></span>
                                        </div>
                                    </div>
                                </td>
                                <!-- Tanggal -->
                                <td class="p-6 hidden sm:table-cell text-sm font-medium text-gray-500" x-text="item.created_at_formatted"></td>
                                <!-- Status -->
                                <td class="p-6">
                                    <span class="px-2.5 py-1 rounded-full text-[0.6rem] font-black uppercase tracking-wider inline-block border"
                                          :class="{
                                              'bg-green-50 text-green-700 border-green-100': item.status === 'completed',
                                              'bg-amber-50 text-amber-700 border-amber-100': item.status === 'pending' || item.status === 'processing',
                                              'bg-red-50 text-red-700 border-red-100': item.status === 'failed'
                                          }" 
                                          x-text="item.status">
                                    </span>
                                </td>
                                <!-- Aksi -->
                                <td class="p-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <!-- Lihat Hasil -->
                                        <template x-if="item.status === 'completed'">
                                            <a :href="item.result_route" class="px-3.5 py-1.5 bg-gray-950 hover:bg-gray-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-all cursor-pointer">
                                                Hasil
                                            </a>
                                        </template>
                                        <!-- Lanjutkan -->
                                        <template x-if="item.status === 'pending' || item.status === 'processing'">
                                            <a :href="item.processing_route" class="px-3.5 py-1.5 bg-amber-100 hover:bg-amber-200 text-amber-700 text-xs font-bold uppercase tracking-wider rounded-xl transition-all cursor-pointer">
                                                Lanjutkan
                                            </a>
                                        </template>
                                        
                                        <!-- Hapus Button -->
                                        <button @click="confirmDelete(item.id, item.title)" class="p-2 hover:bg-red-50 text-gray-400 hover:text-red-600 rounded-xl transition-all cursor-pointer" title="Hapus Analisis">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View (Mobile Only) -->
            <div x-show="total > 0" class="block sm:hidden divide-y divide-gray-100" style="display: none;">
                <template x-for="item in items" :key="item.id">
                    <div class="p-5 flex flex-col gap-4 hover:bg-gray-50/50 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 flex-shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg>
                                </div>
                                <div>
                                    <span class="font-bold text-gray-950 block text-sm" x-text="item.title"></span>
                                    <span class="text-[0.65rem] text-gray-400 font-bold uppercase tracking-wider mt-0.5 block" x-text="item.created_at_formatted"></span>
                                </div>
                            </div>
                            <span class="px-2.5 py-1 rounded-full text-[0.6rem] font-black uppercase tracking-wider inline-block border flex-shrink-0"
                                  :class="{
                                      'bg-green-50 text-green-700 border-green-100': item.status === 'completed',
                                      'bg-amber-50 text-amber-700 border-amber-100': item.status === 'pending' || item.status === 'processing',
                                      'bg-red-50 text-red-700 border-red-100': item.status === 'failed'
                                  }" 
                                  x-text="item.status">
                            </span>
                        </div>
                        
                        <div class="flex items-center justify-between border-t border-gray-50 pt-3">
                            <button @click="confirmDelete(item.id, item.title)" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-xl transition-all flex items-center gap-1.5 text-xs font-bold uppercase tracking-wider cursor-pointer">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus
                            </button>
                            
                            <div class="flex items-center gap-2">
                                <!-- Lihat Hasil -->
                                <template x-if="item.status === 'completed'">
                                    <a :href="item.result_route" class="px-4 py-2 bg-gray-950 hover:bg-gray-800 text-white text-xs font-bold uppercase tracking-wider rounded-xl transition-all cursor-pointer">
                                        Hasil
                                    </a>
                                </template>
                                <!-- Lanjutkan -->
                                <template x-if="item.status === 'pending' || item.status === 'processing'">
                                    <a :href="item.processing_route" class="px-4 py-2 bg-amber-100 hover:bg-amber-200 text-amber-700 text-xs font-bold uppercase tracking-wider rounded-xl transition-all cursor-pointer">
                                        Lanjutkan
                                    </a>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Dynamic Pagination Footer -->
            <div x-show="lastPage > 1" class="p-6 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row items-center justify-between gap-4" style="display: none;">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                    Halaman <span x-text="currentPage"></span> dari <span x-text="lastPage"></span> (<span x-text="total"></span> Sesi)
                </span>
                <div class="flex items-center gap-1.5">
                    <!-- Prev -->
                    <button @click="currentPage > 1 && fetchHistory(currentPage - 1)" 
                            :disabled="currentPage === 1"
                            class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 text-xs font-black uppercase tracking-wider disabled:opacity-40 disabled:hover:bg-transparent transition-all cursor-pointer">
                        Sebelumnya
                    </button>
                    
                    <!-- Next -->
                    <button @click="currentPage < lastPage && fetchHistory(currentPage + 1)" 
                            :disabled="currentPage === lastPage"
                            class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 text-xs font-black uppercase tracking-wider disabled:opacity-40 disabled:hover:bg-transparent transition-all cursor-pointer">
                        Berikutnya
                    </button>
                </div>
            </div>

            {{-- Confirmation Delete Modal (Sleek Blur & Scale Transition) --}}
            <div x-show="showDeleteModal" 
                 class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none"
                 style="display: none;">
                <!-- Backdrop overlay -->
                <div x-show="showDeleteModal"
                     @click="showDeleteModal = false"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>

                <!-- Modal Dialog Card -->
                <div x-show="showDeleteModal"
                     x-transition:enter="transition ease-out duration-300 transform"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200 transform"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="relative w-full max-w-md mx-auto my-6 z-50 bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 p-8 text-center">
                    
                    <!-- Warning Icon -->
                    <div class="w-16 h-16 bg-red-50 text-red-600 rounded-3xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>

                    <h3 class="text-xl font-black text-gray-950 uppercase tracking-wide mb-3">Hapus Analisis?</h3>
                    <p class="text-sm text-gray-500 font-medium leading-relaxed mb-8">
                        Apakah Anda yakin ingin menghapus sesi bimbingan <span class="font-bold text-gray-950" x-text="deleteTargetTitle"></span> ini? Seluruh data transkripsi dan rekaman audio akan dihapus selamanya.
                    </p>

                    <div class="flex gap-3 justify-center">
                        <button @click="showDeleteModal = false" class="px-6 py-3 bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold text-sm uppercase tracking-wider rounded-2xl transition-all cursor-pointer">
                            Batal
                        </button>
                        <button @click="deleteAnalysis()" class="px-6 py-3 bg-red-600 hover:bg-red-500 text-white font-bold text-sm uppercase tracking-wider rounded-2xl transition-all hover:scale-105 flex items-center justify-center gap-2 cursor-pointer">
                            <span x-show="!deleting">Hapus Permanen</span>
                            <span x-show="deleting" class="flex items-center gap-1.5" style="display: none;">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menghapus...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Script for Chart.js Realtime logic --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('realtimeAccuracyChart').getContext('2d');
            
            // Create gradient fill
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(204, 0, 0, 0.25)');
            gradient.addColorStop(0.5, 'rgba(204, 0, 0, 0.05)');
            gradient.addColorStop(1, 'rgba(204, 0, 0, 0.0)');

            const config = {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Tingkat Akurasi (%)',
                        data: [],
                        borderColor: '#cc0000',
                        borderWidth: 3,
                        pointBackgroundColor: '#cc0000',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 0, // Remove bullet points on line
                        pointHoverRadius: 6,
                        pointHitRadius: 10,
                        tension: 0.4,
                        fill: true,
                        backgroundColor: gradient,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: 'rgba(17, 24, 39, 0.95)',
                            titleColor: '#ffffff',
                            bodyColor: '#e5e7eb',
                            borderColor: 'rgba(255, 255, 255, 0.1)',
                            borderWidth: 1,
                            padding: 12,
                            cornerRadius: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return `Akurasi: ${context.parsed.y}%`;
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.03)',
                                borderColor: 'transparent'
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                }
                            }
                        },
                        y: {
                            min: 60,
                            max: 100,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.03)',
                                borderColor: 'transparent'
                            },
                            ticks: {
                                color: '#6b7280',
                                stepSize: 10,
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                },
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            };

            const chart = new Chart(ctx, config);

            // Fetch actual database records
            async function updateChartWithRealData() {
                try {
                    let res = await fetch('{{ route('dashboard.realtime-data') }}');
                    let result = await res.json();
                    
                    if (result.labels && result.data) {
                        chart.data.labels = result.labels;
                        chart.data.datasets[0].data = result.data;
                        chart.update('none'); // smooth redraw without layout flash

                        // Dynamic percentage update in corner
                        const currentBadge = document.getElementById('accuracyPercentageText');
                        if (currentBadge) {
                            currentBadge.textContent = result.current_accuracy + '%';
                        }
                    }
                } catch (e) {
                    console.error("Gagal memuat data grafik realtime:", e);
                }
            }

            // Initial load
            updateChartWithRealData();

            // Periodically refresh the data from the database every 5 seconds
            setInterval(updateChartWithRealData, 5000);
        });
    </script>
</x-layouts.app>
