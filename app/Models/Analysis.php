<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'audio_path',
        'duration_seconds',
        'status',
        'result_data',
        'metrics',
    ];

    protected $casts = [
        'result_data' => 'array',
        'metrics' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function feedback()
    {
        return $this->hasOne(AnalysisFeedback::class);
    }
}
