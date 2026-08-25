<?php

namespace Tests\Unit;

use App\Services\CommitteeRegulationService;
use Tests\TestCase;

class CommitteeRegulationServiceTest extends TestCase
{
    private CommitteeRegulationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CommitteeRegulationService();
    }

    /** @dataProvider copasstBoundaries */
    public function test_copasst_boundaries(int $workers, string $mode, int $principals): void
    {
        $result = $this->service->composition('COPASST', $workers);
        $this->assertSame($mode, $result['mode']);
        $this->assertSame($principals, $result['employer_principals']);
        $this->assertSame($mode === 'VIGIA_SST' ? 1 : $principals, $result['worker_principals']);
    }

    public static function copasstBoundaries(): array
    {
        return [[0, 'VIGIA_SST', 0], [5, 'VIGIA_SST', 0], [9, 'VIGIA_SST', 0], [10, 'COPASST', 1],
            [49, 'COPASST', 1], [50, 'COPASST', 2], [499, 'COPASST', 2], [500, 'COPASST', 3],
            [999, 'COPASST', 3], [1000, 'COPASST', 4], [5000, 'COPASST', 4]];
    }

    /** @dataProvider cclBoundaries */
    public function test_ccl_boundaries(int $workers, int $principals, int $substitutes): void
    {
        $result = $this->service->composition('CCL', $workers);
        $this->assertSame($principals, $result['employer_principals']);
        $this->assertSame($substitutes, $result['employer_substitutes']);
    }

    public static function cclBoundaries(): array
    {
        return [[0, 1, 0], [4, 1, 0], [5, 1, 1], [10, 1, 1], [19, 1, 1], [20, 2, 2], [21, 2, 2], [1000, 2, 2]];
    }
}
