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
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->timestamp('visit_time')->useCurrent(); // Fecha y hora de ingreso
            $table->string('ip_address', 45)->nullable(); // Dirección IP del visitante
            $table->text('user_agent')->nullable(); // Navegador, OS, dispositivo
            $table->string('referrer_url')->nullable(); // De dónde vino (si está disponible)
            $table->string('landing_page_url'); // La URL exacta que visitó

            // Parámetros UTM para seguimiento de campañas
            $table->string('utm_source')->nullable(); // Fuente (e.g., facebook, google, email)
            $table->string('utm_medium')->nullable(); // Medio (e.g., cpc, social_ad, newsletter)
            $table->string('utm_campaign')->nullable(); // Nombre de la campaña
            $table->string('utm_term')->nullable(); // Término de búsqueda (para Ads)
            $table->string('utm_content')->nullable(); // Identificador del anuncio o enlace específico

            // Seguimiento del Video
            $table->boolean('video_played')->default(false);
            $table->boolean('video_paused')->default(false);
            $table->boolean('video_resumed')->default(false);
            $table->float('video_max_watch_time', 8, 2)->default(0); // Máximo segundo visto
            $table->float('video_total_watch_time', 10, 2)->default(0); // Tiempo total acumulado (considerando rewinds) - Más complejo de implementar
            $table->boolean('video_completed')->default(false);
            $table->float('video_duration', 8, 2)->nullable(); // Duración total del video (para contexto)

            // Acción final
            $table->boolean('clicked_whatsapp')->default(false);
            $table->timestamp('whatsapp_click_time')->nullable(); // Hora del clic en WhatsApp

            // Campos de Spam Protection (Ejemplo Honeypot)
            $table->string('honeypot_field')->nullable(); // Campo oculto que no debe ser llenado por humanos

            $table->timestamps(); // created_at, updated_at (útil para ver cuándo se actualizó el registro)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
