<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo Eloquent para la Tabla de Cierre (Closure Table) del Árbol Escalonado / Unilevel.
 *
 * @property int $ancestor_id ID del usuario ancestro en la línea de patrocinio.
 * @property int $descendant_id ID del usuario descendiente.
 * @property int $depth Distancia en generaciones (0 = self, 1 = directo, 2 = indirecto...).
 * @property-read User $ancestor
 * @property-read User $descendant
 */
class UnilevelPath extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $table = 'unilevel_paths';

    protected $fillable = [
        'ancestor_id',
        'descendant_id',
        'depth',
    ];

    protected function casts(): array
    {
        return [
            'depth' => 'integer',
        ];
    }

    /**
     * Usuario ancestro en la red escalonada.
     *
     * @return BelongsTo<User, $this>
     */
    public function ancestor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ancestor_id');
    }

    /**
     * Usuario descendiente en la red escalonada.
     *
     * @return BelongsTo<User, $this>
     */
    public function descendant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'descendant_id');
    }
}
