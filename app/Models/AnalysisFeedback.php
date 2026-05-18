<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalysisFeedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'analysis_id',
        'user_id',
        'is_accurate',
        'comments',
    ];

    protected $casts = [
        'is_accurate' => 'boolean',
    ];

    public function analysis()
    {
        return $this->belongsTo(Analysis::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
