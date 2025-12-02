<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    // Nombre de la tabla de detalles (Debería ser 'invoice_items' o 'invoices_items')
    protected $table = 'invoice_items'; 

    protected $fillable = [
        'invoice_id',   // Llave foránea que apunta a la cabecera (Invoice)
        'product_id',   // Llave foránea al Producto
        'quantity',
        'price_at_sale',
        'line_total',
        'discount_rate',
        'discount_amount',
        'stock_tienda_descontado',
        'stock_bodega_descontado',
    ];

    /**
     * Un ítem de factura pertenece a una factura.
     */
    public function invoice()
    {
        // La relación correcta es belongsTo a Invoice
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Un ítem de factura se relaciona con un producto.
     */
    public function product()
    {
        return $this->belongsTo(Product::class); 
    }
}