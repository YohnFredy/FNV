<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Modelo Eloquent para los nodos del Árbol Escalonado (Unilevel).
 * Cada nodo representa la relación de patrocinio directo y el nivel de profundidad en la red.
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $sponsor_id ID del patrocinador directo (null únicamente para el Root Master).
 * @property int $level Nivel en la jerarquía (Root = 1, Directos = 2...).
 * @property string|null $path Ruta materializada de patrocinio directo (ej: '/1/4/9/').
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @property-read User|null $sponsorUser
 * @property-read Collection<int, UnilevelNode> $directReferrals
 */
class UnilevelNode extends Model
{
    protected $table = 'unilevel_nodes';

    protected $fillable = [
        'user_id',
        'sponsor_id',
        'level',
        'path',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
        ];
    }

    /**
     * Usuario titular de esta posición unilevel.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Usuario patrocinador directo (Sponsor).
     *
     * @return BelongsTo<User, $this>
     */
    public function sponsorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sponsor_id');
    }

    /**
     * Referidos directos en el sistema unilevel (nivel 1 frontal).
     *
     * @return HasMany<UnilevelNode, $this>
     */
    public function directReferrals(): HasMany
    {
        return $this->hasMany(UnilevelNode::class, 'sponsor_id', 'user_id');
    }

    /**
     * Scope para filtrar el nodo raíz master.
     *
     * @param  Builder<UnilevelNode>  $query
     * @return Builder<UnilevelNode>
     */
    public function scopeRoot(Builder $query): Builder
    {
        return $query->whereNull('sponsor_id');
    }
}
