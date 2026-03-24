<div class="main-navbar bg-white border-bottom shadow-sm" id="menu_principal">
    <div class="container-fluid p-0">
        <nav class="navbar align-items-stretch navbar-light flex-md-nowrap p-0 justify-content-end">

            <ul class="navbar-nav flex-row ml-auto align-items-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-nowrap px-3" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                        <img class="user-avatar rounded-circle mr-2" src="{{ asset('/assets/img/usuario.svg') }}" onerror="this.src='{{ asset('/assets/img/usuario.svg') }}'">
                        <span class="d-none d-md-inline-block font-weight-bold text-dark" id="US_nombre_usuario_actual">
                            {{ Auth::user()->nombres ?? 'Usuario' }}
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-small dropdown-menu-right mt-2">
                        <a class="dropdown-item" href="#"><i class="material-icons">&#xE7FD;</i> Mi Perfil</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-danger" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="material-icons text-danger">&#xE879;</i> Cerrar Sesión 
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </div>
                </li>
            </ul>

            <nav class="nav">
                <a href="#" class="nav-link nav-link-icon toggle-sidebar d-inline d-lg-none text-center" data-toggle="collapse" data-target=".header-navbar" aria-expanded="false" aria-controls="header-navbar">
                    <i class="material-icons">&#xE5D2;</i>
                </a>
            </nav>
            
        </nav>
    </div>
</div>