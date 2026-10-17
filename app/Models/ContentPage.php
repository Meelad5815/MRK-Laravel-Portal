<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentPage extends Model
{
    protected $fillable = [
        'title', 'slug', 'category', 'excerpt', 'content', 'status',
        'featured', 'seo_title', 'seo_description',
    ];

    protected $casts = [
        'featured' => 'boolean',
    ];
}
