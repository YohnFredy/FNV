<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country_code',
        'division_term_1',
        'division_term_2',
        'division_term_3',
    ];

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }

    public function documentTypes(): HasMany
    {
        return $this->hasMany(DocumentType::class);
    }
}
