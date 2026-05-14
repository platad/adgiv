<x-layouts.guest>
    <x-slot:title>Masuk</x-slot:title>

    <div class="mb-8 pt-18">
        <h2 class="text-2xl font-black text-gray-900 tracking-tight uppercase">Selamat Datang</h2>
        <p class="text-[0.65rem] font-bold text-gray-400 uppercase tracking-widest mt-1">Masuk untuk mulai analisis bimbingan</p>
    </div>

    <form method="POST" action="/login" class="space-y-6">
        @csrf

        <div class="group">
            <label class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-focus-within:text-bima-red transition-colors" for="email">Alamat Email</label>
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
            <label class="block text-[0.6rem] font-black text-gray-400 uppercase tracking-[0.2em] mb-2 group-focus-within:text-bima-red transition-colors" for="password">Kata Sandi</label>
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
                <span class="text-[0.7rem] font-bold text-gray-400 uppercase tracking-widest group-hover/check:text-gray-600 transition-colors">Ingat saya</span>
            </label>
        </div>

        <button type="submit" class="w-full h-14 bg-gray-900 text-white font-black text-sm uppercase tracking-[0.2em] rounded-2xl hover:bg-bima-red transition-all shadow-xl shadow-gray-100 hover:shadow-red-100 group flex items-center justify-center gap-3">
            <span>Masuk Sekarang</span>
            <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
        </button>
    </form>

    <x-slot:footer>
        <p class="text-center text-[0.7rem] font-bold text-gray-400 uppercase tracking-[0.2em]">
            Belum punya akun? <a href="/register" class="text-bima-red hover:text-bima-red-dark transition-colors">Daftar</a>
        </p>
    </x-slot:footer>
</x-layouts.guest>
