@extends('layouts.app')

@section('content')
<div class="container my-4">
    <a href="{{ route('committees.operations.show', $meeting->committee) }}" class="btn btn-outline-secondary mb-3">← Regresar</a>
    <h2>Reunión: {{ $meeting->subject }}</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Revise la información:</strong>
            <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <p>Estado: <b>{{ $meeting->status }}</b> · {{ $meeting->meeting_date->format('d/m/Y') }} {{ substr((string) $meeting->start_time, 0, 5) }}</p>

    @php
        $principals = $meeting->attendees->filter(fn($attendee) => $attendee->member?->member_type?->value === 'principal');
        $activePrincipals = $meeting->period->members->whereIn('status', ['active', 'designated'])->filter(fn($member) => $member->member_type->value === 'principal');
    @endphp

    <div class="card mb-3">
        <div class="card-header">Asistencia y quórum</div>
        <div class="card-body">
            <div class="alert alert-info">
                Si un principal no puede asistir, márquelo como <b>Ausente</b> o <b>Excusado</b>.
                Luego marque al suplente como <b>Reemplaza a un principal</b> y seleccione a quién reemplaza.
            </div>
            <form method="POST" action="{{ route('committees.meetings.attendance', $meeting) }}">
                @csrf
                @method('PUT')
                <div class="table-responsive">
                    <table class="table">
                        <thead><tr><th>Integrante</th><th>Cargo</th><th>Condición</th><th>Asistencia</th><th>Hora</th><th>Reemplaza a</th></tr></thead>
                        <tbody>
                        @foreach($meeting->attendees as $index => $attendee)
                            @php
                                $isSubstitute = $attendee->member?->member_type?->value === 'substitute';
                                $status = old("attendees.$index.attendance_status", $attendee->attendance_status);
                            @endphp
                            <tr>
                                <td>{{ $attendee->member?->employee?->nombre_completo }}</td>
                                <td>{{ $attendee->member?->roles->pluck('role')->map(fn($role) => $role === 'president' ? 'Presidente' : 'Secretario')->implode(', ') ?: 'Integrante' }}</td>
                                <td>{{ $isSubstitute ? 'Suplente' : 'Principal' }}</td>
                                <td>
                                    <input type="hidden" name="attendees[{{ $index }}][id]" value="{{ $attendee->id }}">
                                    <select name="attendees[{{ $index }}][attendance_status]" class="form-control attendance-status" data-substitute="{{ $isSubstitute ? 1 : 0 }}">
                                        <option value="present" @selected($status === 'present')>Presente</option>
                                        <option value="absent" @selected($status === 'absent')>Ausente</option>
                                        <option value="excused" @selected($status === 'excused')>Excusado</option>
                                        @if($isSubstitute)<option value="replacement" @selected($status === 'replacement')>Reemplaza a un principal</option>@endif
                                    </select>
                                </td>
                                <td><input type="time" name="attendees[{{ $index }}][arrival_time]" class="form-control" value="{{ substr((string) old("attendees.$index.arrival_time", $attendee->arrival_time), 0, 5) }}"></td>
                                <td>
                                    @if($isSubstitute)
                                        <select name="attendees[{{ $index }}][replaces_member_id]" class="form-control replacement-select" @disabled($status !== 'replacement')>
                                            <option value="">Seleccione el principal</option>
                                            @foreach($principals as $principal)
                                                <option value="{{ $principal->committee_member_id }}" @selected((string) old("attendees.$index.replaces_member_id", $attendee->replaces_member_id) === (string) $principal->committee_member_id)>
                                                    {{ $principal->member?->employee?->nombre_completo }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <span class="text-muted">No aplica</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <button class="btn btn-primary">Guardar asistencia y calcular quórum</button>
            </form>

            @if($meeting->quorum_required !== null)
                <div class="alert alert-{{ $meeting->has_quorum ? 'success' : 'danger' }} mt-3">
                    Habilitados: {{ $activePrincipals->count() }} · Presentes válidos: {{ $meeting->quorum_present }} ·
                    Requerido: {{ $meeting->quorum_required }} — <b>{{ $meeting->has_quorum ? 'Existe quórum' : 'No existe quórum' }}</b>
                </div>
            @endif

            <form method="POST" action="{{ route('committees.meetings.start', $meeting) }}">
                @csrf
                <button class="btn btn-success">Iniciar reunión</button>
            </form>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">Asistencia digital y firma</div>
        <div class="card-body">
            @if($meeting->attendanceEvent)
                <p>Estado: <b>{{ $meeting->attendanceEvent->status }}</b></p>
                <a class="btn btn-primary" href="{{ route('attendance.show', $meeting->attendanceEvent) }}">Gestionar asistencia digital</a>
            @else
                <p>Genere un evento transversal con QR, códigos personales y firma manuscrita capturada.</p>
                <form method="POST" action="{{ route('attendance.meetings.store', $meeting) }}">
                    @csrf
                    <div class="form-row">
                        <div class="col-md-4"><label>Apertura</label><input type="datetime-local" name="attendance_opens_at" class="form-control" value="{{ $meeting->meeting_date->format('Y-m-d') }}T{{ substr((string)$meeting->start_time,0,5) }}" required></div>
                        <div class="col-md-4"><label>Cierre</label><input type="datetime-local" name="attendance_closes_at" class="form-control" value="{{ $meeting->meeting_date->format('Y-m-d') }}T{{ $meeting->end_time ? substr((string)$meeting->end_time,0,5) : '18:00' }}" required></div>
                        <div class="col-md-4"><label>Fin del evento</label><input type="datetime-local" name="ends_at" class="form-control" value="{{ $meeting->meeting_date->format('Y-m-d') }}T{{ $meeting->end_time ? substr((string)$meeting->end_time,0,5) : '18:00' }}" required></div>
                    </div>
                    <input type="hidden" name="requires_signature" value="1">
                    <button class="btn btn-success mt-3">Habilitar asistencia digital</button>
                </form>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">Orden del día</div>
        <div class="card-body">
            <form method="POST" action="{{ route('committees.meetings.complete', $meeting) }}">
                @csrf
                <input type="time" name="end_time" class="form-control mb-3" required>
                @foreach($meeting->agendaItems as $index => $item)
                    <div class="card mb-2"><div class="card-body">
                        <b>{{ $item->sort_order }}. {{ $item->title }}</b>
                        <input type="hidden" name="agenda[{{ $index }}][id]" value="{{ $item->id }}">
                        <select name="agenda[{{ $index }}][status]" class="form-control my-2"><option value="treated">Tratado</option><option value="not_treated">No tratado</option></select>
                        <textarea name="agenda[{{ $index }}][development]" class="form-control mb-2" placeholder="Desarrollo">{{ $item->development }}</textarea>
                        <textarea name="agenda[{{ $index }}][conclusions]" class="form-control" placeholder="Conclusiones">{{ $item->conclusions }}</textarea>
                    </div></div>
                @endforeach
                <button class="btn btn-warning">Terminar reunión</button>
            </form>
        </div>
    </div>

    <div class="mt-3">
        @if($meeting->status === 'minutes_draft')
            <form method="POST" action="{{ route('committees.minutes.generate', $meeting) }}">@csrf<button class="btn btn-danger">Generar acta PDF</button></form>
        @endif
        @foreach($meeting->minutes as $minute)
            <p>Acta {{ $minute->minute_number }} v{{ $minute->version }} — {{ $minute->status }}
                <a href="{{ route('committees.minutes.download', $minute) }}">Descargar</a>
                @if($minute->status === 'draft')
                    <form class="d-inline" method="POST" action="{{ route('committees.minutes.approve', $minute) }}">@csrf<button class="btn btn-sm btn-success">Aprobar</button></form>
                @elseif($minute->status === 'approved')
                    <form class="d-inline" method="POST" action="{{ route('committees.minutes.finalize', $minute) }}">@csrf<button class="btn btn-sm btn-primary">Finalizar</button></form>
                @endif
            </p>
        @endforeach
    </div>
</div>

<script>
document.querySelectorAll('.attendance-status[data-substitute="1"]').forEach(function (status) {
    var replacement = status.closest('tr').querySelector('.replacement-select');
    var updateReplacement = function () {
        replacement.disabled = status.value !== 'replacement';
        if (replacement.disabled) replacement.value = '';
    };
    status.addEventListener('change', updateReplacement);
    updateReplacement();
});
</script>
@endsection
