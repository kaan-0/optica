@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detalles de la Factura #{{ $invoice->invoice_number }}</h2>
        <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-danger btn-lg">
            <i class="fa-solid fa-file-pdf"></i> Descargar PDF
        </a>
    </div>

    @include('invoices.partials.invoice_template', ['invoice' => $invoice])

    <a href="{{ route('invoices.index') }}" class="btn btn-secondary mt-3">Volver a Facturas</a>
</div>
@endsection