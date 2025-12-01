@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detalles de la Factura #{{ $invoice->invoice_number }}</h2>
        <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-danger btn-lg">
            <i class="fa-solid fa-file-pdf"></i> Descargar PDF
        </a>
     
    </div>
       @if (!$invoice->is_cancelled)
    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancellationModal">
        <i class="fa-solid fa-ban"></i> Anular Factura
    </button>
@else
    <span class="badge bg-danger fs-5">FACTURA ANULADA</span>
    <p>Razón de Anulación: <strong>{{ $invoice->cancellation_reason }}</strong></p>
@endif

    @include('invoices.partials.invoice_template', ['invoice' => $invoice])

    <a href="{{ route('invoices.index') }}" class="btn btn-secondary mt-3">Volver a Facturas</a>
</div>
<div class="modal fade" id="cancellationModal" tabindex="-1" aria-labelledby="cancellationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancellationModalLabel">Confirmar Anulación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('invoices.cancel', $invoice) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Está a punto de anular la Factura No. **{{ $invoice->invoice_number }}**.</p>
                    <p class="text-danger">Esta acción revertirá el stock de los productos. ¡Confirme la acción y escriba la razón!</p>
                    <div class="mb-3">
                        <label for="cancellationReason" class="form-label">Razón de la Anulación</label>
                        <textarea class="form-control" id="cancellationReason" name="reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Anular y Revertir Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection