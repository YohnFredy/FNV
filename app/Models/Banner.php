<?php

namespace App\Models;

use Database\Factories\BannerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    /** @use HasFactory<BannerFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'image_path',
        'link_url',
        'order',
        'is_active',
        'location',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * Get full storage URL for the banner image.
     */
    public function getImageUrlAttribute(): string
    {
        if (! $this->image_path) {
            return '';
        }

        return asset('storage/'.$this->image_path);
    }
}
