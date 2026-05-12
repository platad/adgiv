{{-- resources/views/components/chat/analysis-results-dashboard.blade.php --}}
<div class="space-y-8 animate-fade-in">
    {{-- Main Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        {{-- Card 1: Advice Category --}}
        <div class="p-6 rounded-[2rem] bg-gray-50 border border-gray-100 hover:border-bima-red/20 transition-all group/card">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-bima-red shadow-sm group-hover/card:shadow-md transition-shadow">
                    <i data-lucide="award" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">Kategori Advice</h4>
                    <p class="text-lg font-bold text-gray-900" x-text="session.advice_category || '-'"></p>
                </div>
            </div>
        </div>

        {{-- Card 2: Power Relation --}}
        <div class="p-6 rounded-[2rem] bg-gray-50 border border-gray-100 hover:border-bima-red/20 transition-all group/card">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-bima-red shadow-sm group-hover/card:shadow-md transition-shadow">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">Karakter Relasi</h4>
                    <p class="text-lg font-bold text-gray-900" x-text="session.character_category || '-'"></p>
                </div>
            </div>
        </div>

        {{-- Card 3: Intonation --}}
        <div class="p-6 rounded-[2rem] bg-gray-50 border border-gray-100 hover:border-bima-red/20 transition-all group/card">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-bima-red shadow-sm group-hover/card:shadow-md transition-shadow">
                    <i data-lucide="volume-2" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">Intonasi</h4>
                    <p class="text-lg font-bold text-gray-900" x-text="session.intonation_analysis || '-'"></p>
                </div>
            </div>
        </div>

        {{-- Card 4: Domain --}}
        <div class="p-6 rounded-[2rem] bg-gray-50 border border-gray-100 hover:border-bima-red/20 transition-all group/card">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-white flex items-center justify-center text-bima-red shadow-sm group-hover/card:shadow-md transition-shadow">
                    <i data-lucide="book-open" class="w-6 h-6"></i>
                </div>
                <div>
                    <h4 class="text-[0.6rem] font-black text-gray-400 uppercase tracking-widest">Ranah Pembicaraan</h4>
                    <p class="text-xs font-bold text-gray-600 line-clamp-2" x-text="session.summary_domain || '-'"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Large Content Cards --}}
    <div class="space-y-6">
        {{-- Aim / Target --}}
        <div class="p-8 rounded-[2.5rem] bg-bima-red text-white shadow-xl shadow-red-100">
            <h4 class="text-[0.65rem] font-black uppercase tracking-[0.2em] mb-4 opacity-80 flex items-center gap-2">
                <i data-lucide="target" class="w-4 h-4"></i> Arah Tujuan
            </h4>
            <p class="text-lg font-medium leading-relaxed" x-text="session.aim_target || '-'"></p>
        </div>

        {{-- Suggestions --}}
        <div class="p-8 rounded-[2.5rem] bg-white border-2 border-green-50 shadow-sm">
            <h4 class="text-[0.65rem] font-black text-green-600 uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
                <i data-lucide="sparkles" class="w-4 h-4"></i> Saran Perbaikan (Kimi AI)
            </h4>
            <div class="text-gray-700 leading-relaxed font-medium" x-text="session.suggestions || '-'"></div>
        </div>
    </div>
</div>
