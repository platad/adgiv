<?php

namespace App\Services;

use App\Events\AgentStatusUpdated;
use App\Events\FinalDecisionReached;
use App\Models\AgentPrompt;
use App\Models\AgentWorkflowLog;
use App\Models\ChatSession;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

/**
 * AgentOrchestratorService
 *
 * Responsibility: Coordinate the full Multi-Agent AI Debate workflow.
 *
 * Flow:
 * 1. Build shared context from the session's linked CSV document (RAG).
 * 2. Run 3 analyst agents IN PARALLEL:  Kosakata | Otoritas | Gaya Bahasa
 * 3. Aggregate their verdicts.
 * 4. Send aggregated verdicts to the Judge Agent.
 * 5. Broadcast FinalDecisionReached and persist everything.
 */
class AgentOrchestratorService
{
    /** Agent slugs that run in parallel */
    private const ANALYST_AGENTS = ['kosakata', 'otoritas', 'gaya_bahasa'];

    /** The judge agent slug */
    private const JUDGE_AGENT = 'judge';

    public function __construct(
        private readonly KimiApiService  $kimiApi,
        private readonly DocumentService $documentService,
    ) {}

    /**
     * Entry point: analyse a user's transcribed text within a chat session.
     *
     * @param  ChatSession $session         The active chat session.
     * @param  string      $transcription   Text from Web Speech API or Whisper.
     * @return array                        Judge agent's final verdict array.
     */
    public function analyse(ChatSession $session, string $transcription): array
    {
        Log::info('[Orchestrator] Starting multi-agent DEBATE WAR.', [
            'session_id' => $session->id,
        ]);

        $sharedContext = $this->buildRagContext($session);

        // ── STEP 1: Determine Debate Duration (Rounds) ──────────────────────
        $totalRounds = $this->estimateDebateDuration($transcription);

        // ── NEW: Broadcast initial status so UI shows total rounds immediately ──
        AgentStatusUpdated::dispatch(
            sessionId:   $session->id,
            agentName:   'orchestrator',
            displayName: 'BIMA Orchestrator',
            status:      'thinking',
            message:     "Menyiapkan arena perdebatan ($totalRounds ronde)...",
            score:       0.5,
            round:       1,
            totalRounds: $totalRounds
        );

        // ── STEP 1.5: Generate Initial Conclusion ───────────────────────────
        try {
            $initialResult = $this->kimiApi->complete(
                systemPrompt: "Anda adalah BIMA AI. Berikan KESIMPULAN AWAL yang SINGKAT (1 kalimat) tentang apakah transkripsi ini cenderung Mahasiswa atau Dosen.",
                userMessage:  "Transkripsi: {$transcription}",
                extraContext: $sharedContext
            );
            Message::create([
                'chat_session_id' => $session->id,
                'role'            => 'assistant',
                'content'         => "### 🏁 Kesimpulan Awal:\n" . trim($initialResult['content'] ?? 'Sedang menganalisis...'),
            ]);
        } catch (\Throwable $e) {
            Log::error('[BIMA] Initial conclusion failed: ' . $e->getMessage());
        }

        // ── STEP 2: Conduct Multi-turn Debate ───────────────────────────────
        $debateResults = $this->conductDebate($session, $transcription, $sharedContext, $totalRounds);
        $analystVerdicts = $debateResults['final_verdicts'];

        // ── STEP 3: Aggregate and send to Judge for Summary ──────────────────
        $finalVerdict = $this->runJudgeAgent($session, $transcription, $analystVerdicts, $sharedContext);

        // ── STEP 4: Persist assistant response (Final Decision) ──────────────
        Message::create([
            'chat_session_id' => $session->id,
            'role'            => 'assistant',
            'content'         => $finalVerdict['reasoning'],
            'metadata'        => [
                'decision'       => $finalVerdict['decision'],
                'confidence'     => $finalVerdict['confidence'],
                'thinking'       => $finalVerdict['thinking'] ?? null,
                'agent_verdicts' => $analystVerdicts,
                'debate_history' => $debateResults['history'],
            ],
        ]);

        // ── STEP 5: Broadcast final decision ────────────────────────────────
        FinalDecisionReached::dispatch(
            sessionId:     $session->id,
            decision:      $finalVerdict['decision'],
            confidence:    $finalVerdict['confidence'],
            reasoning:     $finalVerdict['reasoning'],
            agentVerdicts: $analystVerdicts,
        );

        // ── STEP 6: Generate 3-5 specific recommendations ────────────────────
        $this->runRecommendationAgent($session, $transcription, $finalVerdict, $sharedContext);

        return $finalVerdict;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // PRIVATE HELPERS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Build Retrieval-Augmented Generation context from the session's linked document.
     */
    private function buildRagContext(ChatSession $session): array
    {
        $session->loadMissing('document');

        if (! $session->document || ! $session->document->isProcessed()) {
            return [];
        }

        $csvText = $this->documentService->getExtractedText($session->document);

        return ['csv_data' => $csvText];
    }

    /**
     * Estimate how many rounds the debate should last based on content length.
     */
    private function estimateDebateDuration(string $text): int
    {
        $wordCount = str_word_count($text);
        if ($wordCount < 20) return 2;
        if ($wordCount < 100) return 3;
        return 4;
    }

    /**
     * The core Multi-turn Debate logic.
     */
    private function conductDebate(ChatSession $session, string $transcription, array $context, int $totalRounds): array
    {
        $history = [];
        $currentVerdicts = [];
        $currentScore = 0.5; // 0.0 = Mahasiswa, 1.0 = Dosen

        for ($round = 1; $round <= $totalRounds; $round++) {
            foreach (self::ANALYST_AGENTS as $agentSlug) {
                $agentPrompt = AgentPrompt::getActivePrompt($agentSlug);
                if (!$agentPrompt) continue;

                // Build specific prompt for this round
                $roundPrompt = $this->buildRoundPrompt($round, $totalRounds, $agentPrompt, $transcription, $history);

                $this->broadcastAgentStatus(
                    session:     $session,
                    agentPrompt: $agentPrompt,
                    agentSlug:   $agentSlug,
                    status:      'thinking',
                    message:     $round === 1 ? "Menganalisis data..." : "Menanggapi argumen agen lain (Ronde $round)...",
                    score:       $currentScore,
                    round:       $round,
                    totalRounds: $totalRounds
                );

                try {
                    $result = $this->kimiApi->complete(
                        systemPrompt: $roundPrompt,
                        userMessage:  "Berikan analisis/tanggapan Anda sekarang dalam format JSON.",
                        extraContext: $context
                    );

                    $verdict = $this->parseAgentVerdict($result['content'], $agentSlug);
                    
                    // Update history for next turns
                    $history[] = [
                        'round' => $round,
                        'agent' => $agentSlug,
                        'text'  => $verdict['reasoning'],
                        'verdict' => $verdict['verdict']
                    ];
                    $currentVerdicts[$agentSlug] = $verdict;

                    // ── NEW: Persist each argument as a Message so it flows like a WhatsApp group ──
                    Message::create([
                        'chat_session_id' => $session->id,
                        'role'            => 'assistant',
                        'content'         => "**[{$agentPrompt->display_name} - Ronde {$round}]**: {$verdict['reasoning']}",
                        'metadata'        => [
                            'agent_name' => $agentSlug,
                            'verdict'    => $verdict['verdict'],
                            'confidence' => $verdict['confidence'],
                            'round'      => $round,
                        ],
                    ]);

                    // Update Score
                    $currentScore = $this->calculateTendencyScore($currentVerdicts);

                    $this->broadcastAgentStatus(
                        session:     $session,
                        agentPrompt: $agentPrompt,
                        agentSlug:   $agentSlug,
                        status:      'done',
                        message:     "Selesai Ronde $round.",
                        resultData:  $verdict,
                        score:       $currentScore,
                        round:       $round,
                        totalRounds: $totalRounds,
                        isArgument:  true, // Tells frontend to add a bubble
                    );

                } catch (\Throwable $e) {
                    Log::error("Debate round $round failed for $agentSlug: " . $e->getMessage());
                }
            }
        }

        return [
            'final_verdicts' => $currentVerdicts,
            'history'        => $history
        ];
    }

    private function buildRoundPrompt(int $round, int $totalRounds, $agentPrompt, string $text, array $history): string
    {
        $base = $agentPrompt->system_prompt;
        
        if ($round === 1) {
            return $base . "\n\nIni adalah ronde pertama. Analisis transkripsi berikut:\n" . $text;
        }

        $historySummary = "";
        foreach ($history as $h) {
            $historySummary .= "Ronde {$h['round']} - Agen {$h['agent']}: {$h['text']} (Verdik: {$h['verdict']})\n";
        }

        return $base . "\n\nKonteks Perdebatan (Ronde sebelumnya):\n" . $historySummary . 
               "\n\nTugas Anda di Ronde $round/$totalRounds: Tanggapi atau sanggah poin dari agen lain secara **SINGKAT, PADAT, DAN JELAS** (maksimal 2 kalimat). Pertahankan atau ubah verdik Anda jika ada bukti baru dari diskusi ini.\nData asli: " . $text;
    }

    private function calculateTendencyScore(array $verdicts): float
    {
        if (empty($verdicts)) return 0.5;
        
        $total = 0;
        foreach ($verdicts as $v) {
            // Dosen contributes positively to 1.0, Mahasiswa to 0.0
            $val = (strtolower($v['verdict']) === 'dosen') ? 1.0 : 0.0;
            // Weigh by confidence
            $total += ($val * $v['confidence']) + (0.5 * (1 - $v['confidence']));
        }
        
        return $total / count($verdicts);
    }

    /**
     * Run the Judge Agent which aggregates all analyst verdicts into a final decision.
     */
    private function runJudgeAgent(
        ChatSession $session,
        string $transcription,
        array $analystVerdicts,
        array $sharedContext
    ): array {
        $judgePrompt = AgentPrompt::getActivePrompt(self::JUDGE_AGENT);

        if (! $judgePrompt) {
            // Fallback: simple majority vote
            return $this->fallbackMajorityVote($analystVerdicts);
        }

        // Broadcast: judge is THINKING
        $this->broadcastAgentStatus(
            session:      $session,
            agentPrompt:  $judgePrompt,
            agentSlug:    self::JUDGE_AGENT,
            status:       'thinking',
            message:      'Judge Agent sedang merumuskan keputusan akhir dari semua laporan...',
        );

        $judgeLog = AgentWorkflowLog::create([
            'chat_session_id' => $session->id,
            'agent_name'      => self::JUDGE_AGENT,
            'status'          => 'thinking',
            'process_note'    => 'Agregasi hasil dari semua agen analis.',
        ]);

        // Build aggregated input for the judge
        $aggregatedReport = $this->buildJudgeInput($transcription, $analystVerdicts);

        try {
            $result = $this->kimiApi->complete(
                systemPrompt: $judgePrompt->system_prompt,
                userMessage:  $aggregatedReport,
                extraContext: $sharedContext,
            );

            $finalVerdict = $this->parseJudgeVerdict($result['content']);

            $judgeLog->update([
                'status'       => 'done',
                'process_note' => 'Keputusan akhir telah ditetapkan.',
                'result_data'  => $finalVerdict,
            ]);

            $this->broadcastAgentStatus(
                session:      $session,
                agentPrompt:  $judgePrompt,
                agentSlug:    self::JUDGE_AGENT,
                status:       'done',
                message:      "Keputusan Akhir: {$finalVerdict['decision']} (Confidence: " . round($finalVerdict['confidence'] * 100) . "%)",
                resultData:   $finalVerdict,
            );

            return $finalVerdict;
        } catch (\Throwable $e) {
            Log::error('[Orchestrator] Judge Agent failed.', ['error' => $e->getMessage()]);
            $judgeLog->update(['status' => 'failed', 'process_note' => $e->getMessage()]);
            return $this->fallbackMajorityVote($analystVerdicts);
        }
    }

    /**
     * Broadcast an AgentStatusUpdated event for real-time UI updates.
     */
    private function broadcastAgentStatus(
        ChatSession $session,
        object $agentPrompt,
        string $agentSlug,
        string $status,
        string $message,
        array $resultData = [],
        float $score = 0.5,
        int $round = 1,
        int $totalRounds = 1,
        bool $isArgument = false,
    ): void {
        AgentStatusUpdated::dispatch(
            sessionId:   $session->id,
            agentName:   $agentSlug,
            displayName: $agentPrompt->display_name,
            status:      $status,
            message:     $message,
            resultData:  $resultData,
            score:       $score,
            round:       $round,
            totalRounds: $totalRounds,
            isArgument:  $isArgument,
        );
    }

    private function parseAgentVerdict(string $rawContent, string $agentSlug): array
    {
        $cleaned = trim($rawContent);
        
        // Extract <think> block
        $thinking = null;
        if (preg_match('/<think>(.*?)<\/think>/is', $cleaned, $thinkMatches)) {
            $thinking = trim($thinkMatches[1]);
            // Remove <think> block from cleaned content for easier JSON parsing
            $cleaned = preg_replace('/<think>.*?<\/think>/is', '', $cleaned);
        }

        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', $cleaned);
        $cleaned = preg_replace('/\s*```$/', '', $cleaned);
        
        // Remove agent prefix if present e.g. "[Agen Otoritas]:"
        $cleaned = preg_replace('/^\[.*?\]:\s*/i', '', $cleaned);

        if (preg_match('/\{.*\}/s', $cleaned, $matches)) {
            $parsed = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE && isset($parsed['verdict'])) {
                return [
                    'verdict'    => $parsed['verdict'],
                    'confidence' => (float) ($parsed['confidence'] ?? 0.5),
                    'reasoning'  => $parsed['reasoning'] ?? 'Tidak ada penjelasan.',
                    'agent'      => $agentSlug,
                    'thinking'   => $thinking,
                ];
            }
        }

        // Fallback: keyword detection
        $lower   = strtolower($rawContent);
        $verdict = str_contains($lower, 'dosen') ? 'Dosen' : 'Mahasiswa';

        return [
            'verdict'    => $verdict,
            'confidence' => 0.60,
            'reasoning'  => trim($cleaned),
            'agent'      => $agentSlug,
            'thinking'   => $thinking,
        ];
    }

    /**
     * Parse the Judge Agent's response into a final verdict.
     */
    private function parseJudgeVerdict(string $rawContent): array
    {
        $cleaned = trim($rawContent);
        
        $thinking = null;
        if (preg_match('/<think>(.*?)<\/think>/is', $cleaned, $thinkMatches)) {
            $thinking = trim($thinkMatches[1]);
            $cleaned = preg_replace('/<think>.*?<\/think>/is', '', $cleaned);
        }

        $cleaned = preg_replace('/^```(?:json)?\s*/i', '', $cleaned);
        $cleaned = preg_replace('/\s*```$/', '', $cleaned);
        $cleaned = preg_replace('/^\[.*?\]:\s*/i', '', $cleaned);

        if (preg_match('/\{.*\}/s', $cleaned, $matches)) {
            $parsed = json_decode($matches[0], true);
            if (json_last_error() === JSON_ERROR_NONE && isset($parsed['decision'])) {
                return [
                    'decision'   => $parsed['decision'],
                    'confidence' => (float) ($parsed['confidence'] ?? 0.75),
                    'reasoning'  => $parsed['reasoning'] ?? 'Tidak ada kesimpulan.',
                    'thinking'   => $thinking,
                ];
            }
        }

        $lower    = strtolower($rawContent);
        $decision = str_contains($lower, 'dosen') ? 'Dosen' : 'Mahasiswa';

        return [
            'decision'   => $decision,
            'confidence' => 0.75,
            'reasoning'  => trim($cleaned),
            'thinking'   => $thinking,
        ];
    }

    /**
     * Build a structured prompt for the Judge Agent from analyst verdicts.
     */
    private function buildJudgeInput(string $transcription, array $analystVerdicts): string
    {
        $lines = ["TRANSKRIPSI ASLI:\n{$transcription}\n\nLAPORAN AGEN ANALIS:"];

        foreach ($analystVerdicts as $slug => $verdict) {
            $lines[] = "\n[Agen: " . strtoupper($slug) . "]";
            $lines[] = "Verdik: " . ($verdict['verdict'] ?? 'N/A');
            $lines[] = "Confidence: " . round(($verdict['confidence'] ?? 0) * 100) . "%";
            $lines[] = "Alasan: " . ($verdict['reasoning'] ?? 'N/A');
        }

        $lines[] = "\nBerdasarkan laporan di atas, berikan keputusan akhir yang **SINGKAT, PADAT, DAN JELAS**. Format JSON:\n```json\n{\"decision\": \"Mahasiswa|Dosen\", \"confidence\": 0.00, \"reasoning\": \"...\"}\n```";

        return implode("\n", $lines);
    }

    /**
     * Generate 3-5 specific recommendations after the debate is finished.
     */
    private function runRecommendationAgent(ChatSession $session, string $transcription, array $finalVerdict, array $sharedContext): void
    {
        $systemPrompt = "Anda adalah Pakar Linguistik BIMA. Berdasarkan perdebatan dan keputusan akhir yang ada, berikan 3-5 REKOMENDASI perbaikan atau TUJUAN ARAH yang sebenarnya dibahas. 
        Jawaban harus SINGKAT, PADAT, DAN JELAS dalam format list 1-5. Jangan ada basa-basi.";
        
        $userMessage = "Data Transkripsi: {$transcription}\nKeputusan Akhir: {$finalVerdict['decision']}\nAlasan: {$finalVerdict['reasoning']}\n\nBerikan 3-5 rekomendasi/langkah selanjutnya.";

        try {
            $result = $this->kimiApi->complete(
                systemPrompt: $systemPrompt,
                userMessage:  $userMessage,
                extraContext: $sharedContext
            );

            $recommendations = trim($result['content'] ?? '');
            
            // Persist as a message
            Message::create([
                'chat_session_id' => $session->id,
                'role'            => 'assistant',
                'content'         => "### 💡 Rekomendasi & Langkah Selanjutnya:\n" . $recommendations,
                'metadata'        => ['type' => 'recommendations']
            ]);

            // Broadcast status
            AgentStatusUpdated::dispatch(
                sessionId: $session->id,
                agentName: 'recommendation_agent',
                displayName: 'Rekomendasi Ahli',
                status: 'done',
                message: 'Rekomendasi telah disusun.',
                score: 0.5,
                round: 1,
                totalRounds: 1,
                isArgument: true
            );

        } catch (\Throwable $e) {
            Log::error('[BIMA] Failed to generate recommendations: ' . $e->getMessage());
        }
    }

    /**
     * Simple majority vote fallback if judge agent is unavailable.
     */
    private function fallbackMajorityVote(array $analystVerdicts): array
    {
        $counts = ['Mahasiswa' => 0, 'Dosen' => 0];

        foreach ($analystVerdicts as $verdict) {
            $v = $verdict['verdict'] ?? 'Mahasiswa';
            $counts[$v] = ($counts[$v] ?? 0) + 1;
        }

        $decision   = $counts['Dosen'] >= $counts['Mahasiswa'] ? 'Dosen' : 'Mahasiswa';
        $confidence = $counts[$decision] / max(count($analystVerdicts), 1);

        return [
            'decision'   => $decision,
            'confidence' => round($confidence, 2),
            'reasoning'  => 'Keputusan berdasarkan voting mayoritas agen analis (Judge Agent tidak tersedia).',
        ];
    }
}
