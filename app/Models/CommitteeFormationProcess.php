<?php
namespace App\Models;

use App\Enums\CommitteeStatus;
use Illuminate\Database\Eloquent\Model;

class CommitteeFormationProcess extends Model
{
    protected $fillable = [
        'committee_id', 'committee_period_id', 'status', 'title', 'description', 'call_start_date', 'call_end_date',
        'candidate_registration_start', 'candidate_registration_end', 'election_start_at', 'election_end_at',
        'requirements', 'notes', 'workers_count', 'required_employer_principals', 'required_employer_substitutes',
        'required_worker_principals', 'required_worker_substitutes', 'obligation_mode', 'regulation_reference',
        'regulation_snapshot', 'created_by', 'updated_by',
    ];
    protected $casts = [
        'status' => CommitteeStatus::class, 'call_start_date' => 'date', 'call_end_date' => 'date',
        'candidate_registration_start' => 'datetime', 'candidate_registration_end' => 'datetime',
        'election_start_at' => 'datetime', 'election_end_at' => 'datetime', 'regulation_snapshot' => 'array',
    ];
    public function committee() { return $this->belongsTo(Committee::class); }
    public function period() { return $this->belongsTo(CommitteePeriod::class, 'committee_period_id'); }
    public function candidates() { return $this->hasMany(CommitteeCandidate::class, 'formation_process_id'); }
    public function election() { return $this->hasOne(CommitteeElection::class, 'formation_process_id'); }
}
