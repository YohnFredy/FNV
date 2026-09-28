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
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('nit')->nullable(); // NIT único
            $table->foreignId('user_id')->constrained()->onUpdate('cascade');
            $table->decimal('minimum_percentage', 4, 2)->default(0);
            $table->decimal('maximum_percentage', 4, 2)->default(0);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_active')->default(true); // Define si el comercio está habilitado
            $table->rememberToken();
            $table->timestamps();
            $table->index('name');
            $table->index('nit');
            $table->unique(['name', 'nit']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
