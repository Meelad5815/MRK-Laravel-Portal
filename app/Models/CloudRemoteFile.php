<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CloudRemoteFile extends Model
{
    protected $fillable = [
        'user_id', 'cloud_connection_id', 'provider', 'provider_file_id',
        'name', 'mime_type', 'size', 'category', 'provider_modified_at',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'provider_modified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(CloudConnection::class, 'cloud_connection_id');
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
