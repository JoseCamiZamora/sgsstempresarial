<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommitteeElectionRequest;
use App\Models\CommitteeElection;
use App\Models\CommitteeFormationProcess;
use App\Services\CommitteeElectionService;
use Illuminate\Http\Request;

class CommitteeElectionController extends Controller
{
    public function store(StoreCommitteeElectionRequest $request, CommitteeFormationProcess $formation, CommitteeElectionService $service)
    {
        [$election, $tokens] = $service->prepare($formation, $request->integer('max_selections'), $request->user()->id);
        return redirect()->route('committees.elections.show', $election)->with('success', 'Elección preparada.')->with('generated_tokens', $tokens);
    }

    public function show(CommitteeElection $election)
    {
        $election->load(['formationProcess.committee.company', 'candidates', 'voters.employee']);
        return view('committees.elections.show', compact('election'));
    }

    public function regenerateCredentials(CommitteeElection $election, Request $request, CommitteeElectionService $service)
    {
        $tokens = $service->regeneratePendingCredentials($election, $request->user()->id);
        return back()->with('success', count($tokens).' enlaces personales fueron regenerados. Los anteriores quedaron invalidados.')->with('generated_tokens', $tokens);
    }

    public function open(CommitteeElection $election, Request $request, CommitteeElectionService $service)
    {
        $service->open($election, $request->user()->id);
        return back()->with('success', 'Elección abierta.');
    }

    public function close(CommitteeElection $election, Request $request, CommitteeElectionService $service)
    {
        $data = $request->validate(['reason' => 'required|string|max:1000']);
        $service->close($election, $request->user()->id, $data['reason']);
        return back()->with('success', 'Elección cerrada.');
    }
}
