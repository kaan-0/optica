<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model

{
     protected $table = 'categorias';

    protected $fillable = [
        'id',
        'nombre',
        'descripcion',
        
    ];

    public function products()
{
    return $this->hasMany(Product::class, 'id_categoria');
}

}
