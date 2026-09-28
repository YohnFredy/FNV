<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivationPt extends Model
{
    use HasFactory;

    protected $table = 'activation_pts';

    protected $fillable = [
        'min_pts_first',
        'min_pts_monthly',
        'first_activation_grace_period_enabled',
        'grace_period_months',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'min_pts_first' => 'decimal:2',
            'min_pts_monthly' => 'decimal:2',
            'first_activation_grace_period_enabled' => 'boolean',
            'grace_period_months' => 'integer',
        ];
    }
}
