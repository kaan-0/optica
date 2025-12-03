<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 
        'record_date', 
        'doctor_name',
        'consultation_reason',
        'symptoms',
        'occupation',
        'dominant_hand',
        'dominant_eye',
        
        // Optometría
        'visual_acuity_sc_od', 'visual_acuity_sc_oi', 'visual_acuity_dist_od', 'visual_acuity_dist_oi', 
        'visual_acuity_near_od', 'visual_acuity_near_oi', 
        'retinoscopy_dynamic_od', 'retinoscopy_dynamic_oi', 'retinoscopy_static_od', 'retinoscopy_static_oi', 
        'last_rx_optic_date', 'lens_type', 'lens_color', 'special_instructions', 
        'definitive_prescription_od', 'definitive_prescription_oi', 'test_stereopsis', 
        'test_color', 'amsler_grid', 'optometry_observations', 

        // Oftalmología
        'eyelids_sup', 'eyelids_inf', 'eyelids_aberrations', 'eyelids_func_elev', 
        'eyelids_tonic_orbic', 'conjunctiva', 'sclera', 'lacrimal_vias', 'schirmer', 
        'cornea_description', 'limbus', 'anterior_chamber_prof', 'iris_description', 
        'pupil_description', 'crystalline', 'fundus_description', 'disc_optic', 
        'vessels', 'retina', 'macula_fovea', 'viterous', 'ophthalmology_observations',
    ];

    /**
     * Relación: Este expediente pertenece a un paciente.
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}