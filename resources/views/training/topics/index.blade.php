@extends('layouts.app')
@section('content')
<div class="container-fluid my-4">
    <h2>Catálogo de formación</h2>
    @include('training.partials.nav')
    <form class="card card-body mb-3" method="POST" action="{{ route('training.topics.store') }}">
        @csrf
        <div class="form-row">
            <div class="col"><input name="code" class="form-control" placeholder="Código" required></div>
            <div class="col"><input name="name" class="form-control" placeholder="Nombre del tema" required></div>
            <div class="col"><select name="training_type" class="form-control">@foreach(config('training.training_types') as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach</select></div>
        </div>
        <textarea name="description" class="form-control my-2" placeholder="Descripción" required></textarea>
        <textarea name="general_objective" class="form-control mb-2" placeholder="Objetivo general" required></textarea>
        <textarea name="contents" class="form-control mb-2" placeholder="Contenidos"></textarea>
        <input type="number" name="suggested_duration_minutes" class="form-control mb-2" placeholder="Duración sugerida en minutos (opcional)">
        <label><input type="checkbox" name="is_active" value="1" checked> Activo</label>
        <button class="btn btn-primary">Crear tema</button>
    </form>
    <table class="table"><thead><tr><th>Código</th><th>Tema</th><th>Tipo</th><th>Duración sugerida</th><th>Estado</th></tr></thead><tbody>
    @foreach($topics as $topic)
        <tr><td>{{ $topic->code }}</td><td>{{ $topic->name }}</td><td>{{ config('training.training_types.'.$topic->training_type) }}</td><td>{{ $topic->suggested_duration_minutes ?: 'No definida' }}</td><td>{{ $topic->is_active ? 'Activo' : 'Inactivo' }}</td></tr>
    @endforeach
    </tbody></table>
</div>
@endsection
