@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pt-4">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('home') }}" class="text-decoration-none text-secondary mb-2 d-inline-block font-weight-bold">
                <i class="fa fa-arrow-left mr-1"></i> Volver al Dashboard
            </a>
            
            <h2 class="h3 mb-0 text-gray-800" style="color: #2c3e50;">Matriz de Identificación de Peligros y Riesgos</h2>
            <p class="text-muted small mt-1">Gestión integral de riesgos laborales de la organización.</p>
        </div>
        <div>
            <a href="{{ route('matriz-riesgos.excel') }}" class="btn shadow-sm mr-2" style="background-color: #1cc88a; color: white; font-weight: bold;">
                <i class="fa fa-file-excel-o mr-1"></i> Exportar Excel
            </a>

            <a href="{{ route('matriz-riesgos.pdf') }}" class="btn shadow-sm mr-2" style="background-color: #e74a3b; color: white; font-weight: bold;">
                <i class="fa fa-file-pdf-o mr-1"></i> Exportar PDF
            </a>
            
            <a href="{{ route('matriz-riesgos.create') }}" class="btn shadow-sm" style="background-color: #4e73df; color: white; font-weight: bold;">
                <i class="fa fa-plus-circle mr-1"></i> Registrar Riesgo
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
                <table class="table table-hover table-bordered mb-0 text-sm" style="font-size: 0.85rem; white-space: nowrap;">
                    <thead style="background-color: #f8f9fc; color: #4e73df;">
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Proceso</th>
                            <th>Zona / Lugar</th>
                            <th>Actividad</th>
                            <th class="text-center">¿Rutinaria?</th>
                            <th>Clasificación del Peligro</th>
                            <th>Descripción</th>
                            <th class="text-center">Nivel de Riesgo</th>
                            <th>Registrado Por</th>
                            <th class="text-center" style="width: 100px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riesgos as $riesgo)
                        <tr>
                            <td class="text-center font-weight-bold text-muted">{{ $riesgo->id }}</td>
                            <td class="font-weight-bold text-dark">{{ $riesgo->proceso }}</td>
                            <td>{{ $riesgo->zona_lugar }}</td>
                            <td>{{ $riesgo->actividad }}</td>
                            <td class="text-center">
                                @if($riesgo->es_rutinaria)
                                    <span class="badge badge-info px-2 py-1">Sí</span>
                                @else
                                    <span class="badge badge-secondary px-2 py-1">No</span>
                                @endif
                            </td>
                            <td>{{ $riesgo->clasificacion_peligro }}</td>
                            <td>
                                <span title="{{ $riesgo->descripcion_peligro }}">
                                    {{ \Illuminate\Support\Str::limit($riesgo->descripcion_peligro, 30) }}
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $badgeClass = 'badge-secondary';
                                    if(strtolower($riesgo->nivel_riesgo) == 'bajo') $badgeClass = 'badge-success';
                                    if(strtolower($riesgo->nivel_riesgo) == 'medio') $badgeClass = 'badge-warning';
                                    if(strtolower($riesgo->nivel_riesgo) == 'alto') $badgeClass = 'badge-danger';
                                    if(strtolower($riesgo->nivel_riesgo) == 'extremo') $badgeClass = 'badge-dark';
                                @endphp
                                <span class="badge {{ $badgeClass }} px-3 py-2" style="font-size: 0.8rem;">
                                    {{ strtoupper($riesgo->nivel_riesgo) }}
                                </span>
                            </td>
                            <td class="text-muted small">
                                {{ $riesgo->responsable->name ?? 'Usuario Eliminado' }}
                            </td>
                            <td class="text-center">
                                <a href="{{ route('matriz-riesgos.edit', $riesgo->id) }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Editar Riesgo">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('matriz-riesgos.destroy', $riesgo->id) }}" method="POST" class="d-inline form-eliminar">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Eliminar Riesgo">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="fa fa-folder-open-o fa-3x mb-3 d-block"></i>
                                Aún no hay riesgos registrados en la matriz.<br>
                                Comienza haciendo clic en el botón <strong>"Registrar Nuevo Riesgo"</strong>.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const formularios = document.querySelectorAll('.form-eliminar');
        formularios.forEach(formulario => {
            formulario.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: '¿Eliminar este riesgo?',
                    text: "Se borrará permanentemente de la matriz.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b',
                    cancelButtonColor: '#858796',
                    confirmButtonText: '<i class="fa fa-trash"></i> Sí, eliminar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                })
            });
        });
    });
</script>
@endsection