<?php

namespace Tests\Feature;

use App\Models\{Empleado, TrainingEvaluation, TrainingEvaluationAccess, TrainingEvaluationAttempt, TrainingQuestion, TrainingSession, TrainingTopic, User};
use App\Services\{EmployeePortalPendingItemsService, TrainingAttendanceIntegrationService, TrainingEvaluationService, TrainingSessionService};
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Str;
use Tests\TestCase;

class EmployeePortalTrainingIntegrationTest extends TestCase
{
    use DatabaseTransactions;

    private function companyUser(): User
    {
        $user = User::whereNotNull('company_id')->first();
        if (!$user) {
            $this->markTestSkipped('Sin usuario empresarial.');
        }

        return $user;
    }

    public function test_freezing_a_training_session_opens_attendance_immediately_within_its_window(): void
    {
        $user = $this->companyUser();
        $employee = Empleado::where('company_id', $user->company_id)->first();
        if (!$employee) {
            $this->markTestSkipped('Sin empleado empresarial.');
        }

        $sessionService = app(TrainingSessionService::class);
        $session = $sessionService->create([
            'title' => 'Sesión portal test ' . Str::random(6),
            'description' => 'Prueba de integración',
            'training_type' => 'training',
            'scheduled_start_at' => now()->subMinutes(5),
            'scheduled_end_at' => now()->addHour(),
            'planned_duration_minutes' => 65,
            'modality' => 'presential',
            'location' => 'Sala',
            'instructor_type' => 'internal',
            'instructor_employee_id' => null,
            'audience_type' => 'specific_employees',
            'audience_description' => 'Prueba',
            'specific_employee_ids' => [$employee->id],
            'requires_attendance' => true,
            'requires_signature' => true,
            'requires_material' => false,
            'requires_execution_report' => false,
            'extraordinary_reason' => 'Prueba técnica de integración con el portal de firmas',
            'extraordinary_origin' => 'other',
        ], $user->company_id, $user->id);
        $sessionService->schedule($session, $user->id);

        $integration = app(TrainingAttendanceIntegrationService::class);
        $result = $integration->freeze($session, $user->id);

        $this->assertSame('open', $result['event']->fresh()->status);
        $this->assertTrue($result['event']->fresh()->isOpen(), 'El evento debe quedar realmente abierto para firmar, sin pasos manuales adicionales.');

        $pending = app(EmployeePortalPendingItemsService::class)->forEmployee($employee);
        $participant = $result['event']->participants()->where('employee_id', $employee->id)->first();
        $this->assertTrue($pending->contains(fn ($i) => $i->category === 'attendance' && $i->signableId === (string) $participant->id), 'La asistencia de la sesión de capacitación debe verse como pendiente en el portal sin pasos manuales adicionales.');
    }

    public function test_a_published_evaluation_appears_as_pending_and_the_portal_redirect_grants_a_working_access(): void
    {
        $user = $this->companyUser();
        $employee = Empleado::where('company_id', $user->company_id)->first();
        if (!$employee) {
            $this->markTestSkipped('Sin empleado empresarial.');
        }

        $topic = TrainingTopic::create([
            'company_id' => $user->company_id,
            'code' => 'TEST-EVAL-' . Str::random(6),
            'name' => 'Tema evaluación portal',
            'description' => 'Prueba',
            'training_type' => 'training',
            'general_objective' => 'Validar',
            'is_active' => true,
            'created_by' => $user->id,
        ]);
        $sessionService = app(TrainingSessionService::class);
        $session = $sessionService->create([
            'title' => 'Sesión con evaluación ' . Str::random(6),
            'description' => 'Prueba',
            'training_type' => 'training',
            'training_topic_id' => $topic->id,
            'scheduled_start_at' => now()->subHour(),
            'scheduled_end_at' => now()->subMinutes(30),
            'planned_duration_minutes' => 30,
            'modality' => 'presential',
            'location' => 'Sala',
            'instructor_type' => 'internal',
            'instructor_employee_id' => null,
            'audience_type' => 'specific_employees',
            'audience_description' => 'Prueba',
            'specific_employee_ids' => [$employee->id],
            'requires_attendance' => true,
            'requires_signature' => true,
            'requires_material' => false,
            'requires_execution_report' => false,
            'extraordinary_reason' => 'Prueba técnica de integración con el portal de firmas',
            'extraordinary_origin' => 'other',
        ], $user->company_id, $user->id);
        $sessionService->schedule($session, $user->id);
        $freeze = app(TrainingAttendanceIntegrationService::class)->freeze($session, $user->id);

        $question = TrainingQuestion::create([
            'company_id' => $user->company_id,
            'training_topic_id' => $topic->id,
            'question_text' => 'Pregunta de prueba',
            'question_type' => 'single_choice',
            'default_points' => 1,
            'is_active' => true,
            'created_by' => $user->id,
        ]);
        $question->options()->create(['option_text' => 'Correcta', 'is_correct' => true, 'sort_order' => 1]);
        $question->options()->create(['option_text' => 'Incorrecta', 'is_correct' => false, 'sort_order' => 2]);

        $evaluation = TrainingEvaluation::create([
            'company_id' => $user->company_id,
            'training_topic_id' => $topic->id,
            'training_session_id' => $session->id,
            'uuid' => (string) Str::uuid(),
            'title' => 'Evaluación portal test',
            'instructions' => 'Responda',
            'evaluation_type' => 'knowledge_quiz',
            'passing_score' => 60,
            'maximum_attempts' => 2,
            'requires_reinforcement' => true,
            'requires_confirmed_attendance' => false,
            'status' => 'draft',
            'created_by' => $user->id,
        ]);
        app(TrainingEvaluationService::class)->publish($evaluation, [$question->id], $user->id);

        $pending = app(EmployeePortalPendingItemsService::class)->forEmployee($employee);
        $item = $pending->first(fn ($i) => $i->category === 'evaluacion' && $i->label === 'Evaluación: ' . $evaluation->title);
        $this->assertNotNull($item, 'La evaluación publicada debe verse como pendiente en el portal.');

        $access = TrainingEvaluationAccess::where('training_evaluation_id', $evaluation->id)->first();
        $this->assertSame((string) $access->id, $item->signableId);

        $response = $this->withSession([
            'employee_portal.empleado_id' => $employee->id,
            'employee_portal.last_activity' => now(),
        ])->get(route('employee-portal.evaluation.redirect', ['access' => $access->id]));
        $response->assertRedirect();
        $redirectUrl = $response->headers->get('Location');
        $this->assertStringContainsString('evaluacion-capacitacion', $redirectUrl);

        // El nuevo token debe funcionar en el flujo público existente.
        $this->get($redirectUrl)->assertOk();

        // El token original (usado en publish()) debe haber quedado invalidado.
        $this->assertNotSame($access->token_hash, $access->fresh()->token_hash);
    }

    public function test_an_employee_cannot_redirect_to_another_employees_evaluation(): void
    {
        $user = $this->companyUser();
        $employees = Empleado::where('company_id', $user->company_id)->limit(2)->get();
        if ($employees->count() < 2) {
            $this->markTestSkipped('Se necesitan al menos 2 empleados.');
        }
        [$owner, $intruder] = $employees;

        $topic = TrainingTopic::create([
            'company_id' => $user->company_id,
            'code' => 'TEST-EVAL2-' . Str::random(6),
            'name' => 'Tema evaluación ajena',
            'description' => 'Prueba',
            'training_type' => 'training',
            'general_objective' => 'Validar',
            'is_active' => true,
            'created_by' => $user->id,
        ]);
        $sessionService = app(TrainingSessionService::class);
        $session = $sessionService->create([
            'title' => 'Sesión evaluación ajena ' . Str::random(6),
            'description' => 'Prueba',
            'training_type' => 'training',
            'training_topic_id' => $topic->id,
            'scheduled_start_at' => now()->subHour(),
            'scheduled_end_at' => now()->subMinutes(30),
            'planned_duration_minutes' => 30,
            'modality' => 'presential',
            'location' => 'Sala',
            'instructor_type' => 'internal',
            'instructor_employee_id' => null,
            'audience_type' => 'specific_employees',
            'audience_description' => 'Prueba',
            'specific_employee_ids' => [$owner->id],
            'requires_attendance' => true,
            'requires_signature' => true,
            'requires_material' => false,
            'requires_execution_report' => false,
            'extraordinary_reason' => 'Prueba técnica de integración con el portal de firmas',
            'extraordinary_origin' => 'other',
        ], $user->company_id, $user->id);
        $sessionService->schedule($session, $user->id);
        app(TrainingAttendanceIntegrationService::class)->freeze($session, $user->id);

        $question = TrainingQuestion::create([
            'company_id' => $user->company_id,
            'training_topic_id' => $topic->id,
            'question_text' => 'Pregunta',
            'question_type' => 'single_choice',
            'default_points' => 1,
            'is_active' => true,
            'created_by' => $user->id,
        ]);
        $question->options()->create(['option_text' => 'A', 'is_correct' => true, 'sort_order' => 1]);

        $evaluation = TrainingEvaluation::create([
            'company_id' => $user->company_id,
            'training_topic_id' => $topic->id,
            'training_session_id' => $session->id,
            'uuid' => (string) Str::uuid(),
            'title' => 'Evaluación ajena',
            'instructions' => 'Responda',
            'evaluation_type' => 'knowledge_quiz',
            'passing_score' => 60,
            'requires_reinforcement' => false,
            'requires_confirmed_attendance' => false,
            'status' => 'draft',
            'created_by' => $user->id,
        ]);
        app(TrainingEvaluationService::class)->publish($evaluation, [$question->id], $user->id);
        $access = TrainingEvaluationAccess::where('training_evaluation_id', $evaluation->id)->first();

        $this->withSession([
            'employee_portal.empleado_id' => $intruder->id,
            'employee_portal.last_activity' => now(),
        ])->get(route('employee-portal.evaluation.redirect', ['access' => $access->id]))->assertForbidden();
    }
}
