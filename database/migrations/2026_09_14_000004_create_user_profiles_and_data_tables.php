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
        Schema::table('users', function (Blueprint $table) {
            $table->string('last_name')->default('')->after('name');
            $table->foreignId('document_type_id')->nullable()->after('last_name')->constrained('document_types')->nullOnDelete();
            $table->string('dni')->nullable()->after('document_type_id');

            $table->unique(['document_type_id', 'dni'], 'users_document_type_id_dni_unique');
        });

        Schema::create('user_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('sex', ['male', 'female', 'other'])->nullable();
            $table->date('birthdate')->nullable();
            $table->string('phone')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('city')->nullable();
            $table->foreignId('city_id_2')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('address')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('account_type')->nullable();
            $table->string('account_number')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_data');

        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('document_type_id');
            $table->dropUnique('users_document_type_id_dni_unique');
            $table->dropColumn(['last_name', 'dni']);
        });
    }
};
