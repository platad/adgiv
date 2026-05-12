<x-layouts.guest>
    <x-slot:title>Daftar</x-slot:title>

    <div class="mb-8">
        <h2 class="text-2xl font-black text-gray-900 tracking-tight uppercase">Buat Akun Baru</h2>
        <p class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest mt-1">Bergabung dengan platform bima ai</p>
    </div>

    <form method="POST" action="/register" class="space-y-5">
        @csrf

        <div class="group">
            <label class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-focus-within:text-bima-red transition-colors" for="name">Nama Lengkap</label>
            <div class="relative">
                <i data-lucide="user" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 group-focus-within:text-bima-red transition-colors"></i>
                <input class="w-full pl-12 pr-4 h-14 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-900 focus:ring-2 focus:ring-red-100 focus:border-bima-red focus:bg-white outline-none transition-all placeholder:text-gray-300" 
                       type="text" id="name" name="name"
                       value="{{ old('name') }}" placeholder="Nama Lengkap" required autofocus>
            </div>
            @error('name') <p class="text-[0.65rem] font-bold text-red-500 mt-2 flex items-center gap-1 uppercase tracking-wider"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p> @enderror
        </div>

        <div class="group">
            <label class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-focus-within:text-bima-red transition-colors" for="email">Alamat Email</label>
            <div class="relative">
                <i data-lucide="mail" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 group-focus-within:text-bima-red transition-colors"></i>
                <input class="w-full pl-12 pr-4 h-14 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-900 focus:ring-2 focus:ring-red-100 focus:border-bima-red focus:bg-white outline-none transition-all placeholder:text-gray-300" 
                       type="email" id="email" name="email"
                       value="{{ old('email') }}" placeholder="nama@kampus.ac.id" required>
            </div>
            @error('email') <p class="text-[0.65rem] font-bold text-red-500 mt-2 flex items-center gap-1 uppercase tracking-wider"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p> @enderror
        </div>

        <div class="group">
            <label class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-focus-within:text-bima-red transition-colors" for="password">Kata Sandi</label>
            <div class="relative">
                <i data-lucide="lock" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 group-focus-within:text-bima-red transition-colors"></i>
                <input class="w-full pl-12 pr-4 h-14 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-900 focus:ring-2 focus:ring-red-100 focus:border-bima-red focus:bg-white outline-none transition-all placeholder:text-gray-300" 
                       type="password" id="password"
                       name="password" placeholder="Minimal 8 karakter" required>
            </div>
            @error('password') <p class="text-[0.65rem] font-bold text-red-500 mt-2 flex items-center gap-1 uppercase tracking-wider"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p> @enderror
        </div>

        <div class="group">
            <label class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-focus-within:text-bima-red transition-colors" for="password_confirmation">Konfirmasi Sandi</label>
            <div class="relative">
                <i data-lucide="shield-check" class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300 group-focus-within:text-bima-red transition-colors"></i>
                <input class="w-full pl-12 pr-4 h-14 bg-gray-50 border border-gray-100 rounded-2xl text-sm font-bold text-gray-900 focus:ring-2 focus:ring-red-100 focus:border-bima-red focus:bg-white outline-none transition-all placeholder:text-gray-300" 
                       type="password" id="password_confirmation"
                       name="password_confirmation" placeholder="Ulangi sandi" required>
            </div>
        </div>

        <button type="submit" class="w-full h-14 bg-gray-900 text-white font-black text-sm uppercase tracking-[0.2em] rounded-2xl hover:bg-bima-red transition-all shadow-xl shadow-gray-100 hover:shadow-red-100 group flex items-center justify-center gap-3 mt-4">
            <span>Daftar Sekarang</span>
            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
        </button>
    </form>

    <x-slot:footer>
        <p class="text-center text-[0.7rem] font-bold text-gray-400 uppercase tracking-[0.2em]">
            Sudah punya akun? <a href="/login" class="text-bima-red hover:text-bima-red-dark transition-colors">Masuk</a>
        </p>
    </x-slot:footer>
</x-layouts.guest>
