<div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
    <div class="btn-group mb-2" role="group" aria-label="Navegación principal">
        <a class="btn btn-outline-secondary" href="{{ route('home') }}">
            <i class="fa fa-arrow-left mr-1"></i> Dashboard principal
        </a>
    </div>
</div>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('transport.index') ? 'active' : '' }}" href="{{ route('transport.index') }}">Resumen</a>
    </li>

    @canany(['transporte.rutas.ver', 'transporte.vehiculos.ver', 'transporte.conductores.ver', 'transporte.pasajeros.ver', 'transporte.configuracion.gestionar'])
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('transport.rutas.*', 'transport.routes.*', 'transport.vehiculos.*', 'transport.personal.*', 'transport.pasajeros.*', 'transport.settings.*') ? 'active' : '' }}" href="#" role="button" data-toggle="dropdown">Catálogos</a>
        <div class="dropdown-menu">
            @can('transporte.rutas.ver')<a class="dropdown-item {{ request()->routeIs('transport.rutas.*', 'transport.routes.*') ? 'active' : '' }}" href="{{ route('transport.rutas.index') }}">Rutas</a>@endcan
            @can('transporte.vehiculos.ver')<a class="dropdown-item {{ request()->routeIs('transport.vehiculos.*') ? 'active' : '' }}" href="{{ route('transport.vehiculos.index') }}">Vehículos</a>@endcan
            @can('transporte.conductores.ver')<a class="dropdown-item {{ request()->routeIs('transport.personal.*') ? 'active' : '' }}" href="{{ route('transport.personal.index') }}">Conductores y monitores</a>@endcan
            @can('transporte.pasajeros.ver')<a class="dropdown-item {{ request()->routeIs('transport.pasajeros.*') ? 'active' : '' }}" href="{{ route('transport.pasajeros.index') }}">Pasajeros</a>@endcan
            @can('transporte.configuracion.gestionar')
            <div class="dropdown-divider"></div>
            <a class="dropdown-item {{ request()->routeIs('transport.settings.*') ? 'active' : '' }}" href="{{ route('transport.settings.edit') }}">Configuración</a>
            @endcan
        </div>
    </li>
    @endcanany

    @canany(['transporte.programacion.ver'])
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('transport.schedules.*', 'transport.exceptions.*', 'transport.calendar.*') ? 'active' : '' }}" href="#" role="button" data-toggle="dropdown">Programación</a>
        <div class="dropdown-menu">
            @can('transporte.programacion.ver')<a class="dropdown-item {{ request()->routeIs('transport.schedules.*', 'transport.exceptions.*') ? 'active' : '' }}" href="{{ route('transport.schedules.index') }}">Programación</a>@endcan
            @can('transporte.programacion.ver')<a class="dropdown-item {{ request()->routeIs('transport.calendar.*') ? 'active' : '' }}" href="{{ route('transport.calendar.index') }}">Calendario</a>@endcan
        </div>
    </li>
    @endcanany

    @can('transporte.operacion.ver')
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('transport.operation.*', 'transport.services.*') ? 'active' : '' }}" href="{{ route('transport.operation.index') }}">Transporte — Hoy</a>
    </li>
    @endcan

    @canany(['transporte.indicadores.ver', 'transporte.documentos.ver', 'transporte.historico.ver', 'transporte.alertas.ver', 'transporte.reportes.ver'])
    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('transport.indicators.*', 'transport.documents.*', 'transport.history.*', 'transport.alerts.*', 'transport.reports.*') ? 'active' : '' }}" href="#" role="button" data-toggle="dropdown">Seguimiento</a>
        <div class="dropdown-menu">
            @can('transporte.indicadores.ver')<a class="dropdown-item {{ request()->routeIs('transport.indicators.*') ? 'active' : '' }}" href="{{ route('transport.indicators.index') }}">Indicadores</a>@endcan
            @can('transporte.documentos.ver')<a class="dropdown-item {{ request()->routeIs('transport.documents.*') ? 'active' : '' }}" href="{{ route('transport.documents.index') }}">Control documental</a>@endcan
            @can('transporte.historico.ver')<a class="dropdown-item {{ request()->routeIs('transport.history.*') ? 'active' : '' }}" href="{{ route('transport.history.index') }}">Histórico</a>@endcan
            @can('transporte.alertas.ver')<a class="dropdown-item {{ request()->routeIs('transport.alerts.*') ? 'active' : '' }}" href="{{ route('transport.alerts.index') }}">Alertas</a>@endcan
            @can('transporte.reportes.ver')<a class="dropdown-item {{ request()->routeIs('transport.reports.*') ? 'active' : '' }}" href="{{ route('transport.reports.index') }}">Reportes</a>@endcan
        </div>
    </li>
    @endcanany
</ul>
