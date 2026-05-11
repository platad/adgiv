{{--
    Component: chat-bubble
    Props:
      - $message: Message model instance
      - $isLatest: bool (for animation)
--}}
@props(['message', 'isLatest' => false])

@php
    $isUser      = $message->role === 'user';
    $isAssistant = $message->role === 'assistant';
    $isSystem    = $message->role === 'system';
    $meta        = $message->metadata ?? [];
    $decision    = $meta['decision'] ?? null;
    $confidence  = $meta['confidence'] ?? null;
@endphp

<div class="bubble-wrapper {{ $isUser ? 'bubble-wrapper--user' : '' }} {{ $isLatest ? 'bubble--animate' : '' }}">

    @if (!$isUser)
        <div class="bubble-avatar">
            @if ($isAssistant) 🧠 @else ⚙️ @endif
        </div>
    @endif

    <div class="bubble-body">

        @if ($isAssistant && $decision)
            {{-- Final Decision Card --}}
            <div class="decision-card {{ $decision === 'Dosen' ? 'decision-card--dosen' : 'decision-card--mahasiswa' }}">
                <div class="decision-header">
                    <span class="decision-icon">{{ $decision === 'Dosen' ? '🎓' : '📚' }}</span>
                    <div>
                        <span class="decision-label">Keputusan Akhir</span>
                        <span class="decision-value">{{ $decision }}</span>
                    </div>
                    <div class="decision-confidence">
                        <span class="conf-num">{{ round($confidence * 100) }}%</span>
                        <span class="conf-label">Confidence</span>
                    </div>
                </div>
                <div class="confidence-track">
                    <div class="confidence-track-fill" style="width: {{ round($confidence * 100) }}%"></div>
                </div>
                <p class="decision-reasoning">{{ $message->content }}</p>
            </div>
        @elseif ($isAssistant)
            <div class="bubble bubble--assistant">
                <p>{{ $message->content }}</p>
            </div>
        @elseif ($isUser)
            <div class="bubble bubble--user">
                <p>{{ $message->content }}</p>
            </div>
        @else
            <div class="bubble bubble--system">
                <p>{{ $message->content }}</p>
            </div>
        @endif

        <span class="bubble-time">{{ $message->created_at->format('H:i') }}</span>
    </div>

    @if ($isUser)
        <div class="bubble-avatar bubble-avatar--user">
            {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
        </div>
    @endif

</div>

<style>
.bubble-wrapper {
    display: flex; align-items: flex-end; gap: 0.6rem;
    padding: 0.35rem 1.25rem;
}
.bubble-wrapper--user { flex-direction: row-reverse; }
.bubble--animate { animation: bubbleFadeIn 0.35s ease forwards; }
@keyframes bubbleFadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

.bubble-avatar {
    width: 32px; height: 32px; border-radius: 50%;
    background: linear-gradient(135deg, #7c3aed, #06b6d4);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem; flex-shrink: 0; color: #fff; font-weight: 600;
}
.bubble-avatar--user { background: linear-gradient(135deg, #4f87ff, #7c3aed); }
.bubble-body { max-width: 70%; display: flex; flex-direction: column; gap: 0.2rem; }
.bubble-wrapper--user .bubble-body { align-items: flex-end; }

.bubble {
    padding: 0.7rem 1rem; border-radius: 16px;
    font-size: 0.87rem; line-height: 1.6; word-break: break-word;
}
.bubble--user {
    background: linear-gradient(135deg, #7c3aed, #4f87ff);
    color: #fff;
    border-bottom-right-radius: 4px;
}
.bubble--assistant {
    background: var(--bg-card);
    border: 1px solid var(--border);
    color: var(--text-primary);
    border-bottom-left-radius: 4px;
}
.bubble--system {
    background: rgba(245,158,11,0.08);
    border: 1px solid rgba(245,158,11,0.2);
    color: #fbbf24;
    border-radius: 10px;
    font-size: 0.8rem;
}

.bubble-time { font-size: 0.65rem; color: var(--text-muted); padding: 0 0.25rem; }

/* Decision Card */
.decision-card {
    border-radius: 16px; padding: 1rem 1.1rem;
    border: 1px solid transparent; max-width: 420px;
}
.decision-card--dosen {
    background: rgba(124,58,237,0.1);
    border-color: rgba(124,58,237,0.3);
}
.decision-card--mahasiswa {
    background: rgba(6,182,212,0.08);
    border-color: rgba(6,182,212,0.3);
}
.decision-header {
    display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;
}
.decision-icon { font-size: 1.75rem; }
.decision-label { display: block; font-size: 0.68rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.07em; }
.decision-value { display: block; font-size: 1.2rem; font-weight: 700; color: var(--text-primary); }
.decision-confidence { margin-left: auto; text-align: center; }
.conf-num  { display: block; font-size: 1.4rem; font-weight: 800; color: #a78bfa; }
.conf-label{ display: block; font-size: 0.65rem; color: var(--text-muted); }
.confidence-track { height: 5px; background: rgba(255,255,255,0.06); border-radius: 3px; overflow: hidden; margin-bottom: 0.75rem; }
.confidence-track-fill { height: 100%; background: linear-gradient(90deg, #7c3aed, #06b6d4); border-radius: 3px; transition: width 1s ease; }
.decision-reasoning { font-size: 0.82rem; color: var(--text-secondary); line-height: 1.6; }
</style>
