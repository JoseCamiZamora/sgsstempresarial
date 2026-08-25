<?php
namespace App\Services;
use App\Models\Empleado;
use Illuminate\Support\Collection;
class TrainingGapService
{
 public function __construct(private TrainingRequirementResolverService $resolver){}
 public function forCompany(int$companyId,array$filters=[]):Collection{return Empleado::where('company_id',$companyId)->active()->when($filters['job']??null,fn($q,$v)=>$q->where('cargo',$v))->when($filters['area']??null,fn($q,$v)=>$q->where('area_departamento',$v))->orderBy('nombre_completo')->get()->flatMap(function($employee){return$this->resolver->routeFor($employee)->whereIn('status',['pending','expired'])->map(function($route)use($employee){$requirement=$route['requirement'];return['key'=>hash('sha256',$employee->company_id.'|'.$employee->id.'|'.$requirement->id),'employee'=>$employee,'requirement'=>$requirement,'status'=>$route['status'],'priority'=>$requirement->priority,'expires'=>$route['expires']];});});}
 public function summary(int$companyId,array$filters=[]):array{$gaps=$this->forCompany($companyId,$filters);return['total'=>$gaps->count(),'employees'=>$gaps->pluck('employee.id')->unique()->count(),'critical'=>$gaps->whereIn('priority',['high','critical'])->count(),'by_area'=>$gaps->groupBy('employee.area_departamento')->map->count()->sortDesc(),'by_job'=>$gaps->groupBy('employee.cargo')->map->count()->sortDesc()];}
}
