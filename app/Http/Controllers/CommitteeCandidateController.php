<?php
namespace App\Http\Controllers;

use App\Http\Requests\StoreCommitteeCandidateRequest;
use App\Http\Requests\WithdrawCommitteeCandidateRequest;
use App\Models\CommitteeCandidate;
use App\Models\CommitteeFormationProcess;
use App\Models\Empleado;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CommitteeCandidateController extends Controller
{
    public function approve(CommitteeCandidate $candidate)
    {
        abort_if($candidate->formationProcess()->whereHas('election')->exists(), 422, 'La lista electoral ya fue congelada.');
        $candidate->update(['status' => 'approved', 'updated_by' => auth()->id()]);
        return back()->with('success', 'Candidato aprobado.');
    }
    public function photo(CommitteeCandidate $candidate)
    {
        abort_unless(Storage::disk('public')->exists($candidate->photo_path), 404);
        return Storage::disk('public')->response($candidate->photo_path);
    }

    public function store(StoreCommitteeCandidateRequest $request, CommitteeFormationProcess $formation)
    {
        if (!now()->between($formation->candidate_registration_start, $formation->candidate_registration_end))
            throw ValidationException::withMessages(['employee_id' => 'La inscripción de candidatos no está abierta.']);
        if (!Empleado::active()->whereKey($request->integer('employee_id'))->exists())
            throw ValidationException::withMessages(['employee_id' => 'El empleado no está activo.']);
        $existing = $formation->candidates()->where('employee_id', $request->integer('employee_id'))->first();
        if ($existing && $existing->status->value !== 'withdrawn')
            throw ValidationException::withMessages(['employee_id' => 'El empleado ya está inscrito en este proceso.']);
        $data = $request->validated();
        unset($data['photo']);
        $data['photo_path'] = $request->file('photo')->store("committees/{$formation->committee_id}/candidates", 'public');
        $data += ['registration_date' => now(), 'status' => 'registered', 'eligibility_confirmed' => true,
            'eligibility_confirmed_by' => $request->user()->id, 'created_by' => $request->user()->id, 'updated_by' => $request->user()->id];
        if ($existing) {
            Storage::disk('public')->delete($existing->photo_path);
            $history = collect([
                trim((string) $existing->observations),
                sprintf('[%s] Candidato reincorporado por %s.', now()->format('d/m/Y H:i'), $request->user()->name),
            ])->filter()->implode(PHP_EOL);
            $existing->update(array_merge($data, ['observations' => $history]));
        } else {
            $formation->candidates()->create($data);
        }
        return back()->with('success', 'Candidato registrado correctamente.');
    }

    public function withdraw(WithdrawCommitteeCandidateRequest $request, CommitteeCandidate $candidate)
    {
        if ($candidate->formationProcess()->whereHas('election')->exists()) {
            return back()->with('error','La lista electoral ya fue congelada. Debe anular la elección antes de retirar un candidato.');
        }
        $history = collect([
            trim((string) $candidate->observations),
            sprintf(
                '[%s] Retirado por %s. Motivo: %s',
                now()->format('d/m/Y H:i'),
                $request->user()->name,
                $request->validated('reason')
            ),
        ])->filter()->implode(PHP_EOL);
        $candidate->update([
            'status' => 'withdrawn',
            'observations' => $history,
            'updated_by' => $request->user()->id,
        ]);
        return back()->with('success','El candidato fue retirado del proceso conservando su trazabilidad.');
    }

    public function destroy(CommitteeCandidate $candidate)
    {
        abort_if($candidate->formationProcess()->whereHas('election')->exists(), 422, 'La lista electoral ya fue congelada.');
        Storage::disk('public')->delete($candidate->photo_path);
        $candidate->delete();
        return back()->with('success', 'Candidatura retirada.');
    }
}
