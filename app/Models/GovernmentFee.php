<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Model;

class GovernmentFee extends Model
{
    protected $fillable = [
        'department','category','service_name','priority','government_fee',
        'mrk_service_fee','currency','official_source_url','verification_status',
        'source_hash','source_note','last_checked_at','last_verified_at','is_active',
    ];

    protected $casts = [
        'government_fee' => 'decimal:2',
        'mrk_service_fee' => 'decimal:2',
        'last_checked_at' => 'datetime',
        'last_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];
}
