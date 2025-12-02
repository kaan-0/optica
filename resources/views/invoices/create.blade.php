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
                                <th width="10%">Cantidad</th>
                                <th width="15%">Precio Unitario</th>
                                <th width="10%">Desc. (%)</th> <th width="15%">Total Línea</th>
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
            <p>Subtotal **Bruto** (Sin Desc.): <span id="subtotal-gross-display">0.00</span></p>

            <p>Descuento **Total:** <span id="discount-display">0.00</span></p>
            
            <input type="hidden" name="discount_amount" id="discount_amount_input" value="0.00">

            <hr>

            <p>**Base Imponible (Gravado 15%):** <span id="taxable-base-display">0.00</span></p>
            
            <p>**ISV 15%:** <span id="tax-display">0.00</span></p>
            
            <h4>Total a Pagar: <span id="total-display">0.00</span></h4>
            
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
        // NOTA: totalAmountInput y totalDisplay se mantienen, pero los siguientes deben eliminarse
        // const discountRateInput = document.getElementById('discount_rate_input'); // ELIMINADO
        // const discountInput = document.getElementById('discount_amount_input'); // ELIMINADO

        // Eliminamos los listeners de descuento global obsoletos
        // discountRateInput.addEventListener('input', calculateTotals);
        // discountInput.addEventListener('input', calculateTotals);
        
        let itemIndex = 0;

        // URL para la búsqueda de productos (necesaria para AJAX)
        const searchUrl = "{{ route('products.search') }}";

        // --- FUNCIÓN PRINCIPAL PARA CALCULAR TOTALES CON ISV 15% (Línea por Línea) ---
        function calculateTotals() {
            
            // Totalizadores de la Factura (acumuladores)
            let subtotalGrossSum = 0;      // 1. Suma total sin descuentos (Subtotal Bruto)
            let totalDiscountSum = 0;       // 2. Suma total de descuentos
            let taxableBaseSum = 0;         // 3. Suma total de Bases Imponibles (Neto sin ISV)
            let taxAmountSum = 0;           // 4. Suma total del ISV
            
            const ISV_RATE = 0.15; // Tasa de Impuesto sobre Ventas

            // 1. CALCULAR SUBTOTALES, DESCUENTOS e ISV POR LÍNEA
            itemBody.querySelectorAll('tr').forEach(row => {
                const quantityInput = row.querySelector('.item-quantity');
                const priceInput = row.querySelector('.item-price');
                const discountRateInput = row.querySelector('.item-discount-rate'); 
                const lineTotalSpan = row.querySelector('.line-total-display');
                
                const quantity = parseFloat(quantityInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const discountRate = parseFloat(discountRateInput.value) || 0; // Tasa de descuento por ítem

                // Validación de seguridad para evitar divisiones por cero o resultados negativos en cálculos fiscales
                if (quantity <= 0 || price <= 0) {
                     lineTotalSpan.textContent = "0.00";
                     return; // Skip item if quantity or price is invalid
                }
                
                // a. SUBTOTAL BRUTO DE LA LÍNEA (Precio Unitario * Cantidad)
                const lineSubtotalGross = quantity * price;
                
                // b. DESCUENTO DE LA LÍNEA
                const lineDiscountAmount = lineSubtotalGross * (discountRate / 100);
                
                // c. TOTAL DESCONTADO (Bruto, con ISV)
                const lineTotalDiscountedGross = lineSubtotalGross - lineDiscountAmount;
                
                // d. BASE IMPONIBLE (NETO sin ISV)
                // Se utiliza Math.round para evitar problemas de precisión flotante en JS antes de redondear a 2 decimales
                const lineTaxableBase = lineTotalDiscountedGross / (1 + ISV_RATE);
                
                // e. ISV DE LA LÍNEA
                const lineTaxAmount = lineTotalDiscountedGross - lineTaxableBase;
                
                // Acumular a los totales globales
                subtotalGrossSum += lineSubtotalGross;
                totalDiscountSum += lineDiscountAmount;
                taxableBaseSum += lineTaxableBase;
                taxAmountSum += lineTaxAmount;
                
                // Actualizar Display de Línea (Muestra el precio final descontado)
                lineTotalSpan.textContent = lineTotalDiscountedGross.toFixed(2);
            });
            
            // 2. CALCULAR TOTAL FINAL DE LA FACTURA (Suma de los totales ya descontados)
            const grandTotal = subtotalGrossSum - totalDiscountSum; 
            
            // 3. ACTUALIZAR DISPLAYS Y CAMPOS OCULTOS
            
            // Subtotal Bruto (Sin Descuento)
            document.getElementById('subtotal-gross-display').textContent = subtotalGrossSum.toFixed(2); // ID CORREGIDO
            
            // Descuento Total
            document.getElementById('discount-display').textContent = totalDiscountSum.toFixed(2);
            document.getElementById('discount_amount_input').value = totalDiscountSum.toFixed(2);
            
            // Base Imponible
            document.getElementById('taxable-base-display').textContent = taxableBaseSum.toFixed(2);
            
            // ISV
            document.getElementById('tax-display').textContent = taxAmountSum.toFixed(2);
            document.getElementById('tax_amount_input').value = taxAmountSum.toFixed(2);
            
            // Total Final (Total a Pagar)
            document.getElementById('total-display').textContent = grandTotal.toFixed(2);
            // El campo oculto para el total ya está actualizado por grandTotal
            document.getElementById('total_amount_input').value = grandTotal.toFixed(2); 
            
            // Nota: Se corrige la duplicidad de 'total_amount_input' en el HTML.
        }

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
                    item.addEventListener('click', (e) => {
                        e.preventDefault();
                        const row = dropdown.closest('tr');
                        
                        // Setear los valores en los campos de la fila
                        const priceValue = product.precio_venta.toFixed(2);
                        
                        row.querySelector('.item-search-input').value = product.name;
                        row.querySelector('.item-id-input').value = product.id;
                        
                        // 1. Asigna al campo visible
                        row.querySelector('.item-price').value = priceValue; 
                        
                        // 2. ASIGNA DIRECTAMENTE AL CAMPO OCULTO 
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
                    <input type="number" name="items[${itemIndex}][discount_rate]" class="form-control item-discount-rate" value="0.00" min="0" max="100" step="0.01">
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

            // 1. Manejo de Eventos de la Fila (Escuchan quantity, price y discount_rate)
            newRow.querySelectorAll('.item-quantity, .item-price, .item-discount-rate').forEach(input => {
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