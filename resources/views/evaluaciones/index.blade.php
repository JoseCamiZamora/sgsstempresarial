@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 mt-4">
    <a href="{{ route('home') }}" class="text-decoration-none text-secondary mb-2 d-inline-block font-weight-bold">
            <i class="fa fa-arrow-left mr-1"></i> Volver al Dashboard
        </a>
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold"><i class="fa fa-history text-primary mr-2"></i> Historial de Autoevaluaciones</h1>
        <a href="{{ route('evaluacion.create') }}" class="btn btn-primary shadow-sm font-weight-bold">
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
            @if($sumaTotal == 100)
                <div class="alert alert-success shadow-sm border-left-success">
                    <i class="fa fa-check-circle mr-2"></i> 
                    <strong>Configuración Perfecta:</strong> La suma de los estándares es exactamente <strong>100%</strong>. El sistema está listo para evaluar.
                </div>
            @else
                <div class="alert alert-warning shadow-sm border-left-warning">
                    <i class="fa fa-exclamation-triangle mr-2"></i> 
                    <strong>Atención:</strong> La suma actual es de <strong>{{ $sumaTotal }}%</strong>. 
                    @if($sumaTotal < 100)
                        Te falta un <strong>{{ 100 - $sumaTotal }}%</strong> para completar la matriz legal.
                    @else
                        Te has excedido por <strong>{{ $sumaTotal - 100 }}%</strong>. Revisa los valores.
                    @endif
                </div>
            @endif
        </div>
    </div>
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead class="bg-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Puntaje</th>
                            <th>Estado</th>
                            <th>Realizado por</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($evaluaciones as $ev)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($ev->fecha_evaluacion)->format('d/m/Y') }}</td>
                            <td class="font-weight-bold">{{ $ev->puntaje_total }}%</td>
                            <td>
                                @php $color = $ev->puntaje_total < 60 ? 'danger' : ($ev->puntaje_total <= 85 ? 'warning' : 'success'); @endphp
                                <span class="badge badge-{{ $color }} px-3 py-2">{{ $ev->estado_resultado }}</span>
                            </td>
                            <td>{{ $ev->user->name }}</td>
                            <td>
                                
                                <a href="{{ route('evaluacion.edit', $ev->id) }}" class="btn btn-sm btn-info"><i class="fa fa-edit"></i></a>
                                <form action="{{ route('evaluacion.destroy', $ev->id) }}" method="POST" class="d-inline form-eliminar">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                                <a href="{{ route('evaluacion.pdf', $ev->id) }}" class="btn btn-sm btn-success mr-1" title="Descargar Reporte" target="_blank">
                                    <i class="fa fa-file"></i>
                                </a>
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
    // Escuchamos el evento de envío de todos los formularios con la clase 'form-eliminar'
    $('.form-eliminar').submit(function(e) {
        e.preventDefault(); // Detenemos el envío automático

        Swal.fire({
            title: '¿Estás seguro de eliminar?',
            text: "Esta acción no se puede deshacer y afectará el puntaje del Dashboard.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b', // Color rojo (peligro)
            cancelButtonColor: '#858796', // Color gris
            confirmButtonText: 'Sí, eliminar registro',
            cancelButtonText: 'Cancelar',
            reverseButtons: true // Pone el botón de cancelar a la izquierda
        }).then((result) => {
            if (result.isConfirmed) {
                // Si el usuario confirma, enviamos el formulario manualmente
                this.submit();
            }
        });
    });
</script>
@endsection