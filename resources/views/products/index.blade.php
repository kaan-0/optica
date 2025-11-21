@extends('layouts.app')

@section('content')
<div class="row"> 
    <div class="col-lg-12 margin-tb d-flex justify-content-between align-items-center mb-3"> 
        <h2>Productos</h2> 
        @can('product-create') 
        <a class="btn btn-success btn-sm" href="{{ route('products.create') }}"> 
            <i class="fa fa-plus"></i> Crear Nuevo Producto 
        </a> 
        @endcan 
    </div> 
</div>

<form action="{{ route('products.index') }}" method="GET" class="mb-4">
    <div class="input-group">
        <input 
            type="text" 
            name="search" 
            class="form-control" 
            placeholder="Buscar por Código, Nombre o Detalle..." 
            value="{{ $search ?? '' }}" 
        />
        <button class="btn btn-outline-secondary" type="submit">
            <i class="fa-solid fa-magnifying-glass"></i> Buscar
        </button>
        {{-- Botón para limpiar la búsqueda --}}
        @if (isset($search) && $search)
        <a href="{{ route('products.index') }}" class="btn btn-outline-danger">
            <i class="fa-solid fa-xmark"></i> Limpiar
        </a>
        @endif
    </div>
</form>
    @if ($message = Session::get('success')) 
    <div class="alert alert-success" role="alert"> {{ $message }} </div> 
    @endif 
    @php $i = ($products->currentPage() - 1) * $products->perPage();
     @endphp

<div class="table-responsive">
    <table class="table table-bordered table-striped align-middle">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Código</th>
                <th>Nombre</th>
                <th>Categoría</th>
                <th>Detalles</th>
                <th>Stock Tienda</th>
                <th>Stock Bodega</th>
                <th>Precio Venta</th>
                <th>Precio Compra</th>
                <th>Total</th>
                <th width="250px">Acciones</th>
            </tr>
        </thead>

        <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $product->product_code }}</td>
                <td>{{ $product->name }}</td>

                {{-- Nueva columna de categoría --}}
                <td>
                    {{ $product->categoria->nombre ?? 'Sin categoría' }}
                </td>

                <td>{{ $product->detail }}</td>

                <td>{{ $product->stock_tienda }}</td>
                <td>{{ $product->stock_bodega }}</td>

                <td>L {{ number_format($product->precio_venta, 2) }}</td>
                <td>L {{ number_format($product->precio_compra, 2) }}</td>

                <td><strong>{{ $product->stock_tienda + $product->stock_bodega }}</strong></td>

                <td>
                    <form action="{{ route('products.destroy', $product->id) }}" method="POST">
                        <a class="btn btn-info btn-sm" href="{{ route('products.show',$product->id) }}">
                            <i class="fa-solid fa-list"></i> Mostrar
                        </a>

                        @can('product-edit')
                            <a class="btn btn-primary btn-sm" href="{{ route('products.edit',$product->id) }}">
                                <i class="fa-solid fa-pen-to-square"></i> Editar
                            </a>
                        @endcan

                        @csrf
                        @method('DELETE')

                        @can('product-delete')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro deseas eliminar?')">
                            <i class="fa-solid fa-trash"></i> Borrar
                        </button>
                        @endcan
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>


@endsection
