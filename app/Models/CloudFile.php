<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CloudFile extends Model
{
    protected $fillable = [
        'user_id', 'cloud_folder_id', 'category', 'original_name', 'stored_path', 'mime_type', 'extension', 'size',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(CloudFolder::class, 'cloud_folder_id');
    }

    public function getSizeForHumansAttribute(): string
    {
        $size = (float) $this->size;
        foreach (['B', 'KB', 'MB', 'GB'] as $unit) {
            if ($size < 1024 || $unit === 'GB') {
                return number_format($size, $unit === 'B' ? 0 : 1).' '.$unit;
            }
            $size /= 1024;
        }
        return number_format($size, 1).' GB';
    }
}
