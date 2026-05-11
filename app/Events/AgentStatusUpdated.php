<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AgentStatusUpdated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $sessionId,
        public readonly string $agentName,
        public readonly string $displayName,
        public readonly string $status, // thinking | done | failed
        public readonly string $message,
        public readonly array $resultData = [],
        public readonly float $score = 0.5,
        public readonly int $round = 1,
        public readonly int $totalRounds = 1,
        public readonly bool $isArgument = false,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("chat-session.{$this->sessionId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'agent.status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'session_id'   => $this->sessionId,
            'agent_name'   => $this->agentName,
            'display_name' => $this->displayName,
            'status'       => $this->status,
            'message'      => $this->message,
            'result_data'  => $this->resultData,
            'score'        => $this->score,
            'round'        => $this->round,
            'total_rounds' => $this->totalRounds,
            'is_argument'  => $this->isArgument,
            'timestamp'    => now()->toISOString(),
        ];
    }
}
