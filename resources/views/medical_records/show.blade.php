@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Expediente de Consulta #{{ $record->id }}</h2>
        <div>
            <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Volver al Paciente
            </a>
            {{-- Puedes agregar un botón de edición si lo deseas --}}
            {{-- <a href="{{ route('medical_records.edit', [$patient->id, $record->id]) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar Consulta
            </a> --}}
        </div>
    </div>

    {{-- Información del Paciente (Cabecera) --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            Información del Paciente: {{ $patient->first_name }} {{ $patient->last_name }}
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
                    <strong>Doctor(a):</strong> {{ $record->doctor_name ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Sistema de Pestañas para la información de la consulta --}}
    <ul class="nav nav-tabs" id="medicalRecordShowTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="general-info-tab" data-bs-toggle="tab" data-bs-target="#general-info" type="button" role="tab" aria-controls="general-info" aria-selected="true">1. Datos Generales</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="optometry-info-tab" data-bs-toggle="tab" data-bs-target="#optometry-info" type="button" role="tab" aria-controls="optometry-info" aria-selected="false">2. Sección Optometría</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="ophthalmology-info-tab" data-bs-toggle="tab" data-bs-target="#ophthalmology-info" type="button" role="tab" aria-controls="ophthalmology-info" aria-selected="false">3. Sección Oftalmología</button>
        </li>
    </ul>

    <div class="tab-content border border-top-0 p-4 mb-4 bg-white shadow-sm" id="medicalRecordShowTabsContent">
        
        {{-- Pestaña 1: Datos Generales --}}
        <div class="tab-pane fade show active" id="general-info" role="tabpanel" aria-labelledby="general-info-tab">
            <h4 class="mb-3">Información Principal de la Consulta</h4>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <strong>Fecha de Consulta:</strong> 
                    <p>{{ \Carbon\Carbon::parse($record->record_date)->format('d/m/Y') }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>Ocupación:</strong> 
                    <p>{{ $record->occupation ?? 'No especificada' }}</p>
                </div>
                <div class="col-md-4 mb-3">
                    <strong>Ojo Dominante:</strong> 
                    <p>{{ strtoupper($record->dominant_eye) ?? 'N/A' }}</p>
                </div>
                
                <div class="col-md-12 mb-3">
                    <strong>Motivo de la Consulta:</strong> 
                    <p class="border p-2 rounded bg-light">{{ $record->consultation_reason ?? 'Sin datos' }}</p>
                </div>

                <div class="col-md-12 mb-3">
                    <strong>Síntomas Actuales:</strong> 
                    <p class="border p-2 rounded bg-light">{{ $record->symptoms ?? 'Sin datos' }}</p>
                </div>
            </div>
        </div>

        {{-- Pestaña 2: Sección Optometría --}}
        <div class="tab-pane fade" id="optometry-info" role="tabpanel" aria-labelledby="optometry-info-tab">
            <h4 class="mb-3">Examen Optométrico y Prescripción</h4>

            {{-- Subsección: Agudeza Visual --}}
            <h5 class="mt-3 text-primary">Agudeza Visual</h5>
            <div class="row mb-4 border p-3 rounded bg-light">
                <div class="col-md-3 mb-2">
                    <strong>AV SC OD:</strong> {{ $record->visual_acuity_sc_od ?? 'N/A' }}
                </div>
                <div class="col-md-3 mb-2">
                    <strong>AV SC OI:</strong> {{ $record->visual_acuity_sc_oi ?? 'N/A' }}
                </div>
                <div class="col-md-3 mb-2">
                    <strong>AV Dist. OD:</strong> {{ $record->visual_acuity_dist_od ?? 'N/A' }}
                </div>
                <div class="col-md-3 mb-2">
                    <strong>AV Dist. OI:</strong> {{ $record->visual_acuity_dist_oi ?? 'N/A' }}
                </div>
                <div class="col-md-3 mb-2">
                    <strong>AV Cerca OD:</strong> {{ $record->visual_acuity_near_od ?? 'N/A' }}
                </div>
                <div class="col-md-3 mb-2">
                    <strong>AV Cerca OI:</strong> {{ $record->visual_acuity_near_oi ?? 'N/A' }}
                </div>
                <div class="col-md-6 mb-2">
                    <strong>Test de Color:</strong> {{ $record->test_color ? 'Realizado' : 'No' }}
                </div>
            </div>

            {{-- Subsección: Retinoscopía y Graduación --}}
            <h5 class="mt-3 text-primary">Retinoscopía y Prescripción</h5>
            <div class="row mb-4 border p-3 rounded bg-light">
                <div class="col-md-3 mb-2">
                    <strong>Retino Din. OD:</strong> {{ $record->retinoscopy_dynamic_od ?? 'N/A' }}
                </div>
                <div class="col-md-3 mb-2">
                    <strong>Retino Din. OI:</strong> {{ $record->retinoscopy_dynamic_oi ?? 'N/A' }}
                </div>
                <div class="col-md-3 mb-2">
                    <strong>Retino Est. OD:</strong> {{ $record->retinoscopy_static_od ?? 'N/A' }}
                </div>
                <div class="col-md-3 mb-2">
                    <strong>Retino Est. OI:</strong> {{ $record->retinoscopy_static_oi ?? 'N/A' }}
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Prescripción Definitiva OD:</strong> 
                    <p class="border p-2 rounded bg-white">{{ $record->definitive_prescription_od ?? 'Sin prescripción' }}</p>
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Prescripción Definitiva OI:</strong> 
                    <p class="border p-2 rounded bg-white">{{ $record->definitive_prescription_oi ?? 'Sin prescripción' }}</p>
                </div>
            </div>

            {{-- Subsección: Lentes y Tests --}}
            <h5 class="mt-3 text-primary">Historia Óptica y Pruebas</h5>
            <div class="row mb-4 border p-3 rounded bg-light">
                <div class="col-md-4 mb-2">
                    <strong>Fecha Última RX:</strong> 
                    <p>{{ $record->last_rx_optic_date ? \Carbon\Carbon::parse($record->last_rx_optic_date)->format('d/m/Y') : 'N/A' }}</p>
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Tipo de Lentes:</strong> 
                    <p>{{ $record->lens_type ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Color/Tratamiento:</strong> 
                    <p>{{ $record->lens_color ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-2">
                    <strong>Test Estereopsis:</strong> 
                    <p>{{ $record->test_stereopsis ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-2">
                    <strong>Amsler Grid:</strong> 
                    <p>{{ $record->amsler_grid ? 'Realizado' : 'No' }}</p>
                </div>
                <div class="col-md-12 mb-3">
                    <strong>Instrucciones Especiales:</strong> 
                    <p class="border p-2 rounded bg-white">{{ $record->special_instructions ?? 'No hay instrucciones' }}</p>
                </div>
                <div class="col-md-12 mb-3">
                    <strong>Observaciones Optométricas:</strong> 
                    <p class="border p-2 rounded bg-white">{{ $record->optometry_observations ?? 'No hay observaciones' }}</p>
                </div>
            </div>
        </div>

        {{-- Pestaña 3: Sección Oftalmología --}}
        <div class="tab-pane fade" id="ophthalmology-info" role="tabpanel" aria-labelledby="ophthalmology-info-tab">
            <h4 class="mb-3">Examen Oftalmológico (Segmento Anterior y Posterior)</h4>

            {{-- Subsección: Segmento Anterior --}}
            <h5 class="mt-3 text-success">Segmento Anterior</h5>
            <div class="row mb-4 border p-3 rounded bg-light">
                <div class="col-md-6 mb-2">
                    <strong>Párpados (Sup/Inf):</strong> 
                    <p>{{ $record->eyelids_sup ?? 'N/A' }} / {{ $record->eyelids_inf ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-2">
                    <strong>Aberraciones:</strong> 
                    <p>{{ $record->eyelids_aberrations ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Conjuntiva:</strong> 
                    <p>{{ $record->conjunctiva ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Esclera:</strong> 
                    <p>{{ $record->sclera ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Vías Lagrimales:</strong> 
                    <p>{{ $record->lacrimal_vias ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Córnea:</strong> 
                    <p>{{ $record->cornea_description ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Iris:</strong> 
                    <p>{{ $record->iris_description ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Pupila:</strong> 
                    <p>{{ $record->pupil_description ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- Subsección: Segmento Posterior --}}
            <h5 class="mt-3 text-success">Segmento Posterior</h5>
            <div class="row mb-4 border p-3 rounded bg-light">
                <div class="col-md-6 mb-2">
                    <strong>Fondo de Ojo General:</strong> 
                    <p>{{ $record->fundus_description ?? 'N/A' }}</p>
                </div>
                <div class="col-md-6 mb-2">
                    <strong>Disco Óptico:</strong> 
                    <p>{{ $record->disc_optic ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Vasos Sanguíneos:</strong> 
                    <p>{{ $record->vessels ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Retina Periférica:</strong> 
                    <p>{{ $record->retina ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4 mb-2">
                    <strong>Mácula y Fóvea:</strong> 
                    <p>{{ $record->macula_fovea ?? 'N/A' }}</p>
                </div>
                <div class="col-md-12 mb-3">
                    <strong>Observaciones Oftalmológicas:</strong> 
                    <p class="border p-2 rounded bg-white">{{ $record->ophthalmology_observations ?? 'No hay observaciones' }}</p>
                </div>
            </div>

        </div>
        
    </div>
</div>
@endsection