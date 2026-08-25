<?php
namespace App\Services; use App\Models\TrainingProgramItem;
class TrainingProgressService { public function update(TrainingProgramItem$item):string{$closed=$item->sessions()->where('status','closed')->count();$total=max(1,(int)$item->planned_sessions);$status=$closed===0?($item->sessions()->whereIn('status',['scheduled','called','attendance_open','in_progress','completed'])->exists()?'scheduled':'planned'):($closed<$total?'partially_executed':'executed');$item->update(['status'=>$status]);return$status;} }
