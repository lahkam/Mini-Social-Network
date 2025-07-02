<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    public function sentInvitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'inviter_id');
    }

    public function receivedInvitations(): HasMany
    {
        return $this->hasMany(Invitation::class, 'invitee_id');
    }

    public function grantedAccessTo(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_access', 'content_owner_id', 'viewer_id');
    }

    public function canViewPostsFrom(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_access', 'viewer_id', 'content_owner_id');
    }

    public function pendingInvitations(): HasMany
    {
        return $this->receivedInvitations()->where('status', 'pending');
    }
}
