<?php

namespace App\Services;

use App\Events\DocumentProcessed;
use App\Models\Document;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * DocumentService
 *
 * Responsibility: Handle CSV file upload, text extraction, and persistence.
 * Emits DocumentProcessed event upon completion.
 */
class DocumentService
{
    /**
     * Upload a CSV file, extract its content, and persist metadata.
     */
    public function uploadAndProcess(UploadedFile $file, User $user): Document
    {
        $filename  = $file->getClientOriginalName();
        $safeName  = Str::slug(pathinfo($filename, PATHINFO_FILENAME)) . '_' . time() . '.csv';
        $filePath  = $file->storeAs('documents', $safeName, 'local');

        /** @var Document $document */
        $document = Document::create([
            'user_id'   => $user->id,
            'filename'  => $filename,
            'file_path' => $filePath,
            'status'    => 'processing',
        ]);

        try {
            $extractedText = $this->extractTextFromCsv(Storage::disk('local')->path($filePath));
            $summary       = $this->buildSummary($extractedText);

            $document->update([
                'extracted_text' => $extractedText,
                'summary'        => $summary,
                'status'         => 'processed',
            ]);

            DocumentProcessed::dispatch(
                documentId: $document->id,
                userId: $user->id,
                filename: $filename,
                status: 'processed',
                summary: $summary,
            );
        } catch (\Throwable $e) {
            Log::error('[DocumentService] Failed to extract CSV.', [
                'document_id' => $document->id,
                'error'       => $e->getMessage(),
            ]);
            $document->update(['status' => 'failed']);

            DocumentProcessed::dispatch(
                documentId: $document->id,
                userId: $user->id,
                filename: $filename,
                status: 'failed',
            );
        }

        return $document->fresh();
    }

    /**
     * Read CSV file and return all content as a structured text block.
     */
    public function extractTextFromCsv(string $absolutePath): string
    {
        if (! file_exists($absolutePath)) {
            throw new \RuntimeException("CSV file not found at path: {$absolutePath}");
        }

        $handle = fopen($absolutePath, 'r');
        if ($handle === false) {
            throw new \RuntimeException("Unable to open CSV file.");
        }

        $lines  = [];
        $header = null;

        while (($row = fgetcsv($handle, 0, ',')) !== false) {
            if ($header === null) {
                $header  = $row;
                $lines[] = implode(' | ', $row); // Header row
                continue;
            }

            $mapped  = array_combine($header, $row);
            $lines[] = implode(' | ', array_map(
                fn ($k, $v) => "{$k}: {$v}",
                array_keys($mapped),
                array_values($mapped)
            ));
        }

        fclose($handle);

        return implode("\n", $lines);
    }

    /**
     * Generate a human-readable summary of the CSV content.
     */
    private function buildSummary(string $text): string
    {
        $lines     = explode("\n", trim($text));
        $totalRows = count($lines) - 1; // Exclude header

        return "Dataset memiliki {$totalRows} entri. " .
               "Kolom: " . $lines[0] . ". " .
               "Data ini akan dijadikan konteks oleh para Agen AI untuk analisis.";
    }

    /**
     * Retrieve extracted text for a given document (used by RAG context building).
     */
    public function getExtractedText(Document $document): string
    {
        if ($document->extracted_text) {
            return $document->extracted_text;
        }

        // Fallback: re-extract if somehow missing
        $path = Storage::disk('local')->path($document->file_path);

        return $this->extractTextFromCsv($path);
    }
}
