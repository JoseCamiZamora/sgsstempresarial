@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0">
                <div class="card-header bg-warning d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 font-weight-bold text-dark"><i class="fa fa-edit mr-2"></i> Editar EPP</h5>
                    <a href="{{ route('epps.index') }}" class="btn btn-light btn-sm shadow-sm">
                        <i class="fa fa-arrow-left"></i> Volver
                    </a>
                </div>

                <div class="card-body">
                    {{-- Errores de validación --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('epps.update', $epp->id) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- Indispensable para actualizar --}}

                        <div class="form-group">
                            <label class="font-weight-bold">Nombre del Elemento</label>
                            <input type="text" name="nombre" class="form-control" 
                                   value="{{ old('nombre', $epp->nombre) }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label class="font-weight-bold">Categoría</label>
                            <select name="categoria" class="form-control" required>
                                @php
                                    $categorias = ['Cabeza', 'Ojos/Cara', 'Auditiva', 'Respiratoria', 'Manos', 'Pies', 'Cuerpo'];
                                @endphp
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat }}" {{ $epp->categoria == $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="font-weight-bold">Vida Útil (Meses)</label>
                            <input type="number" name="vida_util_meses" class="form-control" 
                                   value="{{ old('vida_util_meses', $epp->vida_util_meses) }}" required>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-warning px-4 shadow font-weight-bold">
                                <i class="fa fa-sync-alt mr-1"></i> Actualizar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection