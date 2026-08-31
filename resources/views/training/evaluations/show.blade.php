@extends('layouts.app')
@section('content')
<div class="container-fluid my-4"><h2>{{ $evaluation->title }} <span class="badge badge-info">{{ config('training.evaluation_status_labels.'.$evaluation->status,$evaluation->status) }}</span></h2>@include('training.partials.nav')
@if(session('evaluation_links'))<div class="alert alert-warning"><b>Enlaces personales; distribúyalos individualmente.</b>@foreach(session('evaluation_links') as $link)<div>{{ $link['name'] }}: <input class="form-control" value="{{ $link['url'] }}" readonly></div>@endforeach</div>@endif
<p>Sesión: {{ $evaluation->session->title }} · Umbral: {{ $evaluation->passing_score }}% · Intentos: {{ $evaluation->maximum_attempts ?? 'Sin límite configurado' }}</p>
@if($evaluation->status === 'draft')
<form method="POST" action="{{ route('training.evaluations.publish', $evaluation) }}" class="card card-body">@csrf<h5>Seleccione preguntas para congelar esta versión</h5>@foreach($bank as $question)<label><input type="checkbox" name="question_ids[]" value="{{ $question->id }}"> {{ $question->question_text }} ({{ $question->default_points }} puntos)</label>@endforeach<button class="btn btn-success">Publicar evaluación</button></form>
@else
<h4>Preguntas congeladas</h4><ol>@foreach($evaluation->questions as $question)<li>{{ $question->question_text }} — {{ $question->points }} puntos</li>@endforeach</ol>
<h4>Resultados agregados</h4><p>Participantes: {{ $evaluation->session->attendanceEvent?->participants->count() ?? 0 }} · Evaluaron: {{ $evaluation->attempts->where('status','graded')->count() }} · Aprobaron: {{ $evaluation->attempts->where('result','passed')->count() }} · Refuerzo: {{ $evaluation->attempts->where('result','failed')->count() }} · Promedio: {{ round($evaluation->attempts->where('status','graded')->avg('percentage_score') ?? 0, 2) }}%</p>
@if($evaluation->attempts->where('status','pending_review')->count())
<h4>Respuestas con calificación automática fallida</h4>
<p class="text-muted">Estas respuestas de texto libre no se pudieron calificar automáticamente con IA (problema técnico). Califíquelas manualmente para que el intento quede completo.</p>
@foreach($evaluation->attempts->where('status','pending_review') as $attempt)
<div class="card card-body mb-3">
<h5>{{ $attempt->participant->name_snapshot }} — Intento {{ $attempt->attempt_number }}</h5>
@foreach($attempt->answers->whereIn('evaluationQuestion.question_type',['short_answer','long_answer']) as $answer)
<div class="border rounded p-2 mb-2">
<p class="mb-1"><b>{{ $answer->evaluationQuestion->question_text }}</b></p>
<p class="mb-2">Respuesta: {{ $answer->text_answer ?: '(sin respuesta)' }}</p>
@if($answer->is_correct === null)
<form method="POST" action="{{ route('training.evaluations.answers.grade',[$evaluation,$answer]) }}" class="form-row align-items-center">
@csrf
<div class="col-auto"><select name="is_correct" class="form-control form-control-sm" required><option value="">Calificar…</option><option value="1">Correcta</option><option value="0">Incorrecta</option></select></div>
<div class="col-auto"><input type="text" name="evaluator_observations" class="form-control form-control-sm" placeholder="Observación (opcional)" style="min-width:220px"></div>
<div class="col-auto"><button class="btn btn-sm btn-primary">Guardar calificación</button></div>
</form>
@else
<span class="badge {{ $answer->is_correct ? 'badge-success' : 'badge-danger' }}">{{ $answer->is_correct ? 'Calificada correcta' : 'Calificada incorrecta' }}</span>
@endif
</div>
@endforeach
</div>
@endforeach
@endif
<table class="table table-sm"><tr><th>Participante</th><th>Intento</th><th>Resultado</th><th>Constancia</th></tr>@foreach($evaluation->attempts->where('status','graded') as $attempt)<tr><td>{{ $attempt->participant->name_snapshot }}</td><td>{{ $attempt->attempt_number }}</td><td>{{ $attempt->percentage_score }}% — {{ $attempt->result === 'passed' ? 'Aprobado' : 'Requiere refuerzo' }}</td><td>@if($attempt->employee_id && $attempt->result === 'passed')<form method="POST" action="{{ route('training.credentials.generate', $evaluation->session) }}">@csrf<input type="hidden" name="employee_id" value="{{ $attempt->employee_id }}"><input type="hidden" name="credential_type" value="internal_approval_certificate"><button class="btn btn-sm btn-outline-success">Constancia interna de aprobación</button></form>@endif</td></tr>@endforeach</table>
@endif
</div>
@endsection
