<?php
namespace App\Models;

use App\Enums\CommitteeStatus;
use App\Enums\CommitteeType;
use Illuminate\Database\Eloquent\Model;

class Committee extends Model
{
    protected $fillable = ['perfil_empresa_id', 'type', 'name', 'status', 'created_by', 'updated_by'];
    protected $casts = ['type' => CommitteeType::class, 'status' => CommitteeStatus::class];
    public function company() { return $this->belongsTo(PerfilEmpresa::class, 'perfil_empresa_id'); }
    public function periods() { return $this->hasMany(CommitteePeriod::class); }
    public function formationProcesses() { return $this->hasMany(CommitteeFormationProcess::class); }
    public function latestProcess() { return $this->hasOne(CommitteeFormationProcess::class)->latestOfMany(); }
    public function finalFormations() { return $this->hasMany(CommitteeFormation::class); }
    public function latestFinalFormation() { return $this->hasOne(CommitteeFormation::class)->latestOfMany(); }
    public function functions() { return $this->hasMany(CommitteeFunction::class); }
    public function scheduleItems() { return $this->hasMany(CommitteeScheduleItem::class); }
    public function meetings() { return $this->hasMany(CommitteeMeeting::class); }
    public function commitments() { return $this->hasMany(CommitteeCommitment::class); }
    public function operationalAlerts() { return $this->hasMany(CommitteeOperationalAlert::class); }
    public function reports() { return $this->hasMany(CommitteeReport::class); }
    public function operationalAudits() { return $this->hasMany(CommitteeOperationalAudit::class); }
}
