@extends('layouts.app')

@section('content')
<div class="container-fluid py-3">
@include('training.partials.nav')

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Cumplimiento de capacitaciones</h2>
    <form class="form-inline">
        <label class="mr-2 mb-0">Vigencia</label>
        <select name="year" onchange="this.form.submit()" class="form-control">
            @for($y = now()->year; $y >= now()->year - 5; $y--)
                <option value="{{ $y }}" @selected($year === $y)>{{ $y }}</option>
            @endfor
        </select>
    </form>
</div>

{{--
    Pestañas por recarga de página (href con ?tab=), no data-toggle="tab" del lado cliente:
    esta capa comparte layout con un bug preexistente de doble carga de jQuery
    (scripts.blade.php carga jquery-3.3.1 y luego jquery-3.6.0 encima, después de que
    Bootstrap ya registró sus plugins contra la primera instancia) que deja `.tab()`,
    `.modal()` etc. sin funcionar en todo el sitio. No es parte de este cambio arreglarlo
    site-wide, así que esta página no depende de JS de Bootstrap para nada crítico.
--}}
<ul class="nav nav-tabs mb-3">
    @if(isset($sections['indicadores']))
        <li class="nav-item"><a class="nav-link {{ $activeTab === 'indicadores' ? 'active' : '' }}" href="{{ route('training.compliance.index', array_merge(request()->query(), ['tab' => 'indicadores'])) }}">Indicadores</a></li>
    @endif
    @if(isset($sections['matriz']))
        <li class="nav-item"><a class="nav-link {{ $activeTab === 'matriz' ? 'active' : '' }}" href="{{ route('training.compliance.index', array_merge(request()->query(), ['tab' => 'matriz'])) }}">Matriz de cumplimiento</a></li>
    @endif
    @if(isset($sections['brechas']))
        <li class="nav-item"><a class="nav-link {{ $activeTab === 'brechas' ? 'active' : '' }}" href="{{ route('training.compliance.index', array_merge(request()->query(), ['tab' => 'brechas'])) }}">Brechas</a></li>
    @endif
    @if(isset($sections['alertas']))
        <li class="nav-item"><a class="nav-link {{ $activeTab === 'alertas' ? 'active' : '' }}" href="{{ route('training.compliance.index', array_merge(request()->query(), ['tab' => 'alertas'])) }}">Alertas</a></li>
    @endif
    @if(isset($sections['estandares']))
        <li class="nav-item"><a class="nav-link {{ $activeTab === 'estandares' ? 'active' : '' }}" href="{{ route('training.compliance.index', array_merge(request()->query(), ['tab' => 'estandares'])) }}">Estándares</a></li>
    @endif
</ul>

<div class="tab-content">

    {{-- Indicadores --}}
    @if(isset($sections['indicadores']))
    @php($metrics = $sections['indicadores']['metrics'])
    <div class="tab-pane {{ $activeTab === 'indicadores' ? 'active' : '' }}" id="tab-indicadores">
        @php($cards = ['Ejecución' => $metrics['program_execution'], 'Cobertura' => $metrics['coverage'], 'Evaluación' => $metrics['evaluation'], 'Aprobación' => $metrics['approval'], 'Refuerzos' => $metrics['reinforcement'], 'Necesidades atendidas' => $metrics['needs_attended']])
        <div class="row">
            @foreach($cards as $name => $metric)
            <div class="col-md-4 col-xl-2 mb-3">
                <div class="card h-100"><div class="card-body">
                    <small>{{ $name }}</small>
                    <h3>{{ $metric['value'] === null ? 'N/A' : number_format($metric['value'], 2).'%' }}</h3>
                    <small>{{ $metric['numerator'] }} / {{ $metric['denominator'] }}</small>
                </div></div>
            </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-md-6"><div class="card"><div class="card-body">
                <h5>Credenciales</h5>
                <p>Vigentes {{ $metrics['credentials']['valid'] }} · Por vencer {{ $metrics['credentials']['expiring'] }} · Vencidas {{ $metrics['credentials']['expired'] }}</p>
            </div></div></div>
        </div>
        <div class="mt-3">
            <a class="btn btn-danger" href="{{ route('training.reports.pdf', ['year' => $year]) }}">Informe PDF</a>
            <a class="btn btn-success" href="{{ route('training.reports.excel', ['year' => $year]) }}">Indicadores Excel</a>
        </div>
    </div>
    @endif

    {{-- Matriz de cumplimiento --}}
    @if(isset($sections['matriz']))
    @php($employees = $sections['matriz']['employees'])
    <div class="tab-pane {{ $activeTab === 'matriz' ? 'active' : '' }}" id="tab-matriz">
        <form class="form-row mb-3">
            <input type="hidden" name="tab" value="matriz">
            <div class="col"><input class="form-control" name="employee" value="{{ request('employee') }}" placeholder="Trabajador"></div>
            <div class="col"><input class="form-control" name="job" value="{{ request('job') }}" placeholder="Cargo"></div>
            <div class="col"><input class="form-control" name="area" value="{{ request('area') }}" placeholder="Área"></div>
            <div class="col">
                <select class="form-control" name="state">
                    <option value="">Todos los estados</option>
                    @foreach(['completed' => 'Vigente', 'expiring' => 'Por vencer', 'pending' => 'Pendiente', 'expired' => 'Vencido'] as $k => $v)
                        <option value="{{ $k }}" @selected(request('state') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-primary">Filtrar</button>
        </form>
        <a href="{{ route('training.matrix.export', request()->query()) }}" class="btn btn-success mb-2">Exportar Excel</a>
        <div class="table-responsive">
            <table class="table table-bordered table-sm">
                <thead><tr><th>Trabajador</th><th>Cargo / área</th><th>Avance</th><th>Requisitos aplicables</th></tr></thead>
                <tbody>
                @forelse($employees as $e)
                    <tr>
                        <td>{{ $e->nombre_completo }}</td>
                        <td>{{ $e->cargo }}<br><small>{{ $e->area_departamento }}</small></td>
                        <td>{{ $e->training_progress === null ? 'N/A' : $e->training_progress.'%' }}</td>
                        <td>
                            @forelse($e->training_route as $route)
                                @php($labels = ['completed' => ['success', 'Vigente'], 'expiring' => ['warning', 'Próximo a vencer'], 'pending' => ['danger', 'Pendiente'], 'expired' => ['danger', 'Vencido']])
                                @php($x = $labels[$route['status']])
                                <span class="badge badge-{{ $x[0] }} mb-1">{{ $x[1] }}: {{ $route['requirement']->topic->name }}</span><br>
                            @empty
                                <span class="text-muted">No aplica</span>
                            @endforelse
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4">Sin trabajadores para los filtros.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        {{ $employees->links() }}
    </div>
    @endif

    {{-- Brechas --}}
    @if(isset($sections['brechas']))
    @php($gaps = $sections['brechas']['gaps'])
    @php($summary = $sections['brechas']['summary'])
    <div class="tab-pane {{ $activeTab === 'brechas' ? 'active' : '' }}" id="tab-brechas">
        <div class="alert alert-info">{{ $summary['employees'] }} trabajadores con {{ $summary['total'] }} requisitos pendientes o vencidos. La prioridad es interna y no constituye valoración jurídica.</div>
        <a class="btn btn-success mb-2" href="{{ route('training.gaps.export', request()->query()) }}">Exportar Excel</a>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>Trabajador</th><th>Cargo / área</th><th>Requisito</th><th>Estado</th><th>Prioridad</th><th>Acción</th></tr></thead>
                <tbody>
                @forelse($gaps as $g)
                    <tr>
                        <td>{{ $g['employee']->nombre_completo }}</td>
                        <td>{{ $g['employee']->cargo }} / {{ $g['employee']->area_departamento }}</td>
                        <td>{{ $g['requirement']->topic->name }}</td>
                        <td>{{ $g['status'] === 'expired' ? 'Vencido' : 'Pendiente' }}</td>
                        <td>{{ $g['priority'] }}</td>
                        <td>
                            <form method="post" action="{{ route('training.gaps.need') }}">
                                @csrf
                                <input type="hidden" name="employee_id" value="{{ $g['employee']->id }}">
                                <input type="hidden" name="training_requirement_id" value="{{ $g['requirement']->id }}">
                                <button class="btn btn-sm btn-outline-primary">Crear/vincular necesidad</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No se detectaron brechas.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Alertas --}}
    @if(isset($sections['alertas']))
    @php($alerts = $sections['alertas']['alerts'])
    <div class="tab-pane {{ $activeTab === 'alertas' ? 'active' : '' }}" id="tab-alertas">
        <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Reglas: programa faltante, actividad vencida, credencial por vencer/vencida, refuerzo vencido, inducción pendiente, brecha.</span>
            <form method="post" action="{{ route('training.alerts.scan') }}">
                @csrf
                <button class="btn btn-primary">Actualizar análisis</button>
            </form>
        </div>
        <table class="table">
            <thead><tr><th>Severidad</th><th>Alerta</th><th>Estado</th><th>Detectada</th><th>Acciones</th></tr></thead>
            <tbody>
            @forelse($alerts as $a)
                <tr>
                    <td><span class="badge badge-{{ $a->severity === 'critical' ? 'danger' : 'warning' }}">{{ $a->severity }}</span></td>
                    <td><strong>{{ $a->title }}</strong><br>{{ $a->message }}</td>
                    <td>{{ $a->status }}</td>
                    <td>{{ $a->last_detected_at?->format('d/m/Y H:i') }}</td>
                    <td>
                        <form method="post" action="{{ route('training.alerts.update', $a) }}">
                            @csrf @method('patch')
                            <button name="action" value="acknowledge" class="btn btn-sm btn-outline-warning">Reconocer</button>
                            <button name="action" value="resolve" class="btn btn-sm btn-outline-success">Resolver</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No hay alertas.</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $alerts->links() }}
    </div>
    @endif

    {{-- Estándares --}}
    @if(isset($sections['estandares']))
    @php($evidence = $sections['estandares']['evidence'])
    <div class="tab-pane {{ $activeTab === 'estandares' ? 'active' : '' }}" id="tab-estandares">
        <div class="alert alert-warning">Esta vista identifica evidencia disponible. No modifica ni califica automáticamente la autoevaluación.</div>
        <table class="table">
            <thead><tr><th>Componente</th><th>Estado</th><th>Registros</th><th>Acción</th></tr></thead>
            <tbody>
            @foreach($evidence as $e)
                <tr>
                    <td>{{ $e['name'] }}</td>
                    <td>{{ $e['status'] }}</td>
                    <td>{{ $e['count'] }}</td>
                    <td><a href="{{ route('evaluacion.index') }}" class="btn btn-sm btn-outline-primary">Revisar estándar</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endif

</div>
</div>
@endsection
