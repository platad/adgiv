<x-layouts.app title="Riwayat Analisis Dokumen PDF">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in" 
         x-data="{ 
             showDeleteModal: false, 
             deleteUrl: '', 
             deleteTitle: '',
             openDelete(url, title) {
                 this.deleteUrl = url;
                 this.deleteTitle = title;
                 this.showDeleteModal = true;
             }
         }">

        {{-- Session Flash Alert --}}
        @if (session('status'))
            <div class="mb-6 p-4 rounded-2xl bg-green-50 border border-green-200 text-green-800 flex items-center justify-between gap-3 shadow-sm animate-fade-in"
                 x-data="{ show: true }" x-show="show">
                <div class="flex items-center gap-3">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600 shrink-0"></i>
                    <p class="text-xs sm:text-sm font-bold">{{ session('status') }}</p>
                </div>
                <button type="button" @click="show = false" class="text-green-500 hover:text-green-700 p-1 cursor-pointer">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
        @endif

        {{-- Top Navigation & Action Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <a href="{{ route('dashboard', ['locale' => app()->getLocale()]) }}" 
                       class="text-[0.65rem] font-bold text-gray-400 hover:text-bima-red uppercase tracking-wider flex items-center gap-1 transition">
                        <i data-lucide="arrow-left" class="w-3 h-3"></i>
                        <span class="lang-id">Kembali ke Dashboard</span>
                        <span class="lang-en">Back to Dashboard</span>
                        <span class="lang-zh">返回仪表盘</span>
                    </a>
                </div>
                <h1 class="text-2xl lg:text-3xl font-black text-gray-900 tracking-tight uppercase">
                    <span class="lang-id">Riwayat Analisis Dokumen PDF</span>
                    <span class="lang-en">PDF Document Analysis History</span>
                    <span class="lang-zh">PDF 文档分析历史记录</span>
                </h1>
                <p class="text-xs text-gray-500 font-medium mt-1 leading-relaxed">
                    <span class="lang-id">Daftar seluruh ulasan materi PDF, intisari eksekutif, dan panduan suara AI yang telah Anda analisis.</span>
                    <span class="lang-en">Archive of all analyzed PDF documents, executive syntheses, and AI audio briefings.</span>
                    <span class="lang-zh">您已处理的所有 PDF 学术材料档案、核心摘要提炼及 AI 智能语音导读记录。</span>
                </p>
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('document_analysis.create', ['locale' => app()->getLocale()]) }}" 
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-400 hover:to-orange-500 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-orange-500/20 hover:shadow-lg hover:shadow-orange-500/30 transition-all hover:-translate-y-0.5 cursor-pointer border border-amber-300/30">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    <span class="lang-id">Analisis PDF Baru</span>
                    <span class="lang-en">New PDF Analysis</span>
                    <span class="lang-zh">新增 PDF 分析</span>
                </a>
            </div>
        </div>

        {{-- Metric Stats Row --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            {{-- Card 1: Total Dokumen --}}
            <div class="bg-white rounded-3xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-red-50 text-bima-red flex items-center justify-center shrink-0">
                    <i data-lucide="file-text" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 block">Total Dokumen</span>
                    <span class="text-2xl font-black text-gray-900 tracking-tight">{{ number_format($totalDocs) }}</span>
                    <span class="text-[0.65rem] text-gray-400 font-bold ml-1">Berkas</span>
                </div>
            </div>

            {{-- Card 2: Dokumen Selesai --}}
            <div class="bg-white rounded-3xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                    <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 block">Selesai Dianalisis</span>
                    <span class="text-2xl font-black text-gray-900 tracking-tight">{{ number_format($completedDocs) }}</span>
                    <span class="text-[0.65rem] text-green-600 font-bold ml-1">Siap Baca</span>
                </div>
            </div>

            {{-- Card 3: Total Halaman Terbaca --}}
            <div class="bg-white rounded-3xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i data-lucide="book-open" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-[0.65rem] font-black uppercase tracking-widest text-gray-400 block">Halaman Diekstraksi</span>
                    <span class="text-2xl font-black text-gray-900 tracking-tight">{{ number_format($totalPages) }}</span>
                    <span class="text-[0.65rem] text-gray-400 font-bold ml-1">Halaman</span>
                </div>
            </div>
        </div>

        {{-- Search & Filter Bar --}}
        <div class="bg-white rounded-3xl border border-gray-100 p-4 mb-6 shadow-sm">
            <form method="GET" action="{{ route('document_analysis.history', ['locale' => app()->getLocale()]) }}" 
                  class="flex flex-col sm:flex-row items-center gap-3">
                <div class="relative flex-1 w-full">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2"></i>
                    <input type="text" 
                           name="search" 
                           value="{{ $search ?? '' }}" 
                           placeholder="Cari berdasarkan judul analisis atau nama berkas PDF..."
                           class="w-full pl-11 pr-4 h-11 bg-gray-50 border border-gray-100 rounded-2xl text-xs sm:text-sm font-medium text-gray-900 focus:bg-white focus:border-bima-red focus:ring-2 focus:ring-red-100 outline-none transition">
                </div>
                
                <div class="flex items-center gap-2 w-full sm:w-auto">
                    <button type="submit" 
                            class="flex-1 sm:flex-none px-5 h-11 rounded-2xl bg-gray-900 hover:bg-black text-white text-xs font-bold uppercase tracking-wider transition cursor-pointer flex items-center justify-center gap-2">
                        <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                        <span>Cari</span>
                    </button>
                    @if(!empty($search))
                        <a href="{{ route('document_analysis.history', ['locale' => app()->getLocale()]) }}" 
                           class="px-4 h-11 rounded-2xl bg-gray-100 hover:bg-gray-200 text-gray-600 text-xs font-bold uppercase tracking-wider transition flex items-center justify-center gap-1.5"
                           title="Hapus filter pencarian">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                            <span class="hidden sm:inline">Reset</span>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Data Table Section --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            @if($analyses->isEmpty())
                {{-- Empty State --}}
                <div class="text-center py-16 px-4">
                    <div class="w-16 h-16 rounded-3xl bg-red-50 text-bima-red flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="file-question" class="w-8 h-8"></i>
                    </div>
                    <h3 class="text-base font-black text-gray-900 uppercase tracking-tight mb-1">
                        @if(!empty($search))
                            Tidak Ada Dokumen Yang Cocok
                        @else
                            Belum Ada Riwayat Analisis Dokumen
                        @endif
                    </h3>
                    <p class="text-xs text-gray-400 max-w-sm mx-auto mb-6">
                        @if(!empty($search))
                            Tidak ditemukan hasil analisis dengan kata kunci "<span class="font-bold text-gray-700">{{ $search }}</span>". Coba gunakan kata kunci lainnya.
                        @else
                            Anda belum pernah melakukan analisis dokumen PDF. Unggah naskah draf skripsi atau materi akademik Anda sekarang.
                        @endif
                    </p>
                    <a href="{{ route('document_analysis.create', ['locale' => app()->getLocale()]) }}" 
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-bold uppercase tracking-wider shadow-md transition">
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                        <span>Mulai Analisis Dokumen Pertama</span>
                    </a>
                </div>
            @else
                {{-- Desktop Table View --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-100 bg-gray-50/60 text-[0.65rem] font-black uppercase tracking-widest text-gray-400">
                                <th class="py-4 px-6">Dokumen &amp; Berkas</th>
                                <th class="py-4 px-4">Spesifikasi</th>
                                <th class="py-4 px-4">Bahasa</th>
                                <th class="py-4 px-4">Waktu Analisis</th>
                                <th class="py-4 px-4">Status</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs">
                            @foreach($analyses as $doc)
                                <tr class="hover:bg-gray-50/70 transition group">
                                    {{-- Dokumen --}}
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-red-50 text-bima-red flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                                <i data-lucide="file-text" class="w-5 h-5"></i>
                                            </div>
                                            <div class="min-w-0 max-w-md">
                                                <a href="{{ $doc->isCompleted() ? route('document_analysis.result', ['documentAnalysis' => $doc->slug, 'locale' => app()->getLocale()]) : route('document_analysis.processing', ['documentAnalysis' => $doc->slug, 'locale' => app()->getLocale()]) }}" 
                                                   class="font-black text-gray-900 hover:text-bima-red uppercase tracking-tight block truncate transition"
                                                   title="{{ $doc->title }}">
                                                    {{ $doc->title }}
                                                </a>
                                                <p class="text-[0.65rem] text-gray-400 font-medium truncate mt-0.5 flex items-center gap-1.5" title="{{ $doc->file_name }}">
                                                    <span>{{ $doc->file_name }}</span>
                                                    <span>&bull;</span>
                                                    <span>{{ $doc->formattedFileSize() }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    {{-- Spesifikasi (Pages & Words) --}}
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <span class="font-bold text-gray-800 block text-xs">
                                            {{ $doc->total_pages ?? '-' }} Halaman
                                        </span>
                                        <span class="text-[0.62rem] text-gray-400 uppercase font-semibold">
                                            {{ number_format($doc->word_count ?? 0) }} Kata
                                        </span>
                                    </td>

                                    {{-- Bahasa --}}
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-lg bg-gray-100 text-gray-700 text-[0.65rem] font-bold uppercase tracking-wider">
                                            {{ strtoupper($doc->locale ?? 'ID') }}
                                        </span>
                                    </td>

                                    {{-- Waktu --}}
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        <span class="font-bold text-gray-800 block text-xs">
                                            {{ $doc->created_at->format('d M Y') }}
                                        </span>
                                        <span class="text-[0.62rem] text-gray-400 font-semibold">
                                            {{ $doc->created_at->format('H:i') }} WIB
                                        </span>
                                    </td>

                                    {{-- Status --}}
                                    <td class="py-4 px-4 whitespace-nowrap">
                                        @if($doc->status === 'completed')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-green-50 text-green-700 border border-green-200 text-[0.65rem] font-black uppercase tracking-wider">
                                                <i data-lucide="check" class="w-3 h-3"></i>
                                                Selesai
                                            </span>
                                        @elseif($doc->status === 'failed')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-50 text-red-700 border border-red-200 text-[0.65rem] font-black uppercase tracking-wider">
                                                <i data-lucide="alert-circle" class="w-3 h-3"></i>
                                                Gagal
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[0.65rem] font-black uppercase tracking-wider">
                                                <i data-lucide="loader" class="w-3 h-3 animate-spin"></i>
                                                Memproses
                                            </span>
                                        @endif
                                    </td>

                                    {{-- Action Buttons --}}
                                    <td class="py-4 px-6 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            @if($doc->isCompleted())
                                                <a href="{{ route('document_analysis.result', ['documentAnalysis' => $doc->slug, 'locale' => app()->getLocale()]) }}" 
                                                   class="px-3.5 py-1.5 rounded-xl bg-gray-900 hover:bg-black text-white text-[0.68rem] font-bold uppercase tracking-wider transition shadow-sm"
                                                   title="Buka Hasil Analisis">
                                                    Lihat Hasil
                                                </a>
                                            @else
                                                <a href="{{ route('document_analysis.processing', ['documentAnalysis' => $doc->slug, 'locale' => app()->getLocale()]) }}" 
                                                   class="px-3.5 py-1.5 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-800 text-[0.68rem] font-bold uppercase tracking-wider transition"
                                                   title="Lanjutkan Proses">
                                                    Lanjutkan
                                                </a>
                                            @endif

                                            {{-- Unduh PDF --}}
                                            <a href="{{ route('document_analysis.pdf', ['documentAnalysis' => $doc->slug, 'locale' => app()->getLocale()]) }}" 
                                               target="_blank"
                                               class="p-2 rounded-xl bg-gray-50 hover:bg-gray-100 text-gray-500 hover:text-gray-900 transition"
                                               title="Buka / Unduh Berkas PDF Asli">
                                                <i data-lucide="external-link" class="w-4 h-4"></i>
                                            </a>

                                            {{-- Hapus --}}
                                            <button type="button" 
                                                    @click="openDelete('{{ route('document_analysis.destroy', ['documentAnalysis' => $doc->slug, 'locale' => app()->getLocale()]) }}', '{{ addslashes($doc->title) }}')"
                                                    class="p-2 rounded-xl hover:bg-red-50 text-gray-400 hover:text-red-600 transition cursor-pointer"
                                                    title="Hapus Sesi Dokumen">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile List View (< md) --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @foreach($analyses as $doc)
                        <div class="p-5 space-y-3 hover:bg-gray-50/50 transition">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-red-50 text-bima-red flex items-center justify-center shrink-0">
                                        <i data-lucide="file-text" class="w-5 h-5"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <a href="{{ $doc->isCompleted() ? route('document_analysis.result', ['documentAnalysis' => $doc->slug, 'locale' => app()->getLocale()]) : route('document_analysis.processing', ['documentAnalysis' => $doc->slug, 'locale' => app()->getLocale()]) }}" 
                                           class="font-black text-gray-900 uppercase text-xs tracking-tight line-clamp-1">
                                            {{ $doc->title }}
                                        </a>
                                        <span class="text-[0.62rem] text-gray-400 font-medium block mt-0.5">
                                            {{ $doc->created_at->format('d M Y, H:i') }} &bull; {{ $doc->formattedFileSize() }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    @if($doc->status === 'completed')
                                        <span class="px-2 py-0.5 rounded-full bg-green-50 text-green-700 border border-green-200 text-[0.6rem] font-black uppercase">Selesai</span>
                                    @elseif($doc->status === 'failed')
                                        <span class="px-2 py-0.5 rounded-full bg-red-50 text-red-700 border border-red-200 text-[0.6rem] font-black uppercase">Gagal</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-[0.6rem] font-black uppercase">Memproses</span>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between text-[0.65rem] text-gray-500 pt-1 border-t border-gray-50">
                                <span>{{ $doc->total_pages ?? '-' }} Halaman &bull; {{ number_format($doc->word_count ?? 0) }} Kata</span>
                                <span class="font-bold uppercase tracking-wider text-gray-400">Bahasa: {{ strtoupper($doc->locale ?? 'ID') }}</span>
                            </div>

                            <div class="flex items-center justify-between gap-2 pt-2">
                                <button type="button" 
                                        @click="openDelete('{{ route('document_analysis.destroy', ['documentAnalysis' => $doc->slug, 'locale' => app()->getLocale()]) }}', '{{ addslashes($doc->title) }}')"
                                        class="px-3 py-2 rounded-xl bg-red-50 hover:bg-red-100 text-red-600 text-xs font-bold uppercase tracking-wider transition flex items-center gap-1 cursor-pointer">
                                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                    <span>Hapus</span>
                                </button>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('document_analysis.pdf', ['documentAnalysis' => $doc->slug, 'locale' => app()->getLocale()]) }}" 
                                       target="_blank"
                                       class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition"
                                       title="Buka PDF">
                                        <i data-lucide="external-link" class="w-4 h-4"></i>
                                    </a>

                                    @if($doc->isCompleted())
                                        <a href="{{ route('document_analysis.result', ['documentAnalysis' => $doc->slug, 'locale' => app()->getLocale()]) }}" 
                                           class="px-4 py-2 rounded-xl bg-gray-900 hover:bg-black text-white text-xs font-bold uppercase tracking-wider transition">
                                            Lihat Hasil
                                        </a>
                                    @else
                                        <a href="{{ route('document_analysis.processing', ['documentAnalysis' => $doc->slug, 'locale' => app()->getLocale()]) }}" 
                                           class="px-4 py-2 rounded-xl bg-amber-100 hover:bg-amber-200 text-amber-800 text-xs font-bold uppercase tracking-wider transition">
                                            Lanjutkan
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination Links Footer --}}
                @if($analyses->hasPages())
                    <div class="p-6 border-t border-gray-100 bg-gray-50/50">
                        {{ $analyses->links() }}
                    </div>
                @endif
            @endif
        </div>

        {{-- Delete Confirmation Modal --}}
        <div x-show="showDeleteModal" 
             class="fixed inset-0 z-50 flex items-center justify-center overflow-x-hidden overflow-y-auto outline-none focus:outline-none"
             style="display: none;">
            {{-- Backdrop --}}
            <div x-show="showDeleteModal"
                 @click="showDeleteModal = false"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-black/40 backdrop-blur-sm"></div>

            {{-- Modal Content Card --}}
            <div x-show="showDeleteModal"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="relative w-full max-w-md mx-auto my-6 z-50 bg-white rounded-[2.5rem] shadow-2xl border border-gray-100 p-8 text-center">
                
                {{-- Alert Icon --}}
                <div class="w-16 h-16 bg-red-50 text-red-600 rounded-3xl flex items-center justify-center mx-auto mb-6">
                    <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                </div>

                <h3 class="text-xl font-black text-gray-950 uppercase tracking-wide mb-3">
                    Hapus Dokumen Analisis?
                </h3>
                <p class="text-sm text-gray-500 font-medium leading-relaxed mb-8">
                    Apakah Anda yakin ingin menghapus sesi analisis <span class="font-bold text-gray-950" x-text="deleteTitle"></span>? Berkas PDF dan ringkasan audio akan dihapus dari server secara permanen.
                </p>

                <div class="flex gap-3 justify-center">
                    <button type="button" 
                            @click="showDeleteModal = false" 
                            class="px-6 py-3 bg-gray-50 hover:bg-gray-100 text-gray-700 font-bold text-sm uppercase tracking-wider rounded-2xl transition cursor-pointer">
                        Batal
                    </button>
                    <form :action="deleteUrl" method="POST" class="m-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="px-6 py-3 bg-red-600 hover:bg-red-500 text-white font-bold text-sm uppercase tracking-wider rounded-2xl transition hover:scale-105 cursor-pointer shadow-md shadow-red-600/20">
                            Hapus Permanen
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-layouts.app>
