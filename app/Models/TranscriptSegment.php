<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TranscriptSegment extends Model
{
    use HasFactory, HasUuids;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'chat_session_id',
        'segment_index',
        'speaker',
        'start_time',
        'end_time',
        'text',
        'topic',
        'dialogue_act',
        'power_marker',
        'advice_category',
        'intonation',
        'discourse_markers',
        'sentiment',
        'raw_json',
    ];

    protected $casts = [
        'start_time'         => 'float',
        'end_time'           => 'float',
        'segment_index'      => 'integer',
        'discourse_markers'  => 'array',
        'raw_json'           => 'array',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
    ];

    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }

    public function analysisLogs(): HasMany
    {
        return $this->hasMany(SegmentAnalysisLog::class, 'segment_id');
    }
}
