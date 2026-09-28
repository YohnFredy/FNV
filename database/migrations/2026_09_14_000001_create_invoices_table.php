<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración para la tabla de facturas de compras de afiliados.
     * Permite al administrador registrar comisiones ganadas y puntos generados por cada compra.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            // Usuario afiliado que realizó la compra
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Número o código identificador de la factura (ej. INV-20260914-001)
            $table->string('invoice_number', 100)->unique();

            // Monto total de la compra (opcional)
            $table->decimal('total_amount', 18, 4)->nullable()->default(0.0000);

            // Total de comisión asignada al usuario por esta compra
            $table->decimal('commission_total', 18, 4)->default(0.0000);

            // Puntos (PTS) asignados por el administrador
            $table->decimal('pts', 18, 4)->default(0.0000);

            // Estado de la factura
            $table->enum('status', ['pending', 'approved', 'cancelled'])->default('approved');

            // Concepto, notas o detalles adicionales
            $table->text('description')->nullable();

            $table->timestamps();

            // Índices para consultas y reportes de administración
            $table->index(['user_id', 'status'], 'idx_invoices_user_status');
            $table->index('created_at', 'idx_invoices_created_at');
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
