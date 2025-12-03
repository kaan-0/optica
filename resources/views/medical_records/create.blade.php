@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Nueva Consulta para: {{ $patient->first_name }} {{ $patient->last_name }}</h2>
        <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Volver al Paciente
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>¡Ups!</strong> Hubo algunos problemas con tu entrada.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- El formulario apunta a la ruta de almacenamiento, enviando el ID del paciente --}}
    <form action="{{ route('medical_records.store', $patient->id) }}" method="POST">
        @csrf

        {{-- Información del Paciente (Solo Lectura) --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-info text-white">
                Datos del Paciente
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <strong>Identidad:</strong> {{ $patient->identity_number }}
                    </div>
                    <div class="col-md-4">
                        <strong>Fecha Nac.:</strong> {{ \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') }}
                    </div>
                    <div class="col-md-4">
                        <strong>Género:</strong> {{ ucfirst($patient->gender) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Sistema de Pestañas para organizar la gran cantidad de campos --}}
        <ul class="nav nav-tabs" id="medicalRecordTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">1. Datos de la Visita</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="optometry-tab" data-bs-toggle="tab" data-bs-target="#optometry" type="button" role="tab" aria-controls="optometry" aria-selected="false">2. Sección Optometría</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ophthalmology-tab" data-bs-toggle="tab" data-bs-target="#ophthalmology" type="button" role="tab" aria-controls="ophthalmology" aria-selected="false">3. Sección Oftalmología</button>
            </li>
        </ul>

        <div class="tab-content border border-top-0 p-4 mb-4 bg-white shadow-sm" id="medicalRecordTabsContent">
            
            {{-- Pestaña 1: Datos de la Visita --}}
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                <h3>Información de la Consulta</h3>
                <div class="row">
                    
                    <div class="col-md-4 mb-3">
                        <label for="record_date" class="form-label">Fecha de la Consulta *</label>
                        <input type="date" name="record_date" id="record_date" class="form-control @error('record_date') is-invalid @enderror" value="{{ old('record_date', date('Y-m-d')) }}" required>
                        @error('record_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-8 mb-3">
                        <label for="doctor_name" class="form-label">Nombre del Doctor(a)</label>
                        <input type="text" name="doctor_name" id="doctor_name" class="form-control @error('doctor_name') is-invalid @enderror" value="{{ old('doctor_name') }}">
                        @error('doctor_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="consultation_reason" class="form-label">Motivo de la Consulta</label>
                        <textarea name="consultation_reason" id="consultation_reason" class="form-control" rows="2">{{ old('consultation_reason') }}</textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="symptoms" class="form-label">Síntomas Actuales</label>
                        <textarea name="symptoms" id="symptoms" class="form-control" rows="2">{{ old('symptoms') }}</textarea>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="occupation" class="form-label">Ocupación</label>
                        <input type="text" name="occupation" id="occupation" class="form-control" value="{{ old('occupation') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="dominant_hand" class="form-label">Mano Dominante</label>
                        <select name="dominant_hand" id="dominant_hand" class="form-select">
                            <option value="">Seleccione...</option>
                            <option value="right" {{ old('dominant_hand') == 'right' ? 'selected' : '' }}>Derecha</option>
                            <option value="left" {{ old('dominant_hand') == 'left' ? 'selected' : '' }}>Izquierda</option>
                            <option value="ambidextrous" {{ old('dominant_hand') == 'ambidextrous' ? 'selected' : '' }}>Ambas</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="dominant_eye" class="form-label">Ojo Dominante</label>
                        <select name="dominant_eye" id="dominant_eye" class="form-select">
                            <option value="">Seleccione...</option>
                            <option value="od" {{ old('dominant_eye') == 'od' ? 'selected' : '' }}>OD (Ojo Derecho)</option>
                            <option value="oi" {{ old('dominant_eye') == 'oi' ? 'selected' : '' }}>OI (Ojo Izquierdo)</option>
                            <option value="alternating" {{ old('dominant_eye') == 'alternating' ? 'selected' : '' }}>Alternante</option>
                        </select>
                    </div>

                </div>
            </div>

            {{-- Pestaña 2: Sección Optometría --}}
            <div class="tab-pane fade" id="optometry" role="tabpanel" aria-labelledby="optometry-tab">
                <h3>Examen Optométrico</h3>

                {{-- Subsección: Agudeza Visual --}}
                <h5 class="mt-3">Agudeza Visual</h5>
                <div class="row mb-4 border p-3 rounded">
                    <div class="col-12"><p class="fw-bold text-muted">Sin Corrección (SC)</p></div>
                    <div class="col-md-3 mb-3">
                        <label for="visual_acuity_sc_od" class="form-label">AV SC OD</label>
                        <input type="text" name="visual_acuity_sc_od" id="visual_acuity_sc_od" class="form-control" value="{{ old('visual_acuity_sc_od') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="visual_acuity_sc_oi" class="form-label">AV SC OI</label>
                        <input type="text" name="visual_acuity_sc_oi" id="visual_acuity_sc_oi" class="form-control" value="{{ old('visual_acuity_sc_oi') }}">
                    </div>
                    
                    <div class="col-12"><p class="fw-bold text-muted mt-2">Agudeza Visual (Distancia y Cerca)</p></div>
                    <div class="col-md-3 mb-3">
                        <label for="visual_acuity_dist_od" class="form-label">AV Dist. OD</label>
                        <input type="text" name="visual_acuity_dist_od" id="visual_acuity_dist_od" class="form-control" value="{{ old('visual_acuity_dist_od') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="visual_acuity_dist_oi" class="form-label">AV Dist. OI</label>
                        <input type="text" name="visual_acuity_dist_oi" id="visual_acuity_dist_oi" class="form-control" value="{{ old('visual_acuity_dist_oi') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="visual_acuity_near_od" class="form-label">AV Cerca OD</label>
                        <input type="text" name="visual_acuity_near_od" id="visual_acuity_near_od" class="form-control" value="{{ old('visual_acuity_near_od') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="visual_acuity_near_oi" class="form-label">AV Cerca OI</label>
                        <input type="text" name="visual_acuity_near_oi" id="visual_acuity_near_oi" class="form-control" value="{{ old('visual_acuity_near_oi') }}">
                    </div>
                </div>

                {{-- Subsección: Retinoscopía y Graduación --}}
                <h5 class="mt-3">Retinoscopía y Prescripción</h5>
                <div class="row mb-4 border p-3 rounded">
                    <div class="col-md-3 mb-3">
                        <label for="retinoscopy_dynamic_od" class="form-label">Retinoscopía Dinámica OD</label>
                        <input type="text" name="retinoscopy_dynamic_od" id="retinoscopy_dynamic_od" class="form-control" value="{{ old('retinoscopy_dynamic_od') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="retinoscopy_dynamic_oi" class="form-label">Retinoscopía Dinámica OI</label>
                        <input type="text" name="retinoscopy_dynamic_oi" id="retinoscopy_dynamic_oi" class="form-control" value="{{ old('retinoscopy_dynamic_oi') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="retinoscopy_static_od" class="form-label">Retinoscopía Estática OD</label>
                        <input type="text" name="retinoscopy_static_od" id="retinoscopy_static_od" class="form-control" value="{{ old('retinoscopy_static_od') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="retinoscopy_static_oi" class="form-label">Retinoscopía Estática OI</label>
                        <input type="text" name="retinoscopy_static_oi" id="retinoscopy_static_oi" class="form-control" value="{{ old('retinoscopy_static_oi') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="definitive_prescription_od" class="form-label">Prescripción Definitiva OD</label>
                        <textarea name="definitive_prescription_od" id="definitive_prescription_od" class="form-control" rows="2">{{ old('definitive_prescription_od') }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="definitive_prescription_oi" class="form-label">Prescripción Definitiva OI</label>
                        <textarea name="definitive_prescription_oi" id="definitive_prescription_oi" class="form-control" rows="2">{{ old('definitive_prescription_oi') }}</textarea>
                    </div>
                </div>
                
                {{-- Subsección: Historia Óptica y Tests --}}
                <h5 class="mt-3">Historia Óptica y Pruebas</h5>
                <div class="row mb-4 border p-3 rounded">
                    <div class="col-md-4 mb-3">
                        <label for="last_rx_optic_date" class="form-label">Fecha Última RX</label>
                        <input type="date" name="last_rx_optic_date" id="last_rx_optic_date" class="form-control" value="{{ old('last_rx_optic_date') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="lens_type" class="form-label">Tipo de Lentes Actuales</label>
                        <input type="text" name="lens_type" id="lens_type" class="form-control" value="{{ old('lens_type') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="lens_color" class="form-label">Color/Tratamiento</label>
                        <input type="text" name="lens_color" id="lens_color" class="form-control" value="{{ old('lens_color') }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="test_stereopsis" class="form-label">Resultado Test Estereopsis</label>
                        <input type="text" name="test_stereopsis" id="test_stereopsis" class="form-control" value="{{ old('test_stereopsis') }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="special_instructions" class="form-label">Instrucciones Especiales de Lentes</label>
                        <textarea name="special_instructions" id="special_instructions" class="form-control" rows="2">{{ old('special_instructions') }}</textarea>
                    </div>

                    <div class="col-md-3 form-check mb-3 ms-3">
                        <input type="checkbox" name="test_color" id="test_color" class="form-check-input" value="1" {{ old('test_color') ? 'checked' : '' }}>
                        <label class="form-check-label" for="test_color">Se realizó Test de Color</label>
                    </div>
                    <div class="col-md-3 form-check mb-3">
                        <input type="checkbox" name="amsler_grid" id="amsler_grid" class="form-check-input" value="1" {{ old('amsler_grid') ? 'checked' : '' }}>
                        <label class="form-check-label" for="amsler_grid">Se realizó Amsler Grid</label>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label for="optometry_observations" class="form-label">Observaciones Optométricas Generales</label>
                        <textarea name="optometry_observations" id="optometry_observations" class="form-control" rows="3">{{ old('optometry_observations') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Pestaña 3: Sección Oftalmología --}}
            <div class="tab-pane fade" id="ophthalmology" role="tabpanel" aria-labelledby="ophthalmology-tab">
                <h3>Examen Oftalmológico (Segmento Anterior y Posterior)</h3>

                {{-- Subsección: Segmento Anterior --}}
                <h5 class="mt-3">Segmento Anterior</h5>
                <div class="row mb-4 border p-3 rounded">
                    <div class="col-md-6 mb-3">
                        <label for="eyelids_sup" class="form-label">Párpados Sup. / Inf.</label>
                        <input type="text" name="eyelids_sup" id="eyelids_sup" class="form-control" value="{{ old('eyelids_sup') }}" placeholder="Párpado Superior">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="eyelids_inf" class="form-label">Párpados Inf.</label>
                        <input type="text" name="eyelids_inf" id="eyelids_inf" class="form-control" value="{{ old('eyelids_inf') }}" placeholder="Párpado Inferior">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="eyelids_aberrations" class="form-label">Aberraciones / Posición</label>
                        <input type="text" name="eyelids_aberrations" id="eyelids_aberrations" class="form-control" value="{{ old('eyelids_aberrations') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="eyelids_func_elev" class="form-label">Función Elevación / Tonicidad</label>
                        <input type="text" name="eyelids_func_elev" id="eyelids_func_elev" class="form-control" value="{{ old('eyelids_func_elev') }}">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="conjunctiva" class="form-label">Conjuntiva</label>
                        <input type="text" name="conjunctiva" id="conjunctiva" class="form-control" value="{{ old('conjunctiva') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="sclera" class="form-label">Esclera</label>
                        <input type="text" name="sclera" id="sclera" class="form-control" value="{{ old('sclera') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="lacrimal_vias" class="form-label">Vías Lagrimales</label>
                        <input type="text" name="lacrimal_vias" id="lacrimal_vias" class="form-control" value="{{ old('lacrimal_vias') }}">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label for="cornea_description" class="form-label">Córnea</label>
                        <input type="text" name="cornea_description" id="cornea_description" class="form-control" value="{{ old('cornea_description') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="iris_description" class="form-label">Iris</label>
                        <input type="text" name="iris_description" id="iris_description" class="form-control" value="{{ old('iris_description') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="pupil_description" class="form-label">Pupila</label>
                        <input type="text" name="pupil_description" id="pupil_description" class="form-control" value="{{ old('pupil_description') }}">
                    </div>
                </div>

                {{-- Subsección: Segmento Posterior --}}
                <h5 class="mt-3">Segmento Posterior</h5>
                <div class="row mb-4 border p-3 rounded">
                    <div class="col-md-6 mb-3">
                        <label for="fundus_description" class="form-label">Fondo de Ojo General</label>
                        <input type="text" name="fundus_description" id="fundus_description" class="form-control" value="{{ old('fundus_description') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="disc_optic" class="form-label">Disco Óptico</label>
                        <input type="text" name="disc_optic" id="disc_optic" class="form-control" value="{{ old('disc_optic') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="vessels" class="form-label">Vasos Sanguíneos</label>
                        <input type="text" name="vessels" id="vessels" class="form-control" value="{{ old('vessels') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="retina" class="form-label">Retina Periférica</label>
                        <input type="text" name="retina" id="retina" class="form-control" value="{{ old('retina') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="macula_fovea" class="form-label">Mácula y Fóvea</label>
                        <input type="text" name="macula_fovea" id="macula_fovea" class="form-control" value="{{ old('macula_fovea') }}">
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="ophthalmology_observations" class="form-label">Observaciones Oftalmológicas Generales</label>
                        <textarea name="ophthalmology_observations" id="ophthalmology_observations" class="form-control" rows="3">{{ old('ophthalmology_observations') }}</textarea>
                    </div>
                </div>

            </div>
            
        </div>

        <button type="submit" class="btn btn-success btn-lg w-100 mb-4">
            <i class="fas fa-save me-2"></i> Registrar Nueva Consulta
        </button>
    </form>
</div>

{{-- Script para asegurar que las pestañas funcionen si se usa un layout con Bootstrap JS --}}
{{-- Si estás usando un layout de Laravel que incluye Bootstrap 5 JS, esto no es necesario --}}
@endsection