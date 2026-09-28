<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'tax_percent',
        'commission_income',
        'pts_base',
        'pts_bonus',
        'pts_dist',
        'maximum_discount',
        'specifications',
        'information',
        'is_physical',
        'stock',
        'allow_backorder',
        'category_id',
        'brand_id',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'tax_percent' => 'decimal:2',
            'final_price' => 'decimal:2',
            'commission_income' => 'integer',
            'pts_base' => 'decimal:2',
            'pts_bonus' => 'decimal:2',
            'pts_dist' => 'decimal:2',
            'maximum_discount' => 'integer',
            'is_physical' => 'boolean',
            'stock' => 'integer',
            'allow_backorder' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('order');
    }

    public function latestImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->latestOfMany();
    }

    public function oldestImage(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable')->oldestOfMany();
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isAvailable(): bool
    {
        if (! $this->is_physical) {
            return true;
        }

        return $this->stock > 0 || $this->allow_backorder;
    }

    public static function generateSlug(string $name): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $i = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::saving(function (self $model): void {
            if (empty($model->slug)) {
                $model->slug = static::generateSlug($model->name);
            }
        });
    }
}
