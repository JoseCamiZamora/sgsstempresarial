@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-end mb-3" style="margin-right: 321px;">
        <a href="{{ route('home') }}" class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left mr-1"></i> Volver al Dashboard
        </a>
    </div>
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h5 class="m-0 font-weight-bold text-primary"><i class="fa fa-building mr-2"></i> Perfil de la Empresa</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('perfil.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <label class="font-weight-bold">Logo Corporativo</label>
                                <div class="mt-2">
                                    @if($perfil && $perfil->logo_path)
                                        <img src="{{ asset('storage/' . $perfil->logo_path) }}" class="img-thumbnail" style="max-height: 150px;">
                                    @else
                                        <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="height: 150px;">
                                            <i class="fa fa-image fa-3x text-gray-300"></i>
                                        </div>
                                    @endif
                                </div>
                                <input type="file" name="logo" class="form-control-file mt-3">
                            </div>
                            
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-3 form-group">
                                        <label>NIT</label>
                                        <input type="text" name="nit" class="form-control" value="{{ $perfil->nit ?? '' }}" required>
                                    </div>
                                    <div class="col-md-9 form-group">
                                        <label>Nombre de la Empresa</label>
                                        <input type="text" name="razon_social" class="form-control" value="{{ $perfil->razon_social ?? '' }}" required>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12 form-group">
                                        <label>Actividad Económica</label>
                                        <textarea name="actividad_economica" class="form-control" rows="3" required>{{ $perfil->actividad_economica ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h6 class="font-weight-bold text-info mb-3"><i class="fa fa-clipboard-check mr-2"></i>Datos para Evaluación Resolución 0312</h6>
                        <div class="row bg-light p-3 rounded mb-4 border">
                            <div class="col-md-6 form-group mb-0">
                                <label class="font-weight-bold text-muted small">
                                    Número Total de Trabajadores <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       name="numero_trabajadores" 
                                       class="form-control @error('numero_trabajadores') is-invalid @enderror" 
                                       value="{{ old('numero_trabajadores', $perfil->numero_trabajadores ?? '') }}" 
                                       min="1" 
                                       required 
                                       placeholder="Ej: 15">
                                @error('numero_trabajadores')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="col-md-6 form-group mb-0">
                                <label class="font-weight-bold text-muted small">
                                    Nivel de Riesgo ARL <span class="text-danger">*</span>
                                </label>
                                <select name="nivel_riesgo" class="form-control @error('nivel_riesgo') is-invalid @enderror" required>
                                    <option value="" disabled {{ old('nivel_riesgo', $perfil->nivel_riesgo ?? '') == '' ? 'selected' : '' }}>Seleccione el riesgo...</option>
                                    <option value="1" {{ old('nivel_riesgo', $perfil->nivel_riesgo ?? '') == '1' ? 'selected' : '' }}>Riesgo I (Mínimo)</option>
                                    <option value="2" {{ old('nivel_riesgo', $perfil->nivel_riesgo ?? '') == '2' ? 'selected' : '' }}>Riesgo II (Bajo)</option>
                                    <option value="3" {{ old('nivel_riesgo', $perfil->nivel_riesgo ?? '') == '3' ? 'selected' : '' }}>Riesgo III (Medio)</option>
                                    <option value="4" {{ old('nivel_riesgo', $perfil->nivel_riesgo ?? '') == '4' ? 'selected' : '' }}>Riesgo IV (Alto)</option>
                                    <option value="5" {{ old('nivel_riesgo', $perfil->nivel_riesgo ?? '') == '5' ? 'selected' : '' }}>Riesgo V (Máximo)</option>
                                </select>
                                @error('nivel_riesgo')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Representante Legal</label>
                                <input type="text" name="representante_legal" class="form-control" value="{{ $perfil->representante_legal ?? '' }}">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Correo de Contacto</label>
                                <input type="email" name="correo_contacto" class="form-control" value="{{ $perfil->correo_contacto ?? '' }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Dirección Física</label>
                            <input type="text" name="direccion" class="form-control" value="{{ $perfil->direccion ?? '' }}">
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fa fa-save mr-1"></i> Guardar Configuración
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection