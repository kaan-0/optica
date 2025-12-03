@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detalle del Paciente: {{ $patient->first_name }} {{ $patient->last_name }}</h2>
        <a href="{{ route('patients.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Volver al Listado</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- CARD: Información Personal --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            Información Personal
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Nombre Completo:</strong> {{ $patient->first_name }} {{ $patient->last_name }}</p>
                    <p><strong>Número de Identidad:</strong> {{ $patient->identity_number }}</p> 
                    <p><strong>Fecha de Nacimiento:</strong> {{ \Carbon\Carbon::parse($patient->birth_date)->format('d/m/Y') }}</p>
                    <p><strong>Género:</strong> {{ ucfirst($patient->gender) }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Teléfono:</strong> {{ $patient->phone_number }}</p>
                    <p><strong>Email:</strong> {{ $patient->email ?? 'N/A' }}</p>
                    <p><strong>Dirección:</strong> {{ $patient->address ?? 'N/A' }}</p>
                </div>
                <div class="col-md-12 mt-3">
                    <p><strong>Antecedentes Médicos:</strong> {{ $patient->medical_history ?? 'Sin antecedentes registrados.' }}</p>
                </div>
            </div>
        </div>
        <div class="card-footer text-end">
            <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-warning"><i class="fas fa-user-edit me-1"></i> Editar Datos</a>
        </div>
    </div>

    {{-- CARD: Historial de Consultas --}}
    <div class="card shadow-sm">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <h5>Historial de Consultas Oftalmológicas ({{ $patient->medicalRecords->count() }})</h5>
            
            {{-- El botón ahora enlaza a la creación de un nuevo expediente para este paciente --}}
            <a href="{{ route('medical_records.create', ['patient' => $patient->id]) }}" class="btn btn-light">
                <i class="fas fa-plus me-1"></i> Crear Nueva Consulta
            </a>
        </div>
        <div class="card-body">
            @if ($patient->medicalRecords->isEmpty())
                <div class="alert alert-info mb-0">
                    Este paciente no tiene expedientes o consultas registradas.
                </div>
            @else
                {{-- Listado de Consultas --}}
                <div class="list-group list-group-flush">
                    {{-- @foreach ($patient->medicalRecords->sortByDesc('record_date') as $record) --}}
                    @foreach ($patient->medicalRecords as $record)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <p class="mb-0">
                                    <span class="badge bg-secondary me-2">Consulta #{{ $loop->iteration }}</span>
                                    <strong>Fecha:</strong> {{ \Carbon\Carbon::parse($record->record_date)->format('d/m/Y') }}
                                </p>
                                <p class="mb-0 text-muted small">
                                    Dr(a). {{ $record->doctor_name ?? 'N/A' }} | Motivo: {{ Str::limit($record->consultation_reason, 100) }}
                                </p>
                            </div>
                            <div class="d-flex align-items-center">
                                {{-- Se asume que existe la ruta 'medical_records.show' para ver el detalle del expediente --}}
                                <a href="{{ route('medical_records.show', $record->id) }}" class="btn btn-sm btn-outline-info me-2" title="Ver Detalle">
                                    <i class="fas fa-eye"></i> Ver Detalle
                                </a>
                                
                                {{-- Botón de Edición (ya implementado en el controlador) --}}
                                <a href="{{ route('medical_records.edit', $record->id) }}" class="btn btn-sm btn-warning me-2" title="Editar Expediente">
                                    <i class="fas fa-edit"></i> Editar
                                </a>

                                {{-- Botón de Eliminación (Requiere Modal y Formulario) --}}
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteRecordModal{{ $record->id }}" title="Eliminar Expediente">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Modal de Confirmación de Eliminación para cada Expediente --}}
                        <div class="modal fade" id="deleteRecordModal{{ $record->id }}" tabindex="-1" aria-labelledby="deleteRecordModalLabel{{ $record->id }}" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title" id="deleteRecordModalLabel{{ $record->id }}"><i class="fas fa-exclamation-triangle me-2"></i> Confirmar Eliminación</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        ¿Estás absolutamente seguro de que deseas eliminar la consulta del día <strong>{{ \Carbon\Carbon::parse($record->record_date)->format('d/m/Y') }}</strong>?
                                        <br>
                                        Esta acción es **irreversible**.
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <form method="POST" action="{{ route('medical_records.destroy', $record->id) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i> Sí, Eliminar Permanentemente</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection