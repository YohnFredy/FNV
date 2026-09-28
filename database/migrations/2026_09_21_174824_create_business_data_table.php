<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('business_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_admin_id')->nullable()->constrained('business_admins')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->string('slug')->nullable()->unique();

            // Información general
            $table->string('phone')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('website_url')->nullable();
            $table->string('business_email')->nullable();
            $table->text('description')->nullable(); // Descripción de la empresa
            $table->text('keywords')->nullable(); // Palabras clave para el buscador

            // Ubicación
            $table->integer('country_id');
            $table->integer('department_id')->nullable();
            $table->integer('city_id')->nullable();
            $table->string('city')->nullable();
            $table->string('address')->nullable();

            $table->decimal('latitude', 10, 7)->nullable()->default(0);
            $table->decimal('longitude', 10, 7)->nullable()->default(0);

            // Redes sociales (campos opcionales)
            $table->string('facebook_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->string('x_url')->nullable(); // Twitter (X)

            // Multimedia
            $table->text('promo_video_url')->nullable();
            $table->json('additional_videos')->nullable(); // otros videos si aplica

            // Otros enlaces personalizados
            $table->json('custom_links')->nullable(); // por ejemplo enlaces a catálogos, PDF, etc.

            $table->timestamps();

            // Índices para mejorar rendimiento en búsquedas y filtros

            $table->index('is_active');
            $table->index(['latitude', 'longitude']);
            $table->index('country_id');
            $table->index('department_id');
            $table->index('city_id');
            $table->index('city');
            $table->index('address');

            if (DB::getDriverName() === 'mysql') {
                $table->fullText(['keywords', 'description']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_data');
    }
};
