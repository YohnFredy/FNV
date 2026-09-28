<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo Eloquent para la Tabla de Cierre (Closure Table) del Árbol Binario.
 * Almacena las relaciones de todos los ancestros y descendientes en cualquier nivel de profundidad.
 *
 * @property int $ancestor_id ID del usuario ancestro en la línea ascendente.
 * @property int $descendant_id ID del usuario descendiente.
 * @property int $depth Distancia en niveles entre ancestro y descendiente (0 para self).
 * @property string|null $leg Pierna del ancestro donde se sitúa el descendiente ('L' o 'R'). Null para self.
 * @property-read User $ancestor
 * @property-read User $descendant
 */
class BinaryPath extends Model
{
    public $timestamps = false;

    public $incrementing = false;

    protected $table = 'binary_paths';

    protected $fillable = [
        'ancestor_id',
        'descendant_id',
        'depth',
        'leg',
    ];

    protected function casts(): array
    {
        return [
            'depth' => 'integer',
        ];
    }

    /**
     * Usuario ancestro en la línea ascendente.
     *
     * @return BelongsTo<User, $this>
     */
    public function ancestor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ancestor_id');
    }

    /**
     * Usuario descendiente en la red binaria.
     *
     * @return BelongsTo<User, $this>
     */
    public function descendant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'descendant_id');
    }
}
