@props(['isDark' => false])

<div class="mt-12 pt-10 {{ !$isDark ? 'border-t border-gray-100/50' : '' }} px-4 lg:px-0">
    {{-- SVG Filter to remove black background --}}
    <svg width="0" height="0" class="absolute">
        <filter id="remove-black" color-interpolation-filters="sRGB">
            <feColorMatrix type="matrix" values="1 0 0 0 0 
                                                 0 1 0 0 0 
                                                 0 0 1 0 0 
                                                 1 1 1 0 -0.1" />
        </filter>
    </svg>

    <div class="flex flex-col items-center lg:items-start text-center lg:text-left space-y-8">
        {{-- Logos Image --}}
        <div class="w-full">
            <div class="{{ $isDark ? 'bg-black/40 backdrop-blur-xl border border-white/5 p-6 rounded-[2rem]' : '' }} inline-flex flex-wrap items-center justify-center lg:justify-start gap-10">
                <div class="relative group">
                    <img src="{{ asset('assets/img/Bima Kemendikbut.png') }}" 
                         style="{{ $isDark ? 'filter: url(#remove-black);' : '' }}"
                         class="h-10 sm:h-12 lg:h-14 w-auto object-contain drop-shadow-sm hover:scale-[1.05] transition-transform duration-500" 
                         alt="Bima Kemendikbud">
                </div>
                
                <div class="h-8 w-px {{ $isDark ? 'bg-white/10' : 'bg-gray-200' }} hidden sm:block"></div>

                <img src="{{ asset('assets/img/logo_Universitas-Muhammadiyah-Ponorogo-1.png') }}" 
                     class="h-12 sm:h-14 lg:h-16 w-auto object-contain drop-shadow-sm hover:scale-[1.05] transition-transform duration-500" 
                     alt="Universitas Muhammadiyah Ponorogo">
            </div>
        </div>

        {{-- Research Details --}}
        <div class="space-y-6 max-w-xl mx-auto lg:mx-0">
            <div class="space-y-2">
                <h3 class="text-[0.7rem] font-black uppercase tracking-[0.25em] leading-tight {{ $isDark ? 'text-white' : 'text-gray-900' }}">
                    Kolaborasi Penelitian dan Penguatan Kemitraan
                </h3>
                <div class="h-0.5 w-12 rounded-full {{ $isDark ? 'bg-white/30' : 'bg-bima-red/30' }} mx-auto lg:mx-0"></div>
            </div>
            
            <p class="text-[0.65rem] font-bold leading-relaxed uppercase tracking-wider {{ $isDark ? 'text-white/60' : 'text-gray-500' }}">
                Pengembangan Prototipe Advice-Giving berbasis Text-Mining terintegrasi Artificial Intelligence untuk Peningkatan Daya Saing Penelitian dan Mewujudkan Pendidikan Tinggi Inklusif
            </p>

            <div class="flex justify-center lg:justify-start">
                <div class="inline-flex items-center px-4 py-1.5 rounded-full border shadow-sm {{ $isDark ? 'bg-white/5 border-white/10' : 'bg-gray-50 border-gray-100' }}">
                    <span class="text-[0.55rem] font-black uppercase tracking-[0.2em] {{ $isDark ? 'text-white/80' : 'text-bima-red' }} opacity-80">
                        Skema Terapan - Luaran Prototipe
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
