<?php

namespace App\Jobs;

use App\Models\ChatSession;
use App\Services\AgentOrchestratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessAgentWorkflow implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes for multi-round debate

    public function __construct(
        public ChatSession $session,
        public string $transcription
    ) {}

    public function handle(AgentOrchestratorService $orchestrator): void
    {
        Log::info('[Job] Processing agent workflow in background.', [
            'session_id' => $this->session->id
        ]);

        $orchestrator->analyse($this->session, $this->transcription);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('[Job Failed] Agent workflow failed.', [
            'session_id' => $this->session->id,
            'error' => $exception->getMessage()
        ]);

        // Notify UI that it failed so it stops spinning
        \App\Events\AgentStatusUpdated::dispatch(
            sessionId: $this->session->id,
            agentName: 'system',
            displayName: 'System',
            status: 'failed',
            message: 'Maaf, perdebatan terhenti karena gangguan teknis atau timeout.',
            score: 0.5,
            round: 1,
            totalRounds: 1
        );
    }
}
