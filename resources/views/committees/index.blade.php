@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <a href="{{ route('home') }}" class="text-secondary font-weight-bold"><i class="fa fa-arrow-left mr-1"></i> Volver al Dashboard</a>
    <div class="my-4">
        <h2 class="text-primary font-weight-bold"><i class="fas fa-users-cog mr-2"></i>Comités SG-SST</h2>
        <p class="text-muted mb-1">Conformación normativa de los comités de la organización.</p>
        <span class="badge badge-light border">Trabajadores activos registrados: {{ $workersCount }}</span>
    </div>

    @if(!$company)
        <div class="alert alert-warning">Debe configurar primero el perfil de la empresa.</div>
    @endif
    <div class="alert alert-info"><i class="fa fa-info-circle mr-1"></i> La composición se calcula con base en los trabajadores activos registrados en el sistema. Verifique que la planta de personal esté actualizada.</div>

    <div class="row">
        @foreach($cards as $card)
        @php
            $process = optional($card['committee'])->latestProcess;
        @endphp
        <div class="col-lg-6 mb-4">
            <div class="card shadow border-0 h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 text-primary font-weight-bold">{{ $card['type']->label() }}</h4>
                    <span class="badge badge-{{ $card['committee']?->status?->value === 'active' ? 'success' : ($process ? 'warning' : 'secondary') }} px-3 py-2">{{ $card['committee'] ? $card['committee']->status->label() : 'Sin conformar' }}</span>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-4 border-right"><small class="text-muted d-block">Trabajadores</small><strong>{{ $workersCount }}</strong></div>
                        <div class="col-4 border-right"><small class="text-muted d-block">Composición</small><strong>{{ $card['composition']['employer_principals'] }} + {{ $card['composition']['worker_principals'] }}</strong></div>
                        <div class="col-4"><small class="text-muted d-block">Candidatos</small><strong>{{ $process ? $process->candidates->count() : 0 }}</strong></div>
                    </div>
                    @if($card['composition']['mode'] === 'VIGIA_SST')
                        <div class="alert alert-warning py-2">Por tener menos de 10 trabajadores corresponde la figura de <strong>Vigía de Seguridad y Salud en el Trabajo</strong>, no un COPASST convencional.</div>
                    @endif
                    <p class="small text-muted">{{ $card['composition']['regulation_reference'] }}</p>
                    @if($process)
                        <a href="{{ route('committees.formations.show', $process) }}" class="btn btn-outline-primary">Ver proceso actual</a>
                    @endif
                    @if($card['committee']?->latestFinalFormation)<a href="{{route('committees.formations.finalization.show',$card['committee']->latestFinalFormation->election)}}" class="btn btn-success">Ver comité activo</a>@endif
                    <a href="{{ route('committees.formations.create', strtolower($card['type']->value)) }}" class="btn btn-primary">{{ $process ? 'Crear nuevo período' : 'Iniciar conformación' }}</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
