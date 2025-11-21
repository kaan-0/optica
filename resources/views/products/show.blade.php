@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Mostrar Producto</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary" href="{{ route('products.index') }}">Regresar</a>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Codigo:</strong>
            {{ $product->product_code }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Nombre:</strong>
            {{ $product->name }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Detalles:</strong>
            {{ $product->detail }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Marca:</strong>
            {{ $product->marca }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Material:</strong>
            {{ $product->material }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Color:</strong>
            {{ $product->color }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Stock Tienda:</strong>
            {{ $product->stock_tienda }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Stock Bodega:</strong>
            {{ $product->stock_bodega }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Precio venta:</strong>
            {{ $product->price }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Precio compra:</strong>
            {{ $product->precio_compra }}
        </div>
    </div>
    <div class="col-xs-12 col-sm-12 col-md-12">
        <div class="form-group">
            <strong>Total:</strong>
            {{ $product->stock_bodega + $product->stock_tienda }}
        </div>
    </div>
</div>

@endsection