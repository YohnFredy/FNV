<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Modelo Eloquent para los Contadores y Resúmenes del Árbol Binario.
 * Proporciona acceso instantáneo O(1) a la cantidad de afiliados y puntos por pierna.
 *
 * @property int $user_id
 * @property int $total_left_members Total de afiliados en la pierna izquierda.
 * @property int $total_right_members Total de afiliados en la pierna derecha.
 * @property string $total_left_points Puntos acumulados en la pierna izquierda.
 * @property string $total_right_points Puntos acumulados en la pierna derecha.
 * @property int|null $extreme_left_user_id Puntero a la hoja más profunda de la pierna izquierda.
 * @property int|null $extreme_right_user_id Puntero a la hoja más profunda de la pierna derecha.
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read User|null $extremeLeftUser
 * @property-read User|null $extremeRightUser
 */
class BinarySummary extends Model
{
    protected $table = 'binary_summaries';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'total_left_members',
        'total_right_members',
        'total_left_points',
        'total_right_points',
        'extreme_left_user_id',
        'extreme_right_user_id',
    ];

    protected function casts(): array
    {
        return [
            'total_left_members' => 'integer',
            'total_right_members' => 'integer',
            'total_left_points' => 'decimal:4',
            'total_right_points' => 'decimal:4',
        ];
    }

    /**
     * Usuario propietario de este resumen binario.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Usuario en el extremo más profundo de la pierna izquierda.
     *
     * @return BelongsTo<User, $this>
     */
    public function extremeLeftUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'extreme_left_user_id');
    }

    /**
     * Usuario en el extremo más profundo de la pierna derecha.
     *
     * @return BelongsTo<User, $this>
     */
    public function extremeRightUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'extreme_right_user_id');
    }

    /**
     * Total combinado de afiliados en ambas piernas del binario.
     */
    public function getTotalMembersAttribute(): int
    {
        return $this->total_left_members + $this->total_right_members;
    }
}
