<?php

namespace App\Http\Controllers;

use App\Models\PlanTrabajo;
use App\Models\ActividadPlan;
use App\Models\CronogramaActividad;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Exports\PlanTrabajoExport;
use Maatwebsite\Excel\Facades\Excel;

class PlanTrabajoController extends Controller
{
    public function index(Request $request)
    {
        // 1. Determinamos el año a consultar (si no viene en la URL, usamos el actual)
        $anioConsultado = $request->get('anio', date('Y'));

        $mesActual = date('n'); // Mes actual (1 a 12)

        // 2. Buscamos el plan del año solicitado
        $plan = PlanTrabajo::where('anio', $anioConsultado)->first();

        // 1. Actividades Vencidas (Programadas en meses pasados que NO se ejecutaron)
        $actividadesVencidas = CronogramaActividad::whereHas('actividad', function($q) use ($plan) {
            $q->where('plan_trabajo_id', $plan->id);
        })
        ->where('mes', '<', $mesActual)
        ->where('programado', true)
        ->where('ejecutado', false)
        ->count();

        // Si no existe el plan y el usuario está consultando el año en curso, lo creamos
        if (!$plan && $anioConsultado == date('Y')) {
            $plan = PlanTrabajo::create([
                'anio' => date('Y'),
                'estado' => 'Borrador',
                'objetivo_general' => 'Plan de Trabajo Anual de SGSST'
            ]);
        } 
        // Si busca un año antiguo o futuro que no existe, lo devolvemos al actual para evitar errores
        elseif (!$plan) {
            return redirect()->route('plan-trabajo.index')->with('warning', 'El plan de trabajo para el año ' . $anioConsultado . ' no existe.');
        }

        // 3. Traemos las actividades con su cronograma y responsable
        $actividades = ActividadPlan::with(['cronograma', 'responsable'])
                        ->where('plan_trabajo_id', $plan->id)
                        ->get();

        // 2. Actividades del Mes Actual (Pendientes por hacer este mes)
        $actividadesMesActual = CronogramaActividad::whereHas('actividad', function($q) use ($plan) {
            $q->where('plan_trabajo_id', $plan->id);
        })
        ->where('mes', $mesActual)
        ->where('programado', true)
        ->count();
        
        // 4. Calculamos el progreso global
        $totalProgramados = CronogramaActividad::whereHas('actividad', function($q) use ($plan){
            $q->where('plan_trabajo_id', $plan->id);
        })->where('programado', true)->count();
        
        $totalEjecutados = CronogramaActividad::whereHas('actividad', function($q) use ($plan){
            $q->where('plan_trabajo_id', $plan->id);
        })->where('ejecutado', true)->count();

        $porcentajeGlobal = ($totalProgramados > 0) ? round(($totalEjecutados / $totalProgramados) * 100) : 0;

        // 5. Obtenemos datos para formularios y navegación
        $usuarios = \App\Models\User::all();
        
        // Extraemos todos los años que existen en la tabla planes_trabajo (ordenados del más reciente al más viejo)
        $añosDisponibles = PlanTrabajo::pluck('anio')->unique()->sortDesc();

        // 3. Datos para Gráfico de Cumplimiento por Fase (PHVA)
        $statsPHVA = [];
        foreach (['Planear', 'Hacer', 'Verificar', 'Actuar'] as $fase) {
            $programadas = CronogramaActividad::whereHas('actividad', function($q) use ($plan, $fase) {
                $q->where('plan_trabajo_id', $plan->id)->where('fase_phva', $fase);
            })->where('programado', true)->count();

            $ejecutadas = CronogramaActividad::whereHas('actividad', function($q) use ($plan, $fase) {
                $q->where('plan_trabajo_id', $plan->id)->where('fase_phva', $fase);
            })->where('ejecutado', true)->count();

            $statsPHVA[$fase] = ($programadas > 0) ? round(($ejecutadas / $programadas) * 100) : 0;
        }
        // 6. Enviamos TODO a la vista (agregamos $añosDisponibles al compact)
        return view('plan_trabajo.index', compact('plan', 'actividades', 'porcentajeGlobal', 'usuarios', 'añosDisponibles','actividadesVencidas', 'actividadesMesActual', 'statsPHVA'));
    }

    // 2. Mostrar el formulario para crear una nueva actividad
    public function create()
    {
        // Traemos a los usuarios para poder asignarles la tarea
        $usuarios = User::all();
        return view('plan_trabajo.create', compact('usuarios'));
    }

    // 3. Guardar la actividad en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'actividad' => 'required|string|max:255',
            'fecha_programada' => 'required|date',
            'responsable_id' => 'required|exists:users,id',
        ]);

        PlanTrabajo::create($request->all());

        return redirect()->route('plan-trabajo.index')->with('success', 'Actividad agregada al Plan Anual con éxito.');
    }

    // 4. Mostrar el formulario para editar/subir evidencia
    public function edit($id)
    {
        $planTrabajo = PlanTrabajo::findOrFail($id);
        $usuarios = User::all();
        return view('plan_trabajo.edit', compact('planTrabajo', 'usuarios'));
    }

    // 5. Actualizar el estado y guardar el archivo
    public function update(Request $request, $id)
    {
        $planTrabajo = PlanTrabajo::findOrFail($id);

        $request->validate([
            'estado' => 'required|string',
            'evidencia' => 'nullable|mimes:pdf,jpg,jpeg,png|max:5120', // Máximo 5MB
        ]);

        $datos = $request->except(['evidencia']);

        // Si el usuario subió un archivo nuevo
        if ($request->hasFile('evidencia')) {
            // Borramos la evidencia anterior si existía
            if ($planTrabajo->evidencia_pdf) {
                Storage::delete('public/' . $planTrabajo->evidencia_pdf);
            }
            // Guardamos el nuevo archivo en la carpeta "evidencias"
            $rutaArchivo = $request->file('evidencia')->store('evidencias', 'public');
            $datos['evidencia_pdf'] = $rutaArchivo;
        }

        $planTrabajo->update($datos);

        return redirect()->route('plan_trabajo.index')->with('success', 'Actividad y evidencias actualizadas correctamente.');
    }
    // Añade este método en la clase
    public function exportarExcel()
    {
        $anio = date('Y'); // Aquí exportará el año actual. 
        return Excel::download(new PlanTrabajoExport($anio), 'Plan_SGSST_'.$anio.'.xlsx');
    }
}
