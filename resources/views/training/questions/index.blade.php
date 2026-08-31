@extends('layouts.app') @section('content')<div class="container-fluid my-4"><h2>Banco de preguntas</h2>@include('training.partials.nav')<form method="POST" action="{{route('training.questions.store')}}" id="questionForm" class="card card-body mb-3">@csrf<select name="training_topic_id" class="form-control mb-2">@foreach($topics as $topic)<option value="{{$topic->id}}">{{$topic->name}}</option>@endforeach</select><textarea name="question_text" class="form-control mb-2" placeholder="Pregunta" required></textarea><select name="question_type" id="questionType" class="form-control mb-2"><option value="single_choice">Selección única</option><option value="multiple_choice">Selección múltiple</option><option value="true_false">Verdadero/falso</option><option value="short_answer">Respuesta corta</option><option value="long_answer">Respuesta larga</option></select><input type="number" step="0.01" name="default_points" value="1" class="form-control mb-2"><textarea name="explanation" class="form-control mb-2" placeholder="Explicación opcional"></textarea><div id="optionsSection"><p>Opciones. Marque con el check la(s) opción(es) correcta(s) — <strong>obligatorio marcar al menos una</strong>.</p><div id="optionsWrap">@for($i=0;$i<4;$i++)<div class="input-group mb-1 option-row" data-index="{{$i}}"><div class="input-group-prepend"><div class="input-group-text"><input type="checkbox" class="correct-checkbox" name="correct_options[]" value="{{$i}}"></div></div><input name="options[]" class="form-control" placeholder="Opción {{$i+1}}" required></div>@endfor</div></div><p id="freeTextNote" class="text-muted" style="display:none">Este tipo de pregunta no usa opciones; el trabajador responderá en un campo de texto libre. Se califica automáticamente con IA, comparando la respuesta contra la pregunta y el tema.</p><label><input type="checkbox" name="is_critical" value="1"> Pregunta crítica</label><button class="btn btn-primary">Guardar pregunta</button></form><table class="table"><tr><th>Tema</th><th>Pregunta</th><th>Tipo</th><th>Puntos</th></tr>@foreach($questions as $question)<tr><td>{{$question->topic->name}}</td><td>{{$question->question_text}}</td><td>{{config('training.question_types.'.$question->question_type,$question->question_type)}}</td><td>{{$question->default_points}}</td></tr>@endforeach</table></div>
<script>
(function(){
    var typeSelect = document.getElementById('questionType');
    var optionsSection = document.getElementById('optionsSection');
    var freeTextNote = document.getElementById('freeTextNote');
    var rows = document.querySelectorAll('#optionsWrap .option-row');
    var checkboxes = document.querySelectorAll('.correct-checkbox');
    var trueFalseLabels = ['Verdadero', 'Falso'];
    var freeTextTypes = ['short_answer', 'long_answer'];

    function applyType(){
        var type = typeSelect.value;
        var isFreeText = freeTextTypes.indexOf(type) !== -1;
        optionsSection.style.display = isFreeText ? 'none' : '';
        freeTextNote.style.display = isFreeText ? '' : 'none';

        rows.forEach(function(row, i){
            var input = row.querySelector('input[name="options[]"]');
            var checkbox = row.querySelector('.correct-checkbox');
            if (isFreeText) {
                row.style.display = 'none';
                input.required = false;
                input.disabled = true;
                checkbox.disabled = true;
                input.value = '';
                checkbox.checked = false;
            } else if (type === 'true_false') {
                if (i < 2) {
                    row.style.display = '';
                    input.required = true;
                    input.readOnly = true;
                    input.disabled = false;
                    checkbox.disabled = false;
                    input.value = trueFalseLabels[i];
                } else {
                    row.style.display = 'none';
                    input.required = false;
                    input.disabled = true;
                    checkbox.disabled = true;
                    input.value = '';
                    checkbox.checked = false;
                }
            } else {
                row.style.display = '';
                input.required = true;
                input.readOnly = false;
                input.disabled = false;
                checkbox.disabled = false;
                if (trueFalseLabels.indexOf(input.value) !== -1) input.value = '';
            }
        });
    }

    checkboxes.forEach(function(cb){
        cb.addEventListener('change', function(){
            var type = typeSelect.value;
            if (type !== 'multiple_choice' && cb.checked) {
                checkboxes.forEach(function(other){ if (other !== cb) other.checked = false; });
            }
        });
    });

    typeSelect.addEventListener('change', applyType);
    applyType();

    document.getElementById('questionForm').addEventListener('submit', function(e){
        if (freeTextTypes.indexOf(typeSelect.value) !== -1) return;
        var anyChecked = Array.from(checkboxes).some(function(cb){
            return cb.checked && cb.closest('.option-row').style.display !== 'none';
        });
        if (!anyChecked) {
            e.preventDefault();
            alert('Debe marcar al menos una respuesta correcta antes de guardar.');
        }
    });
})();
</script>
@endsection
