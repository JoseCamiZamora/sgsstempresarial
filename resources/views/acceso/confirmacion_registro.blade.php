@extends('layouts.auth')

@section('content')



  <body style='background-color: black;'>


        <div class="preloader">
         <div class="loader">
             <div class="loader__figure"></div>
             <p class="loader__label">Recibe Cargando</p>
         </div>
     </div>
 
         <!-- Main navigation -->
 
 
 
 <!-- Main navigation -->
 <header>

    

   <!-- Full Page Intro -->
   <div class="view jarallax" data-jarallax='{"speed": 0.2}' style="background-image: url( {{ asset('/assets/img/bg_003.jpg') }}); background-repeat: no-repeat; background-size: cover; background-position: center center;">
     <!-- Mask & flexbox options-->
        <div class="mask rgba-blue-slight d-flex justify-content-center align-items-center">
                <!-- Content -->

              
                <div class="container">


                        <!--Grid row-->
                        <div class="row">

                           

                                <!--Grid column-->
                                <div class="col-md-12 white-text align-items-center">
                        
                                        <div class="wow fadeInDown" data-wow-delay="0.3s">
                                            <!--Grid column-->
                                            <div class="col-md-6 col-xl-6 mb-5 mx-auto">

                                                   
                                                       <div class="align-items-center text-center" data-wow-delay="0.3s" style='background-color: rgba(3, 14, 19, 0.89); padding:20px 20px 20px 20px;'>
                                                           

                                                          <div ><i class="fa fa-check-circle-o" style='font-size:5em;font-weight: 100;color: #53bb53;' aria-hidden="true"></i></div>
                                                            <div><h5><b>Registro Completo</b></h5></div>
                                                            <div><p>Se ha registrado correctamente  y ya puede ingresar al sistema con el email y password registrado</p></div>
                                                            <a href="{{ url('login') }}" style='color:#53bb53;'>INGRESAR</a>
                                    
                                                       </div>
                                               
                               
                                                    <!--/.Form-->
                                

                                                
                                                
                                            </div>
                                            <!--Grid column-->
                                        </div>
                        
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
 </header>

 
 


 
 
 
 </body>

@endsection





