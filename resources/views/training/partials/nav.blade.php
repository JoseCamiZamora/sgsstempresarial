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

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('training.needs.*', 'training.topics.*', 'training.programs.*') ? 'active' : '' }}" href="#" role="button" data-toggle="dropdown">Planeación</a>
        <div class="dropdown-menu">
            <a class="dropdown-item {{ request()->routeIs('training.needs.*') ? 'active' : '' }}" href="{{ route('training.needs.index') }}">Necesidades</a>
            <a class="dropdown-item {{ request()->routeIs('training.topics.*') ? 'active' : '' }}" href="{{ route('training.topics.index') }}">Catálogo de temas</a>
            <a class="dropdown-item {{ request()->routeIs('training.programs.*') ? 'active' : '' }}" href="{{ route('training.programs.index') }}">Programa Anual</a>
        </div>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('training.sessions.*', 'training.instructors.*', 'training.inductions.*', 'training.reinforcements.*') ? 'active' : '' }}" href="#" role="button" data-toggle="dropdown">Ejecución</a>
        <div class="dropdown-menu">
            <a class="dropdown-item {{ request()->routeIs('training.sessions.*') ? 'active' : '' }}" href="{{ route('training.sessions.index') }}">Sesiones</a>
            <a class="dropdown-item {{ request()->routeIs('training.inductions.*') ? 'active' : '' }}" href="{{ route('training.inductions.index') }}">Inducciones pendientes</a>
            <a class="dropdown-item {{ request()->routeIs('training.reinforcements.*') ? 'active' : '' }}" href="{{ route('training.reinforcements.index') }}">Refuerzos pendientes</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item {{ request()->routeIs('training.instructors.*') ? 'active' : '' }}" href="{{ route('training.instructors.index') }}">Instructores</a>
        </div>
    </li>

    <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle {{ request()->routeIs('training.evaluations.*', 'training.questions.*', 'training.requirements.*') ? 'active' : '' }}" href="#" role="button" data-toggle="dropdown">Evaluación</a>
        <div class="dropdown-menu">
            <a class="dropdown-item {{ request()->routeIs('training.evaluations.*') ? 'active' : '' }}" href="{{ route('training.evaluations.index') }}">Evaluaciones</a>
            <a class="dropdown-item {{ request()->routeIs('training.questions.*') ? 'active' : '' }}" href="{{ route('training.questions.index') }}">Banco de preguntas</a>
            <a class="dropdown-item {{ request()->routeIs('training.requirements.*') ? 'active' : '' }}" href="{{ route('training.requirements.index') }}">Rutas SST (requisitos)</a>
        </div>
    </li>

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('training.compliance.*') ? 'active' : '' }}" href="{{ route('training.compliance.index') }}">Cumplimiento</a>
    </li>
</ul>
