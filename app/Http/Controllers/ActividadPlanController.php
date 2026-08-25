<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlanTrabajo;
use App\Models\ActividadPlan;
use App\Models\CronogramaActividad;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert; // Asumiendo que usas SweetAlert por tu V1

use Illuminate\Support\Facades\Storage;

class ActividadPlanController extends Controller
{
    /**
     * Guarda una nueva actividad y su programación mensual
     */
    public function store(Request $request)
    {
        // 1. Validación estricta de los datos que vienen del formulario
        $request->validate([
            'plan_trabajo_id' => 'required|exists:planes_trabajo,id',
            'fase_phva' => 'required|in:Planear,Hacer,Verificar,Actuar',
            'actividad' => 'required|string|max:255',
            'objetivo_especifico' => 'nullable|string',
            'recursos_necesarios' => 'nullable|string|max:255',
            'responsable_id' => 'required|exists:users,id',
            'meses_programados' => 'required|array|min:1', // Exigimos al menos 1 mes
            'meses_programados.*' => 'integer|min:1|max:12' // Los meses deben ser del 1 al 12
        ]);

        try {
            // 2. Iniciamos la Transacción Mágica
            DB::transaction(function () use ($request) {
                
                // A. Creamos la fila principal de la Actividad
                $actividad = ActividadPlan::create([
                    'plan_trabajo_id' => $request->plan_trabajo_id,
                    'fase_phva' => $request->fase_phva,
                    'actividad' => $request->actividad,
                    'objetivo_especifico' => $request->objetivo_especifico,
                    'recursos_necesarios' => $request->recursos_necesarios,
                    'responsable_id' => $request->responsable_id,
                ]);

                // B. Iteramos sobre el array de meses que el usuario seleccionó (Ej: [1, 3, 6])
                // y creamos un registro en el cronograma por cada mes.
                foreach ($request->meses_programados as $mes) {
                    CronogramaActividad::create([
                        'actividad_plan_id' => $actividad->id, // El ID que se acaba de crear arriba
                        'mes' => $mes,
                        'programado' => true, // Lo marcamos como programado (La "P" de tu Excel)
                        'ejecutado' => false  // Aún no se ha ejecutado
                    ]);
                }
            });

            // Si llegamos aquí, TODO se guardó correctamente
            // Alert::success('¡Éxito!', 'Actividad y cronograma guardados correctamente.');
            return redirect()->back()->with('success', 'Actividad programada con éxito.');

        } catch (\Exception $e) {
            // Si algo falla, Laravel deshace los cambios automáticamente
            // Alert::error('Error', 'Ocurrió un problema al guardar: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al guardar la actividad.')->withInput();
        }
    }
    public function cerrarMes(Request $request, $id)
    {
        $request->validate([
            'evidencia_pdf' => 'nullable|mimes:pdf|max:5120', // Máximo 5MB
            'observaciones' => 'nullable|string',
            'fecha_ejecucion_real' => 'required|date'
        ]);

        $itemCronograma = CronogramaActividad::findOrFail($id);

        // 1. Guardar el archivo en una carpeta organizada por año y mes
        if ($request->hasFile('evidencia_pdf')) {
            $archivo = $request->file('evidencia_pdf');
            $rutaDestino = 'evidencias_plan/' . date('Y') . '/mes_' . $itemCronograma->mes;
            $path = $archivo->store($rutaDestino, 'public');
            $itemCronograma->evidencia_pdf = $path;
        }

        // 2. Actualizar el estado
        $itemCronograma->ejecutado = true;
        $itemCronograma->fecha_ejecucion_real = $request->fecha_ejecucion_real;
        $itemCronograma->observaciones = $request->observaciones;
        $itemCronograma->save();

        return redirect()->back()->with('success', '¡Actividad ejecutada y evidencia cargada correctamente!');
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'fase_phva' => 'required|in:Planear,Hacer,Verificar,Actuar',
            'actividad' => 'required|string|max:255',
            'responsable_id' => 'required|exists:users,id',
            'meses_programados' => 'array' // Puede venir vacío si decide cancelar todo lo no ejecutado
        ]);

        $actividad = ActividadPlan::findOrFail($id);

        // 1. Actualizamos los datos básicos de la actividad
        $actividad->update($request->only('fase_phva', 'actividad', 'objetivo_especifico', 'recursos_necesarios', 'responsable_id'));

        // 2. Lógica de Reprogramación de Meses
        $mesesSeleccionados = $request->meses_programados ?? [];

        // A. Agregar o mantener los meses que marcó en el formulario
        foreach ($mesesSeleccionados as $mes) {
            CronogramaActividad::firstOrCreate(
                ['actividad_plan_id' => $actividad->id, 'mes' => $mes],
                ['programado' => true, 'ejecutado' => false]
            );
        }

        // B. Eliminar los meses que desmarcó
        $mesesAEliminar = CronogramaActividad::where('actividad_plan_id', $actividad->id)
            ->whereNotIn('mes', $mesesSeleccionados)
            ->get();

        $intentosDeBorrarCerrados = 0;

        foreach ($mesesAEliminar as $item) {
            if ($item->ejecutado) {
                // ¡Blindaje! Trataron de borrar uno cerrado (quizás manipulando el HTML)
                $intentosDeBorrarCerrados++;
            } else {
                // Si no se ha ejecutado, se puede borrar tranquilamente
                $item->delete();
            }
        }

        // 3. Respuesta inteligente
        if ($intentosDeBorrarCerrados > 0) {
            return redirect()->back()->with('warning', 'La actividad se actualizó, pero algunos meses no se eliminaron porque ya tienen evidencia cargada.');
        }

        return redirect()->back()->with('success', 'Actividad reprogramada exitosamente.');
    }
}
