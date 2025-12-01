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
        Schema::table('invoices', function (Blueprint $table) {
        // Campo para marcar la factura como anulada (true/false)
        $table->boolean('is_cancelled')->default(false)->after('total_amount');
        
        // Campo para guardar el motivo de la anulación
        $table->string('cancellation_reason')->nullable()->after('is_cancelled');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
        $table->dropColumn(['is_cancelled', 'cancellation_reason']);
    });
    }
};
