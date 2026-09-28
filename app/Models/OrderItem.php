<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'name',
        'unit_price',
        'pts',
        'quantity',
        'discount',
        'tax_percent',
        'tax_amount',
        'unit_sales_price',
        'total_pts',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'pts' => 'decimal:2',
            'quantity' => 'integer',
            'discount' => 'decimal:2',
            'tax_percent' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'unit_sales_price' => 'decimal:2',
            'total_pts' => 'decimal:2',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getTotalPtsAttribute(): mixed
    {
        return $this->attributes['total_pts'] ?? null;
    }
}
