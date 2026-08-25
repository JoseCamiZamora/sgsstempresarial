<?php

namespace Tests\Unit;

use App\Services\TrainingRegulationService;
use PHPUnit\Framework\TestCase;

class TrainingRegulationServiceTest extends TestCase
{
    public function test_cap1_requires_an_annual_review_with_the_expected_participants(): void
    {
        $service = new TrainingRegulationService();

        $this->assertTrue($service->requiresAnnualProgramReview());
        $this->assertSame(['copasst_or_vigia', 'senior_management'], $service->getReviewParticipants());
        $this->assertNotEmpty($service->getApplicableRequirements());
        $this->assertContains('program_document', $service->getRequiredEvidenceTypes());
    }

    public function test_cap1_does_not_define_a_universal_training_duration(): void
    {
        $configuration = require dirname(__DIR__, 2).'/config/training.php';

        $this->assertArrayNotHasKey('mandatory_duration_minutes', $configuration);
        $this->assertArrayNotHasKey('universal_duration', $configuration);
    }
}
