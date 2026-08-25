@extends('layouts.app')
@section('content')
<div class="container-fluid px-3 px-xl-4 py-4">@include('transport._nav')
<h1>{{$service->scheduled_start_at->format('H:i')}} — {{$service->route_name_snapshot}}</h1><p>{{$service->service_date->format('d/m/Y')}} · <span class="badge badge-info">{{config('transport.service_statuses.'.$service->status,$service->status)}}</span></p>
<div class="card mb-3"><div class="card-body"><div class="row"><div class="col-md-6"><h5>Programado</h5><p>{{$service->scheduled_start_at->format('d/m/Y H:i')}} — {{$service->scheduled_arrival_at->format('d/m/Y H:i')}}<br>Vehículo: {{$service->vehicle?->plate??'Sin asignar'}}<br>Conductor: {{$service->driver?->display_name??'Sin asignar'}}<br>Monitor: {{$service->monitor?->display_name??'Sin asignar'}}</p></div><div class="col-md-6"><h5>Operación real</h5><p>Salida: {{$service->actual_start_at?->format('d/m/Y H:i')??'Pendiente'}}<br>Llegada: {{$service->actual_arrival_at?->format('d/m/Y H:i')??'Pendiente'}}<br>Vehículo: {{$service->actualVehicle?->plate??'Pendiente'}}<br>Conductor: {{$service->actualDriver?->display_name??'Pendiente'}}<br>Transportados: {{$service->actual_passenger_count}}</p></div></div></div></div>
@if($errors->any())<div class="alert alert-danger"><strong>Revise la información:</strong><ul class="mb-0">@foreach($errors->all() as $e)<li>{{$e}}</li>@endforeach</ul></div>@endif

@if($actions['overdue'])
<div class="alert alert-warning"><strong>Atención:</strong> la hora prevista de salida ya pasó y el servicio continúa programado. Puede completar la preparación y registrar posteriormente la salida real, reprogramar o cancelar.</div>
@endif

<div class="row mb-3">
    <div class="col-lg-7">
        <div class="card h-100">
            <div class="card-header"><strong>Estado del proceso</strong></div>
            <div class="card-body">
                @php
                    $steps = [
                        ['Programado', in_array($service->status, ['scheduled','ready','preoperational','in_progress','arrived','closed'])],
                        ['Pasajeros preparados', $actions['snapshot'] === 'frozen'],
                        ['Servicio listo', in_array($service->status, ['ready','preoperational','in_progress','arrived','closed'])],
                        ['Preoperacional', in_array($service->status, ['preoperational','in_progress','arrived','closed'])],
                        ['Salida', (bool) $service->actual_start_at],
                        ['Llegada', (bool) $service->actual_arrival_at],
                        ['Cierre', $service->status === 'closed'],
                    ];
                @endphp
                <div class="d-flex flex-wrap">
                    @foreach($steps as $index => $step)
                        <div class="mr-4 mb-2"><span class="badge badge-{{$step[1] ? 'success' : 'secondary'}}">{{$step[1] ? '✓' : $index + 1}}</span> {{$step[0]}}</div>
                    @endforeach
                </div>
                <div class="alert alert-info mb-0"><strong>Siguiente paso:</strong> {{$actions['next_action']}}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card h-100">
            <div class="card-header"><strong>Pasajeros del servicio</strong></div>
            <div class="card-body">
                <p class="mb-1">Pasajeros esperados: <strong>{{$actions['expected']}}</strong></p>
                <p class="mb-1">Capacidad del vehículo: <strong>{{$actions['capacity'] ?: 'Sin capacidad definida'}}</strong></p>
                <p class="mb-1">Transportados: <strong>{{$actions['transported']}}</strong> · Ausentes: <strong>{{$actions['absent']}}</strong></p>
                <p class="mb-0">Snapshot: <span class="badge badge-{{$actions['snapshot'] === 'frozen' ? 'success' : ($actions['snapshot'] === 'resolved' ? 'warning' : 'secondary')}}">{{['pending'=>'Pendiente','resolved'=>'En revisión','frozen'=>'Preparado'][$actions['snapshot']] ?? $actions['snapshot']}}</span></p>
            </div>
        </div>
    </div>
</div>

@if(in_array($service->status, ['draft','scheduled']))
<div class="card mb-3 border-primary">
    <div class="card-header bg-primary text-white"><strong>Acciones del servicio — Preparación</strong></div>
    <div class="card-body">
        @if($actions['snapshot'] === 'pending')
            <div class="alert alert-warning">La población de este servicio todavía no ha sido preparada.</div>
            @can('transporte.pasajeros_servicio.gestionar')
            <form method="POST" action="{{route('transport.services.passengers.resolve', $service)}}">@csrf
                <button class="btn btn-primary">Resolver pasajeros</button>
            </form>
            @endcan
        @elseif($actions['snapshot'] === 'resolved')
            <h5>Previsualización de pasajeros</h5>
            <p>Pasajeros encontrados: <strong>{{$actions['expected']}}</strong> · Ocupación prevista: <strong>{{$actions['expected']}} / {{$actions['capacity']}}</strong></p>
            <ol>
                @forelse($service->passengers->where('status', 'expected') as $passenger)
                    <li>{{$passenger->passenger_name_snapshot}}</li>
                @empty
                    <li>No se encontraron pasajeros vigentes para la ruta y fecha del servicio.</li>
                @endforelse
            </ol>
            @can('transporte.pasajeros_servicio.gestionar')
            <div class="d-flex">
                <form method="POST" action="{{route('transport.services.passengers.resolve', $service)}}" class="mr-2">@csrf<button class="btn btn-outline-primary">Actualizar previsualización</button></form>
                <form method="POST" action="{{route('transport.services.passengers.confirm', $service)}}">@csrf<button class="btn btn-success">Confirmar pasajeros</button></form>
            </div>
            @endcan
        @else
            <div class="alert alert-success">La lista quedó confirmada. Pasajeros esperados: {{$actions['expected']}}.</div>
            <h6>Checklist para preparar</h6>
            @foreach([
                'route_active' => 'Ruta activa',
                'vehicle_active' => 'Vehículo asignado y activo',
                'driver_active' => 'Conductor asignado y habilitado',
                'monitor_active' => 'Monitor habilitado o no requerido',
                'capacity_ok' => 'Capacidad válida',
            ] as $key => $label)
                <div>{{$actions['checklist'][$key] ? '✅' : '❌'}} {{$label}}</div>
            @endforeach
            <div>{{$actions['checklist']['conflicts'] ? '❌' : '✅'}} Sin conflicto de recursos</div>
            <div>✅ Pasajeros preparados</div>
        @endif

        <hr>
        @can('transporte.servicios.preparar')
        <form method="POST" action="{{route('transport.services.prepare', $service)}}">@csrf
            <button class="btn btn-success" @disabled(!$actions['can_prepare'])>Preparar servicio</button>
        </form>
        @if(!$actions['can_prepare'])
            <p class="text-danger mt-2 mb-0"><strong>No disponible:</strong> {{implode(' ', $actions['prepare_blockers'])}}</p>
        @endif
        @endcan

        <hr>
        <div class="row">
            @can('transporte.servicios.editar')
            <div class="col-lg-8">
                <details><summary class="btn btn-outline-secondary">Reprogramar</summary>
                    <form method="POST" action="{{route('transport.services.reschedule', $service)}}" class="card card-body mt-2">@csrf @method('PUT')
                        <div class="form-row">
                            <div class="col"><label>Salida</label><input type="datetime-local" name="scheduled_start_at" value="{{$service->scheduled_start_at->format('Y-m-d\TH:i')}}" class="form-control" required></div>
                            <div class="col"><label>Llegada</label><input type="datetime-local" name="scheduled_arrival_at" value="{{$service->scheduled_arrival_at->format('Y-m-d\TH:i')}}" class="form-control" required></div>
                        </div>
                        <div class="form-row mt-2">
                            <div class="col"><select name="planned_vehicle_id" class="form-control" required>@foreach($vehicles as $vehicle)<option value="{{$vehicle->id}}" @selected($service->planned_vehicle_id===$vehicle->id)>{{$vehicle->plate}}</option>@endforeach</select></div>
                            <div class="col"><select name="planned_driver_id" class="form-control" required>@foreach($drivers as $driver)<option value="{{$driver->id}}" @selected($service->planned_driver_id===$driver->id)>{{$driver->display_name}}</option>@endforeach</select></div>
                            <div class="col"><select name="planned_monitor_id" class="form-control"><option value="">Sin monitor</option>@foreach($monitors as $monitor)<option value="{{$monitor->id}}" @selected($service->planned_monitor_id===$monitor->id)>{{$monitor->display_name}}</option>@endforeach</select></div>
                        </div>
                        <input name="reason" minlength="10" class="form-control mt-2" placeholder="Motivo de la reprogramación" required>
                        <button class="btn btn-outline-secondary mt-2">Guardar reprogramación</button>
                    </form>
                </details>
            </div>
            @endcan
            @can('transporte.programacion.cancelar')
            <div class="col-lg-4">
                <form method="POST" action="{{route('transport.services.cancel', $service)}}">@csrf
                    <div class="input-group"><input name="reason" minlength="10" class="form-control" placeholder="Motivo de cancelación" required><div class="input-group-append"><button class="btn btn-danger">Cancelar</button></div></div>
                </form>
            </div>
            @endcan
        </div>
    </div>
</div>
@endif

@if($service->status==='ready')<div class="card mb-3"><div class="card-header">1. Control preoperacional</div><div class="card-body">@forelse($templates as $template)<form method="POST" action="{{route('transport.operation.preoperational',$service)}}">@csrf<input type="hidden" name="template_id" value="{{$template->id}}"><h5>{{$template->name}}</h5>@foreach($template->items as $i=>$item)<div class="form-row align-items-center mb-2"><div class="col-md-5">{{$item->label}} @if($item->is_critical)<span class="badge badge-danger">Crítico</span>@endif</div><div class="col-md-3"><input type="hidden" name="results[{{$i}}][item_id]" value="{{$item->id}}"><select name="results[{{$i}}][result]" class="form-control" required><option value="compliant">Conforme</option><option value="non_compliant">No conforme</option><option value="not_applicable">No aplica</option></select></div><div class="col-md-4"><input name="results[{{$i}}][observation]" class="form-control" placeholder="Observación"></div></div>@endforeach<textarea name="override_reason" class="form-control mb-2" placeholder="Justificación autorizada si existe un crítico no conforme"></textarea><button class="btn btn-success">Completar preoperacional</button></form>@empty<div class="alert alert-warning">Primero cree una plantilla configurable en <a href="{{route('transport.settings.edit')}}">Configuración de transporte</a>.</div>@endforelse</div></div>@endif

@if($service->status==='ready')
<div class="card mb-3"><div class="card-header">2. Registrar salida</div><div class="card-body">
    <button class="btn btn-primary" disabled>Registrar salida</button>
    <p class="text-danger mb-0 mt-2">No disponible hasta completar el control preoperacional.</p>
</div></div>
@endif

@if($service->status==='preoperational')<div class="card mb-3"><div class="card-header">2. Registrar salida</div><div class="card-body"><form method="POST" action="{{route('transport.operation.departure',$service)}}">@csrf<div class="form-row"><div class="col"><input type="number" step="0.01" min="0" name="departure_odometer" class="form-control" placeholder="Kilometraje de salida (opcional según configuración)"></div><div class="col-auto"><button class="btn btn-primary">Registrar salida ahora</button></div></div><small>La hora se toma exclusivamente del servidor.</small></form></div></div>@endif

@if($service->status==='in_progress')
<div class="row"><div class="col-lg-7"><div class="card mb-3"><div class="card-header">3. Pasajeros</div><div class="card-body"><table class="table table-sm"><tr><th>Nombre</th><th>Estado operacional</th></tr>@foreach($service->passengers->where('status','!=','excluded') as $p)<tr><td>{{$p->passenger_name_snapshot}}</td><td><form method="POST" action="{{route('transport.operation.passengers.update',[$service,$p->id])}}">@csrf @method('PUT')<select name="status" class="form-control form-control-sm" onchange="this.form.submit()"><option value="expected" @selected($p->status==='expected')>Esperado</option><option value="boarded" @selected($p->status==='boarded')>Abordó</option><option value="absent" @selected($p->status==='absent')>Ausente</option></select></form></td></tr>@endforeach</table></div></div></div>
<div class="col-lg-5"><div class="card mb-3"><div class="card-header">Cambiar recurso real</div><div class="card-body">@foreach(['vehicle'=>['Vehículo',$vehicles],'driver'=>['Conductor',$drivers],'monitor'=>['Monitor',$monitors]] as $type=>$resource)<form method="POST" action="{{route('transport.operation.resources.change',[$service,$type])}}" class="mb-3">@csrf<label>{{$resource[0]}}</label><div class="form-row"><div class="col"><select name="resource_id" class="form-control">@foreach($resource[1] as $r)<option value="{{$r->id}}">{{$type==='vehicle'?$r->plate:$r->display_name}}</option>@endforeach</select><input name="reason" minlength="10" class="form-control mt-1" placeholder="Motivo" required></div><div class="col-auto"><button class="btn btn-outline-warning">Cambiar</button></div></div></form>@endforeach</div></div></div></div>
<div class="card mb-3"><div class="card-header">4. Registrar llegada</div><div class="card-body"><form method="POST" action="{{route('transport.operation.arrival',$service)}}">@csrf<div class="form-row"><div class="col"><select name="receiver_employee_id" class="form-control"><option value="">Responsable externo</option>@foreach($employees as $e)<option value="{{$e->id}}">{{$e->nombre_completo}}</option>@endforeach</select></div><div class="col"><input name="receiver_name" class="form-control" placeholder="Nombre si es externo"></div><div class="col"><input type="number" step="0.01" min="0" name="arrival_odometer" class="form-control" placeholder="Kilometraje llegada"></div></div><textarea name="observation" class="form-control my-2" placeholder="Observación"></textarea><p>Registro mi firma como evidencia de la recepción/verificación de llegada del servicio indicado.</p><canvas id="arrivalSignature" data-signature-pad data-signature-input="arrival_signature" data-signature-required="0" style="width:100%;height:220px;border:2px solid #94a3b8;touch-action:none;background:#fff"></canvas><input type="hidden" name="signature" id="arrival_signature"><button type="button" data-signature-clear="arrivalSignature" class="btn btn-outline-secondary my-2">Borrar firma</button><button class="btn btn-success btn-block">Confirmar llegada ahora</button></form></div></div>
@endif

@if(in_array($service->status,['preoperational','in_progress','arrived','interrupted']))<div class="card mb-3"><div class="card-header">Novedades operacionales</div><div class="card-body">@if(in_array($service->status,['preoperational','in_progress','arrived']))<form method="POST" enctype="multipart/form-data" action="{{route('transport.operation.issues.store',$service)}}" class="mb-3">@csrf<div class="form-row"><div class="col"><select name="issue_type" class="form-control">@foreach(config('transport.issue_types') as $k=>$v)<option value="{{$k}}">{{$v}}</option>@endforeach</select></div><div class="col"><select name="severity" class="form-control">@foreach(config('transport.issue_severities') as $k=>$v)<option value="{{$k}}">{{$v}}</option>@endforeach</select></div><div class="col"><input type="file" name="evidence" accept="image/png,image/jpeg,application/pdf" class="form-control-file"></div></div><textarea name="description" minlength="10" class="form-control my-2" placeholder="Descripción" required></textarea><button class="btn btn-warning">Registrar novedad</button></form>@endif
@forelse($service->issues as $issue)<div class="border-top py-2"><b>{{config('transport.issue_types.'.$issue->issue_type)}} — {{config('transport.issue_severities.'.$issue->severity)}}</b> · {{$issue->status==='open'?'Abierta':'Resuelta'}}<br>{{$issue->description}}@if($issue->status==='open')<form method="POST" action="{{route('transport.operation.issues.resolve',[$service,$issue])}}">@csrf @method('PUT')<input name="action_taken" minlength="5" required placeholder="Acción tomada"><button class="btn btn-sm btn-success">Resolver</button></form>@endif</div>@empty<p>Sin novedades.</p>@endforelse</div></div>@endif

@if($service->status==='arrived')<form method="POST" action="{{route('transport.operation.close',$service)}}">@csrf<button class="btn btn-success btn-lg">Cerrar servicio</button></form>@endif
@if(in_array($service->status,['preoperational','in_progress']))<form method="POST" action="{{route('transport.operation.interrupt',$service)}}" class="mt-3">@csrf<div class="input-group"><input name="reason" minlength="10" class="form-control" required placeholder="Motivo de interrupción"><div class="input-group-append"><button class="btn btn-danger">Interrumpir servicio</button></div></div></form>@endif
@if($service->arrivalSignature)
    @can('transporte.firmas.ver')
        <a class="btn btn-outline-secondary mt-3" href="{{route('transport.operation.signature',$service)}}">Ver firma de llegada</a>
    @endcan
@endif
@if(in_array($service->status,['arrived','closed','interrupted']))
    @can('transporte.reportes.operacion')
        <a class="btn btn-outline-danger mt-3" href="{{route('transport.reports.operation.service',$service)}}">Informe PDF</a>
    @endcan
@endif
<div class="card mt-3"><div class="card-header">Historial trazable</div><div class="card-body">@forelse($service->changes as $c)<p>{{$c->changed_at->format('d/m/Y H:i')}} — {{$c->change_type}}<br><small>{{$c->reason}}</small></p>@empty<p>Sin cambios de programación o recursos.</p>@endforelse</div></div>
</div><script src="{{asset('js/signature-canvas.js')}}"></script>
@endsection
