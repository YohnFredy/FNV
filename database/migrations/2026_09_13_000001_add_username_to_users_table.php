<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Ejecuta la migración agregando la columna username a la tabla users.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Se agrega username único, indexado y alfanumérico
            $table->string('username', 50)->nullable()->unique()->after('name');
        });

        // Asignar usernames limpios a cualquier usuario preexistente en el sistema
        $existingUsers = DB::table('users')->orderBy('id')->get();
        foreach ($existingUsers as $user) {
            $baseUsername = Str::slug($user->name, '_');
            if (empty($baseUsername)) {
                $baseUsername = 'user_'.$user->id;
            }

            // Asegurar unicidad si hay nombres repetidos
            $username = $baseUsername;
            $counter = 1;
            while (DB::table('users')->where('username', $username)->where('id', '!=', $user->id)->exists()) {
                $username = $baseUsername.'_'.$counter;
                $counter++;
            }

            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }

        // Hacer la columna username no-nullable una vez asignados los datos
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 50)->nullable(false)->change();
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
