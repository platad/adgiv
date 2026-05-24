<x-layouts.guest>
    <x-slot:titleId>Masuk</x-slot:titleId>
    <x-slot:titleEn>Sign In</x-slot:titleEn>
    <x-slot:titleZh>登录</x-slot:titleZh>

    <x-slot:styles>
        <style>
            html[lang="id"] .lang-en, html[lang="id"] .lang-zh { display: none !important; }
            html[lang="en"] .lang-id, html[lang="en"] .lang-zh { display: none !important; }
            html[lang="zh"] .lang-id, html[lang="zh"] .lang-en { display: none !important; }
        </style>
    </x-slot:styles>

    <div class="mb-8 pt-18">
        <h2 class="text-2xl font-black text-gray-900 tracking-tight uppercase">
            <span class="lang-id">Selamat Datang</span>
            <span class="lang-en">Welcome Back</span>
            <span class="lang-zh">欢迎回来</span>
        </h2>
        <p class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest mt-1">
            <span class="lang-id">Masuk untuk mulai analisis bimbingan</span>
            <span class="lang-en">Sign in to start supervision analysis</span>
            <span class="lang-zh">登录以开始学术辅导分析</span>
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div class="group">
            <label class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-focus-within:text-bima-red transition-colors" for="email">
                <span class="lang-id">Alamat Email</span>
                <span class="lang-en">Email Address</span>
                <span class="lang-zh">电子邮箱地址</span>
            </label>
            <div class="relative">
                <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 group-focus-within:text-bima-red transition-colors"></i>
                <input class="w-full pl-12 pr-4 h-14 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-900 focus:ring-2 focus:ring-red-100 focus:border-bima-red focus:bg-white outline-none transition-all placeholder:text-gray-300" 
                       type="email" id="email" name="email"
                       value="{{ old('email') }}" placeholder="nama@kampus.ac.id" required autofocus>
            </div>
            @error('email')
                <p class="text-[0.65rem] font-bold text-red-500 mt-2 flex items-center gap-1 uppercase tracking-wider"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p>
            @enderror
        </div>

        <div class="group">
            <label class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-focus-within:text-bima-red transition-colors" for="password">
                <span class="lang-id">Kata Sandi</span>
                <span class="lang-en">Password</span>
                <span class="lang-zh">登录密码</span>
            </label>
            <div class="relative">
                <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 group-focus-within:text-bima-red transition-colors"></i>
                <input class="w-full pl-12 pr-4 h-14 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-900 focus:ring-2 focus:ring-red-100 focus:border-bima-red focus:bg-white outline-none transition-all placeholder:text-gray-300" 
                       type="password" id="password"
                       name="password" placeholder="••••••••" required>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <label class="flex items-center gap-3 cursor-pointer group/check">
                <div class="relative w-5 h-5">
                    <input type="checkbox" name="remember" class="peer appearance-none w-5 h-5 border-2 border-gray-100 rounded-lg checked:bg-bima-red checked:border-bima-red transition-all cursor-pointer">
                    <i data-lucide="check" class="absolute inset-0 m-auto w-3.5 h-3.5 text-white opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none"></i>
                </div>
                <span class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest group-hover/check:text-gray-600 transition-colors">
                    <span class="lang-id">Ingat saya</span>
                    <span class="lang-en">Remember me</span>
                    <span class="lang-zh">记住我的登录状态</span>
                </span>
            </label>
        </div>

        <button type="submit" class="w-full h-14 bg-gray-900 text-white font-black text-sm uppercase tracking-[0.2em] rounded-2xl hover:bg-bima-red transition-all shadow-xl shadow-gray-100 hover:shadow-red-100 group flex items-center justify-center gap-3">
            <span class="lang-id">Masuk Sekarang</span>
            <span class="lang-en">Sign In Now</span>
            <span class="lang-zh">立即登录</span>
            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
        </button>
    </form>

    <x-slot:footer>
        <p class="text-center text-[0.7rem] font-bold text-gray-400 uppercase tracking-[0.2em] mb-6">
            <span class="lang-id">Belum punya akun? <a href="{{ route('register') }}" class="text-bima-red hover:text-bima-red-dark transition-colors">Daftar</a></span>
            <span class="lang-en">Don't have an account? <a href="{{ route('register') }}" class="text-bima-red hover:text-bima-red-dark transition-colors">Register</a></span>
            <span class="lang-zh">还没有账户？ <a href="{{ route('register') }}" class="text-bima-red hover:text-bima-red-dark transition-colors">立即注册</a></span>
        </p>

        <div class="h-px bg-gray-100 my-6"></div>

        {{-- Interactive Privacy Policy Link --}}
        <div class="mt-4 flex items-center justify-center gap-2">
            <a href="{{ route('privacy.consent') }}" class="text-[0.6rem] font-bold text-gray-400 hover:text-bima-red uppercase tracking-widest flex items-center gap-1 transition-colors underline">
                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                <span class="lang-id">Kebijakan Privasi & Persetujuan</span>
                <span class="lang-en">Privacy & Consent Terms</span>
                <span class="lang-zh">隐私政策与知情同意条款</span>
            </a>
        </div>
    </x-slot:footer>
</x-layouts.guest>
