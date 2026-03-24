@extends('layouts.auth')

@section('htmlheader_title')
   Recuperar Password
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


  <body style="background-color:#087463; background-image: url( {{ asset("img/climpek.png") }}); ">


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
   <div class="view jarallax" data-jarallax='{"speed": 0.2}' >
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

                                                    <!--Form-->
                                            <form id="form_rest_password" method="post" action="{{url('recuperar_password')}}">
                                                        <input type='hidden' name='_token' value='{{ csrf_token() }}'>
                                                       <div class="align-items-center" data-wow-delay="0.3s" style="background-color:#0b443f66;" >
                                                     
                                                        <div class='card-body'>


                                                      
                                                           <div class="mx-auto align-items-center" style="margin-top: 2rem; margin-bottom: 1rem">
                                                               <img class="mx-auto" src="{{ asset('/assets/img/iconoversion3.svg') }}" width="200">
                                                           </div>

                                                             <div class="mx-auto align-items-center text-center" >
                                                               <p>Ingrese el email para recuparar su password</p>
                                                               
                                                           </div>

                                                           
                                
                                                       
                                                           <div class="md-form col-xs-12">
                                                               <i class="fa fa-envelope prefix white-text active"> </i>
                                                               <input type="email" class="white-text form-control valCP_email" required id="email" name="email">
                                                               <label for="email" class="active" style="color:white">Email</label>
                                                           </div>
                                                            
                                                       
                                                           @if (count($errors) > 0)
               
        
                                                         
                                                           <ul class='red-text'>
                                                               @foreach ($errors->all() as $error)
                                                                   <li class='red-text'><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> {{ $error }}</li>
                                                               @endforeach
                                                           </ul>
                                                      
                                             
                                                           @endif
                               
                                            
                               
                                                           <div class="md-form  col-xs-12 text-center" style='margin-top:20px; '>
                                                      

                                                                   <div class="text-center ">
                                                                       
                                                                       
                                                                        <button class="btn  accent-4  m-l-5"  style="background-color:#32db64; border-radius:94px; color:white; font-weight: 700px;" type="submit" >recuperar password</button>
                                                           
                                                                    <hr class="hr-light mb-3 mt-4">
                                        
                                                                    <div class="inline-ul text-center d-flex justify-content-center">
                                                                        <div class="col-sm-12 text-center">
                                                                            <h6 style="color: #ffffff">No tienes una cuenta? <a href="{{url('/register')}}" class="text-success m-l-5">Registrate Aquí</a></h6>
                                                                        </div>
                                                                    </div>
                                            
                                                                   
                                        
                                                                 </div>
                                                           
                                                            </div
                               
                                                        
                                                        
                                                       </div> 
                                                       </div>
                                                    </form>
                               
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


