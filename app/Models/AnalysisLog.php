<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'analysis_id',
        'event',
        'status',
        'message',
        'detail',
        'duration_ms',
        'created_at',
    ];

    protected $casts = [
        'detail'     => 'array',
        'duration_ms' => 'integer',
        'created_at' => 'datetime',
    ];

    public function analysis(): BelongsTo
    {
        return $this->belongsTo(Analysis::class);
    }

    public static function record(
        int $analysisId,
        string $event,
        string $status = 'info',
        string $message = '',
        array $detail = [],
        ?int $durationMs = null
    ): self {
        return static::create([
            'analysis_id' => $analysisId,
            'event'       => $event,
            'status'      => $status,
            'message'     => $message,
            'detail'      => $detail ?: null,
            'duration_ms' => $durationMs,
            'created_at'  => now(),
        ]);
    }

    public static function info(int $analysisId, string $event, string $message, array $detail = []): self
    {
        return static::record($analysisId, $event, 'info', $message, $detail);
    }

    public static function success(int $analysisId, string $event, string $message, array $detail = [], ?int $durationMs = null): self
    {
        return static::record($analysisId, $event, 'success', $message, $detail, $durationMs);
    }

    public static function warning(int $analysisId, string $event, string $message, array $detail = []): self
    {
        return static::record($analysisId, $event, 'warning', $message, $detail);
    }

    public static function error(int $analysisId, string $event, string $message, array $detail = []): self
    {
        return static::record($analysisId, $event, 'error', $message, $detail);
    }
}
