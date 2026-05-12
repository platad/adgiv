{{-- session state moved to scripts.blade.php --}}

async init() {
    // Fetch initial session data
    try {
        const res = await this.apiFetch(`/chat/session/${this.sessionId}/data`);
        const data = await res.json();
        if (data.success) {
            this.session = { ...this.session, ...data.session };
        }
    } catch (e) { console.error("Init session fetch failed", e); }
    
    this.loadMessages();
    this.scrollToBottom();
},

async createNewSession() {
    try {
        const res = await this.apiFetch('/chat/session', { method: 'POST', body: JSON.stringify({}) });
        const data = await res.json();
        if (data.success) window.location.href = `/chat?session=${data.session.id}`;
    } catch (_) {}
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
    return text; // Markdown parsing handled by controller/view
},
