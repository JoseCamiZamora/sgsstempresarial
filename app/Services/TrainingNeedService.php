<?php

namespace App\Services;

use App\Models\MatrizRiesgo;
use App\Models\TrainingAudit;
use App\Models\TrainingNeed;
use Illuminate\Support\Facades\DB;

class TrainingNeedService
{
    public function create(array $data, array $risks, int $user, int $company): TrainingNeed
    {
        return DB::transaction(function () use ($data, $risks, $user, $company) {
            $sequence = TrainingNeed::where('company_id', $company)->count() + 1;
            $need = TrainingNeed::create(array_merge($data, [
                'company_id' => $company,
                'code' => 'NEC-'.date('Y').'-'.str_pad($sequence, 3, '0', STR_PAD_LEFT),
                'status' => 'identified',
                'created_by' => $user,
                'updated_by' => $user,
            ]));

            $validRisks = MatrizRiesgo::where('company_id', $company)
                ->whereIn('id', $risks)
                ->pluck('id');
            $need->risks()->sync($validRisks);
            $this->audit($need, 'training_need_created', $user);

            return $need;
        });
    }

    public function approve(TrainingNeed $need, int $user): void
    {
        $need->update(['status' => 'approved', 'updated_by' => $user]);
        $this->audit($need, 'training_need_approved', $user);
    }

    public function cancel(TrainingNeed $need, int $user, string $reason): void
    {
        $need->update([
            'status' => 'cancelled',
            'origin_description' => trim($need->origin_description."\nCancelación: ".$reason),
            'updated_by' => $user,
        ]);
        $this->audit($need, 'training_need_cancelled', $user, ['reason' => $reason]);
    }

    private function audit(TrainingNeed $need, string $event, int $user, array $metadata = []): void
    {
        TrainingAudit::create([
            'company_id' => $need->company_id,
            'event' => $event,
            'subject_type' => 'training_need',
            'subject_id' => $need->id,
            'user_id' => $user,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}
