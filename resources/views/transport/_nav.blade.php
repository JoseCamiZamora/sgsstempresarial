<div class="d-flex flex-wrap mb-3">
 <a class="btn btn-outline-secondary mr-2 mb-2" href="{{route('home')}}">← Dashboard principal</a>
 <a class="btn btn-outline-primary mr-2 mb-2" href="{{route('transport.index')}}">Resumen</a>
 @can('transporte.operacion.ver')<a class="btn btn-outline-primary mr-2 mb-2" href="{{route('transport.operation.index')}}">Transporte — Hoy</a>@endcan
 @can('transporte.programacion.ver')<a class="btn btn-outline-primary mr-2 mb-2" href="{{route('transport.schedules.index')}}">Programación</a>@endcan
 @can('transporte.programacion.ver')<a class="btn btn-outline-primary mr-2 mb-2" href="{{route('transport.calendar.index')}}">Calendario</a>@endcan
 @can('transporte.rutas.ver')<a class="btn btn-outline-primary mr-2 mb-2" href="{{route('transport.rutas.index')}}">Rutas</a>@endcan
 @can('transporte.vehiculos.ver')<a class="btn btn-outline-primary mr-2 mb-2" href="{{route('transport.vehiculos.index')}}">Vehículos</a>@endcan
 @can('transporte.conductores.ver')<a class="btn btn-outline-primary mr-2 mb-2" href="{{route('transport.personal.index')}}">Conductores y monitores</a>@endcan
 @can('transporte.pasajeros.ver')<a class="btn btn-outline-primary mr-2 mb-2" href="{{route('transport.pasajeros.index')}}">Pasajeros</a>@endcan
 @can('transporte.indicadores.ver')<a class="btn btn-outline-primary mr-2 mb-2" href="{{route('transport.indicators.index')}}">Indicadores</a>@endcan
 @can('transporte.documentos.ver')<a class="btn btn-outline-primary mr-2 mb-2" href="{{route('transport.documents.index')}}">Control documental</a>@endcan
 @can('transporte.historico.ver')<a class="btn btn-outline-primary mr-2 mb-2" href="{{route('transport.history.index')}}">Histórico</a>@endcan
 @can('transporte.alertas.ver')<a class="btn btn-outline-primary mr-2 mb-2" href="{{route('transport.alerts.index')}}">Alertas</a>@endcan
 @can('transporte.reportes.ver')<a class="btn btn-outline-primary mr-2 mb-2" href="{{route('transport.reports.index')}}">Reportes</a>@endcan
 @can('transporte.configuracion.gestionar')<a class="btn btn-outline-primary mr-2 mb-2" href="{{route('transport.settings.edit')}}">Configuración</a>@endcan
</div>
