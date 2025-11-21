@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Agregar nuevo producto</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-primary btn-sm" href="{{ route('products.index') }}">
                <i class="fa fa-arrow-left"></i> Regresar
            </a>
        </div>
    </div>
</div>

@if ($errors->any())
<div class="alert alert-danger">
    <strong>Vaya!</strong> Algo salió mal.<br><br>
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form action="{{ route('products.store') }}" method="POST">
    @csrf

    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Código:</strong>
                <input type="text" name="product_code" class="form-control" placeholder="Codigo">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Nombre:</strong>
                <input type="text" name="name" class="form-control" placeholder="Nombre">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Detalle:</strong>
                <textarea class="form-control" style="height:25px" name="detail" placeholder="Detalle"></textarea>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Marca:</strong>
                <textarea class="form-control" style="height:25px" name="marca" placeholder="Marca"></textarea>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Material:</strong>
                <textarea class="form-control" style="height:25px" name="material" placeholder="Material"></textarea>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Color:</strong>
                <textarea class="form-control" style="height:25px" name="color" placeholder="Color"></textarea>
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Stock Tienda:</strong>
                <input type="number" name="stock_tienda" class="form-control" placeholder="Stock Tienda">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Stock Bodega:</strong>
                <input type="number" name="stock_bodega" class="form-control" placeholder="Stock Bodega">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Precio venta:</strong>
                <input type="number" name="price" class="form-control" placeholder="Precio">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div class="form-group">
                <strong>Precio Compra:</strong>
                <input type="number" name="precio_compra" class="form-control" placeholder="Precio">
            </div>
        </div>
        <div class="col-xs-12 col-sm-12 col-md-12 text-center">
            <button type="submit" class="btn btn-primary btn-sm mb-3 mt-2">
                <i class="fa-solid fa-floppy-disk"></i> Enviar
            </button>
        </div>
    </div>
</form>

@endsection