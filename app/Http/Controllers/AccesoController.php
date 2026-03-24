<?php /** @noinspection ALL */

namespace App\Http\Controllers;

use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Datatables;

use App\Directorio;
use App\UsuariosPendientes;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use App\Globales;
use App\Mensajes;
use App\Conteo;
use App\Paises;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Sent;

class AccesoController extends Controller
{
    //

     public function registro_usuario(Request $request){



    

        if(!$request->input('aceptar_tc')){  return view('acceso.error_terminos'); }

        
        $email=$request->input('email_usuario');
        $duplicado= User::where('email','=',$email)->first();

        

        if($duplicado){  return view('acceso.error_registro'); }
        
        $usuarioP=new User;
        $usuarioP->tipo=1;
        $usuarioP->rol=0;
        $usuarioP->nombres=$request->input('nombre_usuario') . ' '. $request->input('apellido_usuario') ;
        $usuarioP->email=$request->input('email_usuario');
        $usuarioP->pais='Colombia';
        $usuarioP->departamento=$request->input('departamento');
        $usuarioP->ciudad=$request->input('ciudad');
        $usuarioP->password= Hash::make($request->input('password_usuario'));
     
        
        if( $usuarioP->save() ){

            Mail::send('email.template_usuario_aprobado', ['user' => $usuarioP, 'password'=>  $request->input('password_usuario') ], function ($m) use ($usuarioP) {
                        $m->from('soporte@recibe.co', 'RECIBE');
                        $m->to($usuarioP->email, $usuarioP->name)->subject('Bienvenido a RECIBE');
                    });

            
            return view('acceso.confirmacion_registro');
        }
        else
        {
            return view('acceso.error_registro');
        }

    }



    public function form_reset_password(){
         return view('auth.resetpassword');
    }

    public function recuperar_password(Request $request){
         $email=$request->input('email','0');
         $user=User::where('email',"=",$email )->first();
         if(!$user ){
             return response()->json([ 'estado' => 'noencontrado' ],400);

         }
         if($user ){  
            
            $newpassword=rand(pow(10, 4 - 1) - 1, pow(10, 4) - 1);        
            $user->password= Hash::make($newpassword); 
           
           
            if($user->save() ){
                    Mail::send('email.template_reset_password', ['user' => $user, 'password'=>  $newpassword], function ($m) use ($user) {
                        $m->from('soporte@recibe.co', 'RECIBE');
                        $m->to($user->email, $user->name)->subject('Recuperaciòn de Password');
                    });
            
                    return response()->json([ 'estado' => 'enviado' ],200);  
            }
           
            
        
        }


        

    }


    public function login_externo(Request $request){

        $password=$request->input('password');
        $email=$request->input('email');
        $user=User::where('email',"=",$email )->first();
        //return dd('llega hasta aqui',$user);
        if(!$user ){
            return redirect('login')->withErrors([ 'email' => 'Email no encontrado '])->withInput();
        }
        if($user ){

            if($user->estado == 'A'){
                if (Auth::attempt(array('email' => $email, 'password' => $password))){
                    $userreal=User::where("email","=",$email)->first();
                    Auth::login($userreal); 
                    return redirect('home');

                }else{

                    return redirect('login')->withErrors([ 'email' => 'credenciales incorrectas'])->withInput();
                
                }
                die;
            }else{
                return redirect('login')->withErrors([ 'email' => 'Usuario inactivo'])->withInput();

            }
         
            
            

        }


       
   }



    public function politicas(){
         return view('acceso.politicas');
    }

    public function logout () {
        //logout user
        auth()->logout();
        // redirect to homepage
        return redirect('/login');
    }






    public function email_revisado(){

        $sent=new Sent;
        $sent->val=1;
        $sent->save();


         return response()->json([ 'estado' => 'enviado' ],200);  
    }


  








}

