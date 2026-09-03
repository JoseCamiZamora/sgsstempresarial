@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800" style="color: #2c3e50;">Editar Usuario: <span class="text-primary">{{ $usuario->nombres }}</span></h2>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left mr-1"></i> Volver
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Identificación</label>
                            <input type="text" name="identificacion" class="form-control" value="{{ old('identificacion', $usuario->identificacion) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Nombres Completos</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $usuario->name) }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $usuario->email) }}" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Teléfono</label>
                            <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $usuario->telefono) }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Nueva Contraseña <span class="text-info font-weight-normal">(Dejar en blanco para no cambiar)</span></label>
                            <input type="password" name="password" class="form-control" minlength="8">
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Estado</label>
                            <select name="estado" class="form-control" required>
                                <option value="A" {{ $usuario->estado == 'A' ? 'selected' : '' }}>Activo</option>
                                <option value="I" {{ $usuario->estado == 'I' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small">Roles en el Sistema</label>
                            <div class="border rounded p-2">
                                @foreach($roles as $rol)
                                    <label class="d-block font-weight-normal mb-1">
                                        <input type="checkbox" name="roles[]" value="{{ $rol->name }}" {{ in_array($rol->name, old('roles', $userRoles)) ? 'checked' : '' }}>
                                        {{ $rol->name }}
                                    </label>
                                @endforeach
                            </div>
                            <small class="text-muted">Puede marcar más de uno si el usuario necesita varios accesos a la vez.</small>
                        </div>
                    </div>
                </div>

                <hr class="mt-2 mb-4">
                <div class="text-right">
                    <button type="submit" class="btn btn-warning px-4 shadow-sm font-weight-bold">
                        <i class="fa fa-refresh mr-1"></i> Actualizar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection