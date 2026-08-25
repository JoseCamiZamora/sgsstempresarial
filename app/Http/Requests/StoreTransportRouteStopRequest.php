<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;use Illuminate\Validation\Rule;
class StoreTransportRouteStopRequest extends FormRequest {public function authorize():bool{return $this->user()?->can('transporte.rutas.gestionar')??false;}public function rules():array{return['stop_order'=>'required|integer|min:1|max:999','name'=>'required|string|max:255','address_reference'=>'nullable|string|max:500','planned_time'=>'nullable|date_format:H:i','notes'=>'nullable|string|max:1000'];}}
