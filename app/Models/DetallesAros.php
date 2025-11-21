<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallesAros extends Model
{
    protected $table = 'detalles_aros';

    protected $fillable = [
        'product_id',
        'tipo_material',
        'forma',
        'genero',
        'color_frente',
        'color_patilla',
        'tam_puente',
        'tam_lente',
        'tam_patilla'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

