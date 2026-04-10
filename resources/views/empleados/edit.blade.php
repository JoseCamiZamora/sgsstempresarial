@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0">
                <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark font-weight-bold"><i class="fa fa-edit mr-2"></i> Editar Empleado: {{ $empleado->nombre_completo }}</h5>
                    <a href="{{ route('empleados.index') }}" class="btn btn-light btn-sm shadow-sm text-dark">
                        <i class="fa fa-arrow-left"></i> Volver
                    </a>
                </div>
                
                <div class="card-body">
                    {{-- Manejo de Errores --}}
                    @if ($errors->any())
                        <div class="alert alert-danger shadow-sm">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('empleados.update', $empleado->id) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- CRUCIAL: Indica a Laravel que es una actualización --}}
                        
                        <ul class="nav nav-pills mb-4 bg-light p-2 rounded" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active font-weight-bold" id="pills-personal-tab" data-toggle="pill" href="#pills-personal" role="tab">1. Datos Personales</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold" id="pills-acceso-tab" data-toggle="pill" href="#pills-acceso" role="tab">2. Acceso al Sistema</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold" id="pills-laboral-tab" data-toggle="pill" href="#pills-laboral" role="tab">3. Información Laboral</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font-weight-bold" id="pills-sst-tab" data-toggle="pill" href="#pills-sst" role="tab">4. Seguridad Social y Tallas</a>
                            </li>
                        </ul>

                        <div class="tab-content" id="pills-tabContent">
                            
                            <div class="tab-pane fade show active" id="pills-personal" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-5 form-group">
                                        <label class="font-weight-bold">Nombre Completo <span class="text-danger">*</span></label>
                                        <input type="text" name="nombre_completo" class="form-control" value="{{ old('nombre_completo', $empleado->nombre_completo) }}" required>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold">Cédula de Ciudadanía <span class="text-danger">*</span></label>
                                        <input type="text" name="cedula" class="form-control" value="{{ old('cedula', $empleado->cedula) }}" required>
                                    </div>
                                     <div class="col-md-4 form-group">
                                        <label class="font-weight-bold">Correo Electronico <span class="text-danger">*</span></label>
                                        <input type="text" name="email_personal" class="form-control" value="{{ old('email_personal', $empleado->email_personal) }}" required>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold">Teléfono</label>
                                        <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $empleado->telefono) }}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold">Género</label>
                                        <select name="genero" class="form-control">
                                            <option value="Masculino" {{ old('genero', $empleado->genero) == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                            <option value="Femenino" {{ old('genero', $empleado->genero) == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                            <option value="Otro" {{ old('genero', $empleado->genero) == 'Otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold">RH</label>
                                        <input type="text" name="rh" class="form-control" value="{{ old('rh', $empleado->rh) }}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold">Fecha de Nacimiento</label>
                                        <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $empleado->fecha_nacimiento) }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Nombre Contacto de Emergencia</label>
                                        <input type="text" name="contacto_emergencia_nombre" class="form-control" value="{{ old('contacto_emergencia_nombre', $empleado->contacto_emergencia_nombre) }}">
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Teléfono de Contacto Emergencia</label>
                                        <input type="text" name="contacto_emergencia_telefono" class="form-control" value="{{ old('contacto_emergencia_telefono', $empleado->contacto_emergencia_telefono) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="pills-acceso" role="tabpanel">
                                <div class="card bg-light shadow-sm">
                                    <div class="card-body text-center py-4">
                                        @if($empleado->user_id)
                                            <i class="fa fa-user-check fa-3x text-success mb-3"></i>
                                            <h5 class="font-weight-bold">Este empleado ya tiene una cuenta vinculada</h5>
                                            <p class="text-muted">Correo de acceso: <strong>{{ $empleado->user->email }}</strong></p>
                                            <p class="small text-muted">Para cambiar la contraseña o el correo, diríjase al módulo de Usuarios.</p>
                                        @else
                                            <i class="fa fa-user-times fa-3x text-muted mb-3"></i>
                                            <h5>Este empleado no tiene cuenta de usuario</h5>
                                            <p>Actualmente no puede ingresar al sistema.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="pills-laboral" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Cargo <span class="text-danger">*</span></label>
                                        <input type="text" name="cargo" class="form-control" value="{{ old('cargo', $empleado->cargo) }}" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Área o Departamento</label>
                                        <input type="text" name="area_departamento" class="form-control" value="{{ old('area_departamento', $empleado->area_departamento) }}">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="font-weight-bold">Tipo de Contrato</label>
                                        <select name="tipo_contrato" class="form-control">
                                            <option value="Termino Indefinido" {{ old('tipo_contrato', $empleado->tipo_contrato) == 'Termino Indefinido' ? 'selected' : '' }}>Término Indefinido</option>
                                            <option value="Termino Fijo" {{ old('tipo_contrato', $empleado->tipo_contrato) == 'Termino Fijo' ? 'selected' : '' }}>Término Fijo</option>
                                            <option value="Obra o Labor" {{ old('tipo_contrato', $empleado->tipo_contrato) == 'Obra o Labor' ? 'selected' : '' }}>Obra o Labor</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="font-weight-bold">Fecha de Ingreso</label>
                                        <input type="date" name="fecha_ingreso" class="form-control" value="{{ old('fecha_ingreso', $empleado->fecha_ingreso) }}">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="font-weight-bold">Salario Mensual</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                            <input type="text" name="salario" id="salario_edit" class="form-control" value="{{ old('salario', $empleado->salario) }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="pills-sst" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-3 form-group"><label class="font-weight-bold">EPS</label><input type="text" name="eps" class="form-control" value="{{ old('eps', $empleado->eps) }}"></div>
                                    <div class="col-md-3 form-group"><label class="font-weight-bold">AFP</label><input type="text" name="afp" class="form-control" value="{{ old('afp', $empleado->afp) }}"></div>
                                    <div class="col-md-3 form-group"><label class="font-weight-bold">ARL</label><input type="text" name="arl" class="form-control" value="{{ old('arl', $empleado->arl) }}"></div>
                                    <div class="col-md-3 form-group"><label class="font-weight-bold">Caja Comp.</label><input type="text" name="caja_compensacion" class="form-control" value="{{ old('caja_compensacion', $empleado->caja_compensacion) }}"></div>
                                    
                                    <div class="col-12 mt-3"><h6 class="text-primary font-weight-bold border-bottom pb-2">Tallas para Dotación</h6></div>
                                    <div class="col-md-4 form-group"><label class="font-weight-bold">Camisa</label><input type="text" name="talla_camisa" class="form-control" value="{{ old('talla_camisa', $empleado->talla_camisa) }}"></div>
                                    <div class="col-md-4 form-group"><label class="font-weight-bold">Pantalón</label><input type="text" name="talla_pantalon" class="form-control" value="{{ old('talla_pantalon', $empleado->talla_pantalon) }}"></div>
                                    <div class="col-md-4 form-group"><label class="font-weight-bold">Calzado</label><input type="text" name="talla_calzado" class="form-control" value="{{ old('talla_calzado', $empleado->talla_calzado) }}"></div>
                                </div>
                            </div>

                        </div>

                        <div class="mt-5 border-top pt-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-warning btn-lg px-5 shadow font-weight-bold">
                                <i class="fa fa-sync-alt mr-2"></i> Actualizar Información
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Formatear salario al cargar y al escribir
    const inputSalario = document.getElementById('salario_edit');

    function formatNumber(value) {
        if (!value) return "";
        let cleanValue = value.toString().replace(/\D/g, "");
        return new Intl.NumberFormat('es-CO').format(cleanValue);
    }

    // Al cargar la página, formateamos el valor que viene de la base de datos
    window.addEventListener('load', () => {
        inputSalario.value = formatNumber(inputSalario.value);
    });

    // Al escribir, formateamos dinámicamente
    inputSalario.addEventListener('input', (e) => {
        e.target.value = formatNumber(e.target.value);
    });
</script>

<style>
    .nav-pills .nav-link.active {
        background-color: #f6c23e !important; /* Amarillo de warning para edición */
        color: #000 !important;
        box-shadow: 0 4px 10px rgba(246, 194, 62, 0.3);
    }
    .form-control:focus {
        border-color: #f6c23e;
        box-shadow: none;
    }
</style>
@endsection