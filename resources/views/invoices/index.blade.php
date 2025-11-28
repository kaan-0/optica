@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Facturas</h2>
        
        {{-- BOTÓN DE CREAR NUEVA FACTURA --}}
        <a class="btn btn-primary btn-lg" href="{{ route('invoices.create') }}">
            <i class="fa-solid fa-receipt"></i> **Crear Nueva Factura**
        </a>
    </div>
    
    @if (session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif


    <div class="table-responsive mt-4">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>No. Factura</th>
                    <th>Cliente</th>
                    <th>DNI/RTN</th>
                    <th class="text-end">Total</th>
                    <th>Fecha</th>
                    <th width="150px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoices as $invoice)
                <tr>
                    <td>{{ $invoice->invoice_number }}</td>
                    <td>{{ $invoice->client_name }}</td>
                    <td>{{ $invoice->client_id_card ?? 'N/A' }}</td>
                    <td class="text-end">L {{ number_format($invoice->total_amount, 2) }}</td>
                    <td>{{ $invoice->date }}</td>
                    <td>
                        <div class="d-flex gap-1">
                            {{-- 1. Ver detalles (abre invoices.show) --}}
                            <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-info" title="Ver Detalles">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            {{-- 2. Descargar PDF (abre invoices.download) --}}
                            <a href="{{ route('invoices.download', $invoice) }}" class="btn btn-sm btn-danger" title="Descargar PDF">
                                <i class="fa-solid fa-file-pdf"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Enlaces de paginación --}}
    <div class="d-flex justify-content-center">
        {{ $invoices->links() }}
    </div>

</div>
@endsection