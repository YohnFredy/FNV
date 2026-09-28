<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Modelo Eloquent para el Libro Contable Inmutable de Transacciones de Puntos (Point Ledger).
 * Registra cada punto personal o de red con trazabilidad de origen y destino.
 *
 * @property int $id
 * @property int $user_id Usuario que recibe el beneficio en puntos.
 * @property int $from_user_id Usuario que realizó la compra origen.
 * @property string $source_type Origen de los puntos: 'order' (tienda virtual) o 'invoice' (comercios aliados).
 * @property int|null $order_id Identificador de la compra/pedido en tienda virtual (orders).
 * @property int|null $invoice_id Identificador de la factura en comercios aliados (invoices).
 * @property string $tree_type 'personal', 'binary' o 'unilevel'.
 * @property string|null $leg 'L' o 'R' en caso de ser punto binario ascendente.
 * @property string $points Cantidad de puntos acreditados.
 * @property string $description Concepto o detalle de la acreditación.
 * @property Carbon $created_at
 * @property-read User $user
 * @property-read User $fromUser
 * @property-read Invoice|null $invoice
 */
class PointTransaction extends Model
{
    public $timestamps = false;

    protected $table = 'point_transactions';

    protected $fillable = [
        'period_id',
        'user_id',
        'from_user_id',
        'source_type',
        'order_id',
        'invoice_id',
        'tree_type',
        'leg',
        'points',
        'description',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'period_id' => 'integer',
            'points' => 'decimal:4',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Periodo MLM al que corresponde la transacción.
     *
     * @return BelongsTo<MlmPeriod, $this>
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(MlmPeriod::class, 'period_id');
    }

    /**
     * Usuario que recibe los puntos.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Usuario que originó la compra de puntos.
     *
     * @return BelongsTo<User, $this>
     */
    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Factura de comercio aliado asociada a los puntos (si aplica).
     *
     * @return BelongsTo<Invoice, $this>
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }
}
