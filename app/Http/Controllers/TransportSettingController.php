<?php
namespace App\Http\Controllers;
use App\Http\Requests\UpdateTransportSettingRequest;use App\Models\{Empleado,TransportChecklistTemplate,TransportSetting};use App\Services\TransportAuditService;use App\Traits\AuthorizesCompanyOwnership;use Illuminate\Http\Request;use Illuminate\Support\Facades\DB;
class TransportSettingController extends Controller {
 use AuthorizesCompanyOwnership;
 public function edit(){ $company=auth()->user()->company_id;return view('transport.settings',['setting'=>TransportSetting::firstOrNew(['company_id'=>$company]),'employees'=>Empleado::where('company_id',$company)->active()->orderBy('nombre_completo')->get(),'templates'=>TransportChecklistTemplate::forCompany($company)->with('items')->get()]);}
 public function update(UpdateTransportSettingRequest $r,TransportAuditService $audit){$company=$r->user()->company_id;$setting=TransportSetting::firstOrNew(['company_id'=>$company]);$setting->fill($r->validated());$setting->created_by=$setting->exists?$setting->created_by:$r->user()->id;$setting->updated_by=$r->user()->id;$setting->save();$audit->record($company,'transport_settings_updated','setting',$setting->id,$r->user()->id);return back()->with('success','Configuración actualizada.');}
 public function checklist(Request $r,TransportAuditService $audit){$data=$r->validate(['name'=>'required|string|max:255','blocks_on_critical_failure'=>'nullable|boolean','items'=>'required|array|min:1','items.0.label'=>'required|string|max:255','items.*.label'=>'nullable|string|max:255','items.*.is_critical'=>'nullable|boolean']);$company=$r->user()->company_id;$template=DB::transaction(function()use($data,$company){$t=TransportChecklistTemplate::create(['company_id'=>$company,'name'=>$data['name'],'blocks_on_critical_failure'=>(bool)($data['blocks_on_critical_failure']??false),'status'=>'active']);$items=array_values(array_filter($data['items'],fn($item)=>!empty($item['label'])));foreach($items as$i=>$item)$t->items()->create(['label'=>$item['label'],'sort_order'=>$i+1,'is_critical'=>(bool)($item['is_critical']??false),'is_required'=>true]);return$t;});$audit->record($company,'transport_checklist_template_created','checklist_template',$template->id,$r->user()->id);return back()->with('success','Plantilla preoperacional creada.');}

 public function updateChecklist(Request $r,TransportChecklistTemplate $template,TransportAuditService $audit){
  $this->own($template);
  $data=$r->validate(['name'=>'required|string|max:255','blocks_on_critical_failure'=>'nullable|boolean','items'=>'required|array|min:1','items.0.label'=>'required|string|max:255','items.*.id'=>'nullable|integer','items.*.label'=>'nullable|string|max:255','items.*.is_critical'=>'nullable|boolean']);
  DB::transaction(function()use($data,$template){
   $template->update(['name'=>$data['name'],'blocks_on_critical_failure'=>(bool)($data['blocks_on_critical_failure']??false)]);
   $existing=$template->items()->pluck('sort_order')->count();
   $next=$existing+1;
   foreach($data['items'] as $item){
    if(empty($item['label']))continue;
    if(!empty($item['id'])){
     $template->items()->whereKey($item['id'])->update(['label'=>$item['label'],'is_critical'=>(bool)($item['is_critical']??false)]);
    } else {
     $template->items()->create(['label'=>$item['label'],'sort_order'=>$next++,'is_critical'=>(bool)($item['is_critical']??false),'is_required'=>true]);
    }
   }
  });
  $audit->record($template->company_id,'transport_checklist_template_updated','checklist_template',$template->id,$r->user()->id);
  return back()->with('success','Plantilla actualizada. Los ítems ya usados en un control preoperacional no se pueden quitar, solo agregar nuevos o editar su texto.');
 }

 public function deactivateChecklist(TransportChecklistTemplate $template,TransportAuditService $audit){
  $this->own($template);
  $template->update(['status'=>'inactive']);
  $audit->record($template->company_id,'transport_checklist_template_deactivated','checklist_template',$template->id,auth()->id());
  return back()->with('success','Plantilla desactivada. Los servicios que ya la usaron conservan su historial.');
 }

 public function activateChecklist(TransportChecklistTemplate $template,TransportAuditService $audit){
  $this->own($template);
  $template->update(['status'=>'active']);
  $audit->record($template->company_id,'transport_checklist_template_activated','checklist_template',$template->id,auth()->id());
  return back()->with('success','Plantilla activada.');
 }
}
