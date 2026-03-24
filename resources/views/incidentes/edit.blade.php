@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="h3 mb-0 text-gray-800" style="color: #2c3e50;">
                <i class="fa fa-folder-open text-primary mr-2"></i> Expediente de Reporte #{{ $incidente->id }}
            </h2>
            <p class="text-muted small mt-1">Detalles del evento y gestión de Seguridad y Salud en el Trabajo.</p>
        </div>
        <a href="{{ route('incidentes.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left mr-1"></i> Volver a la Bandeja
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

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Información del Evento Reportado</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted small font-weight-bold text-uppercase">Reportado por:</div>
                        <div class="col-sm-8 font-weight-bold">{{ $incidente->reportante->nombres ?? 'Usuario Eliminado' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted small font-weight-bold text-uppercase">Fecha y Hora:</div>
                        <div class="col-sm-8">{{ \Carbon\Carbon::parse($incidente->fecha_incidente)->format('d/m/Y') }} a las {{ \Carbon\Carbon::parse($incidente->hora_incidente)->format('h:i A') }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted small font-weight-bold text-uppercase">Tipo de Evento:</div>
                        <div class="col-sm-8">
                            @if($incidente->tipo_evento == 'Incidente')
                                <span class="badge badge-warning px-2 py-1"><i class="fa fa-exclamation-circle"></i> Incidente</span>
                            @else
                                <span class="badge badge-danger px-2 py-1"><i class="fa fa-medkit"></i> Accidente de Trabajo</span>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted small font-weight-bold text-uppercase">Lugar Exacto:</div>
                        <div class="col-sm-8">{{ $incidente->lugar_evento }}</div>
                    </div>
                    <hr>
                    <div class="mb-2 text-muted small font-weight-bold text-uppercase">Descripción de los hechos:</div>
                    <p class="text-justify bg-light p-3 rounded border">{{ $incidente->descripcion }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="card shadow-sm border-left-info h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-info">Gestión del Área SST</h6>
                </div>
                <div class="card-body">
                    
                    @hasanyrole('Super Admin|Administrador SGSST')
                        <form action="{{ route('incidentes.update', $incidente->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-muted small">Estado Actual del Caso</label>
                                <select name="estado" class="form-control form-control-lg font-weight-bold" required>
                                    <option value="Pendiente" class="text-danger" {{ $incidente->estado == 'Pendiente' ? 'selected' : '' }}>🔴 Pendiente de Revisión</option>
                                    <option value="En Investigación" class="text-info" {{ $incidente->estado == 'En Investigación' ? 'selected' : '' }}>🔵 En Investigación</option>
                                    <option value="Cerrado" class="text-success" {{ $incidente->estado == 'Cerrado' ? 'selected' : '' }}>🟢 Caso Cerrado</option>
                                </select>
                            </div>

                            <div class="form-group mb-4">
                                <label class="font-weight-bold text-muted small">Observaciones / Medidas Tomadas (Visible para el empleado)</label>
                                <textarea name="observaciones_sst" class="form-control" rows="5" placeholder="Indique si se realizó investigación, primeros auxilios, remisión a ARL o medidas correctivas...">{{ old('observaciones_sst', $incidente->observaciones_sst) }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-info btn-lg btn-block shadow-sm font-weight-bold">
                                <i class="fa fa-save mr-1"></i> Guardar Gestión
                            </button>
                        </form>
                    @else
                        <div class="text-center mb-4">
                            <span class="d-block text-muted small font-weight-bold text-uppercase mb-2">Estado de tu caso:</span>
                            @php
                                $badgeClass = 'badge-secondary';
                                if($incidente->estado == 'Pendiente') $badgeClass = 'badge-danger';
                                if($incidente->estado == 'En Investigación') $badgeClass = 'badge-info';
                                if($incidente->estado == 'Cerrado') $badgeClass = 'badge-success';
                            @endphp
                            <span class="badge {{ $badgeClass }} px-4 py-2" style="font-size: 1.1rem;">
                                {{ strtoupper($incidente->estado) }}
                            </span>
                        </div>

                        <hr>

                        <div class="mb-2 text-muted small font-weight-bold text-uppercase">Respuesta del área SST:</div>
                        @if($incidente->observaciones_sst)
                            <p class="text-justify bg-light p-3 rounded border text-info">{{ $incidente->observaciones_sst }}</p>
                        @else
                            <div class="alert alert-light border text-center text-muted py-4">
                                <i class="fa fa-clock-o fa-2x mb-2 d-block text-gray-300"></i>
                                El área de Seguridad y Salud en el Trabajo aún no ha emitido observaciones sobre este caso.
                            </div>
                        @endif
                    @endhasanyrole

                </div>
            </div>
        </div>
    </div>
</div>
@endsection