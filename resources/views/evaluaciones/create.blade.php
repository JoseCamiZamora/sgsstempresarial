@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-0">{{ $empresa->razon_social }}</h3>
            <p class="text-muted">NIT: {{ $empresa->nit }} | Riesgo: {{ $empresa->nivel_riesgo }}</p>
        </div>
        <a href="{{ route('home') }}" class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left mr-1"></i> Volver
        </a>
    </div>

    <div class="card shadow border-left-primary">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h4 class="m-0 font-weight-bold text-primary">
                📝 Autoevaluación: {{ $tipoPlantilla }} Estándares
            </h4>
            <span class="badge badge-{{ $tipoPlantilla == 60 ? 'danger' : ($tipoPlantilla == 21 ? 'warning' : 'success') }} p-2">
                Resolución 0312
            </span>
        </div>

        <div class="card-body">
            <div class="alert alert-info border-0 shadow-sm">
                <i class="fa fa-info-circle mr-2"></i> 
                Basado en que la empresa tiene <strong>{{ $empresa->num_trabajadores }} empleados</strong>, el sistema ha activado la plantilla de <strong>{{ $tipoPlantilla }} ítems</strong>.
            </div>

            <form action="{{ route('evaluacion.store') }}" method="POST" id="formEvaluacion">
                @csrf
                <input type="hidden" name="empresa_id" value="{{ $empresa->id }}">
                <input type="hidden" name="tipo_plantilla" value="{{ $tipoPlantilla }}">

                <table class="table table-hover table-bordered">
                    <thead class="bg-primary text-white">
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
                                <input type="checkbox" name="items[{{ $item->id }}]" value="{{ $item->porcentaje }}" 
                                       class="check-item" style="transform: scale(1.5); cursor: pointer;">
                                       
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <div class="row mt-4">
                    <div class="col-md-7">
                        <div class="card bg-light">
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
                                <h6 class="text-uppercase small">Puntaje Obtenido</h6>
                                <h2 class="display-4 font-weight-bold" id="totalDisplay">0.0%</h2>
                                <p id="estadoTexto" class="mb-0 text-warning">Esperando selección...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-block btn-lg shadow mt-4">
                    <i class="fa fa-save mr-2"></i> Finalizar y Guardar en Historial
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log("Script de evaluación iniciado");

        // 1. Definimos las variables capturando los elementos del DOM
        const display = document.getElementById('totalDisplay');
        const estadoTexto = document.getElementById('estadoTexto');
        // Capturamos todos los checks cada vez que se necesite o al inicio
        const obtenerChecks = () => document.querySelectorAll('.check-item');

        function calcularTodo() {
            let sumaMarcados = 0;
            let sumaTotalPosible = 0;
            const checks = obtenerChecks();

            // 2. Sumamos lo que vale CADA checkbox que aparece en la pantalla
            checks.forEach(check => {
                const valor = parseFloat(check.value) || 0;
                sumaTotalPosible += valor; 
                
                if (check.checked) {
                    sumaMarcados += valor;
                }
            });

            // 3. Aplicamos la regla de tres proporcional
            let resultadoFinal = (sumaTotalPosible > 0) ? (sumaMarcados / sumaTotalPosible) * 100 : 0;

            // 4. Mostramos el resultado en el ID totalDisplay
            if (display) {
                display.innerText = resultadoFinal.toFixed(1) + '%';
            }

            // 5. Semáforo de estados
            if (estadoTexto) {
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
        }

        // 6. Escuchamos los cambios en el documento (Delegación de eventos)
        document.addEventListener('change', function(e) {
            if (e.target && e.target.classList.contains('check-item')) {
                calcularTodo();
            }
        });

        // 7. Ejecución inicial para asegurar que empiece en 0.0% correctamente
        calcularTodo();
    });
</script>
@endsection