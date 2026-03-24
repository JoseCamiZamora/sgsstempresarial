<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; }
        .container { background-color: #ffffff; max-width: 600px; margin: 0 auto; border-top: 5px solid #e74a3b; border-radius: 5px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h2 { color: #e74a3b; margin-top: 0; }
        .info-box { background-color: #f8f9fc; border-left: 4px solid #4e73df; padding: 15px; margin-bottom: 20px; }
        .info-box p { margin: 5px 0; font-size: 14px; }
        .btn { display: inline-block; background-color: #4e73df; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>⚠️ Alerta de Seguridad y Salud en el Trabajo</h2>
        <p>Hola, equipo de Administración SGSST.</p>
        <p>Se acaba de registrar un nuevo evento en el sistema Sinergia SST que requiere su atención inmediata:</p>
        
        <div class="info-box">
            <p><strong>ID del Reporte:</strong> #{{ $incidente->id }}</p>
            <p><strong>Tipo de Evento:</strong> {{ $incidente->tipo_evento }}</p>
            <p><strong>Reportado por:</strong> {{ $incidente->reportante->name ?? 'Usuario' }}</p>
            <p><strong>Lugar del Evento:</strong> {{ $incidente->lugar_evento }}</p>
            <p><strong>Fecha y Hora:</strong> {{ \Carbon\Carbon::parse($incidente->fecha_incidente)->format('d/m/Y') }} a las {{ \Carbon\Carbon::parse($incidente->hora_incidente)->format('h:i A') }}</p>
        </div>

        <p><strong>Descripción de los hechos:</strong></p>
        <p style="background-color: #fdfdfd; padding: 10px; border: 1px solid #ddd; font-style: italic;">
            "{{ $incidente->descripcion }}"
        </p>

        <a href="{{ route('incidentes.edit', $incidente->id) }}" class="btn">Ingresar al Sistema para Gestionar</a>
        
        <p style="margin-top: 30px; font-size: 12px; color: #888; border-top: 1px solid #eee; padding-top: 10px;">
            Este es un correo automático generado por Sinergia SST. Por favor, no responda a este mensaje.
        </p>
    </div>
</body>
</html>