@extends('employee-portal.layout')
@section('content')
<h1 class="h3">Portal de firmas</h1>
<p class="text-muted">Ingrese su número de cédula y el código de acceso entregado por su empresa para ver y firmar sus documentos pendientes.</p>
@if(session('success'))<div class="alert alert-success">{{session('success')}}</div>@endif
@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{$e}}</div>@endforeach</div>@endif
<form method="POST" action="{{route('employee-portal.login.submit')}}">
    @csrf
    <div class="form-group">
        <label for="cedula">Cédula</label>
        <input id="cedula" name="cedula" inputmode="numeric" class="form-control form-control-lg" value="{{old('cedula')}}" required autofocus>
    </div>
    <div class="form-group">
        <label for="codigo">Código de acceso</label>
        <input id="codigo" name="codigo" autocomplete="one-time-code" class="form-control form-control-lg text-uppercase" required>
    </div>
    <button class="btn btn-primary btn-block">Ingresar</button>
</form>
@endsection
