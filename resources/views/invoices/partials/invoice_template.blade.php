<?php
// Obtener la ruta absoluta del archivo en el servidor
$path = public_path('images/logo.png'); 
// Verificar si el archivo existe para evitar errores
if (file_exists($path)) {
    // Leer el archivo y codificarlo en Base64
    $type = pathinfo($path, PATHINFO_EXTENSION);
    $data = file_get_contents($path);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
} else {
    $base64 = ''; // Dejar vacío si la imagen no se encuentra
}
?>

<style>
    /* Estilo para reducir la fuente base y el espacio entre líneas */
    body {
        font-size: 10px; /* Reducir la fuente del cuerpo */
        line-height: 1.2;
    }

    /* Reducir el padding y margen de las cartas (card, header, body) */
    .invoice-container {
        padding: 10px !important; /* Reducir el padding del contenedor principal */
    }

    /* Reducir el margen de las secciones principales */
    .header, .client-info, .mb-4 {
        margin-bottom: 5px !important; /* Margen inferior reducido */
    }
    
    /* Reducir el tamaño de la fuente para el encabezado de la empresa/dirección */
    .header p {
        font-size: 9px; 
        margin-bottom: 2px; /* Espacio entre líneas de dirección */
    }

    /* Estilos específicos para la tabla */
    .table-bordered {
        font-size: 9px; /* Fuente más pequeña para la tabla */
    }
    .table-bordered th, .table-bordered td {
        padding: 2px 4px !important; /* Relleno (padding) más pequeño en celdas */
    }

    /* Reducir el tamaño de los títulos de secciones */
    .client-info h5, .header h4 {
        font-size: 12px;
        margin-bottom: 3px !important;
    }
    
    /* Reducir el espaciado y fuente de la tabla de totales */
    .totals .table-sm td, .totals .table-sm th {
        padding: 2px 4px !important; 
        font-size: 10px;
    }
    .totals .table-sm {
        margin-bottom: 0 !important;
    }
    /* Reducir el margen de la línea horizontal */
hr {
    margin-top: 5px; /* Reducir el espacio antes de la línea */
    margin-bottom: 5px; /* Reducir el espacio después de la línea */
}

/* Reducir aún más el margen inferior de la tabla */
.table-bordered.mb-4 {
    margin-bottom: 5px !important; 
}

/* Reducir el margen del pie de página */
.footer.mt-5 {
    margin-top: 10px !important; /* Reducir de 5 (mt-5) a un valor fijo más pequeño */
}
</style>

<div class="invoice-container border p-4" style="position: relative;">
    
    <div class="header row mb-4">
    <div class="col-6">
        @if ($base64)
            <img src="{{ $base64 }}" 
                 alt="Logo Óptica San José" 
                 style="max-width: 200px; height: auto;">
        @else
            <h3>Centro Oftalmológico y Óptica San José</h3>
        @endif
        
        <p>Dirección: Centro Comercial Unicentro, Nacaome, Valle</p>
        <p>RTN:  17011988016903</p>
        <p>Teléfono: 9389-7445</p>
    </div>
    <div class="col-6 text-end">
        <h4>COMPROBANTE</h4>
        <p style="font-size: 0.8rem; color: gray;">DOCUMENTO NO FISCAL</p>
        <p>No: <strong>{{ $invoice->invoice_number }}</strong></p>
        <p>Fecha: {{ $invoice->date }}</p>
        
    </div>
    
</div>
@if ($invoice->is_cancelled)
        <div style="
        position: absolute; 
        top: 150px; 
        left: 15%; 
        width: 70%; 
        height: 50px; 
        text-align: center; 
        z-index: 1000; 
        opacity: 0.5;
        transform: rotate(-25deg); 
       
        padding: 10px;
        font-size: 4em; 
        font-weight: bold; 
        color: red;">
        FACTURA ANULADA
    </div>
    @endif

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
            <th class="text-end" width="15%">Precio Unit.</th>
            <th class="text-end" width="10%">Desc. (%)</th> <th class="text-end" width="15%">Desc. (Monto)</th> <th class="text-end" width="15%">Total Línea</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($invoice->items as $item)
        <tr>
            <td>{{ $item->product->product_code ?? 'N/A' }}</td>
            <td>{{ $item->product->name ?? 'Producto Eliminado' }}</td>
            <td class="text-end">{{ $item->quantity }}</td>
            <td class="text-end">L {{ number_format($item->price_at_sale, 2) }}</td>
            
            {{-- Campos nuevos --}}
            <td class="text-end">{{ number_format($item->discount_rate ?? 0, 2) }}%</td> 
            <td class="text-end">L {{ number_format($item->discount_amount ?? 0, 2) }}</td> 

            {{-- line_total ahora es el precio ya descontado (con ISV) --}}
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
                <td>Subtotal Bruto (Sin Desc.):</td>
                <td>L {{ number_format($invoice->subtotal, 2) }}</td>
            </tr>
            @if ($invoice->discount_amount > 0)
            <tr>
                <td>**(-) Descuento Total:**</td>
                <td>**L ({{ number_format($invoice->discount_amount, 2) }})**</td>
            </tr>
            @endif
            
            {{-- Cálculo de la Base Imponible con los totales de la cabecera --}}
            @php
                // La Base Imponible es el Subtotal Bruto - Descuento Total, quitando el ISV
                $taxableBase = ($invoice->subtotal - $invoice->discount_amount) / 1.15;
            @endphp

            <tr class="table-secondary">
                <td>**Importe Gravado 15% (Base Imponible):**</td>
                <td>**L {{ number_format($taxableBase, 2) }}**</td>
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