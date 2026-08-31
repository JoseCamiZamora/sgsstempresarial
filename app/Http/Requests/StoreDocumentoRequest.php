<?php

namespace App\Http\Requests;

use App\Models\Documento;
use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole(['Super Admin', 'Administrador SGSST']);
    }

    public function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'categoria' => 'required|in:Políticas y Objetivos,Manuales y Procedimientos,Formatos y Registros,Capacitaciones,Otros',
            'prefijo' => ['required', 'in:' . implode(',', array_keys(Documento::PREFIJOS))],
            'archivo' => 'required|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
            'fecha_vigencia_inicio' => 'nullable|date',
            'fecha_vigencia_fin' => 'nullable|date|after:fecha_vigencia_inicio',
            'tipo_accion' => 'required|in:Nuevo,Modificacion',
            'version' => 'required|string|max:20',
            'requiere_firma_empleados' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'prefijo.required' => 'Selecciona el prefijo del documento (tipo documental).',
            'prefijo.in' => 'El prefijo seleccionado no es válido.',
        ];
    }
}
