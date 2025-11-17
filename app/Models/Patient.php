<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Estos campos pueden ser asignados masivamente (Mass Assignment)
     * a través de los métodos create() o update().
     *
     * IMPORTANT: Se han incluido todos los campos del formulario
     * incluyendo el nuevo 'identity_number'.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'identity_number', // ¡NUEVO CAMPO AGREGADO!
        'birth_date',
        'gender',
        'phone_number',
        'email',
        'address',
        'medical_history',
    ];

    /**
     * Alternativa: Usar $guarded = []
     * Si prefieres permitir la asignación masiva de CUALQUIER campo (menos los que no quieres),
     * puedes usar:
     * protected $guarded = [];
     * pero es menos seguro y no lo recomiendo.
     */
}