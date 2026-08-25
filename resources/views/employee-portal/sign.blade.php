@extends('employee-portal.layout')
@section('content')
<h1 class="h4">Firmar</h1>
<div class="alert alert-info"><b>{{$item->label}}</b><br>{{$item->subtitle}}</div>
@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{$e}}</div>@endforeach</div>@endif

@if($referenceSignature)
<div class="portal-item">
    <label class="font-weight-bold">Usar mi firma guardada</label>
    <p class="small text-muted mb-2">Aplique con un toque la firma que ya tiene guardada, sin necesidad de volver a dibujarla.</p>
    <form method="POST" action="{{route('employee-portal.sign.apply-saved',[$category,$id])}}">
        @csrf
        <div class="portal-checkbox mb-3">
            <input type="checkbox" id="acknowledged_saved" name="acknowledged" value="1" required>
            <label for="acknowledged_saved">{{config('employee_portal.consent_text')}}</label>
        </div>
        <button class="btn btn-primary btn-block">Usar mi firma guardada</button>
    </form>
</div>
<p class="text-center text-muted small my-3">— o —</p>
@endif

<form method="POST" action="{{route('employee-portal.sign.store',[$category,$id])}}" enctype="multipart/form-data">
    @csrf
    <label class="font-weight-bold">Opción 1: dibuje su firma</label>
    <canvas id="portalSignature" data-signature-pad data-signature-input="signature"></canvas>
    <input type="hidden" name="signature" id="signature">
    <button type="button" data-signature-clear="portalSignature" class="btn btn-outline-secondary btn-sm my-2">Borrar firma</button>

    <label class="font-weight-bold mt-3">Opción 2: o suba una imagen de su firma</label>
    <input type="file" name="signature_file" accept="image/png,image/jpeg" capture="environment" class="form-control-file">

    <div class="portal-checkbox mt-3">
        <input type="checkbox" id="save_as_reference" name="save_as_reference" value="1">
        <label for="save_as_reference">Guardar esta firma como mi firma de referencia para próximos documentos</label>
    </div>
    <div class="portal-checkbox my-3">
        <input type="checkbox" id="acknowledged" name="acknowledged" value="1" required>
        <label for="acknowledged">{{config('employee_portal.consent_text')}}</label>
    </div>
    <button class="btn btn-success btn-block">Confirmar firma</button>
</form>
<a href="{{route('employee-portal.dashboard')}}" class="btn btn-link btn-block">Volver a pendientes</a>
<script src="{{asset('js/signature-canvas.js')}}"></script>
@endsection
