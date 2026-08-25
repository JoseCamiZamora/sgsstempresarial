<?php
namespace App\Http\Requests;use Illuminate\Foundation\Http\FormRequest;
class CastCommitteeVoteRequest extends FormRequest {public function authorize():bool{return true;}public function rules():array{return['token'=>['required','string','size:64'],'selections'=>['required','array','min:1'],'selections.*'=>['integer','distinct']];}public function messages():array{return['token.size'=>'La credencial electoral no es válida.','selections.required'=>'Debe seleccionar al menos un candidato.','selections.*.distinct'=>'No puede seleccionar el mismo candidato dos veces.'];}}
