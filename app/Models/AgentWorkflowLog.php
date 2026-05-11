<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentWorkflowLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_session_id',
        'agent_name',
        'status',
        'process_note',
        'result_data',
    ];

    protected $casts = [
        'result_data' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function chatSession(): BelongsTo
    {
        return $this->belongsTo(ChatSession::class);
    }
}
