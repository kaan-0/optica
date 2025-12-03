@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Pacientes</h2>
        <a href="{{ route('patients.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Nuevo Paciente
        </a>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ $message }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Formulario de Búsqueda --}}
    <form action="{{ route('patients.index') }}" method="GET" class="mb-4">
        <div class="input-group">
            {{-- Mantiene el valor actual de búsqueda en el campo --}}
            <input type="text" name="search" class="form-control" placeholder="Buscar por Nombre, Apellido o Número de Identidad..." 
                   value="{{ request('search') }}">
            
            <button class="btn btn-outline-secondary" type="submit">
                <i class="fas fa-search"></i> Buscar
            </button>
            
            {{-- Botón para limpiar la búsqueda --}}
            @if(request('search'))
                <a href="{{ route('patients.index') }}" class="btn btn-outline-danger">
                    <i class="fas fa-times"></i> Limpiar
                </a>
            @endif
        </div>
    </form>
    {{-- Fin Formulario de Búsqueda --}}

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    {{-- <th>ID</th> --}}
                    <th>Nombre Completo</th>
                    <th>Identidad</th> {{-- Agregamos la columna Identidad para visibilidad --}}
                    <th>Teléfono</th>
                    <th width="150px">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($patients as $patient)
                <tr>
                    {{-- <td>{{ $patient->id }}</td> --}}
                    <td>{{ $patient->first_name }} {{ $patient->last_name }}</td>
                    <td>{{ $patient->identity_number }}</td> {{-- Mostramos el número de identidad --}}
                    <td>{{ $patient->phone_number }}</td>
                    <td>
                        <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-sm btn-info text-white" title="Ver Detalle"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-sm btn-warning" title="Editar"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" style="display:inline-block;">
                            @csrf
                            @method('DELETE')
                            {{-- Reemplazamos alert() por un modal o un botón que dispare confirmación moderna --}}
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar a {{ $patient->first_name }} {{ $patient->last_name }}? Esta acción es irreversible.')" title="Eliminar"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            No se encontraron pacientes que coincidan con su búsqueda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{-- Aseguramos que los links de paginación incluyan el parámetro de búsqueda --}}
        {{ $patients->appends(request()->query())->links() }}
    </div>
</div>
@endsection