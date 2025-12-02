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
        
        // 1. VALIDACIÓN
        $request->validate([
        'client_name' => 'required|string',
        'items' => 'required|array|min:1',
        'items.*.product_id' => 'required|exists:products,id',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.discount_rate' => 'required|numeric|min:0|max:100', // Descuento individual
        'items.*.price_at_sale' => 'required|numeric|min:0', // Aseguramos que el precio venga
        'client_id_card' => 'nullable|string|max:20',
        'discount_amount' => 'required|numeric|min:0', // Total acumulado del frontend
        'total_amount' => 'required|numeric', // Total final del frontend
        'tax_amount' => 'required|numeric', // ISV total del frontend
    ]);
        
        DB::beginTransaction();

        try {
            
            // 2. GENERAR NÚMERO DE FACTURA
        $lastInvoice = Invoice::orderBy('id', 'desc')->first();
        $lastNumber = $lastInvoice ? intval(substr($lastInvoice->invoice_number, 4)) : 0;
        $newNumber = $lastNumber + 1;
        $invoiceNumber = 'FAC-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);

        // --- INICIALIZACIÓN DE ACUMULADORES ---
        $ISV_RATE = 0.15;
        $totalSubtotalSum = 0; // Subtotal BRUTO (sin descuento)
        $totalDiscountSum = 0; // Suma de descuentos por línea
        $totalTaxAmountSum = 0; // Suma de ISV por línea
        $totalFinalSum = 0; // Suma del Total Final por línea (Grand Total)

        $ID_CONSULTA = 7;
        $processedItems = []; // Array para almacenar los ítems antes de crear la factura
            

            /// 3. PROCESAR ÍTEMS, CALCULAR TOTALES y APLICAR LÓGICA DE STOCK
        foreach ($request->items as $item) {

            $product = Product::find($item['product_id']);
            $quantity = $item['quantity'];
            $price = $item['price_at_sale'];
            $discountRate = $item['discount_rate'];
            
            $product->load('categoria');

            // --- CÁLCULO DE LÍNEA (VALIDACIÓN EN SERVIDOR) ---

            $lineSubtotalGross = $quantity * $price;
            $lineDiscountAmount = round($lineSubtotalGross * ($discountRate / 100), 2);
            $lineTotalDiscountedGross = $lineSubtotalGross - $lineDiscountAmount;
            
            // Cálculo fiscal para Base Imponible (Neto sin ISV)
            $lineBaseImponible = round($lineTotalDiscountedGross / (1 + $ISV_RATE), 2);
            $lineTaxAmount = round($lineTotalDiscountedGross - $lineBaseImponible, 2);
            
            // ACUMULAR TOTALES GENERALES
            $totalSubtotalSum += $lineSubtotalGross;
            $totalDiscountSum += $lineDiscountAmount;
            $totalTaxAmountSum += $lineTaxAmount;
            $totalFinalSum += $lineTotalDiscountedGross;
            // --- LÓGICA DE STOCK ---
            $descontado_tienda = 0;
            $descontado_bodega = 0;

            if ($product->id_categoria != $ID_CONSULTA) {
                
                $totalStock = $product->stock_tienda + $product->stock_bodega;

                if ($totalStock < $quantity) {
                    DB::rollBack();
                    return back()->with('error', "Stock total insuficiente para {$product->name}. Disponible: {$totalStock}")->withInput();
                }

                $stock_a_descontar_de_tienda = min($quantity, $product->stock_tienda);
                $product->stock_tienda -= $stock_a_descontar_de_tienda;
                $descontado_tienda = $stock_a_descontar_de_tienda;

                $remaining_quantity = $quantity - $stock_a_descontar_de_tienda;

                if ($remaining_quantity > 0) {
                    $stock_a_descontar_de_bodega = $remaining_quantity;
                    $product->stock_bodega -= $stock_a_descontar_de_bodega;
                    $descontado_bodega = $stock_a_descontar_de_bodega;
                }

                $product->save();
            }

            // Almacenar el item para la creación masiva
            $processedItems[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price_at_sale' => $price,
                'discount_rate' => $discountRate, // ✨ ESTE ES EL CAMPO QUE FALTABA
                'discount_amount' => $lineDiscountAmount,
                'line_total' => $lineTotalDiscountedGross, // Total de la línea YA DESCONTADO (con ISV)
                'stock_tienda_descontado' => $descontado_tienda,
                'stock_bodega_descontado' => $descontado_bodega,
            ];
        }

        // 4. Crear la Cabecera (con los totales ya calculados)
        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'client_name' => $request->client_name,
            'client_id_card' => $request->client_id_card,
            'subtotal' => $totalSubtotalSum,
            'discount_amount' => $totalDiscountSum, // Suma de todos los descuentos de línea
            'tax_amount' => $totalTaxAmountSum,
            'total_amount' => $totalFinalSum,
            'date' => now(),
        ]);

        // 5. Crear los detalles (Items)
        $invoice->items()->createMany($processedItems);

        // 6. CONFIRMAR TRANSACCIÓN
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