@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Listado de Pacientes</h2>
        <a href="{{ route('patients.create') }}" class="btn btn-primary">
            + Nuevo Paciente
        </a>
    </div>

    @if ($message = Session::get('success'))
        <div class="alert alert-success">{{ $message }}</div>
    @endif

    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Teléfono</th>
                <th>Email</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($patients as $patient)
            <tr>
                <td>{{ $patient->id }}</td>
                <td>{{ $patient->first_name }} {{ $patient->last_name }}</td>
                <td>{{ $patient->phone_number }}</td>
                <td>{{ $patient->email }}</td>
                <td>
                    <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-sm btn-info">Ver</a>
                    <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-sm btn-warning">Editar</a>
                    <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este paciente?')">Eliminar</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $patients->links() }}
    </div>
</div>
@endsection