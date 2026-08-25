<?php
namespace App\Http\Requests;use Illuminate\Foundation\Http\FormRequest;class VerifyAttendanceParticipantRequest extends FormRequest{public function authorize(){return true;}public function rules(){return['personal_code'=>'required|string|min:8|max:30'];}public function messages(){return['personal_code.required'=>'Ingrese su código personal de asistencia.'];}}
