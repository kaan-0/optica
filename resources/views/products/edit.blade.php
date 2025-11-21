@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb d-flex justify-content-between align-items-center mb-3">
        <h2>Editar Producto: {{ $product->name }}</h2>
        <a class="btn btn-primary btn-sm" href="{{ route('products.index') }}">
            <i class="fa fa-arrow-left"></i> Regresar
        </a>
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

<form action="{{ route('products.update', $product->id) }}" method="POST">
    @csrf
    @method('PUT') {{-- Simula el método PUT/PATCH para la actualización --}}

    <div class="row">

        {{-- Código --}}
        <div class="col-md-12">
            <div class="form-group">
                <strong>Código:</strong>
                <input type="text" name="product_code" value="{{ $product->product_code }}" class="form-control" placeholder="Código" required>
            </div>
        </div>

        {{-- Nombre --}}
        <div class="col-md-12">
            <div class="form-group">
                <strong>Nombre:</strong>
                <input type="text" name="name" value="{{ $product->name }}" class="form-control" placeholder="Nombre" required>
            </div>
        </div>

        {{-- Detalle --}}
        <div class="col-md-12">
            <div class="form-group">
                <strong>Detalle:</strong>
                <textarea name="detail" class="form-control" style="height:25px" placeholder="Detalle">{{ $product->detail }}</textarea>
            </div>
        </div>

        {{-- Categoría --}}
        <div class="col-md-12">
            <div class="form-group">
                <strong>Categoría:</strong>
                <select name="id_categoria" id="id_categoria" class="form-control" required>
                    <option value="">Seleccione categoría</option>
                    {{-- Usa el helper 'selected' para precargar la opción correcta --}}
                    <option value="1" {{ $product->id_categoria == 1 ? 'selected' : '' }}>Aros</option>
                    <option value="2" {{ $product->id_categoria == 2 ? 'selected' : '' }}>Lentes</option>
                    <option value="3" {{ $product->id_categoria == 3 ? 'selected' : '' }}>Lentes terminados</option>
                    <option value="4" {{ $product->id_categoria == 4 ? 'selected' : '' }}>Accesorios</option>
                    <option value="5" {{ $product->id_categoria == 5 ? 'selected' : '' }}>Soluciones de limpieza</option>
                    <option value="6" {{ $product->id_categoria == 6 ? 'selected' : '' }}>Estuches</option>
                </select>
            </div>
        </div>

        {{-- STOCK --}}
        <div class="col-md-6">
            <div class="form-group">
                <strong>Stock Tienda:</strong>
                <input type="number" name="stock_tienda" value="{{ $product->stock_tienda }}" class="form-control" placeholder="Stock Tienda" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <strong>Stock Bodega:</strong>
                <input type="number" name="stock_bodega" value="{{ $product->stock_bodega }}" class="form-control" placeholder="Stock Bodega" required>
            </div>
        </div>

        {{-- PRECIOS --}}
        <div class="col-md-6">
            <div class="form-group">
                <strong>Precio venta:</strong>
                <input type="number" name="precio_venta" value="{{ $product->precio_venta }}" class="form-control" placeholder="Precio de venta" step="0.01" required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <strong>Precio compra:</strong>
                <input type="number" name="precio_compra" value="{{ $product->precio_compra }}" class="form-control" placeholder="Precio de compra" step="0.01" required>
            </div>
        </div>

        {{-- ------------------------
            DETALLES DINÁMICOS
            (Asume que el controlador pasa los detalles del producto específico
             si existen, por ejemplo, $product->detalle_aros, etc.)
        -------------------------}}

        {{-- 🔵 FORMULARIO AROS --}}
        {{-- Establecer 'display:block' si es de la categoría 1, sino 'display:none' --}}
        <div id="form_aros" class="detalle-form" style="display:{{ $product->id_categoria == 1 ? 'block' : 'none' }};">
            <h4 class="mt-4">Detalles de Aros</h4>

            <div class="form-group">
                <label>Material:</label>
                <input type="text" name="tipo_material" value="{{ $product->tipo_material ?? '' }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Forma:</label>
                <input type="text" name="forma" value="{{ $product->forma ?? '' }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Género:</label>
                <select name="genero" class="form-control">
                    <option value="">Seleccione</option>
                    {{-- Precargar el género seleccionado --}}
                    <option value="Hombre" {{ ($product->genero ?? '') == 'Hombre' ? 'selected' : '' }}>Hombre</option>
                    <option value="Mujer" {{ ($product->genero ?? '') == 'Mujer' ? 'selected' : '' }}>Mujer</option>
                    <option value="Unisex" {{ ($product->genero ?? '') == 'Unisex' ? 'selected' : '' }}>Unisex</option>
                </select>
            </div>

            <div class="form-group">
                <label>Color Frente:</label>
                <input type="text" name="color_frente" value="{{ $product->color_frente ?? '' }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Color Patilla:</label>
                <input type="text" name="color_patilla" value="{{ $product->color_patilla ?? '' }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Tamaño Puente (mm):</label>
                <input type="number" name="tam_puente" value="{{ $product->tam_puente ?? '' }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Tamaño Lente (mm):</label>
                <input type="number" name="tam_lente" value="{{ $product->tam_lente ?? '' }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Tamaño Patilla (mm):</label>
                <input type="number" name="tam_patilla" value="{{ $product->tam_patilla ?? '' }}" class="form-control">
            </div>
        </div>

        {{-- 🟧 FORMULARIO LENTES --}}
        <div id="form_lentes" class="detalle-form" style="display:{{ $product->id_categoria == 2 ? 'block' : 'none' }};">
            <h4 class="mt-4">Detalles de Lentes</h4>

            <div class="form-group">
                <label>Material:</label>
                <input type="text" name="material" value="{{ $product->material ?? '' }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Tratamiento:</label>
                <input type="text" name="tratamiento" value="{{ $product->tratamiento ?? '' }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Índice de Refracción:</label>
                <input type="text" name="indice_refraccion" value="{{ $product->indice_refraccion ?? '' }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Diámetro:</label>
                <input type="number" name="diametro" value="{{ $product->diametro ?? '' }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Diseño:</label>
                <input type="text" name="diseno" value="{{ $product->diseno ?? '' }}" class="form-control">
            </div>
        </div>

        {{-- 🟥 FORMULARIO LENTES TERMINADOS --}}
        <div id="form_lentes_terminados" class="detalle-form" style="display:{{ $product->id_categoria == 3 ? 'block' : 'none' }};">
            <h4 class="mt-4">Detalles de Lentes Terminados</h4>

            <div class="form-group">
                <label>Esfera:</label>
                <input type="text" name="esfera" value="{{ $product->esfera ?? '' }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Cilindro:</label>
                <input type="text" name="cilindro" value="{{ $product->cilindro ?? '' }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Eje:</label>
                <input type="number" name="eje" value="{{ $product->eje ?? '' }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Diámetro:</label>
                <input type="number" name="diametro" value="{{ $product->diametro ?? '' }}" class="form-control">
            </div>

            <div class="form-group">
                <label>Material:</label>
                <input type="text" name="material" value="{{ $product->material ?? '' }}" class="form-control">
            </div>
        </div>

        {{-- ENVIAR --}}
        <div class="col-md-12 text-center mt-4">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-floppy-disk"></i> Actualizar Producto
            </button>
        </div>

    </div>
</form>

{{-- ============================
    SCRIPTS PARA FORMULARIOS
============================ --}}
<script>
document.getElementById("id_categoria").addEventListener("change", function () {

    document.querySelectorAll('.detalle-form')
            .forEach(f => f.style.display = 'none');

    let categoria = this.value;

    if (categoria == "1") document.getElementById("form_aros").style.display = 'block';
    if (categoria == "2") document.getElementById("form_lentes").style.display = 'block';
    if (categoria == "3") document.getElementById("form_lentes_terminados").style.display = 'block';

});
</script>

@endsection