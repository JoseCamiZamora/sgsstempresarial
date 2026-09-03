@extends('layouts.app')
@section('content')<div class="container-fluid px-3 px-xl-4 py-4">@include('transport._nav')<h1>Configuración de Transporte</h1>
<div class="card mb-3"><div class="card-body"><form method="POST" action="{{route('transport.settings.update')}}">@csrf @method('PUT')<div class="form-row"><div class="col-md-4"><label>Nombre del servicio</label><input name="service_name" value="{{old('service_name',$setting->service_name?:'Gestión de Transporte')}}" class="form-control" required></div><div class="col-md-4"><label>Sede</label><input name="site_name" value="{{old('site_name',$setting->site_name)}}" class="form-control"></div><div class="col-md-4"><label>Responsable</label><select name="responsible_employee_id" class="form-control"><option value="">Sin asignar</option>@foreach($employees as $e)<option value="{{$e->id}}" @selected(old('responsible_employee_id',$setting->responsible_employee_id)==$e->id)>{{$e->nombre_completo}}</option>@endforeach</select></div></div><div class="form-row mt-3"><div class="col"><label>Inicio jornada</label><input type="time" name="workday_starts_at" value="{{$setting->workday_starts_at}}" class="form-control"></div><div class="col"><label>Fin jornada</label><input type="time" name="workday_ends_at" value="{{$setting->workday_ends_at}}" class="form-control"></div><div class="col"><label>Tolerancia salida (min)</label><input type="number" name="departure_tolerance_minutes" value="{{$setting->departure_tolerance_minutes??10}}" class="form-control" required></div><div class="col"><label>Tolerancia llegada (min)</label><input type="number" name="arrival_tolerance_minutes" value="{{$setting->arrival_tolerance_minutes??10}}" class="form-control" required></div><div class="col"><label>Intervalo recursos (min)</label><input type="number" name="turnaround_minutes" value="{{$setting->turnaround_minutes??0}}" class="form-control" required></div><div class="col"><label>Alerta previa (horas)</label><input type="number" name="upcoming_service_hours" value="{{$setting->upcoming_service_hours??24}}" class="form-control" required></div></div><div class="mt-3"><input type="hidden" name="requires_arrival_signature" value="0"><label class="mr-4"><input type="checkbox" name="requires_arrival_signature" value="1" @checked($setting->requires_arrival_signature)> Exigir firma de llegada</label><input type="hidden" name="requires_departure_odometer" value="0"><label class="mr-4"><input type="checkbox" name="requires_departure_odometer" value="1" @checked($setting->requires_departure_odometer)> Exigir odómetro de salida</label><input type="hidden" name="requires_arrival_odometer" value="0"><label><input type="checkbox" name="requires_arrival_odometer" value="1" @checked($setting->requires_arrival_odometer)> Exigir odómetro de llegada</label></div><div class="mt-3"><label>Días activos</label><div>@foreach(config('transport.weekdays') as $k=>$day)<label class="mr-3"><input type="checkbox" name="active_weekdays[]" value="{{$k}}" @checked(in_array($k,old('active_weekdays',$setting->active_weekdays??[1,2,3,4,5])))> {{$day}}</label>@endforeach</div></div><button class="btn btn-success mt-3">Guardar configuración</button></form></div></div>
<div class="card"><div class="card-header">Plantillas de control preoperacional</div><div class="card-body">
    <p>Los ítems son definidos por la organización; no se imponen requisitos normativos.</p>

    <table class="table table-sm mb-4">
        <thead><tr><th>Nombre</th><th>Ítems</th><th>Bloquea por crítico</th><th>Estado</th><th></th></tr></thead>
        <tbody>
        @forelse($templates as $template)
            <tr>
                <td>{{ $template->name }}</td>
                <td>@foreach($template->items as $item)<span class="badge badge-light border mr-1 mb-1">{{ $item->label }}@if($item->is_critical) <span class="text-danger">●</span>@endif</span>@endforeach</td>
                <td>{{ $template->blocks_on_critical_failure ? 'Sí' : 'No' }}</td>
                <td>{{ $template->status === 'active' ? 'Activa' : 'Inactiva' }}</td>
                <td class="text-nowrap">
                    <button type="button" class="btn btn-sm btn-outline-primary edit-checklist-btn mr-1"
                        data-action="{{ route('transport.settings.checklists.update', $template) }}"
                        data-name="{{ $template->name }}"
                        data-blocks="{{ $template->blocks_on_critical_failure ? 1 : 0 }}"
                        data-items='{{ $template->items->map(fn($i) => ["id"=>$i->id,"label"=>$i->label,"is_critical"=>$i->is_critical])->toJson() }}'>
                        <i class="fa fa-edit"></i> Editar
                    </button>
                    @if($template->status === 'active')
                        <form method="POST" action="{{ route('transport.settings.checklists.deactivate', $template) }}" class="deactivate-checklist-form d-inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Desactivar</button></form>
                    @else
                        <form method="POST" action="{{ route('transport.settings.checklists.activate', $template) }}" class="activate-checklist-form d-inline">@csrf @method('PUT')<button class="btn btn-sm btn-outline-success">Activar</button></form>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="text-muted">Aún no ha creado ninguna plantilla.</td></tr>
        @endforelse
        </tbody>
    </table>

    <h6>Crear nueva plantilla</h6>
    <form method="POST" action="{{ route('transport.settings.checklists.store') }}" id="createChecklistForm">
        @csrf
        <div class="form-row">
            <div class="col-md-6"><input name="name" class="form-control" placeholder="Nombre de la plantilla" required></div>
            <div class="col-md-6 pt-2"><label><input type="checkbox" name="blocks_on_critical_failure" value="1" checked> Bloquear por falla crítica</label></div>
        </div>
        <div id="createItemsWrap" class="mt-3"></div>
        <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="addCreateItemBtn"><i class="fa fa-plus mr-1"></i>Agregar ítem</button>
        <div><button class="btn btn-primary mt-3">Crear plantilla</button></div>
    </form>
</div></div>

<div class="modal fade" id="modalEditarChecklist" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" style="color:white">Editar plantilla</h5>
                <button type="button" class="close text-white" data-dismiss="modal"><span>&times;</span></button>
            </div>
            <form method="POST" id="editChecklistForm">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-row">
                        <div class="col-md-6"><label>Nombre</label><input name="name" class="form-control" required></div>
                        <div class="col-md-6 pt-4"><label><input type="checkbox" name="blocks_on_critical_failure" value="1"> Bloquear por falla crítica</label></div>
                    </div>
                    <p class="text-muted small mt-2">Los ítems ya usados en un control preoperacional no se pueden quitar aquí, solo editar su texto o agregar ítems nuevos.</p>
                    <div id="editItemsWrap"></div>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-2" id="addEditItemBtn"><i class="fa fa-plus mr-1"></i>Agregar ítem</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function checklistItemRow(index, opts) {
    opts = opts || {};
    var idInput = opts.id ? '<input type="hidden" name="items[' + index + '][id]" value="' + opts.id + '">' : '';
    var safeLabel = opts.label ? String(opts.label).replace(/"/g, '&quot;') : '';
    var row = document.createElement('div');
    row.className = 'form-row align-items-center mb-2 checklist-item-row';
    row.innerHTML = idInput +
        '<div class="col"><input name="items[' + index + '][label]" class="form-control" placeholder="Ítem" value="' + safeLabel + '"' + (opts.required ? ' required' : '') + '></div>' +
        '<div class="col-auto"><label><input type="checkbox" name="items[' + index + '][is_critical]" value="1"' + (opts.critical ? ' checked' : '') + '> Crítico</label></div>' +
        (opts.removable ? '<div class="col-auto"><button type="button" class="btn btn-sm btn-link text-danger remove-checklist-item">Quitar</button></div>' : '');
    if (opts.removable) {
        row.querySelector('.remove-checklist-item').addEventListener('click', function () { row.remove(); });
    }
    return row;
}

(function () {
    var createWrap = document.getElementById('createItemsWrap');
    var createIndex = 0;
    function addCreateItem(required) {
        createWrap.appendChild(checklistItemRow(createIndex++, { required: required, removable: createIndex > 1 }));
    }
    addCreateItem(true);
    document.getElementById('addCreateItemBtn').addEventListener('click', function () { addCreateItem(false); });
})();

var editWrap = document.getElementById('editItemsWrap');
var editIndex = 0;
document.getElementById('addEditItemBtn').addEventListener('click', function () {
    editWrap.appendChild(checklistItemRow(editIndex++, { removable: true }));
});

document.querySelectorAll('.edit-checklist-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
        var form = document.getElementById('editChecklistForm');
        form.action = btn.dataset.action;
        form.elements['name'].value = btn.dataset.name || '';
        form.elements['blocks_on_critical_failure'].checked = btn.dataset.blocks === '1';
        editWrap.innerHTML = '';
        editIndex = 0;
        var items = JSON.parse(btn.dataset.items || '[]');
        items.forEach(function (item) {
            editWrap.appendChild(checklistItemRow(editIndex++, { id: item.id, label: item.label, critical: item.is_critical, removable: false }));
        });
        $('#modalEditarChecklist').modal('show');
    });
});

function confirmAndSubmit(form, title, text, icon, confirmText) {
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (typeof Swal === 'undefined') {
            if (window.confirm(title)) form.submit();
            return;
        }
        Swal.fire({ title: title, text: text, icon: icon, showCancelButton: true, confirmButtonText: confirmText, cancelButtonText: 'Cancelar' })
            .then(function (r) { if (r.isConfirmed) form.submit(); });
    });
}

document.querySelectorAll('.deactivate-checklist-form').forEach(function (form) {
    confirmAndSubmit(form, '¿Desactivar esta plantilla?', 'Los servicios que ya la usaron conservan su historial.', 'warning', 'Desactivar');
});
document.querySelectorAll('.activate-checklist-form').forEach(function (form) {
    confirmAndSubmit(form, '¿Activar esta plantilla?', 'Volverá a estar disponible para nuevos controles preoperacionales.', 'question', 'Activar');
});
</script>
@endsection
