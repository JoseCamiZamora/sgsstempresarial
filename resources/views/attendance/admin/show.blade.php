@extends('layouts.app')
@section('content')
<div class="container-fluid px-3 px-xl-4 my-4">
 <a href="{{$backUrl}}" class="btn btn-outline-secondary mb-3">← {{$backLabel}}</a><h2>Asistencia digital</h2><h4>{{$event->title}}</h4>
 @if(session('participant_codes'))<div class="alert alert-warning"><b>Código de acceso para firmar en el QR.</b> Para empleados es su número de cédula. Para invitados externos es un código de un solo uso — guárdelo, no se muestra de nuevo.<table class="table table-sm">@foreach(session('participant_codes') as $row)<tr><td>{{$row['name']}}</td><td><code>{{$row['code']}}</code></td></tr>@endforeach</table></div>@endif
 <div class="row"><div class="col-lg-4 col-xl-3 mb-3"><div class="card h-100"><div class="card-body">
  <div id="attendance-stats">@include('attendance.admin._stats')</div>
  @if($publicAttendanceUrl && $qrDataUri)<img class="img-fluid" src="{{$qrDataUri}}" alt="Código QR para registrar asistencia"><p class="small">QR vigente generado en esta sesión.</p><a class="btn btn-outline-primary btn-block mb-2" target="_blank" rel="noopener noreferrer" href="{{$publicAttendanceUrl}}">Abrir enlace de asistencia</a><div class="small text-muted text-break mb-2">Destino: {{$publicAttendanceUrl}}</div>@else<div class="alert alert-info small">El token no se guarda en texto plano. Regenere el QR para visualizarlo.</div>@endif
  <form method="POST" action="{{route('attendance.qr.rotate',$event)}}">@csrf<button class="btn btn-warning btn-block">Regenerar QR</button></form>
  <div class="mt-2">@if(in_array($event->status,['draft','scheduled']))<form method="POST" action="{{route('attendance.open',$event)}}">@csrf<button class="btn btn-success btn-block">Abrir asistencia</button></form>@elseif($event->status==='open')@if(!$event->isOpen() && now()->lt($event->attendance_closes_at))<form method="POST" action="{{route('attendance.open',$event)}}" class="mb-2">@csrf<button class="btn btn-success btn-block">Habilitar acceso ahora</button></form>@endif<form method="POST" action="{{route('attendance.close',$event)}}">@csrf<button class="btn btn-danger btn-block">Cerrar asistencia</button></form>@elseif($event->status==='closed')<form method="POST" action="{{route('attendance.finalize',$event)}}">@csrf<button class="btn btn-primary btn-block">Finalizar asistencia</button></form>@endif</div>
  <hr><a class="btn btn-outline-success btn-block" href="{{route('attendance.export',$event)}}">Exportar Excel</a><form method="POST" action="{{route('attendance.evidence.generate',$event)}}">@csrf<button class="btn btn-outline-danger btn-block mt-2">Generar evidencia PDF</button></form>@foreach($event->evidences as $evidence)<a class="btn btn-sm btn-link" href="{{route('attendance.evidence.download',$evidence)}}">Descargar PDF v{{$evidence->version}}</a>@endforeach
 </div></div></div><div class="col-lg-8 col-xl-9">
  <div class="d-flex justify-content-between align-items-center mb-2">
   <span class="text-muted small" id="attendance-refreshed-at">Última actualización: al cargar la página.</span>
   <button type="button" id="attendance-refresh-btn" class="btn btn-sm btn-outline-primary"><i class="fa fa-refresh mr-1"></i> Actualizar asistencia</button>
  </div>
  <div id="attendance-table-wrap">@include('attendance.admin._participants-table')</div>
  <h5>Registro manual de contingencia</h5><form class="form-row mb-4" method="POST" action="{{route('attendance.manual',$event)}}">@csrf<div class="col-md-4"><select name="participant_id" class="form-control" required><option value="">Participante pendiente</option>@foreach($event->participants->filter(fn($p)=>!$p->record) as $p)<option value="{{$p->id}}">{{$p->name_snapshot}}</option>@endforeach</select></div><div class="col-md-3"><input type="datetime-local" name="confirmed_at" class="form-control" required></div><div class="col-md-4"><input name="reason" class="form-control" minlength="10" placeholder="Motivo de contingencia" required></div><div class="col-md-1"><button class="btn btn-primary">Registrar</button></div></form>
  <h5>Agregar participante externo</h5><form method="POST" action="{{route('attendance.participants.store',$event)}}">@csrf<input type="hidden" name="participant_type" value="external"><input name="name" class="form-control mb-2" placeholder="Nombre" required><input name="organization" class="form-control mb-2" placeholder="Organización"><input type="email" name="email" class="form-control mb-2" placeholder="Correo opcional"><button class="btn btn-primary">Agregar y generar código</button></form>
 </div></div>
</div>
@endsection
@section('scripts')
<script>
$(function () {
    $('#attendance-refresh-btn').on('click', function () {
        var $btn = $(this);
        $btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin mr-1"></i> Actualizando...');
        $.getJSON('{{ route('attendance.status', $event) }}')
            .done(function (data) {
                $('#attendance-stats').html(data.stats);
                $('#attendance-table-wrap').html(data.table);
                $('#attendance-refreshed-at').text('Última actualización: ' + new Date().toLocaleTimeString());
            })
            .fail(function () {
                Swal.fire({ icon: 'error', title: 'No se pudo actualizar', text: 'Intenta de nuevo en unos segundos.' });
            })
            .always(function () {
                $btn.prop('disabled', false).html('<i class="fa fa-refresh mr-1"></i> Actualizar asistencia');
            });
    });
});
</script>
@endsection






