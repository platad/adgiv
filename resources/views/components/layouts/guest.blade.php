<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'BIMA AI' }} – Multi-Agent Voice Analysis</title>
    <meta name="description" content="BIMA – Sistem analisis suara berbasis Multi-Agent AI untuk membedakan Mahasiswa dan Dosen.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        @theme {
            --font-sans: 'Inter', sans-serif;
            --color-openai-bg: #f9f9f9;
        }
        body { background-color: var(--color-openai-bg); }
    </style>
    {{ $styles ?? '' }}
</head>
<body class="font-sans min-h-screen flex items-center justify-center relative p-4 text-gray-900">

    <div class="w-full max-w-sm relative z-10">
        <div class="text-center mb-8">
            <div class="w-12 h-12 bg-black rounded-xl mx-auto mb-4 flex items-center justify-center text-white shadow-sm">
                <i data-lucide="brain" class="w-6 h-6"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">BIMA AI</h1>
            <p class="text-sm text-gray-500 mt-1">Multi-Agent Voice Analysis System</p>
        </div>
        
        <div class="bg-white border border-gray-200 rounded-2xl p-6 sm:p-8 shadow-sm">
            {{ $slot }}
        </div>
        
        {{ $footer ?? '' }}
    </div>

<script>lucide.createIcons();</script>
</body>
</html>
