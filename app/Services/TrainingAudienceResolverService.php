<?php
namespace App\Services;
use App\Models\{Empleado,TrainingSession};
use Illuminate\Support\Collection;
class TrainingAudienceResolverService
{
 public function resolve(TrainingSession $session):Collection{return match($session->audience_type){'all_workers'=>$this->resolveAllWorkers($session->company_id),'area'=>$this->resolveByArea($session->company_id,$session->audience_description),'job'=>$this->resolveByJob($session->company_id,$session->audience_description),'risk_exposure','specific_group','specific_employees','other'=>$this->resolveSpecificEmployees($session->company_id,$session->specific_employee_ids??[]),default=>collect()};}
 public function resolveAllWorkers(int$company):Collection{return$this->base($company)->get();}
 public function resolveByArea(int$company,?string$area):Collection{return$this->base($company)->where('area_departamento',$area)->get();}
 public function resolveByJob(int$company,?string$job):Collection{return$this->base($company)->where('cargo',$job)->get();}
 public function resolveByRiskExposure(int$company,array$riskIds=[]):Collection{return collect();}
 public function resolveSpecificEmployees(int$company,array$ids):Collection{return$this->base($company)->whereIn('id',$ids)->get();}
 public function resolveContractors(int$company):Collection{return collect();}
 public function resolveCustomGroup(int$company,array$ids=[]):Collection{return$this->resolveSpecificEmployees($company,$ids);}
 private function base(int$company){return Empleado::where('company_id',$company)->active()->orderBy('nombre_completo');}
}
