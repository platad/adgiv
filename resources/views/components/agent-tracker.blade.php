{{--
    Component: agent-tracker
    Real-time debate panel with 4 agent cards driven by Alpine.js.
    All icons use inline SVG (Lucide paths) via x-html.
--}}

<div class="tracker-panel" id="agent-tracker">
    <div class="tracker-header">
        <span class="tracker-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="display:inline;vertical-align:middle;margin-right:5px"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
            Debate Panel
        </span>
        <span class="tracker-subtitle" x-text="trackerSubtitle">Menunggu input...</span>
    </div>

    <div class="agents-grid">
        <template x-for="agent in agents" :key="agent.name">
            <div class="agent-card" :class="'agent-card--' + agent.status">
                <div class="agent-card-top">
                    <span class="agent-icon-wrap" x-html="agentIcon(agent.name)"></span>
                    <div class="agent-info">
                        <span class="agent-name" x-text="agent.display"></span>
                        <span class="agent-status-text" x-text="agent.statusText"></span>
                    </div>
                    <div class="agent-indicator">
                        <template x-if="agent.status === 'thinking'">
                            <div class="spinner"></div>
                        </template>
                        <template x-if="agent.status === 'done'">
                            <svg class="icon-done" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        </template>
                        <template x-if="agent.status === 'failed'">
                            <svg class="icon-failed" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </template>
                        <template x-if="agent.status === 'idle'">
                            <svg class="icon-idle" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/></svg>
                        </template>
                    </div>
                </div>

                <template x-if="agent.status === 'done' && agent.verdict">
                    <div class="agent-verdict">
                        <span x-text="'Verdik: ' + agent.verdict"></span>
                        <span class="confidence-bar">
                            <span class="confidence-fill" :style="'width:' + (agent.confidence * 100) + '%'"></span>
                        </span>
                        <span class="confidence-pct" x-text="Math.round(agent.confidence * 100) + '%'"></span>
                    </div>
                </template>

                <template x-if="agent.message">
                    <p class="agent-note" x-text="agent.message"></p>
                </template>
            </div>
        </template>
    </div>
</div>

<style>
.tracker-panel {
    background: var(--bg-card);
    border-bottom: 1px solid var(--border);
    padding: 0.875rem 1.25rem;
    flex-shrink: 0;
}
.tracker-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 0.75rem;
}
.tracker-title { font-size: 0.78rem; font-weight: 700; color: var(--text-primary); letter-spacing: 0.04em; text-transform: uppercase; }
.tracker-subtitle { font-size: 0.72rem; color: var(--text-muted); }

.agents-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 0.6rem; }

.agent-card {
    background: rgba(255,255,255,0.03); border: 1px solid var(--border);
    border-radius: 12px; padding: 0.65rem 0.75rem;
    transition: all 0.3s;
}
.agent-card--thinking {
    border-color: rgba(245,158,11,0.4);
    background: rgba(245,158,11,0.05);
    box-shadow: 0 0 16px rgba(245,158,11,0.1);
}
.agent-card--done {
    border-color: rgba(16,185,129,0.4);
    background: rgba(16,185,129,0.05);
}
.agent-card--failed {
    border-color: rgba(239,68,68,0.3);
    background: rgba(239,68,68,0.05);
}
.agent-card-top { display: flex; align-items: flex-start; gap: 0.5rem; }
.agent-icon-wrap {
    flex-shrink: 0; margin-top: 0.1rem;
    color: var(--text-secondary);
    display: flex; align-items: center;
}
.agent-icon-wrap svg { width: 16px; height: 16px; }
.agent-info { flex: 1; min-width: 0; }
.agent-name { display: block; font-size: 0.75rem; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.agent-status-text { font-size: 0.67rem; color: var(--text-muted); }

.spinner {
    width: 16px; height: 16px;
    border: 2px solid rgba(245,158,11,0.3);
    border-top-color: #f59e0b;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    flex-shrink: 0;
}
@keyframes spin { to { transform: rotate(360deg); } }

.icon-done  { color: #34d399; }
.icon-failed{ color: #f87171; }
.icon-idle  { color: var(--text-muted); }

.agent-verdict {
    display: flex; align-items: center; gap: 0.4rem;
    margin-top: 0.5rem; font-size: 0.68rem; color: #34d399; font-weight: 600;
}
.confidence-bar {
    flex: 1; height: 4px; background: rgba(255,255,255,0.08); border-radius: 2px; overflow: hidden;
}
.confidence-fill { display: block; height: 100%; background: linear-gradient(90deg, #7c3aed, #34d399); border-radius: 2px; transition: width 0.5s; }
.confidence-pct { color: var(--text-muted); font-size: 0.65rem; }

.agent-note {
    font-size: 0.67rem; color: var(--text-muted);
    margin-top: 0.4rem; line-height: 1.4;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
</style>

<script>
/* Agent icon helper — called from chatApp() via agentIcon(name) */
window._bimaAgentIcons = {
    kosakata: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>`,
    otoritas: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.43l4.781-3.317a1 1 0 0 1 1.545 1.062l-2.813 12.544A2 2 0 0 1 18.463 21H5.537a2 2 0 0 1-1.956-1.412L.768 7.045a1 1 0 0 1 1.545-1.062l4.781 3.317a1 1 0 0 0 1.516-.43z"/></svg>`,
    gaya_bahasa: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>`,
    judge: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21H17"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/></svg>`,
};
</script>
