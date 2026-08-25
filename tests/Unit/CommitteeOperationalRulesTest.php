<?php

namespace Tests\Unit;

use App\Enums\CommitteeType;
use App\Services\CommitteeRegulationService;
use Tests\TestCase;

class CommitteeOperationalRulesTest extends TestCase
{
    private CommitteeRegulationService $rules;

    protected function setUp(): void
    {
        parent::setUp();
        $this->rules = new CommitteeRegulationService();
    }

    public function test_both_committees_have_monthly_meetings(): void
    {
        $this->assertSame(1, $this->rules->meetingFrequencyMonths(CommitteeType::COPASST));
        $this->assertSame(1, $this->rules->meetingFrequencyMonths(CommitteeType::CCL));
        $this->assertTrue($this->rules->requiresMonthlyMeeting(CommitteeType::COPASST));
        $this->assertTrue($this->rules->requiresMonthlyMeeting(CommitteeType::CCL));
    }

    /** @dataProvider quorumCases */
    public function test_quorum_is_half_plus_one(int $eligible, int $expected): void
    {
        $this->assertSame($expected, $this->rules->quorumRequired($eligible));
    }

    public static function quorumCases(): array
    {
        return [[0, 0], [1, 1], [2, 2], [3, 2], [4, 3], [5, 3], [6, 4]];
    }

    public function test_ccl_requires_quarterly_and_annual_reports(): void
    {
        $this->assertTrue($this->rules->requiresQuarterlyReport(CommitteeType::CCL));
        $this->assertTrue($this->rules->requiresAnnualReport(CommitteeType::CCL));
        $this->assertFalse($this->rules->requiresQuarterlyReport(CommitteeType::COPASST));
    }

    public function test_normative_functions_are_configured_for_both_committees(): void
    {
        $this->assertNotEmpty($this->rules->normativeFunctions(CommitteeType::COPASST));
        $this->assertNotEmpty($this->rules->normativeFunctions(CommitteeType::CCL));
    }
}
