

<form  method="post"  action="nuevo_usuario" id="f_crear_usuario"   >
  <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="feLastName">Nombres</label>
      <input type="text" maxlength="100" class="form-control" id="nombres" name="nombres"  value="" required>
    </div>
    <div class="form-group col-md-3">
      <label for="feLastName">Identificación</label>
      <input type="text" maxlength="100" class="form-control" id="identificacion" name="identificacion"  value="">
    </div>
     <div class="form-group col-md-3">
      <label for="feLastName">Teléfono</label>
      <input type="text" maxlength="15" class="form-control" id="telefono" name="telefono" value="" >
    </div>
  </div>
  <div class="form-row">
    <div class="form-group col-md-6">
      <label for="feInputCity">Programa</label>
      <select class="form-control" id="programa" name="programa" required >
        <option value="" selected >Seleccione...</option>
        @foreach($programas as $program)
          <option value="{{$program->id}}"  >{{$program->descripcion}}</option>
        @endforeach
      </select>
    </div>
    <div class="form-group col-md-3">
      <label for="feInputCity">Rol del Usuario</label>
      <select class="form-control" name="rol" id="PQRS_id_tipo" onchange="cambiar_tipo(this.value);" required >
        <option value="" selected >Seleccione...</option>
        @foreach($tipouser as $tipo)
          <option value="{{$tipo->id}}"  >{{$tipo->descripcion}}</option>
        @endforeach
      </select>
    </div>
     <div class="form-group col-md-3">
      <label for="feInputCity">Opcione menu</label>
      <select class="form-control" id="PQRS_id_opcion" name="opcion" required >
        <option value="" selected >Seleccione...</option>
        @foreach($opcionSistema as $opcion)
          <option value="{{$opcion->id}}"  >{{$opcion->opcion}}</option>
        @endforeach
      </select>
    </div>
    
    <div class="form-group col-md-6">
      <label for="feLastName">email</label>
      <input type="email" maxlength="255" class="form-control" id="email" name="email"  value="" required>
    </div>
    <div class="form-group col-md-6">
      <label for="feFirstName">password*</label>
      <input type="password" maxlength="100" class="form-control" id="password" name="password"  value="" required>
    </div>
  </div>
  @if ($errors->count() > 0)
  <div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
  </div>
  @endif
  <button type="submit" class="btn btn-accent" >Guardar Datos</button>
</form>

<script>
     var TIPO = @json($tipouser);
     var OPCIONES = @json($opcionSistema);
</script>
                    