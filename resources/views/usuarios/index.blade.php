@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 pt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('home') }}" class="text-decoration-none text-secondary mb-2 d-inline-block font-weight-bold">
                <i class="fa fa-arrow-left mr-1"></i> Volver al Dashboard
            </a>
            
            <h2 class="h3 mb-0 text-gray-800" style="color: #2c3e50;">Gestión de Usuarios</h2>
        </div>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary shadow-sm">
            <i class="fa fa-user-plus mr-1"></i> Nuevo Usuario
        </a>
    </div>

    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: '¡Listo!',
                    text: '{{ session("success") }}',
                    timer: 2500,
                    showConfirmButton: false
                });
            });
        </script>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th>Identificación</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $user)
                        <tr style="border-bottom: 1px solid #f0f4f8;">
                            <td>{{ $user->identificacion }}</td>
                            <td class="font-weight-bold" style="color: #4A90E2;">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if(!empty($user->getRoleNames()))
                                    @foreach($user->getRoleNames() as $rolName)
                                        <span class="badge badge-info px-2 py-1">{{ $rolName }}</span>
                                    @endforeach
                                @else
                                    <span class="badge badge-secondary px-2 py-1">Sin Rol</span>
                                @endif
                            </td>
                            <td>
                                @if($user->estado == 'A')
                                    <span class="badge badge-success px-2 py-1">Activo</span>
                                @else
                                    <span class="badge badge-danger px-2 py-1">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('usuarios.edit', $user->id) }}" class="btn btn-sm btn-outline-primary" title="Editar">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" class="d-inline form-eliminar">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Seleccionamos todos los formularios que tengan la clase 'form-eliminar'
        const formularios = document.querySelectorAll('.form-eliminar');

        formularios.forEach(formulario => {
            formulario.addEventListener('submit', function (e) {
                e.preventDefault(); // Detenemos el envío del formulario por defecto

                Swal.fire({
                    title: '¿Estás completamente seguro?',
                    text: "El usuario será eliminado del sistema y no podrás revertir esto.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74a3b', // Color rojo para eliminar
                    cancelButtonColor: '#858796', // Color gris para cancelar
                    confirmButtonText: '<i class="fa fa-trash"></i> Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Si el usuario confirma, enviamos el formulario
                        this.submit();
                    }
                })
            });
        });
    });
</script>
@endsection