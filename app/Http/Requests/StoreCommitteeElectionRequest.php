<?php
namespace App\Http\Requests;use Illuminate\Foundation\Http\FormRequest;
class StoreCommitteeElectionRequest extends FormRequest {public function authorize():bool{return $this->user()?->can('comites.elecciones.crear')??false;}public function rules():array{return['max_selections'=>['required','integer','min:1','max:10']];}}
