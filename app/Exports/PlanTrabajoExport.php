<?php

namespace App\Exports;

use App\Models\PlanTrabajo;
use App\Models\ActividadPlan;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PlanTrabajoExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $anio;

    // Pasamos el año cuando llamemos a la clase
    public function __construct($anio)
    {
        $this->anio = $anio;
    }

    public function view(): View
    {
        $plan = PlanTrabajo::where('anio', $this->anio)->first();
        $actividades = ActividadPlan::with(['cronograma', 'responsable'])
            ->where('plan_trabajo_id', $plan->id)
            ->get();

        return view('plan_trabajo.excel', [
            'plan' => $plan,
            'actividades' => $actividades
        ]);
    }

    // Le damos un poco de estilo a la cabecera (Negrita y color de fondo)
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            2 => ['font' => ['bold' => true]],
        ];
    }
}
