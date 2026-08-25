<?php
namespace App\Models;

use App\Enums\CandidateStatus;
use Illuminate\Database\Eloquent\Model;

class CommitteeCandidate extends Model
{
    protected $fillable = ['formation_process_id', 'employee_id', 'photo_path', 'short_profile', 'proposal', 'registration_date', 'status', 'observations', 'eligibility_confirmed', 'eligibility_confirmed_by', 'created_by', 'updated_by'];
    protected $casts = ['registration_date' => 'datetime', 'status' => CandidateStatus::class, 'eligibility_confirmed' => 'boolean'];
    public function formationProcess() { return $this->belongsTo(CommitteeFormationProcess::class, 'formation_process_id'); }
    public function employee() { return $this->belongsTo(Empleado::class, 'employee_id'); }
}
