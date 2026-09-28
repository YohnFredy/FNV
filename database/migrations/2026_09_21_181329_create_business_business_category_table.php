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
        Schema::create('business_business_category', function (Blueprint $table) {

            $table->foreignId('business_id')->constrained()->onDelete('cascade');
            $table->foreignId('business_category_id')->constrained()->onDelete('cascade');

            // Esta es tu clave primaria. Asegura que no haya duplicados.
            $table->primary(['business_id', 'business_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_business_category');
    }
};
