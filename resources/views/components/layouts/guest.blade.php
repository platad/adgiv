<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Supervisory AI' }} – Multi-Agent Voice Analysis</title>
    <meta name="description" content="Supervisory AI – Sistem analisis suara berbasis Multi-Agent AI untuk membedakan Mahasiswa dan Dosen.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --font-sans: 'Inter', sans-serif;
            --color-bima-red: #cc0000;
            --color-bima-red-dark: #990000;
        }
        body { background-color: #fafafa; }
        .glass { background: rgba(255, 255, 255, 0.8); backdrop-filter: blur(10px); }
    </style>
    {{ $styles ?? '' }}
</head>
<body class="font-sans min-h-screen flex flex-col lg:flex-row relative text-gray-900 overflow-x-hidden">

    {{-- Left Side: Branding & Research (Desktop Only) --}}
    <div class="hidden lg:flex w-[45%] bg-gray-900 relative overflow-hidden flex-col items-center justify-center p-12 shrink-0">
        {{-- Background Decorations --}}
        <div class="absolute inset-0">
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-bima-red/20 to-transparent"></div>
            <div class="absolute -top-40 -left-40 w-[60rem] h-[60rem] bg-bima-red/10 rounded-full blur-[120px] animate-pulse"></div>
            <div class="absolute -bottom-40 -right-40 w-[60rem] h-[60rem] bg-red-600/5 rounded-full blur-[120px] animate-pulse" style="animation-delay: 3s"></div>
            {{-- Subtle Grid Pattern --}}
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 40px 40px;"></div>
        </div>

        <div class="relative z-10 w-full max-w-xl">
            <div class="mb-12">
                <div class="w-24 h-24 bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2.5rem] flex items-center justify-center text-white shadow-2xl mb-8 group hover:scale-110 transition-transform duration-500">
                    <x-application-logo class="w-12 h-12" />
                </div>
                <h1 class="text-5xl font-black tracking-tighter text-white uppercase leading-none">
                    Supervisory <span class="text-bima-red">AI</span>
                </h1>
                <p class="text-xs font-bold text-white/40 mt-4 uppercase tracking-[0.4em] leading-relaxed">
                    Advanced Multi-Agent Voice Analysis
                </p>
            </div>

            <div class="text-white">
                <x-research-footer isDark="true" />
            </div>
        </div>

        {{-- Bottom Copyright --}}
        <div class="absolute bottom-10 left-12 text-[0.6rem] font-bold text-white/20 uppercase tracking-widest">
            &copy; 2026 Supervisory AI &bull; UMPO &bull; UMPRI
        </div>
    </div>

    {{-- Right Side: Auth Form --}}
    <div class="flex-1 bg-white relative flex items-center justify-center p-6 sm:p-12">
        {{-- Mobile Background Decoration --}}
        <div class="lg:hidden absolute inset-0 overflow-hidden pointer-events-none opacity-50">
            <div class="absolute -top-40 -left-40 w-[40rem] h-[40rem] bg-red-500/5 rounded-full blur-[100px]"></div>
            <div class="absolute -bottom-40 -right-40 w-[40rem] h-[40rem] bg-red-600/5 rounded-full blur-[100px]"></div>
        </div>

        <div class="w-full max-w-md relative z-10">
            {{-- Mobile branding --}}
            <div class="lg:hidden text-center mb-12">
                <div class="w-20 h-20 bg-bima-red rounded-[2rem] mx-auto mb-6 flex items-center justify-center text-white shadow-xl p-4">
                    <x-application-logo class="w-full h-full" />
                </div>
                <h1 class="text-3xl font-black tracking-tight text-gray-900 uppercase">Supervisory <span class="text-bima-red">AI</span></h1>
            </div>

            <div class="bg-white lg:border-none lg:shadow-none rounded-[2.5rem] relative overflow-hidden">
                {{ $slot }}
            </div>
            
            <div class="mt-8 relative z-10">
                {{ $footer ?? '' }}
            </div>

            {{-- Mobile Research Footer --}}
            <div class="lg:hidden mt-12 border-t border-gray-100 pt-10">
                <x-research-footer />
            </div>
        </div>
    </div>

<script>lucide.createIcons();</script>
</body>
</html>
