<?php

namespace App\Actions;

/**
 * ParseAdviceGivingAction
 * 
 * Extracts and formats the advice-giving elements from the raw multimodal AI output.
 * Adheres to the Single Responsibility Principle as an Action class.
 */
class ParseAdviceGivingAction
{
    public function execute(array $rawAiResponse): array
    {
        $processedBlocks = [];
        $transcription = $rawAiResponse['transcription'] ?? [];

        foreach ($transcription as $block) {
            if (!is_array($block)) continue;
            $text = $block['text_html'] ?? '';
            $isAdvice = $block['is_advice'] ?? false;
            if ($isAdvice && str_contains($text, '<b>')) {
                $block['advice_confidence'] = 'high';
            }

            // Process new intonation markers
            $markers = $block['intonation_markers'] ?? [];
            if (is_array($markers)) {
                foreach ($markers as $marker) {
                    if (!is_array($marker)) continue;
                    $id = $marker['id'] ?? '';
                    $type = $marker['type'] ?? 'up';
                    $reason = $marker['reason'] ?? '';

                    if (empty($id)) continue;

                    $iconHtml = '';
                    if ($type === 'up') {
                        $iconHtml = '<svg class="inline-block w-4 h-4 text-blue-500 mx-1 align-middle hover:scale-125 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>';
                    } else if ($type === 'down') {
                        $iconHtml = '<svg class="inline-block w-4 h-4 text-red-500 mx-1 align-middle hover:scale-125 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>';
                    } else {
                        $iconHtml = '<svg class="inline-block w-4 h-4 text-gray-400 mx-1 align-middle hover:scale-125 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 12h14"></path></svg>';
                    }

                    $escapedReason = htmlspecialchars($reason, ENT_QUOTES);
                    $escapedRelation = htmlspecialchars($marker['relation'] ?? 'Tidak ada relasi khusus dengan baris lain.', ENT_QUOTES);
                    $markerTitle = 'Intonasi ' . ($type === 'up' ? 'Naik (High pitch)' : ($type === 'down' ? 'Turun (Low pitch)' : 'Netral'));

                    $tooltipHtml = "<button @click=\"openInsight('{$markerTitle}', '{$type}', '{$escapedReason}', '{$escapedRelation}')\" class=\"inline-flex items-center justify-center p-0.5 hover:bg-gray-100 border border-transparent hover:border-gray-200 rounded-lg transition-all focus:outline-none cursor-pointer align-middle\" title=\"Klik untuk melihat detail intonasi & relasi\">{$iconHtml}</button>";

                    // Support replacing both bracketed and plain format IDs to prevent UI leakage
                    $cleanId = trim($id, '[]');
                    $idWithBrackets = '[' . $cleanId . ']';
                    $idWithoutBrackets = $cleanId;

                    $textHtml = $block['text_html'] ?? '';
                    $textHtml = str_replace($idWithBrackets, $tooltipHtml, $textHtml);
                    
                    if (preg_match('/MARKER_\d+$/i', $idWithoutBrackets)) {
                        $textHtml = str_replace($idWithoutBrackets, $tooltipHtml, $textHtml);
                    }
                    
                    $block['text_html'] = $textHtml;
                }
            }

            // Replace standard pause markers
            $block['text_html'] = str_replace(
                '[PAUSE]', 
                '<span title="Jeda Pembicaraan"><svg class="inline-block w-4 h-4 text-gray-400 mx-1 align-middle" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></span>', 
                $block['text_html'] ?? ''
            );

            // Clean up any remaining unmatched marker tags and empty brackets to prevent format leaks in UI
            if (isset($block['text_html'])) {
                $block['text_html'] = preg_replace('/\[MARKER_\d+\]/i', '', $block['text_html']);
                $block['text_html'] = preg_replace('/\[\s*\]/', '', $block['text_html']);
            }

            $processedBlocks[] = $block;
        }

        return [
            'summary' => $rawAiResponse['summary'] ?? [],
            'transcription' => $processedBlocks
        ];
    }
}
