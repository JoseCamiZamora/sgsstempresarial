@extends('layouts.app')
@section('content')
<div class="container my-4">
 <a href="{{route('committees.formations.show',$election->formationProcess)}}" class="btn btn-outline-secondary mb-3"><i class="fa fa-arrow-left mr-1"></i> Regresar al proceso del comité</a>
 <h2>Elección de representantes</h2>
 @if(session('generated_tokens'))
 <div class="alert alert-warning"><strong>Guarde o distribuya estos enlaces ahora.</strong><p class="mb-2">Por seguridad no podrán consultarse nuevamente. Si vuelve a regenerarlos, estos quedarán invalidados.</p><div class="table-responsive"><table class="table table-sm table-bordered bg-white text-dark"><thead class="thead-light"><tr><th class="text-dark">Empleado</th><th class="text-dark">Enlace personal</th></tr></thead><tbody>
 @forelse(session('generated_tokens') as $item)<tr><td class="text-dark font-weight-bold align-middle">{{$item['employee']->nombre_completo}}</td><td><div class="input-group input-group-sm"><input class="form-control personal-voting-link" readonly value="{{route('public.elections.ballot',[$election,$item['token']])}}"><div class="input-group-append"><button type="button" class="btn btn-outline-primary copy-link">Copiar</button></div></div></td></tr>@empty<tr><td colspan="2" class="text-dark">Todos los electores habilitados ya votaron.</td></tr>@endforelse
 </tbody></table></div></div>
 @endif
 <div class="card"><div class="card-body">
  <h5>Estado: {{$election->status->label()}}</h5>
  <p>Electores: {{$election->electorate_count}} · Candidatos: {{$election->candidate_count}} · Participaron: {{$election->voters->where('has_voted',true)->count()}}</p>
  <label>URL informativa general</label><input class="form-control" readonly value="{{route('public.elections.show',$election)}}"><small>Esta URL muestra el estado, pero no permite votar sin una credencial individual.</small>
  @if(in_array($election->status->value,['prepared','scheduled','open']))
  <div class="alert alert-light border mt-3 mb-0"><p class="mb-2"><strong>¿Perdió los enlaces personales?</strong> Puede regenerar los de los {{$election->voters->where('has_voted',false)->where('status','enabled')->count()}} empleados pendientes. Sus enlaces anteriores quedarán invalidados.</p><form id="regenerateCredentialsForm" method="POST" action="{{route('committees.elections.credentials.regenerate',$election)}}">@csrf<button type="submit" class="btn btn-outline-warning">Regenerar enlaces pendientes</button></form></div>
  @endif
  <hr>
  @if($election->status->value==='prepared')<form method="POST" action="{{route('committees.elections.open',$election)}}">@csrf<button class="btn btn-success">Abrir elección</button></form>@elseif($election->status->value==='open')<form method="POST" action="{{route('committees.elections.close',$election)}}">@csrf<input name="reason" class="form-control my-2" placeholder="Motivo" required><button class="btn btn-danger">Cerrar elección</button></form>@else<a class="btn btn-primary" href="{{route('committees.elections.scrutiny',$election)}}">Escrutinio</a>@endif
 </div></div>
</div>
@endsection
@section('scripts')
<script>
document.querySelectorAll('.copy-link').forEach(function(button){button.addEventListener('click',function(){var input=button.closest('.input-group').querySelector('.personal-voting-link');navigator.clipboard.writeText(input.value).then(function(){button.textContent='Copiado';});});});
var regenerateForm=document.getElementById('regenerateCredentialsForm');
if(regenerateForm){regenerateForm.addEventListener('submit',function(event){event.preventDefault();Swal.fire({title:'¿Regenerar enlaces personales?',text:'Se generarán nuevos enlaces para quienes aún no han votado. Sus enlaces anteriores dejarán de funcionar.',icon:'warning',showCancelButton:true,confirmButtonColor:'#f0ad4e',cancelButtonColor:'#6c757d',confirmButtonText:'Sí, regenerar',cancelButtonText:'Cancelar',reverseButtons:true}).then(function(result){if(result.isConfirmed){Swal.fire({title:'Generando enlaces...',allowOutsideClick:false,allowEscapeKey:false,didOpen:function(){Swal.showLoading();}});regenerateForm.submit();}});});}
</script>
@endsection
