<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    /**
     * Muestra una lista paginada de pacientes.
     */
    public function index()
    {
        // 10 es un buen número para paginación por defecto.
        $patients = Patient::orderBy('last_name')->paginate(10);
        
        return view('patients.index', compact('patients'));
    }

    /**
     * Muestra el formulario para crear un nuevo paciente.
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Almacena un nuevo paciente en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validación de datos
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            // NUEVO: Validación del número de identidad (Obligatorio y Único)
            'identity_number' => 'required|string|max:50|unique:patients,identity_number',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'phone_number' => 'required|string|max:20',
            // CORRECCIÓN: Email ahora es opcional (nullable)
            'email' => 'nullable|email|unique:patients,email|max:100', 
            'address' => 'nullable|string|max:255',
            'medical_history' => 'nullable|string',
        ]);

        // 2. Creación del registro
        Patient::create($request->all());

        // 3. Redirección con mensaje de éxito
        return redirect()->route('patients.index')
                         ->with('success', '¡Paciente registrado exitosamente!');
    }

    /**
     * Muestra los detalles de un paciente específico.
     */
    public function show(Patient $patient)
    {
        // Laravel automáticamente inyecta el modelo Patient basado en el ID de la ruta (Route Model Binding)
        return view('patients.show', compact('patient'));
    }

    /**
     * Muestra el formulario para editar un paciente existente.
     */
    public function edit(Patient $patient)
    {
        return view('patients.edit', compact('patient'));
    }

    /**
     * Actualiza un paciente existente en la base de datos.
     */
    public function update(Request $request, Patient $patient)
    {
        // 1. Validación de datos
        $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            // NUEVO: Validación del número de identidad, ignorando el registro actual
            'identity_number' => 'required|string|max:50|unique:patients,identity_number,'.$patient->id,
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'phone_number' => 'required|string|max:20',
            // CORRECCIÓN: Email es opcional y debe ignorar el registro actual para la unicidad
            'email' => 'nullable|email|unique:patients,email,'.$patient->id.'|max:100',
            'address' => 'nullable|string|max:255',
            'medical_history' => 'nullable|string',
        ]);

        // 2. Actualización del registro
        $patient->update($request->all());

        // 3. Redirección con mensaje de éxito
        return redirect()->route('patients.show', $patient->id)
                         ->with('success', 'Datos del paciente actualizados correctamente.');
    }

    /**
     * Elimina un paciente de la base de datos.
     */
    public function destroy(Patient $patient)
    {
        $patient->delete();

        return redirect()->route('patients.index')
                         ->with('success', 'Paciente eliminado correctamente.');
    }
}