<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class RegisterTransportDepartureRequest extends FormRequest { public function authorize():bool{return true;} public function rules():array{return ['departure_odometer'=>'nullable|numeric|min:0'];} public function attributes():array{return ['departure_odometer'=>'kilometraje de salida'];} }
