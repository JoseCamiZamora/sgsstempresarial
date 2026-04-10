@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-primary">Añadir al Catálogo</h5>
                    <a href="{{ route('epps.index') }}" class="btn btn-outline-secondary btn-sm border-0">
                        <i class="fa fa-arrow-left mr-1"></i> Volver al listado
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('epps.store') }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label class="font-weight-bold">Nombre del Elemento</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej: Casco dieléctrico blanco" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Categoría</label>
                            <select name="categoria" class="form-control" required>
                                <option value="Cabeza">Cabeza (Cascos, gorras)</option>
                                <option value="Ojos/Cara">Ojos/Cara (Gafas, caretas)</option>
                                <option value="Auditiva">Auditiva (Tapones, orejeras)</option>
                                <option value="Respiratoria">Respiratoria (Mascarillas, filtros)</option>
                                <option value="Manos">Manos (Guantes)</option>
                                <option value="Pies">Pies (Botas, zapatos)</option>
                                <option value="Cuerpo">Cuerpo (Arnés, delantales)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Vida Útil (Meses)</label>
                            <input type="number" name="vida_util_meses" class="form-control" value="6" required>
                            <small class="text-muted">Periodicidad sugerida para la reposición.</small>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary px-4 shadow">
                                <i class="fa fa-save mr-1"></i> Guardar en Catálogo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection