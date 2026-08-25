<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class FinalizeCommitteeFormationRequest extends FormRequest {
 public function authorize():bool{return $this->user()?->can('comites.conformacion.finalizar')??false;}
 public function rules():array{return['formation_date'=>['required','date'],'effective_from'=>['required','date','after_or_equal:formation_date'],'president_employee_id'=>['required','integer','exists:empleados,id','different:secretary_employee_id'],'secretary_employee_id'=>['required','integer','exists:empleados,id'],'communication_date'=>['nullable','required_if:committee_type,CCL','date'],'communication_reference'=>['nullable','string','max:255'],'communication_notes'=>['nullable','string','max:2000'],'notes'=>['nullable','string','max:3000'],'committee_type'=>['required','in:COPASST,CCL']];}
 public function messages():array{return['president_employee_id.different'=>'Presidente y Secretario deben ser personas diferentes.','communication_date.required_if'=>'La fecha de comunicación es obligatoria para el Comité de Convivencia Laboral.','effective_from.after_or_equal'=>'La vigencia no puede iniciar antes de la fecha de conformación.'];}
}
