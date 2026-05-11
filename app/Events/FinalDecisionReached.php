<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FinalDecisionReached implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $sessionId,
        public readonly string $decision,   // 'Mahasiswa' | 'Dosen'
        public readonly float $confidence,  // 0.0 – 1.0
        public readonly string $reasoning,
        public readonly array $agentVerdicts = [],
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("chat-session.{$this->sessionId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'final.decision.reached';
    }

    public function broadcastWith(): array
    {
        return [
            'session_id'     => $this->sessionId,
            'decision'       => $this->decision,
            'confidence'     => $this->confidence,
            'reasoning'      => $this->reasoning,
            'agent_verdicts' => $this->agentVerdicts,
            'timestamp'      => now()->toISOString(),
        ];
    }
}
