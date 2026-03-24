@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800" style="color: #2c3e50;">Registrar Nuevo Riesgo</h2>
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
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('matriz-riesgos.store') }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card shadow-sm border-left-primary h-100">
                    <div class="card-header bg-white font-weight-bold text-primary">
                        <i class="fa fa-map-marker mr-1"></i> 1. Ubicación y Actividad
                    </div>
                    <div class="card-body row">
                        <div class="form-group col-md-3 mb-3">
                            <label class="small font-weight-bold text-muted">Proceso</label>
                            <input type="text" name="proceso" class="form-control" placeholder="Ej: Producción, Administrativo" value="{{ old('proceso') }}" required>
                        </div>
                        <div class="form-group col-md-3 mb-3">
                            <label class="small font-weight-bold text-muted">Zona / Lugar</label>
                            <input type="text" name="zona_lugar" class="form-control" placeholder="Ej: Bodega 1, Oficina 205" value="{{ old('zona_lugar') }}" required>
                        </div>
                        <div class="form-group col-md-4 mb-3">
                            <label class="small font-weight-bold text-muted">Actividad Específica</label>
                            <input type="text" name="actividad" class="form-control" placeholder="Ej: Manejo de montacargas, Digitación" value="{{ old('actividad') }}" required>
                        </div>
                        <div class="form-group col-md-2 mb-3">
                            <label class="small font-weight-bold text-muted">¿Es Rutinaria?</label>
                            <select name="es_rutinaria" class="form-control" required>
                                <option value="1" selected>Sí</option>
                                <option value="0">No</option>
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
                            <label class="small font-weight-bold text-muted">Clasificación (GTC 45)</label>
                            <select name="clasificacion_peligro" class="form-control" required>
                                <option value="" disabled selected>Seleccione una clasificación...</option>
                                <option value="Biológico">Biológico (Virus, Bacterias, Hongos)</option>
                                <option value="Físico">Físico (Ruido, Iluminación, Vibración)</option>
                                <option value="Químico">Químico (Polvos, Gases, Vapores)</option>
                                <option value="Psicosocial">Psicosocial (Gestión, Jornada, Estrés)</option>
                                <option value="Biomecánico">Biomecánico (Posturas, Esfuerzos, Mov. Repetitivos)</option>
                                <option value="Condiciones de Seguridad">Condiciones de Seguridad (Mecánico, Eléctrico, Locativo)</option>
                                <option value="Fenómenos Naturales">Fenómenos Naturales (Sismo, Inundación)</option>
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label class="small font-weight-bold text-muted">Descripción del Peligro</label>
                            <textarea name="descripcion_peligro" class="form-control" rows="2" placeholder="Describa cómo se presenta el peligro en la actividad..." required>{{ old('descripcion_peligro') }}</textarea>
                        </div>
                        <div class="form-group mb-0">
                            <label class="small font-weight-bold text-muted">Efectos Posibles en la Salud</label>
                            <textarea name="efectos_posibles" class="form-control" rows="2" placeholder="Ej: Dolor lumbar, Estrés crónico, Hipoacusia..." required>{{ old('efectos_posibles') }}</textarea>
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
                            <label class="small font-weight-bold text-muted">Nivel de Riesgo Inicial</label>
                            <select name="nivel_riesgo" class="form-control form-control-lg text-center font-weight-bold" required>
                                <option value="" disabled selected>Seleccione el nivel...</option>
                                <option value="Bajo" class="text-success">BAJO</option>
                                <option value="Medio" class="text-warning">MEDIO</option>
                                <option value="Alto" class="text-danger">ALTO</option>
                                <option value="Extremo" class="text-dark">EXTREMO</option>
                            </select>
                            <small class="form-text text-muted text-center mt-2">Esta valoración determina la prioridad de intervención.</small>
                        </div>
                        
                        <button type="submit" class="btn btn-success btn-lg btn-block shadow-sm mt-auto" style="font-weight: bold; font-size: 1.1rem;">
                            <i class="fa fa-save mr-1"></i> Guardar en la Matriz
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection