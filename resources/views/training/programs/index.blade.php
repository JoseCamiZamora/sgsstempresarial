@extends('layouts.app')
@section('content')
<div class="container my-4">
    <h2>Programas Anuales de Capacitación</h2>
    @include('training.partials.nav')
    <form class="card card-body mb-3" method="POST" action="{{ route('training.programs.store') }}">
        @csrf
        <div class="form-row"><div class="col"><input type="number" name="year" value="{{ date('Y') }}" class="form-control" required></div><div class="col"><input name="title" value="Programa Anual de Capacitación SG-SST {{ date('Y') }}" class="form-control" required></div></div>
        <textarea name="general_objective" class="form-control my-2" placeholder="Objetivo general" required></textarea>
        <textarea name="scope" class="form-control mb-2" placeholder="Alcance" required></textarea>
        <div class="form-row"><div class="col"><input type="date" name="starts_at" value="{{ date('Y') }}-01-01" class="form-control"></div><div class="col"><input type="date" name="ends_at" value="{{ date('Y') }}-12-31" class="form-control"></div></div>
        <button class="btn btn-primary mt-2">Crear borrador</button>
    </form>
    <table class="table"><thead><tr><th>Año</th><th>Versión</th><th>Título</th><th>Estado</th><th></th></tr></thead><tbody>
    @foreach($programs as $program)
        <tr><td>{{ $program->year }}</td><td>{{ $program->version }}</td><td>{{ $program->title }}</td><td>{{ $program->status }}</td><td><a href="{{ route('training.programs.show', $program) }}">Gestionar</a></td></tr>
    @endforeach
    </tbody></table>
</div>
@endsection
