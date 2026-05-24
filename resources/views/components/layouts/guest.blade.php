<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if(app()->getLocale() === 'zh')
            {{ $titleZh ?? ($title ?? '登录') }}
        @elseif(app()->getLocale() === 'en')
            {{ $titleEn ?? ($title ?? 'Sign In') }}
        @else
            {{ $titleId ?? ($title ?? 'Masuk') }}
        @endif
        – Multi-Agent Voice Analysis
    </title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
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
        .glass { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); }
        
        /* Language visibility class switching */
        html[lang="id"] .lang-en, html[lang="id"] .lang-zh { display: none !important; }
        html[lang="en"] .lang-id, html[lang="en"] .lang-zh { display: none !important; }
        html[lang="zh"] .lang-id, html[lang="zh"] .lang-en { display: none !important; }
    </style>
    {{ $styles ?? '' }}
</head>
<body class="font-sans min-h-screen flex flex-col lg:flex-row relative text-gray-900 overflow-x-hidden pb-24 lg:pb-0">

    {{-- Left Side: Branding & Research (Desktop Only) --}}
    <div class="hidden lg:flex w-[45%] bg-gray-900 relative overflow-hidden flex-col items-center justify-center p-12 shrink-0">
        {{-- Background Decorations --}}
        <div class="absolute inset-0">
            <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-br from-bima-red/20 to-transparent"></div>
            <div class="absolute -top-40 -left-40 w-[60rem] h-[60rem] bg-bima-red/10 rounded-full blur-[120px]"></div>
            <div class="absolute -bottom-40 -right-40 w-[60rem] h-[60rem] bg-red-600/5 rounded-full blur-[120px]" style="animation-delay: 3s"></div>
            {{-- Subtle Grid Pattern --}}
            <div class="absolute inset-0 opacity-[0.03]" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 40px 40px;"></div>
        </div>

        <div class="relative z-10 w-full max-w-xl">
            <div class="mb-12">
                <div class="w-24 h-24 bg-white/5 backdrop-blur-xl border border-white/10 rounded-[2.5rem] flex items-center justify-center text-white shadow-2xl mb-8 group hover:scale-110 transition-transform duration-500 group-hover:rotate-3 shadow-sm relative overflow-hidden">
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
            &copy; 2026 Supervisory AI &bull; UMPO
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

    {{-- Floating Professional Language & Clock Widget in Bottom-Right Corner --}}
    <div class="fixed bottom-6 right-6 z-[80] flex flex-col items-end pointer-events-auto">
        <div class="glass border border-gray-200/80 rounded-2xl p-3 shadow-xl flex items-center gap-4 transition-all duration-300 hover:shadow-2xl hover:scale-[1.02]">
            {{-- Clock --}}
            <div class="flex items-center gap-2">
                <div class="text-left leading-none">
                    <span id="guest-live-time" class="block text-[0.7rem] font-black tracking-widest font-mono text-gray-900">00:00:00</span>
                    <span id="guest-live-date" class="block text-[0.5rem] font-bold text-gray-400 uppercase tracking-widest mt-0.5">---</span>
                </div>
            </div>

            {{-- Separator --}}
            <div class="w-px h-6 bg-gray-200"></div>

            {{-- Language switch buttons --}}
            <div class="bg-gray-200/50 p-0.5 rounded-lg flex items-center gap-0.5">
                <button type="button" id="guest-lang-id" onclick="setLanguage('id')" class="px-2.5 py-1 rounded-lg text-[0.6rem] font-black tracking-widest transition-all">ID</button>
                <button type="button" id="guest-lang-en" onclick="setLanguage('en')" class="px-2.5 py-1 rounded-lg text-[0.6rem] font-black tracking-widest transition-all">EN</button>
                <button type="button" id="guest-lang-zh" onclick="setLanguage('zh')" class="px-2.5 py-1 rounded-lg text-[0.6rem] font-black tracking-widest transition-all">ZH</button>
            </div>
        </div>
    </div>

    {{-- Centralized Client-side Clock & Language Scripts --}}
    <script>
        function updateClock() {
            const now = new Date();
            let hours = String(now.getHours()).padStart(2, '0');
            let minutes = String(now.getMinutes()).padStart(2, '0');
            let seconds = String(now.getSeconds()).padStart(2, '0');
            
            const liveTimeEl = document.getElementById('guest-live-time');
            if (liveTimeEl) liveTimeEl.textContent = `${hours}:${minutes}:${seconds}`;

            const activeLang = document.documentElement.getAttribute('lang') || 'id';
            let dateStr = '';
            
            if (activeLang === 'id') {
                const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                dateStr = `${days[now.getDay()]}, ${now.getDate()} ${months[now.getMonth()]} ${now.getFullYear()} WIB`;
            } else if (activeLang === 'zh') {
                const days = ['星期日', '星期一', '星期二', '星期三', '星期四', '星期五', '星期六'];
                dateStr = `${now.getFullYear()}年${now.getMonth() + 1}月${now.getDate()}日 ${days[now.getDay()]} (WIB)`;
            } else {
                const days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                const tz = 'GMT' + (now.getTimezoneOffset() <= 0 ? '+' : '-') + Math.abs(Math.floor(now.getTimezoneOffset() / 60));
                dateStr = `${days[now.getDay()]}, ${months[now.getMonth()]} ${now.getDate()} ${now.getFullYear()} ${tz}`;
            }
            
            const liveDateEl = document.getElementById('guest-live-date');
            if (liveDateEl) liveDateEl.textContent = dateStr;
        }

        function setLanguage(lang) {
            // Write to cookie (for backend autoredirect)
            document.cookie = "locale=" + lang + "; path=/; max-age=" + (30 * 24 * 60 * 60);
            
            // Save in localStorage (cache browser)
            localStorage.setItem('lang', lang);
            
            // Switch lang attribute
            document.documentElement.setAttribute('lang', lang);
            
            // Update Active Buttons Class
            const btnId = document.getElementById('guest-lang-id');
            const btnEn = document.getElementById('guest-lang-en');
            const btnZh = document.getElementById('guest-lang-zh');
            
            const activeClass = "px-2.5 py-1 rounded-lg text-[0.6rem] font-black tracking-widest bg-bima-red text-white shadow-sm transition-all duration-300 border-none cursor-pointer";
            const inactiveClass = "px-2.5 py-1 rounded-lg text-[0.6rem] font-black tracking-widest text-gray-400 hover:text-gray-900 transition-all duration-300 border-none cursor-pointer";
            
            if (btnId) btnId.className = (lang === 'id') ? activeClass : inactiveClass;
            if (btnEn) btnEn.className = (lang === 'en') ? activeClass : inactiveClass;
            if (btnZh) btnZh.className = (lang === 'zh') ? activeClass : inactiveClass;
            
            // Modify placeholders dynamically
            const emailInput = document.getElementById('email');
            if (emailInput) {
                if (lang === 'zh') {
                    emailInput.placeholder = 'name@kampus.ac.id';
                } else if (lang === 'en') {
                    emailInput.placeholder = 'name@kampus.ac.id';
                } else {
                    emailInput.placeholder = 'nama@kampus.ac.id';
                }
            }

            const nameInput = document.getElementById('name');
            if (nameInput) {
                if (lang === 'zh') nameInput.placeholder = '您的真实姓名';
                else if (lang === 'en') nameInput.placeholder = 'Your full name';
                else nameInput.placeholder = 'Nama Lengkap';
            }

            const pwdInput = document.getElementById('password');
            if (pwdInput) {
                if (lang === 'zh') pwdInput.placeholder = '至少 8 位字符';
                else if (lang === 'en') pwdInput.placeholder = 'At least 8 characters';
                else pwdInput.placeholder = 'Minimal 8 karakter';
            }

            const pwdConfInput = document.getElementById('password_confirmation');
            if (pwdConfInput) {
                if (lang === 'zh') pwdConfInput.placeholder = '重复您的密码';
                else if (lang === 'en') pwdConfInput.placeholder = 'Repeat password';
                else pwdConfInput.placeholder = 'Ulangi sandi';
            }
            
            updateClock();

            // Synchronize active URL path
            const currentPath = window.location.pathname; // e.g. "/id/login"
            const pathParts = currentPath.split('/'); // ["", "id", "login"]
            if (pathParts.length > 1 && ['id', 'en', 'zh'].includes(pathParts[1])) {
                if (pathParts[1] !== lang) {
                    pathParts[1] = lang;
                    const newPath = pathParts.join('/');
                    window.location.href = newPath + window.location.search + window.location.hash;
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Read language state from active URL segment first
            const pathParts = window.location.pathname.split('/');
            let activeLocale = 'id';
            if (pathParts.length > 1 && ['id', 'en', 'zh'].includes(pathParts[1])) {
                activeLocale = pathParts[1];
            } else {
                activeLocale = localStorage.getItem('lang') || 'id';
            }
            
            setLanguage(activeLocale);
            
            updateClock();
            setInterval(updateClock, 1000);
            
            if (window.lucide) {
                window.lucide.createIcons();
            }
        });
    </script>
</body>
</html>
