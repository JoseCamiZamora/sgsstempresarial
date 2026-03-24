@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            <i class="fa fa-pencil-square-o text-info mr-2"></i> Gestionar Actividad
        </h1>
        <a href="{{ route('plan-trabajo.index') }}" class="btn btn-outline-secondary shadow-sm font-weight-bold">
            <i class="fa fa-arrow-left mr-1"></i> Volver al Cronograma
        </a>
    </div>

    <div class="card shadow mb-4 border-bottom-info">
        <div class="card-body">
            <form action="{{ route('plan-trabajo.update', $planTrabajo->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 border-right">
                        <h5 class="font-weight-bold text-info mb-4">Datos de la Actividad</h5>
                        
                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Nombre de la Actividad</label>
                            <input type="text" name="actividad" class="form-control" value="{{ $planTrabajo->actividad }}" required>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Fecha Programada</label>
                            <input type="date" name="fecha_programada" class="form-control" value="{{ $planTrabajo->fecha_programada }}" required>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Responsable</label>
                            <select name="responsable_id" class="form-control" required>
                                @foreach($usuarios as $user)
                                    <option value="{{ $user->id }}" {{ $planTrabajo->responsable_id == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 pl-4">
                        <h5 class="font-weight-bold text-success mb-4">Gestión y Cumplimiento</h5>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Estado Actual de la Actividad</label>
                            <select name="estado" class="form-control font-weight-bold text-primary" required>
                                <option value="Pendiente" {{ $planTrabajo->estado == 'Pendiente' ? 'selected' : '' }}>⏳ Pendiente</option>
                                <option value="En Ejecución" {{ $planTrabajo->estado == 'En Ejecución' ? 'selected' : '' }}>🔄 En Ejecución</option>
                                <option value="Ejecutada" {{ $planTrabajo->estado == 'Ejecutada' ? 'selected' : '' }}>✅ Ejecutada</option>
                                <option value="Cancelada" {{ $planTrabajo->estado == 'Cancelada' ? 'selected' : '' }}>❌ Cancelada</option>
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Subir Evidencia (PDF o Imagen)</label>
                            @if($planTrabajo->evidencia_pdf)
                                <div class="alert alert-success p-2 mb-2">
                                    <i class="fa fa-check-circle mr-1"></i> Ya hay un archivo cargado. Si subes otro, reemplazará al actual.
                                </div>
                            @endif
                            <input type="file" name="evidencia" class="form-control-file border p-2 rounded bg-light" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted">Si cambias el estado a "Ejecutada", te recomendamos adjuntar el registro de asistencia.</small>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Observaciones de la Gestión</label>
                            <textarea name="observaciones" class="form-control" rows="2">{{ $planTrabajo->observaciones }}</textarea>
                        </div>
                    </div>
                </div>

                <hr class="mt-4 mb-4">
                
                <div class="text-right">
                    <button type="submit" class="btn btn-lg shadow" style="background-color: #36b9cc; color: white; font-weight: bold;">
                        <i class="fa fa-refresh mr-2"></i> Actualizar Actividad
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection