<?php
namespace App\Models;

use App\Enums\CommitteeStatus;
use Illuminate\Database\Eloquent\Model;

class CommitteePeriod extends Model
{
    protected $fillable = ['committee_id', 'start_date', 'end_date', 'status', 'workers_count_snapshot', 'regulation_reference', 'created_by', 'updated_by'];
    protected $casts = ['start_date' => 'date', 'end_date' => 'date', 'status' => CommitteeStatus::class];
    public function committee() { return $this->belongsTo(Committee::class); }
    public function formationProcesses() { return $this->hasMany(CommitteeFormationProcess::class); }
    public function members() { return $this->hasMany(CommitteeMember::class); }
    public function roles() { return $this->hasMany(CommitteeMemberRole::class); }
    public function finalFormation() { return $this->hasOne(CommitteeFormation::class); }
}
