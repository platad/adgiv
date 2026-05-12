<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'BIMA AI' }} – Multi-Agent Voice Analysis</title>
    <meta name="description" content="BIMA – Sistem analisis suara berbasis Multi-Agent AI untuk membedakan Mahasiswa dan Dosen.">
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
<body class="font-sans min-h-screen flex items-center justify-center relative p-6 text-gray-900 py-12 sm:py-20 overflow-x-hidden">

    {{-- Background Decorations --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -left-40 w-[40rem] h-[40rem] bg-red-500/5 rounded-full blur-[100px] animate-pulse"></div>
        <div class="absolute -bottom-40 -right-40 w-[40rem] h-[40rem] bg-red-600/5 rounded-full blur-[100px] animate-pulse" style="animation-delay: 2s"></div>
    </div>

    <div class="w-full max-w-sm relative z-10">
        <div class="text-center mb-10 group cursor-default">
            <div class="w-20 h-20 bg-gradient-to-br from-bima-red to-bima-red-dark rounded-[2rem] mx-auto mb-6 flex items-center justify-center text-white shadow-[0_20px_50px_rgba(204,0,0,0.3)] transition-all group-hover:scale-110 group-hover:rotate-3 duration-500">
                <i data-lucide="brain" class="w-10 h-10"></i>
            </div>
            <h1 class="text-3xl font-black tracking-tight text-gray-900 uppercase">BIMA <span class="text-bima-red">AI</span></h1>
            <p class="text-[0.65rem] font-bold text-gray-400 mt-2 uppercase tracking-[0.3em] leading-relaxed">Multi-Agent Voice Analysis System</p>
        </div>
        
        <div class="bg-white border border-gray-100 rounded-[2.5rem] p-8 sm:p-10 shadow-[0_30px_100px_rgba(0,0,0,0.04)] relative overflow-hidden">
            {{-- Decoration inside card --}}
            <div class="absolute -right-10 -top-10 w-32 h-32 bg-red-50/50 rounded-full blur-2xl"></div>
            
            <div class="relative z-10">
                {{ $slot }}
            </div>
        </div>
        
        <div class="mt-8 relative z-10">
            {{ $footer ?? '' }}
        </div>
    </div>

<script>lucide.createIcons();</script>
</body>
</html>
