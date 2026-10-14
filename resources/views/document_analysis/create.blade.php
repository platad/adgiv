<x-layouts.app title="Analisis Dokumen PDF">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 animate-fade-in">

        {{-- Header Section --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <a href="{{ route('dashboard', ['locale' => app()->getLocale()]) }}" class="inline-flex items-center text-sm font-bold text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-widest mb-4">
                    <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
                    <span class="lang-id">Kembali ke Dashboard</span>
                    <span class="lang-en">Back to Dashboard</span>
                    <span class="lang-zh">返回控制面板</span>
                </a>
                <h1 class="text-3xl font-black text-gray-900 tracking-tight uppercase">
                    <span class="lang-id">Analisis Materi Dokumen PDF</span>
                    <span class="lang-en">PDF Document Analysis</span>
                    <span class="lang-zh">PDF 文档智能分析</span>
                </h1>
                <p class="text-xs text-gray-500 font-medium mt-1 leading-relaxed max-w-2xl">
                <span class="lang-id">Unggah naskah draf skripsi, proposal, modul, atau artikel ilmiah Anda untuk memperoleh ringkasan eksekutif, kesimpulan kunci, rekomendasi terarah, serta ulasan audio interaktif.</span>
                <span class="lang-en">Upload your thesis draft, proposal, or article to generate executive summary, key conclusions, actionable recommendations, and interactive voice guidance.</span>
                <span class="lang-zh">上传您的论文初稿、开题报告或学术文献，获取执行综述、核心结论、针对性修改建议与互动式语音导读。</span>
            </p>
            </div>

            <div class="shrink-0 flex items-center gap-2">
                <a href="{{ route('document_analysis.history', ['locale' => app()->getLocale()]) }}"
                   class="inline-flex items-center gap-2 px-4.5 py-2.5 rounded-2xl bg-white border border-gray-200 hover:border-gray-300 text-gray-700 hover:text-gray-900 font-bold text-xs uppercase tracking-wider shadow-sm transition hover:shadow">
                    <i data-lucide="history" class="w-4 h-4 text-gray-500"></i>
                    <span>Riwayat Dokumen</span>
                </a>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="bg-white rounded-[2.5rem] border border-gray-100 p-8 lg:p-10 shadow-xl shadow-gray-200/40 relative overflow-hidden">
            <form action="{{ route('document_analysis.initialize', ['locale' => app()->getLocale()]) }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  id="document-upload-form"
                  x-data="pdfUploader()">
                @csrf

                <input type="hidden" name="locale" value="{{ app()->getLocale() }}">

                {{-- Input Judul Dokumen --}}
                <div class="mb-6">
                    <label for="title" class="block text-[0.65rem] font-black text-gray-400 uppercase tracking-widest mb-2">
                        <span class="lang-id">Judul Naskah / Dokumen</span>
                        <span class="lang-en">Document Title</span>
                        <span class="lang-zh">文档 / 论文标题</span>
                        <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="heading" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300"></i>
                        <input type="text" 
                               id="title" 
                               name="title" 
                               required 
                               placeholder="Contoh: Draf Bab I - III Skripsi Sistem Analisis Suara"
                               class="w-full pl-12 pr-4 h-14 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-900 focus:ring-2 focus:ring-red-100 focus:border-bima-red focus:bg-white outline-none transition-all placeholder:text-gray-300"
                               value="{{ old('title') }}">
                    </div>
                    @error('title')
                        <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Drag and Drop PDF Zone --}}
                <div class="mb-8">
                    <label class="block text-[0.65rem] font-black text-gray-400 uppercase tracking-widest mb-2">
                        <span class="lang-id">Berkas PDF Dokumen</span>
                        <span class="lang-en">PDF File</span>
                        <span class="lang-zh">PDF 格式文件</span>
                        <span class="text-red-500">*</span>
                    </label>

                    {{-- Dropzone Area --}}
                    <div class="relative border-2 border-dashed rounded-[2rem] p-8 text-center transition-all duration-300 cursor-pointer overflow-hidden group"
                         :class="isDragging ? 'border-bima-red bg-red-50/30 ring-4 ring-red-100' : (hasFile ? 'border-green-300 bg-green-50/20' : 'border-gray-200 hover:border-bima-red hover:bg-gray-50/50')"
                         @dragover.prevent="isDragging = true"
                         @dragleave.prevent="isDragging = false"
                         @drop.prevent="handleDrop($event)"
                         @click="$refs.fileInput.click()">

                        <input type="file" 
                               name="pdf_file" 
                               id="pdf_file" 
                               x-ref="fileInput" 
                               accept="application/pdf,.pdf" 
                               class="hidden" 
                               @change="handleFileSelect($event)">

                        {{-- State: No File Selected --}}
                        <div x-show="!hasFile" class="flex flex-col items-center justify-center py-4">
                            <div class="w-16 h-16 rounded-2xl bg-red-50 text-bima-red flex items-center justify-center mb-4 group-hover:scale-110 transition-transform shadow-sm">
                                <i data-lucide="file-up" class="w-8 h-8"></i>
                            </div>
                            <h3 class="text-sm font-black text-gray-800 uppercase tracking-tight mb-1">
                                <span class="lang-id">Pilih atau Seret Berkas PDF ke Sini</span>
                                <span class="lang-en">Choose or Drag & Drop PDF File Here</span>
                                <span class="lang-zh">选择或拖拽 PDF 文件到此处</span>
                            </h3>
                            <p class="text-xs text-gray-400 font-medium mb-3">
                                <span class="lang-id">Maksimal 1 file PDF berukuran di bawah 10 MB</span>
                                <span class="lang-en">Maximum 1 PDF file under 10 MB</span>
                                <span class="lang-zh">单个 PDF 文件，大小不超过 10 MB</span>
                            </p>
                            <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-900 text-white text-[0.65rem] font-bold uppercase tracking-widest shadow-md hover:bg-black transition">
                                <i data-lucide="folder-search" class="w-3.5 h-3.5"></i>
                                Telusuri Berkas
                            </span>
                        </div>

                        {{-- State: File Selected Preview --}}
                        <div x-show="hasFile" x-cloak class="flex items-center justify-between bg-white border border-gray-100 p-4 rounded-2xl shadow-sm">
                            <div class="flex items-center gap-4 text-left">
                                <div class="w-12 h-12 rounded-xl bg-red-600 text-white flex items-center justify-center shrink-0 shadow-md">
                                    <i data-lucide="file-text" class="w-6 h-6"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-black text-gray-900 truncate" x-text="fileName"></p>
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <span class="text-[0.65rem] font-bold text-gray-400 uppercase" x-text="fileSizeFormatted"></span>
                                        <span class="w-1 h-1 rounded-full bg-green-500"></span>
                                        <span class="text-[0.65rem] font-bold text-green-600 uppercase">Siap Dianalisis</span>
                                    </div>
                                </div>
                            </div>
                            <button type="button" 
                                    @click.stop="removeFile()" 
                                    class="w-10 h-10 rounded-xl bg-gray-50 hover:bg-red-50 text-gray-400 hover:text-red-600 transition flex items-center justify-center shrink-0"
                                    title="Hapus file">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Error Message Display --}}
                    <div x-show="errorMessage" x-cloak class="mt-2 text-xs text-red-500 font-bold flex items-center gap-1.5">
                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                        <span x-text="errorMessage"></span>
                    </div>
                    @error('pdf_file')
                        <p class="text-xs text-red-500 font-semibold mt-2">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Action Submit Button --}}
                <div class="pt-2">
                    <button type="submit" 
                            :disabled="!hasFile"
                            class="w-full h-14 rounded-2xl bg-bima-red hover:bg-bima-red-dark text-white font-black text-sm tracking-widest uppercase transition-all flex items-center justify-center gap-3 shadow-xl shadow-red-500/20 disabled:opacity-40 disabled:cursor-not-allowed cursor-pointer hover:scale-[1.01] active:scale-[0.99]">
                        <i data-lucide="sparkles" class="w-5 h-5"></i>
                        <span>Mulai Analisis Dokumen</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Feature Highlights Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-8">
            <div class="bg-white p-5 rounded-2xl border border-gray-100 flex items-start gap-3.5 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black uppercase text-gray-900 tracking-tight">Ringkasan Eksekutif</h4>
                    <p class="text-[0.68rem] text-gray-400 font-medium mt-0.5 leading-relaxed">Ekstraksi poin inti, latar belakang, dan sintesis substansi materi skripsi secara komprehensif.</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 flex items-start gap-3.5 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <i data-lucide="target" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black uppercase text-gray-900 tracking-tight">Rekomendasi Terarah</h4>
                    <p class="text-[0.68rem] text-gray-400 font-medium mt-0.5 leading-relaxed">Ulasan kelemahan, tingkat urgensi perbaikan, dan langkah konkret bagi penulis naskah.</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-gray-100 flex items-start gap-3.5 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-green-50 text-green-600 flex items-center justify-center shrink-0">
                    <i data-lucide="volume-2" class="w-4 h-4"></i>
                </div>
                <div>
                    <h4 class="text-xs font-black uppercase text-gray-900 tracking-tight">Dual-Engine Audio</h4>
                    <p class="text-[0.68rem] text-gray-400 font-medium mt-0.5 leading-relaxed">Dengarkan ulasan eksekutif berkualitas studio plus pembacaan interaktif setiap butir rekomendasi.</p>
                </div>
            </div>
        </div>

    </div>

    <script>
        function pdfUploader() {
            return {
                isDragging: false,
                hasFile: false,
                fileName: '',
                fileSize: 0,
                fileSizeFormatted: '',
                errorMessage: '',
                maxSizeBytes: 10 * 1024 * 1024, // 10 MB

                handleDrop(e) {
                    this.isDragging = false;
                    const files = e.dataTransfer.files;
                    if (files.length > 0) {
                        this.processFile(files[0]);
                    }
                },

                handleFileSelect(e) {
                    const files = e.target.files;
                    if (files.length > 0) {
                        this.processFile(files[0]);
                    }
                },

                processFile(file) {
                    this.errorMessage = '';
                    
                    // Validasi Ekstensi PDF
                    if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
                        this.errorMessage = 'Format file tidak didukung. Mohon hanya unggah berkas dokumen PDF (.pdf).';
                        this.removeFile();
                        return;
                    }

                    // Validasi Ukuran Maksimal 10MB
                    if (file.size > this.maxSizeBytes) {
                        this.errorMessage = 'Ukuran berkas melebihi 10 MB. Ukuran berkas Anda: ' + (file.size / (1024 * 1024)).toFixed(2) + ' MB.';
                        this.removeFile();
                        return;
                    }

                    this.hasFile = true;
                    this.fileName = file.name;
                    this.fileSize = file.size;
                    this.fileSizeFormatted = (file.size >= 1048576) 
                        ? (file.size / 1048576).toFixed(2) + ' MB' 
                        : (file.size / 1024).toFixed(1) + ' KB';

                    // Update input form judul otomatis jika masih kosong
                    const titleInput = document.getElementById('title');
                    if (titleInput && (!titleInput.value || titleInput.value.trim() === '')) {
                        const cleanTitle = file.name.replace(/\.[^/.]+$/, '').replace(/[_-]/g, ' ');
                        titleInput.value = cleanTitle.charAt(0).toUpperCase() + cleanTitle.slice(1);
                    }

                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                },

                removeFile() {
                    this.hasFile = false;
                    this.fileName = '';
                    this.fileSize = 0;
                    this.fileSizeFormatted = '';
                    if (this.$refs.fileInput) {
                        this.$refs.fileInput.value = '';
                    }
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                }
            }
        }
    </script>
</x-layouts.app>
