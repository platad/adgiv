<?php

namespace App\Http\Controllers;

use App\Models\Analysis;
use App\Models\TranscriptLineFeedback;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LineFeedbackController extends Controller
{
    /**
     * Simpan atau update feedback (like/dislike) untuk satu baris transcript.
     * Jika diklik dua kali pada nilai yang sama, feedback di-toggle (dihapus).
     */
    public function store(Request $request, Analysis $analysis): JsonResponse
    {
        $validated = $request->validate([
            'line_index' => ['required', 'integer', 'min:0'],
            'speaker'    => ['nullable', 'string', 'max:100'],
            'text'       => ['required', 'string', 'max:2000'],
            'feedback'   => ['required', 'in:like,dislike'],
        ]);

        $userId = Auth::id();

        $existing = TranscriptLineFeedback::where([
            'analysis_id' => $analysis->id,
            'user_id'     => $userId,
            'line_index'  => $validated['line_index'],
        ])->first();

        // Toggle: jika nilai sama di-klik lagi, hapus feedback-nya
        if ($existing && $existing->feedback === $validated['feedback']) {
            $existing->delete();
            return response()->json([
                'status'   => 'removed',
                'feedback' => null,
                'likes'    => $this->countFeedback($analysis->id, $validated['line_index'], 'like'),
                'dislikes' => $this->countFeedback($analysis->id, $validated['line_index'], 'dislike'),
            ]);
        }

        // Upsert: buat atau update
        $record = TranscriptLineFeedback::updateOrCreate(
            [
                'analysis_id' => $analysis->id,
                'user_id'     => $userId,
                'line_index'  => $validated['line_index'],
            ],
            [
                'speaker'  => $validated['speaker'] ?? null,
                'text'     => $validated['text'],
                'feedback' => $validated['feedback'],
            ]
        );

        return response()->json([
            'status'   => 'saved',
            'feedback' => $record->feedback,
            'likes'    => $this->countFeedback($analysis->id, $validated['line_index'], 'like'),
            'dislikes' => $this->countFeedback($analysis->id, $validated['line_index'], 'dislike'),
        ]);
    }

    private function countFeedback(int $analysisId, int $lineIndex, string $type): int
    {
        return TranscriptLineFeedback::where([
            'analysis_id' => $analysisId,
            'line_index'  => $lineIndex,
            'feedback'    => $type,
        ])->count();
    }
}
