@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            <i class="fa fa-calendar-plus-o text-info mr-2"></i> Programar Nueva Actividad
        </h1>
        <a href="{{ route('plan-trabajo.index') }}" class="btn btn-outline-secondary shadow-sm font-weight-bold">
            <i class="fa fa-arrow-left mr-1"></i> Volver al Cronograma
        </a>
    </div>

    <div class="card shadow mb-4 border-bottom-info">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-info">Detalles de la Capacitación / Actividad</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('plan-trabajo.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Nombre de la Actividad <span class="text-danger">*</span></label>
                            <input type="text" name="actividad" class="form-control" placeholder="Ej: Capacitación en Uso de Extintores, Simulacro de Evacuación..." required>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Fecha Programada <span class="text-danger">*</span></label>
                            <input type="date" name="fecha_programada" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Responsable de Ejecución <span class="text-danger">*</span></label>
                            <select name="responsable_id" class="form-control" required>
                                <option value="" disabled selected>-- Seleccione al encargado --</option>
                                @foreach($usuarios as $user)
                                    <option value="{{ $user->id }}">{{ $user->nombres }} ({{ $user->roles->first()->name ?? 'Empleado' }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark">Observaciones (Opcional)</label>
                            <textarea name="observaciones" class="form-control" rows="3" placeholder="Lugar, presupuesto estimado, requerimientos..."></textarea>
                        </div>
                        
                        <input type="hidden" name="estado" value="Pendiente">
                    </div>
                </div>

                <hr class="mt-4 mb-4">
                
                <div class="text-right">
                    <button type="submit" class="btn btn-lg shadow" style="background-color: #36b9cc; color: white; font-weight: bold;">
                        <i class="fa fa-save mr-2"></i> Guardar en el Cronograma
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection