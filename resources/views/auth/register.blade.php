<x-layouts.guest>
    <x-slot:titleId>Daftar</x-slot:titleId>
    <x-slot:titleEn>Register</x-slot:titleEn>
    <x-slot:titleZh>注册</x-slot:titleZh>

    <x-slot:styles>
        <style>
            html[lang="id"] .lang-en, html[lang="id"] .lang-zh { display: none !important; }
            html[lang="en"] .lang-id, html[lang="en"] .lang-zh { display: none !important; }
            html[lang="zh"] .lang-id, html[lang="zh"] .lang-en { display: none !important; }
        </style>
    </x-slot:styles>

    <div class="mb-8 pt-10">
        <h2 class="text-2xl font-black text-gray-900 tracking-tight uppercase">
            <span class="lang-id">Buat Akun Baru</span>
            <span class="lang-en">Create New Account</span>
            <span class="lang-zh">创建新账户</span>
        </h2>
        <p class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest mt-1">
            <span class="lang-id">Bergabung dengan platform Supervisory AI</span>
            <span class="lang-en">Join the Supervisory AI platform</span>
            <span class="lang-zh">加入 Supervisory AI 平台</span>
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div class="group">
            <label class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-focus-within:text-bima-red transition-colors" for="name">
                <span class="lang-id">Nama Lengkap</span>
                <span class="lang-en">Full Name</span>
                <span class="lang-zh">真实姓名</span>
            </label>
            <div class="relative">
                <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 group-focus-within:text-bima-red transition-colors"></i>
                <input class="w-full pl-12 pr-4 h-14 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-900 focus:ring-2 focus:ring-red-100 focus:border-bima-red focus:bg-white outline-none transition-all placeholder:text-gray-300" 
                       type="text" id="name" name="name"
                       value="{{ old('name') }}" placeholder="Nama Lengkap" required autofocus>
            </div>
            @error('name') 
                <p class="text-[0.65rem] font-bold text-red-500 mt-2 flex items-center gap-1 uppercase tracking-wider"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p> 
            @enderror
        </div>

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
                       value="{{ old('email') }}" placeholder="nama@kampus.ac.id" required>
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
                       name="password" placeholder="Minimal 8 karakter" required>
            </div>
            @error('password') 
                <p class="text-[0.65rem] font-bold text-red-500 mt-2 flex items-center gap-1 uppercase tracking-wider"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p> 
            @enderror
        </div>

        <div class="group">
            <label class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-focus-within:text-bima-red transition-colors" for="password_confirmation">
                <span class="lang-id">Konfirmasi Sandi</span>
                <span class="lang-en">Confirm Password</span>
                <span class="lang-zh">确认密码</span>
            </label>
            <div class="relative">
                <i data-lucide="shield-check" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 group-focus-within:text-bima-red transition-colors"></i>
                <input class="w-full pl-12 pr-4 h-14 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-900 focus:ring-2 focus:ring-red-100 focus:border-bima-red focus:bg-white outline-none transition-all placeholder:text-gray-300" 
                       type="password" id="password_confirmation"
                       name="password_confirmation" placeholder="Ulangi sandi" required>
            </div>
        </div>

        <button type="submit" class="w-full h-14 bg-gray-900 text-white font-black text-sm uppercase tracking-[0.2em] rounded-2xl hover:bg-bima-red transition-all shadow-xl shadow-gray-100 hover:shadow-red-100 group flex items-center justify-center gap-3 mt-4">
            <span class="lang-id">Daftar Sekarang</span>
            <span class="lang-en">Register Now</span>
            <span class="lang-zh">立即注册</span>
            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
        </button>
    </form>

    <x-slot:footer>
        <p class="text-center text-[0.7rem] font-bold text-gray-400 uppercase tracking-[0.2em] mb-6">
            <span class="lang-id">Sudah punya akun? <a href="{{ route('login') }}" class="text-bima-red hover:text-bima-red-dark transition-colors">Masuk</a></span>
            <span class="lang-en">Already have an account? <a href="{{ route('login') }}" class="text-bima-red hover:text-bima-red-dark transition-colors">Sign In</a></span>
            <span class="lang-zh">已有账户？ <a href="{{ route('login') }}" class="text-bima-red hover:text-bima-red-dark transition-colors">立即登录</a></span>
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
