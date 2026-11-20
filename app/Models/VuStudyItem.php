<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VuStudyItem extends Model
{
    protected $fillable = [
        'user_id', 'course_code', 'course_title', 'item_type', 'title',
        'description', 'source_url', 'opens_at', 'due_at', 'status',
        'source_key', 'metadata', 'last_synced_at',
    ];

    protected $casts = [
        'opens_at' => 'datetime',
        'due_at' => 'datetime',
        'last_synced_at' => 'datetime',
        'metadata' => 'array',
    ];
}