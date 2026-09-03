@extends('layouts.app')
@section('content')<div class="container-fluid px-3 px-xl-4 py-4">@include('transport._nav')
<div class="d-flex flex-wrap align-items-center justify-content-between mb-2"><h1 class="mb-0">Pasajeros</h1>@can('transporte.pasajeros.gestionar')<button type="button" class="btn btn-success shadow-sm" data-toggle="modal" data-target="#modalCargaMasivaPasajeros"><i class="fa fa-file-excel mr-2"></i>Cargue Masivo</button>@endcan</div>
<div class="alert alert-info">Información protegida. Esta pantalla requiere autenticación y permiso empresarial.</div>

@can('transporte.pasajeros.gestionar')
<div class="modal fade" id="modalCargaMasivaPasajeros" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" style="color:white"><i class="fa fa-file-excel mr-2"></i>Cargue masivo de pasajeros</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form action="{{route('transport.pasajeros.import.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">Suba un archivo Excel (.xlsx) con un pasajero por fila. El tipo y el estado
                        aceptan el texto tal como aparece en la plantilla (Estudiante, Empleado, Beneficiario, Otro / Activo,
                        Inactivo); si los deja en blanco se asume Estudiante y Activo. Se validará que la identificación no
                        esté duplicada.</p>
                    <a href="{{route('transport.pasajeros.import.template')}}" class="btn btn-outline-secondary btn-sm mb-3">
                        <i class="fa fa-download mr-1"></i> Descargar plantilla de ejemplo
                    </a>
                    <div class="form-group">
                        <label class="font-weight-bold">Archivo Excel <span class="text-danger">*</span></label>
                        <input type="file" name="archivo_excel" class="form-control-file" accept=".xlsx,.xls" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-upload mr-1"></i> Cargar archivo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

@can('transporte.pasajeros.gestionar')<div class="card mb-4"><div class="card-header">Registrar pasajero</div><div class="card-body"><form method="POST" action="{{route('transport.pasajeros.store')}}">@csrf<div class="form-row"><div class="col-md-2"><label>Tipo</label><select name="passenger_type" class="form-control">@foreach(config('transport.passenger_types') as $k=>$v)<option value="{{$k}}">{{$v}}</option>@endforeach</select></div><div class="col-md-3"><label>Nombre</label><input name="name" class="form-control" required></div><div class="col-md-2"><label>Identificación</label><input name="identification" class="form-control"></div><div class="col-md-2"><label>Grado/grupo</label><input name="grade_group" class="form-control"></div><div class="col-md-3"><label>Responsable</label><input name="responsible_name" class="form-control"></div></div><div class="form-row mt-2"><div class="col-md-3"><label>Teléfono responsable</label><input name="responsible_phone" class="form-control"></div><div class="col-md-2"><label>Estado</label><select name="status" class="form-control"><option value="active">Activo</option><option value="inactive">Inactivo</option></select></div></div><button class="btn btn-success mt-3">Guardar pasajero</button></form></div></div>@endcan

@can('transporte.pasajeros.gestionar')
<div class="modal fade" id="modalEditarPasajero" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" style="color:white">Editar pasajero</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="editPassengerForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-2"><label>Tipo</label><select name="passenger_type" class="form-control">@foreach(config('transport.passenger_types') as $k=>$v)<option value="{{$k}}">{{$v}}</option>@endforeach</select></div>
                        <div class="col-md-3"><label>Nombre</label><input name="name" class="form-control" required></div>
                        <div class="col-md-2"><label>Identificación</label><input name="identification" class="form-control"></div>
                        <div class="col-md-2"><label>Grado/grupo</label><input name="grade_group" class="form-control"></div>
                        <div class="col-md-3"><label>Responsable</label><input name="responsible_name" class="form-control"></div>
                    </div>
                    <div class="form-row mt-2">
                        <div class="col-md-3"><label>Teléfono responsable</label><input name="responsible_phone" class="form-control"></div>
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

<div class="table-responsive"><table class="table table-striped"><thead><tr><th>Nombre</th><th>Tipo</th><th>Grado/grupo</th><th>Responsable</th><th>Estado</th><th></th></tr></thead><tbody>@forelse($passengers as $p)<tr><td>{{$p->name}}</td><td>{{config('transport.passenger_types.'.$p->passenger_type)}}</td><td>{{$p->grade_group?:'—'}}</td><td>{{$p->responsible_name?:'—'}}</td><td>{{config('transport.statuses.'.$p->status)}}</td><td class="text-nowrap">@can('transporte.pasajeros.gestionar')<button type="button" class="btn btn-sm btn-outline-primary edit-passenger-btn mr-1" data-action="{{route('transport.pasajeros.update',$p)}}" data-passenger_type="{{$p->passenger_type}}" data-name="{{$p->name}}" data-identification="{{$p->identification}}" data-grade_group="{{$p->grade_group}}" data-responsible_name="{{$p->responsible_name}}" data-responsible_phone="{{$p->responsible_phone}}" data-status="{{$p->status}}"><i class="fa fa-edit"></i> Editar</button>@if($p->status==='active')<form method="POST" action="{{route('transport.pasajeros.destroy',$p)}}" class="deactivate-form d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Desactivar</button></form>@else<form method="POST" action="{{route('transport.pasajeros.activate',$p)}}" class="activate-form d-inline">@csrf @method('PUT')<button class="btn btn-sm btn-outline-success">Activar</button></form>@endif @endcan</td></tr>@empty<tr><td colspan="6">No hay pasajeros registrados.</td></tr>@endforelse</tbody></table></div>{{$passengers->links()}}</div>
<script>
document.querySelectorAll('form.deactivate-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (typeof Swal === 'undefined') {
            if (window.confirm('¿Desactivar este pasajero? Podrá reactivarlo más adelante; su historial no se pierde.')) form.submit();
            return;
        }
        Swal.fire({
            title: '¿Desactivar este pasajero?',
            text: 'Podrá reactivarlo más adelante; su historial no se pierde.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Desactivar',
            cancelButtonText: 'Cancelar',
        }).then(function (r) {
            if (r.isConfirmed) form.submit();
        }).catch(function (err) {
            console.error('Swal error, usando confirm() de respaldo', err);
            if (window.confirm('¿Desactivar este pasajero?')) form.submit();
        });
    });
});

document.querySelectorAll('form.activate-form').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (typeof Swal === 'undefined') {
            if (window.confirm('¿Activar este pasajero?')) form.submit();
            return;
        }
        Swal.fire({
            title: '¿Activar este pasajero?',
            text: 'Volverá a estar disponible para asignarse a rutas y servicios.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Activar',
            cancelButtonText: 'Cancelar',
        }).then(function (r) {
            if (r.isConfirmed) form.submit();
        }).catch(function (err) {
            console.error('Swal error, usando confirm() de respaldo', err);
            if (window.confirm('¿Activar este pasajero?')) form.submit();
        });
    });
});

document.querySelectorAll('.edit-passenger-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var form = document.getElementById('editPassengerForm');
        form.action = btn.dataset.action;
        form.passenger_type.value = btn.dataset.passenger_type || 'student';
        form.name.value = btn.dataset.name || '';
        form.identification.value = btn.dataset.identification || '';
        form.grade_group.value = btn.dataset.grade_group || '';
        form.responsible_name.value = btn.dataset.responsible_name || '';
        form.responsible_phone.value = btn.dataset.responsible_phone || '';
        form.status.value = btn.dataset.status || 'active';
        $('#modalEditarPasajero').modal('show');
    });
});
</script>
@endsection
