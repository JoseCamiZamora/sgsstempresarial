@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <a href="{{ route('home') }}" class="text-decoration-none text-secondary mb-2 d-inline-block font-weight-bold">
        <i class="fa fa-arrow-left mr-1"></i> Volver al Dashboard
    </a>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold"><i class="fa fa-calendar-check-o text-info mr-2"></i> Plan Anual de Trabajo y Capacitaciones</h1>
        <a href="{{ route('plan-trabajo.create') }}" class="btn shadow-sm" style="background-color: #36b9cc; color: white; font-weight: bold;">
            <i class="fa fa-plus-circle mr-1"></i> Programar Actividad
        </a>
    </div>
     <script>
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: '¡Listo!',
                    text: '{{ session("success") }}',
                    timer: 2500,
                    showConfirmButton: false
                });
            });
        @endif
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Ups... algo salió mal',
                text: "{{ session('error') }}",
                confirmButtonColor: '#e74a3b',
            });
        @endif
    </script>

    <div class="card shadow mb-4 border-bottom-info">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-info">Cronograma de Actividades</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered text-center" width="100%" cellspacing="0">
                    <thead class="bg-light text-dark">
                        <tr>
                            <th>Fecha Programada</th>
                            <th>Actividad / Capacitación</th>
                            <th>Responsable</th>
                            <th>Estado</th>
                            <th>Evidencia</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($actividades as $act)
                        <tr>
                            <td class="align-middle"><strong>{{ \Carbon\Carbon::parse($act->fecha_programada)->format('d/m/Y') }}</strong></td>
                            <td class="align-middle text-left">{{ $act->actividad }}</td>
                            <td class="align-middle">{{ $act->responsable->name ?? 'No asignado' }}</td>
                            <td class="align-middle">
                                @if($act->estado == 'Pendiente') <span class="badge badge-warning px-3 py-2">Pendiente</span>
                                @elseif($act->estado == 'En Ejecución') <span class="badge badge-primary px-3 py-2">En Ejecución</span>
                                @elseif($act->estado == 'Ejecutada') <span class="badge badge-success px-3 py-2">Ejecutada</span>
                                @else <span class="badge badge-danger px-3 py-2">Cancelada</span> @endif
                            </td>
                            
                            <td class="align-middle">
                                @if($act->evidencia_pdf)
                                    <a href="{{ asset('storage/' . $act->evidencia_pdf) }}" target="_blank" class="btn btn-sm btn-success font-weight-bold">
                                        <i class="fa fa-download mr-1"></i> Ver Adjunto
                                    </a>
                                @else
                                    <span class="text-muted small"><i class="fa fa-times-circle text-danger"></i> Sin adjuntar</span>
                                @endif
                            </td>
                            
                            <td class="align-middle">
                                <a href="{{ route('plan-trabajo.edit', $act->id) }}" class="btn btn-sm btn-outline-info font-weight-bold shadow-sm">
                                    <i class="fa fa-edit"></i> Gestionar
                                </a>
                            </td>
                        </tr>
                        
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fa fa-calendar-times-o fa-3x mb-3 d-block"></i>
                                Aún no hay actividades programadas.
                            </td>
                        </tr>
                        @endforelse
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection