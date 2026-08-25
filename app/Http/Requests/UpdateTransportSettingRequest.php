<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTransportSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('transporte.configuracion.gestionar') ?? false;
    }

    public function rules(): array
    {
        $companyId = $this->user()->company_id;

        return [
            'service_name' => 'required|string|max:255',
            'site_name' => 'nullable|string|max:255',
            'workday_starts_at' => 'nullable|date_format:H:i',
            'workday_ends_at' => 'nullable|date_format:H:i|after:workday_starts_at',
            'arrival_tolerance_minutes' => 'required|integer|min:0|max:180',
            'departure_tolerance_minutes' => 'required|integer|min:0|max:180',
            'turnaround_minutes' => 'required|integer|min:0|max:240',
            'upcoming_service_hours' => 'required|integer|min:1|max:168',
            'requires_arrival_signature' => 'nullable|boolean',
            'requires_departure_odometer' => 'nullable|boolean',
            'requires_arrival_odometer' => 'nullable|boolean',
            'active_weekdays' => 'nullable|array',
            'active_weekdays.*' => 'integer|between:1,7',
            'responsible_employee_id' => [
                'nullable',
                Rule::exists('empleados', 'id')->where(
                    fn ($query) => $query->where('company_id', $companyId)
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'workday_ends_at.after' => 'La hora de fin de jornada debe ser posterior a la hora de inicio de jornada.',
        ];
    }

    public function attributes(): array
    {
        return [
            'service_name' => 'nombre del servicio',
            'site_name' => 'sede',
            'workday_starts_at' => 'hora de inicio de jornada',
            'workday_ends_at' => 'hora de fin de jornada',
            'arrival_tolerance_minutes' => 'tolerancia de llegada',
            'departure_tolerance_minutes' => 'tolerancia de salida',
            'turnaround_minutes' => 'intervalo entre recursos',
            'upcoming_service_hours' => 'horas de alerta previa',
            'requires_arrival_signature' => 'exigir firma de llegada',
            'requires_departure_odometer' => 'exigir odómetro de salida',
            'requires_arrival_odometer' => 'exigir odómetro de llegada',
            'active_weekdays' => 'días activos',
            'active_weekdays.*' => 'día activo',
            'responsible_employee_id' => 'responsable',
        ];
    }
}
