@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h2 class="mb-0">Registrar Nuevo Paciente y Examen Inicial</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('patients.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="record_date" class="form-label">Fecha de Visita *</label>
                        <input type="date" name="record_date" id="record_date" class="form-control" value="{{ old('record_date', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-8">
                        <label for="doctor_name" class="form-label">Dr. Examinador (Opcional)</label>
                        <input type="text" name="doctor_name" id="doctor_name" class="form-control" value="{{ old('doctor_name') }}">
                    </div>
                </div>

                {{-- ESTRUCTURA DE PESTAÑAS --}}
                <ul class="nav nav-tabs" id="patientTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="data-tab" data-bs-toggle="tab" data-bs-target="#data-pane" type="button" role="tab" aria-controls="data-pane" aria-selected="true">
                            1. Datos Personales
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="exam2-tab" data-bs-toggle="tab" data-bs-target="#exam2-pane" type="button" role="tab" aria-controls="exam2-pane" aria-selected="false">
                            2. Examen Optométrico
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="exam-tab" data-bs-toggle="tab" data-bs-target="#exam-pane" type="button" role="tab" aria-controls="exam-pane" aria-selected="false">
                            3. Examen Oftalmológico
                        </button>
                    </li>
                </ul>
                
                {{-- CONTENIDO DE LAS PESTAÑAS --}}
                <div class="tab-content border border-top-0 p-3" id="patientTabsContent">
                    
                    {{-- PESTAÑA 1: DATOS PERSONALES (Tabla PATIENTS) --}}
                    <div class="tab-pane fade show active" id="data-pane" role="tabpanel" aria-labelledby="data-tab">
                        <h4 class="mb-3">Información General del Paciente</h4>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">Nombre *</label>
                                <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name') }}" required>
                                @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <!-- ... Otros campos de datos personales ... -->
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Apellido *</label>
                                <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name') }}" required>
                                @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="identity_number" class="form-label">Número de Identidad *</label>
                                <input type="text" name="identity_number" id="identity_number" class="form-control @error('identity_number') is-invalid @enderror" value="{{ old('identity_number') }}" required>
                                @error('identity_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="birth_date" class="form-label">Fecha de Nacimiento *</label>
                                <input type="date" name="birth_date" id="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date') }}" required>
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="gender" class="form-label">Género *</label>
                                <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                    <option value="">Seleccione...</option>
                                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Masculino</option>
                                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Femenino</option>
                                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Otro</option>
                                </select>
                                @error('gender')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="phone_number" class="form-label">Teléfono *</label>
                                <input type="text" name="phone_number" id="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number') }}" required>
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="email" class="form-label">Correo Electrónico (Opcional)</label>
                                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="address" class="form-label">Dirección (Opcional)</label>
                                <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address') }}">
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="medical_history" class="form-label">Antecedentes Médicos Relevantes (Opcional)</label>
                                <textarea name="medical_history" id="medical_history" class="form-control">{{ old('medical_history') }}</textarea>
                            </div>
                        </div> 
                    </div> 

                    {{-- PESTAÑA 2: EXAMEN OPTOMÉTRICO (Tabla MEDICAL_RECORDS) --}}
                    <div class="tab-pane fade" id="exam2-pane" role="tabpanel" aria-labelledby="exam2-tab">
                        <h4 class="mb-3">Examen Optométrico y Motivo de Consulta</h4>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="consultation_reason" class="form-label">Motivo de la Consulta</label>
                                <textarea name="consultation_reason" id="consultation_reason" class="form-control">{{ old('consultation_reason') }}</textarea>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="symptoms" class="form-label">Síntomas</label>
                                <input type="text" name="symptoms" id="symptoms" class="form-control" value="{{ old('symptoms') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="occupation" class="form-label">Ocupación Habitual</label>
                                <input type="text" name="occupation" id="occupation" class="form-control" value="{{ old('occupation') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="dominant_eye" class="form-label">Ojo Dominante</label>
                                
                                <select name="dominant_eye" id="dominant_eye" class="form-select">
                                    <option value="">Seleccione...</option>
                                    <option value="right" {{ old('dominant_eye') == 'right' ? 'selected' : '' }}>Derecho</option>
                                    <option value="left" {{ old('dominant_eye') == 'left' ? 'selected' : '' }}>Izquierdo</option>
                                    
                                </select>
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

                            <h5 class="mt-4 mb-2 text-primary">Agudeza Visual (V)</h5>
                            <div class="col-md-3 mb-3">
                                <label for="visual_acuity_sc_od" class="form-label">V. SC (OD)</label>
                                <input type="text" name="visual_acuity_sc_od" id="visual_acuity_sc_od" class="form-control" value="{{ old('visual_acuity_sc_od') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="visual_acuity_sc_oi" class="form-label">V. SC (OI)</label>
                                <input type="text" name="visual_acuity_sc_oi" id="visual_acuity_sc_oi" class="form-control" value="{{ old('visual_acuity_sc_oi') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="visual_acuity_dist_od" class="form-label">V. Dist (OD)</label>
                                <input type="text" name="visual_acuity_dist_od" id="visual_acuity_dist_od" class="form-control" value="{{ old('visual_acuity_dist_od') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="visual_acuity_dist_oi" class="form-label">V. Dist (OI)</label>
                                <input type="text" name="visual_acuity_dist_oi" id="visual_acuity_dist_oi" class="form-control" value="{{ old('visual_acuity_dist_oi') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="visual_acuity_near_od" class="form-label">V. Cerca (OD)</label>
                                <input type="text" name="visual_acuity_near_od" id="visual_acuity_near_od" class="form-control" value="{{ old('visual_acuity_near_od') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="visual_acuity_near_oi" class="form-label">V. Cerca (OI)</label>
                                <input type="text" name="visual_acuity_near_oi" id="visual_acuity_near_oi" class="form-control" value="{{ old('visual_acuity_near_oi') }}">
                            </div>
                            
                            <h5 class="mt-4 mb-2 text-primary">Retinoscopia y RX</h5>
                            <div class="col-md-3 mb-3">
                                <label for="retinoscopy_static_od" class="form-label">Ret. Estática (OD)</label>
                                <input type="text" name="retinoscopy_static_od" id="retinoscopy_static_od" class="form-control" value="{{ old('retinoscopy_static_od') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="retinoscopy_static_oi" class="form-label">Ret. Estática (OI)</label>
                                <input type="text" name="retinoscopy_static_oi" id="retinoscopy_static_oi" class="form-control" value="{{ old('retinoscopy_static_oi') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="retinoscopy_dynamic_od" class="form-label">Ret. Dinámica (OD)</label>
                                <input type="text" name="retinoscopy_dynamic_od" id="retinoscopy_dynamic_od" class="form-control" value="{{ old('retinoscopy_dynamic_od') }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="retinoscopy_dynamic_oi" class="form-label">Ret. Dinámica (OI)</label>
                                <input type="text" name="retinoscopy_dynamic_oi" id="retinoscopy_dynamic_oi" class="form-control" value="{{ old('retinoscopy_dynamic_oi') }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="definitive_prescription_od" class="form-label">Prescripción Definitiva (OD)</label>
                                <textarea name="definitive_prescription_od" id="definitive_prescription_od" class="form-control">{{ old('definitive_prescription_od') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="definitive_prescription_oi" class="form-label">Prescripción Definitiva (OI)</label>
                                <textarea name="definitive_prescription_oi" id="definitive_prescription_oi" class="form-control">{{ old('definitive_prescription_oi') }}</textarea>
                            </div>

                            <div class="col-md-12 mb-4">
                                <label for="optometry_observations" class="form-label">Observaciones Optométricas Generales</label>
                                <textarea name="optometry_observations" id="optometry_observations" class="form-control">{{ old('optometry_observations') }}</textarea>
                            </div>

                            <div class="col-md-4 mb-3 form-check">
                                <input type="checkbox" name="test_color" id="test_color" class="form-check-input" value="1" {{ old('test_color') ? 'checked' : '' }}>
                                <label class="form-check-label" for="test_color">Test Color Realizado</label>
                            </div>
                            <div class="col-md-4 mb-3 form-check">
                                <input type="checkbox" name="amsler_grid" id="amsler_grid" class="form-check-input" value="1" {{ old('amsler_grid') ? 'checked' : '' }}>
                                <label class="form-check-label" for="amsler_grid">Amsler Grid Realizado</label>
                            </div>

                        </div>
                    </div>

                    {{-- PESTAÑA 3: EXAMEN OFTALMOLÓGICO (Tabla MEDICAL_RECORDS) --}}
                    <div class="tab-pane fade" id="exam-pane" role="tabpanel" aria-labelledby="exam-tab">
                        <h4 class="mb-3">Examen Oftalmológico Detallado</h4>
                        <div class="row">

                            <h5 class="mt-2 mb-2 text-primary">Anexos Oculares y Segmento Anterior</h5>

                            <div class="col-md-6 mb-3">
                                <label for="cornea_description" class="form-label">Córnea / Limbo / Transp.</label>
                                <input type="text" name="cornea_description" id="cornea_description" class="form-control" value="{{ old('cornea_description') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="iris_description" class="form-label">Iris / Pupila (Color, Reflejos, Dil. Max)</label>
                                <input type="text" name="iris_description" id="iris_description" class="form-control" value="{{ old('iris_description') }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="crystalline" class="form-label">Cristalino (Núcleo, Cápsulas)</label>
                                <textarea name="crystalline" id="crystalline" class="form-control">{{ old('crystalline') }}</textarea>
                            </div>
                            
                            <h5 class="mt-4 mb-2 text-primary">Fondo de Ojo y Estructuras</h5>
                            
                            <div class="col-md-6 mb-3">
                                <label for="disc_optic" class="form-label">Disco Óptico / Copa / Disco</label>
                                <input type="text" name="disc_optic" id="disc_optic" class="form-control" value="{{ old('disc_optic') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="macula_fovea" class="form-label">Mácula / Fóvea</label>
                                <input type="text" name="macula_fovea" id="macula_fovea" class="form-control" value="{{ old('macula_fovea') }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="fundus_description" class="form-label">Vítrio y Retina Periférica</label>
                                <textarea name="fundus_description" id="fundus_description" class="form-control">{{ old('fundus_description') }}</textarea>
                            </div>

                            <div class="col-md-12 mb-4">
                                <label for="ophthalmology_observations" class="form-label">Observaciones Oftalmológicas Generales</label>
                                <textarea name="ophthalmology_observations" id="ophthalmology_observations" class="form-control">{{ old('ophthalmology_observations') }}</textarea>
                            </div>

                        </div>
                    </div> 

                </div> {{-- Cierre del tab-content --}}
                
                {{-- Botones fuera del contenido de pestañas --}}
                <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                    <button type="submit" class="btn btn-success me-2">
                        <i class="fas fa-save me-1"></i> Guardar Paciente y Examen Inicial
                    </button>
                    <a href="{{ route('patients.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection