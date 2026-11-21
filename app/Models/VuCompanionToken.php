<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VuCompanionToken extends Model
{
    protected $fillable = [
        'user_id',
        'token_hash',
        'label',
        'last_used_at',
        'expires_at',
        'revoked_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];
}
