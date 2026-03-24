@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-end mb-3">
            <a href="{{ route('home') }}" class="btn btn-secondary shadow-sm">
                <i class="fa fa-arrow-left mr-1"></i> Volver al Dashboard
            </a>
        </div>
    <div class="card shadow border-left-primary">
        
        <div class="card-header bg-white py-3">
            <h4 class="m-0 font-weight-bold text-primary">📝 Autoevaluación Estándares Mínimos (Res. 0312)</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('evaluacion.store') }}" method="POST">
                @csrf
                <table class="table table-bordered">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th>Estándar Mínimo</th>
                            <th width="120" class="text-center">Peso</th>
                            <th width="100" class="text-center">Cumple</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estandares as $item)
                        <tr>
                            <td>{{ $item->nombre }}</td>
                            <td class="text-center">{{ $item->porcentaje }}%</td>
                            <td class="text-center">
                                <input type="checkbox" name="items[]" value="{{ $item->porcentaje }}" 
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

                <button type="submit" class="btn btn-success btn-block btn-lg shadow mt-4">
                    <i class="fa fa-calculator mr-2"></i> Finalizar y Calcular Puntaje
                </button>
            </form>
        </div>
    </div>
</div>
@endsection