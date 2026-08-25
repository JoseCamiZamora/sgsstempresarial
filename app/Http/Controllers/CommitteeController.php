<?php

namespace App\Http\Controllers;

use App\Enums\CommitteeType;
use App\Models\Committee;
use App\Models\PerfilEmpresa;
use App\Services\CommitteeRegulationService;

class CommitteeController extends Controller
{
    public function index(CommitteeRegulationService $regulations)
    {
        $company = PerfilEmpresa::first();
        $workersCount = $regulations->activeWorkersCount();
        $committees = Committee::with(['latestProcess.period', 'latestProcess.candidates', 'latestFinalFormation.election'])->get()->keyBy(fn ($item) => $item->type->value);
        $cards = collect(CommitteeType::cases())->map(fn ($type) => [
            'type' => $type, 'committee' => $committees->get($type->value), 'composition' => $regulations->composition($type, $workersCount),
        ]);
        return view('committees.index', compact('company', 'workersCount', 'cards'));
    }
}
