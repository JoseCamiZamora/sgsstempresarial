@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0" style="color: white;"><i class="fa fa-user-plus mr-2"></i> Registrar Nuevo Empleado</h5>
                    <a href="{{ route('empleados.index') }}" class="btn btn-light btn-sm shadow-sm text-primary">
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

                    <form action="{{ route('empleados.store') }}" method="POST" id="formEmpleado">
                        @csrf
                        
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
                                        <input type="text" name="nombre_completo" class="form-control" value="{{ old('nombre_completo') }}" required>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold">Cédula de Ciudadanía <span class="text-danger">*</span></label>
                                        <input type="text" name="cedula" class="form-control" value="{{ old('cedula') }}" required>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="font-weight-bold">Correo Electronico <span class="text-danger">*</span></label>
                                        <input type="text" name="email_personal" class="form-control" value="{{ old('email_personal') }}" required>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold">Teléfono de Contacto</label>
                                        <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold">Género</label>
                                        <select name="genero" class="form-control">
                                            <option value="" >Seleccione...</option>
                                            <option value="Masculino" {{ old('genero') == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                            <option value="Femenino" {{ old('genero') == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                            <option value="Otro" {{ old('genero') == 'Otro' ? 'selected' : '' }}>Otro</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold">RH (Grupo Sanguíneo)</label>
                                        <input type="text" name="rh" class="form-control" value="{{ old('rh') }}" placeholder="Ej: O+">
                                    </div>
                                    <div class="col-md-3 form-group">
                                        <label class="font-weight-bold">Fecha de Nacimiento</label>
                                        <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}">
                                    </div>
                                   
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Nombre Contacto de Emergencia</label>
                                        <input type="text" name="contacto_emergencia_nombre" class="form-control" value="{{ old('contacto_emergencia_nombre') }}">
                                    </div>
                                     <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Teléfono de Contacto Emergencia</label>
                                        <input type="text" name="contacto_emergencia_telefono" class="form-control" value="{{ old('contacto_emergencia_telefono') }}">
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="pills-acceso" role="tabpanel">
                                <div class="card border-left-info shadow-sm">
                                    <div class="card-body">
                                        <div class="custom-control custom-switch mb-4">
                                            <input type="checkbox" class="custom-control-input" id="crear_usuario" name="crear_usuario" value="1" {{ old('crear_usuario') ? 'checked' : '' }}>
                                            <label class="custom-control-label font-weight-bold text-info" for="crear_usuario">¿Generar credenciales de acceso para este empleado?</label>
                                        </div>

                                        <div id="campos_usuario" style="{{ old('crear_usuario') ? 'display: block;' : 'display: none;' }}">
                                            <div class="row">
                                                <div class="col-md-6 form-group">
                                                    <label class="font-weight-bold">Correo Electrónico de Usuario <span class="text-danger">*</span></label>
                                                    <input type="email" name="email_usuario" class="form-control" value="{{ old('email_usuario') }}" placeholder="ejemplo@correo.com">
                                                </div>
                                                <div class="col-md-6 form-group">
                                                    <label class="font-weight-bold">Contraseña Temporal <span class="text-danger">*</span></label>
                                                    <input type="password" name="password_usuario" class="form-control" placeholder="Mínimo 8 caracteres">
                                                </div>
                                            </div>
                                            <div class="alert alert-info py-2 mt-2">
                                                <small><i class="fa fa-info-circle mr-1"></i> Al activar esta opción, se le asignará automáticamente el rol de <strong>Empleado</strong>.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="pills-laboral" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Cargo <span class="text-danger">*</span></label>
                                        <input type="text" name="cargo" class="form-control" value="{{ old('cargo') }}" required>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="font-weight-bold">Área o Departamento</label>
                                        <input type="text" name="area_departamento" class="form-control" value="{{ old('area_departamento') }}">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="font-weight-bold">Tipo de Contrato</label>
                                        <select name="tipo_contrato" class="form-control">
                                            <option value="" >Seleccione...</option>
                                            <option value="Termino Indefinido" {{ old('tipo_contrato') == 'Termino Indefinido' ? 'selected' : '' }}>Término Indefinido</option>
                                            <option value="Termino Fijo" {{ old('tipo_contrato') == 'Termino Fijo' ? 'selected' : '' }}>Término Fijo</option>
                                            <option value="Obra o Labor" {{ old('tipo_contrato') == 'Obra o Labor' ? 'selected' : '' }}>Obra o Labor</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="font-weight-bold">Fecha de Ingreso</label>
                                        <input type="date" name="fecha_ingreso" class="form-control" value="{{ old('fecha_ingreso') }}">
                                    </div>
                                    <div class="col-md-4 form-group">
                                        <label class="font-weight-bold">Salario Mensual</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                            <input type="text" name="salario" id="salario_format" class="form-control" value="{{ old('salario') }}" placeholder="0">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="tab-pane fade" id="pills-sst" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-3 form-group"><label class="font-weight-bold">EPS</label><input type="text" name="eps" class="form-control" value="{{ old('eps') }}"></div>
                                    <div class="col-md-3 form-group"><label class="font-weight-bold">AFP (Pensión)</label><input type="text" name="afp" class="form-control" value="{{ old('afp') }}"></div>
                                    <div class="col-md-3 form-group"><label class="font-weight-bold">ARL</label><input type="text" name="arl" class="form-control" value="{{ old('arl') }}"></div>
                                    <div class="col-md-3 form-group"><label class="font-weight-bold">Caja Comp.</label><input type="text" name="caja_compensacion" class="form-control" value="{{ old('caja_compensacion') }}"></div>
                                    
                                    <div class="col-12 mt-3"><h6 class="text-primary font-weight-bold border-bottom pb-2">Tallas para Dotación</h6></div>
                                    <div class="col-md-4 form-group"><label class="font-weight-bold">Talla Camisa</label><input type="text" name="talla_camisa" class="form-control" value="{{ old('talla_camisa') }}"></div>
                                    <div class="col-md-4 form-group"><label class="font-weight-bold">Talla Pantalón</label><input type="text" name="talla_pantalon" class="form-control" value="{{ old('talla_pantalon') }}"></div>
                                    <div class="col-md-4 form-group"><label class="font-weight-bold">Talla Calzado</label><input type="text" name="talla_calzado" class="form-control" value="{{ old('talla_calzado') }}"></div>
                                </div>
                            </div>

                        </div>

                        <div class="mt-5 border-top pt-3 d-flex justify-content-end">
                            <button type="submit" class="btn btn-success btn-lg px-5 shadow">
                                <i class="fa fa-save mr-2"></i> Finalizar Registro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- SCRIPTS DE INTERACCIÓN --}}
<script>
    // 1. Mostrar/Ocultar campos de usuario
    document.getElementById('crear_usuario').addEventListener('change', function() {
        const seccion = document.getElementById('campos_usuario');
        seccion.style.display = this.checked ? 'block' : 'none';
    });

    // 2. Formateo de Salario con puntos de miles
    const inputSalario = document.getElementById('salario_format');
    inputSalario.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, "");
        if (value === "") {
            e.target.value = "";
            return;
        }
        e.target.value = new Intl.NumberFormat('es-CO').format(value);
    });
</script>

<style>
    /* Estilo para que la pestaña activa se vea mejor */
    .nav-pills .nav-link.active {
        background-color: #4e73df !important;
        box-shadow: 0 4px 10px rgba(78, 115, 223, 0.3);
    }
    .form-control:focus {
        border-color: #4e73df;
        box-shadow: none;
    }
</style>
@endsection