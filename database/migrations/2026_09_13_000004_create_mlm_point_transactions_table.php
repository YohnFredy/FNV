<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración del libro mayor inmutable de transacciones de puntos (Point Ledger).
     * Cada punto generado por compras queda registrado con trazabilidad de origen y destino.
     */
    public function up(): void
    {
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();

            // Usuario que recibe y acumula el punto en su balance
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // Usuario que originó los puntos al efectuar la compra
            $table->foreignId('from_user_id')->constrained('users')->cascadeOnDelete();

            // Identificador de la orden o compra que generó los puntos (opcional)
            $table->unsignedBigInteger('order_id')->nullable();

            // Tipo de distribución:
            // - 'personal': Puntos acumulados por la propia compra del usuario.
            // - 'binary': Puntos propagados hacia arriba en el árbol binario.
            // - 'unilevel': Puntos propagados hacia arriba en la red escalonada.
            $table->enum('tree_type', ['personal', 'binary', 'unilevel']);

            // Pierna en la que se acreditan los puntos en el binario ('L' o 'R'). Null para personal o unilevel.
            $table->enum('leg', ['L', 'R'])->nullable();

            // Cantidad de puntos (alta precisión, sin pérdida por flotantes)
            $table->decimal('points', 18, 4);

            // Descripción o concepto del movimiento
            $table->string('description', 255);

            // Auditoría inmutable de fecha
            $table->timestamp('created_at')->useCurrent();

            // Índices estratégicos para reportes y auditorías masivas
            $table->index(['user_id', 'tree_type'], 'idx_point_user_tree');
            $table->index('from_user_id');
            $table->index('order_id');
            $table->index('created_at');
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('point_transactions');
    }
};
