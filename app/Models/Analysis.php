<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Analysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'title',
        'locale',
        'audio_path',
        'duration_seconds',
        'audio_duration_seconds',
        'total_chunks',
        'processed_chunks',
        'status',
        'model_used',
        'synthesis_model',
        'result_data',
        'metrics',
    ];

    protected $casts = [
        'result_data' => 'array',
        'metrics'     => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            do {
                $slug = rtrim(strtr(base64_encode(random_bytes(9)), '+/', '-_'), '=');
            } while (static::where('slug', $slug)->exists()); // Pastikan unik

            $model->slug = $slug;
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(AnalysisFeedback::class);
    }

    public function chunks(): HasMany
    {
        return $this->hasMany(AnalysisChunk::class)->orderBy('chunk_index');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AnalysisLog::class)->orderBy('created_at');
    }

    public function getProgressPercentAttribute(): int
    {
        if (!$this->total_chunks) return 0;
        return (int) round(($this->processed_chunks / $this->total_chunks) * 100);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isProcessing(): bool
    {
        return in_array($this->status, ['processing', 'synthesizing', 'uploaded']);
    }

    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'partial_failure']);
    }

    public function isResumable(): bool
    {
        return $this->chunks()->where('status', 'failed')->exists();
    }
}
