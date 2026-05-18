<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SegmentAnalysisLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_session_id',
        'segment_id',
        'step_name',
        'sub_step',
        'status',
        'process_detail',
        'input_summary',
        'result_summary',
        'result_data',
        'duration_ms',
    ];

    protected $casts = [
        'result_data' => 'array',
        'duration_ms' => 'integer',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    public function segment(): BelongsTo
    {
        return $this->belongsTo(TranscriptSegment::class, 'segment_id');
    }
}
