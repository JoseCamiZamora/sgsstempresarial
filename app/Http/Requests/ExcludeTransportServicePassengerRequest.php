<?php
namespace App\Http\Requests;use Illuminate\Foundation\Http\FormRequest;class ExcludeTransportServicePassengerRequest extends FormRequest{public function authorize():bool{return$this->user()?->can('transporte.pasajeros_servicio.gestionar')??false;}public function rules():array{return['reason'=>'required|string|min:10|max:1000'];}}
