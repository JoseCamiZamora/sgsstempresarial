<?php
namespace Tests\Unit;
use App\Enums\CommitteeType;use App\Models\CommitteeMember;use App\Services\CommitteeRegulationService;use Carbon\Carbon;use Tests\TestCase;
class CommitteeFormationRulesTest extends TestCase {
 public function test_copasst_president_must_represent_employer():void{$service=new CommitteeRegulationService();$employer=new CommitteeMember(['representation_type'=>'employer']);$worker=new CommitteeMember(['representation_type'=>'worker']);$this->assertTrue($service->canBePresident(CommitteeType::COPASST,$employer));$this->assertFalse($service->canBePresident(CommitteeType::COPASST,$worker));}
 public function test_ccl_president_can_come_from_either_representation():void{$service=new CommitteeRegulationService();$this->assertTrue($service->canBePresident(CommitteeType::CCL,new CommitteeMember(['representation_type'=>'employer'])));$this->assertTrue($service->canBePresident(CommitteeType::CCL,new CommitteeMember(['representation_type'=>'worker'])));}
 public function test_secretary_can_be_any_valid_member():void{$service=new CommitteeRegulationService();$this->assertTrue($service->canBeSecretary(CommitteeType::COPASST,new CommitteeMember(['representation_type'=>'worker'])));$this->assertTrue($service->canBeSecretary(CommitteeType::CCL,new CommitteeMember(['representation_type'=>'employer'])));}
 public function test_copasst_president_role_is_annual_while_committee_is_two_years():void{$service=new CommitteeRegulationService();$start=Carbon::parse('2026-08-20');$this->assertSame('2027-08-19',$service->presidentTermEndsAt(CommitteeType::COPASST,$start)->toDateString());$this->assertSame(2,$service->termYears(CommitteeType::COPASST));}
}
