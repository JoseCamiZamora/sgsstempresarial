<?php
namespace App\Http\Controllers;use App\Http\Requests\CopyTransportProgrammingRequest;use App\Services\TransportProgrammingCopyService;
class TransportProgrammingCopyController extends Controller{public function __invoke(CopyTransportProgrammingRequest$r,TransportProgrammingCopyService$service){$count=$service->copy($r->user()->company_id,$r->validated(),$r->user()->id);return redirect()->route('transport.operation.index',['date'=>$r->validated('target_from')])->with('success',"Se copiaron {$count} servicios con validación de conflictos.");}}
