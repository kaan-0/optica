@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detalle del Paciente: {{ $patient->first_name }} {{ $patient->last_name }}</h2>
        <a href="{{ route('patients.index') }}" class="btn btn-secondary">Volver al Listado</a>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            Información Personal
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nombre Completo:</strong> {{ $patient->first_name }} {{ $patient->last_name }}</p>
                    {{-- NUEVO CAMPO: Número de Identidad --}}
                    <p><strong>Número de Identidad:</strong> {{ $patient->identity_number }}</p> 
                    {{-- FIN NUEVO CAMPO --}}
                    <p><strong>Fecha de Nacimiento:</strong> {{ \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') }}</p>
                    <p><strong>Género:</strong> {{ ucfirst($patient->gender) }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Teléfono:</strong> {{ $patient->phone_number }}</p>
                    <p><strong>Email:</strong> {{ $patient->email ?? 'N/A' }}</p>
                    <p><strong>Dirección:</strong> {{ $patient->address ?? 'N/A' }}</p>
                </div>
                <div class="col-md-12">
                    <p><strong>Antecedentes Médicos:</strong> {{ $patient->medical_history ?? 'Sin antecedentes registrados.' }}</p>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-warning">Editar Datos</a>
        </div>
    </div>

    {{-- Aquí se integrará el Módulo de Historial Clínico Oftalmológico --}}
    <div class="card">
        <div class="card-header bg-success text-white">
            Historial de Consultas Oftalmológicas
        </div>
        <div class="card-body">
            <p class="text-muted">Este espacio es para las **consultas**, **recetas** y **exámenes visuales** (el siguiente paso del desarrollo).</p>
            <button class="btn btn-success mt-2">Crear Nueva Consulta</button>
        </div>
    </div>

</div>
@endsection