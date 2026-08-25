@extends('attendance.public.layout')
@section('content')
<h1 class="h3">Confirme su asistencia</h1><div class="alert alert-info"><b>{{$participant->name_snapshot}}</b><br>{{$participant->role_snapshot}}</div>
@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{$e}}</div>@endforeach</div>@endif
<form method="POST" action="{{route('attendance.public.confirm',[$event,$token])}}">@csrf<label>Firme dentro del recuadro</label><canvas id="attendanceSignature" data-signature-pad data-signature-input="signature" data-signature-required="1"></canvas><input type="hidden" name="signature" id="signature"><button type="button" data-signature-clear="attendanceSignature" class="btn btn-outline-secondary my-2">Borrar firma</button><div class="custom-control custom-checkbox my-3"><input type="checkbox" class="custom-control-input" id="acknowledged" name="acknowledged" value="1" required><label class="custom-control-label" for="acknowledged">{{config('attendance.consent_text')}}</label></div><button class="btn btn-success btn-block">Confirmar asistencia</button></form>
<script src="{{asset('js/signature-canvas.js')}}"></script>
@endsection
