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
use Illuminate\Support\Facades\Crypt;

class User_ComprasController extends Controller
{

    public function listado_compras(){

      $usuario_actual= Auth::user();
      if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }
   	  $compras =Compras::where('id_usuario','=',$usuario_actual->id)->orderBy('created_at','desc')->paginate(100);
   	  return view("usuario.u_listado_compras")->with("compras",$compras);

    }


    public function informacion_compra($idsol){

      $usuario_actual= Auth::user();
      if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }

    	$comprasel =Compras::where('id_usuario','=',$usuario_actual->id)->where('id','=',$idsol)->first();
        $empresasel=EmpresasPublicidad::find($comprasel->id_empresa); 



    	return view("usuario.u_informacion_compra")->with("comprasel",$comprasel)
    	                                           ->with("data_usuario", $usuario_actual)
    	                                           ->with("empresasel",$empresasel);


    }


    public function confirmar_compra(Request $request){


      $usuario_actual= Auth::user();
      if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }

      $id_producto=$request->input('id_producto');
      $producto=Publicidad::where("id","=",$id_producto)->where("cantidad",">",0)->first();
      if( $producto ){}else{ return response()->json(['estado'=> 'nopermitido' ], 200);  }
            
             
      $empresa=EmpresasPublicidad::find($producto->id_empresa);
      if( $empresa ){}else{ return response()->json(['estado'=> 'noempresa' ], 200);  }


             $valor_producto=$producto->puntos?$producto->puntos:0;
             $cantidad_producto=$producto->cantidad ?$producto->cantidad:0;
             
             $saldo_usuario=$usuario_actual->puntos?$usuario_actual->puntos:0;

             if($valor_producto<=0  ){  return response()->json(['estado'=> 'novalorproducto' ], 200);   }
             if($saldo_usuario<=0  ){  return response()->json(['estado'=> 'nosaldo', 'saldo_actual'=>$saldo_usuario ], 200);   }
             if($saldo_usuario<$valor_producto  ){  return response()->json(['estado'=> 'nosaldo' ], 200);   }


             $compra=new Compras;
             $compra->id_usuario=$id_user;
             $compra->id_empresa= $producto->id_empresa;
             $compra->id_producto= $producto->id;
             $compra->titulo=$producto->titulo;
             $compra->descripcion=$producto->descripcion;
             $compra->puntos=$producto->puntos;
             $compra->cantidad_comprada=1;
             $compra->logo=$producto->logo;
             $compra->estado=0;
             $compra->codigo=User_comprasController::generarCodigo(9);

             $parametros=['id_usuario'=> $id_user,  'id_producto' => $producto->id, 'codigo'=>$compra->codigo  ];
             $jsoncode=json_encode( $parametros);
             $encrypted = Crypt::encryptString($jsoncode);
             //$decrypted = Crypt::decryptString($encrypted);
             $compra->json_key=$encrypted;

             if( $compra->save() ){

                $producto->cantidad=$cantidad_producto-1;
                $producto->cantidad_comprada=$producto->cantidad_comprada+1;
                if(  $producto->cantidad<=0){  $producto->estado=0;   }
                $producto->save();

                $usuario=User::find($id_user);
                $puntos_actuales= $usuario->puntos;
                $usuario->puntos=$puntos_actuales-$valor_producto;
                if( $usuario->puntos<0){  $usuario->puntos=0; }
                $usuario->save();


                return response()->json(['estado'=> 'ok',  'compra' => $compra, 'producto'=>$producto->id  ], 200); 
             }
             else
             {
                return response()->json(['estado'=> 'norealizada',  'compra' => 0, 'producto'=> $producto->id ], 200); 
             }




    }

    public function generarCodigo($longitud) {
         $key = '';
         $pattern = '1234567890';
         $max = strlen($pattern)-1;
         for($i=0;$i < $longitud;$i++) $key .= $pattern{mt_rand(0,$max)};
         return $key;
     }

}