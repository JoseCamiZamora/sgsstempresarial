@extends('layouts.app')
@section('content')<div class="container-fluid px-3 px-xl-4 py-4">@include('transport._nav')<h1>Conductores y monitores</h1>
@can('transporte.conductores.gestionar')<div class="card mb-4"><div class="card-header">Registrar perfil operacional</div><div class="card-body"><form method="POST" action="{{route('transport.personal.store')}}">@csrf<div class="form-row"><div class="col-md-2"><label>Vinculación</label><select name="person_type" id="person_type" class="form-control"><option value="employee">Empleado</option><option value="external">Externo</option></select></div><div class="col-md-4"><label>Empleado</label><select name="employee_id" class="form-control"><option value="">Seleccione</option>@foreach($employees as $e)<option value="{{$e->id}}">{{$e->nombre_completo}} — {{$e->cargo}}</option>@endforeach</select></div><div class="col-md-3"><label>Nombre externo</label><input name="name" class="form-control"></div><div class="col-md-3"><label>Documento externo</label><input name="document_number" class="form-control"></div></div><div class="form-row mt-2"><div class="col-md-2"><label>Tipo documento</label><input name="document_type" class="form-control"></div><div class="col-md-2"><label>Teléfono</label><input name="phone" class="form-control"></div><div class="col-md-3"><label>Proveedor</label><input name="provider" class="form-control"></div><div class="col-md-3 pt-4"><label class="mr-3"><input type="checkbox" name="is_driver" value="1"> Conductor</label><label><input type="checkbox" name="is_monitor" value="1"> Monitor</label></div><div class="col-md-2"><label>Estado</label><select name="status" class="form-control"><option value="active">Activo</option><option value="inactive">Inactivo</option></select></div></div><button class="btn btn-success mt-3">Guardar perfil</button></form></div></div>@endcan

@can('transporte.conductores.gestionar')
<div class="modal fade" id="modalEditarPersona" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" style="color:white">Editar perfil operacional</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="editPersonForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-2"><label>Vinculación</label><select name="person_type" class="form-control"><option value="employee">Empleado</option><option value="external">Externo</option></select></div>
                        <div class="col-md-4"><label>Empleado</label><select name="employee_id" class="form-control"><option value="">Seleccione</option>@foreach($employees as $e)<option value="{{$e->id}}">{{$e->nombre_completo}} — {{$e->cargo}}</option>@endforeach</select></div>
                        <div class="col-md-3"><label>Nombre externo</label><input name="name" class="form-control"></div>
                        <div class="col-md-3"><label>Documento externo</label><input name="document_number" class="form-control"></div>
                    </div>
                    <div class="form-row mt-2">
                        <div class="col-md-2"><label>Tipo documento</label><input name="document_type" class="form-control"></div>
                        <div class="col-md-2"><label>Teléfono</label><input name="phone" class="form-control"></div>
                        <div class="col-md-3"><label>Proveedor</label><input name="provider" class="form-control"></div>
                        <div class="col-md-3 pt-4"><label class="mr-3"><input type="checkbox" name="is_driver" value="1"> Conductor</label><label><input type="checkbox" name="is_monitor" value="1"> Monitor</label></div>
                        <div class="col-md-2"><label>Estado</label><select name="status" class="form-control"><option value="active">Activo</option><option value="inactive">Inactivo</option></select></div>
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

<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Nombre</th><th>Vinculación</th><th>Roles</th><th>Proveedor</th><th>Estado</th><th></th></tr></thead><tbody>@forelse($people as $p)<tr><td>{{$p->display_name}}</td><td>{{config('transport.person_types.'.$p->person_type)}}</td><td>{{$p->is_driver?'Conductor':''}}{{$p->is_driver&&$p->is_monitor?' / ':''}}{{$p->is_monitor?'Monitor':''}}</td><td>{{$p->provider?:'—'}}</td><td>{{config('transport.statuses.'.$p->status)}}</td><td class="text-nowrap">@can('transporte.conductores.gestionar')<button type="button" class="btn btn-sm btn-outline-primary edit-person-btn mr-1" data-action="{{route('transport.personal.update',$p)}}" data-person_type="{{$p->person_type}}" data-employee_id="{{$p->employee_id}}" data-name="{{$p->name}}" data-document_type="{{$p->document_type}}" data-document_number="{{$p->document_number}}" data-phone="{{$p->phone}}" data-provider="{{$p->provider}}" data-is_driver="{{$p->is_driver?1:0}}" data-is_monitor="{{$p->is_monitor?1:0}}" data-status="{{$p->status}}"><i class="fa fa-edit"></i> Editar</button>@if($p->status==='active')<form method="POST" action="{{route('transport.personal.destroy',$p)}}" class="deactivate-form d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Desactivar</button></form>@else<form method="POST" action="{{route('transport.personal.activate',$p)}}" class="activate-form d-inline">@csrf @method('PUT')<button class="btn btn-sm btn-outline-success">Activar</button></form>@endif @endcan</td></tr>@empty<tr><td colspan="6">No hay personal registrado.</td></tr>@endforelse</tbody></table></div>{{$people->links()}}</div>
<script>
document.querySelectorAll('form.deactivate-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (typeof Swal === 'undefined') {
            if (window.confirm('¿Desactivar este perfil? Podrá reactivarse más adelante; su historial no se pierde.')) form.submit();
            return;
        }
        Swal.fire({
            title: '¿Desactivar este perfil?',
            text: 'El conductor o monitor podrá reactivarse más adelante; su historial no se pierde.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Desactivar',
            cancelButtonText: 'Cancelar',
        }).then(function (r) {
            if (r.isConfirmed) form.submit();
        }).catch(function (err) {
            console.error('Swal error, usando confirm() de respaldo', err);
            if (window.confirm('¿Desactivar este perfil?')) form.submit();
        });
    });
});

document.querySelectorAll('form.activate-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (typeof Swal === 'undefined') {
            if (window.confirm('¿Activar este perfil?')) form.submit();
            return;
        }
        Swal.fire({
            title: '¿Activar este perfil?',
            text: 'El conductor o monitor volverá a estar disponible para asignarse.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Activar',
            cancelButtonText: 'Cancelar',
        }).then(function (r) {
            if (r.isConfirmed) form.submit();
        }).catch(function (err) {
            console.error('Swal error, usando confirm() de respaldo', err);
            if (window.confirm('¿Activar este perfil?')) form.submit();
        });
    });
});

document.querySelectorAll('.edit-person-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var form = document.getElementById('editPersonForm');
        form.action = btn.dataset.action;
        form.person_type.value = btn.dataset.person_type || 'employee';
        form.employee_id.value = btn.dataset.employee_id || '';
        form.name.value = btn.dataset.name || '';
        form.document_type.value = btn.dataset.document_type || '';
        form.document_number.value = btn.dataset.document_number || '';
        form.phone.value = btn.dataset.phone || '';
        form.provider.value = btn.dataset.provider || '';
        form.is_driver.checked = btn.dataset.is_driver === '1';
        form.is_monitor.checked = btn.dataset.is_monitor === '1';
        form.status.value = btn.dataset.status || 'active';
        $('#modalEditarPersona').modal('show');
    });
});
</script>
@endsection
