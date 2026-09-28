<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'nit',
        'user_id',
        'minimum_percentage',
        'maximum_percentage',
        'email',
        'password',
        'is_active',
    ];

    protected $touches = ['data'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relación polimórfica genérica
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    // RECOMENDACIÓN: Relación específica para logos
    public function logos()
    {
        return $this->morphMany(Image::class, 'imageable')->where('type', 'logo')->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    public function latestLogo()
    {
        return $this->morphOne(Image::class, 'imageable')->ofMany(['id' => 'max'], function ($query) {
            $query->where('type', 'logo');
        });
    }

    // RECOMENDACIÓN: Relación específica para imágenes de galería
    public function galleryImages()
    {
        // Renombramos para evitar confusión con la relación genérica "images"
        return $this->morphMany(Image::class, 'imageable')->where('type', 'image')->orderBy('order', 'asc')->orderBy('id', 'asc');
    }

    public function businessCategories()
    {
        return $this->belongsToMany(
            BusinessCategory::class,
            'business_business_category',
            'business_id',
            'business_category_id'
        );
    }

    public function categories(): BelongsToMany
    {
        // Usamos "categories" como nombre del método para que sea más natural de leer ($business->categories)
        return $this->belongsToMany(BusinessCategory::class, 'business_business_category');
    }

    public function data(): HasMany
    {
        return $this->hasMany(BusinessData::class);
    }

    public function promotions(): HasMany
    {
        return $this->hasMany(BusinessPromotion::class);
    }

    public function cashbackRules(): HasMany
    {
        return $this->hasMany(BusinessCashbackRule::class);
    }

    public function originCashbacks(): HasMany
    {
        return $this->hasMany(UserCashback::class, 'origin_business_id');
    }

    public function redeemedCashbacks(): HasMany
    {
        return $this->hasMany(UserCashback::class, 'redeemed_business_id');
    }

    public static function generateSlug($name)
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

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
