 @extends('layouts.auth')
 @section('htmlheader_title')
Registro
@endsection
  
@section('content')

<Style>
  .active{
   color:#32db64 !important;
    
  }
  .borde-verde{
    border-bottom: 1px solid #269e97 !important;
  }
  input{
    border-bottom: 1px solid #269e97 !important;
  }

  
</Style>

<body style='background-color:#087463; background-image: url( {{ asset("img/climpek.png") }}); '>      



<div class="preloader">
        <div class="loader">
            <div class="loader__figure"></div>
            <p class="loader__label">Cargando...</p>
        </div>
    </div>


        <!-- Main navigation -->
   

          <!-- Full Page Intro -->
          <div class="view" style=" ">
            <!-- Mask & flexbox options-->
            <div class="mask rgba-blue-slight d-flex justify-content-center align-items-center" style='position: relative; overflow:auto;'>
   
              <!-- Content -->
              <div class="container" style='margin-top: 0px;'>
                <!--Grid row-->
                <div class="row mt-5">
                  <!--Grid column-->
                  <div class="col-md-5 mb-4 mt-md-0 mt-4 white-text text-center text-md-left">

                    <h1 class="h1-responsive font-weight-bold wow fadeInLeft" data-wow-delay="0.3s"><img src="{{ asset('/assets/img/iconoversion3.svg') }}" width="210"></h1>

                    <hr class=" wow fadeInLeft" style="border-top: 2px solid #0e6259;" data-wow-delay="0.3s">
                    <h4 class="mb-3 wow fadeInLeft"> Registro de Usuario</h4>
                    <h6 class="mb-3 wow fadeInLeft" data-wow-delay="0.3s">

                      Recibe tiene como propósito
                      <br/>
                      Promover e impulsar el reciclaje en los hogares Colombianos
                      <br/>

                      <br/>
                      </h6>
                    
                  </div>
                  <!--Grid column-->
                  <!--Grid column-->
                  <div class="col-md-7 col-xl-7 mb-5"  >
                    <!--Form-->
                    <form id="form_register" method="post" action="{{ url('/registro_usuario') }}">
                    <input type='hidden' name='_token' value='{{ csrf_token() }}'>
                  
                   <div class="align-items-center" data-wow-delay="0.3s" style="background-color:#0b443f66;">
                    
                      <div class="card-body">
                        <!--Header-->
                        
                        <!--Body-->


                        <div class="row">
                            <div class="col-md-6">
                                <div class="md-form">
                                  <i class="fa fa-user prefix white-text active"></i>
                                  <input type="text" id="nombre_usuario" class="white-text form-control" required name="nombre_usuario">
                                  <label for="nombre_usuario" class="active"  style="color:white !important;" >Nombres</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="md-form">
                                  <i class="fa fa-user prefix white-text active"></i>
                                  <input type="text" id="apellido_usuario" class="white-text form-control" required name="apellido_usuario">
                                  <label for="apellido_usuario" class="active" style="color:white !important;" >Apellidos</label>
                                </div>
                            </div>
                        </div>

                    

                

                  

                   

                        <div class="row">
                            <div class="col-md-6">
                                <div class="md-form" >
                                  <i class="fa fa-map-marker prefix white-text active"></i>
                                    <div class="col-md-12">
                                  
            <select name="pais_usuario" id="pais_usuario" class="browser-default form-control"  style='border-bottom: 1px solid #269e97 !important;border-top:none;border-left:none;border-right:none; color:white; margin-left:20px;' required >	
       
              <option value="Colombia" style='color:#0b443f66;'>Colombia</option>	
         
              </select> 
                                 </div>
                 
                                  <label for="pais_usuario" class="active" style="color:white !important;">Pais</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="md-form">
                                  <i class="fa fa-map-marker prefix white-text active"></i>
                                  <div class="col-md-12">
                                   <select name="departamento" id="departamento" class="browser-default form-control" required onchange="reco_cambiar_departamento(this.value);"  style='border-bottom: 1px solid #269e97 !important;border-top:none;border-left:none;border-right:none; color:white; margin-left:20px;' > 
                                   
                                      @foreach($departamentos as $depa)
                                      
                                      <option value='{{$depa->departamento }}' style='color:#0b443f66;'>{{$depa->departamento}}</option>
                                     
                                      @endforeach
                                   </select>   
                                 </div>
                                  <label for="departamento" class="active" style="color:white !important;" >Departamento</label>
                                </div>
                            </div>


                        </div>




                          
                <div class="row">

                   <div class="col-md-12">
                                <div class="md-form">
                                  <i class="fa fa-map-marker prefix white-text active"></i>
                                  <div class="col-md-12">
                                   <select name="ciudad" id="ciudad" class="browser-default form-control" required   style='border-bottom: 1px solid #269e97 !important;border-top:none;border-left:none;border-right:none; color:white; margin-left:20px;' > 
                                   
                                     <option value='' disabled selected required>seleccione ..</option>
                                   </select>   
                                 </div>
                                  <label for="ciudad" class="active" style="color:white !important;" >ciudad</label>
                                </div>
                            </div>


                </div>


                    <div class="md-form col-xs-12">
                          <i class="fa fa-envelope prefix white-text active"> </i>
                          <input type="email" class="white-text form-control valCP_email" required id="email_usuario" name="email_usuario">
                          <label for="email" class="active" style="color:white !important;" >Email</label>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="md-form">
                                  <i class="fa fa-lock prefix white-text active"></i>
                                  <input type="password" id="password_usuario" class="white-text form-control" required name="password_usuario">
                                  <label for="password_usuario" style="color:white !important;" >Contraseña</label>
                                </div>
                            </div>
                         
                        </div>
          
                       
                       <!--<div class="g-recaptcha" data-sitekey="6Lf8E48UAAAAAPKhfVjtRgZRy6n4PuOUn9Gkt4KM" style="margin:0 auto;"></div>-->
                        

                        <div class="row">
                            <div class="col-md-6">
                              <div class="text-center mt-4">
                                    <button class="btn  accent-4  m-l-5 waves-effect waves-light" style="background-color:#32db64; border-radius:94px; color:white; font-weight: 700px;"  type="submit" onclick='validate_registro();' >Crea tu Cuenta Recibe</button>
                              </div>
                            </div>
                            <div class="col-md-6">
                                  <div class="text-left mt-4">

                                 <div class="form-check mr-3">


                                    <input type="checkbox" id="aceptar_tc" name="aceptar_tc" class="filled-in form-check-input classcod" value="true" required >

                                    <label class="form-check-label text-white" for="aceptar_tc"><a href="{{ url('politicas') }}"  style='color:#32db64;font-size:13px;' target='_blank'>Aceptar Términos Y Condiciones</a></label>
                                  </div>
                                </div>
                               
                            </div>
                        </div>

                        <hr class="hr-light mb-3 mt-4">
                                    <div class="inline-ul text-center d-flex justify-content-center">
                                      <div class="col-sm-12 text-center">
                                          <h6 style="color: #ffffff">Ya tienes una cuenta? <a href="{{ url('/login') }}" class="text-success m-l-5"><b>Ingresa Aquí</b></a></h6>
                                      </div>
                                    </div>

                         

                          <!--/.Panel 3-->
  <?php
$variabledata=json_encode($departamentos);
?>
<textarea id='jsondepartamentos' style='display:none;'>{{$variabledata}}</textarea>

                      </div>
                    </div>
                    </form>
                    <!--/.Form-->
                  </div>
                  <!--Grid column-->
                </div>
                <!--Grid row-->
              </div>
              <!-- Content -->
            </div>
            <!-- Mask & flexbox options-->
          </div>
          <!-- Full Page Intro -->
 
 </body>
@endsection


