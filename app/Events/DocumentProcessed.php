<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DocumentProcessed implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly int $documentId,
        public readonly int $userId,
        public readonly string $filename,
        public readonly string $status,
        public readonly ?string $summary = null,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel("user.{$this->userId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'document.processed';
    }

    public function broadcastWith(): array
    {
        return [
            'document_id' => $this->documentId,
            'filename'    => $this->filename,
            'status'      => $this->status,
            'summary'     => $this->summary,
            'timestamp'   => now()->toISOString(),
        ];
    }
}
