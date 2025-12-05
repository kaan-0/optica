<?php

namespace App\Http\Controllers;

use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
// Importa los modelos de detalle para el método 'store' y 'update'
use App\Models\DetallesAros;
use App\Models\DetallesLentes;
use App\Models\DetallesLentesTerminados;
use App\Models\Category;

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

// En app/Http/Controllers/ProductController.php

public function index(Request $request)
{
    // Obtener parámetros de la URL
    $search = $request->input('search');
    $sort = $request->input('sort', 'id'); // Columna por defecto para ordenar
    $direction = $request->input('direction', 'desc'); // Dirección por defecto

    // Validar columnas permitidas
    $allowedSorts = ['id', 'product_code', 'name', 'precio_venta', 'precio_compra'];

    // Manejar ordenamiento por categoría (relación)
    if ($sort === 'category') {
        $products = Product::join('categorias', 'products.id_categoria', '=', 'categorias.id')
            ->select('products.*', 'categorias.nombre as category_name')
            ->orderBy('category_name', $direction);
    } elseif (in_array($sort, $allowedSorts)) {
        // Ordenar por una columna directa de la tabla products
        $products = Product::orderBy($sort, $direction);
    } else {
        // Orden por defecto si la columna no es válida
        $products = Product::latest();
    }


    // Aplicar filtro de búsqueda
    if ($search) {
        $products->where(function($query) use ($search) {
            $query->where('product_code', 'LIKE', '%' . $search . '%')
                  ->orWhere('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('detail', 'LIKE', '%' . $search . '%');
        });
    }

    // Paginar los resultados. Mantener la búsqueda y el ordenamiento en la URL de paginación
    $products = $products->paginate(10)->withQueryString();

    return view('products.index', compact('products', 'search'));
}

    public function create(): View
    {
        $categorias = Category::all();

        return view('products.create', compact('categorias'));
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
        
        $product = $this->loadProductDetails($product);

        return view('products.show', compact('product'));
    }

    /**
     * Muestra el formulario de edición, cargando los detalles específicos.
     */
    public function edit(Product $product): View
    {
        // 1️⃣ Cargar la relación 'categoria' y el detalle específico
        $product = $this->loadProductDetails($product);
        $categorias = Category::all();

        // La vista 'edit' usará estas relaciones para precargar los campos
        return view('products.edit', compact('product','categorias'));
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

    public function export()
{
    $fileName = 'Inventario_Optica_' . now()->format('Ymd') . '.xlsx';

    // Dispara la descarga usando la clase ProductsExport
    return Excel::download(new ProductsExport, $fileName);
}

public function search(Request $request)
{
    $query = $request->get('query');

    // Busca productos que coincidan con el código o nombre
    $products = Product::where('product_code', 'LIKE', "%{$query}%")
                       ->orWhere('name', 'LIKE', "%{$query}%")
                       ->get(['id', 'product_code', 'name', 'precio_venta']);
    
    return response()->json($products);
}

}