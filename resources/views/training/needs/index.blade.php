@extends('layouts.app')
@section('content')
<div class="container my-4">
    <h2>Necesidades de capacitación</h2>
    @include('training.partials.nav')
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
    <button class="btn btn-primary mb-3" data-toggle="collapse" data-target="#newNeed">Nueva necesidad</button>
    <form id="newNeed" class="collapse card card-body mb-3 {{ $errors->any() ? 'show' : '' }}" method="POST" action="{{ route('training.needs.store') }}">
        @csrf
        <input name="title" value="{{ old('title') }}" class="form-control mb-2" placeholder="¿Qué formación se requiere?" required>
        <textarea name="description" class="form-control mb-2" placeholder="Justificación" required>{{ old('description') }}</textarea>
        <div class="form-row">
            <div class="col"><select name="origin_type" class="form-control">@foreach(config('training.need_origins') as $key => $label)<option value="{{ $key }}" @selected(old('origin_type')===$key)>{{ $label }}</option>@endforeach</select></div>
            <div class="col"><select name="priority" class="form-control">@foreach(['low'=>'Baja','medium'=>'Media','high'=>'Alta','critical'=>'Crítica'] as $key => $label)<option value="{{$key}}" @selected(old('priority')===$key)>{{$label}}</option>@endforeach</select></div>
            <div class="col"><input type="date" name="identified_at" value="{{ old('identified_at', date('Y-m-d')) }}" class="form-control"></div>
        </div>
        <textarea name="origin_description" class="form-control my-2" placeholder="Descripción del origen">{{ old('origin_description') }}</textarea>
        <select name="target_population_type" class="form-control mb-2">@foreach(config('training.population_types') as $key => $label)<option value="{{ $key }}" @selected(old('target_population_type')===$key)>{{ $label }}</option>@endforeach</select>
        <textarea name="target_population_description" class="form-control mb-2" placeholder="Describa la población objetivo" required>{{ old('target_population_description') }}</textarea>
        <label>Riesgos relacionados</label>
        @if($risks->isEmpty())
            <div class="alert alert-warning mb-2">No hay riesgos registrados para esta empresa. Registre primero la Matriz de Riesgos para poder relacionarlos. <a href="{{ route('matriz-riesgos.create') }}" class="alert-link">Registrar riesgo</a>.</div>
        @else
            <select name="risk_ids[]" multiple class="form-control" size="6">@foreach($risks as $risk)<option value="{{ $risk->id }}" @selected(in_array($risk->id, old('risk_ids', [])))>{{ $risk->clasificacion_peligro }} — {{ $risk->descripcion_peligro }} — {{ $risk->proceso }}</option>@endforeach</select>
            <small class="form-text text-muted">Mantenga presionada la tecla Ctrl para seleccionar varios riesgos.</small>
        @endif
        <button class="btn btn-success mt-2">Guardar</button>
    </form>
    <table class="table"><thead><tr><th>Necesidad</th><th>Origen</th><th>Riesgos</th><th>Prioridad</th><th>Estado</th><th></th></tr></thead><tbody>
    @foreach($needs as $need)<tr><td>{{ $need->title }}</td><td>{{ config('training.need_origins.'.$need->origin_type, ucfirst(str_replace('_', ' ', $need->origin_type))) }}</td><td>{{ $need->risks->pluck('clasificacion_peligro')->implode(', ') ?: 'Sin riesgos relacionados' }}</td><td>{{ config('training.priority_labels.'.$need->priority, ucfirst($need->priority)) }}</td><td>{{ config('training.need_status_labels.'.$need->status, ucfirst(str_replace('_', ' ', $need->status))) }}</td><td>@if($need->status === 'identified')<form method="POST" action="{{ route('training.needs.approve', $need) }}">@csrf<button class="btn btn-sm btn-success">Aprobar</button></form>@endif</td></tr>@endforeach
    </tbody></table>
</div>
@endsection
