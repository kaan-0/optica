<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_code',
        'name',
        'detail',
        'stock_tienda',
        'stock_bodega',
        'precio_venta',
        'precio_compra',
        'id_categoria'
    ];

    // Aros / monturas
    public function detallesAros()
    {
        return $this->hasOne(DetallesAros::class, 'product_id');
    }

    // Lentes (no terminados)
    public function detallesLente()
    {
        return $this->hasOne(DetallesLentes::class, 'product_id');
    }

    // Lentes terminados
    public function detallesLenteTerminado()
    {
        return $this->hasOne(DetallesLentesTerminados::class, 'product_id');
    }

    public function categoria()
{
    return $this->belongsTo(Category::class, 'id_categoria');
}

}
