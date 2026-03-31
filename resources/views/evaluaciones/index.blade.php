@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <a href="{{ route('home') }}" class="text-decoration-none text-secondary mb-2 d-inline-block font-weight-bold">
                <i class="fa fa-arrow-left mr-1"></i> Volver al Dashboard
            </a>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
         
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            <i class="fa fa-history text-primary mr-2"></i> Historial de Autoevaluaciones
        </h1>
        <a href="{{ route('evaluacion.crear', ['empresaId' => $empresaPerfil->id]) }}" class="btn btn-primary shadow-sm font-weight-bold">
            <i class="fa fa-plus-circle"></i> Nueva Evaluación
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

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-left-info">
                <div class="card-body py-2">
                    <small class="font-weight-bold text-uppercase text-muted">Estado de Configuración de Estándares:</small>
                    <div class="d-flex justify-content-around mt-2">
                        @foreach($sumas as $tipo => $total)
                            <span class="badge badge-{{ $total == 100 ? 'success' : 'warning' }} p-2">
                                Plantilla {{ $tipo }}: {{ $total }}% 
                                {!! $total == 100 ? '<i class="fa fa-check"></i>' : '<i class="fa fa-times"></i>' !!}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Empresa</th> <th>Riesgo / Empleados</th> <th>Plantilla</th> <th>Puntaje</th>
                            <th>Estado</th>
                            <th>Realizado por</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluaciones as $ev)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($ev->fecha_evaluacion)->format('d/m/Y') }}</td>
                            <td class="font-weight-bold text-primary">{{ $ev->empresa->razon_social }}</td>
                            <td>
                                <span class="badge badge-secondary">Riesgo {{ $ev->empresa->nivel_riesgo }}</span>
                                <span class="badge badge-info">{{ $ev->empresa->numero_trabajadores }} Emp.</span>
                            </td>
                            <td><span class="badge badge-dark">{{ $ev->tipo_plantilla_aplicada }} Ítems</span></td>
                            <td class="font-weight-bold">{{ $ev->puntaje_final }}%</td>
                            <td>
                                @php 
                                    $color = $ev->puntaje_final < 60 ? 'danger' : ($ev->puntaje_final <= 85 ? 'warning' : 'success'); 
                                @endphp
                                <span class="badge badge-{{ $color }} px-3 py-2 text-uppercase">{{ $ev->nivel_madurez }}</span>
                            </td>
                            <td><small>{{ $ev->evaluador }}</small></td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('evaluacion.edit', $ev->id) }}" class="btn btn-sm btn-info" title="Editar">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <a href="{{ route('evaluacion.pdf', $ev->id) }}" target="_blank" class="btn btn-sm btn-success" title="PDF">
                                        <i class="fa fa-file-pdf"></i>
                                    </a>
                                    <form action="{{ route('evaluacion.destroy', $ev->id) }}" method="POST" class="d-inline form-eliminar">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Eliminar Evaluación" style="/*! width: 68px; */height: 27px;">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
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
    // Seleccionamos todos los formularios que tengan la clase 'form-eliminar'
    document.querySelectorAll('.form-eliminar').forEach(form => {
        form.addEventListener('submit', function(e) {
            // 1. Detenemos el envío automático del formulario
            e.preventDefault();

            // 2. Disparamos la alerta de SweetAlert2
            Swal.fire({
                title: '¿Estás completamente seguro?',
                text: "Se eliminará la cabecera y todo el detalle de respuestas. ¡Esta acción no se puede deshacer!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b', // Color rojo (peligro)
                cancelButtonColor: '#858796', // Color gris
                confirmButtonText: 'Sí, eliminar registro',
                cancelButtonText: 'No, cancelar',
                reverseButtons: true // Pone el botón de cancelar a la izquierda
            }).then((result) => {
                // 3. Si el usuario confirma, enviamos el formulario manualmente
                if (result.isConfirmed) {
                    this.submit();
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    // Opcional: una pequeña notificación de que se salvó el dato
                    Swal.fire({
                        title: 'Cancelado',
                        text: 'Tu registro está a salvo :)',
                        icon: 'error',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });
    });
</script>
@endsection