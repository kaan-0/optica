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
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'identity_number',
        'birth_date',
        'gender',
        'phone_number',
        'email',
        'address',
        'medical_history',
    ];

    /**
     * Define la relación uno a muchos con los expedientes médicos (visitas).
     * Un paciente puede tener muchos Medical Records.
     */
    public function medicalRecords()
    {
        return $this->hasMany(MedicalRecord::class);
    }
}