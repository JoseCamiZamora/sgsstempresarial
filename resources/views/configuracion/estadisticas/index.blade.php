@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="h3 mb-0 text-gray-800"><i class="fa fa-database text-primary mr-2"></i> Datos de Exposición Mensual</h2>
        <a href="{{ route('home') }}" class="btn btn-secondary btn-sm shadow-sm">
            <i class="fa fa-arrow-left"></i> Volver al Dashboard
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
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 font-weight-bold text-primary">Registrar Nuevo Periodo</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('estadisticas.store') }}" method="POST">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-2 mb-3">
                        <label class="small font-weight-bold">Mes</label>
                        <select name="mes" class="form-control" required>
                            @php $meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre']; @endphp
                            @foreach($meses as $index => $mes)
                                <option value="{{ $index + 1 }}" {{ (date('n') == $index + 1) ? 'selected' : '' }}>{{ $mes }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="small font-weight-bold">Año</label>
                        <input type="number" name="anio" class="form-control" value="{{ date('Y') }}" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="small font-weight-bold">N° Trabajadores</label>
                        <input type="number" name="num_trabajadores" class="form-control" placeholder="Ej: 100" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="small font-weight-bold">Horas Trabajadas</label>
                        <input type="number" name="horas_trabajadas" class="form-control" placeholder="HHT" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="small font-weight-bold">Días Prog.</label>
                        <input type="number" name="dias_programados" class="form-control" value="24" required>
                    </div>
                    <div class="col-md-2 mb-3">
                        <button type="submit" class="btn btn-primary btn-block shadow-sm">
                            <i class="fa fa-save"></i> Guardar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead class="bg-light">
                        <tr>
                            <th>Periodo</th>
                            <th>Trabajadores</th>
                            <th>Horas Hombre (HHT)</th>
                            <th>Días Prog.</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($estadisticas as $est)
                        <tr>
                            <td>{{ $meses[$est->mes - 1] }} - {{ $est->anio }}</td>
                            <td>{{ $est->num_trabajadores }}</td>
                            <td>{{ number_format($est->horas_trabajadas) }}</td>
                            <td>{{ $est->dias_programados }}</td>
                            <td>
                                <form action="{{ route('estadisticas.destroy', $est->id) }}" method="POST" class="d-inline formulario-eliminar">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5">No hay datos registrados aún.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@section('scripts') {{-- Asegúrate de usar la sección de scripts de tu layout --}}
<script>
    // Usamos delegación de eventos para que siempre encuentre el formulario
    $(document).on('submit', '.formulario-eliminar', function(e) {
        e.preventDefault(); // Aquí detenemos el envío de verdad
        
        var formulario = this; // Guardamos el formulario actual

        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto! Se eliminarán los datos de exposición de este mes.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b', // Color rojo de Bootstrap
            cancelButtonColor: '#858796', // Color gris
            confirmButtonText: 'Sí, eliminarlo',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Si el usuario confirma, enviamos el formulario
                formulario.submit();
            }
        });
    });
</script>
@endsection
@endsection