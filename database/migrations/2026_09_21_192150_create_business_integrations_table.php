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
        Schema::create('business_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->onDelete('cascade');

            // Claves de autenticación
            $table->string('public_key')->unique(); // visible
            $table->string('secret_key'); // privada (encriptar)

            // Nombre de la empresa como lo usará en la URL (slug o username)
            $table->string('slug')->unique(); // p. ej. tienda-maria

            // URL donde recibe notificaciones (webhooks)
            $table->string('webhook_url')->nullable();

            // ¿Está activa esta integración?
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // Índices para búsquedas frecuentes
            $table->index('public_key');
            $table->index('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_integrations');
    }
};
