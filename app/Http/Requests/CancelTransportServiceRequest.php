<?php
namespace App\Http\Requests;use Illuminate\Foundation\Http\FormRequest;class CancelTransportServiceRequest extends FormRequest{public function authorize():bool{return$this->user()?->can('transporte.programacion.cancelar')??false;}public function rules():array{return['reason'=>'required|string|min:10|max:2000'];}}
