@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800" style="color: #2c3e50;">Crear Nuevo Usuario</h2>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left mr-1"></i> Volver
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form action="{{ route('usuarios.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Identificación</label>
                            <input type="text" name="identificacion" class="form-control" value="{{ old('identificacion') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Nombres Completos</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Teléfono (Opcional)</label>
                            <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Contraseña</label>
                            <input type="password" name="password" class="form-control" required minlength="8">
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Estado</label>
                            <select name="estado" class="form-control" required>
                                <option value="A" selected>Activo</option>
                                <option value="I">Inactivo</option>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small">Rol en el Sistema</label>
                            <select name="rol" class="form-control" required>
                                <option value="" disabled selected>Seleccione un rol...</option>
                                @foreach($roles as $rol)
                                    <option value="{{ $rol->name }}">{{ $rol->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="mt-2 mb-4">
                
                <div class="text-right">
                    <button type="submit" class="btn btn-primary px-4 shadow-sm" style="background-color: #4A90E2; border: none;">
                        <i class="fa fa-save mr-1"></i> Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection