<?php
// database/migrations/2025_11_27_XXXXXX_add_discount_amount_to_invoices_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Se añade la nueva columna 'discount_amount'.
            // La colocamos después de 'subtotal' para que quede ordenada.
            $table->decimal('discount_amount', 10, 2)->default(0.00)->after('subtotal');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // En caso de revertir (rollback), eliminamos la columna.
            $table->dropColumn('discount_amount');
        });
    }
};