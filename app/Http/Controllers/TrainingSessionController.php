<?php
namespace App\Http\Controllers;
use App\Http\Requests\StoreTrainingSessionRequest; use App\Models\{Empleado,TrainingInstructor,TrainingProgramItem,TrainingReinforcement,TrainingSession,TrainingSessionEvidence,TrainingTopic}; use App\Services\{TrainingAttendanceIntegrationService,TrainingAudienceResolverService,TrainingEvidenceService,TrainingInvitationService,TrainingSessionClosureService,TrainingSessionService}; use Barryvdh\DomPDF\Facade\Pdf; use Illuminate\Http\Request; use Illuminate\Support\Facades\Storage; use Illuminate\Support\Str;
class TrainingSessionController extends Controller
{
 public function index(){ $company=auth()->user()->company_id;$sessions=TrainingSession::forCompany($company)->with(['programItem','instructorEmployee','externalInstructor'])->latest('scheduled_start_at')->get();return view('training.sessions.index',compact('sessions')); }
 public function create(?TrainingProgramItem$item=null){$company=auth()->user()->company_id;if($item)abort_unless($item->program->company_id===$company,403);$topics=TrainingTopic::availableTo($company)->where('is_active',true)->get();$employees=Empleado::where('company_id',$company)->active()->get();$instructors=TrainingInstructor::forCompany($company)->where('is_active',true)->get();return view('training.sessions.create',compact('item','topics','employees','instructors'));}
 public function store(StoreTrainingSessionRequest$r,TrainingSessionService$s){$data=$r->validated();$item=$r->filled('training_program_item_id')?TrainingProgramItem::findOrFail($r->integer('training_program_item_id')):null;$session=$s->create($data,$r->user()->company_id,$r->user()->id,$item);$s->schedule($session,$r->user()->id);return redirect()->route('training.sessions.show',$session)->with('success','Sesión programada.');}
 public function show(TrainingSession$session,TrainingAudienceResolverService$resolver,TrainingSessionClosureService$closure){$this->own($session);$session->load(['company','programItem.program','topic','instructorEmployee','externalInstructor','attendanceEvent.participants.record.signature','attendanceEvent.evidences','evidences','reminders','changes']);$preview=$session->attendance_event_id?collect():$resolver->resolve($session);$checklist=$closure->checklist($session);$employees=Empleado::where('company_id',$session->company_id)->active()->get();return view('training.sessions.show',compact('session','preview','checklist','employees'));}
 public function freeze(TrainingSession$session,Request$r,TrainingAttendanceIntegrationService$s){$this->own($session);$result=$s->freeze($session,$r->user()->id);return redirect()->route('training.sessions.show',$session)->with(['success'=>'Población congelada y asistencia transversal habilitada.','attendance_token'=>$result['token'],'participant_codes'=>$result['codes']]);}
 public function invite(TrainingSession$session,Request$r,TrainingInvitationService$s){$this->own($session);$result=$s->send($session,$r->user()->id);if($result['sent']===0&&$result['failed']===0){return back()->with('warning','No se envió ninguna convocatoria: ningún participante convocado tiene un correo electrónico registrado.');}if($result['failed']>0){return back()->with('warning',"Convocatorias enviadas: {$result['sent']}; fallidas: {$result['failed']}. Revise que los correos registrados sean válidos.");}return back()->with('success',"Convocatorias enviadas: {$result['sent']}.");}
 public function addParticipant(TrainingSession$session,Request$r,TrainingSessionService$service){$this->own($session);$d=$r->validate(['employee_id'=>'required|exists:empleados,id','reason'=>'required|string|min:10|max:1000']);$employee=Empleado::where('company_id',$session->company_id)->active()->findOrFail($d['employee_id']);$event=$session->attendanceEvent()->firstOrFail();$code=strtoupper(trim($employee->cedula));$p=$event->participants()->firstOrCreate(['employee_id'=>$employee->id],['uuid'=>(string)Str::uuid(),'participant_type'=>'employee','name_snapshot'=>$employee->nombre_completo,'role_snapshot'=>$employee->cargo,'department_snapshot'=>$employee->area_departamento,'email'=>$employee->email_personal,'expected'=>true,'invitation_status'=>'pending','added_after_freeze'=>true,'credential_hash'=>hash('sha256',$code),'credential_expires_at'=>$event->attendance_closes_at]);$service->audit($session,'training_participant_added',$r->user()->id,['participant_id'=>$p->id,'reason'=>$d['reason']]);return back()->with(['success'=>'Participante agregado con trazabilidad.','participant_codes'=>[['name'=>$p->name_snapshot,'code'=>$code]]]);}
 public function excludeParticipant(TrainingSession$session,$participant,Request$r,TrainingSessionService$service){$this->own($session);$d=$r->validate(['reason'=>'required|string|min:10|max:1000']);$p=$session->attendanceEvent->participants()->findOrFail($participant);$p->update(['expected'=>false,'invitation_status'=>'cancelled','excluded_at'=>now(),'exclusion_reason'=>$d['reason'],'excluded_by'=>$r->user()->id]);$service->audit($session,'training_participant_removed',$r->user()->id,['participant_id'=>$p->id,'reason'=>$d['reason']]);return back()->with('success','Participante excluido sin borrar el snapshot.');}
 public function start(TrainingSession$session,Request$r,TrainingSessionService$s){$this->own($session);$s->start($session,$r->user()->id);return back()->with('success','Capacitación iniciada.');}
 public function complete(TrainingSession$session,Request$r,TrainingSessionService$s){$this->own($session);$d=$r->validate(['content_delivered'=>'required|string|max:10000','execution_notes'=>'nullable|string|max:10000']);$s->complete($session,$d,$r->user()->id);return back()->with('success','Ejecución finalizada; complete evidencias y cierre documental.');}
 public function close(TrainingSession$session,Request$r,TrainingSessionClosureService$s){$this->own($session);$s->close($session,$r->user()->id);return back()->with('success','Sesión cerrada y progreso del programa actualizado.');}
 public function reschedule(TrainingSession$session,Request$r,TrainingSessionService$s){$this->own($session);$d=$r->validate(['scheduled_start_at'=>'required|date','scheduled_end_at'=>'required|date|after:scheduled_start_at','reason'=>'required|string|min:10|max:2000']);$s->reschedule($session,$d['scheduled_start_at'],$d['scheduled_end_at'],$d['reason'],$r->user()->id);return back()->with('success','Sesión reprogramada con historial.');}
 public function cancel(TrainingSession$session,Request$r,TrainingSessionService$s){$this->own($session);$d=$r->validate(['reason'=>'required|string|min:10|max:2000']);$s->cancel($session,$d['reason'],$r->user()->id);return back()->with('success','Sesión cancelada sin eliminarla.');}
 public function evidence(TrainingSession$session,Request$r,TrainingEvidenceService$s){$this->own($session);$d=$r->validate(['evidence_type'=>'required|in:photo,training_material,instructor_document,execution_report,external_certificate,other','title'=>'required|string|max:255','file'=>'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx,ppt,pptx,mp4','is_required'=>'nullable|boolean']);$s->store($session,$r->file('file'),$d,$r->user()->id);return back()->with('success','Evidencia almacenada.');}
 public function download(TrainingSessionEvidence$evidence){$this->own($evidence->session);return Storage::disk(config('training.evidence_disk','local'))->download($evidence->file_path,$evidence->original_name);}
 public function report(TrainingSession$session){$this->own($session);$session->load(['company','programItem.program','topic','instructorEmployee','externalInstructor','attendanceEvent.participants.record.signature','attendanceEvent.evidences','evidences']);$signatures=[];if($session->attendanceEvent)foreach($session->attendanceEvent->records as$record)if($record->signature){$bytes=Storage::disk(config('attendance.disk'))->get($record->signature->file_path);$signatures[$record->id]='data:image/png;base64,'.base64_encode($bytes);}return Pdf::loadView('pdf.training_session_report',compact('session','signatures'))->download('ejecucion-capacitacion-'.$session->uuid.'.pdf');}

    // --- Inducciones: antes TrainingInductionController, fusionado aquí (misma lógica, mismas rutas) ---

    public function pendingInductions()
    {
        $company = auth()->user()->company_id;
        $employees = Empleado::where('company_id', $company)->active()->whereNotNull('fecha_ingreso')->get()
            ->filter(fn ($e) => ! TrainingSession::forCompany($company)
                ->where('training_type', 'induction')
                ->where('status', 'closed')
                ->whereHas('attendanceEvent.participants', fn ($q) => $q->where('employee_id', $e->id))
                ->exists());

        return view('training.inductions.index', compact('employees'));
    }

    public function storeInduction(Request $r, TrainingSessionService $s)
    {
        $d = $r->validate([
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:empleados,id',
            'scheduled_start_at' => 'required|date',
            'scheduled_end_at' => 'required|date|after:scheduled_start_at',
            'instructor_employee_id' => 'required|exists:empleados,id',
            'location' => 'nullable|string|max:255',
        ]);

        $company = $r->user()->company_id;
        $ids = Empleado::where('company_id', $company)->active()->whereIn('id', $d['employee_ids'])->pluck('id')->all();
        abort_if(! $ids, 422, 'No se encontraron empleados válidos.');
        $topic = TrainingTopic::availableTo($company)->where('training_type', 'induction')->where('is_active', true)->first();

        $session = $s->create([
            'training_topic_id' => $topic?->id,
            'title' => 'Inducción SG-SST',
            'description' => 'Inducción general y específica previa o asociada al ingreso.',
            'training_type' => 'induction',
            'induction_scope' => 'general',
            'scheduled_start_at' => $d['scheduled_start_at'],
            'scheduled_end_at' => $d['scheduled_end_at'],
            'planned_duration_minutes' => max(1, \Carbon\Carbon::parse($d['scheduled_start_at'])->diffInMinutes(\Carbon\Carbon::parse($d['scheduled_end_at']))),
            'modality' => 'presential',
            'location' => $d['location'] ?? null,
            'instructor_type' => 'internal',
            'instructor_employee_id' => $d['instructor_employee_id'],
            'audience_type' => 'specific_employees',
            'audience_description' => 'Nuevos ingresos seleccionados',
            'specific_employee_ids' => $ids,
            'requires_attendance' => true,
            'requires_signature' => true,
            'requires_material' => true,
            'requires_execution_report' => true,
            'extraordinary_reason' => 'Inducción obligatoria de personal nuevo ingreso (Resolución 0312 de 2019).',
            'extraordinary_origin' => 'new_hire_induction',
        ], $company, $r->user()->id);
        $s->schedule($session, $r->user()->id);

        return redirect()->route('training.sessions.show', $session)->with('success', 'Inducción programada.');
    }

    // --- Refuerzos: antes TrainingReinforcementController, fusionado aquí (misma lógica, mismas rutas) ---

    public function pendingReinforcements()
    {
        $company = auth()->user()->company_id;
        $reinforcements = TrainingReinforcement::where('company_id', $company)->with(['employee', 'attempt.evaluation.topic'])->get();
        $employees = Empleado::where('company_id', $company)->active()->get();

        return view('training.reinforcements.index', compact('reinforcements', 'employees'));
    }

    public function scheduleReinforcement(TrainingReinforcement $reinforcement, Request $r, TrainingSessionService $s)
    {
        abort_unless($reinforcement->company_id === $r->user()->company_id, 403);
        $d = $r->validate([
            'scheduled_start_at' => 'required|date',
            'scheduled_end_at' => 'required|date|after:scheduled_start_at',
            'instructor_employee_id' => 'required|exists:empleados,id',
            'location' => 'nullable|string|max:255',
        ]);

        $topic = $reinforcement->attempt->evaluation->topic;
        $session = $s->create([
            'training_topic_id' => $topic->id,
            'title' => 'Refuerzo: '.$topic->name,
            'description' => 'Sesión de refuerzo administrativamente programada.',
            'training_type' => 'training',
            'session_purpose' => 'reinforcement',
            'scheduled_start_at' => $d['scheduled_start_at'],
            'scheduled_end_at' => $d['scheduled_end_at'],
            'planned_duration_minutes' => max(1, \Carbon\Carbon::parse($d['scheduled_start_at'])->diffInMinutes(\Carbon\Carbon::parse($d['scheduled_end_at']))),
            'modality' => 'presential',
            'location' => $d['location'] ?? null,
            'instructor_type' => 'internal',
            'instructor_employee_id' => $d['instructor_employee_id'],
            'audience_type' => 'specific_employees',
            'audience_description' => 'Trabajador con refuerzo pendiente',
            'specific_employee_ids' => [$reinforcement->employee_id],
            'requires_attendance' => true,
            'requires_signature' => true,
            'requires_material' => false,
            'requires_execution_report' => true,
            'extraordinary_reason' => 'Refuerzo requerido por evaluación no aprobada',
            'extraordinary_origin' => 'evaluation',
        ], $r->user()->company_id, $r->user()->id);
        $s->schedule($session, $r->user()->id);
        $reinforcement->update(['reinforcement_session_id' => $session->id, 'status' => 'scheduled', 'updated_by' => $r->user()->id]);

        return redirect()->route('training.sessions.show', $session)->with('success', 'Refuerzo programado mediante una sesión CAP-2.');
    }

 private function own(TrainingSession$s){abort_unless((int)$s->company_id===(int)auth()->user()->company_id,403);}
}
