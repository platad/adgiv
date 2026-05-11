@props(['sessionId'])

<div
    x-data="documentUploadModal({{ $sessionId }})"
    @open-upload-modal.window="open = true"
>
    {{-- Backdrop --}}
    <div
        class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"
        x-show="open"
        x-transition.opacity.duration.200ms
        @click="open = false"
        style="display:none"
    ></div>

    {{-- Modal panel --}}
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none"
        x-show="open"
        style="display:none"
    >
        <div
            class="bg-white border border-gray-200 rounded-2xl p-6 shadow-xl w-full max-w-md pointer-events-auto"
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
        >
            <div class="flex items-start justify-between mb-5">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        Upload Dataset
                    </h3>
                    <p class="text-xs text-gray-500 mt-1">Dataset akan menjadi konteks bersama seluruh agen AI BIMA.</p>
                </div>
                <button class="text-gray-400 hover:text-red-500 transition rounded-lg p-1" @click="open = false">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            {{-- Drop zone --}}
            <div
                class="border-2 border-dashed rounded-xl p-8 text-center cursor-pointer transition-colors duration-200"
                :class="isDragging ? 'border-purple-500 bg-purple-50' : (file ? 'border-emerald-500 bg-emerald-50' : 'border-gray-300 hover:border-gray-400 hover:bg-gray-50')"
                @dragover.prevent="isDragging = true"
                @dragleave="isDragging = false"
                @drop.prevent="handleDrop($event)"
                @click="$refs.docInput.click()"
            >
                <template x-if="!file">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-10 h-10 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                        <p class="text-sm text-gray-600">Drag & drop file atau <span class="underline decoration-gray-300 hover:decoration-gray-500">klik untuk browse</span></p>
                        <p class="text-[10px] text-gray-400">Maks 10 MB &middot; PDF, DOCX, CSV, dll.</p>
                    </div>
                </template>
                <template x-if="file">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-10 h-10 text-emerald-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        <p class="text-sm font-semibold text-gray-800 truncate max-w-full" x-text="file.name"></p>
                        <p class="text-xs text-gray-500" x-text="(file.size / 1024).toFixed(1) + ' KB'"></p>
                    </div>
                </template>
                <input type="file" x-ref="docInput" class="hidden" @change="handleFileSelect($event)">
            </div>

            {{-- Progress bar --}}
            <div class="h-1 bg-gray-100 rounded-full overflow-hidden mt-4" x-show="uploading" style="display:none;">
                <div class="h-full bg-black transition-all duration-300" :style="'width:' + progress + '%'"></div>
            </div>

            {{-- Error --}}
            <p class="text-xs text-red-500 mt-2 text-center" x-show="errorMsg" x-text="errorMsg" style="display:none;"></p>

            {{-- Actions --}}
            <div class="flex justify-end gap-2 mt-6">
                <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition" @click="open = false">Batal</button>
                <button
                    class="px-4 py-2 text-sm font-medium text-white bg-black hover:bg-gray-800 rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="!file || uploading"
                    @click="upload()"
                >
                    <template x-if="!uploading">
                        <span>Upload & Proses</span>
                    </template>
                    <template x-if="uploading">
                        <span>Memproses...</span>
                    </template>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function documentUploadModal(sessionId) {
    return {
        open: false,
        file: null,
        uploading: false,
        progress: 0,
        isDragging: false,
        errorMsg: '',

        handleDrop(e) {
            this.isDragging = false;
            const f = e.dataTransfer.files[0];
            if (f) {
                this.file = f;
                this.errorMsg = '';
            }
        },

        handleFileSelect(e) {
            this.file = e.target.files[0] || null;
            this.errorMsg = '';
        },

        async upload() {
            if (!this.file) return;
            this.uploading = true; this.progress = 10; this.errorMsg = '';
            const fd = new FormData();
            fd.append('document_file', this.file);
            fd.append('session_id', sessionId);
            fd.append('_token', window.BIMA.csrfToken);
            try {
                this.progress = 40;
                // Change endpoint to generic upload
                const res = await fetch('/chat/upload-document', { method: 'POST', body: fd });
                this.progress = 90;
                const data = await res.json();
                if (data.success) {
                    this.progress = 100;
                    this.$dispatch('document-uploaded', { document: data.document });
                    setTimeout(() => { this.open = false; this.file = null; this.progress = 0; }, 600);
                } else {
                    this.errorMsg = data.message || 'Upload gagal. Coba lagi.';
                }
            } catch (err) {
                this.errorMsg = 'Terjadi kesalahan jaringan.';
            } finally {
                this.uploading = false;
            }
        },
    };
}
</script>
