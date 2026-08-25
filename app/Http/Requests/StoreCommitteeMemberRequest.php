<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class StoreCommitteeMemberRequest extends FormRequest {
    public function authorize(): bool { return $this->user()?->can('comites.representantes.gestionar') ?? false; }
    public function rules(): array { return ['employee_id'=>['required','exists:empleados,id'],'member_type'=>['required',Rule::in(['principal','substitute'])],'designation_date'=>['required','date'],'notes'=>['nullable','string','max:2000'],'eligibility_confirmed'=>['accepted']]; }
    public function messages(): array { return ['required'=>'El campo :attribute es obligatorio.','employee_id.exists'=>'El empleado seleccionado no existe.','member_type.in'=>'El tipo de representante seleccionado no es válido.','designation_date.date'=>'La fecha de designación debe ser válida.','notes.max'=>'Las observaciones no pueden superar :max caracteres.','eligibility_confirmed.accepted'=>'Debe confirmar la elegibilidad del representante.']; }
    public function attributes(): array { return ['employee_id'=>'empleado','member_type'=>'tipo de representante','designation_date'=>'fecha de designación','notes'=>'observaciones','eligibility_confirmed'=>'confirmación de elegibilidad']; }
}
