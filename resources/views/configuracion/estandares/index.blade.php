@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('home') }}" class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left mr-1"></i> Volver al Dashboard
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
    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">⚙️ Configuración de Estándares Mínimos (Res. 0312)</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('item-estandar.store') }}" method="POST" class="mb-4">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-7">
                        <label>Descripción del Ítem</label>
                        <input type="text" name="nombre" class="form-control" required placeholder="Ej: Registro de entrega de EPP">
                    </div>
                    <div class="col-md-3">
                        <label>Peso (%)</label>
                        <input type="number" step="0.01" name="porcentaje" class="form-control" required placeholder="0.00">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-primary btn-block">Agregar</button>
                    </div>
                </div>
            </form>

            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Nombre del Estándar</th>
                        <th>Porcentaje</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $i)
                    <tr>
                        <td class="align-middle">{{ $i->nombre }}</td>
                        <td class="align-middle">{{ $i->porcentaje }}%</td>
                        <td class="d-flex justify-content-center align-items-center">
                            
                            <button type="button" class="btn btn-sm btn-info mr-2" data-toggle="modal" data-target="#editModal{{ $i->id }}">
                                <i class="fa fa-edit"></i>
                            </button>

                            <form action="{{ route('item-estandar.destroy', $i->id) }}" method="POST" class="form-eliminar m-0">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>

                            <div class="modal fade" id="editModal{{ $i->id }}" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('item-estandar.update', $i->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <div class="modal-header">
                                                <h5 class="modal-title">Editar Estándar</h5>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>
                                            <div class="modal-body text-left">
                                                <div class="form-group">
                                                    <label>Nombre del Estándar</label>
                                                    <input type="text" name="nombre" class="form-control" value="{{ $i->nombre }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Porcentaje (%)</label>
                                                    <input type="number" step="0.01" name="porcentaje" class="form-control" value="{{ $i->porcentaje }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection