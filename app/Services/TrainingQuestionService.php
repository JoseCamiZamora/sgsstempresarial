<?php
namespace App\Services; use App\Models\{TrainingQuestion,TrainingTopic}; use Illuminate\Validation\ValidationException;
class TrainingQuestionService
{
 public function create(array$d,int$companyId,int$userId):TrainingQuestion{$topic=TrainingTopic::availableTo($companyId)->find($d['training_topic_id']);if(!$topic)throw ValidationException::withMessages(['training_topic_id'=>'El tema seleccionado no está disponible.']);$q=TrainingQuestion::create(['company_id'=>$companyId,'training_topic_id'=>$topic->id,'question_text'=>$d['question_text'],'question_type'=>$d['question_type'],'explanation'=>$d['explanation']??null,'default_points'=>$d['default_points'],'is_critical'=>$d['is_critical']??false,'is_active'=>true,'created_by'=>$userId]);if(!empty($d['options'])){$correct=array_unique($d['correct_options']??[]);foreach($d['options'] as$i=>$text)$q->options()->create(['option_text'=>$text,'is_correct'=>in_array($i,$correct),'sort_order'=>$i+1]);}return$q;}
}
