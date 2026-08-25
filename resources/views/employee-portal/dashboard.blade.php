@extends('employee-portal.layout')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Hola, {{$empleado->nombre_completo}}</h1>
        <small class="text-muted">Documentos pendientes de firma</small>
    </div>
    <form method="POST" action="{{route('employee-portal.logout')}}"><button class="btn btn-outline-secondary btn-sm">Salir</button></form>
</div>
@if(session('success'))<div class="alert alert-success">{{session('success')}}</div>@endif

@php($categories = ['attendance' => 'Asistencias', 'entrega_epp' => 'Dotación y EPP', 'documento' => 'Documentos'])

@foreach($categories as $key => $categoryLabel)
    <h2 class="h6 mt-4">{{$categoryLabel}}</h2>
    @forelse(($items[$key] ?? collect()) as $item)
        <div class="portal-item">
            <h6>{{$item->label}}</h6>
            @if($item->subtitle)<small>{{$item->subtitle}}</small><br>@endif
            <small class="text-muted">{{$item->date->format('d/m/Y')}}</small>
            @if($item->badge)<span class="badge badge-warning ml-1">{{$item->badge}}</span>@endif
            <div class="mt-2"><a href="{{route($item->signRouteName, $item->signRouteParams)}}" class="btn btn-success btn-sm">Firmar</a></div>
        </div>
    @empty
        <p class="text-muted small">No tiene pendientes en esta categoría.</p>
    @endforelse
@endforeach
@endsection
