<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    // Nombre de la tabla de cabeceras (Debería ser 'invoices')
    protected $table = 'invoices'; 

    protected $fillable = [
        'invoice_number', // Número de factura
        'client_name',    // Nombre del cliente
        'client_id_card', // DNI/RTN del cliente
        'subtotal',       // Subtotal antes de impuestos/descuentos
        'discount_amount',// Monto del descuento (Nuevo campo)
        'tax_amount',     // Impuestos
        'total_amount',   // Total final
        'is_cancelled', //factura cancelada
        'cancellation_reason',
        'date',           // Fecha de emisión
    ];

    /**
     * Una factura tiene muchos ítems de factura.
     */
    public function items()
    {
        // La relación correcta es hasMany a InvoiceItem
        return $this->hasMany(InvoiceItem::class);
    }
}