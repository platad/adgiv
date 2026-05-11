{{-- resources/views/components/chat/scripts/methods-utils.blade.php --}}
async createNewSession() {
    try {
        const res = await this.apiFetch('/chat/session', { method: 'POST', body: JSON.stringify({}) });
        const data = await res.json();
        if (data.success) window.location.href = `/chat?session=${data.session.id}`;
    } catch (_) {}
},
scrollToBottom() {
    setTimeout(() => { const el = this.$refs.messagesWrap; if (el) el.scrollTop = el.scrollHeight; }, 50);
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
resetAgents() {
    this.agents.forEach(a => { a.status = 'idle'; });
},
agentIcon(name) {
    return window._bimaAgentIcons?.[name] || window._bimaAgentIcons?.kosakata || '';
},
