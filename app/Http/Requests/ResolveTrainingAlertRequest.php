<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class ResolveTrainingAlertRequest extends FormRequest{public function authorize():bool{return true;}public function rules():array{return['action'=>'required|in:acknowledge,resolve,dismiss','notes'=>'nullable|string|max:2000'];}}
