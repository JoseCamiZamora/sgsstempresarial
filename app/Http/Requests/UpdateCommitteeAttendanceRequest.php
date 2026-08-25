<?php

namespace App\Http\Requests;

use App\Models\CommitteeMeetingAttendee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCommitteeAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('comites.reuniones.gestionar') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $attendees = $this->input('attendees', []);

        foreach ($attendees as &$attendee) {
            $time = $attendee['arrival_time'] ?? null;
            if (is_string($time) && preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
                $attendee['arrival_time'] = substr($time, 0, 5);
            }

            if (($attendee['attendance_status'] ?? null) !== 'replacement') {
                $attendee['replaces_member_id'] = null;
            }
        }

        $this->merge(['attendees' => $attendees]);
    }

    public function rules(): array
    {
        return [
            'attendees' => 'required|array|min:1',
            'attendees.*.id' => 'required|exists:committee_meeting_attendees,id',
            'attendees.*.attendance_status' => ['required', Rule::in(['present', 'absent', 'excused', 'replacement', 'guest'])],
            'attendees.*.arrival_time' => 'nullable|date_format:H:i',
            'attendees.*.observation' => 'nullable|string|max:1000',
            'attendees.*.replaces_member_id' => 'nullable|required_if:attendees.*.attendance_status,replacement|exists:committee_members,id',
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $meeting = $this->route('meeting');
            $rows = collect($this->input('attendees', []));
            $statusesByAttendee = $rows->keyBy('id');
            $usedPrincipals = [];

            foreach ($rows as $index => $row) {
                $attendee = CommitteeMeetingAttendee::with('member')
                    ->where('meeting_id', $meeting->id)
                    ->find($row['id'] ?? 0);

                if (!$attendee) {
                    $validator->errors()->add("attendees.$index.id", 'El integrante no pertenece a esta reunión.');
                    continue;
                }

                if (($row['attendance_status'] ?? null) !== 'replacement') {
                    continue;
                }

                if ($attendee->member?->member_type?->value !== 'substitute') {
                    $validator->errors()->add("attendees.$index.attendance_status", 'Solo un integrante suplente puede actuar en reemplazo.');
                    continue;
                }

                $principalId = (int) ($row['replaces_member_id'] ?? 0);
                $principalAttendee = $meeting->attendees()->with('member')->where('committee_member_id', $principalId)->first();

                if (!$principalAttendee || $principalAttendee->member?->member_type?->value !== 'principal') {
                    $validator->errors()->add("attendees.$index.replaces_member_id", 'Debe seleccionar un integrante principal de esta reunión.');
                    continue;
                }

                if ($attendee->member->representation_type->value !== $principalAttendee->member->representation_type->value) {
                    $validator->errors()->add("attendees.$index.replaces_member_id", 'El suplente debe pertenecer a la misma representación del principal.');
                }

                $principalStatus = $statusesByAttendee->get($principalAttendee->id)['attendance_status'] ?? $principalAttendee->attendance_status;
                if (!in_array($principalStatus, ['absent', 'excused'], true)) {
                    $validator->errors()->add("attendees.$index.replaces_member_id", 'El principal debe estar marcado como ausente o excusado para ser reemplazado.');
                }

                if (in_array($principalId, $usedPrincipals, true)) {
                    $validator->errors()->add("attendees.$index.replaces_member_id", 'Un principal no puede ser reemplazado por más de un suplente.');
                }
                $usedPrincipals[] = $principalId;
            }
        });
    }

    public function messages(): array
    {
        return [
            'attendees.required' => 'Debe registrar la asistencia de los integrantes.',
            'attendees.*.attendance_status.required' => 'Debe seleccionar el estado de asistencia.',
            'attendees.*.attendance_status.in' => 'El estado de asistencia seleccionado no es válido.',
            'attendees.*.arrival_time.date_format' => 'La hora de llegada debe tener un formato válido.',
            'attendees.*.replaces_member_id.required_if' => 'Debe indicar qué principal está reemplazando el suplente.',
            'attendees.*.replaces_member_id.exists' => 'El principal seleccionado no es válido.',
        ];
    }
}
