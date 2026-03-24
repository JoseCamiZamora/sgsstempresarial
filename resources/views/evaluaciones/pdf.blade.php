<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Autoevaluación - {{ $perfil->nombre_empresa ?? 'Sinergia SST' }}</title>
    <style>
        @page { margin: 2cm; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; font-size: 11pt; line-height: 1.4; }
        
        /* Encabezado Tipo Membrete */
        .table-header { width: 100%; border: 1px solid #000; margin-bottom: 20px; }
        .table-header td { border: 1px solid #000; padding: 10px; text-align: center; vertical-align: middle; }
        .logo { max-width: 120px; max-height: 80px; }
        
        /* Información de la Empresa */
        .empresa-nombre { font-weight: bold; font-size: 14pt; margin: 0; text-transform: uppercase; }
        .empresa-nit { font-size: 10pt; margin: 5px 0; }
        
        /* Títulos de Sección */
        .seccion-titulo { background-color: #2c3e50; color: #ffffff; padding: 8px; font-size: 12pt; text-align: center; font-weight: bold; margin-bottom: 10px; }
        
        /* Tabla de Estándares */
        .table-datos { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table-datos th { background-color: #f2f2f2; border: 1px solid #333; padding: 8px; font-size: 10pt; text-align: center; }
        .table-datos td { border: 1px solid #333; padding: 7px; font-size: 9pt; }
        
        /* Colores de Cumplimiento */
        .cumple-si { color: #1a7e33; font-weight: bold; text-align: center; }
        .cumple-no { color: #d63031; font-weight: bold; text-align: center; }
        
        /* Caja de Resultados */
        .puntaje-container { margin-top: 20px; border: 2px solid #2c3e50; padding: 15px; background-color: #f8f9fa; }
        .estado-box { font-size: 14pt; font-weight: bold; text-align: center; }
        
        /* Firmas */
        .footer-firmas { margin-top: 60px; width: 100%; }
        .firma-box { width: 45%; border-top: 1px solid #000; text-align: center; display: inline-block; padding-top: 5px; font-size: 10pt; }
        .espacio-firma { width: 8%; display: inline-block; }
    </style>
</head>
<body>

    <table class="table-header">
        <tr>
            <td style="width: 25%;">
                @if(isset($logoBase64) && $logoBase64)
                    {{-- Aquí imprimimos la imagen ya convertida --}}
                    <img src="{{ $logoBase64 }}" class="logo" style="max-width: 120px;">
                @else
                    <div style="background: #eee; padding: 10px; border: 1px solid #ccc;">
                        <strong>{{ $perfil->nombre_empresa ?? 'Sinergia SST' }}</strong>
                    </div>
                @endif
            </td>
            <td style="width: 50%;">
                <p class="empresa-nombre">{{ $perfil->nombre_empresa ?? 'Sinergia SST' }}</p>
                <p class="empresa-nit">NIT: {{ $perfil->nit ?? '000.000.000-0' }}</p>
                <p style="font-size: 9pt; margin: 0;">Licencia: {{ $perfil->licencia_sst ?? 'N/A' }}</p>
            </td>
            <td style="width: 25%; font-size: 9pt;">
                <strong>SISTEMA DE GESTIÓN SST</strong><br>
                Evaluación Res. 0312<br>
                Fecha: {{ \Carbon\Carbon::parse($evaluacion->fecha_evaluacion)->format('d/m/Y') }}
            </td>
        </tr>
    </table>

    <div class="seccion-titulo">INFORME DE AUTOEVALUACIÓN DE ESTÁNDARES MÍNIMOS</div>

    <table style="width: 100%; margin-bottom: 15px; font-size: 10pt;">
        <tr>
            <td><strong>Responsable de Evaluación:</strong> {{ $evaluacion->user->name ?? 'No asignado' }}</td>
            <td style="text-align: right;"><strong>ID Evaluación:</strong> #{{ str_pad($evaluacion->id, 5, '0', STR_PAD_LEFT) }}</td>
        </tr>
    </table>

    <table class="table-datos">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 65%;">Ítem del Estándar</th>
                <th style="width: 15%;">Valor</th>
                <th style="width: 15%;">Resultado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($estandares as $index => $item)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $item->nombre }}</td>
                <td style="text-align: center;">{{ $item->porcentaje }}%</td>
                <td>
                    @php
                        $cumple = is_array($evaluacion->respuestas) && in_array($item->porcentaje, $evaluacion->respuestas);
                    @endphp
                    @if($cumple)
                        <span class="cumple-si">CUMPLE</span>
                    @else
                        <span class="cumple-no">NO CUMPLE</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="puntaje-container">
        <table width="100%" style="border: none;">
            <tr>
                <td style="border: none; width: 50%; font-size: 12pt;">
                    <strong>PUNTAJE TOTAL:</strong> {{ $evaluacion->puntaje_total }}%
                </td>
                <td style="border: none; width: 50%;" class="estado-box">
                    VALORACIÓN: 
                    <span style="color: {{ $evaluacion->puntaje_total >= 85 ? '#1a7e33' : ($evaluacion->puntaje_total >= 60 ? '#f39c12' : '#d63031') }}">
                        {{ strtoupper($evaluacion->estado_resultado) }}
                    </span>
                </td>
            </tr>
        </table>
    </div>

    <div class="footer-firmas">
        <div class="firma-box">
            <br><br>
            <strong>{{ $evaluacion->user->name }}</strong><br>
            Responsable SG-SST<br>
            Licencia: {{ $perfil->licencia_sst }}
        </div>
        <div class="espacio-firma"></div>
        <div class="firma-box">
            <br><br>
            <strong>{{ $perfil->representante_legal ?? 'Firma Representante' }}</strong><br>
            Representante Legal<br>
            NIT/CC: {{ $perfil->nit }}
        </div>
    </div>

</body>
</html>