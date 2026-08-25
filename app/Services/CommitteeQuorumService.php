<?php

namespace App\Services;

use App\Models\CommitteeMeeting;

class CommitteeQuorumService
{
    public function __construct(private CommitteeRegulationService $rules) {}

    public function calculate(CommitteeMeeting $meeting): array
    {
        $members = $meeting->period->members()
            ->whereIn('status', ['active', 'designated'])
            ->get();
        $principals = $members->filter(fn($member) => $member->member_type->value === 'principal');
        $eligible = $principals->count();
        $presentMemberIds = $meeting->attendees
            ->whereIn('attendance_status', ['present', 'replacement'])
            ->pluck('committee_member_id')
            ->filter();
        $valid = 0;

        foreach ($principals as $principal) {
            if ($presentMemberIds->contains($principal->id)) {
                $valid++;
                continue;
            }

            $replacement = $meeting->attendees->first(fn($attendee) =>
                $attendee->attendance_status === 'replacement'
                && $attendee->replaces_member_id === $principal->id
                && $attendee->member?->member_type?->value === 'substitute'
                && $attendee->member?->representation_type?->value === $principal->representation_type->value
            );

            if ($replacement) $valid++;
        }

        $required = $this->rules->quorumRequired($eligible);

        return [
            'eligible' => $eligible,
            'present' => $valid,
            'required' => $required,
            'has_quorum' => $valid >= $required,
        ];
    }
}
