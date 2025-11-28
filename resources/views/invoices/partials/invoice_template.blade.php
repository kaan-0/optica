{{-- resources/views/invoices/partials/invoice_template.blade.php --}}

<div class="invoice-container border p-4">
    <div class="header row mb-4">
        <div class="col-6">
            <h3>Óptica San José</h3>
            {{-- <p>RTN: 123456789</p> --}}
            <p>Dirección: Unicentro, Nacaome, Valle</p>
        </div>
        <div class="col-6 text-end">
            <h4>FACTURA</h4>
            <p>No: <strong>{{ $invoice->invoice_number }}</strong></p>
            <p>Fecha: {{ $invoice->date }}</p>
        </div>
    </div>

    <hr>

    <div class="client-info mb-4">
        <h5>Datos del Cliente</h5>
        <p>Nombre: <strong>{{ $invoice->client_name }}</strong></p>
        <p>DNI/RTN: {{ $invoice->client_id_card ?? 'N/A' }}</p>
    </div>

    <table class="table table-bordered table-sm mb-4">
        <thead>
            <tr>
                <th>Cód.</th>
                <th>Descripción</th>
                <th class="text-end" width="10%">Cant.</th>
                <th class="text-end" width="15%">Precio</th>
                <th class="text-end" width="15%">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
            <tr>
                <td>{{ $item->product->product_code ?? 'N/A' }}</td>
                <td>{{ $item->product->name ?? 'Producto Eliminado' }}</td>
                <td class="text-end">{{ $item->quantity }}</td>
                <td class="text-end">L {{ number_format($item->price_at_sale, 2) }}</td>
                <td class="text-end">L {{ number_format($item->line_total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals row justify-content-end">
        <div class="col-md-6"></div> {{-- Espacio vacío para centrar --}}
        <div class="col-md-6 col-lg-4">
            <table class="table table-sm text-end">
                <tr>
                    <td>Subtotal Bruto:</td>
                    <td>L {{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if ($invoice->discount_amount > 0)
                <tr>
                    <td>**(-) Descuento (Monto):**</td>
                    <td>**L ({{ number_format($invoice->discount_amount, 2) }})**</td>
                </tr>
                @endif
                
                {{-- NUEVA LÍNEA CLAVE: BASE IMPONIBLE (Subtotal - Descuento) --}}
                <tr class="table-secondary">
                    <td>**Importe Gravado 15% (Base Imponible):**</td>
                    <td>**L {{ number_format(($invoice->subtotal - $invoice->discount_amount)/1.15, 2) }}**</td>
                </tr>
                
                <tr>
                    <td>(+) ISV (15%):</td>
                    <td>L {{ number_format($invoice->tax_amount, 2) }}</td>
                </tr>
                <tr class="table-dark">
                    <th>Total a Pagar:</th>
                    <th>L {{ number_format($invoice->total_amount, 2) }}</th>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="footer text-center mt-5">
        <p>¡Gracias por su compra!</p>
    </div>
</div>
    
</div>