<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Matriz de Riesgos - Sinergia SST</title>
    <style>
        body { font-family: sans-serif; font-size: 10px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #001f3f; }
        .header p { margin: 5px 0; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #001f3f; color: white; font-weight: bold; }
        .badge-bajo { color: green; font-weight: bold; }
        .badge-medio { color: orange; font-weight: bold; }
        .badge-alto { color: red; font-weight: bold; }
        .badge-extremo { color: darkred; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Matriz de Identificación de Peligros y Riesgos</h2>
        <p>Sistema de Gestión de Seguridad y Salud en el Trabajo (SGSST) - Generado el {{ date('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Proceso / Zona</th>
                <th>Peligro</th>
                <th>Efectos Posibles</th>
                <th>Nivel</th>
                <th>Registrado Por</th>
            </tr>
        </thead>
        <tbody>
            @foreach($riesgos as $riesgo)
            <tr>
                <td style="text-align: center;">{{ $riesgo->id }}</td>
                <td><strong>{{ $riesgo->proceso }}</strong><br>{{ $riesgo->zona_lugar }}</td>
                <td>{{ $riesgo->clasificacion_peligro }}</td>
                <td>{{ $riesgo->efectos_posibles }}</td>
                <td>
                    @php
                        $clase = 'badge-bajo';
                        if($riesgo->nivel_riesgo == 'Medio') $clase = 'badge-medio';
                        if($riesgo->nivel_riesgo == 'Alto') $clase = 'badge-alto';
                        if($riesgo->nivel_riesgo == 'Extremo') $clase = 'badge-extremo';
                    @endphp
                    <span class="{{ $clase }}">{{ strtoupper($riesgo->nivel_riesgo) }}</span>
                </td>
                <td>{{ $riesgo->responsable->name ?? 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>