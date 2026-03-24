<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f6f9; margin: 0; padding: 20px; }
        .container { background-color: #ffffff; max-width: 600px; margin: 0 auto; border-top: 5px solid #36b9cc; border-radius: 5px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h2 { color: #36b9cc; margin-top: 0; }
        .info-box { background-color: #f8f9fc; border-left: 4px solid #f6c23e; padding: 15px; margin-bottom: 20px; }
        .info-box p { margin: 5px 0; font-size: 14px; }
        .btn { display: inline-block; background-color: #36b9cc; color: #ffffff; text-decoration: none; padding: 10px 20px; border-radius: 5px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>📅 Recordatorio del Plan Anual de Trabajo</h2>
        <p>Hola, <strong>{{ $actividad->responsable->name ?? 'Usuario' }}</strong>.</p>
        <p>Te recordamos que tienes una actividad del Sistema de Gestión SST programada para los próximos días y aún se encuentra en estado <strong>Pendiente</strong>.</p>
        
        <div class="info-box">
            <p><strong>Actividad / Capacitación:</strong> {{ $actividad->actividad }}</p>
            <p><strong>Fecha Programada:</strong> {{ \Carbon\Carbon::parse($actividad->fecha_programada)->format('d/m/Y') }}</p>
            <p><strong>Estado Actual:</strong> ⏳ {{ $actividad->estado }}</p>
        </div>

        <p>Por favor, asegúrate de ejecutar esta actividad en la fecha indicada. Una vez finalizada, recuerda ingresar al sistema, cambiar el estado a "Ejecutada" y subir la evidencia (lista de asistencia o fotografías).</p>

        <a href="{{ route('plan-trabajo.index') }}" class="btn">Ingresar a Sinergia SST</a>
        
        <p style="margin-top: 30px; font-size: 12px; color: #888; border-top: 1px solid #eee; padding-top: 10px;">
            Este es un correo automático de Sinergia SST. Por favor, no responda a este mensaje.
        </p>
    </div>
</body>
</html>