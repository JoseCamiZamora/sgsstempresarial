<?php

namespace App\Http\Controllers;

use App\Http\Requests\CastCommitteeVoteRequest;
use App\Models\CommitteeElection;
use App\Services\CommitteeVotingService;

class PublicCommitteeElectionController extends Controller
{
    public function show(CommitteeElection $election)
    {
        $election->load('formationProcess.committee.company');
        return response()->view('committees.elections.public', compact('election'))
            ->header('X-Robots-Tag', 'noindex, nofollow')->header('Cache-Control', 'no-store, private');
    }

    public function ballot(CommitteeElection $election, string $token, CommitteeVotingService $service)
    {
        $voter = $service->voter($election, $token);
        if ($voter->has_voted || $voter->credential_used_at) {
            return response()->view('committees.elections.thanks')->header('Cache-Control', 'no-store, private');
        }
        $election->load(['formationProcess.committee', 'candidates']);
        return response()->view('committees.elections.ballot', compact('election', 'token'))
            ->header('Cache-Control', 'no-store, private');
    }

    public function vote(CastCommitteeVoteRequest $request, CommitteeElection $election, CommitteeVotingService $service)
    {
        $service->cast($election, (string) $request->input('token'), $request->input('selections', []));
        return response()->view('committees.elections.thanks')->header('Cache-Control', 'no-store, private');
    }
}
