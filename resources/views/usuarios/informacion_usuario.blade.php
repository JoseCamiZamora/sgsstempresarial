@extends('layouts.app')



@section('content')

<style>.bar {
    height: 18px;
    background: green;
}</style>



<div class='container'>
<div class="main-content-container container-fluid px-4">
            <!-- Page Header -->
    <div class="page-header row no-gutters py-4">
              <div class="col-md-10 text-center text-sm-left mb-0">
               <span class="text-uppercase page-subtitle">Información y datos del Usuario</span>
                <h4 class="page-title" >Datos del Usuario</h4>
                 
              
              </div>

                <div  class="col-md-2 text-right">
                  Atrás
                  <a href="{{ url('usuarios') }}" class="mb-2 btn btn-sm  mr-1 btn-redondo-suave text-primary" title="salir a usuarios" ><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                </div>

              
    </div>
            <!-- End Page Header -->
            <!-- Default Light Table -->
            <div class="row">
              <div class="col-lg-4">
                <div class="card card-small mb-4 pt-3">
                  <div class="card-header border-bottom text-center">
                    <div class="mb-3 mx-auto" style="height: 150px; background-color:#f5f6f8;">
                     
                      <img src="{{ url('/mostrar_imagen/'.$usuario->id.'/'.$usuario->foto) }}" style='max-height: 150px;' onerror="this.src='../assets/img/usuario.svg'" id="logo_contratista" >
                     
                    </div>
                    <h4 class="mb-0"></h4>
                    <span class="text-muted d-block mb-2"></span>
                   <!-- <button type="button" onclick="SU_cambiar_imagen({{$usuario->id}});" class="mb-2 btn btn-sm btn-pill btn-outline-primary mr-2"><i class="material-icons mr-1">person_add</i>Cambiar imagen</button> -->
                  </div>
                  <ul class="list-group list-group-flush">
                    <li class="list-group-item px-4">
                      <div class="progress-wrapper">
                        <strong class="text-muted d-block mb-2">Perfil del usuario</strong>
                        <div class="progress progress-sm">
                          <div class="progress-bar bg-primary" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                            <span class="progress-value">Registrado</span>
                          </div>
                        </div>
                      </div>
                    </li>
                    <li class="list-group-item p-4">
                      <strong class="text-muted d-block mb-2">Datos de acceso</strong>
                      <span></span>
                    </li>
                  </ul>
                </div>
              </div>

              <div class="col-lg-8">

                <div class="card card-small mb-4">
                  
                  <div class="card-header border-bottom ">
                    <h6 class="m-0" >Información del  Usuario</h6>
                  </div>

                  <ul class="list-group list-group-flush">
                    <li class="list-group-item p-3">
                      <div class="row">
                        <div class="col">

                          <form  method="post"  action="editar_usuario" id="f_editar_usuario"   >
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
                            <input type="hidden" maxlength="100" class="form-control" id="id_usuario" name="id_usuario"  value="{{$usuario->id}}">
                            <div class="form-row">
                             
                              <div class="form-group col-md-6">
                                <label for="feLastName">Nombres</label>
                                <input type="text" maxlength="100" class="form-control" id="nombres" name="nombres"  value="{{ $usuario->nombres}}" required>
                              </div>

                              <div class="form-group col-md-6">
                                <label for="feFirstName">Teléfono</label>
                                <input type="text" maxlength="100" class="form-control" id="telefono" name="telefono"  value="{{ $usuario->telefono}}" >
                              </div>
                            </div>

                        
                            <div class="form-row">
                               <div class="form-group col-md-12">
                                <label for="feInputCity">Rol del Usuario</label>
                                <select class="form-control" name="rol" >
                                  @if($usuario->rol==1)
                                     <option value="1" selected>Administrador del sistema</option>
                                     <option value="2" >Recaudador</option>
                                  @elseif($usuario->rol==2)
                                     <option value="2" selected>Recaudador</option>
                                     <option value="1" >Administrador del sistema</option>
                    
                                  @endif
                                  
                                </select>
                              </div>
                            </div>
                         
                            <button type="submit" class="btn btn-accent" >Guardar Datos</button>

                          </form>
                       

                        </div>
                      </div>
                    </li>
                  </ul>
                </div>
                <!--card-->


                <div class="card card-small mb-4">
                  
                  <div class="card-header border-bottom ">
                    <h6 class="m-0" >Acceso al sistema</h6>
                  </div>

                   <ul class="list-group list-group-flush">
                    <li class="list-group-item p-3">
                      <div class="row">
                        <div class="col">


                           <form  method="post"  action="editar_acceso" id="f_editar_acceso"   >
                            <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>"> 
                            <input type="hidden" maxlength="100" class="form-control" id="id_usuario2" name="id_usuario"  value="{{$usuario->id}}">
                            <div class="form-row">
                             
                              <div class="form-group col-md-6">
                                <label for="feLastName">email</label>
                                <input type="email" maxlength="255" class="form-control" id="email" name="email"  value="{{$usuario->email}}" readonly >
                              </div>

                               <div class="form-group col-md-6">
                                <label for="feFirstName">password*</label>
                                <input type="password" maxlength="100" class="form-control" id="password" name="password"  value="" required>
                              </div>

                            </div>


                            <button type="submit" class="btn btn-accent" >Guardar acceso</button>
                          </form>



                        </div>
                      </div>
                    </li>
                   </ul>       

                </div>

                <!--card-->


              </div>
            </div>
            <!-- End Default Light Table -->
</div>

</div>


<div class="modal fade show" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="modal_usuarios">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="datohtml">
        <h4 class="modal-title" id="titul_modal_usuarios">Imagen de Usuario</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body" id="contenido_modal_usuarios" style='min-height: 400px;'>
      </div>
    </div>
  </div>
</div>

@endsection
