<?php

namespace App\Services;

use App\Models\Documento;
use App\Models\DocumentoCambio;
use App\Models\DocumentoFirmaRequerimiento;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentoService
{
    /**
     * Crea un documento nuevo: genera el código consecutivo por prefijo,
     * guarda el archivo y registra el primer cambio de control de versiones.
     */
    public function crear(array $datos, UploadedFile $archivo, int $usuarioId): Documento
    {
        $datos['subido_por'] = $usuarioId;
        $datos['codigo'] = $this->generarCodigo($datos['prefijo']);
        $datos = array_merge($datos, $this->datosArchivo($archivo));

        $documento = Documento::create($datos);

        $this->registrarCambio($documento, $datos, $usuarioId);
        $this->registrarFirmaSiAplica($documento, $datos);

        return $documento;
    }

    /**
     * Actualiza un documento existente. El código y el prefijo son la identidad
     * permanente del documento en el listado maestro: no se recalculan aquí.
     */
    public function actualizar(Documento $documento, array $datos, ?UploadedFile $archivo, int $usuarioId): Documento
    {
        if ($archivo) {
            if (Storage::disk('public')->exists($documento->archivo_ruta)) {
                Storage::disk('public')->delete($documento->archivo_ruta);
            }
            $datos = array_merge($datos, $this->datosArchivo($archivo));
        }

        $documento->update($datos);

        $this->registrarCambio($documento, $datos, $usuarioId, esNuevo: false);
        $this->registrarFirmaSiAplica($documento, $datos);

        return $documento;
    }

    /**
     * Código consecutivo por prefijo, ej: SST-FT-001, SST-FT-002, SST-PR-001.
     * Se calcula a partir del máximo consecutivo ya usado por ese prefijo
     * (no un conteo de filas) para no repetir código si algún documento fue eliminado.
     */
    private function generarCodigo(string $prefijo): string
    {
        $ultimoNumero = Documento::where('prefijo', $prefijo)
            ->selectRaw("MAX(CAST(SUBSTRING_INDEX(codigo, '-', -1) AS UNSIGNED)) as max_num")
            ->value('max_num');

        $numero = ((int) $ultimoNumero) + 1;

        return sprintf('SST-%s-%03d', $prefijo, $numero);
    }

    private function datosArchivo(UploadedFile $archivo): array
    {
        return [
            'extension_archivo' => $archivo->getClientOriginalExtension(),
            'nombre_archivo' => $archivo->getClientOriginalName(),
            'archivo_ruta' => $archivo->store('documentos', 'public'),
        ];
    }

    private function registrarCambio(Documento $documento, array $datos, int $usuarioId, bool $esNuevo = true): void
    {
        DocumentoCambio::create([
            'documento_id' => $documento->id,
            'version' => $datos['version'],
            'fecha_vigencia_inicio' => $datos['fecha_vigencia_inicio'] ?? null,
            'fecha_vigencia_fin' => $datos['fecha_vigencia_fin'] ?? null,
            'tipo_cambio' => $datos['tipo_accion'],
            'observaciones' => $datos['observaciones']
                ?? ($esNuevo
                    ? 'Documento ' . ($datos['tipo_accion'] === 'Nuevo' ? 'creado' : 'modificado') . ' inicialmente'
                    : 'Documento actualizado'),
            'registrado_por' => $usuarioId,
        ]);
    }

    private function registrarFirmaSiAplica(Documento $documento, array $datos): void
    {
        if (! empty($datos['requiere_firma_empleados'])) {
            DocumentoFirmaRequerimiento::firstOrCreate([
                'documento_id' => $documento->id,
                'version_requerida' => $datos['version'],
            ]);
        }
    }
}
