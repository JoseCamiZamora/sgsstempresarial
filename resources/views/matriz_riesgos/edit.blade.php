@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800" style="color: #2c3e50;">Editar Riesgo #{{ $riesgo->id }}</h2>
        <a href="{{ route('matriz-riesgos.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left mr-1"></i> Volver a la Matriz
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('matriz-riesgos.update', $riesgo->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm border-left-primary h-100">
                    <div class="card-header bg-white font-weight-bold text-primary">
                        <i class="fa fa-map-marker mr-1"></i> 1. Ubicación y Actividad
                    </div>
                    <div class="card-body row">
                        <div class="form-group col-md-3 mb-3">
                            <label class="small font-weight-bold text-muted">Proceso</label>
                            <input type="text" name="proceso" class="form-control" value="{{ old('proceso', $riesgo->proceso) }}" required>
                        </div>
                        <div class="form-group col-md-3 mb-3">
                            <label class="small font-weight-bold text-muted">Zona / Lugar</label>
                            <input type="text" name="zona_lugar" class="form-control" value="{{ old('zona_lugar', $riesgo->zona_lugar) }}" required>
                        </div>
                        <div class="form-group col-md-4 mb-3">
                            <label class="small font-weight-bold text-muted">Actividad Específica</label>
                            <input type="text" name="actividad" class="form-control" value="{{ old('actividad', $riesgo->actividad) }}" required>
                        </div>
                        <div class="form-group col-md-2 mb-3">
                            <label class="small font-weight-bold text-muted">¿Es Rutinaria?</label>
                            <select name="es_rutinaria" class="form-control" required>
                                <option value="1" {{ $riesgo->es_rutinaria == 1 ? 'selected' : '' }}>Sí</option>
                                <option value="0" {{ $riesgo->es_rutinaria == 0 ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8 mb-4">
                <div class="card shadow-sm border-left-warning h-100">
                    <div class="card-header bg-white font-weight-bold text-warning">
                        <i class="fa fa-exclamation-triangle mr-1"></i> 2. Identificación del Peligro
                    </div>
                    <div class="card-body">
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted">Clasificación</label>
                            <select name="clasificacion_peligro" class="form-control" required>
                                @php
                                    $clasificaciones = ['Biológico', 'Físico', 'Químico', 'Psicosocial', 'Biomecánico', 'Condiciones de Seguridad', 'Fenómenos Naturales'];
                                @endphp
                                @foreach($clasificaciones as $clase)
                                    <option value="{{ $clase }}" {{ $riesgo->clasificacion_peligro == $clase ? 'selected' : '' }}>
                                        {{ $clase }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted">Descripción del Peligro</label>
                            <textarea name="descripcion_peligro" class="form-control" rows="2" required>{{ old('descripcion_peligro', $riesgo->descripcion_peligro) }}</textarea>
                        </div>
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-muted">Efectos Posibles</label>
                            <textarea name="efectos_posibles" class="form-control" rows="2" required>{{ old('efectos_posibles', $riesgo->efectos_posibles) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-left-danger h-100">
                    <div class="card-header bg-white font-weight-bold text-danger">
                        <i class="fa fa-calculator mr-1"></i> 3. Valoración
                    </div>
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="form-group mb-4">
                            <label class="small font-weight-bold text-muted">Nivel de Riesgo</label>
                            <select name="nivel_riesgo" class="form-control form-control-lg text-center font-weight-bold" required>
                                <option value="Bajo" class="text-success" {{ $riesgo->nivel_riesgo == 'Bajo' ? 'selected' : '' }}>BAJO</option>
                                <option value="Medio" class="text-warning" {{ $riesgo->nivel_riesgo == 'Medio' ? 'selected' : '' }}>MEDIO</option>
                                <option value="Alto" class="text-danger" {{ $riesgo->nivel_riesgo == 'Alto' ? 'selected' : '' }}>ALTO</option>
                                <option value="Extremo" class="text-dark" {{ $riesgo->nivel_riesgo == 'Extremo' ? 'selected' : '' }}>EXTREMO</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-warning btn-lg btn-block shadow-sm mt-auto" style="font-weight: bold; font-size: 1.1rem; color: #fff;">
                            <i class="fa fa-refresh mr-1"></i> Actualizar Riesgo
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection