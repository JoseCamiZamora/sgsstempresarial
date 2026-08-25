<?php
namespace App\Models;

use App\Enums\MemberRepresentation;
use App\Enums\MemberType;
use Illuminate\Database\Eloquent\Model;

class CommitteeMember extends Model
{
    protected $fillable = ['committee_period_id', 'employee_id', 'representation_type', 'member_type', 'source_type', 'election_id', 'election_candidate_id', 'elected_votes', 'elected_rank', 'position', 'designation_date', 'status', 'document_path', 'notes', 'eligibility_confirmed', 'eligibility_confirmed_by', 'created_by', 'updated_by'];
    protected $casts = ['representation_type' => MemberRepresentation::class, 'member_type' => MemberType::class, 'designation_date' => 'date', 'eligibility_confirmed' => 'boolean'];
    public function period() { return $this->belongsTo(CommitteePeriod::class, 'committee_period_id'); }
    public function employee() { return $this->belongsTo(Empleado::class, 'employee_id'); }
    public function roles() { return $this->hasMany(CommitteeMemberRole::class); }
    public function election() { return $this->belongsTo(CommitteeElection::class); }
    public function electionCandidate() { return $this->belongsTo(CommitteeElectionCandidate::class); }
}
