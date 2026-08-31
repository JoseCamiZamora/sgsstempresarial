<?php
namespace App\Services; use App\Models\TrainingEvaluationQuestion;
class TrainingEvaluationScoringService{
 public function scoreQuestion(TrainingEvaluationQuestion$q,array$selected,?string$textAnswer=null,?array$aiResult=null):array{
  if(in_array($q->question_type,['short_answer','long_answer'])){
   if($aiResult!==null)return['correct'=>$aiResult['correct'],'points'=>$aiResult['correct']?(float)$q->points:0.0,'countable'=>true,'pending'=>false,'rationale'=>$aiResult['rationale']??null];
   return['correct'=>null,'points'=>0.0,'countable'=>true,'pending'=>true,'rationale'=>null];
  }
  $correct=$q->options()->where('is_correct',true)->pluck('id')->map(fn($id)=>(int)$id)->sort()->values()->all();
  $selected=collect($selected)->map(fn($id)=>(int)$id)->unique()->sort()->values()->all();
  $ok=$correct===$selected;
  return['correct'=>$ok,'points'=>$ok?(float)$q->points:0.0,'countable'=>true,'pending'=>false];
 }
 public function calculatePercentage(float$raw,float$possible):float{return$possible>0?round(($raw/$possible)*100,2):0.0;}
 public function determineResult(float$percentage,float$passing):string{return$percentage+0.00001>=$passing?'passed':'failed';}
}
