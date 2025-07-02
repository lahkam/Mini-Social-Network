<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model
{
    use HasFactory;

     protected $fillable = [
        'nom',
        'log',
        'pass',
    ];


public function invitationsSent()
{
    return $this->hasMany(Invitation::class, 'inviter_id');
}

public function invitationsReceived()
{
    return $this->hasMany(Invitation::class, 'invitee_id');
}
}
