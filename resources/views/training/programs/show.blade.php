@extends('layouts.app')
@section('content')
<div class="container-fluid my-4">
@include('training.partials.nav')
<a class="btn btn-sm btn-outline-secondary mb-2" href="{{ route('training.programs.index') }}">← Regresar a programas</a><h2>{{ $p->title }} <small>v{{ $p->version }}</small></h2>
<p>Estado: <span class="badge badge-info">{{ config('training.program_status_labels.'.$p->status, ucfirst(str_replace('_', ' ', $p->status))) }}</span> · Actividades: {{ $p->items->count() }} · Pendientes: {{ $pending->count() }}</p>
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
<div class="mb-3"><a class="btn btn-outline-danger" href="{{ route('training.programs.pdf', $p) }}">PDF</a> <a class="btn btn-outline-success" href="{{ route('training.programs.excel', $p) }}">Excel</a>
@if($p->status === 'draft')<form class="d-inline" method="POST" action="{{ route('training.programs.submit', $p) }}">@csrf<button class="btn btn-warning">Enviar a revisión</button></form>
@elseif($p->status === 'in_review')<form class="d-inline" method="POST" action="{{ route('training.programs.approve', $p) }}">@csrf<input name="justification" placeholder="Justificación si hay prioridades pendientes"><button class="btn btn-success">Aprobar</button></form>
@elseif($p->status === 'approved')<form class="d-inline" method="POST" action="{{ route('training.programs.activate', $p) }}">@csrf<button class="btn btn-primary">Activar</button></form>@endif
@if(in_array($p->status, ['approved','active','closed']))<form class="d-inline" method="POST" action="{{ route('training.programs.version', $p) }}">@csrf<button class="btn btn-outline-primary">Crear nueva versión</button></form>@endif</div>
<div class="row"><div class="col-lg-8"><h4>Matriz de planificación</h4>
<table class="table table-sm"><thead><tr><th>Necesidad</th><th>Origen</th><th>Prioridad</th><th>Actividad</th><th>Mes</th><th>Estado</th><th>Ejecución</th></tr></thead><tbody>
@foreach($p->items as $item) @foreach($item->needs as $need)<tr><td>{{ $need->title }}</td><td>{{ config('training.need_origins.'.$need->origin_type) }}</td><td>{{ config('training.priority_labels.'.$need->priority, ucfirst($need->priority)) }}</td><td>{{ $item->title }}</td><td>{{ $item->planned_month }}</td><td>{{ config('training.program_item_status_labels.'.$item->status, ucfirst(str_replace('_', ' ', $item->status))) }}</td><td><a class="btn btn-sm btn-outline-primary" href="{{ route('training.sessions.create-from-item', $item) }}">Programar sesión</a></td></tr>@endforeach @endforeach
@foreach($pending as $need)<tr class="table-warning"><td>{{ $need->title }}</td><td>{{ config('training.need_origins.'.$need->origin_type) }}</td><td>{{ config('training.priority_labels.'.$need->priority, ucfirst($need->priority)) }}</td><td>—</td><td>—</td><td>Pendiente</td><td>—</td></tr>@endforeach
</tbody></table>
@if(in_array($p->status, ['draft','in_review']))
<h4>Agregar actividad planeada</h4><form method="POST" action="{{ route('training.programs.items.store', $p) }}">@csrf
<select name="training_topic_id" class="form-control mb-2"><option value="">Tema opcional</option>@foreach($topics as $topic)<option value="{{ $topic->id }}">{{ $topic->name }}</option>@endforeach</select>
<input name="title" class="form-control mb-2" placeholder="Actividad" required><textarea name="description" class="form-control mb-2" placeholder="Descripción"></textarea>
<div class="form-row"><div class="col"><select name="training_type" class="form-control">@foreach(config('training.training_types') as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></div><div class="col"><input type="number" min="1" max="12" name="planned_month" class="form-control" placeholder="Mes" required></div><div class="col"><input type="date" name="planned_date" class="form-control"></div></div>
<select name="target_population_type" class="form-control my-2">@foreach(config('training.population_types') as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select>
<textarea name="target_population_description" class="form-control mb-2" placeholder="Población" required></textarea>
<select name="responsible_employee_id" class="form-control mb-2"><option value="">Responsable externo</option>@foreach($employees as $employee)<option value="{{ $employee->id }}">{{ $employee->nombre_completo }}</option>@endforeach</select><input name="external_responsible" class="form-control mb-2" placeholder="Responsable externo">
<select name="planned_modality" class="form-control mb-2">@foreach(config('training.modalities') as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select>
<select name="priority" class="form-control mb-2"><option value="medium">Media</option><option value="high">Alta</option><option value="critical">Crítica</option><option value="low">Baja</option></select>
<label>Necesidades atendidas</label><select name="need_ids[]" multiple class="form-control" required>@foreach($needs as $need)<option value="{{ $need->id }}">{{ $need->title }} — {{ config('training.priority_labels.'.$need->priority, ucfirst($need->priority)) }}</option>@endforeach</select><button class="btn btn-primary mt-2">Agregar actividad</button></form>@endif
</div><div class="col-lg-4"><h4>Revisión del programa</h4><form method="POST" action="{{ route('training.programs.reviews.store', $p) }}">@csrf
<input type="date" name="review_date" value="{{ date('Y-m-d') }}" class="form-control mb-2"><input type="hidden" name="review_type" value="annual">
<select name="committee_id" class="form-control mb-2"><option value="">COPASST/Vigía relacionado</option>@foreach($committees as $committee)<option value="{{ $committee->id }}">{{ $committee->name }}</option>@endforeach</select>
<label><input type="hidden" name="copasst_or_vigia_participation" value="0"><input type="checkbox" name="copasst_or_vigia_participation" value="1"> Participó COPASST/Vigía</label>
<select name="senior_management_employee_id" class="form-control mb-2"><option value="">Alta dirección</option>@foreach($employees as $employee)<option value="{{ $employee->id }}">{{ $employee->nombre_completo }} — {{ $employee->cargo }}</option>@endforeach</select>
<label><input type="hidden" name="senior_management_participation" value="0"><input type="checkbox" name="senior_management_participation" value="1"> Participó alta dirección</label>
<textarea name="conclusions" class="form-control mb-2" placeholder="Conclusiones" required></textarea><textarea name="improvement_actions" class="form-control mb-2" placeholder="Acciones de mejora" required></textarea><button class="btn btn-success">Registrar revisión</button></form></div></div>
</div>
@endsection

