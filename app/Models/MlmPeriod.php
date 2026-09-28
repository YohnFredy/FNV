<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MlmPeriod extends Model
{
    use HasFactory;

    public const STATUS_UPCOMING = 'upcoming';

    public const STATUS_ACTIVE = 'active';

    public const STATUS_CLOSING = 'closing';

    public const STATUS_SETTLED = 'settled';

    protected $table = 'mlm_periods';

    protected $fillable = [
        'name',
        'code',
        'starts_at',
        'ends_at',
        'min_activation_pts',
        'total_commission_binary',
        'total_commission_unilevel',
        'total_commission_strategic_partner',
        'total_commission_rank',
        'total_commission_other',
        'total_payout',
        'status',
        'settled_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'starts_at' => 'date',
            'ends_at' => 'date',
            'min_activation_pts' => 'decimal:2',
            'total_commission_binary' => 'decimal:2',
            'total_commission_unilevel' => 'decimal:2',
            'total_commission_strategic_partner' => 'decimal:2',
            'total_commission_rank' => 'decimal:2',
            'total_commission_other' => 'decimal:2',
            'total_payout' => 'decimal:2',
            'settled_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<MlmPeriodUserBalance, $this>
     */
    public function userBalances(): HasMany
    {
        return $this->hasMany(MlmPeriodUserBalance::class, 'period_id');
    }

    /**
     * @return HasMany<PointTransaction, $this>
     */
    public function pointTransactions(): HasMany
    {
        return $this->hasMany(PointTransaction::class, 'period_id');
    }

    /**
     * Obtiene el periodo actualmente activo en el sistema.
     */
    public static function current(): ?self
    {
        return static::where('status', self::STATUS_ACTIVE)->first();
    }

    /**
     * Scope para filtrar únicamente el periodo activo.
     *
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
