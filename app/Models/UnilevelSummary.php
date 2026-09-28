<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Modelo Eloquent para los Contadores y Resúmenes del Árbol Escalonado (Unilevel).
 * Mantiene en O(1) la cantidad de directos y el total de la red descendente.
 *
 * @property int $user_id
 * @property int $direct_sponsors_count Cantidad de patrocinados directos (frontales en nivel 1).
 * @property int $total_network_members Cantidad de afiliados en toda la red unilevel.
 * @property string $personal_points Puntos personales acumulados.
 * @property string $group_points Puntos grupales acumulados.
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 */
class UnilevelSummary extends Model
{
    protected $table = 'unilevel_summaries';

    protected $primaryKey = 'user_id';

    public $incrementing = false;

    protected $fillable = [
        'user_id',
        'direct_sponsors_count',
        'total_network_members',
        'personal_points',
        'group_points',
    ];

    protected function casts(): array
    {
        return [
            'direct_sponsors_count' => 'integer',
            'total_network_members' => 'integer',
            'personal_points' => 'decimal:4',
            'group_points' => 'decimal:4',
        ];
    }

    /**
     * Usuario propietario de este resumen escalonado.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
