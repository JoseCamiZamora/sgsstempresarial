<div class="d-flex flex-wrap align-items-center justify-content-between mb-3">
    <div class="btn-group mb-2" role="group" aria-label="Navegación principal">
        <a class="btn btn-outline-secondary" href="{{ route('home') }}">
            <i class="fa fa-arrow-left mr-1"></i> Regresar al menú
        </a>
        <a class="btn btn-outline-primary" href="{{ route('training.index') }}">
            <i class="fa fa-tachometer-alt mr-1"></i> Dashboard de capacitaciones
        </a>
    </div>
</div>

<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('training.index') ? 'active' : '' }}" href="{{ route('training.index') }}">Resumen</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('training.needs.*') ? 'active' : '' }}" href="{{ route('training.needs.index') }}">Necesidades</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('training.topics.*') ? 'active' : '' }}" href="{{ route('training.topics.index') }}">Catálogo</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('training.programs.*') ? 'active' : '' }}" href="{{ route('training.programs.index') }}">Programa Anual</a>
    </li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('training.sessions.*') ? 'active' : '' }}" href="{{ route('training.sessions.index') }}">Sesiones</a></li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('training.instructors.*') ? 'active' : '' }}" href="{{ route('training.instructors.index') }}">Instructores</a></li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('training.inductions.*') ? 'active' : '' }}" href="{{ route('training.inductions.index') }}">Inducciones</a></li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('training.evaluations.*') ? 'active' : '' }}" href="{{ route('training.evaluations.index') }}">Evaluaciones</a></li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('training.questions.*') ? 'active' : '' }}" href="{{ route('training.questions.index') }}">Preguntas</a></li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('training.requirements.*') ? 'active' : '' }}" href="{{ route('training.requirements.index') }}">Rutas SST</a></li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('training.reinforcements.*') ? 'active' : '' }}" href="{{ route('training.reinforcements.index') }}">Refuerzos</a></li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('training.analytics.*') ? 'active' : '' }}" href="{{ route('training.analytics.index') }}">Indicadores</a></li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('training.matrix.*') ? 'active' : '' }}" href="{{ route('training.matrix.index') }}">Matriz</a></li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('training.gaps.*') ? 'active' : '' }}" href="{{ route('training.gaps.index') }}">Brechas</a></li>
    <li class="nav-item"><a class="nav-link {{ request()->routeIs('training.alerts.*') ? 'active' : '' }}" href="{{ route('training.alerts.index') }}">Alertas</a></li>
</ul>
