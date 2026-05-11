{{--
    Component: voice-input
    Handles: Web Speech API recognition + audio blob upload + text submission.
    Communicates upward via Alpine dispatch events: 'submit-transcription'.
--}}

<div class="w-full bg-white relative rounded-2xl border border-gray-300 shadow-sm focus-within:ring-1 focus-within:ring-gray-300 transition-shadow pb-14" x-data="voiceInputWidget()">

    {{-- ── Transcription Preview ── --}}
    <div class="absolute bottom-full mb-2 left-0 right-0 flex items-start gap-2 bg-purple-50 border border-purple-200 rounded-xl p-3 shadow-sm z-10" x-show="transcript.length > 0 && isRecording" style="display: none;">
        <div class="w-2 h-2 mt-1.5 rounded-full bg-red-500 animate-pulse shrink-0"></div>
        <p class="flex-1 text-sm text-purple-800 leading-relaxed break-words" x-text="transcript"></p>
    </div>

    {{-- Attached Audio Cards --}}
    <div class="absolute bottom-full mb-3 left-0 right-0 flex flex-wrap gap-2 px-2 z-10" x-show="files.length > 0" style="display: none;">
        <template x-for="f in files" :key="f.id">
            <div class="flex items-center gap-3 bg-white border border-gray-200 rounded-2xl p-2 shadow-sm relative pr-8 max-w-[200px] animate-fade-in-up">
                <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 border border-purple-100/50">
                    <template x-if="f.status === 'uploading'">
                        <div class="w-4 h-4 border-2 border-purple-300 border-t-purple-600 rounded-full animate-spin"></div>
                    </template>
                    <template x-if="f.status === 'done'">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 18v3c0 .6.4 1 1 1h4v-3h3v-3h2l1.4-1.4a6.5 6.5 0 1 0-4-4Z"/><circle cx="16.5" cy="7.5" r=".5"/></svg>
                    </template>
                    <template x-if="f.status === 'error'">
                        <svg class="w-4 h-4 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
                    </template>
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="text-[0.8rem] font-semibold text-gray-800 truncate" x-text="f.name" :title="f.name"></span>
                    <span class="text-[0.65rem] font-medium" :class="f.status === 'error' ? 'text-red-500' : 'text-gray-500'" x-text="f.status === 'error' ? 'Gagal' : f.size"></span>
                </div>
                <button class="absolute top-1.5 right-1.5 p-1 text-gray-400 hover:text-red-500 rounded-full hover:bg-gray-100 transition" @click="files = files.filter(x => x.id !== f.id)">
                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
        </template>
    </div>

    {{-- Document link indicator --}}
    <div class="absolute -top-3 left-4 px-3 py-1 bg-white text-gray-800 text-[0.7rem] font-medium rounded-xl border border-gray-200 flex items-center gap-2 shadow-sm" x-show="linkedDoc && files.length === 0" style="display: none;">
        <svg class="w-3.5 h-3.5 text-blue-500 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
        <span x-text="linkedDoc" class="max-w-[150px] truncate"></span>
    </div>

    {{-- Text input --}}
    <textarea
        id="chat-text-input"
        class="w-full bg-transparent border-none focus:ring-0 resize-none p-3.5 max-h-[140px] text-gray-800 text-[0.95rem] leading-relaxed placeholder-gray-400 outline-none"
        x-model="transcript"
        placeholder="Ketik pesan atau gunakan mikrofon..."
        rows="1"
        @keydown.enter.prevent="!$event.shiftKey && submitTranscript()"
        @input="autoResize($event.target)"
    ></textarea>

    {{-- Bottom Actions Row --}}
    <div class="absolute bottom-2 left-2 right-2 flex items-center justify-between">
        <div class="flex items-center gap-1">
            {{-- Upload Audio button --}}
            <button
                id="btn-upload-file"
                class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-gray-800 transition disabled:opacity-50"
                @click="$refs.genericFileInput.click()"
                title="Upload Rekaman Suara (Audio)"
                :disabled="isLoading"
            >
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
            </button>
            <input type="file" x-ref="genericFileInput" class="hidden" accept="audio/*,.m4a,.mp3,.wav,.ogg,.webm,.mp4" @change="handleFileSelect($event)">
            
            {{-- Mic / Stop button --}}
            <button
                id="btn-mic"
                class="w-8 h-8 rounded-lg flex items-center justify-center transition"
                :class="isRecording ? 'bg-red-100 text-red-600 hover:bg-red-200' : 'text-gray-500 hover:bg-gray-100 hover:text-gray-800'"
                @click="toggleRecording()"
                :title="isRecording ? 'Stop Rekam' : 'Mulai Rekam Suara'"
            >
                <template x-if="!isRecording">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>
                </template>
                <template x-if="isRecording">
                    <div class="w-2.5 h-2.5 rounded-sm bg-red-600 animate-pulse"></div>
                </template>
            </button>
        </div>

        <div class="flex items-center gap-2">
            <span class="text-xs text-gray-400" x-show="statusHint" x-text="statusHint"></span>
            {{-- Send button --}}
            <button
                id="btn-send"
                class="w-8 h-8 rounded-lg flex items-center justify-center bg-black text-white hover:bg-gray-800 transition disabled:opacity-30 disabled:hover:bg-black shadow-sm"
                @click="submitTranscript()"
                :disabled="(!transcript.trim() && files.length === 0) || isLoading"
                title="Kirim Analisis"
            >
                <template x-if="!isLoading">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 7-7 7 7"/><path d="M12 19V5"/></svg>
                </template>
                <template x-if="isLoading">
                    <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                </template>
            </button>
        </div>
    </div>
    
    {{-- Drag drop overlay for the whole widget --}}
    <div 
        class="absolute inset-0 bg-purple-500/10 border-2 border-dashed border-purple-500 rounded-2xl flex flex-col items-center justify-center z-50 transition-opacity pointer-events-none"
        x-show="isDraggingDoc"
        x-transition
        style="display:none;"
    >
        <svg class="w-8 h-8 text-purple-600 mb-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
        <span class="text-sm font-semibold text-purple-700 bg-white/80 px-3 py-1 rounded-full shadow-sm">Jatuhkan file di sini</span>
    </div>
    <div 
        class="absolute inset-0 z-40 cursor-copy" 
        @dragover.prevent="isDraggingDoc = true" 
        @dragleave.prevent="isDraggingDoc = false" 
        @drop.prevent="handleDocDrop($event)"
        x-show="isDraggingDoc"
    ></div>
</div>

<script>
function voiceInputWidget() {
    return {
        transcript: '',
        isRecording: false,
        isLoading: false,
        statusHint: '',
        linkedDoc: null,
        recognition: null,
        isDraggingDoc: false,
        files: [], // audio files attached

        formatBytes(bytes) {
            if(bytes === 0) return '0 Bytes';
            const k = 1024, dm = 2, sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        },

        init() {
            this.$el.addEventListener('set-linked-doc', e => { this.linkedDoc = e.detail.name; });
            
            // Listen for window drag to show dropzone
            window.addEventListener('dragover', (e) => {
                if (e.dataTransfer.types.includes('Files')) {
                    this.isDraggingDoc = true;
                }
            });
        },

        handleDocDrop(e) {
            this.isDraggingDoc = false;
            const file = e.dataTransfer.files[0];
            if (file) {
                this.processSelectedFile(file);
            }
        },

        handleFileSelect(event) {
            const file = event.target.files[0];
            if (!file) return;
            this.processSelectedFile(file);
            event.target.value = '';
        },

        processSelectedFile(file) {
            if (file.type.startsWith('audio/') || file.type.startsWith('video/mp4') || file.name.match(/\.(m4a|mp3|wav|ogg|webm|aac|mp4)$/i)) {
                if (this.files.length >= 5) {
                    this.statusHint = 'Maksimal 5 file audio!';
                    setTimeout(() => { this.statusHint = '' }, 3000);
                    return;
                }
                const fileId = Date.now() + Math.random();
                this.files.push({
                    id: fileId,
                    name: file.name,
                    size: this.formatBytes(file.size),
                    status: 'uploading',
                    transcription: ''
                });
                this.uploadAudio(file, fileId);
            } else {
                this.statusHint = 'Saat ini lampiran chat hanya mendukung file Audio.';
                setTimeout(() => { this.statusHint = '' }, 4000);
            }
        },

        autoResize(el) {
            el.style.height = 'auto';
            el.style.height = Math.min(el.scrollHeight, 140) + 'px';
        },

        toggleRecording() {
            if (this.isRecording) {
                this.stopRecording();
            } else {
                this.startRecording();
            }
        },

        startRecording() {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            if (!SpeechRecognition) {
                this.statusHint = 'Browser tidak mendukung Web Speech API.';
                return;
            }
            this.recognition = new SpeechRecognition();
            this.recognition.lang = 'id-ID';
            this.recognition.continuous = true;
            this.recognition.interimResults = true;

            let finalTranscript = this.transcript;
            this.recognition.onresult = (e) => {
                let interim = '';
                for (let i = e.resultIndex; i < e.results.length; i++) {
                    if (e.results[i].isFinal) {
                        finalTranscript += e.results[i][0].transcript + ' ';
                    } else {
                        interim += e.results[i][0].transcript;
                    }
                }
                this.transcript = finalTranscript + interim;
            };
            this.recognition.onerror = (e) => {
                this.statusHint = 'Error: ' + e.error;
                this.isRecording = false;
            };
            this.recognition.onend = () => { this.isRecording = false; };
            this.recognition.start();
            this.isRecording = true;
            this.statusHint = 'Sedang merekam...';
        },

        stopRecording() {
            if (this.recognition) {
                this.recognition.stop();
            }
            this.isRecording = false;
            this.statusHint = '';
        },

        async uploadAudio(file, fileId) {
            this.isLoading = true;
            this.statusHint = 'Mengunggah ke Groq...';
            
            try {
                const formData = new FormData();
                formData.append('audio', file);

                const response = await fetch('/chat/transcribe', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                const data = await response.json();
                const f = this.files.find(x => x.id === fileId);
                
                if (data.success && data.transcription) {
                    if (f) {
                        f.transcription = data.transcription;
                        f.status = 'done';
                    }
                    this.statusHint = 'Selesai!';
                } else {
                    throw new Error(data.message || 'Gagal transkripsi.');
                }
            } catch (err) {
                console.error('[BIMA AI] Groq Upload Error:', err);
                const f = this.files.find(x => x.id === fileId);
                if (f) f.status = 'error';
                this.statusHint = 'Gagal Transkripsi.';
            } finally {
                this.isLoading = this.files.some(f => f.status === 'uploading');
                if (!this.isLoading) {
                    setTimeout(() => { this.statusHint = '' }, 2000);
                }
            }
        },

        submitTranscript() {
            if (this.isLoading) return;
            let finalOutput = this.transcript.trim();
            const doneFiles = this.files.filter(f => f.status === 'done' && f.transcription);
            if (!finalOutput && doneFiles.length === 0) return;
            
            this.$dispatch('submit-transcription', { 
                text: finalOutput,
                files: doneFiles.map(f => ({
                    name: f.name,
                    size: f.size,
                    transcription: f.transcription
                }))
            });
            
            this.transcript = '';
            this.statusHint = '';
            this.files = [];
            const el = document.getElementById('chat-text-input');
            if(el) el.style.height = 'auto';
        },
    };
}
</script>
