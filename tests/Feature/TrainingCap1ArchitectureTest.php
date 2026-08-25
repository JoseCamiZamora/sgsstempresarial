<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TrainingCap1ArchitectureTest extends TestCase
{
    public function test_cap1_persistence_has_company_scope_and_traceability(): void
    {
        $this->assertTrue(Schema::hasColumns('training_needs', ['company_id', 'origin_type', 'priority', 'status']));
        $this->assertTrue(Schema::hasColumns('training_programs', ['company_id', 'year', 'version', 'status', 'approved_by']));
        $this->assertTrue(Schema::hasColumns('training_program_items', ['training_program_id', 'annual_work_plan_item_id', 'planned_month', 'responsible_employee_id']));
        $this->assertTrue(Schema::hasColumns('training_program_reviews', ['committee_id', 'committee_meeting_id', 'senior_management_participation']));
        $this->assertTrue(Schema::hasTable('training_need_risk'));
        $this->assertTrue(Schema::hasTable('training_need_program_item'));
    }

    public function test_training_module_does_not_duplicate_attendance_or_certification_domains(): void
    {
        $this->assertFalse(Schema::hasTable('training_attendances'));
        $this->assertFalse(Schema::hasTable('training_certificates'));
    }
}
