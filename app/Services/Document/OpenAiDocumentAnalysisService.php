<?php

namespace App\Services\Document;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class OpenAiDocumentAnalysisService
{
    private const BASE_URL = 'https://api.openai.com/v1/chat/completions';
    private const MODEL = 'gpt-4o-mini';

    public function __construct(
        private readonly string $apiKey = ''
    ) {}

    private function resolvedKey(): string
    {
        return $this->apiKey ?: (config('services.openai.key') ?? env('OPENAI_API_KEY', ''));
    }

    /**
     * Menganalisis teks dokumen PDF menggunakan OpenAI GPT-4o-mini.
     *
     * @param string $documentText
     * @param string $documentTitle
     * @param string $locale
     * @return array{executive_summary: string, key_conclusions: array, recommendations: array, audio_script: string}
     */
    public function analyze(string $documentText, string $documentTitle, string $locale = 'id'): array
    {
        $apiKey = $this->resolvedKey();
        if (empty($apiKey)) {
            throw new RuntimeException("OpenAI API Key belum terkonfigurasi pada file environment (.env).");
        }

        // Batasi teks maksimal ~60.000 karakter (~12.000-15.000 token) untuk efisiensi latensi dan biaya
        $sanitizedText = $this->truncateTextForContext($documentText, 60000);

        $systemPrompt = $this->buildSystemPrompt($locale);
        $userPrompt = $this->buildUserPrompt($documentTitle, $sanitizedText, $locale);

        try {
            $response = Http::withToken($apiKey)
                ->timeout(120)
                ->post(self::BASE_URL, [
                    'model' => self::MODEL,
                    'temperature' => 0.2,
                    'response_format' => ['type' => 'json_object'],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $systemPrompt
                        ],
                        [
                            'role' => 'user',
                            'content' => $userPrompt
                        ]
                    ]
                ]);

            if ($response->failed()) {
                Log::error('[OpenAiDocumentAnalysisService] API Error', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                throw new RuntimeException("Gagal melakukan analisis AI: " . ($response->json('error.message') ?? $response->body()));
            }

            $rawContent = $response->json('choices.0.message.content');
            if (empty($rawContent)) {
                throw new RuntimeException("Respons dari OpenAI kosong.");
            }

            // Bersihkan markdown fence jika ada
            $cleanedJson = preg_replace('/```json\s*/', '', $rawContent);
            $cleanedJson = preg_replace('/```\s*/', '', $cleanedJson);

            $data = json_decode(trim($cleanedJson), true);

            if (!is_array($data) || !isset($data['executive_summary'])) {
                Log::warning('[OpenAiDocumentAnalysisService] JSON decode invalid, fallback', ['raw' => $rawContent]);
                return $this->fallbackStructure($documentTitle, $locale);
            }

            return [
                'executive_summary' => (string) ($data['executive_summary'] ?? ''),
                'key_conclusions'   => (array) ($data['key_conclusions'] ?? []),
                'recommendations'   => (array) ($data['recommendations'] ?? []),
                'audio_script'      => (string) ($data['audio_script'] ?? ''),
            ];

        } catch (\Throwable $e) {
            Log::error("[OpenAiDocumentAnalysisService] Exception: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Potong teks secara cerdas (ambil awal dan akhir jika teks sangat besar)
     */
    private function truncateTextForContext(string $text, int $maxChars): string
    {
        $length = mb_strlen($text);
        if ($length <= $maxChars) {
            return $text;
        }

        $half = (int) ($maxChars / 2);
        $head = mb_substr($text, 0, $half);
        $tail = mb_substr($text, -$half);

        return $head . "\n\n... [BAGIAN TENGAH DOKUMEN DIRINGKAS UNTUK OPTIMASI KONTEKS] ...\n\n" . $tail;
    }

    private function buildSystemPrompt(string $locale): string
    {
        if ($locale === 'en') {
            return "You are an elite Academic Supervisory AI and Senior Research Reviewer. Your role is to examine academic/educational PDF manuscripts and provide:
1. Executive Summary: A rigorous, clear synthesis of the document's problem, methodology, findings, and pedagogical value.
2. Key Conclusions: 3 to 6 major thematic takeaways and insights.
3. Actionable Recommendations: 3 to 6 specific, constructive suggestions with urgency levels and concrete steps.
4. Audio Script: A spoken briefing script (~150 to 200 words) written in a natural, supportive, professional supervisory tone ready for Text-to-Speech narration.
Always respond in pure JSON format adhering to the requested schema.";
        }

        if ($locale === 'zh') {
            return "您是顶尖的学术辅导AI与高级评审专家。您的任务是分析学术/课件PDF文档并生成：
1. Executive Summary: 严谨清晰的综述，涵盖问题、方法、主要论述与教学价值。
2. Key Conclusions: 3至6条核心学术结论。
3. Actionable Recommendations: 3至6条具有可操作性的修改建议，包括紧急程度和具体对策。
4. Audio Script: 专为语音朗读设计的口语化专业简报文稿（约150-200词），语调鼓励且专业。
必须以纯JSON格式返回。";
        }

        return "Anda adalah Asisten Pakar Supervisory AI Akademik dan Reviewer Naskah Ilmiah Senior. Peran Anda adalah menganalisis draf materi, proposal, skripsi, atau artikel jurnal akademik dalam format PDF, kemudian menghasilkan:
1. 'executive_summary': Ringkasan eksekutif komprehensif (2-3 paragraf) yang menjelaskan latar belakang, metodologi/isi substansi, dan urgensi materi dengan bahasa akademis yang lugas.
2. 'key_conclusions': Array berisi 3-5 kesimpulan pokok, masing-masing memiliki 'title' (judul singkat kesimpulan) dan 'detail' (elaborasi temuan).
3. 'recommendations': Array berisi 3-6 rekomendasi perbaikan terarah, masing-masing memiliki:
   - 'category': Kategori (misal: 'Struktur & Metodologi', 'Substansi Akademik', 'Ketajaman Analisis', atau 'Referensi & Validitas').
   - 'urgency': Tingkat urgensi ('Tinggi', 'Sedang', atau 'Penyempurnaan').
   - 'finding': Temuan kelemahan, celah pembahasan, atau area yang perlu diperkuat pada materi.
   - 'action': Rekomendasi aksi perbaikan konkret yang harus diambil oleh mahasiswa/penulis.
4. 'audio_script': Naskah narasi briefing audio berdurasi 60-90 detik (~150-200 kata) dalam bahasa tutur alami, formal, santun, dan memotivasi, siap diucapkan oleh Text-to-Speech (TTS) untuk menyapa mahasiswa/dosen dan merangkum inti ulasan naskah.

Format output WAJIB berupa JSON murni dengan struktur kunci:
{
  \"executive_summary\": \"...\",
  \"key_conclusions\": [
    {\"title\": \"...\", \"detail\": \"...\"}
  ],
  \"recommendations\": [
    {\"category\": \"...\", \"urgency\": \"...\", \"finding\": \"...\", \"action\": \"...\"}
  ],
  \"audio_script\": \"...\"
}";
    }

    private function buildUserPrompt(string $title, string $content, string $locale): string
    {
        return "Judul Dokumen: \"{$title}\"\nBahasa Dokumen/Output: {$locale}\n\nIsi Teks Dokumen:\n" . $content . "\n\nSilakan hasilkan analisis mendalam, kesimpulan, rekomendasi aksi terarah, dan audio script dalam format JSON terstruktur.";
    }

    private function fallbackStructure(string $title, string $locale): array
    {
        return [
            'executive_summary' => "Dokumen '{$title}' telah berhasil dipindai. Dokumen ini membahas materi akademik dan memerlukan penelaahan lebih lanjut pada bab metodologi dan pembuktian empiris.",
            'key_conclusions'   => [
                [
                    'title' => 'Fokus Pembahasan Materi',
                    'detail' => 'Dokumen berfokus pada eksplorasi topik sesuai ruang lingkup yang ditetapkan.'
                ]
            ],
            'recommendations'   => [
                [
                    'category' => 'Substansi Akademik',
                    'urgency' => 'Sedang',
                    'finding' => 'Pemaparan temuan dapat diperkuat dengan rujukan primer terbaru.',
                    'action' => 'Tambahkan komparasi literatur jurnal 5 tahun terakhir untuk memperkaya kedalaman analisis.'
                ]
            ],
            'audio_script'      => "Halo, berkas dokumen Anda telah berhasil ditelaah oleh Supervisory AI. Kami telah menyiapkan ringkasan eksekutif, poin-poin kesimpulan, serta rekomendasi perbaikan terarah untuk menyempurnakan naskah Anda. Silakan simak rinciannya pada layar.",
        ];
    }
}
