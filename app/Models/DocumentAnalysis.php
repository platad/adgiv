<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'slug',
        'title',
        'locale',
        'pdf_path',
        'file_name',
        'file_size',
        'total_pages',
        'word_count',
        'status',
        'error_message',
        'executive_summary',
        'key_conclusions',
        'recommendations',
        'audio_script',
        'briefing_audio_path',
    ];

    protected $casts = [
        'key_conclusions' => 'array',
        'recommendations' => 'array',
        'total_pages'     => 'integer',
        'word_count'      => 'integer',
        'file_size'       => 'integer',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            do {
                $slug = 'doc_' . rtrim(strtr(base64_encode(random_bytes(8)), '+/', '-_'), '=');
            } while (static::where('slug', $slug)->exists());

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

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function formattedFileSize(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' bytes';
    }
}
