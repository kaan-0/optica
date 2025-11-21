<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallesLentes extends Model
{
    protected $table = 'detalles_lentes';

    protected $fillable = [
        'product_id',
        'material',
        'tratamiento',
        'indice_refraccion',
        'diametro',
        'diseno'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
