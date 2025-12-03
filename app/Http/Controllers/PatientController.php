<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\MedicalRecord; // Importamos el nuevo modelo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PatientController extends Controller
{
    /**
     * Muestra una lista paginada de pacientes.
     */
    public function index(Request $request) // <--- MODIFICACIÓN 1: Aceptar el objeto Request
    {
        // Obtener el término de búsqueda (será null si no hay búsqueda)
        $search = $request->input('search');

        // 1. Iniciar la consulta base de pacientes
        $query = Patient::orderBy('last_name');

        // 2. Aplicar la lógica de búsqueda si existe un término
        if ($search) {
            $query->where(function ($q) use ($search) {
                // Filtra si el nombre o el apellido o el número de identidad contiene el término
                $q->where('first_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('last_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('identity_number', 'LIKE', '%' . $search . '%');
            });
        }
        // <--- FIN MODIFICACIÓN 2: Lógica de búsqueda

        // Obtener los resultados paginados
        $patients = $query->paginate(10);
        
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
     * Almacena un nuevo paciente en la base de datos, junto con su primer expediente médico.
     */
    public function store(Request $request)
    {
        // 1. VALIDACIÓN
        // Validamos todos los campos del formulario (ambas tablas)
        $validated = $request->validate([
            // --- PATIENT (Requeridos) ---
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'identity_number' => 'required|string|max:50|unique:patients,identity_number',
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'phone_number' => 'required|string|max:20',
            
            // --- PATIENT (Opcionales) ---
            'email' => 'nullable|email|unique:patients,email|max:100', 
            'address' => 'nullable|string|max:255',
            'medical_history' => 'nullable|string',

            // --- MEDICAL_RECORD (Requeridos para la visita inicial) ---
            'record_date' => 'required|date',
            
            // --- MEDICAL_RECORD (Opcionales / Texto - DEBE COINCIDIR CON FILLABLE DEL MODELO) ---
            'doctor_name' => 'nullable|string|max:255',
            'consultation_reason' => 'nullable|string',
            'symptoms' => 'nullable|string',
            'occupation' => 'nullable|string',
            'dominant_hand' => 'nullable|string',
            'dominant_eye' => 'nullable|string',

            // Optometría (Ejemplo, deben ser validados todos los campos)
            'visual_acuity_sc_od' => 'nullable|string|max:10',
            'visual_acuity_sc_oi' => 'nullable|string|max:10',
            'retinoscopy_dynamic_od' => 'nullable|string|max:50',
            'definitive_prescription_od' => 'nullable|string',
            'optometry_observations' => 'nullable|string',
            'test_color' => 'boolean', // Para checkbox
            
            // Oftalmología (Ejemplo, deben ser validados todos los campos)
            'cornea_description' => 'nullable|string',
            'iris_description' => 'nullable|string',
            'fundus_description' => 'nullable|string',
            'ophthalmology_observations' => 'nullable|string',
        ]);
        
        // 2. CREACIÓN (USANDO TRANSACCIÓN)
        DB::beginTransaction();

        try {
            // A. Separar y Crear el Paciente (Tabla PATIENTS)
            $patientData = $request->only([
                'first_name', 'last_name', 'identity_number', 'birth_date', 'gender', 
                'phone_number', 'email', 'address', 'medical_history'
            ]);
            
            $patient = Patient::create($patientData);

            // B. Separar y Crear el Registro de Expediente (Tabla MEDICAL_RECORDS)
            // La lista de campos aquí DEBE coincidir con los campos de MedicalRecord en la vista y en la migración
            $recordData = $request->only([
                'record_date', 'doctor_name', 'consultation_reason', 'symptoms',
                'occupation', 'dominant_hand', 'dominant_eye', 
                'visual_acuity_sc_od', 'visual_acuity_sc_oi', 'visual_acuity_dist_od', 'visual_acuity_dist_oi', 
                'visual_acuity_near_od', 'visual_acuity_near_oi', 'retinoscopy_dynamic_od', 'retinoscopy_dynamic_oi', 
                'retinoscopy_static_od', 'retinoscopy_static_oi', 'last_rx_optic_date', 'lens_type', 'lens_color', 
                'special_instructions', 'definitive_prescription_od', 'definitive_prescription_oi', 'test_stereopsis', 
                'test_color', 'amsler_grid', 'optometry_observations', 'eyelids_sup', 'eyelids_inf', 
                'eyelids_aberrations', 'eyelids_func_elev', 'eyelids_tonic_orbic', 'conjunctiva', 'sclera', 
                'lacrimal_vias', 'schirmer', 'cornea_description', 'limbus', 'anterior_chamber_prof', 
                'iris_description', 'pupil_description', 'crystalline', 'fundus_description', 'disc_optic', 
                'vessels', 'retina', 'macula_fovea', 'viterous', 'ophthalmology_observations',
            ]);
            
            // Si un checkbox no fue marcado, su valor no estará en el request, 
            // aseguramos que tenga un valor por defecto (falso o 0)
            $recordData['test_color'] = $request->has('test_color');
            $recordData['amsler_grid'] = $request->has('amsler_grid');

            // Crear el registro de expediente asociado al paciente
            $patient->medicalRecords()->create($recordData);
            
            DB::commit();

            return redirect()->route('patients.show', $patient)->with('success', 'Paciente y examen inicial registrados exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Retornar con error y datos anteriores para que el usuario no pierda el input
            // Aquí puedes cambiar a `back()` para volver al formulario y mostrar el error.
            return back()->with('error', 'Error al registrar paciente y consulta: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Muestra los detalles de un paciente específico, incluyendo su historial.
     */
    public function show(Patient $patient)
    {
        // Cargar los expedientes médicos para mostrarlos en la vista de detalles
        $patient->load('medicalRecords');
        return view('patients.show', compact('patient'));
    }

    /**
     * Muestra el formulario para editar un paciente existente.
     */
    public function edit(Patient $patient)
    {
        $patient->load([
            'medicalRecords' => function ($query) {
                $query->orderBy('record_date', 'desc');
            }
        ]);
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
            // Ignorar el registro actual para la unicidad
            'identity_number' => 'required|string|max:50|unique:patients,identity_number,'.$patient->id,
            'birth_date' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'phone_number' => 'required|string|max:20',
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