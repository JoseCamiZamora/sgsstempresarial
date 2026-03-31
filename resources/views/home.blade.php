@extends('layouts.app') {{-- O el nombre de tu layout principal --}}

@section('content')
<div class="container-fluid" style="background-color: #f4f6f9; min-height: 100vh;">
    <div class="row">
        
        <nav class="col-md-2 d-none d-md-block bg-white sidebar shadow-sm" style="min-height: 100vh;">
            <div class="sidebar-sticky pt-4">
                <div class="text-center mb-4">
                    <img src="{{ asset('assets/img/synergy.png') }}" alt="Logo Sinergia SST" class="img-fluid mb-2" style="max-width: 80px; height: auto; filter: drop-shadow(0px 4px 6px rgba(0,0,0,0.1));">
                    <h5 class="mt-2 font-weight-bold" style="color: #2c3e50; letter-spacing: 0.5px;">Sinergia SST</h5>
                </div>
                
                <ul class="nav flex-column mt-2">
                    <li class="nav-item mb-2">
                        <a class="nav-link text-primary font-weight-bold" href="{{ route('home') }}">
                            <i class="fa fa-home mr-2"></i> Inicio / Dashboard
                        </a>
                    </li>

                    {{-- SECCIÓN: GESTIÓN OPERATIVA --}}
                    @hasanyrole('Super Admin|Administrador SGSST')
                    <hr class="my-2">
                    <div class="sidebar-heading text-muted small font-weight-bold px-3 mb-2 text-uppercase">
                        Gestión Operativa
                    </div>

                    <li class="nav-item mb-1">
                        <a class="nav-link text-secondary py-1" href="{{ route('matriz-riesgos.index') }}">
                            <i class="fa fa-table mr-2 text-warning"></i> Matriz de Riesgos
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link text-secondary py-1" href="{{ route('plan-trabajo.index') }}">
                            <i class="fa fa-calendar mr-2 text-info"></i> Plan de Trabajo
                        </a>
                    </li>
                    @endhasanyrole

                    {{-- SECCIÓN: AUDITORÍA Y LEY (RES. 0312) --}}
                    @hasanyrole('Super Admin|Administrador SGSST')
                    <hr class="my-2">
                    <div class="sidebar-heading text-muted small font-weight-bold px-3 mb-2 text-uppercase">
                        Auditoría Legal
                    </div>

                    <li class="nav-item mb-1">
                        <a class="nav-link text-secondary py-1" href="{{ route('evaluacion.index') }}">
                            <i class="fa fa-history mr-2"></i> Historial 0312
                        </a>
                    </li>
                    @if($empresaPerfil)
                        {{-- CASO A: La empresa ya existe, mostramos el link normal --}}
                        <li class="nav-item mb-1">
                            <a class="nav-link text-secondary py-1" href="{{ route('evaluacion.crear', ['empresaId' => $empresaPerfil->id]) }}">
                                <i class="fa fa-check mr-2 text-success"></i> Nueva Autoevaluación
                            </a>
                        </li>
                    @else
                        {{-- CASO B: No hay empresa, mostramos el link con alerta --}}
                        <li class="nav-item mb-1">
                            <a class="nav-link text-secondary py-1" href="javascript:void(0);" onclick="alertaSinEmpresa()">
                                <i class="fa fa-lock mr-2 text-muted"></i> Nueva Autoevaluación
                            </a>
                        </li>
                    @endif
                    @endhasanyrole

                    {{-- SECCIÓN: REPORTES Y SOPORTE --}}
                    <hr class="my-2">
                    <div class="sidebar-heading text-muted small font-weight-bold px-3 mb-2 text-uppercase">
                        Reportes
                    </div>

                    <li class="nav-item mb-1">
                        <a class="nav-link text-secondary py-1" href="{{ route('incidentes.index') }}">
                            <i class="fa fa-exclamation-triangle mr-2 text-danger"></i> Reportar Incidente
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-secondary" href="{{ route('indicadores.index') }}">
                            <i class="fa fa-list mr-2 text-primary"></i> Indicadores de Gestión
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link text-secondary py-1" href="{{ route('documentos.index') }}">
                            <i class="fa fa-folder-open mr-2 text-secondary"></i> Documentos
                        </a>
                    </li>

                    {{-- SECCIÓN: CONFIGURACIÓN DEL SISTEMA --}}
                    @hasanyrole('Super Admin|Administrador SGSST')
                    <hr class="my-2">
                    <div class="sidebar-heading text-muted small font-weight-bold px-3 mb-2 text-uppercase">
                        Configuración
                    </div>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('perfil.index') }}">
                            <i class="fa fa-cogs mr-2"></i> Perfil de Empresa
                        </a>
                    </li>

                    <li class="nav-item mb-1">
                        <a class="nav-link text-secondary py-1" href="{{ route('item-estandar.index') }}">
                            <i class="fa fa-cog mr-2 text-dark"></i> Configurar Ítems 0312
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link text-secondary py-1" href="{{ route('estadisticas.index') }}">
                            <i class="fa fa-database mr-2 text-dark"></i> Datos Mensuales
                        </a>
                    </li>
                    <li class="nav-item mb-1">
                        <a class="nav-link text-secondary py-1" href="{{ route('usuarios.index') }}">
                            <i class="fa fa-users mr-2 text-dark"></i> Gestión de Usuarios
                        </a>
                    </li>
                    @endhasanyrole
                </ul>
            </div>
        </nav>

        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4 pt-4">
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-4 border-bottom">
                <h1 class="h2" style="color: #2c3e50;">Bienvenido, {{ Auth::user()->name ?? 'Usuario' }}</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="badge badge-primary px-3 py-2" style="font-size: 0.9rem;">
                        Rol: {{ Auth::user()->roles->pluck('name')->first() ?? 'Sin Rol' }}
                    </span>
                </div>
            </div>

            @hasrole('Super Admin|Administrador SGSST|Gerencia')
            <div class="row">
                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm border-0 border-left-primary h-100 py-2" style="border-left: 4px solid #4A90E2;">
                        <div class="card-body">
                          <div class="row no-gutters align-items-center">
                              <div class="col mr-2">
                                  <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                      Incidentes Pendientes
                                  </div>
                                  <div class="h5 mb-0 font-weight-bold {{ $incidentesPendientes > 0 ? 'text-danger' : 'text-success' }}">
                                      {{ $incidentesPendientes }} Por revisar
                                  </div>
                              </div>
                              <div class="col-auto">
                                  <i class="fa fa-ambulance fa-2x text-gray-300"></i>
                              </div>
                          </div>
                      </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                  <div class="card border-left-danger shadow h-100 py-2">
                      <div class="card-body">
                          <div class="row no-gutters align-items-center">
                              <div class="col mr-2">
                                  <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                      Riesgos Críticos
                                  </div>
                                  <div class="h5 mb-0 font-weight-bold text-gray-800">
                                      {{ $riesgosCriticos }} Identificados
                                  </div>
                              </div>
                              <div class="col-auto">
                                  <i class="fa fa-fire fa-2x text-gray-300"></i>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
              <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-success shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                    Cumplimiento / Salud SST
                                </div>
                                <div class="row no-gutters align-items-center">
                                    <div class="col-auto">
                                        <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $cumplimiento }}%</div>
                                    </div>
                                    <div class="col">
                                        <div class="progress progress-sm mr-2">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $cumplimiento }}%" aria-valuenow="{{ $cumplimiento }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fa fa-check-square pt-2 fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

                <div class="col-md-3 mb-4">
                    <div class="card shadow-sm border-0 h-100 py-2" style="border-left: 4px solid #f6c23e;">
                        <div class="card-body">
                          <div class="row no-gutters align-items-center">
                              <div class="col mr-2">
                                  <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                      Usuarios Activos
                                  </div>
                                  <div class="h5 mb-0 font-weight-bold" style="color: #2c3e50;">
                                      {{ $totalUsuarios }} Registrados
                                  </div>
                              </div>
                              <div class="col-auto">
                                  <i class="fa fa-users fa-2x text-gray-300"></i>
                              </div>
                          </div>
                      </div>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow border-left-info">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Puntaje Autoevaluación Estándares Mínimos (Resolución 0312)
                                    </div>
                                    
                                    <div class="row no-gutters align-items-center">
                                        <div class="col-auto">
                                            <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">
                                                {{ number_format($puntaje0312, 1) }}%
                                            </div>
                                        </div>
                                        <div class="col">
                                            <div class="progress progress-sm mr-2" style="height: 15px; background-color: #eaecf4; border-radius: 10px;">
                                                @php
                                                    // Lógica de colores basada en la norma
                                                    $colorBarra = '#e74a3b'; // Rojo (Crítico)
                                                    if($puntaje0312 >= 60 && $puntaje0312 <= 85) $colorBarra = '#f6c23e'; // Amarillo (Moderado)
                                                    if($puntaje0312 > 85) $colorBarra = '#1cc88a'; // Verde (Aceptable)
                                                @endphp
                                                <div class="progress-bar shadow-sm progress-bar-striped progress-bar-animated" 
                                                    role="progressbar" 
                                                    style="width: {{ $puntaje0312 }}%; background-color: {{ $colorBarra }}; border-radius: 10px;height: 22px;" 
                                                    aria-valuenow="{{ $puntaje0312 }}" aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3 d-flex align-items-center">
                                        <span class="badge badge-pill px-3 py-2" style="background-color: {{ $colorBarra }}; color: white; font-size: 0.75rem;">
                                            <i class="fa fa-shield mr-1"></i> ESTADO: {{ strtoupper($estado0312) }}
                                        </span>
                                        
                                        <small class="text-muted ml-3">
                                            <i class="fa fa-calendar-check-o mr-1"></i> 
                                            Última evaluación: <strong>{{ $fecha0312 }}</strong>
                                        </small>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <i class="fa {{ $puntaje0312 > 85 ? 'fa-check-circle' : 'fa-exclamation-triangle' }} fa-3x text-gray-300"></i>
                                </div>
                            </div>
                            
                            <hr>
                            <div class="row mt-3 text-center text-xs font-weight-bold text-gray-600">
                                <div class="col-4 border-right">
                                    <span class="text-danger">●</span> CRÍTICO <br> (< 60%)
                                </div>
                                <div class="col-4 border-right">
                                    <span class="text-warning">●</span> MODERADO <br> (60% - 85%)
                                </div>
                                <div class="col-4">
                                    <span class="text-success">●</span> ACEPTABLE <br> (> 85%)
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-xl-4 col-lg-5">
                    <div class="card shadow mb-4 h-100">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-primary"><i class="fa fa-pie-chart mr-1"></i> Estado de Incidentes</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-pie pt-4 pb-2">
                                <canvas id="incidentesChart" style="height: 180px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8 col-lg-7">
                    <div class="card shadow mb-4 h-100">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-danger"><i class="fa fa-bar-chart mr-1"></i> Mapa de Riesgos por Nivel</h6>
                        </div>
                        <div class="card-body">
                            <div class="chart-bar">
                                <canvas id="riesgosChart" style="height: 180px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
              <div class="col-12">
                  <div class="card shadow-sm border-0">
                      <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                          <h6 class="m-0 font-weight-bold text-primary">
                              <i class="fa fa-clock-o mr-1"></i> Últimos Riesgos Identificados
                          </h6>
                          <a href="{{ route('matriz-riesgos.index') }}" class="btn btn-sm btn-outline-primary">Ver Matriz Completa</a>
                      </div>
                      <div class="card-body p-0">
                          <div class="table-responsive">
                              <table class="table table-hover table-borderless align-middle mb-0 text-sm">
                                  <thead class="thead-light">
                                      <tr>
                                          <th>Fecha</th>
                                          <th>Proceso / Zona</th>
                                          <th>Peligro</th>
                                          <th class="text-center">Nivel</th>
                                          <th>Registrado Por</th>
                                      </tr>
                                  </thead>
                                  <tbody>
                                      @forelse($ultimosRiesgos as $riesgo)
                                          <tr style="border-bottom: 1px solid #f0f4f8;">
                                              <td class="text-muted">{{ $riesgo->created_at->format('d/m/Y') }}</td>
                                              <td><strong>{{ $riesgo->proceso }}</strong> <br> <small class="text-muted">{{ $riesgo->zona_lugar }}</small></td>
                                              <td>{{ $riesgo->clasificacion_peligro }}</td>
                                              <td class="text-center">
                                                  @php
                                                      $badgeClass = 'badge-secondary';
                                                      if($riesgo->nivel_riesgo == 'Bajo') $badgeClass = 'badge-success';
                                                      if($riesgo->nivel_riesgo == 'Medio') $badgeClass = 'badge-warning';
                                                      if($riesgo->nivel_riesgo == 'Alto') $badgeClass = 'badge-danger';
                                                      if($riesgo->nivel_riesgo == 'Extremo') $badgeClass = 'badge-dark';
                                                  @endphp
                                                  <span class="badge {{ $badgeClass }} px-2 py-1">{{ strtoupper($riesgo->nivel_riesgo) }}</span>
                                              </td>
                                              <td class="small">{{ $riesgo->responsable->name ?? 'Usuario Eliminado' }}</td>
                                          </tr>
                                      @empty
                                          <tr>
                                              <td colspan="5" class="text-center py-4 text-muted">No hay riesgos recientes registrados.</td>
                                          </tr>
                                      @endforelse
                                  </tbody>
                              </table>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
            @else
            <div class="alert alert-info">
                <i class="fa fa-info-circle mr-2"></i> Bienvenido a Sinergia SST. Utiliza el menú lateral para reportar incidentes o consultar tus documentos.
            </div>
            @endhasrole

        </main>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // --- GRÁFICO 1: INCIDENTES (Dona) ---
    var ctxIncidentes = document.getElementById("incidentesChart");
    var incidentesChart = new Chart(ctxIncidentes, {
        type: 'doughnut',
        data: {
            labels: ["Pendientes", "En Investigación", "Cerrados"],
            datasets: [{
                data: [
                    {{ $graficoIncidentes['Pendiente'] }}, 
                    {{ $graficoIncidentes['En Investigación'] }}, 
                    {{ $graficoIncidentes['Cerrado'] }}
                ],
                backgroundColor: ['#e74a3b', '#36b9cc', '#1cc88a'], // Rojo, Azul, Verde
                hoverBackgroundColor: ['#e02d1b', '#2c9faf', '#17a673'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            cutoutPercentage: 70, // Qué tan grueso es el anillo
            legend: { display: true, position: 'bottom' }
        }
    });

    // --- GRÁFICO 2: RIESGOS (Barras) ---
    var ctxRiesgos = document.getElementById("riesgosChart");
    var riesgosChart = new Chart(ctxRiesgos, {
        type: 'bar',
        data: {
            labels: ["Bajo", "Medio", "Alto", "Extremo"],
            datasets: [{
                label: "Cantidad de Peligros",
                backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b', '#5a5c69'], // Verde, Amarillo, Rojo, Gris Oscuro
                data: [
                    {{ $graficoRiesgos['Bajo'] }}, 
                    {{ $graficoRiesgos['Medio'] }}, 
                    {{ $graficoRiesgos['Alto'] }}, 
                    {{ $graficoRiesgos['Extremo'] }}
                ],
            }],
        },
        options: {
            maintainAspectRatio: false,
            legend: { display: false },
            scales: {
                yAxes: [{
                    ticks: { beginAtZero: true, stepSize: 1 } // Para que cuente de 1 en 1
                }]
            }
        }
    });
});
</script>
<script>
    var ctxPeso = document.getElementById("pesosEstandaresChart");
    var pesosEstandaresChart = new Chart(ctxPeso, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($labelsEstandares) !!},
            datasets: [{
                data: {!! json_encode($valoresEstandares) !!},
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', 
                    '#858796', '#5a5c69', '#f8f9fc', '#4e73df'
                ],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                hoverBorderColor: "rgba(234, 236, 244, 1)",
            }],
        },
        options: {
            maintainAspectRatio: false,
            tooltips: {
                callbacks: {
                    label: function(tooltipItem, data) {
                        var label = data.labels[tooltipItem.index] || '';
                        var value = data.datasets[0].data[tooltipItem.index];
                        return label + ': ' + value + '%';
                    }
                }
            },
            legend: {
                display: true,
                position: 'bottom',
                labels: { boxWidth: 12 }
            },
            cutoutPercentage: 70,
        },
    });
</script>
<script>
function alertaSinEmpresa() {
    Swal.fire({
        title: '¡Paso previo requerido!',
        text: "Antes de realizar una autoevaluación, debes registrar los datos de la empresa (NIT, Riesgo, Empleados).",
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#4e73df',
        cancelButtonColor: '#858796',
        confirmButtonText: '<i class="fa fa-plus"></i> Registrar Empresa Ahora',
        cancelButtonText: 'Después',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Cambia 'perfil.create' por el nombre de tu ruta de registro de empresa
            window.location.href = "{{ route('perfil.index') }}"; 
        }
    });
}
</script>
@endsection