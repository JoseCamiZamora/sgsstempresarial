<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="robots" content="noindex,nofollow"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Votación</title></head>
<body><main style="max-width:700px;margin:30px auto;font-family:sans-serif">
<h1>Elección {{$election->formationProcess->committee->name}}</h1>
@if($errors->any())<div style="background:#f8d7da;color:#842029;padding:15px;margin-bottom:15px;border:1px solid #f5c2c7">@foreach($errors->all() as $error)<div>{{$error}}</div>@endforeach</div>@endif
<form id="voteForm" method="POST" action="{{route('public.elections.vote',$election)}}">@csrf<input type="hidden" name="token" value="{{$token}}"><p>Seleccione máximo {{$election->max_selections}}:</p>
@foreach($election->candidates as $candidate)<label style="display:block;border:1px solid #ddd;padding:15px;margin:10px"><input type="{{$election->max_selections===1?'radio':'checkbox'}}" name="selections[]" value="{{$candidate->id}}"> <b>{{$candidate->name}}</b><br>{{$candidate->position}} — {{$candidate->department}}<br>{{$candidate->proposal}}</label>@endforeach
<button id="voteButton" style="padding:15px;width:100%">Registrar mi voto</button></form>
</main><script>document.getElementById('voteForm').addEventListener('submit',function(){var button=document.getElementById('voteButton');button.disabled=true;button.textContent='Registrando voto...';});</script></body></html>
