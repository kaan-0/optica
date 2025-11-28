@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Crear Nueva Factura</h2>
    
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('invoices.store') }}" method="POST">
        @csrf
        
        <div class="card mb-4">
            <div class="card-header">Detalles del Cliente</div>
            <div class="card-body row">
                <div class="col-md-6 mb-3">
                    <label for="invoice_number" class="form-label">Número de Factura</label>
                    
                    <input type="text" name="invoice_number" id="invoice_number" 
                        class="form-control" 
                        value="{{ 'FAC-' . (App\Models\Invoice::orderBy('id', 'desc')->value('invoice_number') ? intval(substr(App\Models\Invoice::orderBy('id', 'desc')->value('invoice_number'), 4)) + 1 : 1) }}" 
                        readonly>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="client_name" class="form-label">Nombre del Cliente</label>
                    <input type="text" name="client_name" id="client_name" class="form-control" required value="{{ old('client_name') }}">
                </div>
                <div class="col-md-4 mb-3">
            <label for="client_id_card" class="form-label">DNI/RTN</label>
            <input type="text" name="client_id_card" id="client_id_card" class="form-control" value="{{ old('client_id_card') }}">
        </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between">
                Productos a Facturar
                <button type="button" class="btn btn-sm btn-info" id="add-item-btn">
                    <i class="fa-solid fa-plus"></i> Añadir Producto
                </button>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Producto (ID/Nombre)</th>
                            <th width="15%">Cantidad</th>
                            <th width="15%">Precio Unitario</th>
                            <th width="15%">Total Línea</th>
                            <th width="5%">Acción</th>
                        </tr>
                    </thead>
                    <tbody id="invoice-items-body">
                        {{-- Las filas de productos irán aquí, generadas por JS --}}
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Sección de Totales (Footer) --}}
        <div class="row justify-content-end">
    <div class="col-md-4">
        <div class="card p-3">
            <p>Subtotal (Antes de Descuento): <span id="subtotal-display">0.00</span></p>

            {{-- Descuento por Porcentaje --}}
            <div class="input-group mb-2">
                <span class="input-group-text">Desc. (%)</span>
                <input type="number" step="0.01" name="discount_rate" id="discount_rate_input" class="form-control" value="0.00" min="0" max="100">
            </div>
            
            <p>Descuento (Monto): <span id="discount-display">0.00</span></p>
            
            {{-- Campo oculto para el monto de descuento que se va a la BD --}}
            <input type="hidden" name="discount_amount" id="discount_amount_input" value="0.00">

            <hr>

            {{-- NUEVOS CAMPOS FISCALES --}}
            
            <p>**Base Imponible (Gravado 15%):** <span id="taxable-base-display">0.00</span></p>
            
            <p>**ISV 15%:** <span id="tax-display">0.00</span></p>
            
            <h4>Total a Pagar: <span id="total-display">0.00</span></h4>
            
            {{-- Campos ocultos para el controlador --}}
            <input type="hidden" name="tax_amount" id="tax_amount_input" value="0.00">
            <input type="hidden" name="total_amount" id="total_amount_input" value="0.00">
        </div>
    </div>
</div>
        
        {{-- Campo oculto para el total --}}
        <input type="hidden" name="total_amount" id="total_amount_input" value="0.00">

        <button type="submit" class="btn btn-success btn-lg">
            <i class="fa-solid fa-save"></i> Generar y Guardar Factura
        </button>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const itemBody = document.getElementById('invoice-items-body');
        const addItemBtn = document.getElementById('add-item-btn');
        const totalAmountInput = document.getElementById('total_amount_input');
        const totalDisplay = document.getElementById('total-display');
        const discountRateInput = document.getElementById('discount_rate_input');
        const discountInput = document.getElementById('discount_amount_input');
        discountRateInput.addEventListener('input', calculateTotals);
        discountInput.addEventListener('input', calculateTotals);
        let itemIndex = 0;

        // URL para la búsqueda de productos (necesaria para AJAX)
        const searchUrl = "{{ route('products.search') }}";

        // --- FUNCIÓN PRINCIPAL PARA CALCULAR TOTALES CON ISV 15% ---
function calculateTotals() {
    let subtotal = 0;
    
    // 1. CALCULAR SUBTOTAL (Suma de líneas sin descuento)
    itemBody.querySelectorAll('tr').forEach(row => {
        const quantityInput = row.querySelector('.item-quantity');
        const priceInput = row.querySelector('.item-price');
        const lineTotalSpan = row.querySelector('.line-total-display');
        
        const quantity = parseFloat(quantityInput.value) || 0;
        const price = parseFloat(priceInput.value) || 0;
        const lineTotal = quantity * price;

        lineTotalSpan.textContent = lineTotal.toFixed(2);
        subtotal += lineTotal;
    });

    // --- CÁLCULO DE DESCUENTO ---
    const discountRateInput = document.getElementById('discount_rate_input'); 
    const discountAmountInput = document.getElementById('discount_amount_input'); 
    const discountDisplay = document.getElementById('discount-display');
    const taxableBaseDisplay = document.getElementById('taxable-base-display');

    const discountRate = parseFloat(discountRateInput.value) || 0;
    
    // Monto monetario del descuento
    const calculatedDiscountAmount = subtotal * (discountRate / 100);
    
    // 2. BASE IMPONIBLE (Subtotal después de descuento)
    let taxableBase = subtotal - calculatedDiscountAmount;
    taxableBase = taxableBase / 1.15;
    if (taxableBase < 0) taxableBase = 0;
    
    // --- CÁLCULO DE ISV (Impuesto sobre Ventas 15%) ---
    const ISV_RATE = 0.15;
    const taxAmount = taxableBase * ISV_RATE;
    
    // 3. TOTAL FINAL (Total a Pagar)
    const grandTotal = taxableBase + taxAmount;
    // NOTA: Esto es matemáticamente igual a: taxableBase * 1.15
    
    // 4. ACTUALIZAR DISPLAYS Y CAMPOS OCULTOS
    
    document.getElementById('subtotal-display').textContent = subtotal.toFixed(2);

    // Descuento
    discountDisplay.textContent = calculatedDiscountAmount.toFixed(2);
    discountAmountInput.value = calculatedDiscountAmount.toFixed(2);
    
    // Base Imponible y ISV
    taxableBaseDisplay.textContent = taxableBase.toFixed(2); // NUEVO DISPLAY
    
    document.getElementById('tax-display').textContent = taxAmount.toFixed(2); // Muestra ISV 15%
    document.getElementById('tax_amount_input').value = taxAmount.toFixed(2); // Guarda el MONTO del ISV
    
    // Total Final
    totalDisplay.textContent = grandTotal.toFixed(2);
    totalAmountInput.value = grandTotal.toFixed(2); // Guarda el TOTAL FINAL
}

// Asegúrate de que los inputs de descuento (rate) y cantidad/precio llamen a esta función

        // --- FUNCIÓN PARA BUSCAR PRODUCTOS (AJAX) ---
        async function searchProducts(query, dropdown) {
            if (query.length < 3) {
                dropdown.innerHTML = '';
                return;
            }

            try {
                const response = await fetch(`${searchUrl}?query=${query}`);
                const products = await response.json();
                
                dropdown.innerHTML = '';
                
                products.forEach(product => {
                    const item = document.createElement('a');
                    item.className = 'dropdown-item';
                    item.href = '#';
                    item.textContent = `[${product.product_code}] ${product.name}`;
                    
                    // Al hacer clic, se selecciona el producto
                    // DESPUÉS (CÓDIGO CORREGIDO)
                        item.addEventListener('click', (e) => {
                            e.preventDefault();
                            const row = dropdown.closest('tr');
                            
                            // Setear los valores en los campos de la fila
                            const priceValue = product.precio_venta.toFixed(2);
                            
                            row.querySelector('.item-search-input').value = product.name;
                            row.querySelector('.item-id-input').value = product.id;
                            
                            // 1. Asigna al campo visible
                            row.querySelector('.item-price').value = priceValue; 
                            
                            // 2. ASIGNA DIRECTAMENTE AL CAMPO OCULTO (¡Esta es la corrección!)
                            row.querySelector('.item-price-hidden').value = priceValue; 

                            // Ocultar el dropdown
                            dropdown.innerHTML = '';
                            dropdown.style.display = 'none'; 
                            
                            calculateTotals(); 
                        });
                    dropdown.appendChild(item);
                });
                dropdown.style.display = products.length > 0 ? 'block' : 'none';

            } catch (error) {
                console.error("Error al buscar productos:", error);
            }
        }


        // --- FUNCIÓN PARA AÑADIR NUEVA FILA ---
        function addNewItem() {
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <div class="position-relative">
                        <input type="text" class="form-control item-search-input" placeholder="Buscar producto..." data-index="${itemIndex}">
                        <div class="dropdown-menu show" style="position:absolute; width:100%;" id="product-dropdown-${itemIndex}">
                            </div>
                        <input type="hidden" name="items[${itemIndex}][product_id]" class="item-id-input">
                        <input type="hidden" name="items[${itemIndex}][price_at_sale]" class="item-price-hidden">
                    </div>
                </td>
                <td>
                    <input type="number" name="items[${itemIndex}][quantity]" class="form-control item-quantity" value="1" min="1" required>
                </td>
                <td>
                    <input type="number" step="0.01" class="form-control item-price" value="0.00" readonly>
                </td>
                <td>
                    L <span class="line-total-display">0.00</span>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-item-btn">
                        <i class="fa-solid fa-trash"></i>
                    </button>
                </td>
            `;

            itemBody.appendChild(newRow);

            // 1. Manejo de Eventos de la Fila
            newRow.querySelectorAll('.item-quantity, .item-price').forEach(input => {
                input.addEventListener('input', calculateTotals);
            });
            
            // 2. Manejo del Botón de Eliminar
            newRow.querySelector('.remove-item-btn').addEventListener('click', function() {
                newRow.remove();
                calculateTotals();
            });

            // 3. Manejo del Buscador (AJAX)
            const searchInput = newRow.querySelector('.item-search-input');
            const dropdown = document.getElementById(`product-dropdown-${itemIndex}`);
            
            // Replicar el valor del input de precio en el campo oculto antes de enviar
            newRow.querySelector('.item-price').addEventListener('input', function() {
                newRow.querySelector('.item-price-hidden').value = this.value;
                calculateTotals();
            });

            searchInput.addEventListener('input', function() {
                // Muestra la lista de búsqueda al escribir
                searchProducts(this.value, dropdown);
            });

            searchInput.addEventListener('focus', function() {
                // Muestra la lista al enfocar, si hay texto
                 searchProducts(this.value, dropdown);
            });
            
            // Ocultar dropdown al hacer clic fuera
            document.addEventListener('click', (e) => {
                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });


            itemIndex++;
            calculateTotals(); // Recalcular después de agregar una fila
        }

        // --- LISTENERS GLOBALES ---
        addItemBtn.addEventListener('click', addNewItem);

        // Añadir la primera fila al cargar
        addNewItem();
    });
</script>

@endsection