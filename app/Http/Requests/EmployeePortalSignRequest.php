<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class EmployeePortalSignRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'signature' => 'nullable|string|max:700000',
            'signature_file' => 'nullable|file|mimes:png,jpg,jpeg|max:2048',
            'acknowledged' => 'accepted',
        ];
    }

    public function messages()
    {
        return [
            'signature_file.mimes' => 'La imagen debe ser PNG o JPG.',
            'acknowledged.accepted' => 'Debe confirmar el aviso de tratamiento de datos.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->filled('signature') && !$this->hasFile('signature_file')) {
                $validator->errors()->add('signature', 'Debe dibujar su firma o subir una imagen de su firma.');
            }
        });
    }
}
