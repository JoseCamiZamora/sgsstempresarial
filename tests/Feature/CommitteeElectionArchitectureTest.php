<?php
namespace Tests\Feature;
use App\Models\CommitteeBallot;use Illuminate\Support\Facades\Schema;use Tests\TestCase;
class CommitteeElectionArchitectureTest extends TestCase {
 public function test_ballot_has_no_voter_identity_columns_or_relation():void {$columns=Schema::getColumnListing('committee_ballots');foreach(['employee_id','voter_id','user_id','ip','user_agent','created_at'] as $forbidden)$this->assertNotContains($forbidden,$columns);$this->assertFalse(method_exists(new CommitteeBallot(),'voter'));}
 public function test_public_unknown_uuid_returns_not_found():void {$this->get('/votaciones/00000000-0000-4000-8000-000000000000')->assertNotFound();}
}
