<?php

namespace App\Services;

use App\Enums\CommitteeElectionStatus;
use App\Models\CommitteeBallot;
use App\Models\CommitteeElection;
use App\Models\CommitteeElectionVoter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CommitteeVotingService
{
    public function voter(CommitteeElection $election, string $token): CommitteeElectionVoter
    {
        $voter = $election->voters()->where('credential_hash', hash('sha256', $token))->first();
        if (!$voter || $voter->credential_expires_at->lte(now()) || $voter->status !== 'enabled') {
            throw ValidationException::withMessages(['token' => 'La credencial es inválida o expiró.']);
        }
        return $voter;
    }

    public function cast(CommitteeElection $election, string $token, array $candidateIds): void
    {
        DB::transaction(function () use ($election, $token, $candidateIds) {
            $election->refresh();
            if ($election->status !== CommitteeElectionStatus::OPEN || now()->lt($election->opens_at) || now()->gte($election->closes_at)) {
                throw ValidationException::withMessages(['election' => 'La elección no está abierta.']);
            }

            $voter = $election->voters()->where('credential_hash', hash('sha256', $token))->lockForUpdate()->first();
            if (!$voter || $voter->status !== 'enabled' || $voter->has_voted || $voter->credential_used_at || $voter->credential_expires_at->lte(now())) {
                throw ValidationException::withMessages(['token' => 'Esta credencial es inválida, expiró o ya fue utilizada para votar.']);
            }

            $candidateIds = array_values(array_unique(array_map('intval', $candidateIds)));
            if (count($candidateIds) < 1 || count($candidateIds) > $election->max_selections) {
                throw ValidationException::withMessages(['selections' => "Seleccione entre 1 y {$election->max_selections} candidatos."]);
            }
            $validIds = $election->candidates()->where('enabled', true)->whereIn('id', $candidateIds)->pluck('id')->all();
            if (count($validIds) !== count($candidateIds)) {
                throw ValidationException::withMessages(['selections' => 'La selección contiene candidatos no habilitados.']);
            }

            sort($candidateIds);
            $uuid = (string) Str::uuid();
            CommitteeBallot::create([
                'uuid' => $uuid,
                'election_id' => $election->id,
                'integrity_hash' => hash('sha256', $uuid.'|'.$election->public_uuid.'|'.implode('|', $candidateIds)),
            ]);
            foreach ($candidateIds as $candidateId) {
                DB::table('committee_ballot_selections')->insert([
                    'ballot_uuid' => $uuid,
                    'election_candidate_id' => $candidateId,
                ]);
            }
            $voter->update(['has_voted' => true, 'voted_at' => now(), 'credential_used_at' => now()]);
        });
    }
}
