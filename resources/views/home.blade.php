{{-- @extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('Bienvenido!') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">{{ __('Resumen General') }}</h5>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <h3 class="mb-4">¡Bienvenido! </h3>
                    

                    {{-- FILA DE TARJETAS DE RESUMEN --}}
                    <div class="row">
                        
                        {{-- CARD 1: INGRESOS EFECTIVOS (PAGADO) --}}
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card bg-success text-white shadow-lg h-100">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                                Total Ingresos
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold">
                                                {{-- Formato de moneda, ajusta 'USD' a tu moneda local si lo necesitas --}}
                                                {{ number_format($totalPaid, 2, '.', ',') }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-money-check-alt fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CARD 2: descuentos --}}
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card bg-secondary text-white shadow-lg h-100">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                                Descuentos otorgados
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold">
                                                {{ number_format($totalDiscount, 2, '.', ',') }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-tag fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CARD 3: total de facturas no canceladas --}}
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card bg-primary text-white shadow-lg h-100">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                                Total Facturas
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold">
                                                {{ number_format($totalInvoices, 0, '.', ',') }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-file-invoice fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- CARD 4: CONTEO DE productos --}}
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card bg-info text-white shadow-lg h-100">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col">
                                            <div class="text-xs font-weight-bold text-uppercase mb-1">
                                                Total Productos en Inventario
                                            </div>
                                            <div class="h5 mb-0 font-weight-bold">
                                                {{ number_format($totalProducts, 0, '.', ',') }}
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-box-open fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- FIN FILA DE TARJETAS DE RESUMEN --}}
                    
                    <hr>
                    <p>Este es un resumen de información para el mes en curso.</p>

                    {{-- <p>Utiliza la barra de navegación para acceder al listado de pacientes o a las funciones de facturación.</p> --}}

                </div>
            </div>
        </div>
    </div>
</div>
@endsection