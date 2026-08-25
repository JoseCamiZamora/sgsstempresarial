<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;use Illuminate\Validation\Rule;
class AssignTransportPassengerRequest extends FormRequest {public function authorize():bool{return $this->user()?->can('transporte.rutas.gestionar')??false;}public function rules():array{$company=$this->user()->company_id;return['transport_passenger_id'=>['required',Rule::exists('transport_passengers','id')->where(fn($q)=>$q->where('company_id',$company))],'transport_route_stop_id'=>'nullable|exists:transport_route_stops,id','direction'=>['required',Rule::in(array_keys(config('transport.directions')))],'valid_from'=>'required|date','valid_until'=>'nullable|date|after_or_equal:valid_from','status'=>'required|in:active,inactive'];}}
