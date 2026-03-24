<?php

namespace App\Http\Controllers;

use App\Models\PlanTrabajo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PlanTrabajoController extends Controller
{
    // 1. Mostrar la tabla con todas las actividades
    public function index()
    {
        // Traemos las actividades ordenadas por fecha (las más próximas primero)
        $actividades = PlanTrabajo::with('responsable')->orderBy('fecha_programada', 'asc')->get();
        return view('plan_trabajo.index', compact('actividades'));
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

        return redirect()->route('plan-trabajo.index')->with('success', 'Actividad y evidencias actualizadas correctamente.');
    }
}
