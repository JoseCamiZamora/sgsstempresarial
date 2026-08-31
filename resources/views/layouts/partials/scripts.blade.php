

    <script src="{{ asset('/assets/plugins/code.jquery.com/jquery-3.3.1.min.js') }}" ></script>
    <script src="{{ asset('/assets/plugins/cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js') }}" ></script>
    <script src="{{asset('/assets/plugins/stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js') }}" ></script>
    <script src="{{ asset('/assets/scripts/jsrender.min.js') }}" ></script>
    <script src="{{ asset('/assets/plugins/sammy/lib/sammy.js') }}"></script>
    <script src="{{ asset('/assets/plugins/sammy/lib/plugins/sammy.template.js') }}"></script>
    <script src="{{ asset('/assets/plugins/jqueryform/jquery.form.min.js') }}"></script>
      <!-- ============================================================== -->
    <!-- mensajes en pantalla-->
    <script src="{{ asset('/assets/plugins/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/sweetalert/jquery.sweet-alert.custom.js') }}"></script>
    <script src="{{ asset('/assets/plugins/BsMultiSelect.min.js') }}"></script>
    <script src="{{ asset('/assets/plugins/autoNumeric.js') }}"></script>

    <script src="{{ asset('/assets/plugins/Utilschart.js') }}"></script>
    <script src="{{ asset('assets/plugins/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/chartjs-plugin-datalabels.min.js') }}"></script>
    
    <script src="{{ asset('/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('/assets/js/lib/notify.min.js') }}"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{--
        NO cargar otro jQuery aquí. Hubo un jquery-3.6.0.min.js duplicado en este punto que
        pisaba window.jQuery/$ después de que Bootstrap, select2, BsMultiSelect, el datepicker
        y sammy ya habían registrado sus plugins contra la instancia de jquery-3.3.1.min.js de
        arriba — eso dejaba .tab()/.dropdown()/.modal()/.collapse()/.alert() (y select2, etc.)
        sin funcionar en todo el sitio, con cero error visible en consola. Si se vuelve a
        necesitar una versión de jQuery más nueva, hay que actualizar TODOS los plugins de
        esta lista a compatibilidad con esa versión, no simplemente cargarla al final.
    --}}


    <script>
    	$( document ).ready(function() {
        	$('.preloader').hide();
        });

    </script>

    {{--
        Mensajes de confirmación/advertencia normalizados con SweetAlert2 (Swal).
        Cualquier vista que use session()->with('success'|'error'|'warning'|'info', ...)
        o falle validación ($errors) muestra el mismo popup, sin tener que repetirlo
        vista por vista. Si una vista además pinta su propio banner Bootstrap con el
        mismo mensaje, quítalo para no duplicar el aviso.
    --}}
    @if(session('success') || session('error') || session('warning') || session('info') || $errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: '¡Listo!',
                    text: @json(session('success')),
                    confirmButtonColor: '#4e73df',
                    timer: 4000,
                    timerProgressBar: true,
                });
            @elseif(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Ocurrió un error',
                    text: @json(session('error')),
                    confirmButtonColor: '#e74a3b',
                });
            @elseif(session('warning'))
                Swal.fire({
                    icon: 'warning',
                    title: 'Advertencia',
                    text: @json(session('warning')),
                    confirmButtonColor: '#f6c23e',
                });
            @elseif(session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Información',
                    text: @json(session('info')),
                    confirmButtonColor: '#36b9cc',
                });
            @elseif($errors->any())
                Swal.fire({
                    icon: 'warning',
                    title: 'Revisa el formulario',
                    html: @json(collect($errors->all())->implode('<br>')),
                    confirmButtonColor: '#e74a3b',
                });
            @endif
        });
    </script>
    @endif

    {{--
        Dictado por voz para textareas marcados con data-voice-dictation="es-CO" (o cualquier
        locale). Usa la Web Speech API nativa del navegador (SpeechRecognition /
        webkitSpeechRecognition) — sin backend, sin dependencia nueva. Solo Chrome/Edge la
        soportan: si el navegador no la expone, el botón simplemente no se agrega. El audio se
        procesa en el motor de reconocimiento del navegador, no en nuestro servidor.
    --}}
    <script>
    (function () {
        var SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
        if (!SpeechRecognition) return;

        $(function () {
            $('textarea[data-voice-dictation]').each(function () {
                var $textarea = $(this);
                var lang = $textarea.data('voiceDictation');
                if (lang === true || !lang) lang = 'es-CO';

                var $btn = $('<button type="button" class="btn btn-sm btn-outline-secondary voice-dictation-btn mb-1"><i class="fa fa-microphone"></i> Dictar</button>');
                $textarea.before($btn);

                var recognition = new SpeechRecognition();
                recognition.lang = lang;
                recognition.continuous = true;
                recognition.interimResults = false;
                var listening = false;

                function setIdle() {
                    $btn.removeClass('btn-danger').addClass('btn-outline-secondary').html('<i class="fa fa-microphone"></i> Dictar');
                }
                function setListening() {
                    $btn.removeClass('btn-outline-secondary').addClass('btn-danger').html('<i class="fa fa-microphone"></i> Detener');
                }

                recognition.onresult = function (event) {
                    var text = '';
                    for (var i = event.resultIndex; i < event.results.length; i++) {
                        if (event.results[i].isFinal) text += event.results[i][0].transcript;
                    }
                    if (!text) return;
                    var current = $textarea.val();
                    var sep = current && !/\s$/.test(current) ? ' ' : '';
                    $textarea.val(current + sep + text.trim() + ' ');
                };

                recognition.onerror = function (event) {
                    if (['not-allowed', 'audio-capture', 'service-not-allowed'].indexOf(event.error) === -1) return;
                    listening = false;
                    setIdle();
                    if (event.error === 'not-allowed') {
                        Swal.fire({ icon: 'warning', title: 'Permiso de micrófono denegado', text: 'Habilita el acceso al micrófono en el navegador para usar el dictado.' });
                    }
                };

                recognition.onend = function () {
                    if (listening) {
                        try { recognition.start(); } catch (e) {}
                    } else {
                        setIdle();
                    }
                };

                $btn.on('click', function () {
                    listening = !listening;
                    if (listening) {
                        recognition.start();
                        setListening();
                    } else {
                        recognition.stop();
                    }
                });
            });
        });
    })();
    </script>





