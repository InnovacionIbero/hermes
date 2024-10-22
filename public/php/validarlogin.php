<?php

///traemos el archivo requerido
require_once __dir__."/../../../modelo/obtenerdatos.php";

/// creamos la nueva clase
$mvcDatos=new GetDatos();
// seteamos por defecto la zona horaria
date_default_timezone_set("America/bogota");

//traemos la hota actual
$momento=date("d/m/Y h:i:s A");

/// verificamos los dagtos enviados por post
if (isset($_POST["usr"]) && isset($_POST["pass"])) {
		
	/// si existen  ejecutamos el query
	$result=$mvcDatos->selectQuery("SELECT * FROM usuarios WHERE usuario='".$_POST["usr"]."' and password=md5('".$_POST["pass"]."') and estado=1");

	/// verificamos que hayan datos
	if (count($result)>0) 
	{
		/// verificamos que exista la sesion  si no existe la inicializamos
		if (!isset($_SESSION))
		{
			session_start();
		}

		$tipouser='';


		/// definimos las variables de usuario (id,tipo,nombre,foto)
		$_SESSION["id_usuario"]=$result[0]["id_usuario"];
		$_SESSION["tipo"]=$result[0]["tipo"];
		$_SESSION["usuario"]=$result[0]["usuario"];
		$_SESSION["foto"]=$result[0]["foto"];
		$_SESSION["nombre"]=$result[0]["nombre"]." ".$result[0]["apellido"];


		/// verificamos que tipo de rol tiene
		/*if ($result[0]["tipo"]==1)
		{
			//tipo 1 es administrador
			$tipouser="Administrador";
		}
		else
		{
			/// tipo usuario normal
			$tipouser="Usuario";
		}
		*/
		///si existe el usuario en la base de datos redirigimos el home
		if ($result[0]["tipo"] ) 
		{			
			$info=array('success'=>true,'msg'=>"Usuario Correcto",'link'=>controlador::$rutaAPP."index.php/home");		

			/// actualizamos la base de datos con el logueo a 0 
			$result=$mvcDatos->update_query("update usuarios set intentos=0 where usuario='".$_POST["usr"]."'");
		}

	} else{
		
		
		$consultar=$mvcDatos->selectQuery("select intentos from usuarios where usuario='".$_POST["usr"]."'");

		/// sino tiene resultado la consulta anterior verificamos si exizte el usuario
		if (count($consultar)>0)
		{

			/// si existe el usuario verificamos el numero de intentos que ha hecho
			foreach ($consultar as $key => $value) {
				$intentos=$value["intentos"];
			}

			/// dependiendo el numero de intentos le mostrara cuantos tiene disponible antes de bloquear el acceso total del usuario en este caso tenemos 5 intentos permitidos

			if ($intentos==0)
			{
				$result=$mvcDatos->update_query("update usuarios set intentos=1 where usuario='".$_POST["usr"]."'");

				$info=array('success'=>false,'msg'=>"CONTRASEÑA INCORRECTA (INTENTOS DISPONIBLES:".(4).")");

			}elseif ($intentos==1) {
				$result=$mvcDatos->update_query("update usuarios set intentos=2 where usuario='".$_POST["usr"]."'");
				$info=array('success'=>false,'msg'=>"CONTRASEÑA INCORRECTA (INTENTOS DISPONIBLES:".(3).")");
			}elseif ($intentos==2) {
				$result=$mvcDatos->update_query("update usuarios set intentos=3 where usuario='".$_POST["usr"]."'");
				$info=array('success'=>false,'msg'=>"CONTRASEÑA INCORRECTA (INTENTOS DISPONIBLES:".(2).")");
			}elseif ($intentos==3) {
				$result=$mvcDatos->update_query("update usuarios set intentos=4 where usuario='".$_POST["usr"]."'");
				$info=array('success'=>false,'msg'=>"CONTRASEÑA INCORRECTA (INTENTOS DISPONIBLES:".(1).")");
			}elseif ($intentos==4) {
				$result=$mvcDatos->update_query("update usuarios set intentos=5 where usuario='".$_POST["usr"]."'");
				$info=array('success'=>false,'msg'=>"CONTRASEÑA INCORRECTA (INTENTOS DISPONIBLES:".(0).")");
			}elseif ($intentos==5) {
				$result=$mvcDatos->update_query("update usuarios set estado=2 where usuario='".$_POST["usr"]."'");
				$info=array('success'=>false,'msg'=>"USUARIO '".($_POST["usr"])."' BLOQUEADO, CONTACTE CON EL ADMINISTRADOR.");
			}
		}else
		{
			/// si no existe el usuario 
			$info=array('success'=>false,'msg'=>"USUARIO DESCONOCIDO");
		}
					

	}
} else {
	/// si no envian datos 
	$info=array('success'=>false,'msg'=>"-");
}
echo json_encode($info);
?>