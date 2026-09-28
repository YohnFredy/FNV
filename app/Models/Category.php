<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'is_final',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_final' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->where('is_active', true);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Obtiene la ruta completa de la categoría (Padre > Hijo).
     */
    public function getFullPathAttribute(): string
    {
        $path = [$this->name];
        $category = $this;

        while ($category->parent) {
            $path[] = $category->parent->name;
            $category = $category->parent;
        }

        return implode(' > ', array_reverse($path));
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
