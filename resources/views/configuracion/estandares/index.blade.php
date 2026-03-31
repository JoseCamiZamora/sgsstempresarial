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
            <form action="{{ route('item-estandar.store') }}" method="POST" class="mb-5 border-bottom pb-4">
                @csrf
                <div class="row">
                    <div class="col-md-3 form-group">
                        <label>Ciclo PHVA</label>
                        <select name="ciclo" class="form-control">
                            <option value="">Seleccione...</option>
                            <option value="Planear">Planear</option>
                            <option value="Hacer">Hacer</option>
                            <option value="Verificar">Verificar</option>
                            <option value="Actuar">Actuar</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group">
                        <label>Numeral</label>
                        <input type="text" name="numeral" class="form-control" placeholder="Ej: 1.1.1">
                    </div>
                    <div class="col-md-4 form-group">
                        <label>Clasificación (Res. 0312) <span class="text-danger">*</span></label>
                        <select name="tipo_plantilla" class="form-control" required>
                            <option value="" disabled selected>Seleccione el grupo...</option>
                            <option value="7">7 Estándares</option>
                            <option value="21">21 Estándares</option>
                            <option value="60">60 Estándares</option>
                        </select>
                    </div>
                    <div class="col-md-3 form-group">
                        <label>Peso (%) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="porcentaje" class="form-control" required placeholder="0.00">
                    </div>
                </div>

                <div class="row align-items-end">
                    <div class="col-md-5 form-group">
                        <label>Descripción del Estándar <span class="text-danger">*</span></label>
                        <textarea name="nombre" class="form-control" rows="2" required placeholder="Descripción del ítem a evaluar..."></textarea>
                    </div>
                    <div class="col-md-5 form-group">
                        <label>Modo de Verificación</label>
                        <textarea name="modo_verificacion" class="form-control" rows="2" placeholder="¿Cómo se verifica este ítem?..."></textarea>
                    </div>
                    <div class="col-md-2 form-group">
                        <button class="btn btn-primary btn-block" style="height: 60px;">
                            <i class="fa fa-plus-circle mr-1"></i> Agregar
                        </button>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-sm table-hover table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%" class="text-center">Numeral</th>
                            <th width="8%" class="text-center">Ciclo</th>
                            <th width="40%">Descripción del Estándar</th>
                            <th width="12%" class="text-center">Clasificación</th>
                            <th width="10%" class="text-center">Peso (%)</th>
                            <th width="10%" class="text-center">Estado</th>
                            <th width="15%" class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $i)
                        <tr>
                            <td class="align-middle text-center font-weight-bold">{{ $i->numeral ?? '-' }}</td>
                            <td class="align-middle text-center">
                                @if($i->ciclo == 'Planear') <span class="badge badge-primary">Planear</span> @endif
                                @if($i->ciclo == 'Hacer') <span class="badge badge-success">Hacer</span> @endif
                                @if($i->ciclo == 'Verificar') <span class="badge badge-warning">Verificar</span> @endif
                                @if($i->ciclo == 'Actuar') <span class="badge badge-danger">Actuar</span> @endif
                            </td>
                            <td class="align-middle">
                                {{ $i->nombre }}
                                @if($i->modo_verificacion)
                                    <small class="d-block text-muted mt-1"><i class="fa fa-info-circle"></i> {{ \Illuminate\Support\Str::limit($i->modo_verificacion, 50) }}</small>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                <span class="badge badge-info">{{ $i->tipo_plantilla }} Estándares</span>
                            </td>
                            <td class="align-middle text-center font-weight-bold">{{ $i->porcentaje }}%</td>
                            <td class="align-middle text-center">
                                @if($i->activo)
                                    <span class="badge badge-success">Activo</span>
                                @else
                                    <span class="badge badge-secondary">Inactivo</span>
                                @endif
                            </td>
                            <td class="align-middle text-center">
                                <div class="d-flex justify-content-center">
                                    <button type="button" class="btn btn-sm btn-info mr-1" data-toggle="modal" data-target="#editModal{{ $i->id }}" title="Editar">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form action="{{ route('item-estandar.destroy', $i->id) }}" method="POST" class="m-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <div class="modal fade" id="editModal{{ $i->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog modal-lg" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('item-estandar.update', $i->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-header bg-light">
                                                    <h5 class="modal-title font-weight-bold"><i class="fa fa-edit text-primary"></i> Editar Estándar Mínimo</h5>
                                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                                </div>
                                                <div class="modal-body text-left">
                                                    
                                                    <div class="row">
                                                        <div class="col-md-4 form-group">
                                                            <label>Ciclo PHVA</label>
                                                            <select name="ciclo" class="form-control">
                                                                <option value="">Seleccione...</option>
                                                                <option value="Planear" {{ $i->ciclo == 'Planear' ? 'selected' : '' }}>Planear</option>
                                                                <option value="Hacer" {{ $i->ciclo == 'Hacer' ? 'selected' : '' }}>Hacer</option>
                                                                <option value="Verificar" {{ $i->ciclo == 'Verificar' ? 'selected' : '' }}>Verificar</option>
                                                                <option value="Actuar" {{ $i->ciclo == 'Actuar' ? 'selected' : '' }}>Actuar</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-3 form-group">
                                                            <label>Numeral</label>
                                                            <input type="text" name="numeral" class="form-control" value="{{ $i->numeral }}">
                                                        </div>
                                                        <div class="col-md-5 form-group">
                                                            <label>Clasificación (Res. 0312) <span class="text-danger">*</span></label>
                                                            <select name="tipo_plantilla" class="form-control" required>
                                                                <option value="7" {{ $i->tipo_plantilla == 7 ? 'selected' : '' }}>7 Estándares</option>
                                                                <option value="21" {{ $i->tipo_plantilla == 21 ? 'selected' : '' }}>21 Estándares</option>
                                                                <option value="60" {{ $i->tipo_plantilla == 60 ? 'selected' : '' }}>60 Estándares</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="form-group">
                                                        <label>Descripción del Estándar <span class="text-danger">*</span></label>
                                                        <textarea name="nombre" class="form-control" rows="2" required>{{ $i->nombre }}</textarea>
                                                    </div>
                                                    
                                                    <div class="form-group">
                                                        <label>Modo de Verificación</label>
                                                        <textarea name="modo_verificacion" class="form-control" rows="2">{{ $i->modo_verificacion }}</textarea>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-md-6 form-group">
                                                            <label>Peso (%) <span class="text-danger">*</span></label>
                                                            <input type="number" step="0.01" name="porcentaje" class="form-control" value="{{ $i->porcentaje }}" required>
                                                        </div>
                                                        <div class="col-md-6 form-group">
                                                            <label>Estado</label>
                                                            <select name="activo" class="form-control">
                                                                <option value="1" {{ $i->activo == 1 ? 'selected' : '' }}>Activo</option>
                                                                <option value="0" {{ $i->activo == 0 ? 'selected' : '' }}>Inactivo</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="modal-footer bg-light">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save mr-1"></i> Guardar Cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                </td>
                        </tr>
                        @endforeach
                    </tbody>
                     <tfoot class="bg-light">
                            <tr>
                                <td colspan="7">
                                    <div class="d-flex justify-content-between align-items-center px-2">
                                        <span><b>Mostrando:</b> {{  $items->count() }} de {{  $items->total() }} registrados</span>
                                        <div>{{  $items->links('pagination::bootstrap-4') }}</div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection