<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta las migraciones para el sistema binario MLM.
     * Diseñado para soportar millones de registros con máxima concurrencia y consultas en O(1).
     */
    public function up(): void
    {
        // 1. Tabla principal de nodos del árbol binario
        Schema::create('binary_nodes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('users')->nullOnDelete();

            // Posición en el binario: 'L' (Izquierda / Left) o 'R' (Derecha / Right).
            // Es NULL únicamente para el Nodo Maestro Raíz.
            $table->enum('position', ['L', 'R'])->nullable();

            // Punteros directos a los hijos para navegación inmediata en O(1)
            $table->foreignId('left_child_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('right_child_id')->nullable()->constrained('users')->nullOnDelete();

            // Profundidad en el árbol (Raíz = 0)
            $table->unsignedInteger('depth')->default(0);

            // Ruta materializada indexada para auditoría y visualizaciones (ej: '/1/5/12/35/')
            $table->string('path', 500)->nullable();

            $table->timestamps();

            // RESTRICCIÓN DE SEGURIDAD FÍSICA: Un padre jamás puede tener dos hijos en la misma pierna.
            // Protege de condiciones de carrera a nivel de motor MySQL.
            $table->unique(['parent_id', 'position'], 'unique_binary_parent_position');

            // Índices de alto rendimiento para búsquedas y colocación rápida
            $table->index('parent_id');
            $table->index('left_child_id');
            $table->index('right_child_id');
            $table->index('path');
        });

        // 2. Tabla de Cierre (Closure Table) para el árbol binario.
        // Permite obtener todos los ancestros o descendientes de cualquier nodo en 1 sola consulta indexada O(1).
        Schema::create('binary_paths', function (Blueprint $table) {
            $table->foreignId('ancestor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('descendant_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('depth'); // Distancia entre ancestro y descendiente

            // Pierna relativa del ancestro: indica en qué rama ('L' o 'R') del ancestro se encuentra el descendiente.
            // NULL para la relación reflexiva (ancestor_id == descendant_id).
            $table->enum('leg', ['L', 'R'])->nullable();

            $table->primary(['ancestor_id', 'descendant_id']);
            $table->index('descendant_id');
            $table->index(['ancestor_id', 'leg'], 'idx_binary_paths_ancestor_leg');
        });

        // 3. Tabla de Resúmenes y Contadores en tiempo real del Árbol Binario.
        // Evita realizar COUNT(*) pesados en tablas con millones de afiliados.
        Schema::create('binary_summaries', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->cascadeOnDelete();

            // Conteo exacto de afiliados en cada pierna
            $table->unsignedBigInteger('total_left_members')->default(0);
            $table->unsignedBigInteger('total_right_members')->default(0);

            // Volumen de puntos acumulados en cada pierna (sin decimales flotantes)
            $table->decimal('total_left_points', 18, 4)->default(0);
            $table->decimal('total_right_points', 18, 4)->default(0);

            // Punteros a los nodos extremos de cada rama para colocación ultra-rápida en O(1)
            $table->foreignId('extreme_left_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('extreme_right_user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Revierte las migraciones del sistema binario.
     */
    public function down(): void
    {
        Schema::dropIfExists('binary_summaries');
        Schema::dropIfExists('binary_paths');
        Schema::dropIfExists('binary_nodes');
    }
};
