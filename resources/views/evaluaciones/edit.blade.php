@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">{{ $empresa->razon_social ?? 'Empresa no encontrada' }}</h3>
            <p class="text-muted">NIT: {{ $empresa->nit }} | Riesgo: {{ $empresa->nivel_riesgo }}</p>
        </div>
        <a href="{{ route('evaluacion.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left mr-1"></i> Volver a la Bandeja
        </a>
    </div>

    <div class="card shadow border-left-info">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold text-info">
                ✏️ Editar Autoevaluación: {{ $tipoPlantilla }} Estándares
            </h4>
            <span class="badge badge-info p-2">Editando Registro #{{ $evaluacion->id }}</span>
        </div>

        <div class="card-body">
            <div class="alert alert-light border shadow-sm">
                <i class="fa fa-info-circle mr-2 text-info"></i> 
                Esta evaluación se rige bajo la plantilla de <strong>{{ $tipoPlantilla }} ítems</strong> seleccionada automáticamente por el sistema.
            </div>

            <form action="{{ route('evaluacion.update', $evaluacion->id) }}" method="POST" id="formEditEvaluacion">
                @csrf
                @method('PUT')
                
                <input type="hidden" name="empresa_id" value="{{ $empresa->id }}">
                <input type="hidden" name="tipo_plantilla" value="{{ $tipoPlantilla }}">

                <table class="table table-hover table-bordered">
                    <thead class="bg-info text-white">
                        <tr>
                            <th>Estándar Mínimo</th>
                            <th width="120" class="text-center">Peso (%)</th>
                            <th width="100" class="text-center">Cumple</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($estandares as $item)
                        <tr>
                            <td>
                                <span class="font-weight-bold text-dark">{{ $item->codigo }}</span><br>
                                <small class="text-muted">{{ $item->nombre }}</small>
                            </td>
                            <td class="text-center align-middle font-weight-bold">
                                {{ $item->porcentaje }}%
                            </td>
                            <td class="text-center align-middle">
                                <input type="checkbox" 
                                       name="items[{{ $item->id }}]" 
                                       value="{{ $item->porcentaje }}" 
                                       class="check-item" 
                                       style="transform: scale(1.8); cursor: pointer;"
                                       {{ in_array($item->id, $respuestasIds) ? 'checked' : '' }}>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="row mt-4">
                    <div class="col-md-7">
                        <div class="card bg-light border-0 shadow-sm">
                            <div class="card bg-light border-0 shadow-sm">
                                <div class="card-body">
                                    <h5 class="font-weight-bold"><i class="fa fa-calculator mr-2"></i>Fórmula de Calificación:</h5>
                                    <p class="mb-0 text-center py-2" style="font-size: 1.1rem;">
                                        $$ \text{Puntaje Final} = \left( \frac{\sum \text{Pesos de ítems marcados}}{\sum \text{Pesos totales de la plantilla}} \right) \times 100 $$
                                    </p>
                                    <small class="text-muted d-block text-center mt-2">
                                        * El puntaje se calcula sobre el 100% de los estándares que le aplican a su empresa según la Res. 0312.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="card bg-dark text-white shadow">
                            <div class="card-body text-center">
                                <h6 class="text-uppercase small text-info">Puntaje Calculado</h6>
                                <h2 class="display-4 font-weight-bold" id="totalDisplay">0.0%</h2>
                                <p id="estadoTexto" class="mb-0 font-weight-bold">Calculando...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-info btn-block btn-lg shadow mt-4">
                    <i class="fa fa-sync-alt mr-2"></i> Actualizar Evaluación y Puntaje
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checks = document.querySelectorAll('.check-item');
        const display = document.getElementById('totalDisplay');
        const estadoTexto = document.getElementById('estadoTexto');

        function calcular() {
            let sumaMarcados = 0;
            let sumaTotalPosible = 0;

            // 1. Sumamos lo que vale CADA checkbox que aparece en la pantalla
            checks.forEach(check => {
                const valor = parseFloat(check.value) || 0;
                sumaTotalPosible += valor; // Esto sumará 36.0 en tu caso
                
                if (check.checked) {
                    sumaMarcados += valor;
                }
            });

            // 2. Aplicamos la regla de tres: (Marcados / Total Posible) * 100
            // Si sumaTotalPosible es 0, evitamos división por cero
            let resultadoFinal = (sumaTotalPosible > 0) ? (sumaMarcados / sumaTotalPosible) * 100 : 0;

            // 3. Mostramos el resultado
            display.innerText = resultadoFinal.toFixed(1) + '%';

            // 4. Semáforo de estados
            if (resultadoFinal < 60) {
                estadoTexto.innerText = "ESTADO: CRÍTICO";
                estadoTexto.className = "mb-0 font-weight-bold text-danger";
            } else if (resultadoFinal >= 60 && resultadoFinal <= 85) {
                estadoTexto.innerText = "ESTADO: MODERADAMENTE ACEPTABLE";
                estadoTexto.className = "mb-0 font-weight-bold text-warning";
            } else {
                estadoTexto.innerText = "ESTADO: ACEPTABLE";
                estadoTexto.className = "mb-0 font-weight-bold text-success";
            }
        }

        // Escuchar cambios
        checks.forEach(check => {
            check.addEventListener('change', calcular);
        });

        // EJECUCIÓN INICIAL (Crucial para Editar para que no empiece en 0%)
        calcular();
    });
</script>
@endsection