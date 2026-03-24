<?php

namespace App\Http\Controllers\usuario;


use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Solicitudes;
use App\Recolectores;
use App\Valores;
use App\Compras;
use App\EmpresasPublicidad;
use App\Publicidad;
use App\Departamentos;
use App\PuntosCedidos;
use Illuminate\Support\Facades\Crypt;



use Illuminate\Pagination\LengthAwarePaginator as Paginator;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class User_CuentaController extends Controller
{
    
    public function informacion_cuenta(){

  	$usuario_actual= Auth::user();
    if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }

    $departamentos=Departamentos::all();

    return view("usuario.u_info_editar_cuenta")->with("usuario",  	$usuario_actual)->with("departamentos",$departamentos); 
    }


    public function editar_cuenta(Request $request){
      $usuario_actual= Auth::user();
	  if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }
	          
	    $idusuario=$usuario_actual->id;
	    $usuario=User::where('id','=',$idusuario)->where('rol','=',0)->first();

		$usuario->nombres=strtoupper( $request->input("nombres") ) ;
	    $usuario->telefono=$request->input("telefono");
	    $usuario->celular=$request->input("telefono");
	    $usuario->pais=$request->input("pais");
	    $usuario->departamento=$request->input("departamento");
	    $usuario->ciudad=$request->input("ciudad");
	    $usuario->direccion=$request->input("direccion");
	    $usuario->identificacion=$request->input("identificacion");
	    
	    
		 
	    if( $usuario->save()){
	        return response()->json([ 'estado' => 'actualizado', 'idusuario' => $usuario->id ]);
	    }
	    else
	    {
			return response()->json([ 'estado' => 'error', 'idusuario' => 0 ]);
	    }
    }



    public function editar_acceso_cuenta(Request $request){

           $usuario_actual= Auth::user();
         if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }
         
         $idusuario= $usuario_actual->id;
         $usuario=User::where('id','=',$idusuario)->where('rol','=',0)->first();
    
         $usuario->password=  Hash::make($request->input('password','niltonpruebas'));
         
         if( $usuario->save()){
            return response()->json([ 'estado' => 'actualizado', 'idusuario' => $usuario->id ]);
         }
          else
         {
            return response()->json([ 'estado' => 'error', 'idusuario' => 0 ]);
         }
    }

     public function editar_imagen_cuenta(Request $request){
    
        $usuario_actual= Auth::user();
         if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }
   
        $file = $request->file('photo');
   
        $id_usuario = $usuario_actual->id;
    
        $rutafinal='cuentas/'.$id_usuario.'/avatar_'.$id_usuario.'.'.$file->extension();
        Storage::disk('public')->put($rutafinal,  file_get_contents($file)  );
        $usuario=User::where('id','=', $id_usuario)->where('rol','=',0)->first();
        $usuario->foto=$rutafinal;
   
	    if( $usuario->save()){
	        return response()->json([ 'estado' => 'actualizado', 'idusuario' => $usuario->id, 'url_imagen'=> $rutafinal ]);
	    }
	    else
	    {
	    return response()->json([ 'estado' => 'error', 'idusuario' => 0, 'url_imagen'=> '' ]);
	    }
    
  
    }


	public function listado_monedas(Request $request){

	      $usuario_actual= Auth::user();
	      if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }

          $puntoscedidos=PuntosCedidos::where('id_de','=',$usuario_actual->id)->get();
          $puntosganados=PuntosCedidos::where('id_para','=',$usuario_actual->id)->get();

	      $solicitudes =Solicitudes::where('estado','=',4)->where('id_usuario','=',$usuario_actual->id)->paginate(100);
	      return view("usuario.u_listado_monedas")->with("solicitudes",$solicitudes)
	                                              ->with("puntosganados", $puntosganados)
	                                              ->with("puntoscedidos", $puntoscedidos);

	}


	public function form_ceder_monedas(){

		$usuario_actual= Auth::user();
	    if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }


        return view("usuario.u_form_ceder_monedas");

	}


	public function ceder_monedas(Request $request){

		$usuario_actual= Auth::user();
	    if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }

	    $puntos=$request->input('puntos')?$request->input('puntos'):0;
	    $puntos=intval($puntos);
	    $email=$request->input('email')?$request->input('email'):'niltonjairo2000@gmail.com';
	    $userpara=User::where('email','=',$email)->first();
	    if($userpara){}else{  return response()->json([ 'estado' => 'noemail', 'idusuario' => 0 ],200); }

	    if($userpara->email==$usuario_actual->email){  return response()->json([ 'estado' => 'emailidentico', 'idusuario' => 0 ],200); }

	    
	    if($puntos>0 ){}else{  return response()->json([ 'estado' => 'puntosmenores', 'idusuario' => 0 ],200); }

	    if( $puntos>$usuario_actual->puntos ){  return response()->json([ 'estado' => 'nosaldo', 'idusuario' => 0 ],200);      }

        
	    $userde=User::where('id','=',$usuario_actual->id)->first();
        $userde->puntos= $userde->puntos-$puntos;
        if($userde->save() ){ 
               $userpara=User::where('email','=',$email)->first();
               $userpara->puntos=  $userpara->puntos+$puntos;

               if( $userpara->save()){
               	   $puntoscedidos=new PuntosCedidos;
               	   $puntoscedidos->id_de=$usuario_actual->id;
               	   $puntoscedidos->id_para=$userpara->id;
               	   $puntoscedidos->detalle='ref : ' . $userpara->email .'';
               	   $puntoscedidos->puntos=$puntos;
               	   $puntoscedidos->save();

	               return response()->json([ 'estado' => 'actualizado' ],200);
			    }
			    else
			    {
			       return response()->json([ 'estado' => 'error', 'idusuario' => 0 ],400);
			    }


        }



	}


	public function borrar_cuenta(Request $request){
        
        $usuario_actual= Auth::user();
		$idusuario= $usuario_actual->id;
        $usuario=User::find($idusuario);

        $compras=Compras::where('id_usuario','=', $idusuario)->delete();
        $solicitudes=Solicitudes::where('id_usuario','=', $idusuario)->delete();
       

      
        if( $usuario->delete() ){
        
           return response()->json([ 'estado' => 'borrado', 'idusuario' => $idusuario ]);
           
        }
        else
        {
            return response()->json([ 'estado' => 'error', 'idusuario' => 0 ]);
        }

	}
   
}