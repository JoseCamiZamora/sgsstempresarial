@extends('employee-portal.layout')
@section('content')
<div class="text-center">
    <h1 class="h4 text-success">Firma registrada</h1>
    <p class="text-muted">Su firma quedó registrada correctamente.</p>
    <p class="small text-muted">Código de verificación: <b>{{$event->verification_code}}</b></p>
    <a href="{{route('employee-portal.dashboard')}}" class="btn btn-primary btn-block">Volver a pendientes</a>
</div>
@endsection
