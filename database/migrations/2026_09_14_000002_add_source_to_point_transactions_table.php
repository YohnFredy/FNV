<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración para agregar origen ('order' vs 'invoice') a las transacciones de puntos.
     * - 'order': Compras originadas en la tienda virtual (tabla orders).
     * - 'invoice': Comisiones/puntos originados en compras de comercios aliados (tabla invoices).
     */
    public function up(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            // Origen de los puntos: tienda virtual ('order') o comercio aliado ('invoice')
            $table->enum('source_type', ['order', 'invoice'])->default('order')->after('from_user_id');

            // Llave foránea para vincular directamente a la tabla invoices
            $table->foreignId('invoice_id')->nullable()->after('order_id')->constrained('invoices')->cascadeOnDelete();

            // Índices compuestos para auditoría rápida por origen
            $table->index(['source_type', 'order_id'], 'idx_pt_source_order');
            $table->index(['source_type', 'invoice_id'], 'idx_pt_source_invoice');
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::table('point_transactions', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropIndex('idx_pt_source_order');
            $table->dropIndex('idx_pt_source_invoice');
            $table->dropColumn(['source_type', 'invoice_id']);
        });
    }
};
