@extends('layouts.app')
@section('content')<div class="container-fluid px-3 px-xl-4 py-4">@include('transport._nav')<h1>Vehículos</h1>
@can('transporte.vehiculos.gestionar')<div class="card mb-4"><div class="card-header">Registrar vehículo</div><div class="card-body"><form method="POST" action="{{route('transport.vehiculos.store')}}">@csrf<div class="form-row"><div class="col-md-2"><label>Placa</label><input name="plate" value="{{old('plate')}}" class="form-control" required></div><div class="col-md-2"><label>Código interno</label><input name="internal_code" value="{{old('internal_code')}}" class="form-control"></div><div class="col-md-2"><label>Tipo</label><select name="vehicle_type" class="form-control">@foreach(config('transport.vehicle_types') as $k=>$v)<option value="{{$k}}">{{$v}}</option>@endforeach</select></div><div class="col-md-2"><label>Marca</label><input name="brand" class="form-control"></div><div class="col-md-2"><label>Modelo</label><input name="model" class="form-control"></div><div class="col-md-2"><label>Año</label><input type="number" name="year" class="form-control"></div></div><div class="form-row mt-2"><div class="col-md-2"><label>Capacidad</label><input type="number" min="1" name="capacity" class="form-control" required></div><div class="col-md-2"><label>Propiedad</label><select name="owner_type" class="form-control">@foreach(config('transport.owner_types') as $k=>$v)<option value="{{$k}}">{{$v}}</option>@endforeach</select></div><div class="col-md-3"><label>Propietario/proveedor</label><input name="owner_name" class="form-control"></div><div class="col-md-2"><label>Estado</label><select name="status" class="form-control"><option value="active">Activo</option><option value="inactive">Inactivo</option></select></div><div class="col-md-3"><label>Observaciones</label><input name="notes" class="form-control"></div></div><button class="btn btn-success mt-3">Guardar vehículo</button></form></div></div>@endcan

@can('transporte.vehiculos.gestionar')
<div class="modal fade" id="modalEditarVehiculo" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" style="color:white">Editar vehículo</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="editVehicleForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-2"><label>Placa</label><input name="plate" class="form-control" required></div>
                        <div class="col-md-2"><label>Código interno</label><input name="internal_code" class="form-control"></div>
                        <div class="col-md-2"><label>Tipo</label><select name="vehicle_type" class="form-control">@foreach(config('transport.vehicle_types') as $k=>$v)<option value="{{$k}}">{{$v}}</option>@endforeach</select></div>
                        <div class="col-md-2"><label>Marca</label><input name="brand" class="form-control"></div>
                        <div class="col-md-2"><label>Modelo</label><input name="model" class="form-control"></div>
                        <div class="col-md-2"><label>Año</label><input type="number" name="year" class="form-control"></div>
                    </div>
                    <div class="form-row mt-2">
                        <div class="col-md-2"><label>Capacidad</label><input type="number" min="1" name="capacity" class="form-control" required></div>
                        <div class="col-md-2"><label>Propiedad</label><select name="owner_type" class="form-control">@foreach(config('transport.owner_types') as $k=>$v)<option value="{{$k}}">{{$v}}</option>@endforeach</select></div>
                        <div class="col-md-3"><label>Propietario/proveedor</label><input name="owner_name" class="form-control"></div>
                        <div class="col-md-2"><label>Estado</label><select name="status" class="form-control"><option value="active">Activo</option><option value="inactive">Inactivo</option></select></div>
                        <div class="col-md-3"><label>Observaciones</label><input name="notes" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Placa</th><th>Código</th><th>Tipo</th><th>Marca/modelo</th><th>Capacidad</th><th>Estado</th><th></th></tr></thead><tbody>@forelse($vehicles as $v)<tr><td>{{$v->plate}}</td><td>{{$v->internal_code?:'—'}}</td><td>{{config('transport.vehicle_types.'.$v->vehicle_type,$v->vehicle_type)}}</td><td>{{$v->brand}} {{$v->model}}</td><td>{{$v->capacity}}</td><td>{{config('transport.statuses.'.$v->status,$v->status)}}</td><td class="text-nowrap">@can('transporte.vehiculos.gestionar')<button type="button" class="btn btn-sm btn-outline-primary edit-vehicle-btn mr-1" data-action="{{route('transport.vehiculos.update',$v)}}" data-plate="{{$v->plate}}" data-internal_code="{{$v->internal_code}}" data-vehicle_type="{{$v->vehicle_type}}" data-brand="{{$v->brand}}" data-model="{{$v->model}}" data-year="{{$v->year}}" data-capacity="{{$v->capacity}}" data-owner_type="{{$v->owner_type}}" data-owner_name="{{$v->owner_name}}" data-status="{{$v->status}}" data-notes="{{$v->notes}}"><i class="fa fa-edit"></i> Editar</button>@if($v->status==='active')<form method="POST" action="{{route('transport.vehiculos.destroy',$v)}}" class="deactivate-form d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Desactivar</button></form>@else<form method="POST" action="{{route('transport.vehiculos.activate',$v)}}" class="activate-form d-inline">@csrf @method('PUT')<button class="btn btn-sm btn-outline-success">Activar</button></form>@endif @endcan</td></tr>@empty<tr><td colspan="7">No hay vehículos registrados.</td></tr>@endforelse</tbody></table></div>{{$vehicles->links()}}</div>
<script>
document.querySelectorAll('form.deactivate-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (typeof Swal === 'undefined') {
            if (window.confirm('¿Desactivar este vehículo? Podrá reactivarlo más adelante; su historial no se pierde.')) form.submit();
            return;
        }
        Swal.fire({
            title: '¿Desactivar este vehículo?',
            text: 'Podrá reactivarlo más adelante; su historial no se pierde.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Desactivar',
            cancelButtonText: 'Cancelar',
        }).then(function (r) {
            if (r.isConfirmed) form.submit();
        }).catch(function (err) {
            console.error('Swal error, usando confirm() de respaldo', err);
            if (window.confirm('¿Desactivar este vehículo?')) form.submit();
        });
    });
});

document.querySelectorAll('form.activate-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (typeof Swal === 'undefined') {
            if (window.confirm('¿Activar este vehículo?')) form.submit();
            return;
        }
        Swal.fire({
            title: '¿Activar este vehículo?',
            text: 'Volverá a estar disponible para asignarse a rutas y servicios.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Activar',
            cancelButtonText: 'Cancelar',
        }).then(function (r) {
            if (r.isConfirmed) form.submit();
        }).catch(function (err) {
            console.error('Swal error, usando confirm() de respaldo', err);
            if (window.confirm('¿Activar este vehículo?')) form.submit();
        });
    });
});

document.querySelectorAll('.edit-vehicle-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var form = document.getElementById('editVehicleForm');
        form.action = btn.dataset.action;
        form.plate.value = btn.dataset.plate || '';
        form.internal_code.value = btn.dataset.internal_code || '';
        form.vehicle_type.value = btn.dataset.vehicle_type || '';
        form.brand.value = btn.dataset.brand || '';
        form.model.value = btn.dataset.model || '';
        form.year.value = btn.dataset.year || '';
        form.capacity.value = btn.dataset.capacity || '';
        form.owner_type.value = btn.dataset.owner_type || '';
        form.owner_name.value = btn.dataset.owner_name || '';
        form.status.value = btn.dataset.status || 'active';
        form.notes.value = btn.dataset.notes || '';
        $('#modalEditarVehiculo').modal('show');
    });
});
</script>
@endsection
