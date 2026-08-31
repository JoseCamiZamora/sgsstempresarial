<?php

namespace App\Http\Controllers;

use App\Exports\DocumentosExport;
use App\Http\Requests\StoreDocumentoRequest;
use App\Http\Requests\UpdateDocumentoRequest;
use App\Models\Documento;
use App\Services\DocumentoService;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class DocumentoController extends Controller
{
    public function __construct(private DocumentoService $documentos)
    {
    }

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

    public function store(StoreDocumentoRequest $request)
    {
        $datos = $request->safe()->except(['archivo']);
        $datos['requiere_firma_empleados'] = $request->boolean('requiere_firma_empleados');

        $documento = $this->documentos->crear($datos, $request->file('archivo'), auth()->id());

        return redirect()->route('documentos.index')
            ->with('success', 'Documento subido exitosamente. Código: ' . $documento->codigo);
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

    public function update(UpdateDocumentoRequest $request, string $id)
    {
        $documento = Documento::findOrFail($id);

        $datos = $request->safe()->except(['archivo']);
        $datos['requiere_firma_empleados'] = $request->boolean('requiere_firma_empleados');

        $this->documentos->actualizar($documento, $datos, $request->file('archivo'), auth()->id());

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

    /**
     * Descarga el listado maestro de documentos en Excel.
     * Visible para cualquiera que pueda ver el listado (mismos datos de la tabla).
     */
    public function export()
    {
        return Excel::download(new DocumentosExport, 'listado-maestro-documentos-' . now()->format('Y-m-d') . '.xlsx');
    }
}
