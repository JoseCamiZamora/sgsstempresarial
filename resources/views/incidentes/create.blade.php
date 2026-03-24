@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 mb-0 text-gray-800" style="color: #2c3e50;">
                    <i class="fa fa-exclamation-triangle text-warning mr-2"></i> Reportar Incidente o Accidente
                </h2>
                <a href="{{ route('home') }}" class="btn btn-secondary shadow-sm">
                    <i class="fa fa-arrow-left mr-1"></i> Volver
                </a>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 pl-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card shadow border-0 mb-4">
                <div class="card-body p-4">
                    <p class="text-muted mb-4">Por favor, detalla lo sucedido. Esta información es vital para prevenir futuros eventos y proteger a todo el equipo.</p>
                    
                    <form action="{{ route('incidentes.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-muted small">Fecha del Evento</label>
                                <input type="date" name="fecha_incidente" class="form-control" max="{{ date('Y-m-d') }}" value="{{ old('fecha_incidente') }}" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-muted small">Hora Aproximada</label>
                                <input type="time" name="hora_incidente" class="form-control" value="{{ old('hora_incidente') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-muted small">Tipo de Evento</label>
                                <select name="tipo_evento" class="form-control" required>
                                    <option value="" disabled selected>Seleccione...</option>
                                    <option value="Incidente">Incidente (Casi accidente / No hubo lesión)</option>
                                    <option value="Accidente de Trabajo">Accidente de Trabajo (Hubo lesión)</option>
                                </select>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="font-weight-bold text-muted small">Lugar Exacto</label>
                                <input type="text" name="lugar_evento" class="form-control" placeholder="Ej: Pasillo principal, Bodega 2..." value="{{ old('lugar_evento') }}" required>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-muted small">Descripción Detallada de lo Sucedido</label>
                            <textarea name="descripcion" class="form-control" rows="4" placeholder="¿Qué estaba haciendo? ¿Cómo ocurrió? ¿Qué partes del cuerpo se vieron afectadas (si aplica)?" required>{{ old('descripcion') }}</textarea>
                        </div>

                        <hr>

                        <button type="submit" class="btn btn-warning btn-block btn-lg shadow-sm font-weight-bold text-dark">
                            <i class="fa fa-paper-plane mr-2"></i> Enviar Reporte a SST
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection