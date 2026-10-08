<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'summary',
        'description',
        'category',
        'technologies',
        'project_url',
        'status',
        'featured',
        'completed_at',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'completed_at' => 'date',
    ];
}
