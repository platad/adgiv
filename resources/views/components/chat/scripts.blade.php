{{-- resources/views/components/chat/scripts.blade.php --}}
@props(['activeSession'])
<x-chat.scripts.markdown />
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('chatApp', (sessionId, sessionTitle) => ({
        sessionId: sessionId,
        currentSessionTitle: sessionTitle,
        isAnalysing: false,
        messages: [],
        activeStep: null,
        transcriptionModalOpen: false,
        session: {
            raw_transcription: '-',
            refined_transcription: '-',
            matched_transcription: '-',
            advice_category: '-',
            character_category: '-',
            intonation_analysis: '-',
            summary_domain: '-',
            aim_target: '-',
            suggestions: '-'
        },

        async init() {
            console.log("[BIMA AI] Component Initialized", this.sessionId);
            try {
                const res = await this.apiFetch(`/chat/session/${this.sessionId}/data`);
                const data = await res.json();
                if (data.success) {
                    this.session = { ...this.session, ...data.session };
                }
            } catch (e) { console.error("Init session fetch failed", e); }
            
            await this.loadMessages();
            this.scrollToBottom();
        },

        // --- Load Methods ---
        async loadMessages() {
            try {
                const res = await this.apiFetch(`/chat/session/${this.sessionId}/messages`);
                const data = await res.json();
                if (data.success) {
                    this.messages = data.messages;
                    if (this.messages.length === 0) {
                        this.messages.push({
                            id: 'greeting', role: 'assistant',
                            content: 'Halo! Saya BIMA. Apa yang bisa saya bantu analisis hari ini?',
                            created_at: new Date()
                        });
                    }
                    this.$nextTick(() => this.scrollToBottom());
                }
            } catch (_) {}
        },

        // --- Submit Methods ---
        async handleMultiStepAnalysis(text) {
            console.log("[BIMA AI] Starting Multi-Step Analysis", { text });
            if (this.isAnalysing) return;
            
            this.isAnalysing = true;
            const typingId = 'typing-' + Date.now();
            this.messages.push({ id: typingId, role: 'assistant', content: '', created_at: new Date(), isTyping: true });
            
            try {
                // Step 1: Save Transcription
                console.log("[BIMA AI] Step 1: Saving Transcription Starting...");
                this.activeStep = 1;
                const res1 = await this.apiFetch('/chat/analyse/step-1', {
                    method: 'POST',
                    body: JSON.stringify({ session_id: this.sessionId, transcription: text })
                });
                const data1 = await res1.json();
                if (!data1.success) throw new Error(data1.message || 'Gagal di Langkah 1');
                console.log("[BIMA AI] Step 1 Finished", data1);
                this.session.raw_transcription = text;

                console.log("[BIMA AI] Step 2: Refine Starting...");
                this.activeStep = 2;
                const res2 = await this.apiFetch('/chat/analyse/step-2', {
                    method: 'POST',
                    body: JSON.stringify({ session_id: this.sessionId })
                });
                const data2 = await res2.json();
                if (!data2.success) throw new Error(data2.message || 'Gagal di Langkah 2');
                console.log("[BIMA AI] Step 2 Finished", data2);
                if (data2.data) this.session.refined_transcription = data2.data.refined_text;

                // Step 3: Matching
                console.log("[BIMA AI] Step 3: Matching Starting...");
                this.activeStep = 3;
                const res3 = await this.apiFetch('/chat/analyse/step-3', {
                    method: 'POST',
                    body: JSON.stringify({ session_id: this.sessionId })
                });
                const data3 = await res3.json();
                if (!data3.success) throw new Error(data3.message || 'Gagal di Langkah 3');
                console.log("[BIMA AI] Step 3 Finished", data3);
                if (data3.data) this.session.matched_transcription = data3.data.matched_text;

                // Step 4: Advice
                console.log("[BIMA AI] Step 4: Advice Starting...");
                this.activeStep = 4;
                const res4 = await this.apiFetch('/chat/analyse/step-4', {
                    method: 'POST',
                    body: JSON.stringify({ session_id: this.sessionId })
                });
                const data4 = await res4.json();
                if (!data4.success) throw new Error(data4.message || 'Gagal di Langkah 4');
                console.log("[BIMA AI] Step 4 Finished", data4);
                if (data4.data) this.session.advice_category = data4.data.category;

                // Step 5: Character
                console.log("[BIMA AI] Step 5: Character Starting...");
                this.activeStep = 5;
                const res5 = await this.apiFetch('/chat/analyse/step-5', {
                    method: 'POST',
                    body: JSON.stringify({ session_id: this.sessionId })
                });
                const data5 = await res5.json();
                if (!data5.success) throw new Error(data5.message || 'Gagal di Langkah 5');
                console.log("[BIMA AI] Step 5 Finished", data5);
                if (data5.data) this.session.character_category = data5.data.category;

                // Step 6: Final
                console.log("[BIMA AI] Step 6: Insights Starting...");
                this.activeStep = 6;
                const res6 = await this.apiFetch('/chat/analyse/step-6', {
                    method: 'POST',
                    body: JSON.stringify({ session_id: this.sessionId })
                });
                const data6 = await res6.json();
                if (!data6.success) throw new Error(data6.message || 'Gagal di Langkah 6');
                console.log("[BIMA AI] Step 6 Finished", data6);

                if (data6.data) {
                    if (data6.data.intonation) this.session.intonation_analysis = data6.data.intonation.intonation;
                    if (data6.data.insights) {
                        this.session.summary_domain = data6.data.insights.summary;
                        this.session.aim_target = data6.data.insights.aim;
                        this.session.suggestions = data6.data.insights.suggestion;
                    }
                }

                this.messages = this.messages.filter(m => m.id !== typingId);
                this.activeStep = null;
                if (data6.full_message) {
                    console.log("[BIMA AI] Workflow Complete. Streaming result...");
                    this.startStreaming(Date.now(), data6.full_message, 'final_result');
                } else {
                    this.isAnalysing = false;
                }
                
            } catch (err) {
                this.messages = this.messages.filter(m => m.id !== typingId);
                this.isAnalysing = false;
                this.activeStep = null;
                console.error("[BIMA AI] Workflow Error:", err);
                this.messages.push({ id: Date.now(), role: 'assistant', content: 'Kesalahan Sistem: ' + err.message, created_at: new Date() });
            }
            this.$nextTick(() => this.scrollToBottom());
        },

        startStreaming(msgId, fullText, type) {
            let i = 0; 
            let msg = { id: msgId, role: 'assistant', content: '', created_at: new Date(), isTyping: true };
            this.messages.push(msg);
            let interval = setInterval(() => {
                let p = this.messages.find(m => m.id === msgId);
                if (!p) { clearInterval(interval); return; }
                if (i < fullText.length) {
                    p.content += fullText.substring(i, i + 3); i += 3; this.$nextTick(() => this.scrollToBottom());
                } else {
                    p.isTyping = false; this.isAnalysing = false; clearInterval(interval);
                }
            }, 15);
        },

        scrollToBottom() {
            setTimeout(() => { const el = document.getElementById('messages-wrap'); if (el) el.scrollTop = el.scrollHeight; }, 50);
        },

        formatTime(dt) {
            const d = dt instanceof Date ? dt : new Date(dt);
            return isNaN(d) ? '' : d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        },

        apiFetch(url, options = {}) {
            return fetch(url, {
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.BIMA.csrfToken, 'Accept': 'application/json' },
                ...options
            });
        },

        formatMarkdown(text) {
            if (!text) return '';
            return text;
        }
    }));
});
</script>
