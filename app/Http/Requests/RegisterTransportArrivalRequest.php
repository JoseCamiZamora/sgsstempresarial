<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class RegisterTransportArrivalRequest extends FormRequest { public function authorize():bool{return true;} public function rules():array{return ['arrival_odometer'=>'nullable|numeric|min:0','receiver_employee_id'=>'nullable|integer','receiver_name'=>'nullable|string|max:255','observation'=>'nullable|string|max:2000','signature'=>'nullable|string|max:700000'];} public function attributes():array{return ['arrival_odometer'=>'kilometraje de llegada','receiver_employee_id'=>'responsable de recepción','receiver_name'=>'nombre del responsable','signature'=>'firma'];} }
