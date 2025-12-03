<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedicalRecordController extends Controller
{
    /**
     * Muestra el formulario para crear un nuevo expediente médico (nueva consulta)
     * asociado a un paciente específico.
     */
    public function create(Patient $patient)
    {
        // El paciente ya viene inyectado gracias a Route Model Binding.
        return view('medical_records.create', compact('patient'));
    }

    /**
     * Almacena un nuevo expediente médico.
     */
    public function store(Request $request, Patient $patient)
    {
        // 1. VALIDACIÓN
        $validatedData = $request->validate([
            'record_date' => 'required|date',
            
            // --- CAMPOS GENERALES ---
            'doctor_name' => 'nullable|string|max:255',
            'consultation_reason' => 'nullable|string',
            'symptoms' => 'nullable|string',
            'occupation' => 'nullable|string',
            'dominant_hand' => 'nullable|string',
            'dominant_eye' => 'nullable|string',

            // --- OPTOMETRÍA ---
            'visual_acuity_sc_od' => 'nullable|string|max:10',
            'visual_acuity_sc_oi' => 'nullable|string|max:10',
            'visual_acuity_dist_od' => 'nullable|string|max:10', 
            'visual_acuity_dist_oi' => 'nullable|string|max:10', 
            'visual_acuity_near_od' => 'nullable|string|max:10', 
            'visual_acuity_near_oi' => 'nullable|string|max:10', 
            'retinoscopy_dynamic_od' => 'nullable|string|max:50', 
            'retinoscopy_dynamic_oi' => 'nullable|string|max:50', 
            'retinoscopy_static_od' => 'nullable|string|max:50', 
            'retinoscopy_static_oi' => 'nullable|string|max:50', 
            'last_rx_optic_date' => 'nullable|date', 
            'lens_type' => 'nullable|string|max:100', 
            'lens_color' => 'nullable|string|max:50', 
            'special_instructions' => 'nullable|string', 
            'definitive_prescription_od' => 'nullable|string', 
            'definitive_prescription_oi' => 'nullable|string', 
            'test_stereopsis' => 'nullable|string', 
            'test_color' => 'nullable|boolean', // Manejado como boolean
            'amsler_grid' => 'nullable|boolean', // Manejado como boolean
            'optometry_observations' => 'nullable|string', 

            // --- OFTALMOLOGÍA ---
            'eyelids_sup' => 'nullable|string', 
            'eyelids_inf' => 'nullable|string', 
            'eyelids_aberrations' => 'nullable|string', 
            'eyelids_func_elev' => 'nullable|string', 
            'eyelids_tonic_orbic' => 'nullable|string', 
            'conjunctiva' => 'nullable|string', 
            'sclera' => 'nullable|string', 
            'lacrimal_vias' => 'nullable|string', 
            'schirmer' => 'nullable|string', 
            'cornea_description' => 'nullable|string', 
            'limbus' => 'nullable|string', 
            'anterior_chamber_prof' => 'nullable|string', 
            'iris_description' => 'nullable|string', 
            'pupil_description' => 'nullable|string', 
            'crystalline' => 'nullable|string', 
            'fundus_description' => 'nullable|string', 
            'disc_optic' => 'nullable|string', 
            'vessels' => 'nullable|string', 
            'retina' => 'nullable|string', 
            'macula_fovea' => 'nullable|string', 
            'viterous' => 'nullable|string', 
            'ophthalmology_observations' => 'nullable|string',
        ]);

        // Manejar checkboxes (si no están presentes, son falsos)
        $validatedData['test_color'] = $request->has('test_color');
        $validatedData['amsler_grid'] = $request->has('amsler_grid');

        // 2. CREACIÓN del registro asociado al paciente
        try {
            $patient->medicalRecords()->create($validatedData);

            return redirect()->route('patients.show', $patient)
                             ->with('success', '¡Nueva consulta registrada exitosamente!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al crear el registro de consulta: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Muestra los detalles de un expediente médico específico.
     */
public function show($id)
{
    
    $record = MedicalRecord::findOrFail($id); 
    
    // Carga el paciente asociado con este expediente
    $patient = $record->patient; 
    
    // Pasa ambas variables a la vista 'medical_records.show'
    return view('medical_records.show', compact('record', 'patient'));
}

    public function edit($id)
    {
        // 1. Buscar el expediente o fallar con 404
        $record = MedicalRecord::findOrFail($id);

        // 2. Cargar el paciente asociado
        $patient = $record->patient;
        
        // 3. Pasar el expediente y el paciente a la vista de edición
        return view('medical_records.edit', compact('record', 'patient'));
    }

    public function update(Request $request, $id)
    {
        // 1. Definir reglas de validación (deben coincidir con las de 'store' y ser estrictas)
        $rules = [
            'record_date' => 'required|date',
            'doctor_name' => 'nullable|string|max:255',
            'consultation_reason' => 'nullable|string',
            'symptoms' => 'nullable|string',
            'occupation' => 'nullable|string|max:255',
            'dominant_hand' => 'nullable|in:right,left,ambidextrous',
            'dominant_eye' => 'nullable|in:od,oi,alternating',
            // --- Reglas para la sección Optometría ---
            'visual_acuity_sc_od' => 'nullable|string|max:50',
            'visual_acuity_sc_oi' => 'nullable|string|max:50',
            'visual_acuity_dist_od' => 'nullable|string|max:50',
            'visual_acuity_dist_oi' => 'nullable|string|max:50',
            'visual_acuity_near_od' => 'nullable|string|max:50',
            'visual_acuity_near_oi' => 'nullable|string|max:50',
            'retinoscopy_dynamic_od' => 'nullable|string|max:255',
            'retinoscopy_dynamic_oi' => 'nullable|string|max:255',
            'retinoscopy_static_od' => 'nullable|string|max:255',
            'retinoscopy_static_oi' => 'nullable|string|max:255',
            'definitive_prescription_od' => 'nullable|string',
            'definitive_prescription_oi' => 'nullable|string',
            'last_rx_optic_date' => 'nullable|date',
            'lens_type' => 'nullable|string|max:255',
            'lens_color' => 'nullable|string|max:255',
            'test_stereopsis' => 'nullable|string|max:255',
            'special_instructions' => 'nullable|string',
            'test_color' => 'nullable|boolean',
            'amsler_grid' => 'nullable|boolean',
            'optometry_observations' => 'nullable|string',
            // --- Reglas para la sección Oftalmología ---
            'eyelids_sup' => 'nullable|string|max:255',
            'eyelids_inf' => 'nullable|string|max:255',
            'eyelids_aberrations' => 'nullable|string|max:255',
            'eyelids_func_elev' => 'nullable|string|max:255',
            'conjunctiva' => 'nullable|string|max:255',
            'sclera' => 'nullable|string|max:255',
            'lacrimal_vias' => 'nullable|string|max:255',
            'cornea_description' => 'nullable|string|max:255',
            'iris_description' => 'nullable|string|max:255',
            'pupil_description' => 'nullable|string|max:255',
            'fundus_description' => 'nullable|string|max:255',
            'disc_optic' => 'nullable|string|max:255',
            'vessels' => 'nullable|string|max:255',
            'retina' => 'nullable|string|max:255',
            'macula_fovea' => 'nullable|string|max:255',
            'ophthalmology_observations' => 'nullable|string',
        ];

        // 2. Realizar la validación
        $validatedData = $request->validate($rules);

        // 3. Ajustar los campos checkbox (si no se marcan, no están en el request, por lo que se deben establecer en 0)
        $validatedData['test_color'] = $request->has('test_color');
        $validatedData['amsler_grid'] = $request->has('amsler_grid');

        // 4. Buscar el expediente a actualizar
        $record = MedicalRecord::findOrFail($id);

        // 5. Actualizar el registro con los datos validados
        $record->update($validatedData);

        // 6. Redirigir al detalle del paciente con un mensaje de éxito
        // Obtiene el ID del paciente del registro que se acaba de actualizar
        $patientId = $record->patient_id; 

        return redirect()->route('patients.show', $patientId)
                         ->with('success', 'Expediente médico actualizado correctamente.');
    }

    public function destroy($id)
    {
        // 1. Buscar el expediente o fallar con 404
        $record = MedicalRecord::findOrFail($id);
        
        // 2. Guardar el ID del paciente para la redirección
        $patientId = $record->patient_id;

        // 3. Eliminar el registro
        $record->delete();

        // 4. Redirigir al detalle del paciente con un mensaje de éxito
        return redirect()->route('patients.show', $patientId)
                         ->with('success', 'Expediente médico eliminado correctamente.');
    }
}