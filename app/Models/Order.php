<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    /** @use HasFactory<Factory<Order>> */
    use HasFactory;

    public const STATUS_SALE_PENDING = 1;

    public const STATUS_SALE_APPROVED = 2;

    public const STATUS_PTS_GENERATED = 3;

    public const STATUS_SENT = 4;

    public const STATUS_DELIVERED = 5;

    public const STATUS_SALE_REJECTED = 6;

    public const STATUS_VOIDED = 7;

    public const STATUS_VOID_REJECTED = 8;

    public const SHIPPING_TYPE_STORE = 1;

    public const SHIPPING_TYPE_DELIVERY = 2;

    protected $fillable = [
        'public_order_number',
        'user_id',
        'status',
        'payment_method',
        'shipping_type',
        'shipping_name',
        'document_type_id',
        'shipping_document',
        'shipping_phone',
        'subtotal',
        'discount',
        'taxable_amount',
        'tax_amount',
        'shipping_cost',
        'total',
        'total_pts',
        'shipping_country_id',
        'shipping_department_id',
        'shipping_city_id',
        'shipping_addCity',
        'shipping_address',
        'shipping_additional_address',
    ];

    protected function casts(): array
    {
        return [
            'status' => 'integer',
            'shipping_type' => 'integer',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'taxable_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'total' => 'decimal:2',
            'total_pts' => 'decimal:2',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_order_number';
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<DocumentType, $this>
     */
    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    /**
     * @return BelongsTo<Country, $this>
     */
    public function shippingCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'shipping_country_id');
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function shippingDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'shipping_department_id');
    }

    /**
     * @return BelongsTo<City, $this>
     */
    public function shippingCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'shipping_city_id');
    }

    /**
     * @return HasOne<OrderBillingData, $this>
     */
    public function billingData(): HasOne
    {
        return $this->hasOne(OrderBillingData::class);
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return HasOne<PaymentWebhook, $this>
     */
    public function webhook(): HasOne
    {
        return $this->hasOne(PaymentWebhook::class, 'reference', 'public_order_number');
    }

    public function updateStatus(int $status): void
    {
        $this->update(['status' => $status]);
    }
}
