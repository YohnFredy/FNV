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
        Schema::create('business_data_store_type', function (Blueprint $table) {
            $table->id();

            $table->foreignId('business_data_id')->constrained('business_data')->onDelete('cascade');
            $table->foreignId('store_type_id')->constrained()->onDelete('cascade');

            $table->timestamps();

            $table->unique(['business_data_id', 'store_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_data_store_type');
    }
};
