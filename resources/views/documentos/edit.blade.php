@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800" style="color: #2c3e50;">
            <i class="fa fa-edit text-warning mr-2"></i> Editar Documento: {{ $documento->titulo }}
        </h2>
        <a href="{{ route('documentos.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left mr-1"></i> Volver
        </a>
    </div>

    <div class="card shadow border-0 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('documentos.update', $documento->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" value="{{ $documento->codigo }}" readonly disabled>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Categoría <span class="text-danger">*</span></label>
                            <select name="categoria" class="form-control" required>
                                <option value="Políticas y Objetivos" {{ $documento->categoria == 'Políticas y Objetivos' ? 'selected' : '' }}>Políticas y Objetivos</option>
                                <option value="Manuales y Procedimientos" {{ $documento->categoria == 'Manuales y Procedimientos' ? 'selected' : '' }}>Manuales y Procedimientos</option>
                                <option value="Formatos y Registros" {{ $documento->categoria == 'Formatos y Registros' ? 'selected' : '' }}>Formatos y Registros</option>
                                <option value="Capacitaciones" {{ $documento->categoria == 'Capacitaciones' ? 'selected' : '' }}>Capacitaciones / Certificados</option>
                                <option value="Otros" {{ $documento->categoria == 'Otros' ? 'selected' : '' }}>Otros</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-bold text-muted small">Título del Documento <span class="text-danger">*</span></label>
                    <input type="text" name="titulo" class="form-control" value="{{ old('titulo', $documento->titulo) }}" placeholder="Ej: Política SST 2026" required>
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-bold text-muted small">Descripción</label>
                    <textarea name="descripcion" class="form-control" rows="2" placeholder="Breve resumen...">{{ old('descripcion', $documento->descripcion) }}</textarea>
                </div>

                <div class="form-row">
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Tipo de Acción <span class="text-danger">*</span></label>
                            <select name="tipo_accion" class="form-control" required>
                                <option value="Nuevo" {{ old('tipo_accion', 'Nuevo') == 'Nuevo' ? 'selected' : '' }}>Nuevo Documento</option>
                                <option value="Modificacion" {{ old('tipo_accion', 'Modificacion') == 'Modificacion' ? 'selected' : '' }}>Modificación</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Versión <span class="text-danger">*</span></label>
                            <input type="text" name="version" class="form-control" value="{{ old('version', $documento->version ?? '1.0') }}" placeholder="1.0" required>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Archivo Nuevo (Opcional)</label>
                            <input type="file" name="archivo" class="form-control-file" accept=".pdf,.doc,.docx,.xls,.xlsx">
                            <small class="text-muted">Actual: {{ $documento->nombre_archivo ?? $documento->archivo_ruta }}</small>
                        </div>
                    </div>
                </div>

                <div class="form-row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Fecha Inicio Vigencia <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_vigencia_inicio" class="form-control" value="{{ old('fecha_vigencia_inicio', $documento->fecha_vigencia_inicio) }}" required>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Fecha Fin Vigencia</label>
                            <input type="date" name="fecha_vigencia_fin" class="form-control" value="{{ old('fecha_vigencia_fin', $documento->fecha_vigencia_fin) }}">
                        </div>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label class="font-weight-bold text-muted small">Observaciones del Cambio</label>
                    <textarea name="observaciones" class="form-control" rows="2" placeholder="Describa los cambios realizados...">{{ old('observaciones') }}</textarea>
                </div>

                <div class="form-group mb-3 p-3" style="background-color: #f8f9fc; border-radius: 8px;">
                    <div style="display:flex;align-items:flex-start;gap:8px;">
                        <input type="checkbox" name="requiere_firma_empleados" id="requiere_firma_empleados" value="1" style="width:20px;height:20px;flex:0 0 auto;margin-top:2px;" {{ old('requiere_firma_empleados', $documento->requiere_firma_empleados) ? 'checked' : '' }}>
                        <label for="requiere_firma_empleados" class="font-weight-bold mb-0">Requiere firma de los empleados</label>
                    </div>
                    <small class="text-muted d-block mt-1">Si cambia la versión con esta opción activa, quienes ya habían firmado la versión anterior volverán a aparecer como pendientes.</small>
                </div>

                <hr>

                <button type="submit" class="btn btn-primary btn-block btn-lg shadow-sm font-weight-bold">
                    <i class="fa fa-save mr-2"></i> Guardar Cambios
                </button>
            </form>
        </div>
    </div>

    @if($cambios->count() > 0)
    <div class="card shadow border-0">
        <div class="card-header bg-white py-3">
            <h5 class="m-0 font-weight-bold text-primary">Historial de Cambios Recientes</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Versión</th>
                        <th>Tipo</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Registrado Por</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cambios->take(5) as $cambio)
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
                        <td>{{ $cambio->registradoPor->name ?? 'Usuario Eliminado' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection