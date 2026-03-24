@extends('layouts.app')



@section('content')

<div class="main-content-container container-fluid px-4 pb-4">
     <!-- Page Header -->
  <div class='container'>
  <div class="page-header row no-gutters py-4">
    <div class="col">
      <span class="page-subtitle">Modulo Usuarios </span>
  
        <h4 class="page-title" >Listado General de Usuarios  <span style='font-size: 0.6em;'></span> </h4>
     
       <div  class=" ml-auto" style="text-align: right; margin-top: -35px;">
        Atrás
        <a href="{{ url('home') }}" class="mb-2 btn btn-sm  mr-1 btn-redondo-suave text-primary" title="salir a menu" ><i class="fa fa-chevron-left" aria-hidden="true"></i> </a>
      </div>
    </div>
  </div>
  <!-- End Page Header -->
  <!-- Default Light Table -->

  <div class="row">
    <div class="col" style="text-align: left;">

      

      <a href="javascript:void(0);" onclick="SU_form_crear_usuario();" class="mb-2 btn btn-sm btn-primary " ><i class="fa fa-user-plus margin-icon" aria-hidden="true" ></i>Nuevo Usuario</a>
    

                       
    </div>
  </div>

  <div class="row">
    <div class="col">
     
      <table    class='table table-generic' >

            <thead class="bg-light">
              <tr>
                <th scope="col" class="th-gris text-center" style="width: 30px;" >No.</th>
                <th scope="col" class="th-gris text-center " style="width: 50px;"  >Imágen</th>
                <th scope="col" class="th-gris" style="width: 100px;">Identificación</th>
                <th scope="col" class="th-gris" style="width: 250px;">Nombre usuario</th>
                <th scope="col" class="th-gris" style="width: 100px;">Correo</th>
                <th scope="col" class="th-gris" style="width: 80px;">Telefono</th>
                <th scope="col" class="th-gris text-left" style="width: 80px;">Estado</th>
                <th scope="col" class="th-gris text-center " style="width: 20px;"></th>
                <th scope="col" class="th-gris text-center " style="width: 20px;"></th>
                <th scope="col" class="th-gris text-center " style="width: 20px;"></th>
            
              </tr>
            </thead>
            <tbody>

              @foreach($usuarios as $usuario)

                <tr>
                  <td class='text-center'>{{ $loop->index+1 }}</td>
                  <td  class="lo-stats__image   text-center">
                    <img src="" style='max-height: 60px;' onerror="this.src='{{ asset('assets/img/usuario.svg') }}' "  >
                  </td>
                  <td class='text-left'>{{ $usuario->identificacion }}</td>
                  <td class='text-left'>

                   <h6 class="td-titulo">{{ $usuario->nombres }}</h6>

                   @if($usuario->rol==1)
                   
                   <span class="badge badge-pill badge-success" style='font-size: 0.5em;  margin-top:0px;'>Administrador del sistema</span> 
                   @endif
                   @if($usuario->rol==2)
                   <span class="badge badge-pill badge-warning" style='font-size: 0.5em;   margin-top:0px;'>Usuario administrativo</span> 
                   @endif
                   @if($usuario->rol==3)
                   <span class="badge badge-pill badge-info" style='font-size: 0.5em;   margin-top:0px;'>Coordinador</span> 
                   @endif
                  </td>
                  <td class='text-left'>{{ $usuario->email }}</td>
                  <td class='text-left'>{{ $usuario->telefono }}</td>
                  @if($usuario->estado=='A')
                    <td class='text-left'>ACTIVO</td>
                  @else
                    <td class='text-left'>INACTIVO</td>
                  @endif

                  <td class="td-btn-azul-claro" >
                    <a class="nav-link nav-link-icon text-center" href="{{ url('informacion_usuario/'. $usuario->id ) }}" role="button" id="dropdownMenuLink" >
                      <div class="nav-link-icon__wrapper" id="count_coment">
                      
                       <i class="fa fa-edit text-primary" style="font-size:1.3em;"></i>
                          <br/>
                         <span >Editar</span>
                      </div>
                    </a>
                    
                  </td>
                  <td class="td-btn-azul-claro" >
                    <a class="nav-link nav-link-icon text-center" href="{{ url('informacion_usuario/'. $usuario->id ) }}" role="button" id="dropdownMenuLink" >
                      <div class="nav-link-icon__wrapper" id="count_coment">
                      
                       <i class="fa fa-trash-alt text-primary" style="font-size:1.3em;"></i>
                          <br/>
                         <span >Borrar</span>
                      </div>
                    </a>
                  </td>
                   <td class="td-btn-azul-claro" >
                    <a class="nav-link nav-link-icon text-center" href="{{ url('informacion_usuario/'. $usuario->id ) }}" role="button" id="dropdownMenuLink" >
                      <div class="nav-link-icon__wrapper" id="count_coment">
                      
                       <i class="fas fa-angle-right text-primary" style="font-size:2em;"></i>
                          <br/>
                         <span >Inactivar</span>
                      </div>
                    </a>
                    
                  </td>
                </tr>

              @endforeach
            </tbody>
               <tfoot>
              <tr>
                 
                  <td colspan='4'><span style='font-size:0.9em'><b>Total:</b> {{ $usuarios->count() }} usuarios</span></td>
                 
              </tr>
              </tfoot>
          </table>
         </div> 

  </div>
</div>
  <!-- End Default Light Table -->
</div>


<div class="modal fade show" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" id="modal_usuarios">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" id="datohtml">
        <h4 class="modal-title" id="titul_modal_usuarios">Crear Nuevo Usuario</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">×</span>
        </button>
      </div>
      <div class="modal-body" id="contenido_modal_usuarios" style='min-height: 200px;'>
      </div>
    </div>
  </div>
</div>

@endsection
