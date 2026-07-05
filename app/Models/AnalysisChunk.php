<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisChunk extends Model
{
    use HasFactory;

    protected $fillable = [
        'analysis_id',
        'chunk_index',
        'total_chunks',
        'chunk_path',
        'chunk_duration_seconds',
        'chunk_size_bytes',
        'status',
        'model_used',
        'prompt_used',
        'raw_response',
        'result_data',
        'error_message',
        'retry_count',
        'duration_ms',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'result_data'             => 'array',
        'started_at'              => 'datetime',
        'completed_at'            => 'datetime',
        'chunk_duration_seconds'  => 'integer',
        'chunk_size_bytes'        => 'integer',
        'duration_ms'             => 'integer',
        'retry_count'             => 'integer',
    ];

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(Analysis::class);
    }

    public function isDone(): bool
    {
        return $this->status === 'done';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    public function getDurationFormattedAttribute(): string
    {
        if (!$this->duration_ms) return '-';
        return number_format($this->duration_ms / 1000, 1) . ' dtk';
    }

    public function getChunkSizeFormattedAttribute(): string
    {
        if (!$this->chunk_size_bytes) return '-';
        return number_format($this->chunk_size_bytes / 1024, 1) . ' KB';
    }
}
