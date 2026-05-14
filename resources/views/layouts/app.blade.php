<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Chat' }} – Supervisory AI</title>
    <meta name="description" content="BIMA – Analisis suara Multi-Agent AI untuk klasifikasi Mahasiswa vs Dosen.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- CDN: Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- CDN: Pusher JS (for Laravel Echo) --}}
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --bg-base:    #080b18;
            --bg-surface: #0f1322;
            --bg-card:    #151929;
            --bg-hover:   #1c2236;
            --border:     rgba(255,255,255,0.07);
            --accent:     #7c3aed;
            --accent-glow:rgba(124,58,237,0.35);
            --text-primary:   #f1f5f9;
            --text-secondary: #94a3b8;
            --text-muted:     #475569;
            --success:  #10b981;
            --warning:  #f59e0b;
            --danger:   #ef4444;
            --sidebar-w: 280px;
        }
        html, body { height: 100%; font-family: 'Inter', sans-serif; background: var(--bg-base); color: var(--text-primary); overflow: hidden; }

        /* ── App Shell ────────────────────────────── */
        .app-shell { display: flex; height: 100vh; width: 100vw; }

        /* ── Sidebar ──────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w); flex-shrink: 0;
            background: var(--bg-surface);
            border-right: 1px solid var(--border);
            display: flex; flex-direction: column;
            overflow: hidden;
        }
        .sidebar-header {
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 0.75rem;
        }
        .sidebar-logo {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, #7c3aed, #06b6d4);
            border-radius: 12px; display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem; flex-shrink: 0;
            box-shadow: 0 0 20px var(--accent-glow);
        }
        .sidebar-brand h2 { font-size: 1rem; font-weight: 700; color: var(--text-primary); }
        .sidebar-brand span { font-size: 0.72rem; color: var(--text-muted); }
        .sidebar-body { flex: 1; overflow-y: auto; padding: 1rem 0.75rem; }
        .sidebar-body::-webkit-scrollbar { width: 4px; }
        .sidebar-body::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }
        .sidebar-footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid var(--border);
        }
        .user-pill {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.6rem 0.75rem;
            border-radius: 12px;
            background: var(--bg-card);
        }
        .user-avatar {
            width: 34px; height: 34px; border-radius: 50%;
            background: linear-gradient(135deg, #7c3aed, #4f87ff);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem; font-weight: 600; color: #fff; flex-shrink: 0;
        }
        .user-info { flex: 1; min-width: 0; }
        .user-info .name { font-size: 0.8rem; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-info .role { font-size: 0.7rem; color: var(--text-muted); }
        .logout-btn {
            background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 0.25rem;
            border-radius: 6px; transition: color 0.2s, background 0.2s;
        }
        .logout-btn:hover { color: var(--danger); background: rgba(239,68,68,0.1); }

        /* ── Main content ─────────────────────────── */
        .main-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

        /* ── Topbar ───────────────────────────────── */
        .topbar {
            height: 60px; flex-shrink: 0;
            padding: 0 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 1rem;
            background: var(--bg-surface);
        }

        /* ── Page Content Slot ────────────────────── */
        .page-content { flex: 1; overflow: hidden; display: flex; }

        /* ── Shared Utilities ─────────────────────── */
        .btn {
            display: inline-flex; align-items: center; gap: 0.4rem;
            padding: 0.5rem 1rem; border-radius: 10px; font-size: 0.82rem;
            font-weight: 500; cursor: pointer; border: none; outline: none;
            transition: all 0.2s; white-space: nowrap;
        }
        .btn-accent {
            background: linear-gradient(135deg, var(--accent), #4f87ff);
            color: #fff;
            box-shadow: 0 2px 12px var(--accent-glow);
        }
        .btn-accent:hover { opacity: 0.88; transform: translateY(-1px); }
        .btn-ghost {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            color: var(--text-secondary);
        }
        .btn-ghost:hover { background: var(--bg-hover); color: var(--text-primary); }
        .badge {
            display: inline-flex; align-items: center; justify-content: center;
            padding: 0.15rem 0.5rem; border-radius: 999px;
            font-size: 0.7rem; font-weight: 600;
        }
        .badge-purple { background: rgba(124,58,237,0.2); color: #a78bfa; }
        .badge-green  { background: rgba(16,185,129,0.15); color: #34d399; }
        .badge-yellow { background: rgba(245,158,11,0.15); color: #fbbf24; }
        .badge-red    { background: rgba(239,68,68,0.15); color: #f87171; }
        .badge-gray   { background: rgba(255,255,255,0.05); color: var(--text-muted); }

        /* ── Scrollbars ───────────────────────────── */
        * { scrollbar-width: thin; scrollbar-color: var(--border) transparent; }
    </style>

    {{ $styles ?? '' }}
</head>
<body>
<div class="app-shell">

    {{-- ── Sidebar (injected via named slot) ── --}}
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo">🧠</div>
            <div class="sidebar-brand">
                <h2>Supervisory AI</h2>
                <span>Multi-Agent Debate</span>
            </div>
        </div>
        <div class="sidebar-body">
            {{ $sidebar ?? '' }}
        </div>
        <div class="sidebar-footer">
            <div class="user-pill">
                <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</div>
                <div class="user-info">
                    <div class="name">{{ auth()->user()->name ?? 'User' }}</div>
                    <div class="role">Peneliti BIMA</div>
                </div>
                <form method="POST" action="/logout" style="margin:0">
                    @csrf
                    <button type="submit" class="logout-btn" title="Logout">
                        <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ── Main Area ── --}}
    <div class="main-content">
        {{-- Topbar --}}
        <header class="topbar">
            {{ $topbar ?? '' }}
        </header>

        {{-- Page slot --}}
        <div class="page-content">
            {{ $slot }}
        </div>
    </div>

</div>

{{-- ── Global Scripts ── --}}
<script>
    // Global config for Alpine + Reverb
    window.BIMA = {
        csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        sessionId: {{ $activeSessionId ?? 'null' }},
        reverbKey:    '{{ config('broadcasting.connections.reverb.key', 'bima-key-local') }}',
        reverbHost:   '{{ config('broadcasting.connections.reverb.options.host', 'localhost') }}',
        reverbPort:   {{ config('broadcasting.connections.reverb.options.port', 8080) }},
        reverbScheme: '{{ config('broadcasting.connections.reverb.options.scheme', 'http') }}',
    };

    // Bootstrap Pusher JS pointed at Laravel Reverb endpoint (no Pusher account needed)
    if (window.BIMA.reverbKey) {
        Pusher.logToConsole = false;
        const pusher = new Pusher(window.BIMA.reverbKey, {
            wsHost: window.BIMA.reverbHost,
            wsPort: window.BIMA.reverbPort,
            wssPort: window.BIMA.reverbPort,
            forceTLS: window.BIMA.reverbScheme === 'https',
            disableStats: true,
            enabledTransports: ['ws', 'wss'],
            cluster: 'mt1',
        });
        window.__pusher = pusher;
        pusher.connection.bind('connected',    () => console.log('[BIMA] Reverb connected ✓'));
        pusher.connection.bind('disconnected', () => console.warn('[BIMA] Reverb disconnected'));
        pusher.connection.bind('error',        (e) => console.error('[BIMA] Reverb error', e));
    }
</script>

{{ $scripts ?? '' }}
</body>
</html>
