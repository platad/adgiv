@props([
    'title' => null,
    'welcomeAudio' => null,
    'activeSessionId' => null,
])
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
        @if(app()->getLocale() === 'zh')
            {{ $title ?? '控制面板' }}
        @elseif(app()->getLocale() === 'en')
            {{ $title ?? 'Dashboard' }}
        @else
            {{ $title ?? 'Dashboard' }}
        @endif
        – Supervisory AI
    </title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <meta name="description"
        content="Supervisory AI – Analisis suara Multi-Agent AI untuk klasifikasi Mahasiswa vs Dosen.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- CDN: Alpine.js Plugins & Core --}}
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
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

        body {
            background-color: var(--color-openai-bg);
            color: var(--color-openai-text);
        }

        * {
            scrollbar-width: thin;
            scrollbar-color: #cc0000 transparent;
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .scrollbar-none::-webkit-scrollbar {
            display: none !important;
        }
        /* Hide scrollbar for IE, Edge and Firefox */
        .scrollbar-none {
            -ms-overflow-style: none !important;  /* IE and Edge */
            scrollbar-width: none !important;  /* Firefox */
        }

        /* Responsive Sidebar Branding: Hide when screen height is <= 840px (normal 100% zoom or short viewport) */
        @media (max-height: 840px) {
            .sidebar-vertical-branding {
                display: none !important;
            }
        }

        /* Language visibility class switching */
        html[lang="id"] .lang-en, html[lang="id"] .lang-zh { display: none !important; }
        html[lang="en"] .lang-id, html[lang="en"] .lang-zh { display: none !important; }
        html[lang="zh"] .lang-id, html[lang="zh"] .lang-en { display: none !important; }
    </style>

    {{ $styles ?? '' }}
    <script>
        window.BIMA = {
            csrfToken: '{{ csrf_token() }}',
            sessionId: '{{ $activeSessionId ?? '' }}',
            reverbKey:    '{{ config('broadcasting.connections.reverb.key') ?: '' }}',
            reverbHost:   '{{ config('broadcasting.connections.reverb.options.host') ?: 'localhost' }}',
            reverbPort:   {{ (int) (config('broadcasting.connections.reverb.options.port') ?: 8080) }},
            reverbScheme: '{{ config('broadcasting.connections.reverb.options.scheme', 'http') }}',
        };
    </script>
</head>

<body class="min-h-screen bg-gray-50 flex font-sans antialiased" x-data="{ mobileAnalysisDrawer: false }">

    {{-- Mobile Header --}}
    <div
        class="lg:hidden fixed top-0 left-0 right-0 h-16 bg-gray-900 z-[60] flex items-center justify-between px-6 border-b border-white/10 shadow-lg">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-bima-red rounded-xl flex items-center justify-center text-white p-2 shadow-md shadow-red-500/20">
                <x-application-logo class="w-full h-full" />
            </div>
            <span class="text-white font-black tracking-tighter text-lg">Supervisory <span
                    class="text-bima-red">AI</span></span>
        </div>
        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="w-10 h-10 rounded-xl bg-white/5 hover:bg-white/10 text-white/60 hover:text-white flex items-center justify-center transition" title="Keluar">
                    <i data-lucide="log-out" class="w-5 h-5"></i>
                </button>
            </form>
        </div>
    </div>

    {{-- Sidebar (Desktop) --}}
    <aside
        class="hidden lg:flex w-24 bg-gray-900 flex-col items-center py-6 shrink-0 border-r border-white/5 z-50 fixed inset-y-0 left-0 overflow-visible">
        <div class="flex flex-col items-center justify-between h-full w-full">
            {{-- Logo --}}
            <div class="group cursor-pointer shrink-0">
                <div
                    class="w-14 h-14 bg-bima-red rounded-[1.5rem] flex items-center justify-center text-white shadow-[0_10px_30px_rgba(204,0,0,0.4)] group-hover:scale-110 transition-transform duration-500 p-3">
                    <x-application-logo class="w-full h-full" />
                </div>
            </div>

            {{-- Vertical Branding (Responsive: Visible on tall screen / zoom out, auto-hidden when height <= 840px) --}}
            <div class="sidebar-vertical-branding flex flex-col items-center gap-1 py-2 my-auto select-none transition-all duration-300 shrink-0">
                <span
                    class="[writing-mode:vertical-lr] rotate-180 text-sm font-black tracking-[0.3em] text-white uppercase opacity-80">Supervisory
                    AI</span>
                <div class="w-px h-8 bg-white/20 my-2"></div>
                <span
                    class="[writing-mode:vertical-lr] rotate-180 text-[0.55rem] font-bold tracking-widest text-white/50 uppercase">Prototipe</span>
            </div>

            {{-- Nav Container (Dashboard, Group Dropdown Modul Analisis, Metodologi) --}}
            <nav class="flex flex-col items-center gap-4 my-auto shrink-0">
                {{-- 1. Dashboard --}}
                <a href="{{ route('dashboard') }}"
                    class="w-12 h-12 rounded-2xl {{ request()->routeIs('dashboard') ? 'bg-bima-red text-white shadow-lg shadow-red-500/25 ring-2 ring-red-400/20' : 'bg-white/5 text-white/50 hover:text-white hover:bg-white/10' }} flex items-center justify-center transition-all group relative cursor-pointer"
                    title="Dashboard">
                    <i data-lucide="layout-dashboard" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <div
                        class="hidden lg:block absolute left-full ml-4 px-3 py-1.5 bg-gray-900 text-white text-[0.65rem] font-bold rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap uppercase tracking-widest z-50 border border-white/10 shadow-xl">
                        Dashboard</div>
                </a>

                {{-- 2. Modul Analisis AI (Group Dropdown Flyout) --}}
                <div class="relative" x-data="{ openAnalysis: false }" @click.away="openAnalysis = false">
                    {{-- Trigger Button --}}
                    <button type="button"
                        @click="openAnalysis = !openAnalysis; $nextTick(() => window.lucide && window.lucide.createIcons())"
                        class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all group relative cursor-pointer {{ request()->routeIs('analysis.*', 'document_analysis.*') ? 'bg-bima-red text-white shadow-lg shadow-red-500/25 ring-2 ring-red-400/20' : 'bg-white/5 text-white/50 hover:text-white hover:bg-white/10' }}"
                        title="Modul Analisis AI"
                        aria-haspopup="true"
                        :aria-expanded="openAnalysis">
                        <i data-lucide="layers" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>

                        {{-- Active Indicator Pulse Dot --}}
                        @if(request()->routeIs('analysis.*', 'document_analysis.*'))
                            <span class="absolute top-2.5 right-2.5 w-2 h-2 rounded-full bg-white shadow-sm animate-pulse"></span>
                        @endif

                        {{-- Tooltip when flyout is closed --}}
                        <div x-show="!openAnalysis"
                            class="hidden lg:block absolute left-full ml-4 px-3 py-1.5 bg-gray-900 text-white text-[0.65rem] font-bold rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap uppercase tracking-widest z-50 border border-white/10 shadow-xl">
                            Modul Analisis AI</div>
                    </button>

                    {{-- Flyout Dropdown Panel --}}
                    <div x-show="openAnalysis"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-x-2 scale-95"
                        x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                        x-transition:leave-end="opacity-0 translate-x-2 scale-95"
                        style="display: none;"
                        class="absolute left-full top-1/2 -translate-y-1/2 ml-4 w-80 bg-gray-900/98 backdrop-blur-2xl border border-white/15 rounded-3xl p-3.5 shadow-[0_20px_50px_rgba(0,0,0,0.6)] z-[80]">
                        
                        {{-- Flyout Header --}}
                        <div class="px-3 py-2 border-b border-white/10 mb-2.5 flex items-center justify-between">
                            <div>
                                <span class="text-[0.65rem] font-black uppercase tracking-widest text-bima-red">Modul Analisis AI</span>
                                <p class="text-[0.68rem] text-white/50 mt-0.5">Pilih instrumen & fitur analisis</p>
                            </div>
                            <span class="text-[0.6rem] font-black px-2 py-0.5 rounded-full bg-white/10 text-white/80 border border-white/10">4 Modul</span>
                        </div>

                        {{-- Modules List --}}
                        <div class="flex flex-col gap-1.5">
                            {{-- Analisis Suara V1 --}}
                            <a href="{{ route('analysis.create') }}"
                                class="flex items-center gap-3 p-2.5 rounded-2xl transition group/item {{ request()->routeIs('analysis.create', 'analysis.processing', 'analysis.result') && !request()->routeIs('analysis.v2.*') ? 'bg-bima-red text-white shadow-md shadow-red-500/20' : 'hover:bg-white/5 text-white/80 hover:text-white' }}">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ request()->routeIs('analysis.create', 'analysis.processing', 'analysis.result') && !request()->routeIs('analysis.v2.*') ? 'bg-white/20 text-white' : 'bg-red-500/10 text-red-400 group-hover/item:scale-105 group-hover/item:bg-red-500/20' }} transition">
                                    <i data-lucide="mic" class="w-4.5 h-4.5"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-bold truncate">Analisis Suara (V1)</div>
                                    <div class="text-[0.65rem] {{ request()->routeIs('analysis.create') ? 'text-white/80' : 'text-white/40' }} truncate">Audio rekaman bimbingan C-CDA</div>
                                </div>
                            </a>

                            {{-- Uji Coba Model V2 --}}
                            <a href="{{ route('analysis.v2.create') }}"
                                class="flex items-center gap-3 p-2.5 rounded-2xl transition group/item {{ request()->routeIs('analysis.v2.*') ? 'bg-purple-600 text-white shadow-md shadow-purple-500/20' : 'hover:bg-white/5 text-white/80 hover:text-white' }}">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ request()->routeIs('analysis.v2.*') ? 'bg-white/20 text-white' : 'bg-purple-500/10 text-purple-400 group-hover/item:scale-105 group-hover/item:bg-purple-500/20' }} transition">
                                    <i data-lucide="flask-conical" class="w-4.5 h-4.5"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-bold truncate flex items-center gap-1.5">
                                        <span>Uji Coba Model V2</span>
                                        <span class="text-[0.55rem] px-1.5 py-0.2 rounded bg-purple-400/20 text-purple-300 uppercase font-black">Beta</span>
                                    </div>
                                    <div class="text-[0.65rem] {{ request()->routeIs('analysis.v2.*') ? 'text-white/80' : 'text-white/40' }} truncate">WhisperX + RoBERTa Multi-Task</div>
                                </div>
                            </a>

                            {{-- Analisis PDF --}}
                            <a href="{{ route('document_analysis.create', ['locale' => app()->getLocale()]) }}"
                                class="flex items-center gap-3 p-2.5 rounded-2xl transition group/item {{ request()->routeIs('document_analysis.create', 'document_analysis.processing', 'document_analysis.result') ? 'bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-md shadow-orange-500/20' : 'hover:bg-white/5 text-white/80 hover:text-white' }}">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ request()->routeIs('document_analysis.create', 'document_analysis.processing', 'document_analysis.result') ? 'bg-white/20 text-white' : 'bg-amber-500/10 text-amber-400 group-hover/item:scale-105 group-hover/item:bg-amber-500/20' }} transition">
                                    <i data-lucide="file-text" class="w-4.5 h-4.5"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-bold truncate">Analisis Dokumen PDF</div>
                                    <div class="text-[0.65rem] {{ request()->routeIs('document_analysis.create') ? 'text-white/80' : 'text-white/40' }} truncate">Materi, ringkasan & briefing suara</div>
                                </div>
                            </a>

                            {{-- Riwayat Analisis PDF --}}
                            <a href="{{ route('document_analysis.history', ['locale' => app()->getLocale()]) }}"
                                class="flex items-center gap-3 p-2.5 rounded-2xl transition group/item {{ request()->routeIs('document_analysis.history', 'document_analysis.index') ? 'bg-gradient-to-r from-amber-600 to-orange-600 text-white shadow-md shadow-orange-500/20' : 'hover:bg-white/5 text-white/80 hover:text-white' }}">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 {{ request()->routeIs('document_analysis.history', 'document_analysis.index') ? 'bg-white/20 text-white' : 'bg-amber-500/10 text-amber-400 group-hover/item:scale-105 group-hover/item:bg-amber-500/20' }} transition">
                                    <i data-lucide="history" class="w-4.5 h-4.5"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="text-xs font-bold truncate">Riwayat Analisis PDF</div>
                                    <div class="text-[0.65rem] {{ request()->routeIs('document_analysis.history') ? 'text-white/80' : 'text-white/40' }} truncate">Arsip telaah dokumen Anda</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- 3. Methodology --}}
                <a href="{{ route('methodology') }}"
                    class="w-12 h-12 rounded-2xl {{ request()->routeIs('methodology') ? 'bg-bima-red text-white shadow-lg shadow-red-500/25 ring-2 ring-red-400/20' : 'bg-white/5 text-white/50 hover:text-white hover:bg-white/10' }} flex items-center justify-center transition-all group relative cursor-pointer"
                    title="Metodologi C-CDA">
                    <i data-lucide="book-open" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    <div
                        class="hidden lg:block absolute left-full ml-4 px-3 py-1.5 bg-gray-900 text-white text-[0.65rem] font-bold rounded-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap uppercase tracking-widest z-50 border border-white/10 shadow-xl">
                        Metodologi</div>
                </a>
            </nav>

            {{-- Bottom Actions (Accessibility, Compact Language Switcher, Logout) --}}
            <div class="mt-auto flex flex-col items-center gap-3 shrink-0">
                {{-- Accessibility Button --}}
                <button type="button" id="sidebar-accessibility-toggle"
                    onclick="toggleAccessibilityMode()"
                    class="w-11 h-11 rounded-2xl bg-white/5 hover:bg-white/10 text-white/50 hover:text-white transition flex items-center justify-center group/access relative cursor-pointer"
                    title="Mode Ramah Disabilitas (Panduan Suara)">
                    <i data-lucide="volume-2" class="w-5 h-5 group-hover/access:scale-110 transition-transform"></i>
                    <div
                        class="hidden lg:block absolute left-full ml-4 px-3 py-1.5 bg-gray-900 text-white text-[0.65rem] font-bold rounded-lg opacity-0 group-hover/access:opacity-100 pointer-events-none transition-opacity whitespace-nowrap uppercase tracking-widest z-50 shadow-xl border border-white/10">
                        <span id="sidebar-access-label">Aksesibilitas (Suara)</span>
                    </div>
                </button>

                {{-- Compact Language Switcher Dropdown --}}
                <div class="relative" x-data="{ openLang: false }" @click.away="openLang = false">
                    <button type="button" @click="openLang = !openLang"
                        class="w-11 h-11 rounded-2xl bg-white/5 hover:bg-white/10 text-white/80 hover:text-white border border-white/5 flex items-center justify-center cursor-pointer transition group"
                        title="Pilih Bahasa / Language">
                        <span class="text-[0.65rem] font-black tracking-widest uppercase">
                            {{ app()->getLocale() }}
                        </span>
                    </button>
                    
                    <div x-show="openLang"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 translate-x-2"
                        x-transition:enter-end="opacity-100 translate-x-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-x-0"
                        x-transition:leave-end="opacity-0 translate-x-2"
                        style="display: none;"
                        class="absolute left-full bottom-0 ml-4 bg-gray-900/98 backdrop-blur-2xl border border-white/10 rounded-2xl p-1.5 shadow-2xl flex flex-col gap-1 w-24 z-50">
                        <button type="button" onclick="setGlobalLanguage('id')" id="global-lang-id" class="w-full py-1.5 rounded-xl text-[0.65rem] font-bold tracking-wider text-left px-2.5 transition cursor-pointer {{ app()->getLocale() === 'id' ? 'bg-bima-red text-white' : 'text-white/70 hover:bg-white/10' }}">
                            ID (Idn)
                        </button>
                        <button type="button" onclick="setGlobalLanguage('en')" id="global-lang-en" class="w-full py-1.5 rounded-xl text-[0.65rem] font-bold tracking-wider text-left px-2.5 transition cursor-pointer {{ app()->getLocale() === 'en' ? 'bg-bima-red text-white' : 'text-white/70 hover:bg-white/10' }}">
                            EN (Eng)
                        </button>
                        <button type="button" onclick="setGlobalLanguage('zh')" id="global-lang-zh" class="w-full py-1.5 rounded-xl text-[0.65rem] font-bold tracking-wider text-left px-2.5 transition cursor-pointer {{ app()->getLocale() === 'zh' ? 'bg-bima-red text-white' : 'text-white/70 hover:bg-white/10' }}">
                            ZH (中文)
                        </button>
                    </div>
                </div>

                {{-- Separator --}}
                <div class="w-8 h-px bg-white/10"></div>

                {{-- Logout Button --}}
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit"
                        class="w-11 h-11 rounded-2xl bg-white/5 hover:bg-red-500/20 hover:text-white text-white/50 transition flex items-center justify-center group/logout cursor-pointer"
                        title="Keluar">
                        <i data-lucide="log-out" class="w-5 h-5 group-hover/logout:scale-110 transition-transform"></i>
                        <div
                            class="hidden lg:block absolute left-full ml-4 px-3 py-1.5 bg-gray-900 text-white text-[0.65rem] font-bold rounded-lg opacity-0 group-hover/logout:opacity-100 pointer-events-none transition-opacity whitespace-nowrap uppercase tracking-widest z-50 shadow-xl border border-white/10">
                            Keluar</div>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Mobile Bottom Nav (Action access for mobile) --}}
    <div class="lg:hidden fixed bottom-6 left-6 right-6 h-16 bg-gray-900/95 backdrop-blur-xl rounded-[2rem] border border-white/10 z-[70] flex items-center justify-around px-4 shadow-2xl">
        {{-- Dashboard --}}
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center w-12 h-12 rounded-xl {{ request()->routeIs('dashboard') ? 'text-bima-red font-black' : 'text-white/60 hover:text-white' }} transition">
            <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
            <span class="text-[0.55rem] font-bold mt-1 tracking-wider uppercase">Dash</span>
        </a>

        {{-- Modul Analisis (Opens Mobile Bottom Sheet Drawer) --}}
        <button type="button" @click="mobileAnalysisDrawer = true; $nextTick(() => window.lucide && window.lucide.createIcons())" class="flex flex-col items-center justify-center w-12 h-12 rounded-xl {{ request()->routeIs('analysis.*', 'document_analysis.*') ? 'text-bima-red font-black' : 'text-white/60 hover:text-white' }} transition cursor-pointer">
            <div class="relative">
                <i data-lucide="layers" class="w-5 h-5"></i>
                @if(request()->routeIs('analysis.*', 'document_analysis.*'))
                    <span class="absolute -top-0.5 -right-0.5 w-2 h-2 rounded-full bg-bima-red ring-2 ring-gray-900"></span>
                @endif
            </div>
            <span class="text-[0.55rem] font-bold mt-1 tracking-wider uppercase">Analisis</span>
        </button>

        {{-- Methodology --}}
        <a href="{{ route('methodology') }}" class="flex flex-col items-center justify-center w-12 h-12 rounded-xl {{ request()->routeIs('methodology') ? 'text-bima-red font-black' : 'text-white/60 hover:text-white' }} transition">
            <i data-lucide="book-open" class="w-5 h-5"></i>
            <span class="text-[0.55rem] font-bold mt-1 tracking-wider uppercase">Metode</span>
        </a>

        {{-- Accessibility / Voice Mode Toggle --}}
        <button type="button" onclick="toggleAccessibilityMode()" class="flex flex-col items-center justify-center w-12 h-12 rounded-xl text-white/60 hover:text-white transition cursor-pointer">
            <i data-lucide="volume-2" class="w-5 h-5"></i>
            <span class="text-[0.55rem] font-bold mt-1 tracking-wider uppercase">Suara</span>
        </button>
    </div>

    {{-- Mobile Analysis Modules Bottom Sheet Drawer --}}
    <div x-show="mobileAnalysisDrawer"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="lg:hidden fixed inset-0 z-[80] bg-black/60 backdrop-blur-sm"
        style="display: none;"
        @click="mobileAnalysisDrawer = false"></div>

    <div x-show="mobileAnalysisDrawer"
        x-transition:enter="transition ease-out duration-300 transform"
        x-transition:enter-start="translate-y-full"
        x-transition:enter-end="translate-y-0"
        x-transition:leave="transition ease-in duration-200 transform"
        x-transition:leave-start="translate-y-0"
        x-transition:leave-end="translate-y-full"
        class="lg:hidden fixed inset-x-0 bottom-0 z-[85] bg-gray-900 border-t border-white/10 rounded-t-[2.5rem] p-6 shadow-2xl max-h-[85vh] overflow-y-auto"
        style="display: none;">
        
        {{-- Drawer Handle --}}
        <div class="w-12 h-1.5 bg-white/20 rounded-full mx-auto mb-5"></div>

        {{-- Drawer Header --}}
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="text-lg font-black text-white tracking-tight uppercase">Modul Analisis AI</h3>
                <p class="text-xs text-white/50">Pilih instrumen & fitur analisis bimbingan</p>
            </div>
            <button @click="mobileAnalysisDrawer = false" class="w-10 h-10 rounded-xl bg-white/5 text-white/60 hover:text-white flex items-center justify-center cursor-pointer">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        {{-- Feature Cards Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-6">
            {{-- 1. Analisis Suara V1 --}}
            <a href="{{ route('analysis.create') }}"
                class="flex items-center gap-3.5 p-3.5 rounded-2xl border transition {{ request()->routeIs('analysis.create', 'analysis.processing', 'analysis.result') && !request()->routeIs('analysis.v2.*') ? 'bg-bima-red/20 border-bima-red text-white' : 'bg-white/5 border-white/5 text-white/80 hover:bg-white/10' }}">
                <div class="w-12 h-12 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center shrink-0">
                    <i data-lucide="mic" class="w-6 h-6"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-sm text-white truncate">Analisis Suara (V1)</div>
                    <div class="text-[0.7rem] text-white/50 truncate">Audio rekaman bimbingan</div>
                </div>
            </a>

            {{-- 2. Uji Coba Model V2 --}}
            <a href="{{ route('analysis.v2.create') }}"
                class="flex items-center gap-3.5 p-3.5 rounded-2xl border transition {{ request()->routeIs('analysis.v2.*') ? 'bg-purple-600/20 border-purple-500 text-white' : 'bg-white/5 border-white/5 text-white/80 hover:bg-white/10' }}">
                <div class="w-12 h-12 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center shrink-0">
                    <i data-lucide="flask-conical" class="w-6 h-6"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-sm text-white truncate flex items-center gap-2">
                        <span>Uji Coba Model V2</span>
                        <span class="text-[0.6rem] px-1.5 py-0.5 rounded bg-purple-500/30 text-purple-300 uppercase font-black">Beta</span>
                    </div>
                    <div class="text-[0.7rem] text-white/50 truncate">WhisperX + RoBERTa Multi-Task</div>
                </div>
            </a>

            {{-- 3. Analisis Dokumen PDF --}}
            <a href="{{ route('document_analysis.create', ['locale' => app()->getLocale()]) }}"
                class="flex items-center gap-3.5 p-3.5 rounded-2xl border transition {{ request()->routeIs('document_analysis.create', 'document_analysis.processing', 'document_analysis.result') ? 'bg-amber-500/20 border-amber-500 text-white' : 'bg-white/5 border-white/5 text-white/80 hover:bg-white/10' }}">
                <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                    <i data-lucide="file-text" class="w-6 h-6"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-sm text-white truncate">Analisis Dokumen PDF</div>
                    <div class="text-[0.7rem] text-white/50 truncate">Materi, Ringkasan & Briefing</div>
                </div>
            </a>

            {{-- 4. Riwayat Dokumen PDF --}}
            <a href="{{ route('document_analysis.history', ['locale' => app()->getLocale()]) }}"
                class="flex items-center gap-3.5 p-3.5 rounded-2xl border transition {{ request()->routeIs('document_analysis.history', 'document_analysis.index') ? 'bg-amber-500/20 border-amber-500 text-white' : 'bg-white/5 border-white/5 text-white/80 hover:bg-white/10' }}">
                <div class="w-12 h-12 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0">
                    <i data-lucide="history" class="w-6 h-6"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-sm text-white truncate">Riwayat Dokumen PDF</div>
                    <div class="text-[0.7rem] text-white/50 truncate">Daftar arsip telaah dokumen</div>
                </div>
            </a>
        </div>

        {{-- Mobile Language Switcher --}}
        <div class="border-t border-white/10 pt-4 flex items-center justify-between">
            <span class="text-xs font-bold text-white/60 uppercase tracking-wider">Pilihan Bahasa:</span>
            <div class="flex items-center gap-2">
                <button type="button" onclick="setGlobalLanguage('id')" class="px-3.5 py-2 rounded-xl text-xs font-black transition cursor-pointer {{ app()->getLocale() === 'id' ? 'bg-bima-red text-white shadow-md' : 'bg-white/5 text-white/60 hover:bg-white/10' }}">
                    ID (Indonesia)
                </button>
                <button type="button" onclick="setGlobalLanguage('en')" class="px-3.5 py-2 rounded-xl text-xs font-black transition cursor-pointer {{ app()->getLocale() === 'en' ? 'bg-bima-red text-white shadow-md' : 'bg-white/5 text-white/60 hover:bg-white/10' }}">
                    EN (English)
                </button>
                <button type="button" onclick="setGlobalLanguage('zh')" class="px-3.5 py-2 rounded-xl text-xs font-black transition cursor-pointer {{ app()->getLocale() === 'zh' ? 'bg-bima-red text-white shadow-md' : 'bg-white/5 text-white/60 hover:bg-white/10' }}">
                    ZH (中文)
                </button>
            </div>
        </div>
    </div>

    {{-- Main Content Wrapper --}}
    <div class="flex-1 flex flex-col w-full px-4 pt-20 pb-24 lg:pl-52 lg:pr-8 lg:py-10 relative">
        {{-- The main panel --}}
        <main class="flex-1 w-full flex flex-col relative overflow-hidden">
            {{ $slot }}
        </main>
    </div>

    {{-- Floating Accessibility Button (Universal Desktop & Mobile) --}}
    <button type="button" id="accessibility-toggle" onclick="toggleAccessibilityMode()"
        class="fixed bottom-24 right-5 lg:bottom-8 lg:right-8 z-[75] flex items-center gap-2.5 px-3.5 py-2.5 rounded-2xl bg-white/95 backdrop-blur-xl border border-gray-200 shadow-xl hover:shadow-2xl hover:scale-105 transition-all duration-300 cursor-pointer group"
        title="Mode Ramah Disabilitas (Panduan Suara / Voice Guidance)"
        aria-label="Mode Ramah Disabilitas">
        <span id="access-icon-wrapper" class="w-8 h-8 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center transition-all duration-300 group-hover:scale-110 shadow-sm">
            <i data-lucide="volume-2" class="w-4 h-4"></i>
        </span>
        <div class="text-left flex flex-col pr-1">
            <span id="access-status-text" class="text-[0.65rem] font-black uppercase tracking-wider text-gray-800">Suara: Nonaktif</span>
            <span class="text-[0.52rem] font-bold text-gray-400 uppercase tracking-widest">Aksesibilitas</span>
        </div>
    </button>

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
                wsHost: window.BIMA.reverbHost,
                wsPort: window.BIMA.reverbPort,
                wssPort: window.BIMA.reverbPort,
                forceTLS: window.BIMA.reverbScheme === 'https',
                disableStats: true,
                enabledTransports: ['ws', 'wss'],
                cluster: 'mt1',
            });
            window.__pusher = pusher;
            pusher.connection.bind('connected', () => console.log('[BIMA] Reverb connected ✓'));
        }

        // Global session delete
        function deleteSession(id, isActive) {
            window.dispatchEvent(new CustomEvent('open-delete-modal', {
                detail: {
                    id,
                    isActive
                }
            }));
        }
    </script>

    {{-- Delete Session Modal --}}
    <div x-data="{ open: false, sessionId: null, isActive: false, deleting: false }"
        @open-delete-modal.window="open = true; sessionId = $event.detail.id; isActive = $event.detail.isActive;">
        <div class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm" style="display:none;" x-show="open"
            x-transition.opacity></div>
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4 pointer-events-none" style="display:none;"
            x-show="open">
            <div class="bg-white rounded-[2.5rem] p-8 shadow-2xl w-full max-w-md pointer-events-auto border border-gray-100"
                x-show="open" x-transition>
                <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center text-red-600 mb-6 mx-auto">
                    <i data-lucide="alert-triangle" class="w-8 h-8"></i>
                </div>
                <h3 class="text-xl font-black text-gray-900 text-center mb-2">Hapus Sesi?</h3>
                <p class="text-sm text-gray-500 text-center mb-8 leading-relaxed">Sesi ini dan semua riwayat analisis di
                    dalamnya akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.</p>

                <div class="grid grid-cols-2 gap-4">
                    <button class="h-14 rounded-2xl text-sm font-bold text-gray-400 hover:bg-gray-50 transition"
                        @click="open = false" :disabled="deleting">Batal</button>
                    <button
                        class="h-14 rounded-2xl text-sm font-bold text-white bg-red-600 hover:bg-red-700 transition disabled:opacity-50 flex items-center justify-center gap-2"
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
                        <span x-show="deleting"
                            class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
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
    }" @open-history.window="open = true; loadHistory();" class="relative z-[70]">

        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-md" style="display:none;" x-show="open"
            x-transition.opacity @click="open = false"></div>

        <div class="fixed inset-y-0 right-0 w-full max-w-md bg-white shadow-2xl p-0 flex flex-col"
            style="display:none;" x-show="open" x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full">

            {{-- Header --}}
            <div class="p-8 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h2 class="text-xl font-black text-gray-900 tracking-tight">Riwayat Analisis</h2>
                    <p class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest mt-1">Daftar Sesi
                        Sebelumnya</p>
                </div>
                <button @click="open = false"
                    class="w-10 h-10 rounded-xl hover:bg-white hover:shadow-sm transition flex items-center justify-center text-gray-400">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            {{-- Session List --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-4">
                <template x-if="loading">
                    <div class="flex flex-col items-center justify-center py-20 gap-4">
                        <div class="w-8 h-8 border-3 border-gray-100 border-t-bima-red rounded-full animate-spin">
                        </div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Memuat
                            Riwayat...</span>
                    </div>
                </template>

                <template x-if="!loading && sessions.length === 0">
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <div
                            class="w-20 h-20 bg-gray-50 rounded-[2rem] flex items-center justify-center text-gray-200 mb-6">
                            <i data-lucide="folder-open" class="w-10 h-10"></i>
                        </div>
                        <p class="text-sm font-bold text-gray-400 uppercase tracking-widest">Belum ada riwayat sesi.
                        </p>
                    </div>
                </template>

                <template x-for="s in sessions" :key="s.id">
                    <div class="group relative bg-white border border-gray-100 hover:border-red-100 p-5 rounded-[2rem] transition-all hover:shadow-[0_20px_50px_rgba(204,0,0,0.05)] flex items-center gap-5 cursor-pointer overflow-hidden"
                        :class="s.id === activeSessionId ? 'border-red-200 bg-red-50/10 shadow-sm ring-1 ring-red-100' : ''"
                        @click="window.location.href = `/chat?session=${s.id}`">

                        {{-- Decorative Background --}}
                        <div
                            class="absolute -right-4 -top-4 w-24 h-24 bg-red-50/30 rounded-full blur-3xl opacity-0 group-hover:opacity-100 transition-opacity">
                        </div>

                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 transition-all duration-500 group-hover:scale-110 group-hover:rotate-3 shadow-sm relative overflow-hidden"
                            :class="s.id === activeSessionId ? 'bg-bima-red text-white shadow-xl shadow-red-200' :
                                'bg-gray-50 text-gray-400'">

                            <svg class="w-7 h-7 relative z-10" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                <path d="M8 9h8" opacity="0.5" />
                                <path d="M8 13h5" opacity="0.5" />
                            </svg>

                            <div x-show="s.id === activeSessionId"
                                class="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent"></div>
                        </div>

                        <div class="flex-1 min-w-0 relative z-10">
                            <h4 class="text-sm font-black text-gray-800 truncate group-hover:text-bima-red transition-colors duration-300 uppercase tracking-tight"
                                x-text="s.title"></h4>
                            <div class="flex items-center gap-2 mt-1.5">
                                <div class="w-1.5 h-1.5 rounded-full bg-green-500"
                                    :class="s.id === activeSessionId ? 'animate-pulse' : 'opacity-40'"></div>
                                <p class="text-[0.6rem] font-bold text-gray-400 uppercase tracking-widest"
                                    x-text="new Date(s.created_at).toLocaleDateString('id-ID', {day:'numeric', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit'})">
                                </p>
                            </div>
                        </div>

                        {{-- Delete Action --}}
                        <button
                            class="w-12 h-12 rounded-2xl text-gray-300 hover:text-white hover:bg-red-600 transition-all opacity-0 group-hover:opacity-100 flex items-center justify-center shadow-lg relative z-20 translate-x-4 group-hover:translate-x-0 duration-300"
                            @click.stop="deleteSession(s.id, s.id === activeSessionId)">
                            <i data-lucide="trash-2" class="w-5 h-5"></i>
                        </button>
                    </div>
                </template>
            </div>

            {{-- Footer Action --}}
            <div class="p-6 border-t border-gray-100">
                <button onclick="createNewSessionGlobal()"
                    class="w-full h-14 rounded-2xl bg-gray-900 text-white font-black text-sm flex items-center justify-center gap-3 hover:bg-black transition shadow-xl">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    MULAI SESI BARU
                </button>
            </div>
        </div>
    </div>

    <script>
        // --- ACCESSIBILITY LOGIC (DUAL-ENGINE) ---
        let accessibilityMode = localStorage.getItem('accessibilityMode') === 'true';
        const synth = window.speechSynthesis;
        let welcomeAudioObj = null;
        const welcomeAudioUrl = @json($welcomeAudio ?? ($__data['welcomeAudio'] ?? ''));

        function updateAccessibilityUI() {
            const iconWrapper = document.getElementById('access-icon-wrapper');
            const statusText = document.getElementById('access-status-text');
            const sidebarBtn = document.getElementById('sidebar-accessibility-toggle');
            const activeLang = document.documentElement.getAttribute('lang') || 'id';

            if (accessibilityMode) {
                if (iconWrapper) {
                    iconWrapper.className = 'w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center transition-all duration-300';
                }
                if (statusText) {
                    statusText.textContent = activeLang === 'zh' ? '语音: 已开启' : (activeLang === 'en' ? 'Voice: Active' : 'Suara: Aktif');
                    statusText.className = 'text-[0.65rem] font-black uppercase tracking-wider text-amber-600';
                }
                if (sidebarBtn) {
                    sidebarBtn.className = 'w-11 h-11 rounded-2xl bg-amber-500/20 text-amber-400 border border-amber-500/30 transition flex items-center justify-center group/access relative cursor-pointer';
                }
            } else {
                if (iconWrapper) {
                    iconWrapper.className = 'w-8 h-8 rounded-xl bg-gray-100 text-gray-700 flex items-center justify-center transition-all duration-300';
                }
                if (statusText) {
                    statusText.textContent = activeLang === 'zh' ? '语音: 未开启' : (activeLang === 'en' ? 'Voice: Inactive' : 'Suara: Nonaktif');
                    statusText.className = 'text-[0.65rem] font-black uppercase tracking-wider text-gray-800';
                }
                if (sidebarBtn) {
                    sidebarBtn.className = 'w-11 h-11 rounded-2xl bg-white/5 hover:bg-white/10 text-white/50 hover:text-white transition flex items-center justify-center group/access relative cursor-pointer';
                }
                if (synth) synth.cancel();
                if (welcomeAudioObj) {
                    welcomeAudioObj.pause();
                    welcomeAudioObj.currentTime = 0;
                }
            }
            if (window.lucide) window.lucide.createIcons();
        }

        function speakText(text) {
            if (!accessibilityMode || !synth) return;
            synth.cancel();
            const utterance = new SpeechSynthesisUtterance(text);
            const activeLang = document.documentElement.getAttribute('lang') || 'id';
            utterance.lang = activeLang === 'zh' ? 'zh-CN' : (activeLang === 'en' ? 'en-US' : 'id-ID');
            utterance.rate = 1.0;
            utterance.pitch = 1.0;
            synth.speak(utterance);
        }

        function playWelcomeAudio() {
            if (!accessibilityMode) return;
            if (welcomeAudioUrl) {
                try {
                    if (!welcomeAudioObj) {
                        welcomeAudioObj = new Audio(welcomeAudioUrl);
                    }
                    welcomeAudioObj.currentTime = 0;
                    welcomeAudioObj.play().catch(e => {
                        console.warn('Autoplay blocked or audio failed, falling back to Web Speech:', e);
                        speakGreetingFallback();
                    });
                } catch (e) {
                    speakGreetingFallback();
                }
            } else {
                speakGreetingFallback();
            }
        }

        function speakGreetingFallback() {
            const activeLang = document.documentElement.getAttribute('lang') || 'id';
            let msg = 'Mode ramah disabilitas diaktifkan. Panduan audio siap membantu navigasi Anda.';
            if (activeLang === 'en') {
                msg = 'Accessibility mode activated. Voice guidance is ready.';
            } if (activeLang === 'zh') {
                msg = '无障碍模式已开启。语音助手准备就绪。';
            }
            speakText(msg);
        }

        function toggleAccessibilityMode() {
            accessibilityMode = !accessibilityMode;
            localStorage.setItem('accessibilityMode', accessibilityMode ? 'true' : 'false');
            updateAccessibilityUI();

            if (accessibilityMode) {
                playWelcomeAudio();
            }
        }

        // Form elements speech feedback
        document.addEventListener('focusin', (e) => {
            if (!accessibilityMode) return;
            const target = e.target;
            if (['INPUT', 'SELECT', 'TEXTAREA'].includes(target.tagName)) {
                const labelEl = document.querySelector(`label[for="${target.id}"]`);
                const labelText = labelEl ? labelEl.innerText.trim() : (target.getAttribute('aria-label') || target.placeholder || target.name || '');
                const typeText = target.type === 'file' ? 'area unggah berkas audio' : (target.type === 'password' ? 'kolom kata sandi' : 'kolom');
                speakText(`Anda berada di ${typeText} ${labelText}.`);
            } else if (target.tagName === 'BUTTON' || target.tagName === 'A') {
                const btnText = target.innerText.trim() || target.getAttribute('title') || target.getAttribute('aria-label');
                if (btnText && btnText.length < 60) speakText(`Pilihan: ${btnText}`);
            }
        });

        // Hover speech feedback on action buttons
        document.addEventListener('mouseover', (e) => {
            if (!accessibilityMode) return;
            const btn = e.target.closest('button, .btn-primary, a[href]');
            if (btn && btn.id !== 'accessibility-toggle' && btn.id !== 'sidebar-accessibility-toggle' && !btn.dataset.spokenRecently) {
                btn.dataset.spokenRecently = 'true';
                setTimeout(() => delete btn.dataset.spokenRecently, 2000);
                const text = btn.innerText.trim() || btn.getAttribute('title') || btn.getAttribute('aria-label');
                if (text && text.length > 1 && text.length < 50) {
                    speakText(text);
                }
            }
        });

        function setGlobalLanguage(lang) {
            // Write to cookie (for backend autoredirect)
            document.cookie = "locale=" + lang + "; path=/; max-age=" + (30 * 24 * 60 * 60);
            
            // Save in localStorage (cache browser)
            localStorage.setItem('lang', lang);
            
            // Switch lang attribute on html tag
            document.documentElement.setAttribute('lang', lang);
            
            // Synchronize active URL path
            const currentPath = window.location.pathname; // e.g. "/id/dashboard"
            const pathParts = currentPath.split('/'); // ["", "id", "dashboard"]
            if (pathParts.length > 1 && ['id', 'en', 'zh'].includes(pathParts[1])) {
                if (pathParts[1] !== lang) {
                    pathParts[1] = lang;
                    const newPath = pathParts.join('/');
                    window.location.href = newPath + window.location.search + window.location.hash;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateAccessibilityUI();

            // If accessibility mode was already enabled, play welcome audio on first user click
            if (accessibilityMode) {
                document.body.addEventListener('click', () => {
                    if (welcomeAudioUrl && (!welcomeAudioObj || welcomeAudioObj.paused)) {
                        playWelcomeAudio();
                    }
                }, { once: true });
            }
        });
    </script>

    {{ $scripts ?? '' }}
</body>

</html>
