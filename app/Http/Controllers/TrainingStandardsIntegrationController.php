<?php
namespace App\Http\Controllers;
use App\Services\TrainingStandardsEvidenceService;use Illuminate\Http\Request;
class TrainingStandardsIntegrationController extends Controller{public function index(Request$r,TrainingStandardsEvidenceService$s){$year=(int)$r->input('year',now()->year);$evidence=$s->getAvailableEvidence($r->user()->company_id,$year);return view('training.standards.index',compact('evidence','year'));}}
