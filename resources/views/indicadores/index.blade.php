@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <a href="{{ route('home') }}" class="text-decoration-none text-secondary mb-2 d-inline-block font-weight-bold">
            <i class="fa fa-arrow-left mr-1"></i> Volver al Dashboard
        </a>
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="font-weight-bold text-dark"><i class="fa fa-line-chart text-primary mr-2"></i> Indicadores de Gestión SST</h2>
            <p class="text-muted">Análisis dinámico basado en la Resolución 0312.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-primary">Índice de Frecuencia de Accidentes</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartFrecuencia"></canvas>
                    <div class="mt-3 small text-center">
                        $$ IF = \frac{\text{N° Accidentes}}{\text{N° Trabajadores}} \times 100 $$
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 font-weight-bold text-danger">Índice de Severidad de Accidentes</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartSeveridad"></canvas>
                    <div class="mt-3 small text-center">
                        $$ IS = \frac{\text{Días de Incapacidad}}{\text{N° Trabajadores}} \times 100 $$
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = {!! json_encode($labels) !!};

    // Configuración Frecuencia
    new Chart(document.getElementById('chartFrecuencia'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Frecuencia %',
                data: {!! json_encode($dataFrecuencia) !!},
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                fill: true,
                tension: 0.3
            }]
        }
    });

    // Configuración Severidad
    new Chart(document.getElementById('chartSeveridad'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Días por cada 100 trab.',
                data: {!! json_encode($dataSeveridad) !!},
                backgroundColor: '#e74a3b',
                borderRadius: 5
            }]
        }
    });
</script>
@endsection
@endsection