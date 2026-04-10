@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <a href="{{ route('home') }}" class="text-decoration-none text-secondary mb-2 d-inline-block font-weight-bold">
                <i class="fa fa-arrow-left mr-1"></i> Volver al Dashboard
            </a>
    <div class="d-flex justify-content-between align-items-center mb-4">
        
        <div>
            <h2 class="font-weight-bold text-primary">👥 Directorio de Empleados</h2>
            <p class="text-muted">Gestión integral de personal y perfiles sociodemográficos.</p>
        </div>
        <a href="{{ route('empleados.create') }}" class="btn btn-primary btn-lg shadow-sm">
            <i class="fa fa-user-plus mr-2"></i> Nuevo Empleado
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


    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="tablaEmpleados">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Empleado</th>
                            <th class="border-0">Identificación</th>
                            <th class="border-0">Cargo / Área</th>
                            <th class="border-0">Contrato</th>
                            <th class="border-0">Seguridad Social</th>
                            <th class="border-0 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($empleados as $empleado)
                        <tr>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3" style="width: 40px; height: 40px;">
                                        {{ substr($empleado->nombre_completo, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-weight-bold">{{ $empleado->nombre_completo }}</div>
                                        <small class="text-muted">{{ $empleado->email_personal ?? 'Sin correo' }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-light border text-dark p-2">
                                    <i class="fa fa-id-card mr-1 text-muted"></i> {{ $empleado->cedula }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <div>{{ $empleado->cargo }}</div>
                                <small class="text-primary font-weight-bold">{{ $empleado->area_departamento ?? 'General' }}</small>
                            </td>
                            <td class="align-middle">
                                @php
                                    $badgeColor = match($empleado->tipo_contrato) {
                                        'Termino Indefinido' => 'success',
                                        'Termino Fijo' => 'warning',
                                        'Obra o Labor' => 'info',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge badge-{{ $badgeColor }}-soft text-{{ $badgeColor }} p-2">
                                    {{ $empleado->tipo_contrato }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <small class="d-block"><strong>EPS:</strong> {{ $empleado->eps ?? 'N/A' }}</small>
                                <small class="d-block"><strong>ARL:</strong> {{ $empleado->arl ?? 'N/A' }}</small>
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group shadow-sm">
                                    <a href="{{ route('empleados.show', $empleado->id) }}" class="btn btn-white btn-sm" title="Ver Ficha">
                                        <i class="fa fa-eye text-primary"></i>
                                    </a>
                                    <a href="{{ route('empleados.edit', $empleado->id) }}" class="btn btn-white btn-sm" title="Editar">
                                        <i class="fa fa-edit text-warning"></i>
                                    </a>
                                    <div class="btn-group shadow-sm">
                                        <button type="button" class="btn btn-white btn-sm delete-btn" 
                                                data-id="{{ $empleado->id }}" 
                                                data-nombre="{{ $empleado->nombre_completo }}">
                                            <i class="fa fa-trash text-danger"></i>
                                        </button>

                                        <form id="delete-form-{{ $empleado->id }}" 
                                            action="{{ route('empleados.destroy', $empleado->id) }}" 
                                            method="POST" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fa fa-users fa-5x text-gray-300 mb-3 d-block"></i> 
                                <p class="text-muted h6 mt-2">No hay empleados registrados aún.</p>
                                <a href="{{ route('empleados.create') }}" class="btn btn-primary btn-sm mt-3">
                                    <i class="fa fa-plus mr-1"></i> Agregar el primero
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilo para los badges suaves */
    .badge-success-soft { background-color: #e1fcef; color: #1cc88a; }
    .badge-warning-soft { background-color: #fff9e6; color: #f6c23e; }
    .badge-info-soft    { background-color: #e5f4ff; color: #36b9cc; }
    .btn-white { background: white; border: 1px solid #e3e6f0; }
    .btn-white:hover { background: #f8f9fa; }
</style>
<script>
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const empleadoId = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');

            Swal.fire({
                title: '¿Estás seguro?',
                text: `Vas a eliminar a ${nombre}. ¡Esta acción no se puede deshacer!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b', // Rojo de peligro
                cancelButtonColor: '#858796', // Gris
                confirmButtonText: 'Sí, eliminar registro',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Si el usuario confirma, enviamos el formulario correspondiente
                    document.getElementById(`delete-form-${empleadoId}`).submit();
                }
            });
        });
    });
</script>
@endsection