<?php

namespace App\Http\Controllers;

use App\Models\MatrizRiesgo;
use Illuminate\Http\Request;
use App\Exports\MatrizRiesgosExport;

use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class MatrizRiesgoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Traemos todos los riesgos ordenados del más reciente al más antiguo
        // y usamos 'with' para traer también los datos del usuario responsable
        $riesgos = MatrizRiesgo::with('responsable')->orderBy('id', 'desc')->get();
        
        return view('matriz_riesgos.index', compact('riesgos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('matriz_riesgos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validamos que el usuario llene todos los campos obligatorios
        $request->validate([
            'proceso'               => 'required|string|max:255',
            'zona_lugar'            => 'required|string|max:255',
            'actividad'             => 'required|string|max:255',
            'es_rutinaria'          => 'required|boolean',
            'clasificacion_peligro' => 'required|string|max:255',
            'descripcion_peligro'   => 'required|string',
            'efectos_posibles'      => 'required|string',
            'nivel_riesgo'          => 'required|string|in:Bajo,Medio,Alto,Extremo',
        ], [
            // Algunos mensajes en español por si se les olvida algo
            'proceso.required'               => 'Debes indicar el proceso.',
            'clasificacion_peligro.required' => 'Selecciona una clasificación para el peligro.',
            'nivel_riesgo.required'          => 'Debes valorar el nivel de riesgo.',
        ]);

        // 2. Tomamos todos los datos del formulario
        $datosRiesgo = $request->all();
        
        // 3. Le inyectamos el ID del usuario que está logueado en este momento (Auditoría)
        $datosRiesgo['registrado_por'] = auth()->id();

        // 4. Guardamos en la base de datos
        MatrizRiesgo::create($datosRiesgo);

        // 5. Redirigimos a la tabla con un mensaje de éxito
        return redirect()->route('matriz-riesgos.index')
                         ->with('success', 'El riesgo ha sido registrado exitosamente en la matriz.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Buscamos el riesgo específico en la base de datos
        $riesgo = MatrizRiesgo::findOrFail($id);
        
        return view('matriz_riesgos.edit', compact('riesgo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $riesgo = MatrizRiesgo::findOrFail($id);

        // 1. Validamos los mismos campos que en la creación
        $request->validate([
            'proceso'               => 'required|string|max:255',
            'zona_lugar'            => 'required|string|max:255',
            'actividad'             => 'required|string|max:255',
            'es_rutinaria'          => 'required|boolean',
            'clasificacion_peligro' => 'required|string|max:255',
            'descripcion_peligro'   => 'required|string',
            'efectos_posibles'      => 'required|string',
            'nivel_riesgo'          => 'required|string|in:Bajo,Medio,Alto,Extremo',
        ]);

        // 2. Tomamos los datos del formulario (excepto el token y el método put)
        $datosRiesgo = $request->except(['_token', '_method']);

        // 3. Actualizamos en la base de datos
        $riesgo->update($datosRiesgo);

        // 4. Redirigimos a la tabla principal
        return redirect()->route('matriz-riesgos.index')
                         ->with('success', 'El riesgo ha sido actualizado correctamente en la matriz.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $riesgo = MatrizRiesgo::findOrFail($id);
        $riesgo->delete();

        return redirect()->route('matriz-riesgos.index')
                         ->with('success', 'Riesgo eliminado permanentemente de la matriz.');
    }

    public function exportExcel()
    {
        return Excel::download(new MatrizRiesgosExport, 'Matriz_de_Riesgos_Sinergia_SST.xlsx');
    }
    
    public function exportPdf()
    {
        // 1. Traemos los datos
        $riesgos = MatrizRiesgo::with('responsable')->get();
        
        // 2. Cargamos la vista que acabamos de crear y le pasamos los datos
        $pdf = Pdf::loadView('matriz_riesgos.pdf', compact('riesgos'));
        
        // Opcional: Para que el PDF salga en horizontal (ideal para tablas grandes)
        $pdf->setPaper('A4', 'landscape');
        
        // 3. Descargamos el archivo
        return $pdf->download('Matriz_de_Riesgos_Sinergia_SST.pdf');
    }
}
