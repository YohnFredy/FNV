<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MlmPeriodUserBalance extends Model
{
    use HasFactory;

    protected $table = 'mlm_period_user_balances';

    protected $fillable = [
        'period_id',
        'user_id',
        'personal_points',
        'binary_left_points',
        'binary_right_points',
        'binary_points_matched',
        'unilevel_group_points',
        'is_active',
        'activation_type',
        'rank_achieved',
        'rank_level',
        'commission_binary',
        'commission_unilevel',
        'commission_strategic_partner',
        'commission_rank',
        'commission_other',
        'total_commission',
        'commission_notes',
        'settled_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'period_id' => 'integer',
            'user_id' => 'integer',
            'personal_points' => 'decimal:2',
            'binary_left_points' => 'decimal:2',
            'binary_right_points' => 'decimal:2',
            'binary_points_matched' => 'decimal:2',
            'unilevel_group_points' => 'decimal:2',
            'is_active' => 'boolean',
            'rank_level' => 'integer',
            'commission_binary' => 'decimal:2',
            'commission_unilevel' => 'decimal:2',
            'commission_strategic_partner' => 'decimal:2',
            'commission_rank' => 'decimal:2',
            'commission_other' => 'decimal:2',
            'total_commission' => 'decimal:2',
            'settled_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<MlmPeriod, $this>
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(MlmPeriod::class, 'period_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
