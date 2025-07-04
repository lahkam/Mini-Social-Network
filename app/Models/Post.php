<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'content',
        'type',
        'ai_prompt'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isAiGenerated(): bool
    {
        return $this->type === 'ai_generated';
    }

    public function scopeVisibleTo($query, User $viewer)
    {
        return $query->whereHas('user', function ($userQuery) use ($viewer) {
            $userQuery->where('id', $viewer->id)
                ->orWhereHas('grantedAccessTo', function ($accessQuery) use ($viewer) {
                    $accessQuery->where('viewer_id', $viewer->id);
                });
        });
    }
}
