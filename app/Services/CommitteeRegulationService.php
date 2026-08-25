<?php

namespace App\Services;

use App\Enums\CommitteeType;
use App\Models\Empleado;
use App\Models\CommitteeMember;
use Carbon\CarbonInterface;
use InvalidArgumentException;

class CommitteeRegulationService
{
    public function activeWorkersCount(?CarbonInterface $onDate = null): int
    {
        $date = ($onDate ?? now())->toDateString();
        return Empleado::query()
            ->where(function ($query) use ($date) {
                $query->whereNull('fecha_ingreso')->orWhereDate('fecha_ingreso', '<=', $date);
            })
            ->where(function ($query) use ($date) {
                $query->whereNull('fecha_retiro')->orWhereDate('fecha_retiro', '>=', $date);
            })->count();
    }

    public function composition(CommitteeType|string $type, int $workers): array
    {
        $type = $type instanceof CommitteeType ? $type : CommitteeType::from($type);
        if ($workers < 0) throw new InvalidArgumentException('El número de trabajadores no puede ser negativo.');
        $rules = config("committees.regulations.{$type->value}");
        foreach ($rules['ranges'] as $range) {
            if ($workers >= $range['min'] && ($range['max'] === null || $workers <= $range['max'])) {
                return [
                    'type' => $type->value, 'mode' => $range['mode'], 'workers_count' => $workers,
                    'employer_principals' => $range['employer_representatives'] ?? $range['representatives'],
                    'employer_substitutes' => isset($range['employer_representatives']) ? 0 : $range['substitutes'],
                    'worker_principals' => $range['representatives'], 'worker_substitutes' => $range['substitutes'],
                    'period_years' => $rules['period_years'], 'regulation_reference' => $rules['reference'],
                    'boundary_interpretation' => $rules['boundary_interpretation'] ?? null,
                ];
            }
        }
        throw new InvalidArgumentException('No existe una regla normativa configurada para este valor.');
    }

    public function validateMemberEligibility(CommitteeType $type, bool $isActive, bool $administrativeConfirmation): array
    {
        $errors = [];
        if (!$isActive) $errors[] = 'El empleado no se encuentra activo.';
        if ($type === CommitteeType::CCL && !$administrativeConfirmation) {
            $errors[] = 'Debe confirmarse administrativamente la elegibilidad prevista por la Resolución 3461 de 2025.';
        }
        return $errors;
    }

    public function termYears(CommitteeType $type): int
    {
        return (int) config("committees.regulations.{$type->value}.period_years", 2);
    }

    public function canBePresident(CommitteeType $type, CommitteeMember $member): bool
    {
        return $type === CommitteeType::CCL || $member->representation_type->value === 'employer';
    }

    public function canBeSecretary(CommitteeType $type, CommitteeMember $member): bool
    {
        return in_array($member->representation_type->value, ['employer', 'worker'], true);
    }

    public function presidentTermEndsAt(CommitteeType $type, CarbonInterface $startsAt): CarbonInterface
    {
        return $type === CommitteeType::COPASST ? $startsAt->copy()->addYear()->subDay() : $startsAt->copy()->addYears($this->termYears($type))->subDay();
    }

    public function meetingFrequencyMonths(CommitteeType $type): int
    {
        return (int) config("committees.operations.{$type->value}.meeting_frequency_months", 1);
    }

    public function requiresMonthlyMeeting(CommitteeType $type): bool
    {
        return $this->meetingFrequencyMonths($type) === 1;
    }

    public function quorumRequired(int $eligibleMembers): int
    {
        return $eligibleMembers > 0 ? intdiv($eligibleMembers, 2) + 1 : 0;
    }

    public function normativeFunctions(CommitteeType $type): array
    {
        return config("committees.operations.{$type->value}.functions", []);
    }

    public function requiresQuarterlyReport(CommitteeType $type): bool
    {
        return (bool) config("committees.operations.{$type->value}.quarterly_report", false);
    }

    public function requiresAnnualReport(CommitteeType $type): bool
    {
        return (bool) config("committees.operations.{$type->value}.annual_report", false);
    }

    public function meetingReference(CommitteeType $type): string
    {
        return (string) config("committees.operations.{$type->value}.meeting_reference");
    }
}
