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
            --color-bima-red: #cc0000;
            --color-bima-red-dark: #990000;
            --color-openai-bg: #ffffff;
            --color-openai-panel: #ffffff;
            --color-openai-text: #1a1a1a;
            --color-openai-muted: #666666;
            --color-openai-border: #f0f0f0;
            --color-openai-hover: #f9f9f9;
        }
        body { background-color: var(--color-openai-bg); color: var(--color-openai-text); }
        * { scrollbar-width: thin; scrollbar-color: #cc0000 transparent; }
    </style>

    {{ $styles ?? '' }}
    <script>
        window.BIMA = {
            csrfToken: '{{ csrf_token() }}',
            sessionId: '{{ $activeSessionId ?? '' }}',
            reverbKey:    '{{ config('broadcasting.connections.reverb.key', 'bima-key-local') }}',
            reverbHost:   '{{ config('broadcasting.connections.reverb.options.host', 'localhost') }}',
            reverbPort:   {{ config('broadcasting.connections.reverb.options.port', 8080) }},
            reverbScheme: '{{ config('broadcasting.connections.reverb.options.scheme', 'http') }}',
        };
    </script>
</head>
<body class="min-h-screen bg-gray-50 flex font-sans antialiased">

    {{-- Mobile Header --}}
    <div class="lg:hidden fixed top-0 left-0 right-0 h-16 bg-gray-900 z-[60] flex items-center justify-between px-6 border-b border-white/10">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 bg-bima-red rounded-lg flex items-center justify-center text-white">
                <i data-lucide="brain" class="w-5 h-5"></i>
            </div>
            <span class="text-white font-black tracking-tighter text-lg">BIMA <span class="text-bima-red">AI</span></span>
        </div>
        <form method="POST" action="/logout" class="m-0">
            @csrf
            <button type="submit" class="text-white/60 hover:text-white">
                <i data-lucide="log-out" class="w-5 h-5"></i>
            </button>
        </form>
    </div>

    {{-- Sidebar (Desktop) --}}
    <aside class="hidden lg:flex w-24 bg-gray-900 flex-col items-center py-10 shrink-0 border-r border-white/5 z-50 fixed inset-y-0 left-0">
        <div class="flex flex-col items-center gap-12 h-full">
            {{-- Logo --}}
            <div class="group cursor-pointer">
                <div class="w-14 h-14 bg-bima-red rounded-[1.5rem] flex items-center justify-center text-white shadow-[0_10px_30px_rgba(204,0,0,0.4)] group-hover:scale-110 transition-transform duration-500">
                    <i data-lucide="brain" class="w-8 h-8"></i>
                </div>
            </div>

            {{-- Nav --}}
            <nav class="flex flex-col gap-8">
                <a href="#" class="w-12 h-12 rounded-2xl bg-white/10 text-white flex items-center justify-center group/nav transition-all">
                    <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
                </a>
                <a href="#" class="w-12 h-12 rounded-2xl hover:bg-white/5 text-white/40 hover:text-white flex items-center justify-center group/nav transition-all">
                    <i data-lucide="message-square" class="w-5 h-5"></i>
                </a>
                <a href="#" class="w-12 h-12 rounded-2xl hover:bg-white/5 text-white/40 hover:text-white flex items-center justify-center group/nav transition-all">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                </a>
            </nav>

            {{-- Bottom Actions --}}
            <div class="mt-auto flex flex-col gap-6">
                {{-- Separator --}}
                <div class="w-8 h-px bg-white/20"></div>

                {{-- Logout Button --}}
                <form method="POST" action="/logout" class="m-0">
                    @csrf
                    <button type="submit" class="w-12 h-12 rounded-2xl bg-white/5 hover:bg-red-500/20 hover:text-white text-white/60 transition flex items-center justify-center group/logout" title="Keluar">
                        <i data-lucide="log-out" class="w-5 h-5 group-hover/logout:scale-110 transition-transform"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Action Buttons (Floating) --}}
    <div class="fixed right-6 bottom-24 lg:bottom-auto lg:left-28 lg:top-10 flex flex-col gap-4 z-40" x-data="{}">
        {{-- New Session --}}
        <button onclick="createNewSessionGlobal()" 
                class="w-14 h-14 bg-white rounded-2xl shadow-xl flex items-center justify-center text-bima-red hover:bg-bima-red hover:text-white transition-all group relative border border-red-50 cursor-pointer"
                title="Sesi Baru">
            <i data-lucide="plus" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
            <div class="hidden lg:block absolute left-full ml-4 px-3 py-1.5 bg-gray-900 text-white text-[0.6rem] font-bold rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap uppercase tracking-widest z-50">Sesi Baru</div>
        </button>

        {{-- History --}}
        <button @click="$dispatch('open-history')" 
                class="w-14 h-14 bg-white rounded-2xl shadow-xl flex items-center justify-center text-gray-400 hover:text-bima-red transition-all group relative border border-gray-50 cursor-pointer"
                title="Riwayat Sesi">
            <i data-lucide="history" class="w-6 h-6 group-hover:scale-110 transition-transform"></i>
            <div class="hidden lg:block absolute left-full ml-4 px-3 py-1.5 bg-gray-900 text-white text-[0.6rem] font-bold rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap uppercase tracking-widest z-50">Riwayat</div>
        </button>
    </div>

    {{-- Main Content Wrapper --}}
    <div class="flex-1 flex flex-col w-full px-4 pt-20 pb-24 lg:pl-52 lg:pr-8 lg:py-10 relative">
        {{-- The main panel --}}
        <main class="flex-1 w-full bg-white shadow-2xl border border-gray-100 rounded-[2rem] lg:rounded-[3rem] flex flex-col relative overflow-hidden">
            {{ $slot }}
        </main>
    </div>

<script>
    lucide.createIcons();

    function createNewSessionGlobal() {
        fetch('/chat/session', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.BIMA.csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({})
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) window.location.href = `/chat?session=${data.session.id}`;
        })
        .catch(err => console.error("Failed to create session", err));
    }

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
        <div class="bg-white rounded-[2.5rem] p-8 shadow-2xl w-full max-w-md pointer-events-auto border border-gray-100" x-show="open" x-transition>
            <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center text-red-600 mb-6 mx-auto">
                <i data-lucide="alert-triangle" class="w-8 h-8"></i>
            </div>
            <h3 class="text-xl font-black text-gray-900 text-center mb-2">Hapus Sesi?</h3>
            <p class="text-sm text-gray-500 text-center mb-8 leading-relaxed">Sesi ini dan semua riwayat analisis di dalamnya akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.</p>
            
            <div class="grid grid-cols-2 gap-4">
                <button class="h-14 rounded-2xl text-sm font-bold text-gray-400 hover:bg-gray-50 transition" @click="open = false" :disabled="deleting">Batal</button>
                <button class="h-14 rounded-2xl text-sm font-bold text-white bg-red-600 hover:bg-red-700 transition disabled:opacity-50 flex items-center justify-center gap-2"
                        :disabled="deleting"
                        @click="
                            deleting = true;
                            fetch(`/chat/session/${sessionId}`, {
                                method: 'DELETE',
                                headers: { 
                                    'X-CSRF-TOKEN': window.BIMA.csrfToken,
                                    'Accept': 'application/json'
                                }
                            }).then(async res => {
                                if (!res.ok) {
                                    const data = await res.json();
                                    throw new Error(data.message || 'Gagal menghapus sesi.');
                                }
                                if (isActive) window.location.href = '/';
                                else window.location.reload();
                            }).catch(err => { 
                                alert(err.message);
                                deleting = false; 
                                open = false; 
                            });
                        ">
                    <span x-show="deleting" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

{{-- History Modal --}}
<div x-data="{ 
        open: false, 
        sessions: [], 
        loading: false,
        activeSessionId: '{{ $activeSessionId ?? '' }}',
        async loadHistory() {
            this.loading = true;
            try {
                const res = await fetch('/chat/sessions', {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                if (data.success) {
                    this.sessions = data.sessions;
                    this.$nextTick(() => {
                        if (window.lucide) window.lucide.createIcons();
                    });
                }
            } catch (e) { console.error(e); }
            this.loading = false;
        }
     }"
     @open-history.window="open = true; loadHistory();"
     class="relative z-[70]">
    
    <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-md" style="display:none;" x-show="open" x-transition.opacity @click="open = false"></div>
    
    <div class="fixed inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl p-0 flex flex-col" style="display:none;" x-show="open" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full">
        
        {{-- Header --}}
        <div class="p-8 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <div>
                <h2 class="text-xl font-black text-gray-900 tracking-tight">Riwayat Analisis</h2>
                <p class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest mt-1">Daftar Sesi Sebelumnya</p>
            </div>
            <button @click="open = false" class="w-10 h-10 rounded-xl hover:bg-white hover:shadow-sm transition flex items-center justify-center text-gray-400">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        {{-- Session List --}}
        <div class="flex-1 overflow-y-auto p-6 space-y-4">
            <template x-if="loading">
                <div class="flex flex-col items-center justify-center py-20 gap-4">
                    <div class="w-8 h-8 border-3 border-gray-100 border-t-bima-red rounded-full animate-spin"></div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Memuat Riwayat...</span>
                </div>
            </template>

            <template x-if="!loading && sessions.length === 0">
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center text-gray-200 mb-6">
                        <i data-lucide="folder-open" class="w-10 h-10"></i>
                    </div>
                    <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Belum ada riwayat sesi.</p>
                </div>
            </template>

            <template x-for="s in sessions" :key="s.id">
                <div class="group relative bg-white border border-gray-100 hover:border-red-100 p-5 rounded-[2rem] transition-all hover:shadow-[0_20px_50px_rgba(204,0,0,0.05)] flex items-center gap-5 cursor-pointer overflow-hidden"
                     :class="s.id === activeSessionId ? 'border-red-200 bg-red-50/10 shadow-sm ring-1 ring-red-100' : ''"
                     @click="window.location.href = `/chat?session=${s.id}`">
                    
                    {{-- Decorative Background --}}
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-50/30 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity"></div>

                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 transition-all duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-sm relative overflow-hidden"
                         :class="s.id === activeSessionId ? 'bg-bima-red text-white shadow-xl shadow-red-200' : 'bg-gray-50 text-gray-400'">
                        
                        <svg class="w-7 h-7 relative z-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            <path d="M8 9h8" opacity="0.5"/>
                            <path d="M8 13h5" opacity="0.5"/>
                        </svg>

                        <div x-show="s.id === activeSessionId" class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"></div>
                    </div>

                    <div class="flex-1 min-w-0 relative z-10">
                        <h4 class="text-sm font-black text-gray-800 truncate group-hover:text-bima-red transition-colors duration-300 uppercase tracking-tight" x-text="s.title"></h4>
                        <div class="flex items-center gap-2 mt-1.5">
                            <div class="w-1.5 h-1.5 rounded-full bg-green-500" :class="s.id === activeSessionId ? 'animate-pulse' : 'opacity-40'"></div>
                            <p class="text-[0.6rem] font-bold text-gray-400 uppercase tracking-widest" x-text="new Date(s.created_at).toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'})"></p>
                        </div>
                    </div>

                    {{-- Delete Action --}}
                    <button class="w-12 h-12 rounded-2xl text-gray-300 hover:text-white hover:bg-red-600 transition-all opacity-0 group-hover:opacity-100 flex items-center justify-center shadow-lg relative z-20 translate-x-4 group-hover:translate-x-0 duration-300"
                            @click.stop="deleteSession(s.id, s.id === activeSessionId)">
                        <i data-lucide="trash-2" class="w-5 h-5"></i>
                    </button>
                </div>
            </template>
        </div>

        {{-- Footer Action --}}
        <div class="p-6 border-t border-gray-100">
            <button onclick="createNewSessionGlobal()" class="w-full h-14 rounded-2xl bg-gray-900 text-white font-black text-sm flex items-center justify-center gap-3 hover:bg-black transition shadow-xl">
                <i data-lucide="plus" class="w-4 h-4"></i>
                MULAI SESI BARU
            </button>
        </div>
    </div>
</div>

{{ $scripts ?? '' }}
</body>
</html>
