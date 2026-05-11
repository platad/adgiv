{{--
    Component: session-sidebar
    Props:
      - $sessions  : Collection<ChatSession>
      - $activeId  : int  (current session id)
--}}
@props(['sessions' => collect(), 'activeId' => null])

<div class="flex flex-col gap-1">

    {{-- New Session button --}}
    <button
        class="flex items-center justify-between w-full p-2.5 mb-4 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl shadow-sm hover:bg-gray-50 hover:border-gray-400 transition-all group"
        onclick="window.dispatchEvent(new CustomEvent('new-session'))"
        id="btn-new-session"
    >
        <span class="flex items-center gap-2">
            <svg class="w-4 h-4 text-black" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
            Sesi Baru
        </span>
    </button>

    <p class="px-2 mb-2 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Riwayat Sesi</p>

    <div class="space-y-1">
        @forelse ($sessions as $session)
            <div class="group relative flex items-center rounded-xl transition {{ $session->id === $activeId ? 'bg-gray-100 ring-1 ring-gray-200' : 'hover:bg-gray-50' }}" id="session-row-{{ $session->id }}">
                
                {{-- Main clickable area --}}
                <button
                    class="flex items-center gap-3 flex-1 min-w-0 p-2.5 rounded-xl border-none bg-transparent cursor-pointer text-left"
                    onclick="window.location.href='/chat?session={{ $session->id }}'"
                    id="session-item-{{ $session->id }}"
                    title="{{ $session->title }}"
                >
                    <span class="shrink-0 {{ $session->id === $activeId ? 'text-black' : 'text-gray-400' }}">
                        @if($session->document_id)
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                        @else
                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        @endif
                    </span>
                    <div class="flex-1 min-w-0">
                        <div class="text-[0.82rem] font-medium truncate {{ $session->id === $activeId ? 'text-black' : 'text-gray-600' }}">{{ Str::limit($session->title, 25) }}</div>
                    </div>
                </button>

                {{-- Delete button --}}
                <button
                    class="absolute right-2 opacity-0 group-hover:opacity-100 p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-all rounded-lg"
                    onclick="window.dispatchEvent(new CustomEvent('open-delete-modal', { detail: { id: {{ $session->id }}, isActive: {{ $session->id === $activeId ? 'true' : 'false' }} } }))"
                    title="Hapus sesi ini"
                    id="delete-session-{{ $session->id }}"
                >
                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                </button>
            </div>
        @empty
            <p class="px-2 text-sm text-gray-400 mt-2">Belum ada sesi.</p>
        @endforelse
    </div>

</div>
