<?php

namespace App\Http\Requests;

use App\Enums\CommitteeType;
use App\Models\Empleado;
use App\Services\CommitteeRegulationService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreCommitteeFormationRequest extends FormRequest
{
    public function authorize(): bool { return $this->user()?->can('comites.crear') ?? false; }

    public function rules(): array
    {
        return [
            'type' => ['required', Rule::enum(CommitteeType::class)],
            'start_date' => ['required', 'date'], 'end_date' => ['required', 'date', 'after:start_date'],
            'title' => ['required', 'string', 'max:255'], 'description' => ['nullable', 'string', 'max:5000'],
            'call_start_date' => ['required', 'date'], 'call_end_date' => ['required', 'date', 'after_or_equal:call_start_date'],
            'candidate_registration_start' => ['required', 'date', 'after_or_equal:call_start_date'],
            'candidate_registration_end' => ['required', 'date', 'after:candidate_registration_start'],
            'election_start_at' => ['required', 'date', 'after:candidate_registration_end'],
            'election_end_at' => ['required', 'date', 'after:election_start_at'],
            'requirements' => ['nullable', 'string', 'max:5000'], 'notes' => ['nullable', 'string', 'max:5000'],
            'employer_members' => ['nullable', 'array'],
            'employer_members.*.employee_id' => ['required', 'integer', 'distinct', 'exists:empleados,id'],
            'employer_members.*.member_type' => ['required', Rule::in(['principal', 'substitute'])],
            'employer_members.*.eligibility_confirmed' => ['accepted'],
            'candidates' => ['nullable', 'array'],
            'candidates.*.employee_id' => ['required', 'integer', 'distinct', 'exists:empleados,id'],
            'candidates.*.photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'candidates.*.short_profile' => ['nullable', 'string', 'max:1000'],
            'candidates.*.proposal' => ['nullable', 'string', 'max:2000'],
            'candidates.*.eligibility_confirmed' => ['accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'date' => 'El campo :attribute debe contener una fecha válida.',
            'end_date.after' => 'La fecha de finalización del período debe ser posterior a la fecha de inicio.',
            'call_end_date.after_or_equal' => 'El cierre de la convocatoria debe ser igual o posterior a su apertura.',
            'candidate_registration_start.after_or_equal' => 'El inicio de inscripciones debe ser igual o posterior a la apertura de la convocatoria.',
            'candidate_registration_end.after' => 'El cierre de inscripciones debe ser posterior al inicio de inscripciones.',
            'election_start_at.after' => 'La apertura de la elección debe ser posterior al cierre de inscripciones.',
            'election_end_at.after' => 'El cierre de la elección debe ser posterior a su apertura.',
            'exists' => 'El valor seleccionado para :attribute no es válido.',
            'distinct' => 'No puede seleccionar el mismo :attribute más de una vez.',
            'accepted' => 'Debe aceptar la confirmación de :attribute.',
            'image' => 'La fotografía del candidato debe ser una imagen válida.',
            'mimes' => 'La fotografía debe ser un archivo JPG, JPEG, PNG o WEBP.',
            'candidates.*.photo.max' => 'La fotografía no puede superar los 2 MB.',
            'max.string' => 'El campo :attribute no puede superar :max caracteres.',
        ];
    }

    public function attributes(): array
    {
        return [
            'type' => 'tipo de comité', 'start_date' => 'inicio del período', 'end_date' => 'fin del período',
            'title' => 'título', 'description' => 'descripción', 'call_start_date' => 'apertura de convocatoria',
            'call_end_date' => 'cierre de convocatoria', 'candidate_registration_start' => 'inicio de inscripciones',
            'candidate_registration_end' => 'cierre de inscripciones', 'election_start_at' => 'apertura de elección',
            'election_end_at' => 'cierre de elección', 'requirements' => 'requisitos', 'notes' => 'observaciones',
            'employer_members.*.employee_id' => 'representante del empleador',
            'employer_members.*.member_type' => 'tipo de representante',
            'employer_members.*.eligibility_confirmed' => 'elegibilidad del representante',
            'candidates.*.employee_id' => 'candidato', 'candidates.*.photo' => 'fotografía del candidato',
            'candidates.*.short_profile' => 'perfil breve', 'candidates.*.proposal' => 'propuesta del candidato',
            'candidates.*.eligibility_confirmed' => 'elegibilidad del candidato',
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator) {
            if (!$this->filled('type')) return;
            $type = CommitteeType::tryFrom((string) $this->input('type'));
            if (!$type) return;
            $composition = app(CommitteeRegulationService::class)->composition($type, app(CommitteeRegulationService::class)->activeWorkersCount());
            $members = collect($this->input('employer_members', []));
            if ($members->where('member_type', 'principal')->count() > $composition['employer_principals'])
                $validator->errors()->add('employer_members', 'Supera el máximo de representantes principales permitido.');
            if ($members->where('member_type', 'substitute')->count() > $composition['employer_substitutes'])
                $validator->errors()->add('employer_members', 'Supera el máximo de representantes suplentes permitido.');

            $ids = $members->pluck('employee_id')->merge(collect($this->input('candidates', []))->pluck('employee_id'))->filter()->unique();
            $activeIds = Empleado::active()->whereIn('id', $ids)->pluck('id');
            foreach ($ids->diff($activeIds) as $id) $validator->errors()->add('employees', "El empleado {$id} no está activo.");

            if ($validator->errors()->hasAny(['candidate_registration_start', 'candidate_registration_end'])) return;

            if (count($this->input('candidates', [])) && !now()->between($this->date('candidate_registration_start'), $this->date('candidate_registration_end'))) {
                $message = now()->lt($this->date('candidate_registration_start'))
                    ? 'La inscripción de candidatos todavía no ha iniciado. Puede guardar el proceso sin candidatos y registrarlos cuando se abra la inscripción.'
                    : 'El período de inscripción de candidatos ya finalizó.';
                $validator->errors()->add('candidates', $message);
            }
        }];
    }
}
