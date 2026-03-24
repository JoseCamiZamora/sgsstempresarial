<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Incidentes - Sinergia SST</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #001f3f; padding-bottom: 10px; }
        .header h2 { margin: 0; color: #001f3f; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; vertical-align: top; }
        th { background-color: #001f3f; color: white; font-weight: bold; }
        .text-center { text-align: center; }
        .badge-pendiente { color: #e74a3b; font-weight: bold; }
        .badge-investigacion { color: #36b9cc; font-weight: bold; }
        .badge-cerrado { color: #1cc88a; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Historial de Incidentes y Accidentes</h2>
        <p>Sistema de Gestión de Seguridad y Salud en el Trabajo (SGSST) - Generado el {{ date('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha y Hora</th>
                <th>Reportante</th>
                <th>Tipo / Lugar</th>
                <th>Estado</th>
                <th>Observaciones Área SST</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incidentes as $inc)
            <tr>
                <td class="text-center">{{ $inc->id }}</td>
                <td>{{ \Carbon\Carbon::parse($inc->fecha_incidente)->format('d/m/Y') }}<br>{{ \Carbon\Carbon::parse($inc->hora_incidente)->format('h:i A') }}</td>
                <td>{{ $inc->reportante->name ?? 'N/A' }}</td>
                <td><strong>{{ $inc->tipo_evento }}</strong><br>{{ $inc->lugar_evento }}</td>
                <td class="text-center">
                    @php
                        $clase = 'badge-pendiente';
                        if($inc->estado == 'En Investigación') $clase = 'badge-investigacion';
                        if($inc->estado == 'Cerrado') $clase = 'badge-cerrado';
                    @endphp
                    <span class="{{ $clase }}">{{ strtoupper($inc->estado) }}</span>
                </td>
                <td>{{ $inc->observaciones_sst ?? 'Pendiente de gestión.' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>