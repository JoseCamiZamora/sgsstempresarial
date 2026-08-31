<?php
namespace App\Services; use App\Exceptions\TrainingAiGradingException; use App\Models\{TrainingEvaluationQuestion,TrainingTopic}; use Illuminate\Support\Facades\Http;
class TrainingAiGradingService
{
 public function grade(TrainingEvaluationQuestion$question,?TrainingTopic$topic,string$answer):array{
  $key=config('services.anthropic.key');
  if(!$key)throw new TrainingAiGradingException('ANTHROPIC_API_KEY no está configurada.');
  $context=trim(($topic?->general_objective?:'').' '.($topic?->contents?:''));
  $prompt="Eres un evaluador de capacitaciones de Seguridad y Salud en el Trabajo (SG-SST). Califica si la respuesta del trabajador es correcta y congruente con la pregunta, usando el contexto del tema como referencia.\n\n".
   "Tema: ".($topic?->name??'(sin tema)')."\n".
   "Contexto del tema: ".(mb_substr($context,0,3000)?:'(sin contexto adicional)')."\n\n".
   "Pregunta: ".mb_substr($question->question_text,0,1000)."\n\n".
   "Respuesta del trabajador: ".mb_substr($answer,0,3000)."\n\n".
   "Usa la herramienta submit_grade para registrar tu calificación.";
  try{
   $response=Http::withHeaders(['x-api-key'=>$key,'anthropic-version'=>'2023-06-01'])
    ->timeout(20)
    ->post('https://api.anthropic.com/v1/messages',[
     'model'=>config('services.anthropic.model'),
     'max_tokens'=>512,
     'tools'=>[[
      'name'=>'submit_grade',
      'description'=>'Registra si la respuesta del trabajador es correcta y congruente con la pregunta.',
      'input_schema'=>['type'=>'object','properties'=>[
       'correct'=>['type'=>'boolean','description'=>'true si la respuesta es correcta y congruente con la pregunta y el tema'],
       'rationale'=>['type'=>'string','description'=>'Explicación breve (1-2 frases) de por qué se calificó así'],
      ],'required'=>['correct','rationale']],
     ]],
     'tool_choice'=>['type'=>'tool','name'=>'submit_grade'],
     'messages'=>[['role'=>'user','content'=>$prompt]],
    ]);
  }catch(\Throwable$e){throw new TrainingAiGradingException('Error de red al calificar con IA: '.$e->getMessage(),0,$e);}
  if($response->failed())throw new TrainingAiGradingException('La API de IA respondió con error: '.$response->status().' '.$response->body());
  $toolUse=collect($response->json('content',[]))->firstWhere('type','tool_use');
  if(!$toolUse||!isset($toolUse['input']['correct']))throw new TrainingAiGradingException('Respuesta de IA inesperada: '.$response->body());
  return['correct'=>(bool)$toolUse['input']['correct'],'rationale'=>(string)($toolUse['input']['rationale']??'')];
 }
}
