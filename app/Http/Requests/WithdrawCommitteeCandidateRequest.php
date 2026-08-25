<?php
namespace App\Http\Requests;use Illuminate\Foundation\Http\FormRequest;
class WithdrawCommitteeCandidateRequest extends FormRequest {public function authorize():bool{return $this->user()?->can('comites.candidatos.editar')??false;}public function rules():array{return['reason'=>['required','string','min:10','max:2000']];}public function messages():array{return['reason.required'=>'Debe indicar el motivo del retiro.','reason.min'=>'El motivo del retiro debe tener al menos :min caracteres.'];}}
