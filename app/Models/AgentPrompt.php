<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentPrompt extends Model
{
    use HasFactory;

    protected $fillable = [
        'agent_name',
        'display_name',
        'system_prompt',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getActivePrompt(string $agentName): ?self
    {
        return static::where('agent_name', $agentName)->where('is_active', true)->first();
    }
}
