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

@if ($message = Session::get('success'))
    <div class="alert alert-success" role="alert"> 
        {{ $message }}
    </div>
@endif

@php
    $i = ($products->currentPage() - 1) * $products->perPage();
@endphp

<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>No</th>
            <th>Name</th>
            <th>Details</th>
            <th>Stock</th>
            <th width="280px">Action</th>
        </tr>
        @foreach ($products as $product)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->detail }}</td>
            <td>{{ $product->stock_actual }}</td>
            <td>
                <form action="{{ route('products.destroy',$product->id) }}" method="POST">
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
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                            <i class="fa-solid fa-trash"></i> Borrar
                        </button>
                    @endcan
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>

{!! $products->links() !!}

@endsection