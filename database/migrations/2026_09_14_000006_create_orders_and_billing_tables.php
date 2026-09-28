<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Órdenes de compra
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('public_order_number')->unique(); // Número de orden público
            $table->foreignId('user_id')->constrained()->restrictOnDelete(); // Cliente que hizo la compra
            $table->tinyInteger('status')->default(1)->unsigned(); // Estado de la orden (ej. 1=pending, 2=paid, etc.)
            $table->string('payment_method')->nullable(); // Método de pago (tarjeta, PSE, etc.)
            $table->unsignedTinyInteger('shipping_type')->default(1)->comment('1 = Store, 2 = delivery'); // Tipo de entrega

            // DATOS del que recibe
            $table->string('shipping_name')->nullable(); // Nombre completo / Razón social
            $table->foreignId('document_type_id')->nullable()->constrained('document_types')->cascadeOnUpdate()->nullOnDelete();
            $table->string('shipping_document')->nullable(); // Número de documento
            $table->string('shipping_phone')->nullable();

            $table->decimal('subtotal', 10, 2)->default(0); // La suma del precio de los productos (sin ajustes)
            $table->decimal('discount', 10, 2)->default(0); // Descuentos aplicados
            $table->decimal('taxable_amount', 10, 2)->default(0); // La base sobre la que se calculó el impuesto
            $table->decimal('tax_amount', 10, 2)->default(0); // Total de IVA
            $table->decimal('shipping_cost', 10, 2)->default(0); // Costo de envío
            $table->decimal('total', 10, 2)->default(0); // Total pagado = subtotal - descuento + impuesto + envío
            $table->decimal('total_pts', 10, 2)->default(0); // Puntos generados por la orden

            // Datos de entrega
            $table->foreignId('shipping_country_id')->nullable()->constrained('countries')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('shipping_department_id')->nullable()->constrained('departments')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('shipping_city_id')->nullable()->constrained('cities')->cascadeOnUpdate()->nullOnDelete();
            $table->string('shipping_addCity')->nullable(); // Para agregar ciudad manual si no aparece
            $table->text('shipping_address')->nullable();
            $table->string('shipping_additional_address')->nullable(); // Detalles adicionales de dirección

            $table->timestamps();

            $table->index('public_order_number');
            $table->index('status');
            $table->index('user_id');
        });

        // 2. Datos de facturación de la orden
        Schema::create('order_billing_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();

            $table->string('name')->nullable(); // Nombre completo / Razón social
            $table->foreignId('document_type_id')->nullable()->constrained('document_types')->cascadeOnUpdate()->nullOnDelete();
            $table->string('document')->nullable()->index(); // Número de documento
            $table->string('email')->nullable()->index();   // Email para factura electrónica
            $table->string('phone')->nullable();

            $table->foreignId('country_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->string('addCity')->nullable(); // Para agregar ciudad manual
            $table->text('address')->nullable();   // Dirección fiscal

            $table->timestamps();

            $table->index('order_id');
        });

        // 3. Items o detalles de la orden
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate(); // Relación con la orden
            $table->foreignId('product_id')->constrained()->cascadeOnUpdate()->restrictOnDelete(); // Producto relacionado

            $table->string('name'); // Nombre del producto al momento de la compra (histórico)

            $table->decimal('unit_price', 10, 2); // Precio con iva
            $table->decimal('pts', 10, 2)->default(0);
            $table->unsignedInteger('quantity');
            $table->decimal('discount', 10, 2)->default(0); // Descuento total
            $table->decimal('tax_percent', 5, 2)->default(0); // Porcentaje de impuesto aplicado (ej. 19%)
            $table->decimal('tax_amount', 10, 2)->default(0); // Valor del IVA total en este item
            $table->decimal('unit_sales_price', 10, 2);
            $table->decimal('total_pts', 10, 2)->default(0);
            $table->timestamps();

            $table->index('order_id');
            $table->index('product_id');
        });

        // 4. Webhooks de pago
        Schema::create('payment_webhooks', function (Blueprint $table) {
            $table->id();
            $table->string('payment_gateway'); // Stripe, PayPal, etc.
            $table->string('reference')->unique(); // Relacionado con 'public_order_number'
            $table->json('payload'); // Guarda el JSON completo de la pasarela
            $table->timestamps();

            $table->index('reference');
            $table->foreign('reference')->references('public_order_number')->on('orders')->cascadeOnUpdate()->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_webhooks');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('order_billing_data');
        Schema::dropIfExists('orders');
    }
};
