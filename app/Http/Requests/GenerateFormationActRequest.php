<?php
namespace App\Http\Requests;
class GenerateFormationActRequest extends FinalizeCommitteeFormationRequest {public function authorize():bool{return $this->user()?->can('comites.acta_conformacion.generar')??false;}}
