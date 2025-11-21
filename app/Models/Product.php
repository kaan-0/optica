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
        'price',
        'marca',
        'material',
        'color',
        'precio_compra'
    ];
}
