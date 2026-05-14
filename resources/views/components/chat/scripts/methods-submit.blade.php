{{-- resources/views/components/chat/scripts/methods-submit.blade.php --}}
async handleMultiStepAnalysis(text) {
    console.log("[Supervisory AI] Starting Multi-Step Analysis", { text });
    if (this.isAnalysing) return;
    
    this.isAnalysing = true;
    const typingId = 'typing-' + Date.now();
    this.messages.push({ id: typingId, role: 'assistant', content: '', created_at: new Date(), isTyping: true });
    
    try {
        // Step 1 is already done by voice-input (saving raw transcription)
        // We start from Step 2: Refine
        
        // --- STEP 2: Merapikan Hasil ---
        this.activeStep = 2;
        const res2 = await this.apiFetch('/chat/analyse/step-2', {
            method: 'POST',
            body: JSON.stringify({ session_id: this.sessionId })
        });
        const data2 = await res2.json();
        if (!data2.success) throw new Error(data2.message || 'Gagal di Langkah 2');
        if (data2.data) this.session.refined_transcription = data2.data.refined_text;

        // --- STEP 3: Pencocokan Suara ---
        this.activeStep = 3;
        const res3 = await this.apiFetch('/chat/analyse/step-3', {
            method: 'POST',
            body: JSON.stringify({ session_id: this.sessionId })
        });
        const data3 = await res3.json();
        if (!data3.success) throw new Error(data3.message || 'Gagal di Langkah 3');
        if (data3.data) this.session.matched_transcription = data3.data.matched_text;

        // --- STEP 4: Advice Giving ---
        this.activeStep = 4;
        const res4 = await this.apiFetch('/chat/analyse/step-4', {
            method: 'POST',
            body: JSON.stringify({ session_id: this.sessionId })
        });
        const data4 = await res4.json();
        if (!data4.success) throw new Error(data4.message || 'Gagal di Langkah 4');
        if (data4.data) this.session.advice_category = data4.data.category;

        // --- STEP 5: Karakter Relasi ---
        this.activeStep = 5;
        const res5 = await this.apiFetch('/chat/analyse/step-5', {
            method: 'POST',
            body: JSON.stringify({ session_id: this.sessionId })
        });
        const data5 = await res5.json();
        if (!data5.success) throw new Error(data5.message || 'Gagal di Langkah 5');
        if (data5.data) this.session.character_category = data5.data.category;

        // --- STEP 6: Intonasi & Insights ---
        this.activeStep = 6;
        const res6 = await this.apiFetch('/chat/analyse/step-6', {
            method: 'POST',
            body: JSON.stringify({ session_id: this.sessionId })
        });
        const data6 = await res6.json();
        if (!data6.success) throw new Error(data6.message || 'Gagal di Langkah 6');

        if (data6.data) {
            if (data6.data.intonation) this.session.intonation_analysis = data6.data.intonation.intonation;
            if (data6.data.insights) {
                this.session.summary_domain = data6.data.insights.summary;
                this.session.aim_target = data6.data.insights.aim;
                this.session.suggestions = data6.data.insights.suggestion;
            }
        }

        // Final UI updates
        this.messages = this.messages.filter(m => m.id !== typingId);
        this.activeStep = null;
        if (data6.full_message) {
            this.startStreaming(Date.now(), data6.full_message, 'final_result');
        }
        
    } catch (err) {
        this.messages = this.messages.filter(m => m.id !== typingId);
        this.isAnalysing = false;
        this.activeStep = null;
        console.error("[Supervisory AI] Workflow Error:", err);
        this.messages.push({ id: Date.now(), role: 'assistant', content: 'Kesalahan Sistem: ' + err.message, created_at: new Date() });
    }
    this.$nextTick(() => this.scrollToBottom());
},

startStreaming(msgId, fullText, type) {
    let i = 0; let msg = { id: msgId, role: 'assistant', content: '', created_at: new Date(), isTyping: true };
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

// Deprecated handleSubmit, kept for backward compat if needed
async handleSubmit(text, files) {
    // This is now handled by start-multi-step-analysis
}
