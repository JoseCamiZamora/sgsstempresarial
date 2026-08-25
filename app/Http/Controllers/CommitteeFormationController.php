<?php

namespace App\Http\Controllers;

use App\Enums\CommitteeStatus;
use App\Enums\CommitteeType;
use App\Http\Requests\StoreCommitteeFormationRequest;
use App\Http\Requests\UpdateCommitteeFormationRequest;
use App\Models\Committee;
use App\Models\CommitteeCandidate;
use App\Models\CommitteeFormationProcess;
use App\Models\CommitteeMember;
use App\Models\Empleado;
use App\Models\PerfilEmpresa;
use App\Services\CommitteeRegulationService;
use Illuminate\Support\Facades\DB;

class CommitteeFormationController extends Controller
{
    public function create(string $type, CommitteeRegulationService $regulations)
    {
        $committeeType = CommitteeType::tryFrom(strtoupper($type)) ?? abort(404);
        $company = PerfilEmpresa::firstOrFail();
        $workersCount = $regulations->activeWorkersCount();
        $composition = $regulations->composition($committeeType, $workersCount);
        $employees = Empleado::active()->orderBy('nombre_completo')->get();
        return view('committees.formations.create', compact('committeeType', 'company', 'workersCount', 'composition', 'employees'));
    }

    public function store(StoreCommitteeFormationRequest $request, CommitteeRegulationService $regulations)
    {
        $data = $request->validated();
        $type = CommitteeType::from($data['type']);
        $company = PerfilEmpresa::firstOrFail();
        $workers = $regulations->activeWorkersCount();
        $composition = $regulations->composition($type, $workers);

        $process = DB::transaction(function () use ($data, $type, $company, $workers, $composition, $request) {
            $userId = $request->user()->id;
            $committee = Committee::firstOrCreate(
                ['perfil_empresa_id' => $company->id, 'type' => $type->value],
                ['name' => $type->label(), 'status' => CommitteeStatus::CONFIGURED->value, 'created_by' => $userId, 'updated_by' => $userId]
            );
            $committee->update(['name' => $type->label(), 'status' => CommitteeStatus::CONFIGURED->value, 'updated_by' => $userId]);
            $period = $committee->periods()->create([
                'start_date' => $data['start_date'], 'end_date' => $data['end_date'], 'status' => CommitteeStatus::CONFIGURED->value,
                'workers_count_snapshot' => $workers, 'regulation_reference' => $composition['regulation_reference'], 'created_by' => $userId, 'updated_by' => $userId,
            ]);
            $process = $committee->formationProcesses()->create([
                'committee_period_id' => $period->id, 'status' => CommitteeStatus::CONFIGURED->value,
                'title' => $data['title'], 'description' => $data['description'] ?? null,
                'call_start_date' => $data['call_start_date'], 'call_end_date' => $data['call_end_date'],
                'candidate_registration_start' => $data['candidate_registration_start'], 'candidate_registration_end' => $data['candidate_registration_end'],
                'election_start_at' => $data['election_start_at'], 'election_end_at' => $data['election_end_at'],
                'requirements' => $data['requirements'] ?? null, 'notes' => $data['notes'] ?? null, 'workers_count' => $workers,
                'required_employer_principals' => $composition['employer_principals'], 'required_employer_substitutes' => $composition['employer_substitutes'],
                'required_worker_principals' => $composition['worker_principals'], 'required_worker_substitutes' => $composition['worker_substitutes'],
                'obligation_mode' => $composition['mode'], 'regulation_reference' => $composition['regulation_reference'],
                'regulation_snapshot' => $composition, 'created_by' => $userId, 'updated_by' => $userId,
            ]);
            foreach ($data['employer_members'] ?? [] as $member) CommitteeMember::create([
                'committee_period_id' => $period->id, 'employee_id' => $member['employee_id'], 'representation_type' => 'employer',
                'member_type' => $member['member_type'], 'designation_date' => now()->toDateString(), 'eligibility_confirmed' => true,
                'eligibility_confirmed_by' => $userId, 'created_by' => $userId, 'updated_by' => $userId,
            ]);
            foreach ($data['candidates'] ?? [] as $index => $candidate) {
                $path = $request->file("candidates.{$index}.photo")->store("committees/{$committee->id}/candidates", 'public');
                CommitteeCandidate::create([
                    'formation_process_id' => $process->id, 'employee_id' => $candidate['employee_id'], 'photo_path' => $path,
                    'short_profile' => $candidate['short_profile'] ?? null, 'proposal' => $candidate['proposal'] ?? null,
                    'registration_date' => now(), 'eligibility_confirmed' => true, 'eligibility_confirmed_by' => $userId,
                    'created_by' => $userId, 'updated_by' => $userId,
                ]);
            }
            return $process;
        });
        return redirect()->route('committees.formations.show', $process)->with('success', 'Proceso de conformación guardado correctamente.');
    }

    public function show(CommitteeFormationProcess $formation)
    {
        $formation->load(['committee.company', 'period.members.employee', 'candidates.employee', 'election']);
        $activeCandidateIds = $formation->candidates->reject(fn ($candidate) => $candidate->status->value === 'withdrawn')->pluck('employee_id');
        $availableEmployees = Empleado::active()->whereNotIn('id', $activeCandidateIds)->orderBy('nombre_completo')->get();
        $employerMemberIds = $formation->period->members
            ->filter(fn ($member) => $member->representation_type->value === 'employer')
            ->pluck('employee_id');
        $availableEmployerEmployees = Empleado::active()->whereNotIn('id', $employerMemberIds)->orderBy('nombre_completo')->get();
        return view('committees.formations.show', compact('formation', 'availableEmployees', 'availableEmployerEmployees'));
    }

    public function edit(CommitteeFormationProcess $formation)
    {
        $formation->load(['committee', 'period']);
        return view('committees.formations.edit', compact('formation'));
    }

    public function update(UpdateCommitteeFormationRequest $request, CommitteeFormationProcess $formation)
    {
        if ($formation->election) {
            return back()->with('error', 'La lista electoral ya fue congelada. Para modificar información electoral debe anular la elección y crear una nueva.');
        }
        $data = $request->validated();
        DB::transaction(function () use ($formation, $data, $request) {
            $formation->period->update(['start_date'=>$data['start_date'],'end_date'=>$data['end_date'],'updated_by'=>$request->user()->id]);
            $formation->update([...collect($data)->except(['start_date','end_date'])->all(),'updated_by'=>$request->user()->id]);
        });
        return redirect()->route('committees.formations.show',$formation)->with('success','Información del proceso actualizada correctamente.');
    }
}
