<?php
namespace App\Http\Requests;use Illuminate\Foundation\Http\FormRequest;class EmployeePortalAcknowledgeRequest extends FormRequest{public function authorize(){return true;}public function rules(){return['acknowledged'=>'accepted'];}public function messages(){return['acknowledged.accepted'=>'Debe confirmar el aviso de tratamiento de datos.'];}}
