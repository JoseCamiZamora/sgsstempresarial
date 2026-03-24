<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documento;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Traemos todos los documentos ordenados del más nuevo al más viejo
        $documentos = Documento::with('autor')->orderBy('id', 'desc')->get();
        return view('documentos.index', compact('documentos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Solo los administradores deberían poder subir documentos
        if (!auth()->user()->hasRole(['Super Admin', 'Administrador SGSST'])) {
            abort(403, 'No tienes permiso para subir documentos.');
        }

        return view('documentos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       if (!auth()->user()->hasRole(['Super Admin', 'Administrador SGSST'])) {
            abort(403, 'No tienes permiso para subir documentos.');
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'categoria' => 'required|in:Políticas y Objetivos,Manuales y Procedimientos,Formatos y Registros,Capacitaciones,Otros',
            'archivo' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:5120', // Máximo 5MB, solo PDFs, Word y Excel
        ]);

        $datos = $request->only(['titulo', 'descripcion', 'categoria']);
        $datos['subido_por'] = auth()->id();

        // Lógica para guardar el archivo físico
        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            // Sacamos la extensión (ej: pdf)
            $datos['extension_archivo'] = $archivo->getClientOriginalExtension();
            // Guardamos el archivo en la carpeta public/documentos y obtenemos la ruta
            $ruta = $archivo->store('documentos', 'public');
            $datos['archivo_ruta'] = $ruta;
        }

        Documento::create($datos);

        return redirect()->route('documentos.index')
                         ->with('success', 'Documento subido y guardado exitosamente.');
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Candado de seguridad
        if (!auth()->user()->hasRole(['Super Admin', 'Administrador SGSST'])) {
            abort(403, 'No tienes permiso para eliminar documentos.');
        }

        $documento = Documento::findOrFail($id);

        // 1. Eliminamos el archivo físico de la carpeta storage
        if (Storage::disk('public')->exists($documento->archivo_ruta)) {
            Storage::disk('public')->delete($documento->archivo_ruta);
        }

        // 2. Eliminamos el registro de la base de datos
        $documento->delete();

        return redirect()->route('documentos.index')
                         ->with('success', 'Documento eliminado permanentemente del sistema.');
    }
}
