@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <a href="{{ route('empleados.index') }}" class="btn btn-secondary shadow-sm">
            <i class="fa fa-arrow-left mr-1"></i> Volver al Listado
        </a>
        <div>
            <a href="{{ route('empleados.edit', $empleado->id) }}" class="btn btn-warning shadow-sm">
                <i class="fa fa-edit mr-1"></i> Editar Información
            </a>
            <button class="btn btn-danger shadow-sm" onclick="window.print()">
                <i class="fa fa-print mr-1"></i> Imprimir Ficha
            </button>
        </div>
    </div>
    <script>
        @if(session('portal_code_generated'))
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Código de firma generado',
                    html: 'Anótelo y compártalo con el empleado ahora — no podrá volver a verlo.<br><h3 class="mt-2">{{ session("portal_code_generated") }}</h3><p class="small text-muted mb-0">Enlace: {{ route("employee-portal.login") }}</p>@if(session()->has("portal_code_mail_sent"))<p class="small mt-2 mb-0 {{ session("portal_code_mail_sent") ? "text-success" : "text-danger" }}">{{ session("portal_code_mail_sent") ? "✓ Correo con los datos de acceso enviado a ".$empleado->email_personal : "✗ No se pudo enviar el correo automáticamente. Copie el código y compártalo manualmente." }}</p>@endif',
                    confirmButtonText: 'Listo',
                });
            });
        @endif

        document.addEventListener('DOMContentLoaded', function () {
            var regenerateForm = document.getElementById('regenerateCodeForm');
            regenerateForm.addEventListener('submit', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: '{{ $empleado->portalCredential ? "¿Regenerar el código de firma?" : "¿Generar el código de firma?" }}',
                    text: '{{ $empleado->portalCredential ? "El código anterior dejará de funcionar de inmediato." : "Se creará un código nuevo y se enviará al empleado por correo." }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ $empleado->portalCredential ? "Regenerar" : "Generar" }}',
                    cancelButtonText: 'Cancelar',
                }).then(function (r) {
                    if (r.isConfirmed) regenerateForm.submit();
                });
            });
        });
    </script>

    <div class="row">
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center shadow" style="width: 100px; height: 100px; font-size: 40px;">
                            {{ substr($empleado->nombre_completo, 0, 1) }}
                        </div>
                    </div>
                    <h4 class="font-weight-bold mb-0">{{ $empleado->nombre_completo }}</h4>
                    <p class="text-muted">{{ $empleado->cargo }}</p>
                    <span class="badge badge-success px-3 py-2">ACTIVO</span>
                    <hr>
                    <div class="text-left small">
                        <p class="mb-2"><strong><i class="fa fa-id-card mr-2 text-muted"></i>Cédula:</strong> {{ $empleado->cedula }}</p>
                        <p class="mb-2"><strong><i class="fa fa-envelope mr-2 text-muted"></i>Correo:</strong> {{ $empleado->email_personal ?? 'N/A' }}</p>
                        <p class="mb-2"><strong><i class="fa fa-phone mr-2 text-muted"></i>Teléfono:</strong> {{ $empleado->telefono ?? 'N/A' }}</p>
                        <p class="mb-0"><strong><i class="fa fa-tint mr-2 text-danger"></i>RH:</strong> {{ $empleado->rh ?? 'No registrado' }}</p>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4 border-left-danger">
                <div class="card-body">
                    <h6 class="font-weight-bold text-danger text-uppercase mb-3 small">🚨 Contacto de Emergencia</h6>
                    <p class="mb-1"><strong>Nombre:</strong> {{ $empleado->contacto_emergencia_nombre ?? 'N/A' }}</p>
                    <p class="mb-0"><strong>Teléfono:</strong> {{ $empleado->contacto_emergencia_telefono ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="card shadow mb-4 no-print">
                <div class="card-body">
                    <h6 class="font-weight-bold text-uppercase mb-2 small">🔐 Portal de firmas</h6>
                    <p class="small text-muted mb-2">Código de acceso para que el empleado firme sus documentos pendientes desde su celular, sin necesidad de una cuenta del sistema.</p>
                    <div class="input-group input-group-sm mb-3">
                        <input type="text" class="form-control" id="portalFirmasUrl" value="{{ route('employee-portal.login') }}" readonly>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText(document.getElementById('portalFirmasUrl').value).then(()=>Swal.fire({icon:'success',title:'Enlace copiado',timer:1200,showConfirmButton:false}))">
                                <i class="fa fa-copy"></i>
                            </button>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('empleados.portal.regenerate', $empleado->id) }}" id="regenerateCodeForm">
                        @csrf
                        <button class="btn btn-outline-primary btn-sm btn-block">
                            {{ $empleado->portalCredential ? 'Regenerar código de firma' : 'Generar código de firma' }}
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            
            <ul class="nav nav-pills mb-3 bg-white p-2 shadow-sm rounded no-print" id="pills-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="pills-info-tab" data-toggle="pill" href="#pills-info" role="tab">General</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-docs-tab" data-toggle="pill" href="#pills-docs" role="tab">Documentos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="pills-epp-tab" data-toggle="pill" href="#pills-epp" role="tab">Dotación y EPP</a>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                
                <div class="tab-pane fade show active" id="pills-info" role="tabpanel">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 bg-white">
                            <h6 class="m-0 font-weight-bold text-primary">💼 Información Laboral</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small text-uppercase">Área</label>
                                    <p class="h6 font-weight-bold">{{ $empleado->area_departamento ?? 'No asignada' }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small text-uppercase">Contrato</label>
                                    <p class="h6 font-weight-bold text-info">{{ $empleado->tipo_contrato }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small text-uppercase">Ingreso</label>
                                    <p class="h6 font-weight-bold">{{ \Carbon\Carbon::parse($empleado->fecha_ingreso)->format('d/m/Y') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-muted small text-uppercase">Salario</label>
                                    <p class="h6 font-weight-bold text-success">${{ number_format($empleado->salario, 0) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 bg-white"><h6 class="m-0 font-weight-bold text-primary small">🛡️ Seguridad Social</h6></div>
                                <ul class="list-group list-group-flush small">
                                    <li class="list-group-item d-flex justify-content-between"><span>EPS:</span> <strong>{{ $empleado->eps }}</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>ARL:</span> <strong>{{ $empleado->arl }}</strong></li>
                                    <li class="list-group-item d-flex justify-content-between"><span>AFP:</span> <strong>{{ $empleado->afp }}</strong></li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3 bg-white"><h6 class="m-0 font-weight-bold text-primary small">👕 Tallas</h6></div>
                                <div class="card-body py-2">
                                    <div class="row text-center">
                                        <div class="col-4 border-right"><small class="d-block text-muted">Camisa</small><strong>{{ $empleado->talla_camisa ?? '-' }}</strong></div>
                                        <div class="col-4 border-right"><small class="d-block text-muted">Pant.</small><strong>{{ $empleado->talla_pantalon ?? '-' }}</strong></div>
                                        <div class="col-4"><small class="d-block text-muted">Calz.</small><strong>{{ $empleado->talla_calzado ?? '-' }}</strong></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pills-docs" role="tabpanel">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                            <h6 class="m-0 font-weight-bold text-primary">📂 Expediente Digital</h6>
                            <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modalSubir">
                                <i class="fa fa-upload mr-1"></i> Subir
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="bg-light small">
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Archivo</th>
                                        <th class="text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($empleado->documentos as $doc)
                                    <tr>
                                        <td class="align-middle"><span class="badge badge-secondary">{{ $doc->tipo_documento }}</span></td>
                                        <td class="align-middle small">{{ $doc->nombre_archivo }}</td>
                                        <td class="text-right">
                                            <a href="{{ asset('storage/' . $doc->ruta_archivo) }}" target="_blank" class="btn btn-info btn-sm">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="3" class="text-center py-4 text-muted">Sin documentos.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="tab-pane fade" id="pills-epp" role="tabpanel">
                    <div class="card shadow mb-4 border-left-success">
                        <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                            <h6 class="m-0 font-weight-bold text-success">🛠️ Control de Entregas EPP</h6>
                            <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#modalEntregaEpp">
                                <i class="fa fa-plus mr-1"></i> Nueva Entrega
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="bg-light small">
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Elemento</th>
                                        <th>Talla</th>
                                        <th>Motivo</th>
                                        <th class="text-center">Firma</th>
                                        <th class="text-center">Acta Entrega</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($empleado->entregasEpp as $entrega)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($entrega->fecha_entrega)->format('d/m/Y') }}</td>
                                        <td><strong>{{ $entrega->epp->nombre }}</strong></td>
                                        <td><span class="badge badge-primary">{{ $entrega->talla_entregada }}</span></td>
                                        <td><small>{{ $entrega->motivo }}</small></td>
                                        <td class="text-center">
                                            @if($entrega->signature_status === 'signed')
                                                <span class="badge badge-success"><i class="fa fa-check mr-1"></i>Firmado</span>
                                            @else
                                                <span class="badge badge-warning text-dark"><i class="fa fa-clock mr-1"></i>Pendiente</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('entrega-epp.pdf', $entrega->id) }}"
                                            target="_blank"
                                            class="btn btn-sm btn-outline-danger border-0"
                                            title="Descargar Acta">
                                                <i class="fa fa-file-pdf"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-4 text-muted">No hay registros de entrega.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-4"><div class="card shadow-sm"><div class="card-header"><b>Formación y Competencias SST</b></div><div class="card-body"><h6>Ruta vigente</h6>@forelse($trainingRoute as $route)<div><span class="badge badge-{{ $route['status']==='completed'?'success':($route['status']==='expiring'?'warning':'danger') }}">{{ $route['status'] }}</span> {{ $route['requirement']->topic->name }} — {{ $route['requirement']->requirement_type }}</div>@empty<p class="text-muted">Sin requisitos configurados.</p>@endforelse<hr><h6>Evaluaciones</h6><table class="table table-sm"><tr><th>Fecha</th><th>Tema</th><th>Resultado</th></tr>@foreach($trainingAttempts as $attempt)<tr><td>{{ $attempt->submitted_at?->format('d/m/Y') }}</td><td>{{ $attempt->evaluation->topic->name }}</td><td>{{ $attempt->percentage_score }}% — {{ $attempt->result==='passed'?'Aprobado':'Requiere refuerzo' }}</td></tr>@endforeach</table><p>Refuerzos pendientes: <b>{{ $trainingReinforcements->where('status','pending')->count() }}</b> · Credenciales registradas: <b>{{ $trainingCredentials->count() }}</b></p></div></div></div>

            </div> </div>
    </div>
</div>

<div class="modal fade" id="modalSubir" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="{{ route('empleados.subirDoc', $empleado->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-primary text-white"><h5>Cargar Documento</h5></div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Tipo</label>
                        <select name="tipo_documento" class="form-control" required>
                            <option value="Identidad">Cédula</option>
                            <option value="Contrato">Contrato</option>
                            <option value="Salud">Certificado Médico</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Archivo</label>
                        <input type="file" name="archivo" class="form-control-file" required>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary shadow">Subir Ahora</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="modalEntregaEpp" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form action="{{ route('entrega-epp.store') }}" method="POST">
            @csrf
            <input type="hidden" name="empleado_id" value="{{ $empleado->id }}">
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-success text-white"><h5>🛠️ Registrar Entrega de Dotación</h5></div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-7 form-group">
                            <label class="font-weight-bold">Elemento</label>
                            <select name="epp_id" class="form-control" id="select_epp" required>
                                <option value="">-- Buscar en catálogo --</option>
                                @foreach($catalogEpps as $epp)
                                    <option value="{{ $epp->id }}" data-categoria="{{ $epp->categoria }}">{{ $epp->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-5 form-group">
                            <label class="font-weight-bold">Fecha</label>
                            <input type="date" name="fecha_entrega" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Talla Entregada</label>
                            <input type="text" name="talla_entregada" id="talla_sugerida" class="form-control" required>
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="font-weight-bold">Motivo</label>
                            <select name="motivo" class="form-control">
                                <option value="Dotación Inicial">Dotación Inicial</option>
                                <option value="Reposición">Reposición</option>
                                <option value="Deterioro">Deterioro</option>
                            </select>
                        </div>
                        <input type="hidden" name="cantidad" value="1">
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-success shadow">Confirmar Entrega</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPTS --}}
<script>
    // Lógica para autocompletar tallas según EPP seleccionado
    document.getElementById('select_epp')?.addEventListener('change', function() {
        const categoria = this.options[this.selectedIndex].getAttribute('data-categoria');
        const inputTalla = document.getElementById('talla_sugerida');

        if (categoria === 'Pies') {
            inputTalla.value = "{{ $empleado->talla_calzado }}";
        } else if (categoria === 'Cabeza' || categoria === 'Auditiva' || categoria === 'Ojos/Cara') {
            inputTalla.value = "Única";
        } else {
            inputTalla.value = "{{ $empleado->talla_camisa }}";
        }
    });

    // Alerta de éxito si existe sesión
    @if(session('success'))
        Swal.fire({ icon: 'success', title: '¡Hecho!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false });
    @endif
</script>

<style>
    .nav-pills .nav-link.active { background-color: #4e73df !important; shadow: 0 2px 4px rgba(0,0,0,0.1); }
    @media print {
        .no-print, .btn, .nav, .modal { display: none !important; }
        .card { border: 1px solid #ddd !important; shadow: none !important; }
        .tab-pane { display: block !important; opacity: 1 !important; }
    }
</style>
@endsection
