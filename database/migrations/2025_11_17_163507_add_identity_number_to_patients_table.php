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
        Schema::table('patients', function (Blueprint $table) {
            // NUEVO CAMPO: Se añade el campo de número de identidad como obligatorio y único
            $table->string('identity_number', 50)->unique()->after('last_name');
            
            // CORRECCIÓN: Se asegura que la columna 'email' sea nullable (opcional)
            $table->string('email', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('identity_number');
            // Opcional: Revertir email a no nulo si ese era el estado anterior
            // $table->string('email', 100)->nullable(false)->change(); 
        });
    }
};