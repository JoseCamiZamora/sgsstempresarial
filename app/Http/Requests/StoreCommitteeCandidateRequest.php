<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreCommitteeCandidateRequest extends FormRequest {
    public function authorize(): bool { return $this->user()?->can('comites.candidatos.crear') ?? false; }
    public function rules(): array { return ['employee_id'=>['required','exists:empleados,id'],'photo'=>['required','image','mimes:jpg,jpeg,png,webp','max:2048'],'short_profile'=>['nullable','string','max:1000'],'proposal'=>['nullable','string','max:2000'],'observations'=>['nullable','string','max:2000'],'eligibility_confirmed'=>['accepted']]; }
    public function messages(): array { return ['required'=>'El campo :attribute es obligatorio.','employee_id.exists'=>'El empleado seleccionado no existe.','photo.image'=>'La fotografía debe ser una imagen válida.','photo.mimes'=>'La fotografía debe ser JPG, JPEG, PNG o WEBP.','photo.max'=>'La fotografía no puede superar los 2 MB.','max.string'=>'El campo :attribute no puede superar :max caracteres.','eligibility_confirmed.accepted'=>'Debe confirmar la elegibilidad del candidato.']; }
    public function attributes(): array { return ['employee_id'=>'empleado','photo'=>'fotografía','short_profile'=>'perfil breve','proposal'=>'propuesta','observations'=>'observaciones','eligibility_confirmed'=>'confirmación de elegibilidad']; }
}
