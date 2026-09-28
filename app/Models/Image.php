<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'type',
        'order',
        'imageable_type',
        'imageable_id',
    ];

    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
