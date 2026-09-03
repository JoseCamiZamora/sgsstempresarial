<p style="color:#6c757d;margin-bottom:4px;">{{ $companyName }} — Sistema de Gestión de Seguridad y Salud en el Trabajo</p>
<h2>Bienvenido/a, {{ $nombre }}</h2>
<p><b>{{ $companyName }}</b> le ha creado su acceso al <b>Portal de Firmas</b> del SG-SST.</p>
<p>Desde este portal podrá firmar, sin necesidad de crear una cuenta, los documentos y registros pendientes: asistencia a capacitaciones, entrega de EPP, actas y otros formatos que requieran su firma.</p>

<h4>Sus datos de acceso</h4>
<p>
    Cédula: <b>{{ $cedula }}</b><br>
    Código de firma: <b>{{ $code }}</b><br>
    Enlace: <a href="{{ $portalUrl }}">{{ $portalUrl }}</a>
</p>

<h4>Instrucciones</h4>
<ol>
    <li>Ingrese al enlace anterior desde su celular o computador.</li>
    <li>Escriba su número de cédula y el código de firma indicados arriba.</li>
    <li>Revise y firme los documentos pendientes que se le muestren.</li>
</ol>

<p>Guarde este código en un lugar seguro; por seguridad no se volverá a mostrar. Si lo pierde u olvida, solicite al responsable de SST que le genere uno nuevo.</p>

<hr>
<p style="color:#6c757d;font-size:12px;">Este correo fue enviado por <b>{{ $companyName }}</b> a través de Sinergia SST porque usted es empleado registrado de la compañía. Si no reconoce esta empresa como su lugar de trabajo, no ingrese al enlace y comuníquese con su área de talento humano.</p>
