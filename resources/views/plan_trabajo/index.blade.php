@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <a href="{{ route('home') }}" class="text-decoration-none text-secondary mb-2 d-inline-block font-weight-bold">
            <i class="fa fa-arrow-left mr-1"></i> Volver al Dashboard
        </a>
    <div class="row mb-4 align-items-center">
        
        <div class="col-md-6">
            <h1 class="h3 mb-0 text-gray-800"><i class="fa fa-calendar-alt text-primary mr-2"></i>Plan de Trabajo Anual - {{ $plan->anio }}</h1>
            <p class="text-muted small">Estado: <span class="badge badge-{{ $plan->estado == 'Aprobado' ? 'success' : 'warning' }}">{{ $plan->estado }}</span></p>
        </div>
        <div class="col-md-6 text-right">
            <button class="btn btn-primary shadow-sm" data-toggle="modal" data-target="#modalNuevaActividad">
                <i class="fa fa-plus fa-sm text-white-50"></i> Nueva Actividad
            </button>
            <a href="{{ route('plan-trabajo.exportar') }}" class="btn btn-success shadow-sm">
                <i class="fa fa-file-excel mr-1"></i> Exportar
            </a>
        </div>
        <div class="dropdown d-inline-block">
            <button class="btn btn-outline-secondary dropdown-toggle shadow-sm" type="button" data-toggle="dropdown">
                <i class="fa fa-calendar mr-1"></i> Año: {{ $plan->anio }}
            </button>
            <div class="dropdown-menu">
                @foreach($añosDisponibles as $a)
                    <a class="dropdown-item" href="{{ route('plan-trabajo.index', ['anio' => $a]) }}">{{ $a }}</a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Resumen de Cumplimiento --}}
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-left-info">
                <div class="card-body py-2">
                    <div class="row align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Progreso Global de Ejecución</div>
                            <div class="row no-gutters align-items-center">
                                <div class="col-auto">
                                    <div class="h5 mb-0 mr-3 font-weight-bold text-gray-800">{{ $porcentajeGlobal }}%</div>
                                </div>
                                <div class="col">
                                    <div class="progress progress-sm mr-2">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $porcentajeGlobal }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Actividades Vencidas</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $actividadesVencidas }} Pendientes</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-exclamation-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Tareas este Mes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $actividadesMesActual }} Programadas</div>
                        </div>
                        <div class="col-auto">
                            <i class="fa fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-2">Eficacia por Fase PHVA</div>
                    <div class="row no-gutters align-items-center">
                        @foreach($statsPHVA as $fase => $porcentaje)
                            <div class="col text-center border-right">
                                <div class="text-muted small">{{ $fase }}</div>
                                <div class="font-weight-bold text-dark">{{ $porcentaje }}%</div>
                                <div class="progress progress-sm mx-2 mt-1">
                                    <div class="progress-bar bg-success" style="width: {{ $porcentaje }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <div class="card shadow mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 text-center" style="font-size: 0.85rem;">
                    <thead class="bg-light">
                        <tr>
                            <th rowspan="2" class="align-middle" style="width: 50px;">Fase</th>
                            <th rowspan="2" class="align-middle text-left" style="min-width: 250px;">Actividad</th>
                            <th rowspan="2" class="align-middle" style="width: 150px;">Responsable</th>
                            <th colspan="12" class="py-1">Cronograma Mensual (P: Programado | E: Ejecutado)</th>
                            <th rowspan="2" class="align-middle" style="width: 60px;">%</th>
                        </tr>
                        <tr class="bg-white">
                            @foreach(['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'] as $mes)
                                <th class="py-1 px-0" style="width: 40px;">{{ $mes }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php $fases = ['Planear', 'Hacer', 'Verificar', 'Actuar']; @endphp
                        
                        @foreach($fases as $fase)
                            {{-- Cabecera de la Fase --}}
                            <tr class="bg-light font-weight-bold text-left">
                                <td colspan="15" class="py-2 px-3 text-primary">
                                    <i class="fa fa-arrow-circle-right mr-1"></i> FASE: {{ strtoupper($fase) }}
                                </td>
                            </tr>

                            @forelse($actividades->where('fase_phva', $fase) as $actividad)
                                <tr>
                                    <td class="align-middle"><small class="badge badge-outline-secondary">{{ substr($fase, 0, 1) }}</small></td>
                                    <td class="text-left align-middle font-weight-bold">
                                        {{ $actividad->actividad }}
                                        <button class="btn btn-sm btn-link text-primary float-right p-0" 
                                                onclick="abrirModalEditar(
                                                    {{ $actividad->id }}, 
                                                    '{{ $actividad->fase_phva }}', 
                                                    '{{ htmlspecialchars($actividad->actividad, ENT_QUOTES) }}', 
                                                    '{{ htmlspecialchars($actividad->objetivo_especifico, ENT_QUOTES) }}', 
                                                    {{ $actividad->responsable_id }}, 
                                                    '{{ htmlspecialchars($actividad->recursos_necesarios, ENT_QUOTES) }}',
                                                    {{ $actividad->cronograma->pluck('mes') }},
                                                    {{ $actividad->cronograma->where('ejecutado', true)->pluck('mes') }} {{-- ESTE ES EL NUEVO PARÁMETRO --}}
                                                )" title="Editar / Reprogramar">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </td>
                                    <td class="align-middle text-muted">{{ $actividad->responsable->name }}</td>
                                    
                                    {{-- Celdas de los Meses --}}
                                    @for($m = 1; $m <= 12; $m++)
                                        @php 
                                            $prog = $actividad->cronograma->where('mes', $m)->first();
                                        @endphp
                                        <td class="p-0 align-middle" style="height: 50px;">
                                            @if($prog)
                                                <div class="d-flex flex-column h-100">
                                                    {{-- Letra P: Programado --}}
                                                    <div class="flex-fill d-flex align-items-center justify-content-center bg-primary text-white" 
                                                         style="font-size: 0.65rem; border-bottom: 1px solid rgba(255,255,255,0.2);" title="Programado">
                                                        P
                                                    </div>
                                                    {{-- Letra E: Ejecutado --}}
                                                   <div class="flex-fill d-flex align-items-center justify-content-center {{ $prog->ejecutado ? 'bg-success text-white' : 'bg-white text-muted' }}" 
                                                        style="font-size: 0.65rem; cursor: pointer; position: relative;" 
                                                        title="{{ $prog->ejecutado ? 'Ver Ejecución' : 'Pendiente de ejecutar' }}">
                                                        
                                                        @if($prog->ejecutado)
                                                            {{-- Si ya se hizo, mostramos la E y si tiene PDF un pequeño clip --}}
                                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center" 
                                                                onclick="abrirModalDetalle({{ $prog->id }}, '{{ $prog->fecha_ejecucion_real }}', '{{ $prog->observaciones }}', '{{ $prog->evidencia_pdf ? asset('storage/'.$prog->evidencia_pdf) : '' }}')">
                                                                E @if($prog->evidencia_pdf) <i class="fa fa-paperclip ml-1" style="font-size: 0.5rem;"></i> @endif
                                                            </div>
                                                        @else
                                                            {{-- Si no se ha hecho, clic para cerrar --}}
                                                            <div class="w-100 h-100 d-flex align-items-center justify-content-center" onclick="abrirModalCierre({{ $prog->id }})">
                                                                -
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                    @endfor

                                    {{-- % de cumplimiento de esta actividad específica --}}
                                    <td class="align-middle font-weight-bold text-primary">
                                        @php
                                            $totalP = $actividad->cronograma->where('programado', true)->count();
                                            $totalE = $actividad->cronograma->where('ejecutado', true)->count();
                                            $cumplimiento = ($totalP > 0) ? round(($totalE / $totalP) * 100) : 0;
                                        @endphp
                                        {{ $cumplimiento }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="15" class="text-muted small py-2">No hay actividades registradas en esta fase.</td>
                                </tr>
                            @endforelse
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalNuevaActividad" tabindex="-1" role="dialog" aria-labelledby="modalNuevaActividadTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold" id="modalNuevaActividadTitle">
                    <i class="fa fa-plus-circle mr-2"></i>Programar Nueva Actividad
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            
            <form action="{{ route('actividades-plan.store') }}" method="POST">
                @csrf
                <input type="hidden" name="plan_trabajo_id" value="{{ $plan->id }}">
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="font-weight-bold text-gray-800 small">Fase PHVA *</label>
                            <select name="fase_phva" class="form-control" required>
                                <option value="">Seleccione...</option>
                                <option value="Planear">Planear</option>
                                <option value="Hacer">Hacer</option>
                                <option value="Verificar">Verificar</option>
                                <option value="Actuar">Actuar</option>
                            </select>
                        </div>
                        
                        <div class="col-md-8 mb-3">
                            <label class="font-weight-bold text-gray-800 small">Nombre de la Actividad *</label>
                            <input type="text" name="actividad" class="form-control" placeholder="Ej: Inspección de extintores" required>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="font-weight-bold text-gray-800 small">Objetivo Específico</label>
                            <textarea name="objetivo_especifico" class="form-control" rows="2" placeholder="¿Qué se busca lograr con esta actividad?"></textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-800 small">Responsable *</label>
                            <select name="responsable_id" class="form-control" required>
                                <option value="">Seleccione un responsable...</option>
                                @foreach($usuarios as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="font-weight-bold text-gray-800 small">Recursos Necesarios</label>
                            <input type="text" name="recursos_necesarios" class="form-control" placeholder="Físicos, humanos, financieros...">
                        </div>
                    </div>

                    <hr>
                    
                    <h6 class="font-weight-bold text-primary mb-3"><i class="fa fa-calendar-check-o mr-1"></i> Cronograma de Ejecución</h6>
                    <p class="small text-muted mb-2">Seleccione los meses en los que se debe ejecutar esta actividad:</p>
                    
                    <div class="d-flex flex-wrap justify-content-between bg-light p-3 border rounded">
                        @php
                            $meses = [
                                1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 
                                5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Ago', 
                                9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic'
                            ];
                        @endphp
                        
                        @foreach($meses as $num => $nombre)
                            <div class="custom-control custom-checkbox mr-2 mb-2">
                                <input type="checkbox" class="custom-control-input" id="mes_{{ $num }}" name="meses_programados[]" value="{{ $num }}">
                                <label class="custom-control-label font-weight-bold text-gray-700" for="mes_{{ $num }}">{{ $nombre }}</label>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary shadow-sm" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary shadow-sm"><i class="fa fa-save mr-1"></i> Guardar Actividad</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalCerrarActividad" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold"><i class="fa fa-upload mr-2"></i>Cargar Evidencia de Ejecución</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formCerrarActividad" action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info small">
                        <i class="fa fa-info-circle mr-1"></i> Vas a marcar esta actividad como <strong>Ejecutada</strong>. Adjunta el soporte legal en PDF.
                    </div>
                    
                    <div class="form-group">
                        <label class="small font-weight-bold">Fecha de Ejecución Real *</label>
                        <input type="date" name="fecha_ejecucion_real" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="form-group">
                        <label class="small font-weight-bold">Evidencia (PDF) <span class="text-muted font-weight-normal">(Opcional)</span></label>
                        <div class="custom-file">
                            <input type="file" name="evidencia_pdf" class="custom-file-input" id="evidencia_pdf" accept=".pdf">
                            <label class="custom-file-label" for="evidencia_pdf">Seleccionar archivo...</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="small font-weight-bold">Observaciones / Hallazgos</label>
                        <textarea name="observaciones" class="form-control" rows="3" placeholder="Describe brevemente el resultado de la actividad..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-success btn-sm">Finalizar Actividad</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modalVerDetalle" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content border-0">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title font-weight-bold"><i class="fa fa-info-circle mr-2"></i>Detalle de Ejecución</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-6">
                        <label class="small font-weight-bold">Fecha Real:</label>
                        <p id="det_fecha" class="text-muted">-</p>
                    </div>
                    <div class="col-6">
                        <label class="small font-weight-bold">Estado:</label>
                        <p><span class="badge badge-success">EJECUTADO</span></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="small font-weight-bold">Observaciones / Hallazgos:</label>
                    <p id="det_obs" class="text-muted bg-light p-2 rounded" style="min-height: 50px;">-</p>
                </div>
                <div id="btn_pdf_container">
                    {{-- Aquí se inyectará el botón del PDF con JS --}}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Aquí irían tus modales de Nueva Actividad y Cierre de Actividad --}}

<style>
    /* Estilo para que la tabla parezca Excel */
    .table-bordered th, .table-bordered td {
        border: 1px solid #dee2e6 !important;
        vertical-align: middle;
    }
    .badge-outline-secondary {
        border: 1px solid #858796;
        color: #858796;
        background: transparent;
    }
</style>
<script>
    function abrirModalCierre(id) {
        let url = "{{ route('plan-trabajo.cerrar-mes', ':id') }}";
        url = url.replace(':id', id);
        document.getElementById('formCerrarActividad').action = url;
        
        if (typeof bootstrap !== 'undefined') {
            var myModal = new bootstrap.Modal(document.getElementById('modalCerrarActividad'));
            myModal.show();
        } else {
            let btnOculto = document.createElement('button');
            btnOculto.setAttribute('data-toggle', 'modal');
            btnOculto.setAttribute('data-target', '#modalCerrarActividad');
            btnOculto.style.display = 'none';
            document.body.appendChild(btnOculto);
            btnOculto.click();
            btnOculto.remove();
        }
    }

    // NUEVO CÓDIGO EN JAVASCRIPT PURO PARA EL NOMBRE DEL ARCHIVO
    document.addEventListener("DOMContentLoaded", function() {
        let fileInputs = document.querySelectorAll('.custom-file-input');
        
        fileInputs.forEach(function(input) {
            input.addEventListener('change', function(e) {
                // Verificamos que sí se haya seleccionado un archivo
                if (e.target.files.length > 0) {
                    let fileName = e.target.files[0].name;
                    let nextLabel = e.target.nextElementSibling;
                    
                    // Cambiamos el texto "Seleccionar archivo..." por el nombre real
                    if (nextLabel) {
                        nextLabel.innerText = fileName;
                    }
                }
            });
        });
    });
    function abrirModalDetalle(id, fecha, obs, pdfUrl) {
        document.getElementById('det_fecha').innerText = fecha;
        document.getElementById('det_obs').innerText = obs || 'Sin observaciones registradas.';
        
        let container = document.getElementById('btn_pdf_container');
        if(pdfUrl) {
            container.innerHTML = `<a href="${pdfUrl}" target="_blank" class="btn btn-primary btn-block">
                                        <i class="fa fa-file-pdf-o mr-2"></i> Ver Evidencia PDF
                                </a>`;
        } else {
            container.innerHTML = `<div class="alert alert-secondary small text-center">No se cargó evidencia física.</div>`;
        }

        // Abrimos el modal con el truco de JS puro para evitar errores de jQuery
        if (typeof bootstrap !== 'undefined') {
            var myModal = new bootstrap.Modal(document.getElementById('modalVerDetalle'));
            myModal.show();
        } else {
            let btnDet = document.createElement('button');
            btnDet.setAttribute('data-toggle', 'modal');
            btnDet.setAttribute('data-target', '#modalVerDetalle');
            btnDet.style.display = 'none';
            document.body.appendChild(btnDet);
            btnDet.click();
            btnDet.remove();
        }
    }
    function abrirModalEditar(id, fase, nombre, objetivo, responsable_id, recursos, mesesProgramados, mesesEjecutados) {
        document.getElementById('modalNuevaActividadTitle').innerHTML = '<i class="fa fa-edit mr-2"></i>Editar y Reprogramar Actividad';
        
        let form = document.querySelector('#modalNuevaActividad form');
        let urlUpdate = "{{ route('actividades-plan.update', ':id') }}".replace(':id', id);
        form.action = urlUpdate;
        
        if (!document.getElementById('method_put')) {
            let hiddenMethod = document.createElement('input');
            hiddenMethod.type = 'hidden';
            hiddenMethod.name = '_method';
            hiddenMethod.value = 'PUT';
            hiddenMethod.id = 'method_put';
            form.appendChild(hiddenMethod);
        }

        form.querySelector('select[name="fase_phva"]').value = fase;
        form.querySelector('input[name="actividad"]').value = nombre;
        form.querySelector('textarea[name="objetivo_especifico"]').value = objetivo;
        form.querySelector('select[name="responsable_id"]').value = responsable_id;
        form.querySelector('input[name="recursos_necesarios"]').value = recursos;

        // Limpiar estilos y checkboxes previos
        document.querySelectorAll('input[name="meses_programados[]"]').forEach(chk => {
            chk.checked = false;
            chk.disabled = false; // Quitar bloqueos anteriores
            let label = chk.nextElementSibling;
            label.innerHTML = label.innerText.replace(' 🔒', ''); // Limpiar candado
            label.classList.remove('text-success');
        });

        // Limpiar inputs ocultos anteriores
        document.querySelectorAll('.hidden-mes-ejecutado').forEach(el => el.remove());

        // Marcar y bloquear
        mesesProgramados.forEach(mes => {
            let checkbox = document.getElementById('mes_' + mes);
            if(checkbox) {
                checkbox.checked = true;
                
                // Si el mes ya se ejecutó, lo bloqueamos visualmente
                if(mesesEjecutados.includes(mes)) {
                    checkbox.disabled = true;
                    let label = checkbox.nextElementSibling;
                    label.innerHTML += ' <i class="fa fa-lock" title="Ya ejecutado"></i>';
                    label.classList.add('text-success');

                    // Como los inputs 'disabled' no se envían por POST, creamos un input oculto para que Laravel no lo pierda
                    let hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'meses_programados[]';
                    hiddenInput.value = mes;
                    hiddenInput.classList.add('hidden-mes-ejecutado');
                    form.appendChild(hiddenInput);
                }
            }
        });

        if (typeof bootstrap !== 'undefined') {
            new bootstrap.Modal(document.getElementById('modalNuevaActividad')).show();
        } else {
            let btn = document.createElement('button');
            btn.setAttribute('data-toggle', 'modal');
            btn.setAttribute('data-target', '#modalNuevaActividad');
            btn.style.display = 'none';
            document.body.appendChild(btn);
            btn.click();
            btn.remove();
        }
    }
</script>
@endsection