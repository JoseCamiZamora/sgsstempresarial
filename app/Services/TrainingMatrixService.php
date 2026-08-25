<?php
namespace App\Services;
use App\Models\Empleado;
class TrainingMatrixService
{
 public function __construct(private TrainingRequirementResolverService $resolver){}
 public function paginate(int $companyId,array $filters=[]){$perPage=min((int)($filters['per_page']??20),1000);$employees=Empleado::where('company_id',$companyId)->active()->when($filters['job']??null,fn($q,$v)=>$q->where('cargo',$v))->when($filters['area']??null,fn($q,$v)=>$q->where('area_departamento',$v))->when($filters['employee']??null,fn($q,$v)=>$q->where('nombre_completo','like','%'.$v.'%'))->orderBy('nombre_completo')->paginate($perPage)->withQueryString();$employees->getCollection()->transform(function($employee)use($filters){$route=$this->resolver->routeFor($employee);if($state=$filters['state']??null)$route=$route->where('status',$state)->values();$applicable=$route->count();$satisfied=$route->whereIn('status',['completed','expiring'])->count();$employee->training_route=$route;$employee->training_progress=$applicable?round($satisfied/$applicable*100,2):null;return$employee;});return$employees;}
}
