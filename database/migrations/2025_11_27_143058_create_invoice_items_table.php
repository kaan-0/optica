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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            // Relaciones
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('restrict'); // Evita borrar producto si está facturado

            // Detalles del Ítem
            $table->integer('quantity');
            $table->decimal('price_at_sale', 10, 2); // Precio al momento de la venta
            $table->decimal('line_total', 10, 2);

            // Trazabilidad del Stock (Opcional, pero muy útil)
            $table->integer('stock_tienda_descontado')->default(0);
            $table->integer('stock_bodega_descontado')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
