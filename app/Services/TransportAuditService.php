<?php
namespace App\Services;
use App\Models\TransportAudit;
class TransportAuditService {public function record(int $companyId,string $event,string $subjectType,int $subjectId,?int $userId,array $metadata=[]):void{TransportAudit::create(['company_id'=>$companyId,'event'=>$event,'subject_type'=>$subjectType,'subject_id'=>$subjectId,'user_id'=>$userId,'metadata'=>$metadata,'created_at'=>now()]);}}
