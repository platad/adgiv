<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Chat' }} – BIMA AI</title>
    <meta name="description" content="BIMA – Analisis suara Multi-Agent AI untuk klasifikasi Mahasiswa vs Dosen.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- CDN: Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- CDN: Pusher JS (Reverb) --}}
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>

    {{-- CDN: Lucide Icons --}}
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>

    {{-- CDN: Tailwind CSS v4 --}}
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --font-sans: 'Inter', sans-serif;
            --color-openai-bg: #f9f9f9;
            --color-openai-panel: #ffffff;
            --color-openai-text: #0d0d0d;
            --color-openai-muted: #6b6b6b;
            --color-openai-border: #e5e5e5;
            --color-openai-hover: #ececec;
        }
        body { background-color: var(--color-openai-bg); color: var(--color-openai-text); }
        * { scrollbar-width: thin; scrollbar-color: var(--color-openai-border) transparent; }
    </style>

    {{ $styles ?? '' }}
    <script>
        window.BIMA = {
            csrfToken: '{{ csrf_token() }}',
            sessionId: {{ $activeSessionId ?? 'null' }},
            reverbKey:    '{{ config('broadcasting.connections.reverb.key', 'bima-key-local') }}',
            reverbHost:   '{{ config('broadcasting.connections.reverb.options.host', 'localhost') }}',
            reverbPort:   {{ config('broadcasting.connections.reverb.options.port', 8080) }},
            reverbScheme: '{{ config('broadcasting.connections.reverb.options.scheme', 'http') }}',
        };
    </script>
</head>
<body class="h-screen w-screen overflow-hidden flex font-sans antialiased" x-data="{ isSidebarOpen: true }">

    {{-- Sidebar --}}
    <aside 
        class="flex-shrink-0 flex flex-col transition-all duration-300 ease-in-out relative z-10"
        :class="isSidebarOpen ? 'w-64' : 'w-0 overflow-hidden'"
    >
        <div class="p-4 flex items-center justify-between min-w-[16rem]">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-black rounded-lg flex items-center justify-center text-white">
                    <i data-lucide="brain" class="w-5 h-5"></i>
                </div>
                <div>
                    <h2 class="text-sm font-semibold leading-tight">BIMA AI</h2>
                    <span class="text-xs text-openai-muted">Multi-Agent Debate</span>
                </div>
            </div>
            <!-- Small screen close button (optional) -->
        </div>

        <div class="flex-1 overflow-y-auto px-3 min-w-[16rem] pb-4">
            {{ $sidebar ?? '' }}
        </div>
    </aside>

    {{-- Main Content Wrapper --}}
    <div class="flex-1 flex flex-col min-w-0 p-2 pl-0 relative transition-all duration-300">
        
        {{-- Topbar inside Main Content --}}
        <header class="h-12 flex items-center px-4 shrink-0 w-full gap-4">
            <div class="flex items-center gap-2 flex-1 min-w-0">
                <button 
                    @click="isSidebarOpen = !isSidebarOpen"
                    class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition shrink-0"
                    title="Toggle Sidebar"
                >
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><line x1="9" x2="9" y1="3" y2="21"/></svg>
                </button>
                <a href="/" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition shrink-0" title="Sesi Baru" x-show="!isSidebarOpen">
                    <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                </a>
                <div class="flex items-center gap-2 flex-1 min-w-0">
                    {{ $topbar ?? '' }}
                </div>
            </div>
            
            {{-- User Dropdown --}}
            <div class="relative shrink-0" x-data="{ userMenuOpen: false }">
                <button @click="userMenuOpen = !userMenuOpen" @click.away="userMenuOpen = false" class="flex items-center gap-2 p-1.5 rounded-xl hover:bg-gray-100 transition focus:outline-none">
                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-700 border border-gray-300 shrink-0">
                        {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                    </div>
                </button>

                <div x-show="userMenuOpen" x-transition.opacity.duration.200ms class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-lg z-50 overflow-hidden" style="display:none;">
                    <div class="p-3 border-b border-gray-100">
                        <div class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name ?? 'User' }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? 'user@bima.ai' }}</div>
                        <div class="mt-1 text-[0.65rem] font-bold text-purple-600 uppercase tracking-wider">Peneliti BIMA</div>
                    </div>
                    <div class="p-1">
                        <form method="POST" action="/logout" class="m-0">
                            @csrf
                            <button type="submit" class="w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition flex items-center gap-2">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        {{-- The rounded white panel --}}
        <main class="flex-1 bg-openai-panel rounded-2xl shadow-sm border border-openai-border overflow-hidden flex flex-col relative">
            {{ $slot }}
        </main>
    </div>

<script>
    lucide.createIcons();

    if (window.BIMA.reverbKey) {
        Pusher.logToConsole = false;
        const pusher = new Pusher(window.BIMA.reverbKey, {
            wsHost:            window.BIMA.reverbHost,
            wsPort:            window.BIMA.reverbPort,
            wssPort:           window.BIMA.reverbPort,
            forceTLS:          window.BIMA.reverbScheme === 'https',
            disableStats:      true,
            enabledTransports: ['ws', 'wss'],
            cluster:           'mt1',
        });
        window.__pusher = pusher;
        pusher.connection.bind('connected',    () => console.log('[BIMA] Reverb connected ✓'));
    }

    // Global session delete
    function deleteSession(id, isActive) {
        window.dispatchEvent(new CustomEvent('open-delete-modal', { detail: { id, isActive } }));
    }
</script>

{{-- Delete Session Modal --}}
<div x-data="{ open: false, sessionId: null, isActive: false, deleting: false }"
     @open-delete-modal.window="open = true; sessionId = $event.detail.id; isActive = $event.detail.isActive;">
    <div class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm" style="display:none;" x-show="open" x-transition.opacity></div>
    <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 pointer-events-none" style="display:none;" x-show="open">
        <div class="bg-white rounded-2xl p-6 shadow-xl w-full max-w-sm pointer-events-auto" x-show="open" x-transition>
            <div class="flex items-center gap-3 text-red-600 mb-2">
                <svg class="w-6 h-6 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                <h3 class="text-lg font-bold text-gray-900">Hapus Sesi?</h3>
            </div>
            <p class="text-sm text-gray-600 mb-6">Sesi ini dan semua riwayat chat di dalamnya akan dihapus secara permanen. Anda yakin?</p>
            <div class="flex justify-end gap-2">
                <button class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition" @click="open = false" :disabled="deleting">Batal</button>
                <button class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition disabled:opacity-50 flex items-center gap-2"
                        :disabled="deleting"
                        @click="
                            deleting = true;
                            fetch(`/chat/session/${sessionId}`, {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': window.BIMA.csrfToken }
                            }).then(() => {
                                if (isActive) window.location.href = '/';
                                else window.location.reload();
                            }).catch(() => { deleting = false; open = false; });
                        ">
                    <span x-show="deleting" class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    Hapus
                </button>
            </div>
        </div>
    </div>
</div>

{{ $scripts ?? '' }}
</body>
</html>
