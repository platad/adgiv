<x-layouts.guest>
    <x-slot:title>Login</x-slot:title>

    <h2 class="text-xl font-bold text-gray-900 mb-1">Selamat Datang</h2>
    <p class="text-sm text-gray-500 mb-6">Masuk untuk mulai menganalisis suara</p>

    <form method="POST" action="/login" class="space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2" for="email">Email</label>
            <input class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-black focus:border-black outline-none transition" type="email" id="email" name="email"
                   value="{{ old('email') }}" placeholder="nama@kampus.ac.id" required autofocus>
            @error('email')
                <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1"><i data-lucide="alert-circle" class="w-3 h-3"></i> {{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-2" for="password">Password</label>
            <input class="w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm text-gray-900 focus:ring-2 focus:ring-black focus:border-black outline-none transition" type="password" id="password"
                   name="password" placeholder="••••••••" required>
        </div>

        <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-600">
            <input type="checkbox" name="remember" class="rounded border-gray-300 text-black focus:ring-black">
            Ingat saya
        </label>

        <button type="submit" class="w-full bg-black text-white font-medium py-2.5 rounded-lg hover:bg-gray-800 transition shadow-sm mt-2">
            Masuk
        </button>
    </form>

    <x-slot:footer>
        <p class="text-center mt-6 text-sm text-gray-500">
            Belum punya akun? <a href="/register" class="text-black font-semibold hover:underline">Daftar</a>
        </p>
    </x-slot:footer>
</x-layouts.guest>
