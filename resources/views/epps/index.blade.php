@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <a href="{{ route('home') }}" class="text-decoration-none text-secondary mb-2 d-inline-block font-weight-bold">
                <i class="fa fa-arrow-left mr-1"></i> Volver al Dashboard
            </a>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary font-weight-bold">📦 Catálogo de EPP</h2>
            <p class="text-muted">Definición de elementos de protección personal por categoría.</p>
        </div>
        <a href="{{ route('epps.create') }}" class="btn btn-primary shadow-sm">
            <i class="fa fa-plus mr-2"></i> Nuevo Elemento
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
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Icono</th>
                            <th>Nombre del EPP</th>
                            <th>Categoría</th>
                            <th>Vida Útil Sugerida</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($epps as $epp)
                        <tr>
                            <td style="width: 50px;" class="text-center">
                                @php
                                    $icon = match($epp->categoria) {
                                        'Cabeza' => 'fa-hard-hat',
                                        'Ojos/Cara' => 'fa-glasses',
                                        'Manos' => 'fa-mitten',
                                        'Pies' => 'fa-shoe-prints',
                                        'Auditiva' => 'fa-headphones',
                                        'Respiratoria' => 'fa-mask',
                                        default => 'fa-shield-alt'
                                    };
                                @endphp
                                <i class="fas {{ $icon }} fa-2x text-muted"></i>
                            </td>
                            <td class="align-middle font-weight-bold">{{ $epp->nombre }}</td>
                            <td class="align-middle">
                                <span class="badge badge-info px-3 py-2">{{ $epp->categoria }}</span>
                            </td>
                            <td class="align-middle">{{ $epp->vida_util_meses }} Meses</td>
                            <td class="text-center align-middle">
                                <a href="{{ route('epps.edit', $epp->id) }}" class="btn btn-sm btn-outline-warning border-0">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger border-0 delete-epp-btn" 
                                        data-id="{{ $epp->id }}" 
                                        data-nombre="{{ $epp->nombre }}">
                                    <i class="fa fa-trash"></i>
                                </button>

                                <form id="delete-epp-form-{{ $epp->id }}" 
                                    action="{{ route('epps.destroy', $epp->id) }}" 
                                    method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">El catálogo está vacío.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    document.querySelectorAll('.delete-epp-btn').forEach(button => {
        button.addEventListener('click', function() {
            const eppId = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');

            Swal.fire({
                title: '¿Eliminar del catálogo?',
                text: `Estás a punto de quitar "${nombre}". Esto podría afectar los registros de entrega futuros.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b', // Rojo peligro
                cancelButtonColor: '#858796', // Gris
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Enviamos el formulario oculto
                    document.getElementById(`delete-epp-form-${eppId}`).submit();
                }
            });
        });
    });
</script>
@endsection