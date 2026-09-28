<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserData extends Model
{
    use HasFactory;

    protected $table = 'user_data';

    protected $fillable = [
        'user_id',
        'sex',
        'birthdate',
        'phone',
        'country_id',
        'department_id',
        'city_id',
        'city',
        'city_id_2',
        'address',
        'bank_name',
        'account_type',
        'account_number',
    ];

    protected function casts(): array
    {
        return [
            'birthdate' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function secondaryCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id_2');
    }
}
