<!DOCTYPE html>
<html>
<head>
    <title>Acta de Entrega de EPP</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; border-bottom: 2px solid #444; padding-bottom: 10px; margin-bottom: 20px; }
        .section-title { background: #eee; padding: 5px; font-weight: bold; margin-top: 20px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .compromiso { font-size: 10px; text-align: justify; margin-top: 30px; border: 1px solid #000; padding: 10px; }
        .firmas { margin-top: 50px; }
        .firma-box { width: 45%; display: inline-block; border-top: 1px solid #000; text-align: center; padding-top: 5px; margin-right: 4%; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SISTEMA DE GESTIÓN DE SEGURIDAD Y SALUD EN EL TRABAJO (SG-SST)</h2>
        <h3>ACTA DE ENTREGA DE ELEMENTOS DE PROTECCIÓN PERSONAL</h3>
    </div>

    <div class="section-title">Datos del Trabajador</div>
    <table>
        <tr>
            <th>Nombre Completo:</th>
            <td>{{ $entrega->empleado->nombre_completo }}</td>
            <th>Cédula:</th>
            <td>{{ $entrega->empleado->cedula }}</td>
        </tr>
        <tr>
            <th>Cargo:</th>
            <td>{{ $entrega->empleado->cargo }}</td>
            <th>Fecha:</th>
            <td>{{ \Carbon\Carbon::parse($entrega->fecha_entrega)->format('d/m/Y') }}</td>
        </tr>
    </table>

    <div class="section-title">Detalle del Elemento Entregado</div>
    <table>
        <thead>
            <tr style="background: #f2f2f2;">
                <th>Elemento</th>
                <th>Categoría</th>
                <th>Talla</th>
                <th>Cantidad</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $entrega->epp->nombre }}</td>
                <td>{{ $entrega->epp->categoria }}</td>
                <td>{{ $entrega->talla_entregada }}</td>
                <td>{{ $entrega->cantidad }}</td>
                <td>{{ $entrega->motivo }}</td>
            </tr>
        </tbody>
    </table>

    <div class="compromiso">
        <strong>COMPROMISO DEL TRABAJADOR:</strong><br>
        Manifiesto que he recibido los Elementos de Protección Personal (EPP) arriba descritos y me comprometo a: 
        1. Utilizarlos permanentemente durante mi jornada laboral. 
        2. Mantenerlos en buen estado de limpieza y conservación. 
        3. Informar inmediatamente a mi jefe directo o al responsable de SST sobre cualquier deterioro o pérdida para su reposición. 
        4. No realizar modificaciones a los elementos entregados. 
        Entiendo que el uso de estos elementos es obligatorio y su incumplimiento es falta grave al contrato de trabajo según el Reglamento Interno de Trabajo.
    </div>

    <div class="firmas">
        <div class="firma-box">
            Firma del Trabajador<br>
            C.C. {{ $entrega->empleado->cedula }}
        </div>
        <div class="firma-box">
            Responsable SG-SST / Empresa<br>
            Entrega Autorizada
        </div>
    </div>
</body>
</html>