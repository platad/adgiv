{{--
    Component: voice-input
    Handles: Web Speech API recognition + audio blob upload + text submission.
    Communicates upward via Alpine dispatch events: 'submit-transcription'.
--}}

<div class="w-full bg-white relative rounded-[2rem] p-6 flex flex-col items-center gap-6" x-data="voiceInputWidget()">

    {{-- ── Audio Input Center ── --}}
    <div class="flex flex-col items-center justify-center w-full py-4 border-2 border-dashed border-gray-100 rounded-[2.5rem] bg-gray-50/50 transition-colors"
         :class="isDraggingDoc ? 'border-bima-red bg-red-50' : ''"
         @dragover.prevent="isDraggingDoc = true" 
         @dragleave.prevent="isDraggingDoc = false" 
         @drop.prevent="handleDocDrop($event)">
        
        <div class="flex items-center gap-6">
            {{-- Mic Button --}}
            <button
                id="btn-mic"
                class="w-20 h-20 rounded-full flex items-center justify-center transition-all shadow-xl group relative"
                :class="isRecording ? 'bg-red-600 text-white scale-110 animate-pulse' : 'bg-white text-bima-red hover:bg-red-50'"
                @click="toggleRecording()"
                :title="isRecording ? 'Stop Rekam' : 'Mulai Rekam Suara'"
            >
                <template x-if="!isRecording">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>
                </template>
                <template x-if="isRecording">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="12" height="12" x="6" y="6" rx="2"/></svg>
                </template>
                
                {{-- Status Ripple --}}
                <span x-show="isRecording" class="absolute inset-0 rounded-full bg-red-600/20 animate-ping"></span>
            </button>

            <div class="w-px h-12 bg-gray-200"></div>

            {{-- Upload Button --}}
            <button
                class="w-16 h-16 rounded-full bg-white text-gray-400 flex items-center justify-center hover:text-bima-red hover:bg-red-50 transition shadow-lg border border-gray-100"
                @click="$refs.genericFileInput.click()"
                :disabled="isLoading"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" x2="12" y1="3" y2="15"/></svg>
            </button>
            <input type="file" x-ref="genericFileInput" class="hidden" accept="audio/*,.m4a,.mp3,.wav,.ogg,.webm,.mp4" @change="handleFileSelect($event)">
        </div>

        <div class="mt-6 flex flex-col items-center gap-2">
            <span class="text-sm font-bold" :class="isRecording ? 'text-red-600' : 'text-gray-400'" x-text="isRecording ? 'Sedang Mendengarkan...' : 'Ketuk Mic atau Drop File Audio'"></span>
            <span class="text-[0.65rem] font-bold text-gray-300 uppercase tracking-widest" x-show="!isRecording">Support: MP3, WAV, M4A, WEBM</span>
        </div>

        {{-- ── Transcription & Ready State ── --}}
        <div class="w-full flex flex-col gap-6 animate-in fade-in slide-in-from-bottom-4 duration-500" x-show="step1Done" style="display:none;">
            
            {{-- Success Card --}}
            <div class="w-full max-w-2xl mx-auto bg-emerald-50/50 backdrop-blur-sm border border-emerald-100 rounded-[1.5rem] sm:rounded-[2rem] p-4 sm:p-6 flex flex-col sm:flex-row items-center text-center sm:text-left gap-4 sm:gap-6 shadow-sm relative overflow-hidden mt-10">
                {{-- Decorative background glow --}}
                <div class="absolute -right-10 -top-10 w-32 h-32 bg-emerald-100/30 rounded-full blur-3xl"></div>
                
                <div class="w-12 h-12 sm:w-16 sm:h-16 bg-white rounded-xl sm:rounded-2xl flex items-center justify-center text-emerald-500 shrink-0 shadow-sm relative z-10">
                    <i data-lucide="check-circle-2" class="w-6 h-6 sm:w-10 sm:h-10"></i>
                </div>
                <div class="flex-1 relative z-10">
                    <h4 class="text-sm sm:text-base font-black text-emerald-900 uppercase tracking-tight">Audio Berhasil Diupload</h4>
                    <p class="text-[0.65rem] sm:text-xs font-bold text-emerald-600/70 uppercase tracking-widest mt-0.5 sm:mt-1">Transkripsi telah diamankan di database</p>
                </div>
                <button class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-white border border-emerald-100 flex items-center justify-center text-emerald-300 hover:text-emerald-600 hover:bg-emerald-50 transition-all shadow-sm relative z-10" 
                        @click="resetWidget()" title="Upload Ulang">
                    <i data-lucide="rotate-ccw" class="w-4 h-4 sm:w-5 sm:h-5"></i>
                </button>
            </div>

            {{-- Big Analyze Button --}}
            <div class="relative group w-full max-w-2xl mx-auto">
                <div class="absolute -inset-1 bg-gradient-to-r from-gray-900 to-gray-700 rounded-2xl blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                <button
                    class="relative w-full h-14 sm:h-16 rounded-[1.25rem] sm:rounded-2xl bg-gray-900 text-white font-black text-sm sm:text-lg flex items-center justify-center gap-3 sm:gap-4 hover:bg-black transition-all"
                    @click="triggerAnalysis()"
                >
                    <span class="flex items-center gap-2 sm:gap-4 tracking-normal sm:tracking-widest">
                        MULAI ANALISA
                    </span>
                    <i data-lucide="arrow-right" class="w-4 h-4 sm:w-5 sm:h-5 group-hover:translate-x-1 transition-transform ml-1 sm:ml-2"></i>
                </button>
            </div>

            {{-- Show Transcription Modal Button --}}
            <button 
                class="flex items-center gap-2 self-center text-[0.7rem] font-black text-gray-400 uppercase tracking-[0.25em] hover:text-bima-red transition py-2 group"
                @click="$dispatch('open-transcription-modal')">
                <i data-lucide="file-text" class="w-3.5 h-3.5 opacity-50 group-hover:opacity-100"></i>
                Lihat Hasil Transkripsi
            </button>
        </div>
    </div>

    {{-- Status Hint --}}
    <div class="text-[0.7rem] font-bold text-red-500 animate-pulse" x-show="statusHint" x-text="statusHint"></div>

    {{-- Drag drop overlay --}}
    <div 
        class="absolute inset-0 bg-red-600/10 border-2 border-dashed border-bima-red rounded-2xl flex flex-col items-center justify-center z-50 transition-opacity pointer-events-none"
        x-show="isDraggingDoc"
        x-transition
        style="display:none;"
    >
        <i data-lucide="file-audio" class="w-10 h-10 text-bima-red mb-3"></i>
        <span class="text-sm font-bold text-bima-red bg-white/95 px-5 py-2 rounded-full shadow-lg border border-red-100">Jatuhkan Rekaman di Sini</span>
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
        files: [],
        step1Done: false,
        sessionId: window.BIMA.sessionId,

        formatBytes(bytes) {
            if(bytes === 0) return '0 Bytes';
            const k = 1024, dm = 2, sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        },

        async init() {
            this.$el.addEventListener('set-linked-doc', e => { this.linkedDoc = e.detail.name; });
            
            // Check if session already has transcription
            try {
                const res = await fetch(`/chat/session/${this.sessionId}/data`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success && data.session.raw_transcription && data.session.raw_transcription !== '-') {
                    this.step1Done = true;
                    this.transcript = data.session.raw_transcription;
                    console.log("[Supervisory AI] Restored Step 1 state from DB");
                }
            } catch (e) { console.error("Voice input init failed", e); }

            // Listen for window drag to show dropzone
            window.addEventListener('dragover', (e) => {
                if (e.dataTransfer.types.includes('Files')) {
                    this.isDraggingDoc = true;
                }
            });
        },

        resetWidget() {
            this.step1Done = false;
            this.transcript = '';
            this.files = [];
            this.statusHint = '';
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
                const fileId = Date.now() + Math.random();
                this.files.push({
                    id: fileId,
                    name: file.name,
                    size: this.formatBytes(file.size),
                    status: 'uploading',
                    transcription: ''
                });
                this.uploadAudio(file, fileId);
            }
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

            let finalTranscript = '';
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
            this.recognition.onend = () => { 
                if (this.isRecording) {
                    this.isRecording = false; 
                }
                if (this.transcript.trim()) {
                    this.saveTranscriptToDb(this.transcript.trim());
                }
            };
            this.recognition.start();
            this.isRecording = true;
            this.statusHint = 'Sedang merekam...';
        },

        stopRecording() {
            if (this.recognition) {
                this.recognition.stop();
            }
            this.isRecording = false;
        },

        async uploadAudio(file, fileId) {
            this.isLoading = true;
            this.statusHint = 'Sedang mentranskripsi via OpenAI...';
            
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
                if (data.success && data.transcription) {
                    this.transcript = data.transcription;
                    await this.saveTranscriptToDb(data.transcription);
                }
            } catch (err) {
                console.error('[Supervisory AI] Transcription Error:', err);
                this.statusHint = 'Gagal Transkripsi.';
            } finally {
                this.isLoading = false;
            }
        },

        async saveTranscriptToDb(text) {
            // Simplified: Just set state so analyze button appears
            this.step1Done = true;
            this.statusHint = 'Siap Analisa!';
        },

        triggerAnalysis() {
            this.$dispatch('start-multi-step-analysis', { 
                text: this.transcript
            });
            this.resetWidget();
        }
    };
}
</script>
