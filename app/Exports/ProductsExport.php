<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Obtener todos los productos con la relación 'categoria'
        return Product::with('categoria')->get()->map(function ($product) {
            return [
                'Código' => $product->product_code,
                'Nombre' => $product->name,
                'Categoría' => $product->categoria->nombre ?? 'Sin categoría',
                'Stock Tienda' => $product->stock_tienda,
                'Stock Bodega' => $product->stock_bodega,
                'Precio Venta' => $product->precio_venta,
                'Precio Compra' => $product->precio_compra,
            ];
        });
    }

    public function headings(): array
    {
        // Los títulos de las columnas del archivo Excel
        return [
            'Código',
            'Nombre',
            'Categoría',
            'Stock Tienda',
            'Stock Bodega',
            'Precio Venta',
            'Precio Compra',
        ];
    }
}