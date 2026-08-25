<?php

namespace App\Http\Controllers;

use App\Http\Requests\FinalizeCommitteeMeetingRequest;
use App\Http\Requests\StoreCommitteeMeetingRequest;
use App\Http\Requests\UpdateCommitteeAttendanceRequest;
use App\Models\Committee;
use App\Models\CommitteeMeeting;
use App\Services\CommitteeMeetingService;
use Illuminate\Http\Request;

class CommitteeMeetingController extends Controller
{
    public function store(Committee $committee, StoreCommitteeMeetingRequest $request, CommitteeMeetingService $service)
    {
        $meeting = $service->create($committee, $request->validated(), $request->user()->id);
        return redirect()->route('committees.meetings.show', $meeting)->with('success', 'Reunión programada y agenda sugerida creada.');
    }

    public function show(CommitteeMeeting $meeting)
    {
        // Sincroniza reuniones ya creadas y también incluye representantes del
        // empleador que en fases anteriores quedaron con estado "designated".
        $meeting->period->members()
            ->whereIn('status', ['active', 'designated'])
            ->with('employee')
            ->get()
            ->each(function ($member) use ($meeting) {
                $meeting->attendees()->firstOrCreate(
                    ['committee_member_id' => $member->id],
                    ['employee_id' => $member->employee_id, 'attendance_status' => 'absent']
                );
            });

        $meeting->load([
            'committee', 'period.members.employee', 'period.members.roles', 'attendees.member.employee', 'attendees.member.roles',
            'agendaItems', 'decisions', 'commitments.responsible', 'minutes', 'attendanceEvent',
        ]);

        return view('committees.meetings.show', compact('meeting'));
    }

    public function attendance(CommitteeMeeting $meeting, UpdateCommitteeAttendanceRequest $request, CommitteeMeetingService $service)
    {
        $service->updateAttendance($meeting, $request->validated('attendees'), $request->user()->id);
        return back()->with('success', 'Asistencia y quórum actualizados.');
    }

    public function start(CommitteeMeeting $meeting, Request $request, CommitteeMeetingService $service)
    {
        $service->start($meeting, $request->user()->id);
        return back()->with('success', $meeting->fresh()->has_quorum ? 'Reunión iniciada.' : 'Se registró convocatoria sin quórum.');
    }

    public function complete(CommitteeMeeting $meeting, FinalizeCommitteeMeetingRequest $request, CommitteeMeetingService $service)
    {
        $service->complete($meeting, $request->validated(), $request->user()->id);
        return back()->with('success', 'Reunión terminada. Ya puede generar el acta.');
    }

    public function agenda(CommitteeMeeting $meeting, Request $request)
    {
        abort_if(in_array($meeting->status, ['closed', 'cancelled']), 422);
        $data = $request->validate(['title' => 'required|string|max:255', 'description' => 'nullable|string|max:3000', 'type' => 'required|string|max:30']);
        $meeting->agendaItems()->create(array_merge($data, ['sort_order' => ($meeting->agendaItems()->max('sort_order') ?? 0) + 1, 'status' => 'pending']));
        return back()->with('success', 'Punto agregado.');
    }

    public function decision(CommitteeMeeting $meeting, Request $request)
    {
        abort_unless($meeting->status === 'in_progress', 422, 'La reunión debe estar en curso.');
        $data = $request->validate(['agenda_item_id' => 'nullable|exists:committee_agenda_items,id', 'description' => 'required|string|max:3000', 'decision_type' => 'required|string|max:30', 'approved' => 'required|boolean', 'notes' => 'nullable|string|max:2000']);
        $meeting->decisions()->create($data);
        return back()->with('success', 'Decisión registrada.');
    }
}
