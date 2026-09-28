<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class BusinessData extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_active',
        'business_admin_id',
        'business_id',
        'slug',
        'phone',
        'whatsapp',
        'website_url',
        'business_email',
        'description',
        'keywords',
        'country_id',
        'department_id',
        'city_id',
        'city',
        'address',
        'latitude',
        'longitude',
        'facebook_url',
        'instagram_url',
        'linkedin_url',
        'youtube_url',
        'tiktok_url',
        'x_url',
        'promo_video_url',
        'additional_videos',
        'custom_links',
    ];

    protected $casts = [
        'additional_videos' => 'array',
        'custom_links' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        // Crear slug automáticamente SOLO si no existe
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = static::generateUniqueSlug($model);
            }
        });

        // Si se edita el dato y no hay slug, generarlo
        static::updating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = static::generateUniqueSlug($model);
            }
        });
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    /*     public function business()
{
    return $this->belongsTo(Business::class)->select(['id','name','nit']);
} */

    public function storeTypes()
    {
        return $this->belongsToMany(StoreType::class, 'business_data_store_type');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function cityRelation()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function parish()
    {
        return $this->belongsTo(Parish::class, 'parish_id');
    }

    public function admin()
    {
        return $this->belongsTo(BusinessAdmin::class, 'business_admin_id');
    }

    public function invoiceSupports()
    {
        return $this->hasMany(InvoiceSupport::class, 'business_data_id');
    }

    public static function generateUniqueSlug($model)
    {
        // Usamos el nombre del negocio (tabla businesses)
        $name = $model->business ? $model->business->name : 'business';

        $slug = Str::slug($name);
        $originalSlug = $slug;
        $i = 1;

        // Garantizar que sea único en business_data
        while (static::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$i}";
            $i++;
        }

        return $slug;
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
