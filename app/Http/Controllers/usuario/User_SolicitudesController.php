<?php

namespace App\Http\Controllers\usuario;


use App\Http\Controllers\Controller;


use Illuminate\Http\Request;
use Auth;
use App\Models\User;
use App\Solicitudes;
use App\Recolectores;
use App\Valores;
use App\Departamentos;
use App\Materiales;
use App\Medios;
use App\FechasRecoleccion;
use App\Rutas;
use App\Barrios;


class User_SolicitudesController extends Controller
{


     public function listado_solicitudes(Request $request){

      $usuario_actual= Auth::user();
      if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }
   	  $solicitudes =Solicitudes::where('estado','=',1)->where('id_usuario','=',$usuario_actual->id)->orderBy('created_at','desc')->paginate(100);
   	  return view("usuario.u_listado_solicitudes")->with("solicitudes",$solicitudes);

     }

    public function listado_solicitudes_asignadas(Request $request){

	      $usuario_actual= Auth::user();
	      if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }
	      $solicitudes =Solicitudes::where('estado','=',2)->where('id_usuario','=',$usuario_actual->id)->paginate(100);
	      return view("usuario.u_listado_solicitudes_asignadas")->with("solicitudes",$solicitudes);

	}


	public function listado_solicitudes_recogidas(Request $request){

	      $usuario_actual= Auth::user();
	      if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }
	      $solicitudes =Solicitudes::where('estado','=',3)->where('id_usuario','=',$usuario_actual->id)->paginate(100);
	      return view("usuario.u_listado_solicitudes_recogidas")->with("solicitudes",$solicitudes);

	}


	public function listado_solicitudes_completadas(Request $request){

	      $usuario_actual= Auth::user();
	      if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }
	      $solicitudes =Solicitudes::where('estado','=',4)->where('id_usuario','=',$usuario_actual->id)->paginate(100);
	      return view("usuario.u_listado_solicitudes_completadas")->with("solicitudes",$solicitudes);

	}


	 public function listado_solicitudes_canceladas(Request $request){

	      $usuario_actual= Auth::user();
	      if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }
	      $solicitudes =Solicitudes::where('estado','=',0)->where('id_usuario','=',$usuario_actual->id)->orderBy('created_at','desc')->paginate(100);
	      return view("usuario.u_listado_solicitudes_canceladas")->with("solicitudes",$solicitudes);

	}


	public function informacion_solicitud($idsol){

      $usuario_actual= Auth::user();
      if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }

    	$solicitud=Solicitudes::where('id','=',$idsol)->where('id_usuario','=',$usuario_actual->id)->first();
        $usuarios=User::where('rol', '=', 2)->where('estado', '=', 1)->get();



    	return view("usuario.u_informacion_solicitud")->with("solicitud",$solicitud)
                                                      ->with("recolectores",$usuarios);

    }

     public function informacion_solicitud_asignar($idsol){

       
      $usuario_actual= Auth::user();
      if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); 
      }

      $solicitud=Solicitudes::where('id','=',$idsol)->where('id_usuario','=', $usuario_actual->id)->first();
      $usuarios=User::where('rol', '=', 2)->get();
      $recolectorsel=User::where('id', '=', $solicitud->id_recolector )->where('rol', '=', 2)->first();


      return view("usuario.u_informacion_solicitud_asignada")->with("solicitud",$solicitud)
                                                              ->with("recolectorsel",$recolectorsel)
                                                              ->with("recolectores",$usuarios);

    }


    public function informacion_solicitud_recogida($idsol){

        $usuario_actual= Auth::user();
        if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }

      $solicitud=Solicitudes::where('id','=',$idsol)->where('id_usuario','=', $usuario_actual->id)->first();
      $valores=Valores::where('app','=',1)->first();
      $usuarios=User::where('rol', '=', 2)->get();
      $recolectorsel=User::where('id', '=', $solicitud->id_recolector )->where('rol', '=', 2)->first();
      $recolectorrec=User::where('id', '=', $solicitud->id_usuario_rec )->first();


      return view("usuario.u_informacion_solicitud_recogida")->with("solicitud",$solicitud)
                                                                ->with("recolectorrec", $recolectorrec )
                                                               ->with("recolectorsel",$recolectorsel)
                                                               ->with("recolectores",$usuarios)
                                                               ->with("valores", $valores);

    }

     public function informacion_solicitud_final($idsol){

        $usuario_actual= Auth::user();
        if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }

      $solicitud=Solicitudes::where('id','=',$idsol)->where('id_usuario','=', $usuario_actual->id)->first();
      $usuarios=User::where('rol', '=', 2)->get();
      $recolectorsel=User::where('id', '=', $solicitud->id_recolector )->where('rol', '=', 2)->first();
      $recolectorrec=User::where('id', '=', $solicitud->id_usuario_rec )->first();
      $valores=Valores::where('app','=',1)->first();


      return view("usuario.u_informacion_solicitud_final")->with("solicitud",$solicitud)
                                                                ->with("recolectorrec", $recolectorrec )
                                                               ->with("recolectorsel",$recolectorsel)
                                                               ->with("valores", $valores)
                                                               ->with("recolectores",$usuarios);

    }


    public function informacion_solicitud_cancelada($idsol){

      $usuario_actual= Auth::user();
      if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }

      $solicitud=Solicitudes::where('id','=',$idsol)->where('id_usuario','=', $usuario_actual->id)->first();
      $usuarios=User::where('rol', '=', 2)->get();



      return view("usuario.u_informacion_solicitud_cancelada")->with("solicitud",$solicitud)
                                                      ->with("recolectores",$usuarios);

    }



   
    public function cancelar_solicitud($idsol){
         $usuario_actual= Auth::user();
        if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }

    	$solicitud=Solicitudes::find($idsol);
        $solicitud->estado=0;

	    if( $solicitud->save()){
	        return response()->json([ 'estado' => 'cancelada', 'idsolicitud' => $idsol ], 200);
	     }
	      else
	     {
	        return response()->json([ 'estado' => 'error', 'idsolicitud' => 0 ]);
	     }

    }

    public function form_nueva_solicitud(){



      $usuario_actual=Auth::user();
      if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }
      $materiales=Materiales::all();
      $medios=Medios::all();
      $barrios=Barrios::all();
      $rutas=Rutas::all();
      $fechas=FechasRecoleccion::all();
      $departamentos=Departamentos::all();
      return view("usuario.u_form_nueva_solicitud")
                 ->with("usuario_actual",$usuario_actual)
                 ->with("departamentos",$departamentos)
                 ->with("materiales",$materiales)
                 ->with("rutas",$rutas)
                 ->with("medios",$medios)
                 ->with("barrios",$barrios)
                 ->with("fechas",$fechas);

    }

    public function crear_solicitud(Request $request){

      $usuario_actual=Auth::user();
      if( $usuario_actual->rol!=0 ){  return view("mensajes.msj_no_autorizado")->with("msj","no tiene autorizacion para acceder a esta seccion"); }


        $id_user = $usuario_actual->id;
        //$fecha_recoleccion=$request->json()->get('fecha') ? $request->json()->get('fecha'):[];
        //$fecha_recoleccion=$data->fecha? $data->fecha:[];
       
        //$medio_recoleccion= $data->mediorecoleccion ? $data->mediorecoleccion:[];
        //$materiales=$data->materiales ? $data->materiales :[];
        $usuariosel =User::find($id_user);
        $data_usuario = array('id' => $usuariosel->id, 'nombres' => $usuariosel->nombres, 'email' => $usuariosel->email);
        $direccion=array('direccion' => $request->input('direccion'), 'pais'=>'Colombia','ciudad' => $request->input('ciudad'), 'departamento' => $request->input('departamento'),'celular' => $request->input('telefono') );

      

        $materiales=Materiales::all();
        $arrayMateriles=[];

        $news = $request->input('materiales') ? $request->input('materiales'):[];
        foreach($news as $us){  
              foreach($materiales as $mat){ 
                if($mat->id == $us ){

                   $dd=array('id' => $mat->id, 'material' => $mat->titulo );
                   array_push(  $arrayMateriles, $dd);
                }
                
              }
         }



    


        $arrayFecha=[];
        $fbr=$request->input('barrio')?$request->input('barrio'):0;
        $barriosrec=Barrios::find($fbr); 
  
        $frc=$request->input('fecharec')?$request->input('fecharec'):0;
        $fechasrec=FechasRecoleccion::find($frc);

        $newfec=array('id_barrio' =>  $barriosrec->id, 'id_fecha' =>  $fechasrec->id, 'id_ruta'=>$barriosrec->id_ruta,  'nom_barrio'=>$barriosrec->titulo, 'nom_fecha' =>  $fechasrec->titulo );
        array_push( $arrayFecha,$newfec);

        $arrayMedio=[];
        $fmed=$request->input('medios')?$request->input('medios'):0;
        $mediosrec=Medios::find($fmed);      
        $newMed=array('id' =>  $mediosrec->id, 'medio' =>  $mediosrec->titulo );
        array_push( $arrayMedio,  $newMed);

        
        //return response()->json(['estado'=> 'ok','materiales'=> $arrayMateriles,  'fechas'=>  $arrayFecha, 'medios'=>  $arrayMedio   ], 200);
            
       

        $solicitud=new Solicitudes;
        $solicitud->id_usuario=$id_user;
        $solicitud->data_usuario=json_encode($data_usuario);
        $solicitud->materiales=json_encode($arrayMateriles);
        $solicitud->fecha_recoleccion= json_encode($arrayFecha);
        $solicitud->medio=json_encode($arrayMedio);
        $solicitud->direccion= json_encode($direccion);
        $solicitud->estado= 1;

        if($solicitud->save() ){

      
           return response()->json(['estado'=> 'ok','solicitud'=>$solicitud   ], 200);
        }
        else{
            return response()->json(['estado'=> 'error','solicitud'=>'fallo solicitud'  ], 400); 
        }


    }

}