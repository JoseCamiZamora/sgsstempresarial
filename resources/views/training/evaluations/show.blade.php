@extends('layouts.app')
@section('content')
<div class="container my-4"><h2>{{ $evaluation->title }} <span class="badge badge-info">{{ $evaluation->status }}</span></h2>@include('training.partials.nav')
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('evaluation_links'))<div class="alert alert-warning"><b>Enlaces personales; distribúyalos individualmente.</b>@foreach(session('evaluation_links') as $link)<div>{{ $link['name'] }}: <input class="form-control" value="{{ $link['url'] }}" readonly></div>@endforeach</div>@endif
<p>Sesión: {{ $evaluation->session->title }} · Umbral: {{ $evaluation->passing_score }}% · Intentos: {{ $evaluation->maximum_attempts ?? 'Sin límite configurado' }}</p>
@if($evaluation->status === 'draft')
<form method="POST" action="{{ route('training.evaluations.publish', $evaluation) }}" class="card card-body">@csrf<h5>Seleccione preguntas para congelar esta versión</h5>@foreach($bank as $question)<label><input type="checkbox" name="question_ids[]" value="{{ $question->id }}"> {{ $question->question_text }} ({{ $question->default_points }} puntos)</label>@endforeach<button class="btn btn-success">Publicar evaluación</button></form>
@else
<h4>Preguntas congeladas</h4><ol>@foreach($evaluation->questions as $question)<li>{{ $question->question_text }} — {{ $question->points }} puntos</li>@endforeach</ol>
<h4>Resultados agregados</h4><p>Participantes: {{ $evaluation->session->attendanceEvent?->participants->count() ?? 0 }} · Evaluaron: {{ $evaluation->attempts->where('status','graded')->count() }} · Aprobaron: {{ $evaluation->attempts->where('result','passed')->count() }} · Refuerzo: {{ $evaluation->attempts->where('result','failed')->count() }} · Promedio: {{ round($evaluation->attempts->where('status','graded')->avg('percentage_score') ?? 0, 2) }}%</p>
<table class="table table-sm"><tr><th>Participante</th><th>Intento</th><th>Resultado</th><th>Constancia</th></tr>@foreach($evaluation->attempts->where('status','graded') as $attempt)<tr><td>{{ $attempt->participant->name_snapshot }}</td><td>{{ $attempt->attempt_number }}</td><td>{{ $attempt->percentage_score }}% — {{ $attempt->result === 'passed' ? 'Aprobado' : 'Requiere refuerzo' }}</td><td>@if($attempt->employee_id && $attempt->result === 'passed')<form method="POST" action="{{ route('training.credentials.generate', $evaluation->session) }}">@csrf<input type="hidden" name="employee_id" value="{{ $attempt->employee_id }}"><input type="hidden" name="credential_type" value="internal_approval_certificate"><button class="btn btn-sm btn-outline-success">Constancia interna de aprobación</button></form>@endif</td></tr>@endforeach</table>
@endif
</div>
@endsection
