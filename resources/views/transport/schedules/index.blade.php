@extends('layouts.app')

@section('content')
<div class="container-fluid px-3 px-xl-4 py-4">
    @include('transport._nav')
    <h1>Programación recurrente</h1>
    <div class="row">
        <div class="col-lg-8">
            @can('transporte.programacion.crear')
            <div class="card mb-4">
                <div class="card-header">Nueva programación</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('transport.schedules.store') }}">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-4"><label>Ruta</label><select name="transport_route_id" class="form-control" required>
                                @foreach($routes as $route)
                                    <option value="{{ $route->id }}">{{ $route->code }} — {{ $route->name }}</option>
                                @endforeach
                            </select></div>
                            <div class="col-md-4"><label>Nombre</label><input name="name" class="form-control" required></div>
                            <div class="col-md-2"><label>Desde</label><input type="date" name="starts_on" class="form-control" required></div>
                            <div class="col-md-2"><label>Hasta</label><input type="date" name="ends_on" class="form-control" required></div>
                        </div>
                        <div class="mt-3"><label>Días</label><div>
                            @foreach(config('transport.weekdays') as $key => $day)
                                <label class="mr-3"><input type="checkbox" name="weekdays[]" value="{{ $key }}"> {{ $day }}</label>
                            @endforeach
                        </div></div>
                        <div class="form-row mt-2">
                            <div class="col"><label>Salida</label><input type="time" name="default_start_time" class="form-control" required></div>
                            <div class="col"><label>Llegada</label><input type="time" name="default_arrival_time" class="form-control" required></div>
                            <div class="col pt-4"><label><input type="checkbox" name="arrival_next_day" value="1"> Llega al día siguiente</label></div>
                            <div class="col"><label>Tipo</label><select name="service_type" class="form-control">
                                @foreach(config('transport.service_types') as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select></div>
                            <div class="col"><label>Jornada</label><select name="shift" class="form-control">
                                @foreach(config('transport.shifts') as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select></div>
                        </div>
                        <div class="form-row mt-2">
                            <div class="col"><label>Vehículo</label><select name="vehicle_id" class="form-control"><option value="">Sin asignar</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}">{{ $vehicle->plate }}</option>
                                @endforeach
                            </select></div>
                            <div class="col"><label>Conductor</label><select name="driver_id" class="form-control"><option value="">Sin asignar</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->display_name }}</option>
                                @endforeach
                            </select></div>
                            <div class="col"><label>Monitor</label><select name="monitor_id" class="form-control"><option value="">Sin asignar</option>
                                @foreach($monitors as $monitor)
                                    <option value="{{ $monitor->id }}">{{ $monitor->display_name }}</option>
                                @endforeach
                            </select></div>
                        </div>
                        <button class="btn btn-primary mt-3">Previsualizar</button>
                    </form>
                </div>
            </div>
            @endcan

            <table class="table table-striped">
                <tr><th>Programación</th><th>Ruta</th><th>Vigencia</th><th>Días</th><th></th></tr>
                @foreach($schedules as $schedule)
                <tr>
                    <td>{{ $schedule->name }}</td><td>{{ $schedule->route->name }}</td>
                    <td>{{ $schedule->starts_on->format('d/m/Y') }}–{{ $schedule->ends_on->format('d/m/Y') }}</td>
                    <td>{{ $schedule->days->pluck('day_of_week')->map(fn ($day) => config('transport.weekdays.'.$day))->implode(', ') }}</td>
                    <td><a class="btn btn-sm btn-outline-primary" href="{{ route('transport.schedules.preview', $schedule) }}">Previsualizar</a></td>
                </tr>
                @endforeach
            </table>
            {{ $schedules->links() }}
        </div>

        <div class="col-lg-4"><div class="card">
            <div class="card-header">Excepciones de calendario</div>
            <div class="card-body">
                @can('transporte.programacion.editar')
                <form method="POST" action="{{ route('transport.exceptions.store') }}">
                    @csrf
                    <input type="date" name="exception_date" class="form-control mb-2" required>
                    <select name="type" class="form-control mb-2">
                        @foreach(config('transport.exception_types') as $key => $value)
                            <option value="{{ $key }}">{{ $value }}</option>
                        @endforeach
                    </select>
                    <select name="transport_route_id" class="form-control mb-2"><option value="">Todas las rutas</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}">{{ $route->name }}</option>
                        @endforeach
                    </select>
                    <input name="reason" class="form-control mb-2" placeholder="Motivo" required>
                    <label><input type="checkbox" name="applies_to_all_routes" value="1"> Aplica a todas las rutas</label>
                    <button class="btn btn-warning btn-block">Registrar excepción</button>
                </form><hr>
                @endcan

                @foreach($exceptions as $exception)
                    <p><strong>{{ $exception->exception_date->format('d/m/Y') }}</strong> — {{ $exception->route?->name ?? 'Todas las rutas' }}<br><small>{{ $exception->reason }}</small></p>
                @endforeach
            </div>
        </div></div>
    </div>
</div>

<div class="card mt-3"><div class="card-header">Copiar programación</div><div class="card-body">
    <form method="POST" action="{{ route('transport.schedules.copy') }}">@csrf
        <div class="form-row">
            <div class="col"><label>Origen desde</label><input type="date" name="source_from" class="form-control" required></div>
            <div class="col"><label>Origen hasta</label><input type="date" name="source_to" class="form-control" required></div>
            <div class="col"><label>Nueva fecha inicial</label><input type="date" name="target_from" class="form-control" required></div>
            <div class="col"><label>Motivo</label><input name="reason" minlength="10" class="form-control" required></div>
            <div class="col-auto pt-4"><button class="btn btn-outline-primary">Copiar</button></div>
        </div>
    </form>
</div></div>
@endsection
