<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallesLentesTerminados extends Model
{
    protected $table = 'detalles_lentes_terminados';

    protected $fillable = [
        'product_id',
        'esfera',
        'cilindro',
        'eje',
        'diametro',
        'material'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
