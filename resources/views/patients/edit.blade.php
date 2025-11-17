@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Editar Paciente: {{ $patient->first_name }} {{ $patient->last_name }}</h2>

    <form action="{{ route('patients.update', $patient->id) }}" method="POST">
        @csrf
        @method('PUT') {{-- Importante para indicar que es una actualización --}}

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="first_name" class="form-label">Nombre *</label>
                <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ old('first_name', $patient->first_name) }}" required>
                @error('first_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">
                <label for="last_name" class="form-label">Apellido *</label>
                <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ old('last_name', $patient->last_name) }}" required>
                @error('last_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            {{-- NUEVO CAMPO: Número de Identidad --}}
            <div class="col-md-6 mb-3">
                <label for="identity_number" class="form-label">Número de Identidad *</label>
                <input type="text" name="identity_number" id="identity_number" class="form-control @error('identity_number') is-invalid @enderror" value="{{ old('identity_number', $patient->identity_number) }}" required>
                @error('identity_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            {{-- FIN NUEVO CAMPO --}}

            <div class="col-md-4 mb-3">
                <label for="birth_date" class="form-label">Fecha de Nacimiento *</label>
                <input type="date" name="birth_date" id="birth_date" class="form-control @error('birth_date') is-invalid @enderror" value="{{ old('birth_date', $patient->birth_date) }}" required>
                @error('birth_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label for="gender" class="form-label">Género *</label>
                <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror" required>
                    <option value="">Seleccione...</option>
                    @foreach(['male', 'female', 'other'] as $g)
                        <option value="{{ $g }}" {{ old('gender', $patient->gender) == $g ? 'selected' : '' }}>
                            {{ ucfirst($g) }}
                        </option>
                    @endforeach
                </select>
                @error('gender')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label for="phone_number" class="form-label">Teléfono *</label>
                <input type="text" name="phone_number" id="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ old('phone_number', $patient->phone_number) }}" required>
                @error('phone_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12 mb-3">
                <label for="email" class="form-label">Correo Electrónico (Opcional)</label>
                <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $patient->email) }}">
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12 mb-3">
                <label for="address" class="form-label">Dirección (Opcional)</label>
                <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror" value="{{ old('address', $patient->address) }}">
                @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-md-12 mb-3">
                <label for="medical_history" class="form-label">Antecedentes Médicos Relevantes (Opcional)</label>
                <textarea name="medical_history" id="medical_history" class="form-control">{{ old('medical_history', $patient->medical_history) }}</textarea>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Actualizar Paciente</button>
        <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection