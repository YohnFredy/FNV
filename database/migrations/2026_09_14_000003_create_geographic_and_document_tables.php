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
        // 1. Paises
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('country_code', 3)->nullable()->unique();
            $table->string('division_term_1')->default('Departamento');
            $table->string('division_term_2')->default('Ciudad');
            $table->string('division_term_3')->nullable()->default('Parroquia');
            $table->timestamps();
        });

        // 2. Departamentos / Provincias / Estados
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name', 100);
            $table->timestamps();
        });

        // 3. Ciudades / Municipios / Cantones
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name', 100);
            $table->decimal('cost', 8, 2)->default(0);
            $table->timestamps();
        });

        // 4. Parroquias / Localidades
        Schema::create('parishes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('city_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name', 100);
            $table->decimal('cost', 8, 2)->default(0);
            $table->timestamps();
        });

        // 5. Tipos de documento
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->nullable()->constrained('countries')->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_default')->default(false);
            $table->string('code', 20)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Tipos de tienda
        Schema::create('store_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();

            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_types');
        Schema::dropIfExists('document_types');
        Schema::dropIfExists('parishes');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('countries');
    }
};
