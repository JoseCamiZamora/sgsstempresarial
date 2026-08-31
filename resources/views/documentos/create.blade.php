@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3 mb-0 text-gray-800" style="color: #2c3e50;">
                    <i class="fa fa-upload text-primary mr-2"></i> Subir Nuevo Documento
                </h2>
                <a href="{{ route('documentos.index') }}" class="btn btn-secondary shadow-sm">
                    <i class="fa fa-arrow-left mr-1"></i> Volver
                </a>
            </div>

            <div class="card shadow border-0 mb-4">
                <div class="card-body p-4">
                    <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Título del Documento</label>
                            <input type="text" name="titulo" class="form-control" placeholder="Ej: Política de Seguridad y Salud en el Trabajo 2026" value="{{ old('titulo') }}" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Categoría</label>
                            <select name="categoria" class="form-control" required>
                                <option value="" disabled selected>Seleccione dónde se organizará...</option>
                                <option value="Políticas y Objetivos">Políticas y Objetivos</option>
                                <option value="Manuales y Procedimientos">Manuales y Procedimientos</option>
                                <option value="Formatos y Registros">Formatos y Registros</option>
                                <option value="Capacitaciones">Capacitaciones / Certificados</option>
                                <option value="Otros">Otros</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Prefijo del Documento</label>
                            <select name="prefijo" class="form-control" required>
                                <option value="" disabled selected>Seleccione el tipo documental...</option>
                                @foreach(\App\Models\Documento::PREFIJOS as $valor => $etiqueta)
                                    <option value="{{ $valor }}" {{ old('prefijo') === $valor ? 'selected' : '' }}>{{ $etiqueta }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">Define el código del documento junto con el consecutivo, ej: SST-FT-001.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Tipo de Acción</label>
                            <select name="tipo_accion" class="form-control" required>
                                <option value="Nuevo" selected>Nuevo Documento</option>
                                <option value="Modificacion">Modificación a Documento Existente</option>
                            </select>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-muted small">Versión</label>
                            <input type="text" name="version" class="form-control" value="{{ old('version', '1.0') }}" placeholder="1.0" required>
                        </div>

                        <div class="form-group mb-4 p-3" style="background-color: #f8f9fc; border-radius: 8px;">
                            <div style="display:flex;align-items:flex-start;gap:8px;">
                                <input type="checkbox" name="requiere_firma_empleados" id="requiere_firma_empleados" value="1" style="width:20px;height:20px;flex:0 0 auto;margin-top:2px;" {{ old('requiere_firma_empleados') ? 'checked' : '' }}>
                                <label for="requiere_firma_empleados" class="font-weight-bold mb-0">Requiere firma de los empleados</label>
                            </div>
                            <small class="text-muted d-block mt-1">Si se activa, este documento aparecerá como pendiente de firma en el Portal de Firmas para todos los empleados. Si más adelante sube una nueva versión, quienes ya firmaron deberán volver a firmar.</small>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-muted small">Fecha Inicio Vigencia (Opcional)</label>
                                    <input type="date" name="fecha_vigencia_inicio" class="form-control" value="{{ old('fecha_vigencia_inicio') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold text-muted small">Fecha Fin Vigencia (Opcional)</label>
                                    <input type="date" name="fecha_vigencia_fin" class="form-control" value="{{ old('fecha_vigencia_fin') }}">
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-4 p-4 text-center" style="border: 2px dashed #bac8d3; background-color: #f8f9fc; border-radius: 10px;">
                            <i class="fa fa-file-pdf-o fa-3x text-danger mb-3"></i>
                            <i class="fa fa-file-word-o fa-3x text-primary mb-3 ml-2"></i>
                            <i class="fa fa-file-excel-o fa-3x text-success mb-3 ml-2"></i>
                            <br>
                            <label class="font-weight-bold text-muted mb-2">Selecciona el archivo (PDF, DOCX, XLSX)</label>
                            <input type="file" name="archivo" class="form-control-file d-block mx-auto" accept=".pdf,.doc,.docx,.xls,.xlsx" required>
                            <small class="text-muted mt-2 d-block">Tamaño máximo permitido: 5 MB.</small>
                        </div>

                        <hr>

                        <button type="submit" class="btn btn-primary btn-block btn-lg shadow-sm font-weight-bold">
                            <i class="fa fa-cloud-upload mr-2"></i> Subir y Guardar Documento
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection