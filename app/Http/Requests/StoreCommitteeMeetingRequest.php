<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCommitteeMeetingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('comites.reuniones.crear') ?? false;
    }

    protected function prepareForValidation(): void
    {
        $startTime = $this->input('start_time');

        if (is_string($startTime) && preg_match('/^\d{2}:\d{2}:\d{2}$/', $startTime)) {
            $this->merge(['start_time' => substr($startTime, 0, 5)]);
        }
    }

    public function rules(): array
    {
        return [
            'schedule_item_id' => 'nullable|exists:committee_schedule_items,id',
            'meeting_type' => ['required', Rule::in(['ordinary', 'extraordinary'])],
            'meeting_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'location' => 'nullable|string|max:255',
            'virtual_link' => 'nullable|url|max:500',
            'modality' => ['required', Rule::in(['presencial', 'virtual', 'mixta'])],
            'subject' => 'required|string|max:255',
            'call_notes' => 'nullable|string|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.required' => 'Debe indicar la hora de inicio de la reunión.',
            'start_time.date_format' => 'La hora de inicio debe tener un formato válido.',
            'meeting_date.required' => 'Debe indicar la fecha de la reunión.',
            'subject.required' => 'Debe indicar el asunto de la reunión.',
            'modality.in' => 'La modalidad seleccionada no es válida.',
        ];
    }
}
