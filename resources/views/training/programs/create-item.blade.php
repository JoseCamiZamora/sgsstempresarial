@extends('layouts.app')

@section('content')
<div class="container-fluid my-4">
@include('training.partials.nav')
@include('training.partials.guided-breadcrumb', ['step' => 2])

<a class="btn btn-sm btn-outline-secondary mb-2" href="{{ route('training.needs.index') }}">← Regresar a necesidades</a>
<h2>Programar actividad para: {{ $need->title }}</h2>
<p class="text-muted">Se agregará al Programa Anual {{ $program->year }} (v{{ $program->version }}) — estado {{ config('training.program_status_labels.'.$program->status, ucfirst(str_replace('_', ' ', $program->status))) }}.</p>

<form method="POST" action="{{ route('training.programs.items.store', $program) }}" class="card card-body">
    @csrf
    <input type="hidden" name="continue_to_session" value="1">
    <input type="hidden" name="need_ids[]" value="{{ $need->id }}">

    <select name="training_topic_id" class="form-control mb-2">
        <option value="">Tema opcional</option>
        @foreach($topics as $topic)
            <option value="{{ $topic->id }}">{{ $topic->name }}</option>
        @endforeach
    </select>
    <input name="title" value="{{ old('title', $need->title) }}" class="form-control mb-2" placeholder="Actividad" required>
    <textarea name="description" class="form-control mb-2" placeholder="Descripción">{{ old('description', $need->description) }}</textarea>

    <div class="form-row">
        <div class="col">
            <select name="training_type" class="form-control">
                @foreach(config('training.training_types') as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col"><input type="number" min="1" max="12" name="planned_month" value="{{ old('planned_month', now()->month) }}" class="form-control" placeholder="Mes" required></div>
        <div class="col"><input type="date" name="planned_date" class="form-control"></div>
    </div>

    <select name="target_population_type" class="form-control my-2">
        @foreach(config('training.population_types') as $key => $label)
            <option value="{{ $key }}" @selected($need->target_population_type === $key)>{{ $label }}</option>
        @endforeach
    </select>
    <textarea name="target_population_description" class="form-control mb-2" placeholder="Población" required>{{ old('target_population_description', $need->target_population_description) }}</textarea>

    <select name="responsible_employee_id" class="form-control mb-2">
        <option value="">Responsable externo</option>
        @foreach($employees as $employee)
            <option value="{{ $employee->id }}">{{ $employee->nombre_completo }}</option>
        @endforeach
    </select>
    <input name="external_responsible" class="form-control mb-2" placeholder="Responsable externo">

    <select name="planned_modality" class="form-control mb-2">
        @foreach(config('training.modalities') as $key => $label)
            <option value="{{ $key }}">{{ $label }}</option>
        @endforeach
    </select>

    <select name="priority" class="form-control mb-2">
        <option value="low" @selected($need->priority === 'low')>Baja</option>
        <option value="medium" @selected($need->priority === 'medium')>Media</option>
        <option value="high" @selected($need->priority === 'high')>Alta</option>
        <option value="critical" @selected($need->priority === 'critical')>Crítica</option>
    </select>

    <button class="btn btn-primary mt-2">Continuar → Programar sesión</button>
</form>
</div>
@endsection
