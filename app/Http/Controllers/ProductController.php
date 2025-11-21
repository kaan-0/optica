<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
// Importa los modelos de detalle para el método 'store' y 'update'
use App\Models\DetallesAros;
use App\Models\DetallesLentes;
use App\Models\DetallesLentesTerminados;

class ProductController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:product-list|product-create|product-edit|product-delete', ['only' => ['index','show']]);
        $this->middleware('permission:product-create', ['only' => ['create','store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    // public function index(): View
    // {
    //     $products = Product::with('categoria')->paginate(10);
    //     // Ajustado el cálculo de $i para que coincida con la paginación de 10 si es necesario.
    //     return view('products.index', compact('products'))
    //         ->with('i', (request()->input('page', 1) - 1) * 10);
    // }
    // En app/Http/Controllers/ProductController.php

public function index(Request $request): View
{
    // Obtener el término de búsqueda de la solicitud (si existe)
    $search = $request->query('search');

    // Iniciar la consulta a los productos, cargando la relación categoria
    $productsQuery = Product::with('categoria');

    // Si hay un término de búsqueda, aplicar el filtro
    if ($search) {
        $productsQuery->where(function ($query) use ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('product_code', 'LIKE', "%{$search}%")
                  ->orWhere('detail', 'LIKE', "%{$search}%");
        });
    }

    // Paginar los resultados y adjuntar el parámetro 'search' a la URL
    $products = $productsQuery->paginate(10)->withQueryString();

    // Calcular el índice
    $i = ($products->currentPage() - 1) * $products->perPage();
    
    // Pasar el término de búsqueda de vuelta a la vista para rellenar el campo
    return view('products.index', compact('products', 'search'))
        ->with('i', $i);
}

    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required',
            'product_code' => 'required',
            'stock_tienda' => 'required|integer',
            'stock_bodega' => 'required|integer',
            'precio_venta' => 'required|numeric',
            'precio_compra' => 'required|numeric',
            'id_categoria' => 'required|integer'
        ]);

        // 1️⃣ Guardar el producto principal
        $product = Product::create($request->all());

        // 2️⃣ Guardar detalle según categoría
        switch ($request->id_categoria) {

            case 1: // Aros / Monturas
                DetallesAros::create([
                    'product_id' => $product->id,
                    'tipo_material' => $request->tipo_material,
                    'forma' => $request->forma,
                    'genero' => $request->genero,
                    'color_frente' => $request->color_frente,
                    'color_patilla' => $request->color_patilla,
                    'tam_puente' => $request->tam_puente,
                    'tam_lente' => $request->tam_lente,
                    'tam_patilla' => $request->tam_patilla,
                ]);
                break;

            case 2: // Lentes no terminados
                DetallesLentes::create([
                    'product_id' => $product->id,
                    'material' => $request->material,
                    'tratamiento' => $request->tratamiento,
                    'indice_refraccion' => $request->indice_refraccion,
                    'diametro' => $request->diametro,
                    'diseno' => $request->diseno,
                ]);
                break;

            case 3: // Lentes terminados
                DetallesLentesTerminados::create([
                    'product_id' => $product->id,
                    'esfera' => $request->esfera,
                    'cilindro' => $request->cilindro,
                    'eje' => $request->eje,
                    'diametro' => $request->diametro,
                    'material' => $request->material,
                ]);
                break;
        }

        return redirect()->route('products.index')
            ->with('success', 'Producto creado con éxito.');
    }

    // --- MÉTODOS MODIFICADOS ---

    /**
     * Muestra los detalles del producto y sus detalles específicos.
     */
    public function show(Product $product): View
    {
        // 1️⃣ Cargar la relación 'categoria' y el detalle específico
        $product = $this->loadProductDetails($product);

        // La vista 'show' usará estas relaciones para mostrar los datos
        return view('products.show', compact('product'));
    }

    /**
     * Muestra el formulario de edición, cargando los detalles específicos.
     */
    public function edit(Product $product): View
    {
        // 1️⃣ Cargar la relación 'categoria' y el detalle específico
        $product = $this->loadProductDetails($product);

        // La vista 'edit' usará estas relaciones para precargar los campos
        return view('products.edit', compact('product'));
    }

    /**
     * Actualiza el producto principal y sus detalles específicos.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        // 1️⃣ Validación de campos principales
        $request->validate([
            'name' => 'required',
            'product_code' => 'required',
            'stock_tienda' => 'required|integer',
            'stock_bodega' => 'required|integer',
            'precio_venta' => 'required|numeric',
            'precio_compra' => 'required|numeric',
            'id_categoria' => 'required|integer' // Asumo que no cambias la categoría
        ]);

        // 2️⃣ Actualizar el producto principal
        $product->update($request->except(['_token', '_method']));

        // 3️⃣ Actualizar detalle según categoría
        $this->updateProductDetails($product, $request);

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado con éxito.');
    }

    // --- NUEVOS MÉTODOS DE AYUDA ---

    /**
     * Carga las relaciones de detalle específicas del producto.
     */
    protected function loadProductDetails(Product $product): Product
    {
        // Siempre carga la categoría
        $product->load('categoria');

        // Cargar detalles dinámicamente
        switch ($product->id_categoria) {
            case 1:
                // Asume que tienes una relación 'detallesAros' en el modelo Product
                $product->load('detallesAros');
                break;
            case 2:
                // Asume que tienes una relación 'detallesLentes' en el modelo Product
                $product->load('detallesLentes');
                break;
            case 3:
                // Asume que tienes una relación 'detallesLentesTerminados' en el modelo Product
                $product->load('detallesLentesTerminados');
                break;
        }
        return $product;
    }

    /**
     * Actualiza los detalles específicos del producto.
     */
    protected function updateProductDetails(Product $product, Request $request): void
    {
        $data = $request->all();

        switch ($product->id_categoria) {
            case 1: // Aros
                DetallesAros::updateOrCreate(
                    ['product_id' => $product->id],
                    $request->only([
                        'tipo_material', 'forma', 'genero', 'color_frente',
                        'color_patilla', 'tam_puente', 'tam_lente', 'tam_patilla'
                    ])
                );
                break;

            case 2: // Lentes no terminados
                DetallesLentes::updateOrCreate(
                    ['product_id' => $product->id],
                    $request->only([
                        'material', 'tratamiento', 'indice_refraccion', 'diametro', 'diseno'
                    ])
                );
                break;

            case 3: // Lentes terminados
                DetallesLentesTerminados::updateOrCreate(
                    ['product_id' => $product->id],
                    $request->only([
                        'esfera', 'cilindro', 'eje', 'diametro', 'material'
                    ])
                );
                break;
        }
    }


    public function destroy(Product $product): RedirectResponse
    {
        // Opcional: Si los modelos de detalle no tienen un CASCADE ON DELETE,
        // deberías eliminarlos manualmente antes de eliminar el producto principal.
        
        $product->delete();

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado!');
    }
}