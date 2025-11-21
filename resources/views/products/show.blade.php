@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb d-flex justify-content-between align-items-center mb-3">
        <h2>Detalles del Producto</h2>
        <a class="btn btn-primary btn-sm" href="{{ route('products.index') }}">
            <i class="fa fa-arrow-left"></i> Regresar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">{{ $product->name }} ({{ $product->product_code }})</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    {{-- Columna 1: Información General --}}
                    <div class="col-md-6">
                        <p><strong>Código:</strong> {{ $product->product_code }}</p>
                        <p><strong>Nombre:</strong> {{ $product->name }}</p>
                        <p><strong>Categoría:</strong> {{ $product->categoria->nombre ?? 'Sin categoría' }}</p>
                        <p><strong>Detalle:</strong> {{ $product->detail }}</p>
                        <p><strong>Precio Venta:</strong> L {{ number_format($product->precio_venta, 2) }}</p>
                        <p><strong>Precio Compra:</strong> L {{ number_format($product->precio_compra, 2) }}</p>
                    </div>

                    {{-- Columna 2: Stock y Total --}}
                    <div class="col-md-6">
                        <p><strong>Stock Tienda:</strong> {{ $product->stock_tienda }}</p>
                        <p><strong>Stock Bodega:</strong> {{ $product->stock_bodega }}</p>
                        <p><strong>Total en Stock:</strong> <strong>{{ $product->stock_tienda + $product->stock_bodega }}</strong></p>
                    </div>
                </div>

                <hr>

                

                {{-- Detalles Dinámicos --}}
                <h5 class="mt-4">Detalles Específicos de la Categoría</h5>

                @if ($product->id_categoria == 1) {{-- Aros --}}
                    {{-- Verifica que la relación exista antes de acceder a sus propiedades --}}
                    @php $detalles = $product->detallesAros; @endphp 

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Material:</strong> {{ $detalles?->tipo_material ?? 'N/A' }}</p>
                            <p><strong>Forma:</strong> {{ $detalles?->forma ?? 'N/A' }}</p>
                            <p><strong>Género:</strong> {{ $detalles?->genero ?? 'N/A' }}</p>
                            <p><strong>Color Frente:</strong> {{ $detalles?->color_frente ?? 'N/A' }}</p>
                            <p><strong>Color Patilla:</strong> {{ $detalles?->color_patilla ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tamaño Puente:</strong> {{ $detalles?->tam_puente ? $detalles->tam_puente . ' mm' : 'N/A' }}</p>
                            <p><strong>Tamaño Lente:</strong> {{ $detalles?->tam_lente ? $detalles->tam_lente . ' mm' : 'N/A' }}</p>
                            <p><strong>Tamaño Patilla:</strong> {{ $detalles?->tam_patilla ? $detalles->tam_patilla . ' mm' : 'N/A' }}</p>
                        </div>
                    </div>

                @elseif ($product->id_categoria == 2) {{-- Lentes --}}
                    @php $detalles = $product->detallesLentes; @endphp 

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Material:</strong> {{ $detalles?->material ?? 'N/A' }}</p>
                            <p><strong>Tratamiento:</strong> {{ $detalles?->tratamiento ?? 'N/A' }}</p>
                            <p><strong>Índice de Refracción:</strong> {{ $detalles?->indice_refraccion ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Diámetro:</strong> {{ $detalles?->diametro ? $detalles->diametro . ' mm' : 'N/A' }}</p>
                            <p><strong>Diseño:</strong> {{ $detalles?->diseno ?? 'N/A' }}</p>
                        </div>
                    </div>

                @elseif ($product->id_categoria == 3) {{-- Lentes Terminados --}}
                    @php $detalles = $product->detallesLentesTerminados; @endphp 

                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Esfera:</strong> {{ $detalles?->esfera ?? 'N/A' }}</p>
                            <p><strong>Cilindro:</strong> {{ $detalles?->cilindro ?? 'N/A' }}</p>
                            <p><strong>Eje:</strong> {{ $detalles?->eje ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Diámetro:</strong> {{ $detalles?->diametro ? $detalles->diametro . ' mm' : 'N/A' }}</p>
                            <p><strong>Material:</strong> {{ $detalles?->material ?? 'N/A' }}</p>
                        </div>
                    </div>

                @else
                    <p>No hay detalles específicos para esta categoría.</p>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection