@php($step = $step ?? 1)
<ol class="breadcrumb bg-light py-2 px-3 mb-3">
    <li class="breadcrumb-item {{ $step == 1 ? 'font-weight-bold text-primary' : 'text-muted' }}">1. Necesidad</li>
    <li class="breadcrumb-item {{ $step == 2 ? 'font-weight-bold text-primary' : 'text-muted' }}">2. Actividad del programa</li>
    <li class="breadcrumb-item {{ $step == 3 ? 'font-weight-bold text-primary' : 'text-muted' }}">3. Sesión</li>
</ol>
