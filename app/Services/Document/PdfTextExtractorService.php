<?php

namespace App\Services\Document;

use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PdfTextExtractorService
{
    private Parser $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    /**
     * Ekstraksi teks dari file PDF lokal.
     *
     * @param string $absoluteFilePath
     * @return array{text: string, page_count: int, word_count: int, pages: array<int, string>}
     * @throws RuntimeException
     */
    public function extract(string $absoluteFilePath): array
    {
        if (!file_exists($absoluteFilePath)) {
            throw new RuntimeException("Berkas PDF tidak ditemukan: {$absoluteFilePath}");
        }

        try {
            $pdf = $this->parser->parseFile($absoluteFilePath);
            $pages = $pdf->getPages();
            $pageCount = count($pages);

            $extractedPages = [];
            $fullTextParts = [];

            foreach ($pages as $index => $page) {
                $pageNumber = $index + 1;
                $pageText = trim($page->getText());
                
                // Sanitasi karakter kontrol anomali UTF-8
                $cleanedText = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $pageText);
                
                $extractedPages[$pageNumber] = $cleanedText;
                if (!empty($cleanedText)) {
                    $fullTextParts[] = "--- [Halaman {$pageNumber}] ---\n" . $cleanedText;
                }
            }

            $combinedText = implode("\n\n", $fullTextParts);
            $wordCount = str_word_count(strip_tags($combinedText));

            if (empty(trim($combinedText))) {
                throw new RuntimeException("Dokumen PDF tidak memuat teks digital yang dapat dibaca. Pastikan dokumen bukan pindaian gambar murni (scanned image).");
            }

            return [
                'text'       => $combinedText,
                'page_count' => $pageCount,
                'word_count' => $wordCount,
                'pages'      => $extractedPages,
            ];
        } catch (\Throwable $e) {
            Log::error("[PdfTextExtractorService] Gagal mengekstrak PDF: {$e->getMessage()}", [
                'path' => $absoluteFilePath,
                'trace' => $e->getTraceAsString(),
            ]);

            if ($e instanceof RuntimeException) {
                throw $e;
            }

            throw new RuntimeException("Gagal membaca struktur PDF: " . $e->getMessage());
        }
    }
}
