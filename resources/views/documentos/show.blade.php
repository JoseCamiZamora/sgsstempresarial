@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800" style="color: #2c3e50;">
            <i class="fa fa-file text-primary mr-2"></i> {{ $documento->titulo }}
        </h2>
        <a href="{{ route('documentos.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left mr-1"></i> Volver
        </a>
    </div>

    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="informacion-tab" data-toggle="tab" href="#informacion" role="tab">
                <i class="fa fa-info-circle mr-1"></i> Información del Documento
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="cambios-tab" data-toggle="tab" href="#cambios" role="tab">
                <i class="fa fa-history mr-1"></i> Control de Cambios
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="informacion" role="tabpanel">
            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <tr>
                                    <th width="150">Código</th>
                                    <td><span class="badge badge-primary">{{ $documento->codigo }}</span></td>
                                </tr>
                                <tr>
                                    <th>Título</th>
                                    <td>{{ $documento->titulo }}</td>
                                </tr>
                                <tr>
                                    <th>Categoría</th>
                                    <td><span class="badge badge-light border">{{ $documento->categoria }}</span></td>
                                </tr>
                                <tr>
                                    <th>Versión</th>
                                    <td><span class="badge badge-info">{{ $documento->version ?? '1.0' }}</span></td>
                                </tr>
                                <tr>
                                    <th>Descripción</th>
                                    <td>{{ $documento->descripcion ?? 'Sin descripción' }}</td>
                                </tr>
                                <tr>
                                    <th>Archivo Original</th>
                                    <td>{{ $documento->nombre_archivo ?? 'No disponible' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Inicio</th>
                                    <td>{{ $documento->fecha_vigencia_inicio ? \Carbon\Carbon::parse($documento->fecha_vigencia_inicio)->format('d/m/Y') : 'No definida' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Fin</th>
                                    <td>{{ $documento->fecha_vigencia_fin ? \Carbon\Carbon::parse($documento->fecha_vigencia_fin)->format('d/m/Y') : 'Vigente indefinidamente' }}</td>
                                </tr>
                                <tr>
                                    <th>Subido Por</th>
                                    <td>{{ $documento->autor->name ?? 'Usuario Eliminado' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha Registro</th>
                                    <td>{{ $documento->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <h5 class="m-0 font-weight-bold text-primary">Acciones</h5>
                        </div>
                        <div class="card-body text-center">
                            <a href="{{ asset('storage/' . $documento->archivo_ruta) }}" target="_blank" class="btn btn-success btn-lg btn-block mb-2">
                                <i class="fa fa-download mr-1"></i> Descargar
                            </a>
                            @hasanyrole('Super Admin|Administrador SGSST')
                            <a href="{{ route('documentos.edit', $documento->id) }}" class="btn btn-warning btn-lg btn-block mb-2">
                                <i class="fa fa-edit mr-1"></i> Editar
                            </a>
                            @endhasanyrole
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="cambios" role="tabpanel">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="m-0 font-weight-bold text-primary">
                        <i class="fa fa-history mr-1"></i> Historial de Cambios
                    </h5>
                    @hasanyrole('Super Admin|Administrador SGSST')
                    <a href="{{ route('documentos.edit', $documento->id) }}" class="btn btn-sm btn-primary">
                        <i class="fa fa-plus mr-1"></i> Registrar Cambio
                    </a>
                    @endhasanyrole
                </div>
                
                <div class="card-body p-0">
                    @if($documento->cambios->count() > 0)
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Versión</th>
                                <th>Tipo</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Observaciones</th>
                                <th>Registrado Por</th>
                                <th>Fecha Registro</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documento->cambios as $cambio)
                            <tr>
                                <td><span class="badge badge-info">{{ $cambio->version }}</span></td>
                                <td>
                                    @if($cambio->tipo_cambio === 'Nuevo')
                                        <span class="badge badge-success">Nuevo</span>
                                    @else
                                        <span class="badge badge-warning">Modificación</span>
                                    @endif
                                </td>
                                <td>{{ $cambio->fecha_vigencia_inicio ? \Carbon\Carbon::parse($cambio->fecha_vigencia_inicio)->format('d/m/Y') : '-' }}</td>
                                <td>{{ $cambio->fecha_vigencia_fin ? \Carbon\Carbon::parse($cambio->fecha_vigencia_fin)->format('d/m/Y') : 'Indefinido' }}</td>
                                <td>{{ $cambio->observaciones ?? '-' }}</td>
                                <td>{{ $cambio->registradoPor->name ?? 'Usuario Eliminado' }}</td>
                                <td>{{ $cambio->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="text-center py-5 text-muted">
                        <i class="fa fa-history fa-3x mb-3 d-block"></i>
                        No hay registros de cambios.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection