@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('evaluacion.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left mr-1"></i> Volver a la Bandeja
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <h4 class="m-0 font-weight-bold text-info">✏️ Editar Autoevaluación</h4>
        </div>
        
        <div class="card-body">
            <form action="{{ route('evaluacion.update', $evaluacion->id) }}" method="POST">
                @csrf @method('PUT')
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Estándar</th>
                            <th class="text-center">Cumple</th>
                        </tr>
                    </thead>
                    <tbody>
                       @foreach($estandares as $item)
                        <tr>
                            <td>{{ $item->nombre }} ({{ $item->porcentaje }}%)</td>
                            <td class="text-center">
                                <input type="checkbox" name="items[]" value="{{ $item->porcentaje }}" 
                                    {{-- Comprobamos si el valor existe en el array de respuestas --}}
                                    @if(is_array($evaluacion->respuestas) && in_array($item->porcentaje, $evaluacion->respuestas))
                                        checked 
                                    @endif
                                    style="transform: scale(1.5);">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="card bg-light mt-3">
                    <div class="card-body">
                        <h5>Fórmula de Calificación:</h5>
                        $$ \text{Resultado} = \sum (\text{Ítems marcados}) $$
                    </div>
                </div>
                <br>
                <button type="submit" class="btn btn-info btn-block btn-lg shadow mt-1">
                    <i class="fa fa-calculator mr-2"></i>  Actualizar y Recalcular Puntaje
                </button>
            </form>
        </div>
    </div>
</div>
@endsection