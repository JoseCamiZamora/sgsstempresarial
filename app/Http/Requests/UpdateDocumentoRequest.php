<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDocumentoRequest extends FormRequest
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
            'archivo' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx|max:5120',
            'fecha_vigencia_inicio' => 'nullable|date',
            'fecha_vigencia_fin' => 'nullable|date|after:fecha_vigencia_inicio',
            'tipo_accion' => 'required|in:Nuevo,Modificacion',
            'version' => 'required|string|max:20',
            'requiere_firma_empleados' => 'nullable|boolean',
            'observaciones' => 'nullable|string',
        ];
    }
}
