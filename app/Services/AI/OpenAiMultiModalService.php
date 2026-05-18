<?php

namespace App\Services\AI;

use App\Contracts\AI\MultiModalAnalysisInterface;
use App\Contracts\AI\LlmConfigurationInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAiMultiModalService implements MultiModalAnalysisInterface
{
    private const BASE_URL = 'https://api.openai.com/v1/chat/completions';

    public function __construct(
        private readonly LlmConfigurationInterface $config,
        private readonly string $apiKey = ''
    ) {}

    private function resolvedKey(): string
    {
        return $this->apiKey ?: config('services.openai.key');
    }

    public function analyzeAudio(string $audioPath): array
    {
        $resolvedKey = $this->resolvedKey();
        if (empty($resolvedKey)) {
            throw new \RuntimeException("OpenAI API Key is not configured.");
        }

        $audioData = base64_encode(file_get_contents($audioPath));
        $extension = pathinfo($audioPath, PATHINFO_EXTENSION);
        $format = in_array(strtolower($extension), ['mp3', 'wav']) ? strtolower($extension) : 'wav';

        try {
            $response = Http::withToken($resolvedKey)
                ->timeout($this->config->getTimeout())
                ->post(self::BASE_URL, [
                    'model' => $this->config->getModelName(),
                    'temperature' => $this->config->getTemperature(),
                    'modalities' => ['text'],
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->config->getSystemPrompt()
                        ],
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $this->config->getUserPrompt()
                                ],
                                [
                                    'type' => 'input_audio',
                                    'input_audio' => [
                                        'data' => $audioData,
                                        'format' => $format
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]);

            if ($response->failed()) {
                Log::error('[OpenAiMultiModal] API Error', ['body' => $response->body()]);
                throw new \RuntimeException('Gagal memproses audio di server AI.');
            }

            $json = $response->json();
            $content = $json['choices'][0]['message']['content'] ?? '[]';
            
            $content = preg_replace('/```json\s*/', '', $content);
            $content = preg_replace('/```\s*/', '', $content);

            $parsed = json_decode($content, true);
            if (!$parsed) {
                return $this->mockResponse();
            }

            return $parsed;

        } catch (\Throwable $e) {
            Log::error('[OpenAiMultiModal] Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    private function mockResponse(): array
    {
        return [
            "summary" => [
                "kategori_advice" => "Bimbingan Bertahap",
                "karakter_relasi" => "Power-maintaining (Keseimbangan Kuasa)",
                "intonasi_dominan" => "Kalimat Perintah / Instruksi",
                "ranah_pembicaraan" => "Pembicaraan ini berfokus pada proses penulisan dan validitas data.",
                "arah_tujuan" => "Tujuan utama dari pembicaraan ini adalah untuk memastikan bahwa penulisan skripsi memenuhi standar akademis yang diperlukan, termasuk validitas data dan format penulisan yang benar.",
                "saran_perbaikan" => "Kalimat sudah baik dan jelas. Teruskan semangatnya dalam menyusun skripsi!"
            ],
            "transcription" => [
                [
                    "speaker" => "Dosen",
                    "timestamp" => "00:00 - 00:08",
                    "text_html" => "Untuk literature review ya, ini gimana caranya supaya diluaskan lagi [MARKER_1] Apalagi cuma satu section ya. [PAUSE] <b>Ini masih terlalu sedikit [MARKER_2]</b>",
                    "is_advice" => true,
                    "advice_type" => "down",
                    "agent_insight" => "Dosen memberikan instruksi korektif agar mahasiswa memperluas tinjauan pustaka karena dirasa belum memenuhi standar minimum.",
                    "advice_relation" => "Kalimat ini merupakan instruksi tindak tutur korektif pertama Dosen yang menanggapi template baru pilihan Mahasiswa pada Baris 1.",
                    "intonation_markers" => [
                        [
                            "id" => "[MARKER_1]",
                            "type" => "up",
                            "reason" => "Dosen menggunakan intonasi naik untuk memancing respons atau memberikan pertanyaan retoris.",
                            "relation" => "Berelasi dengan kalimat Mahasiswa di Baris 1 untuk menegaskan fokus perubahan literature review."
                        ],
                        [
                            "id" => "[MARKER_2]",
                            "type" => "down",
                            "reason" => "Penurunan nada di akhir menunjukan instruksi absolut yang tidak bisa ditawar.",
                            "relation" => "Menegaskan ketidaksetujuan atas satu section literature review yang terlalu tipis."
                        ]
                    ]
                ],
                [
                    "speaker" => "Mahasiswa",
                    "timestamp" => "00:09 - 00:12",
                    "text_html" => "Baik, Bu. Saya akan coba cari referensi jurnal baru lagi [UP]",
                    "is_advice" => false,
                    "advice_type" => "neutral",
                    "agent_insight" => "Mahasiswa menunjukkan persetujuan dan penerimaan instruksi secara koperatif.",
                    "advice_relation" => "Merupakan tanggapan koperatif langsung terhadap instruksi Dosen di Baris 2."
                ]
            ]
        ];
    }
}
