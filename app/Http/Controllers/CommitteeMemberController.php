<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommitteeMemberRequest;
use App\Models\CommitteeMember;
use App\Models\CommitteePeriod;
use App\Models\Empleado;
use App\Services\CommitteeRegulationService;
use Illuminate\Validation\ValidationException;

class CommitteeMemberController extends Controller
{
    public function store(StoreCommitteeMemberRequest $request, CommitteePeriod $period, CommitteeRegulationService $regulations)
    {
        $this->ensureElectionIsNotFrozen($period);
        $data = $request->validated();
        $period->load('committee');
        if (!Empleado::active()->whereKey($data['employee_id'])->exists()) {
            throw ValidationException::withMessages(['employee_id' => 'El empleado no está activo.']);
        }
        if ($period->members()->where('employee_id', $data['employee_id'])->exists()) {
            throw ValidationException::withMessages(['employee_id' => 'El empleado ya es representante en este período.']);
        }
        $this->validateAvailableSlot($period, $data['member_type'], $regulations);
        $period->members()->create([
            ...$data, 'representation_type' => 'employer', 'status' => 'designated',
            'eligibility_confirmed' => true, 'eligibility_confirmed_by' => $request->user()->id,
            'created_by' => $request->user()->id, 'updated_by' => $request->user()->id,
        ]);
        return back()->with('success', 'Representante designado correctamente.');
    }

    public function update(StoreCommitteeMemberRequest $request, CommitteeMember $member, CommitteeRegulationService $regulations)
    {
        $member->load('period.committee');
        $this->ensureElectionIsNotFrozen($member->period);
        abort_if($member->representation_type->value !== 'employer', 422, 'Solo se pueden editar representantes del empleador desde esta opción.');
        $data = $request->validated();
        if (!Empleado::active()->whereKey($data['employee_id'])->exists()) {
            throw ValidationException::withMessages(['employee_id' => 'El empleado no está activo.']);
        }
        if ($member->period->members()->where('employee_id', $data['employee_id'])->whereKeyNot($member->id)->exists()) {
            throw ValidationException::withMessages(['employee_id' => 'El empleado ya es representante en este período.']);
        }
        $this->validateAvailableSlot($member->period, $data['member_type'], $regulations, $member->id);
        $member->update([
            ...$data, 'representation_type' => 'employer', 'status' => 'designated',
            'eligibility_confirmed' => true, 'eligibility_confirmed_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);
        return back()->with('success', 'Representante del empleador actualizado correctamente.');
    }

    public function destroy(CommitteeMember $member)
    {
        $member->load('period');
        $this->ensureElectionIsNotFrozen($member->period);
        abort_if($member->representation_type->value !== 'employer', 422, 'Solo se pueden retirar representantes del empleador desde esta opción.');
        $member->delete();
        return back()->with('success', 'Representante retirado del período.');
    }

    private function validateAvailableSlot(CommitteePeriod $period, string $memberType, CommitteeRegulationService $regulations, ?int $exceptMemberId = null): void
    {
        $composition = $regulations->composition($period->committee->type, $period->workers_count_snapshot);
        $field = $memberType === 'principal' ? 'employer_principals' : 'employer_substitutes';
        $query = $period->members()->where('representation_type', 'employer')->where('member_type', $memberType);
        if ($exceptMemberId) $query->whereKeyNot($exceptMemberId);
        if ($query->count() >= $composition[$field]) {
            throw ValidationException::withMessages(['member_type' => 'Ya se alcanzó el máximo normativo permitido para este tipo de representante.']);
        }
    }

    private function ensureElectionIsNotFrozen(CommitteePeriod $period): void
    {
        abort_if($period->formationProcesses()->whereHas('election')->exists(), 422,
            'La lista electoral ya fue congelada. Debe anular la elección antes de modificar representantes.');
    }
}
