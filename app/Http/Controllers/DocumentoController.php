<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documento;
use App\Models\DocumentoCambio;
use App\Models\DocumentoFirmaRequerimiento;
use Illuminate\Support\Facades\Storage;

class DocumentoController extends Controller
{
    public function index()
    {
        $documentos = Documento::with('autor')->orderBy('id', 'desc')->get();
        return view('documentos.index', compact('documentos'));
    }

    public function create()
    {
        if (!auth()->user()->hasRole(['Super Admin', 'Administrador SGSST'])) {
            abort(403, 'No tienes permiso para subir documentos.');
        }

        return view('documentos.create');
    }

    public function store(Request $request)
    {
        if (!auth()->user()->hasRole(['Super Admin', 'Administrador SGSST'])) {
            abort(403, 'No tienes permiso para subir documentos.');
        }

        $request->validate([
            'titulo' => 'required|string|max:255',
            'categoria' => 'required|in:Políticas y Objetivos,Manuales y Procedimientos,Formatos y Registros,Capacitaciones,Otros',
            'archivo' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
            'fecha_vigencia_inicio' => 'nullable|date',
            'fecha_vigencia_fin' => 'nullable|date|after:fecha_vigencia_inicio',
            'tipo_accion' => 'required|in:Nuevo,Modificacion',
            'version' => 'required|string|max:20',
            'requiere_firma_empleados' => 'nullable|boolean',
        ]);

        $datos = $request->only(['titulo', 'descripcion', 'categoria', 'fecha_vigencia_inicio', 'fecha_vigencia_fin', 'tipo_accion', 'version']);
        $datos['subido_por'] = auth()->id();
        $datos['requiere_firma_empleados'] = $request->boolean('requiere_firma_empleados');

        $prefijoCategoria = [
            'Políticas y Objetivos' => 'P',
            'Manuales y Procedimientos' => 'M',
            'Formatos y Registros' => 'F',
            'Capacitaciones' => 'C',
            'Otros' => 'O'
        ];
        $prefijo = $prefijoCategoria[$request->categoria] ?? 'X';

        $ultimoId = Documento::max('id');
        $numero = ($ultimoId ? (int)$ultimoId + 1 : 1);
        $datos['codigo'] = 'SST-' . $prefijo . '-' . str_pad($numero, 2, '0', STR_PAD_LEFT);

        if ($request->hasFile('archivo')) {
            $archivo = $request->file('archivo');
            $datos['extension_archivo'] = $archivo->getClientOriginalExtension();
            $datos['nombre_archivo'] = $archivo->getClientOriginalName();
            $ruta = $archivo->store('documentos', 'public');
            $datos['archivo_ruta'] = $ruta;
        }

        $documento = Documento::create($datos);

        DocumentoCambio::create([
            'documento_id' => $documento->id,
            'version' => $request->version,
            'fecha_vigencia_inicio' => $request->fecha_vigencia_inicio,
            'fecha_vigencia_fin' => $request->fecha_vigencia_fin,
            'tipo_cambio' => $request->tipo_accion,
            'observaciones' => 'Documento ' . ($request->tipo_accion === 'Nuevo' ? 'creado' : 'modificado') . ' inicialmente',
            'registrado_por' => auth()->id(),
        ]);

        if ($documento->requiere_firma_empleados) {
            DocumentoFirmaRequerimiento::firstOrCreate([
                'documento_id' => $documento->id,
                'version_requerida' => $request->version,
            ]);
        }

        return redirect()->route('documentos.index')
            ->with('success', 'Documento subido exitosamente. Código: ' . $datos['codigo']);
    }

    public function show(string $id)
    {
        $documento = Documento::with(['autor', 'cambios.registradoPor'])->findOrFail($id);
        return view('documentos.show', compact('documento'));
    }

    public function edit(string $id)
    {
        if (!auth()->user()->hasRole(['Super Admin', 'Administrador SGSST'])) {
            abort(403, 'No tienes permiso para editar documentos.');
        }

        $documento = Documento::findOrFail($id);
        $cambios = $documento->cambios()->with('registradoPor')->orderBy('fecha_vigencia_inicio', 'desc')->get();
        
        return view('documentos.edit', compact('documento', 'cambios'));
    }

    public function update(Request $request, string $id)
    {
        if (!auth()->user()->hasRole(['Super Admin', 'Administrador SGSST'])) {
            abort(403, 'No tienes permiso para editar documentos.');
        }

        $documento = Documento::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'categoria' => 'required|in:Políticas y Objetivos,Manuales y Procedimientos,Formatos y Registros,Capacitaciones,Otros',
            'archivo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
            'fecha_vigencia_inicio' => 'nullable|date',
            'fecha_vigencia_fin' => 'nullable|date|after:fecha_vigencia_inicio',
            'tipo_accion' => 'required|in:Nuevo,Modificacion',
            'version' => 'required|string|max:20',
            'requiere_firma_empleados' => 'nullable|boolean',
        ]);

        $datos = $request->only(['titulo', 'descripcion', 'categoria', 'fecha_vigencia_inicio', 'fecha_vigencia_fin', 'tipo_accion', 'version']);
        $datos['requiere_firma_empleados'] = $request->boolean('requiere_firma_empleados');

        if ($request->hasFile('archivo')) {
            if (Storage::disk('public')->exists($documento->archivo_ruta)) {
                Storage::disk('public')->delete($documento->archivo_ruta);
            }
            
            $archivo = $request->file('archivo');
            $datos['extension_archivo'] = $archivo->getClientOriginalExtension();
            $datos['nombre_archivo'] = $archivo->getClientOriginalName();
            $ruta = $archivo->store('documentos', 'public');
            $datos['archivo_ruta'] = $ruta;
        }

        $documento->update($datos);

        DocumentoCambio::create([
            'documento_id' => $documento->id,
            'version' => $request->version,
            'fecha_vigencia_inicio' => $request->fecha_vigencia_inicio,
            'fecha_vigencia_fin' => $request->fecha_vigencia_fin,
            'tipo_cambio' => $request->tipo_accion,
            'observaciones' => $request->observaciones ?? 'Documento actualizado',
            'registrado_por' => auth()->id(),
        ]);

        if ($documento->requiere_firma_empleados) {
            DocumentoFirmaRequerimiento::firstOrCreate([
                'documento_id' => $documento->id,
                'version_requerida' => $request->version,
            ]);
        }

        return redirect()->route('documentos.index')
            ->with('success', 'Documento actualizado. Versión: ' . $request->version);
    }

    public function destroy(string $id)
    {
        if (!auth()->user()->hasRole(['Super Admin', 'Administrador SGSST'])) {
            abort(403, 'No tienes permiso para eliminar documentos.');
        }

        $documento = Documento::findOrFail($id);

        if (Storage::disk('public')->exists($documento->archivo_ruta)) {
            Storage::disk('public')->delete($documento->archivo_ruta);
        }

        $documento->delete();

        return redirect()->route('documentos.index')
            ->with('success', 'Documento eliminado permanentemente del sistema.');
    }
}