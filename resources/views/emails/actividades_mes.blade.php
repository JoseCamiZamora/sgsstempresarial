<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; color: #333; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 5px; }
        .header { background-color: #4A90E2; color: white; padding: 10px; text-align: center; font-weight: bold; border-radius: 3px; }
        .task-list { background-color: #f9f9f9; padding: 15px; border-left: 4px solid #f6c23e; margin: 20px 0; }
        .footer { font-size: 0.8rem; color: #888; text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            Sistema de Gestión SST - Sinergia
        </div>
        <p>Hola <strong>{{ $usuario->name }}</strong>,</p>
        <p>Iniciamos el mes de <strong>{{ $mesTexto }}</strong> y tienes las siguientes actividades del Plan de Trabajo Anual bajo tu responsabilidad:</p>
        
        <div class="task-list">
            <ul>
                @foreach($actividades as $item)
                    <li>
                        <strong>{{ $item->actividad->fase_phva }}:</strong> 
                        {{ $item->actividad->actividad }}
                    </li>
                @endforeach
            </ul>
        </div>

        <p>Por favor, recuerda ejecutar estas actividades y subir la evidencia correspondiente en el sistema para mantener nuestros indicadores al día.</p>
        
        <p>Puedes acceder al sistema haciendo clic aquí: <br>
        <a href="{{ route('home') }}">{{ route('home') }}</a></p>

        <div class="footer">
            Este es un correo automático, por favor no respondas a esta dirección.
        </div>
    </div>
</body>
</html>