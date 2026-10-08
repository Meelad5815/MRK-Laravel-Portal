<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'title','slug','summary','description','category','icon',
        'status','featured','sort_order','cta_label','cta_url',
        'seo_title','seo_description',
    ];

    protected $casts = [
        'featured' => 'boolean',
        'sort_order' => 'integer',
    ];
}
