<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class BusinessCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function businesses(): BelongsToMany
    {
        // 1er arg: Modelo a relacionar
        // 2do arg (opcional): Nombre de la tabla pivote. Lo ponemos por claridad.
        return $this->belongsToMany(Business::class, 'business_business_category');
    }

    public function children()
    {
        return $this->hasMany(BusinessCategory::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(BusinessCategory::class, 'parent_id');
    }
}
