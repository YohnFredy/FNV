<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Modelo Eloquent para los nodos de colocación en el Árbol Binario.
 * Cada registro representa una posición única en el árbol binario con un máximo de 2 hijos (L y R).
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $parent_id
 * @property string|null $position 'L' (Izquierda) o 'R' (Derecha). Null para el Root Master.
 * @property int|null $left_child_id
 * @property int|null $right_child_id
 * @property int $depth Nivel de profundidad desde la raíz (Raíz = 0).
 * @property string|null $path Ruta materializada (ej: '/1/5/14/')
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read User|null $parentUser
 * @property-read User|null $leftChildUser
 * @property-read User|null $rightChildUser
 */
class BinaryNode extends Model
{
    protected $table = 'binary_nodes';

    protected $fillable = [
        'user_id',
        'parent_id',
        'position',
        'left_child_id',
        'right_child_id',
        'depth',
        'path',
    ];

    protected function casts(): array
    {
        return [
            'depth' => 'integer',
        ];
    }

    /**
     * Usuario propietario de esta posición en el árbol binario.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Usuario padre inmediato en la línea ascendente del binario.
     *
     * @return BelongsTo<User, $this>
     */
    public function parentUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Usuario colocado en el slot inmediato de la pierna izquierda.
     *
     * @return BelongsTo<User, $this>
     */
    public function leftChildUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'left_child_id');
    }

    /**
     * Usuario colocado en el slot inmediato de la pierna derecha.
     *
     * @return BelongsTo<User, $this>
     */
    public function rightChildUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'right_child_id');
    }

    /**
     * Nodo binario del padre.
     *
     * @return BelongsTo<BinaryNode, $this>
     */
    public function parentNode(): BelongsTo
    {
        return $this->belongsTo(BinaryNode::class, 'parent_id', 'user_id');
    }

    /**
     * Nodo binario del hijo izquierdo.
     *
     * @return BelongsTo<BinaryNode, $this>
     */
    public function leftChildNode(): BelongsTo
    {
        return $this->belongsTo(BinaryNode::class, 'left_child_id', 'user_id');
    }

    /**
     * Nodo binario del hijo derecho.
     *
     * @return BelongsTo<BinaryNode, $this>
     */
    public function rightChildNode(): BelongsTo
    {
        return $this->belongsTo(BinaryNode::class, 'right_child_id', 'user_id');
    }

    /**
     * Scope para filtrar únicamente el nodo maestro raíz.
     *
     * @param  Builder<BinaryNode>  $query
     * @return Builder<BinaryNode>
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('parent_id');
    }
}
