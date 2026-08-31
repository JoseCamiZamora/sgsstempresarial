@extends('layouts.app')

@section('content')
<div class="container my-4">
    <a href="{{ route('committees.index') }}" class="text-secondary font-weight-bold"><i class="fa fa-arrow-left mr-1"></i> Volver a comités</a>
    <div class="card shadow border-0 mt-3">
        <div class="card-header bg-white"><h4 class="text-primary font-weight-bold mb-0">Conformación: {{ $committeeType->label() }}</h4></div>
        <div class="card-body">
            <div class="progress mb-4" style="height:8px"><div id="wizardProgress" class="progress-bar" style="width:16.66%"></div></div>
            <form action="{{ route('committees.formations.store') }}" method="POST" enctype="multipart/form-data" id="committeeWizard">
                @csrf <input type="hidden" name="type" value="{{ $committeeType->value }}">

                <section class="wizard-step" data-step="1">
                    <h5 class="font-weight-bold">1. Tipo de comité</h5>
                    <div class="alert alert-primary"><strong>{{ $committeeType->label() }}</strong><br>Empresa: {{ $company->razon_social }}</div>
                </section>

                <section class="wizard-step d-none" data-step="2">
                    <h5 class="font-weight-bold">2. Verificación normativa</h5>
                    <div class="card bg-light border-0"><div class="card-body">
                        <p>Trabajadores activos registrados: <strong>{{ $workersCount }}</strong></p>
                        <p>Figura aplicable: <strong>{{ $composition['mode'] === 'VIGIA_SST' ? 'Vigía SST' : $committeeType->label() }}</strong></p>
                        <p>Composición: empleador {{ $composition['employer_principals'] }} principal(es) y {{ $composition['employer_substitutes'] }} suplente(s); trabajadores {{ $composition['worker_principals'] }} principal(es) y {{ $composition['worker_substitutes'] }} suplente(s).</p>
                        <p class="mb-0 small text-muted">{{ $composition['regulation_reference'] }}</p>
                    </div></div>
                    @if($composition['boundary_interpretation'])<div class="alert alert-warning mt-3 small">{{ $composition['boundary_interpretation'] }}</div>@endif
                </section>

                <section class="wizard-step d-none" data-step="3">
                    <h5 class="font-weight-bold">3. Período y convocatoria</h5>
                    <div class="row">
                        <div class="col-md-6 form-group"><label>Inicio del período</label><input type="date" name="start_date" class="form-control" value="{{ old('start_date', now()->toDateString()) }}" required></div>
                        <div class="col-md-6 form-group"><label>Fin del período</label><input type="date" name="end_date" class="form-control" value="{{ old('end_date', now()->addYears($composition['period_years'])->subDay()->toDateString()) }}" required></div>
                        <div class="col-12 form-group"><label>Título</label><input name="title" class="form-control" value="{{ old('title', 'Convocatoria de conformación '.$committeeType->label()) }}" required></div>
                        <div class="col-12 form-group"><label>Descripción</label><textarea name="description" class="form-control">{{ old('description') }}</textarea></div>
                        <div class="col-md-6 form-group"><label>Apertura convocatoria</label><input type="date" name="call_start_date" value="{{ old('call_start_date') }}" class="form-control" required></div>
                        <div class="col-md-6 form-group"><label>Cierre convocatoria</label><input type="date" name="call_end_date" value="{{ old('call_end_date') }}" class="form-control" required></div>
                        <div class="col-md-6 form-group"><label>Inicio inscripción</label><input type="datetime-local" name="candidate_registration_start" value="{{ old('candidate_registration_start') }}" class="form-control" required></div>
                        <div class="col-md-6 form-group"><label>Cierre inscripción</label><input type="datetime-local" name="candidate_registration_end" value="{{ old('candidate_registration_end') }}" class="form-control" required></div>
                        <div class="col-md-6 form-group"><label>Apertura prevista elección</label><input type="datetime-local" name="election_start_at" value="{{ old('election_start_at') }}" class="form-control" required></div>
                        <div class="col-md-6 form-group"><label>Cierre previsto elección</label><input type="datetime-local" name="election_end_at" value="{{ old('election_end_at') }}" class="form-control" required></div>
                        <div class="col-md-6 form-group"><label>Requisitos</label><textarea name="requirements" class="form-control">{{ old('requirements') }}</textarea></div>
                        <div class="col-md-6 form-group"><label>Observaciones</label><textarea name="notes" class="form-control">{{ old('notes') }}</textarea></div>
                    </div>
                </section>

                <section class="wizard-step d-none" data-step="4">
                    <h5 class="font-weight-bold">4. Representantes del empleador</h5>
                    @if($composition['employer_principals'] + $composition['employer_substitutes'] === 0)
                        <div class="alert alert-info">La figura de Vigía SST no requiere representantes del empleador en este proceso.</div>
                    @else
                        @php
                            $memberIndex = 0;
                        @endphp
                        @foreach(['principal' => $composition['employer_principals'], 'substitute' => $composition['employer_substitutes']] as $memberType => $count)
                            @for($i=0; $i<$count; $i++)
                            <div class="border rounded p-3 mb-3">
                                <label class="font-weight-bold">{{ $memberType === 'principal' ? 'Principal' : 'Suplente' }} {{ $i+1 }}</label>
                                <input type="hidden" name="employer_members[{{ $memberIndex }}][member_type]" value="{{ $memberType }}">
                                <select name="employer_members[{{ $memberIndex }}][employee_id]" class="form-control mb-2" required><option value="">Seleccione empleado</option>@foreach($employees as $employee)<option value="{{ $employee->id }}" {{ (string) old("employer_members.$memberIndex.employee_id") === (string) $employee->id ? 'selected' : '' }}>{{ $employee->nombre_completo }} — {{ $employee->cargo }}</option>@endforeach</select>
                                <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="member-confirm-{{ $memberIndex }}" name="employer_members[{{ $memberIndex }}][eligibility_confirmed]" value="1" {{ old("employer_members.$memberIndex.eligibility_confirmed") ? 'checked' : '' }} required><label class="custom-control-label small" for="member-confirm-{{ $memberIndex }}">Confirmo que el empleado cumple las condiciones de elegibilidad aplicables.</label></div>
                            </div>
                            @php
                                $memberIndex++;
                            @endphp
                            @endfor
                        @endforeach
                    @endif
                    @if($committeeType->value === 'CCL')<div class="alert alert-warning small">No existe un módulo confidencial de quejas para comprobar automáticamente la restricción del año anterior. La confirmación administrativa queda registrada con el usuario actual.</div>@endif
                </section>

                <section class="wizard-step d-none" data-step="5">
                    <div class="d-flex justify-content-between"><h5 class="font-weight-bold">5. Candidatos</h5><button type="button" class="btn btn-sm btn-outline-primary" id="addCandidate">Agregar candidato</button></div>
                    <p class="text-muted small">Puede dejarlos para después. Solo es posible registrarlos si la ventana de inscripción está abierta.</p>
                    @if(old('candidates'))
                        <div class="alert alert-warning small">Los datos de los candidatos fueron recuperados. Por seguridad, el navegador no permite restaurar archivos seleccionados; vuelva a elegir únicamente las fotografías antes de enviar.</div>
                    @endif
                    <div id="candidateRows"></div>
                </section>

                <section class="wizard-step d-none" data-step="6">
                    <h5 class="font-weight-bold">6. Resumen</h5>
                    <div class="alert alert-success">Se guardará el proceso con snapshot de {{ $workersCount }} trabajadores, norma {{ $composition['regulation_reference'] }} y estado Configurado. No se habilitará votación en esta fase.</div>
                </section>

                <hr><div class="d-flex justify-content-between"><button type="button" id="prevStep" class="btn btn-secondary d-none">Anterior</button><button type="button" id="nextStep" class="btn btn-primary ml-auto">Siguiente</button><button type="submit" id="submitWizard" class="btn btn-success d-none">Guardar proceso de conformación</button></div>
            </form>
        </div>
    </div>
</div>

<template id="candidateTemplate"><div class="candidate-row border rounded p-3 mb-3"><div class="d-flex justify-content-between"><strong>Candidato</strong><button type="button" class="btn btn-sm btn-link text-danger remove-candidate">Retirar</button></div><div class="row"><div class="col-md-6 form-group"><label>Empleado</label><select data-name="employee_id" class="form-control" required><option value="">Seleccione</option>@foreach($employees as $employee)<option value="{{ $employee->id }}">{{ $employee->nombre_completo }}</option>@endforeach</select></div><div class="col-md-6 form-group"><label>Fotografía</label><input data-name="photo" type="file" accept="image/jpeg,image/png,image/webp" class="form-control-file" required></div><div class="col-md-6 form-group"><label>Perfil breve</label><textarea data-name="short_profile" class="form-control"></textarea></div><div class="col-md-6 form-group"><label>Propuesta</label><textarea data-name="proposal" class="form-control"></textarea></div><div class="col-12 custom-control custom-checkbox ml-3"><input data-name="eligibility_confirmed" type="checkbox" value="1" class="custom-control-input" required><label class="custom-control-label small">Confirmo la elegibilidad y la validación administrativa.</label></div></div></div></template>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const oldCandidates = @json(old('candidates', []));
    let step = @if($errors->has('candidates') || $errors->has('candidates.*')) 5 @elseif($errors->has('employer_members') || $errors->has('employer_members.*') || $errors->has('employees')) 4 @elseif($errors->any()) 3 @else 1 @endif;
    let candidateIndex=0; const max=6;
    const render=()=>{ document.querySelectorAll('.wizard-step').forEach(el=>el.classList.toggle('d-none', Number(el.dataset.step)!==step)); document.getElementById('prevStep').classList.toggle('d-none',step===1); document.getElementById('nextStep').classList.toggle('d-none',step===max); document.getElementById('submitWizard').classList.toggle('d-none',step!==max); document.getElementById('wizardProgress').style.width=(step/max*100)+'%'; };
    document.getElementById('nextStep').addEventListener('click',()=>{ const current=document.querySelector('.wizard-step:not(.d-none)'); if([...current.querySelectorAll('[required]')].some(i=>!i.checkValidity())) { current.querySelector('[required]:invalid').reportValidity(); return; } step++; render(); });
    document.getElementById('prevStep').addEventListener('click',()=>{step--;render();});
    const addCandidate = (data = {}) => {
        const index = candidateIndex++;
        const fragment=document.getElementById('candidateTemplate').content.cloneNode(true);
        fragment.querySelectorAll('[data-name]').forEach(el=>{
            const field=el.dataset.name;
            el.name=`candidates[${index}][${field}]`;
            if(field==='eligibility_confirmed'){
                el.id=`candidate-confirm-${index}`;
                el.nextElementSibling.setAttribute('for',el.id);
                el.checked=Boolean(data[field]);
            } else if(field !== 'photo' && data[field] !== undefined) {
                el.value=data[field] ?? '';
            }
        });
        document.getElementById('candidateRows').appendChild(fragment);
    };
    document.getElementById('addCandidate').addEventListener('click',()=>addCandidate());
    document.getElementById('candidateRows').addEventListener('click',e=>{if(e.target.classList.contains('remove-candidate'))e.target.closest('.candidate-row').remove();});
    oldCandidates.forEach(candidate => addCandidate(candidate));
    render();
});
</script>
@endsection
