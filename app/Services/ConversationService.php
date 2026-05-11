<?php

namespace App\Services;

use App\Models\ChatSession;
use Illuminate\Support\Facades\Log;

/**
 * ConversationService
 *
 * Handles all user messages with a two-phase approach:
 *  1. Ask Kimi to decide: respond conversationally OR trigger multi-agent analysis.
 *  2. Act on Kimi's decision.
 *
 * This allows BIMA to be a natural conversational AI that activates
 * the multi-agent workflow only when the AI itself deems it necessary.
 */
class ConversationService
{
    public function __construct(
        private readonly KimiApiService            $kimi,
        private readonly AgentOrchestratorService  $orchestrator,
        private readonly TranscriptionService      $transcription,
    ) {}

    /**
     * Process an incoming user message.
     *
     * @return array{type: 'chat'|'analysis_started', message?: array}
     */
    public function process(ChatSession $session, string $userMessage, array $attachedFiles = []): array
    {
        // ── 1. Persist the user message ──────────────────────────────────────
        $session->messages()->create([
            'role' => 'user', 
            'content' => $userMessage,
            'metadata' => ['attached_files' => $attachedFiles]
        ]);

        // ── 2. Build conversation history for context ─────────────────────
        $history = $session->messages()
            ->orderBy('created_at')
            ->get()
            ->map(function ($m) {
                $content = $m->content;
                if ($m->role === 'user' && !empty($m->metadata['attached_files'])) {
                    $trTexts = collect($m->metadata['attached_files'])->map(function($f) {
                        return "[Audio {$f['name']}]: {$f['transcription']}";
                    })->implode("\n\n");
                    $content = $content ? ($content . "\n\n" . $trTexts) : $trTexts;
                }
                return [
                    'role'    => in_array($m->role, ['user', 'assistant']) ? $m->role : 'user',
                    'content' => $content,
                ];
            })
            ->toArray();

        // ── 3. Send to Kimi with decision-making system prompt ────────────
        $systemPrompt = $this->buildSystemPrompt($session);

        try {
            $rawResponse = $this->kimi->chat(
                messages: array_merge(
                    [['role' => 'system', 'content' => $systemPrompt]],
                    $history
                )
            );
        } catch (\Throwable $e) {
            Log::error('[BIMA ConversationService] Kimi API error: ' . $e->getMessage());
            $errorMsg = $session->messages()->create([
                'role'    => 'assistant',
                'content' => 'Maaf, saya sedang mengalami gangguan koneksi. Silakan coba lagi.',
            ]);
            return ['type' => 'chat', 'message' => $errorMsg];
        }

        // ── 4. Parse Kimi's decision ──────────────────────────────────────
        $decision = $this->parseDecision($rawResponse['content'] ?? '');

        if ($decision['action'] === 'analyze') {
            // Provide a default acknowledgment if Kimi forgot
            $ackText = $decision['response'] ?? 'Baik, saya akan menggunakan AI Workflow untuk menganalisis dokumen/suara Anda berdasarkan kriteria yang ada...';
            
            $ackMessage = $session->messages()->create([
                'role'    => 'assistant',
                'content' => $ackText,
                'metadata'=> [
                    'thinking' => $decision['thinking'] ?? null,
                ],
            ]);

            // Clean the sample text
            $baseSample = $decision['sample'] ?? $userMessage;
            if (!empty($attachedFiles)) {
                $trTexts = collect($attachedFiles)->map(fn($f) => "[Audio {$f['name']}]: {$f['transcription']}")->implode("\n\n");
                $baseSample = $baseSample ? ($baseSample . "\n\n" . $trTexts) : $trTexts;
            }
            $sampleText = $this->transcription->processWebSpeechText($baseSample);

            // Run multi-agent workflow in the background queue
            \App\Jobs\ProcessAgentWorkflow::dispatch($session, $sampleText);

            return ['type' => 'analysis_started', 'message' => $ackMessage];
        }

        // ── 5. Normal conversational response ─────────────────────────────
        $assistantMessage = $session->messages()->create([
            'role'    => 'assistant',
            'content' => $decision['response'] ?? $rawResponse,
            'metadata'=> [
                'thinking' => $decision['thinking'] ?? null,
            ],
        ]);

        return ['type' => 'chat', 'message' => $assistantMessage];
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function buildSystemPrompt(ChatSession $session): string
    {
        $ragContext = '';
        if ($session->document && $session->document->extracted_text) {
            $ragContext = "\n\n## Konteks Dataset Referensi:\n" . $session->document->extracted_text;
        }

        return <<<PROMPT
Anda adalah BIMA (Bahasa Intelligence Multi-Agent), asisten AI riset yang cerdas dan ramah.

**Kepribadian:** Santai, hangat, dan helpfully conversational. Anda bisa membicarakan topik apapun seputar linguistik, akademik, atau sekedar ngobrol santai.

**Kemampuan Utama:** Menganalisis pola ucapan/teks untuk menentukan apakah penuturnya adalah seorang Mahasiswa atau Dosen, berdasarkan kosakata, otoritas akademis, dan gaya bahasa.

**INSTRUKSI PENTING — Selalu balas dalam format JSON berikut (tanpa markdown, tanpa backtick):**

Jika pengguna mengobrol santai, bertanya tentang BIMA, atau belum memberikan sampel ucapan yang jelas:
{"action":"chat","response":"balas conversational Anda di sini"}

Jika pengguna memberikan sampel ucapan/teks yang cukup substansial untuk dianalisis, atau secara eksplisit meminta analisis pola bicara:
{"action":"analyze","response":"Tentu, saya akan mengaktifkan tim agen untuk menganalisis data tersebut. Silakan tunggu sebentar.","sample":"isi teks atau transkripsi audio yang akan dianalisis"}

**Kapan memilih "analyze":**
- Pengguna memberikan sampel ucapan/teks panjang (>7 kata) yang memerlukan penilaian "Mahasiswa atau Dosen".
- Pengguna secara eksplisit meminta BIMA untuk menganalisis suatu ucapan.

**Kapan memilih "chat":**
- Ucapan sangat pendek (sapaan, ucapan terima kasih, konfirmasi singkat seperti "oke", "halo", "terima kasih").
- Pertanyaan umum tentang BIMA atau cara kerja sistem.
- Obrolan santai biasa.
$ragContext
PROMPT;
    }

    private function parseDecision(string $raw): array
    {
        $raw = trim($raw);

        $thinking = null;
        if (preg_match('/<think>(.*?)<\/think>/is', $raw, $thinkMatches)) {
            $thinking = trim($thinkMatches[1]);
            $raw = preg_replace('/<think>.*?<\/think>/is', '', $raw);
        }

        // Strip markdown code fences if Kimi wraps in ```json ... ```
        $raw = preg_replace('/^```(?:json)?\s*/i', '', $raw);
        $raw = preg_replace('/\s*```$/', '', $raw);

        // Extract the first JSON object
        if (preg_match('/\{.*\}/s', $raw, $matches)) {
            $decoded = json_decode($matches[0], true);
            if (is_array($decoded) && isset($decoded['action'])) {
                $decoded['thinking'] = $thinking;
                return $decoded;
            }
        }

        // Fallback: treat entire response as a chat reply
        return ['action' => 'chat', 'response' => trim($raw), 'thinking' => $thinking];
    }
}
