<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceController extends Controller
{

    public function index()
{
    // Obtener todas las facturas paginadas para evitar cargar demasiados datos a la vez
    $invoices = Invoice::latest()->paginate(20); 

    return view('invoices.index', compact('invoices'));
}
    public function create()
    {
        // Esto cargará el formulario de creación (Paso 8)
        return view('invoices.create');
    }

    public function store(Request $request)
    {
        
        $request->validate([
            'client_name' => 'required|string',
            //'invoice_number' => 'required|unique:invoices,invoice_number',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'client_id_card' => 'nullable|string|max:20',
            'discount_rate' => 'nullable|numeric|min:0|max:100',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric',
            'tax_amount' => 'required|numeric',
            
        ]);
        
        DB::beginTransaction();

        try {
            // 1. CREAR LA FACTURA (CABECERA)
            // Lógica simple para calcular total (ajusta si tienes impuestos o descuentos)
            $lastInvoice = Invoice::orderBy('id', 'desc')->first();

            $lastNumber = $lastInvoice 
                      ? intval(substr($lastInvoice->invoice_number, 4)) // Si existe, toma el número después del prefijo 'FAC-'
                      : 0; // Si no existe, inicia en 0
        
        $newNumber = $lastNumber + 1;
        
        // Formatear: Rellenar con ceros a la izquierda (ej: 0001, 0010, 0100)
        $invoiceNumber = 'FAC-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        // --- CÁLCULO DE TOTALES (Servidor) ---
        $ISV_RATE = 0.15;

        $subtotal = collect($request->items)->sum(fn($item) => $item['price_at_sale'] * $item['quantity']);
        
        // 2. USAR VALORES CALCULADOS DEL JS (enviados en campos ocultos)
        $discount_amount = (float)$request->discount_amount;
                // 3. BASE IMPONIBLE (Total Gravado)
            $taxable_base = $subtotal - $discount_amount;
            $taxable_base = $taxable_base / 1.15;
            if ($taxable_base < 0) $taxable_base = 0;
            
            // 4. ISV 15%
            $tax_amount = round($taxable_base * $ISV_RATE, 2); // Redondeo a 2 decimales para precisión fiscal
            
            // 5. TOTAL FINAL
            $grand_total = $taxable_base + $tax_amount;
            
           $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'client_name' => $request->client_name,
            'client_id_card' => $request->client_id_card,
            'subtotal' => $subtotal, // Subtotal recalculado
            'discount_amount' => $discount_amount, // <-- MONTO CALCULADO PORCENTUAL
            'tax_amount' => $tax_amount,
            'total_amount' => $grand_total, // <-- TOTAL FINAL DEL JS
            'date' => now(),
        ]);

            $ID_CONSULTA=7;
            

            // 2. PROCESAR ÍTEMS Y APLICAR LÓGICA DE STOCK INTELIGENTE
            foreach ($request->items as $item) {

                $product = Product::find($item['product_id']);
                $quantity = $item['quantity'];
                $price = $item['price_at_sale'];

                //obtener categoria para no rebajar consulta de inventario
                $product->load('categoria');


                $descontado_tienda = 0;
                $descontado_bodega = 0;
                
                $totalStock = $product->stock_tienda + $product->stock_bodega;

                if($product->id_categoria == $ID_CONSULTA){
                    //no rebajamos consulta de inventario

                }else{

                    if ($totalStock < $quantity) {
                    DB::rollBack();
                    return back()->with('error', "Stock total insuficiente para {$product->name}. Disponible: {$totalStock}")->withInput();
                }


                // Lógica de Prioridad: Tienda -> Bodega
                
                // 1. Descontar de la Tienda
                $stock_a_descontar_de_tienda = min($quantity, $product->stock_tienda);
                $product->stock_tienda -= $stock_a_descontar_de_tienda;
                $descontado_tienda = $stock_a_descontar_de_tienda;

                $remaining_quantity = $quantity - $stock_a_descontar_de_tienda;

                // 2. Descontar de la Bodega (si queda algo pendiente)
                if ($remaining_quantity > 0) {
                    $stock_a_descontar_de_bodega = $remaining_quantity;
                    $product->stock_bodega -= $stock_a_descontar_de_bodega;
                    $descontado_bodega = $stock_a_descontar_de_bodega;
                }

                // Guardar los cambios de stock
                $product->save();

                }

                

                // 3. CREAR EL DETALLE DE LA FACTURA (InvoiceItem)
                $invoice->items()->create([
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'price_at_sale' => $price,
                    'line_total' => $quantity * $price,
                    'stock_tienda_descontado' => $descontado_tienda,
                    'stock_bodega_descontado' => $descontado_bodega,
                ]);
            }

            // 4. CONFIRMAR TRANSACCIÓN
            DB::commit();

            return redirect()->route('invoices.show', $invoice)->with('success', 'Factura registrada y stock actualizado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar la factura: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Invoice $invoice)
{
    // Carga los ítems y los productos relacionados
    $invoice->load('items.product'); 

    // Muestra la vista con los datos de la factura
    return view('invoices.show', compact('invoice'));
}

public function download(Invoice $invoice)
{
    // Carga la data necesaria
    $invoice->load('items.product');
    
    // Generar la instancia PDF usando una vista específica (o la misma show)
    $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

    // Nombre del archivo a descargar
    $fileName = 'Factura-' . $invoice->invoice_number . '-' . now()->format('Ymd') . '.pdf';

    // Retorna el PDF para la descarga
    return $pdf->download($fileName);
}

public function cancel (Request $request, Invoice $invoice){

    if ($invoice->is_cancelled) {
        return back()->with('error', 'Esta factura ya está anulada.');
    }

    $request->validate([
        'reason' => 'required|string|max:200'
    ]);

    DB::beginTransaction();
    try {
        // 2. REVERSAR EL STOCK
        // Recorre todos los ítems de la factura
        foreach ($invoice->items as $item) {
            $product = $item->product; 

            if (!$product) {
                 // Si el producto fue eliminado, registra el error pero continúa.
                 // Para fines de stock, esta línea podría generar un error si el producto no existe.
                 continue; 
            }

            // Devolver stock a la tienda
            $product->stock_tienda += $item->stock_tienda_descontado;
            
            // Devolver stock a la bodega
            $product->stock_bodega += $item->stock_bodega_descontado;
            
            $product->save();
            
            // Opcional: Para mayor trazabilidad, puedes actualizar el item para marcarlo como revertido.
        }

        // 3. MARCAR LA FACTURA COMO ANULADA
        $invoice->is_cancelled = true;// o 1
        $invoice->cancellation_reason = $request->reason;
        $invoice->save();

        DB::commit();
        
        return redirect()->route('invoices.show', $invoice)->with('success', 
            "Factura #{$invoice->invoice_number} ha sido ANULADA. El stock ha sido revertido correctamente."
        );

    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Error al anular la factura y revertir stock: ' . $e->getMessage());
    }

}


    
    
}