@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-xl-4 py-4">
    @include('transport._nav')

    <a href="{{ route('transport.pasajeros.index') }}" class="text-decoration-none text-secondary mb-2 d-inline-block font-weight-bold">
        <i class="fa fa-arrow-left mr-1"></i> Volver a Pasajeros
    </a>

    <div class="mb-4">
        <h2 class="font-weight-bold text-primary">📥 Resultado del Cargue Masivo de Pasajeros</h2>
        <p class="text-muted mb-0">Revise el detalle fila por fila.</p>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-left-success shadow-sm">
                <div class="card-body">
                    <div class="text-uppercase text-success font-weight-bold small">Creados con éxito</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $creados }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-danger shadow-sm">
                <div class="card-body">
                    <div class="text-uppercase text-danger font-weight-bold small">Omitidos por error</div>
                    <div class="h3 mb-0 font-weight-bold">{{ $omitidos }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-left-info shadow-sm">
                <div class="card-body">
                    <div class="text-uppercase text-info font-weight-bold small">Total de filas procesadas</div>
                    <div class="h3 mb-0 font-weight-bold">{{ count($resultados) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">Fila</th>
                            <th class="border-0">Nombre</th>
                            <th class="border-0">Estado</th>
                            <th class="border-0">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($resultados as $r)
                        <tr class="{{ $r['status'] === 'ok' ? '' : 'table-danger' }}">
                            <td class="align-middle">{{ $r['row'] }}</td>
                            <td class="align-middle">{{ $r['nombre'] ?: '—' }}</td>
                            <td class="align-middle">
                                @if($r['status'] === 'ok')
                                    <span class="badge badge-success p-2"><i class="fa fa-check mr-1"></i>Creado</span>
                                @else
                                    <span class="badge badge-danger p-2"><i class="fa fa-times mr-1"></i>Omitido</span>
                                @endif
                            </td>
                            <td class="align-middle">{{ $r['message'] }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No se procesó ninguna fila.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
