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

class User_ProductosController extends Controller
{

    public function listado_productos(){

      $usuario_actual= Auth::user();
      if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }
   	  $productos =Publicidad::where('cantidad','>',0)->orderBy('created_at','desc')->paginate(100);
   	  return view("usuario.u_listado_productos")->with("productos",$productos);

    }


    public function informacion_producto($idsol){

      $usuario_actual= Auth::user();
      if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }

    	  $productosel =Publicidad::where('id','=',$idsol)->first();
        $empresasel=EmpresasPublicidad::find( $productosel->id_empresa); 



    	return view("usuario.u_informacion_producto")->with("productosel",$productosel)
    	                                           ->with("data_usuario", $usuario_actual)
    	                                           ->with("empresasel",$empresasel);
    }

}