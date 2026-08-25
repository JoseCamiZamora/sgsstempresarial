<?php

namespace App\Services;

use App\Enums\CommitteeElectionStatus;
use App\Models\CommitteeElection;
use App\Models\Empleado;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CommitteeElectionService
{
    public function prepare($formation, int $maxSelections, int $userId): array
    {
        return DB::transaction(function () use ($formation, $maxSelections, $userId) {
            if ($formation->election) throw ValidationException::withMessages(['election' => 'Este proceso ya tiene una elección.']);
            $candidates = $formation->candidates()->where('status', 'approved')->with('employee')->get();
            if ($candidates->count() < $maxSelections) throw ValidationException::withMessages(['candidates' => 'No hay suficientes candidatos aprobados.']);
            $election = CommitteeElection::create([
                'public_uuid' => (string) Str::uuid(), 'formation_process_id' => $formation->id,
                'status' => 'prepared', 'opens_at' => $formation->election_start_at, 'closes_at' => $formation->election_end_at,
                'max_selections' => $maxSelections, 'candidate_count' => $candidates->count(),
                'created_by' => $userId, 'updated_by' => $userId,
            ]);
            foreach ($candidates as $candidate) {
                $election->candidates()->create([
                    'committee_candidate_id' => $candidate->id, 'name' => $candidate->employee->nombre_completo,
                    'position' => $candidate->employee->cargo, 'department' => $candidate->employee->area_departamento,
                    'photo_path' => $candidate->photo_path, 'short_profile' => $candidate->short_profile, 'proposal' => $candidate->proposal,
                ]);
            }
            $tokens = [];
            foreach (Empleado::active()->get() as $employee) {
                $token = Str::random(64);
                $election->voters()->create([
                    'employee_id' => $employee->id, 'credential_hash' => hash('sha256', $token),
                    'credential_expires_at' => $election->closes_at,
                ]);
                $tokens[] = ['employee' => $employee, 'token' => $token];
            }
            $election->update(['electorate_count' => count($tokens)]);
            $this->audit($election, 'election_prepared', $userId, ['candidates' => $candidates->count(), 'electors' => count($tokens)]);
            return [$election, $tokens];
        });
    }

    public function regeneratePendingCredentials(CommitteeElection $election, int $userId): array
    {
        if (!in_array($election->status, [CommitteeElectionStatus::PREPARED, CommitteeElectionStatus::SCHEDULED, CommitteeElectionStatus::OPEN], true)) {
            throw ValidationException::withMessages(['status' => 'Solo se pueden regenerar enlaces de una elección preparada, programada o abierta.']);
        }
        return DB::transaction(function () use ($election, $userId) {
            $tokens = [];
            $voters = $election->voters()->where('status', 'enabled')->where('has_voted', false)->with('employee')->get();
            foreach ($voters as $voter) {
                $token = Str::random(64);
                $voter->update([
                    'credential_hash' => hash('sha256', $token),
                    'credential_expires_at' => $election->closes_at,
                    'credential_used_at' => null,
                ]);
                $tokens[] = ['employee' => $voter->employee, 'token' => $token];
            }
            $this->audit($election, 'pending_credentials_regenerated', $userId, ['credentials' => count($tokens)]);
            return $tokens;
        });
    }

    public function open(CommitteeElection $election, int $userId): void
    {
        if (!in_array($election->status, [CommitteeElectionStatus::PREPARED, CommitteeElectionStatus::SCHEDULED], true)) throw ValidationException::withMessages(['status' => 'La elección no puede abrirse desde su estado actual.']);
        $election->update(['status' => 'open', 'opened_at' => now(), 'updated_by' => $userId]);
        $this->audit($election, 'election_opened', $userId);
    }

    public function close(CommitteeElection $election, int $userId, string $reason): void
    {
        if ($election->status !== CommitteeElectionStatus::OPEN) throw ValidationException::withMessages(['status' => 'Solo una elección abierta puede cerrarse.']);
        $election->update(['status' => 'closed', 'closed_at' => now(), 'updated_by' => $userId]);
        $this->audit($election, 'election_closed', $userId, ['reason' => $reason]);
    }

    public function audit(CommitteeElection $election, string $event, ?int $userId, array $metadata = []): void
    {
        $election->audits()->create(['event' => $event, 'user_id' => $userId, 'metadata' => $metadata, 'created_at' => now()]);
    }
}
