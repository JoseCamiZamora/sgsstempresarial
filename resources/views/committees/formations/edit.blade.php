@extends('layouts.app')
@section('content')
<div class="container my-4">
 <a href="{{route('committees.formations.show',$formation)}}" class="text-secondary">← Volver al proceso</a>
 <div class="card shadow border-0 mt-3"><div class="card-header bg-white"><h4 class="mb-0">Editar {{$formation->committee->name}}</h4></div><div class="card-body">
 @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{$error}}</li>@endforeach</ul></div>@endif
 @if(session('error'))<div class="alert alert-danger">{{session('error')}}</div>@endif
 <form method="POST" action="{{route('committees.formations.update',$formation)}}">@csrf @method('PUT')
  <div class="row">
   <div class="col-12 form-group"><label>Título</label><input name="title" class="form-control" value="{{old('title',$formation->title)}}" required></div>
   <div class="col-12 form-group"><label>Descripción</label><textarea name="description" class="form-control">{{old('description',$formation->description)}}</textarea></div>
   <div class="col-md-6 form-group"><label>Inicio del período</label><input type="date" name="start_date" class="form-control" value="{{old('start_date',$formation->period->start_date->format('Y-m-d'))}}" required></div>
   <div class="col-md-6 form-group"><label>Fin del período</label><input type="date" name="end_date" class="form-control" value="{{old('end_date',$formation->period->end_date->format('Y-m-d'))}}" required></div>
   <div class="col-md-6 form-group"><label>Apertura convocatoria</label><input type="date" name="call_start_date" class="form-control" value="{{old('call_start_date',$formation->call_start_date->format('Y-m-d'))}}" required></div>
   <div class="col-md-6 form-group"><label>Cierre convocatoria</label><input type="date" name="call_end_date" class="form-control" value="{{old('call_end_date',$formation->call_end_date->format('Y-m-d'))}}" required></div>
   <div class="col-md-6 form-group"><label>Inicio inscripción</label><input type="datetime-local" name="candidate_registration_start" class="form-control" value="{{old('candidate_registration_start',$formation->candidate_registration_start->format('Y-m-d\TH:i'))}}" required></div>
   <div class="col-md-6 form-group"><label>Cierre inscripción</label><input type="datetime-local" name="candidate_registration_end" class="form-control" value="{{old('candidate_registration_end',$formation->candidate_registration_end->format('Y-m-d\TH:i'))}}" required></div>
   <div class="col-md-6 form-group"><label>Apertura elección</label><input type="datetime-local" name="election_start_at" class="form-control" value="{{old('election_start_at',$formation->election_start_at->format('Y-m-d\TH:i'))}}" required></div>
   <div class="col-md-6 form-group"><label>Cierre elección</label><input type="datetime-local" name="election_end_at" class="form-control" value="{{old('election_end_at',$formation->election_end_at->format('Y-m-d\TH:i'))}}" required></div>
   <div class="col-md-6 form-group"><label>Requisitos</label><textarea name="requirements" class="form-control">{{old('requirements',$formation->requirements)}}</textarea></div>
   <div class="col-md-6 form-group"><label>Observaciones</label><textarea name="notes" class="form-control">{{old('notes',$formation->notes)}}</textarea></div>
  </div><button class="btn btn-primary">Guardar cambios</button>
 </form></div></div>
</div>
@endsection
