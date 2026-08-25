<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class CompleteTransportPreOperationalRequest extends FormRequest { public function authorize():bool{return true;} public function rules():array{return ['template_id'=>'required|integer','results'=>'required|array|min:1','results.*.item_id'=>'required|integer','results.*.result'=>'required|in:compliant,non_compliant,not_applicable','results.*.observation'=>'nullable|string|max:1000','override_reason'=>'nullable|string|min:10|max:1000'];} public function attributes():array{return ['results'=>'resultados del checklist','override_reason'=>'justificación de excepción'];} }
