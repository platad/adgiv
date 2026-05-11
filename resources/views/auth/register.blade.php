<x-layouts.guest>
    <x-slot:title>Daftar</x-slot:title>

    <h2 class="text-xl font-bold text-gray-900 mb-1">Buat Akun Baru</h2>
    <p class="text-sm text-gray-500 mb-6">Bergabung dengan platform analisis suara BIMA</p>

    <form method="POST" action="/register" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2" for="name">Nama Lengkap</label>
            <input class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-black focus:border-black outline-none transition" type="text" id="name" name="name"
                   value="{{ old('name') }}" placeholder="Nama Lengkap" required autofocus>
            @error('name') <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2" for="email">Email</label>
            <input class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-black focus:border-black outline-none transition" type="email" id="email" name="email"
                   value="{{ old('email') }}" placeholder="nama@kampus.ac.id" required>
            @error('email') <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2" for="password">Password</label>
            <input class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-black focus:border-black outline-none transition" type="password" id="password"
                   name="password" placeholder="Minimal 8 karakter" required>
            @error('password') <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2" for="password_confirmation">Konfirmasi Password</label>
            <input class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-black focus:border-black outline-none transition" type="password" id="password_confirmation"
                   name="password_confirmation" placeholder="Ulangi password" required>
        </div>

        <button type="submit" class="w-full bg-black text-white font-medium py-2.5 rounded-lg hover:bg-gray-800 transition shadow-sm mt-2">
            Buat Akun
        </button>
    </form>

    <x-slot:footer>
        <p class="text-center mt-6 text-sm text-gray-500">
            Sudah punya akun? <a href="/login" class="text-black font-semibold hover:underline">Masuk</a>
        </p>
    </x-slot:footer>
</x-layouts.guest>
