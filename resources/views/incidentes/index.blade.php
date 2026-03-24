@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('home') }}" class="text-decoration-none text-secondary mb-2 d-inline-block font-weight-bold">
                <i class="fa fa-arrow-left mr-1"></i> Volver al Dashboard
            </a>
            <h2 class="h3 mb-0 text-gray-800" style="color: #2c3e50;">
                <i class="fa fa-inbox text-primary mr-2"></i> Bandeja de Incidentes
            </h2>
            <p class="text-muted small mt-1">Historial y gestión de reportes de accidentes e incidentes.</p>
        </div>
       <div>
            <a href="{{ route('incidentes.excel') }}" class="btn shadow-sm mr-2 mb-2" style="background-color: #1cc88a; color: white; font-weight: bold;">
                <i class="fa fa-file-excel-o mr-1"></i> Exportar Excel
            </a>

            <a href="{{ route('incidentes.pdf') }}" class="btn shadow-sm mr-2 mb-2" style="background-color: #e74a3b; color: white; font-weight: bold;">
                <i class="fa fa-file-pdf-o mr-1"></i> Exportar PDF
            </a>

            <a href="{{ route('incidentes.create') }}" class="btn shadow-sm mb-2" style="background-color: #4e73df; color: #fff; font-weight: bold;">
                <i class="fa fa-plus-circle mr-1"></i> Nuevo Reporte
            </a>
        </div>
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

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 text-sm">
                    <thead style="background-color: #f8f9fc; color: #4e73df;">
                        <tr>
                            <th class="text-center" style="width: 80px;">ID</th>
                            <th>Fecha y Hora</th>
                            <th>Reportado Por</th>
                            <th>Tipo de Evento</th>
                            <th>Lugar</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center" style="width: 120px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incidentes as $incidente)
                        <tr>
                            <td class="text-center font-weight-bold text-muted">#{{ $incidente->id }}</td>
                            <td>
                                <strong>{{ \Carbon\Carbon::parse($incidente->fecha_incidente)->format('d/m/Y') }}</strong> <br>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($incidente->hora_incidente)->format('h:i A') }}</small>
                            </td>
                            <td>{{ $incidente->reportante->name ?? 'Usuario Eliminado' }}</td>
                            <td>
                                @if($incidente->tipo_evento == 'Incidente')
                                    <span class="text-warning font-weight-bold"><i class="fa fa-exclamation-circle"></i> Incidente</span>
                                @else
                                    <span class="text-danger font-weight-bold"><i class="fa fa-medkit"></i> Accidente</span>
                                @endif
                            </td>
                            <td>{{ $incidente->lugar_evento }}</td>
                            <td class="text-center">
                                @php
                                    $badgeClass = 'badge-secondary';
                                    if($incidente->estado == 'Pendiente') $badgeClass = 'badge-danger';
                                    if($incidente->estado == 'En Investigación') $badgeClass = 'badge-info';
                                    if($incidente->estado == 'Cerrado') $badgeClass = 'badge-success';
                                @endphp
                                <span class="badge {{ $badgeClass }} px-3 py-2" style="font-size: 0.8rem;">
                                    {{ strtoupper($incidente->estado) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('incidentes.edit', $incidente->id) }}" class="btn btn-sm btn-outline-primary" title="Gestionar / Ver Detalles">
                                    <i class="fa fa-folder-open"></i> Abrir
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa fa-inbox fa-3x mb-3 d-block"></i>
                                No hay reportes de incidentes en este momento.
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