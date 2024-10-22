<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CambioPassRequest;
use App\Http\Requests\UsuarioLoginRequest;
use App\Http\Requests\CrearFacultadRequest;
use App\Models\Facultad;
use App\Models\Roles;
use DateTime;
use App\Models\User;
use App\Models\Usuario;
use App\Http\Util\Constantes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

// use Yajra\DataTables\DataTables;


/** campos de usuario auth()->user()
 *
 *
 * id":2,
 * "id_banner":789,
 * "documento":789,
 * "nombre":"ihbkj",
 * "email":"juan@juan.com",
 * "id_rol":2,
 * "id_facultad":1,
 * "programa":"3;6;",
 * "ingreso_plataforma":1,
 * "activo":1,
 * "email_verified_at":null,
 * "created_at":"2023-06-20T14:45:03.000000Z",
 * "updated_at":"2023-06-20T17:08:15.000000Z"}
 *
 *
 */
class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Retorna a la vista Home
     */
    public function home(Request $request)
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        $user = auth()->user();
        $rol_db = DB::table('roles')->where([['id', '=', $user->id_rol]])->get();
        $nombre_rol = $rol_db[0]->nombreRol;

        auth()->user()->nombre_rol = $nombre_rol;
        return view('vistas.home');
    }

    public function vistasMafi()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        $user = auth()->user();

        $rol_db = DB::table('roles')->where([['id', '=', $user->id_rol]])->get();

        $nombre_rol = $rol_db[0]->nombreRol;
        auth()->user()->nombre_rol = $nombre_rol;

        
        if (!empty($user->id_facultad)) {
            $facultad = DB::table('facultad')->where([['id', '=', $user->id_facultad]])->get();
        } else {
            $facultad = DB::table('facultad')->get();
        }

        $datos = array(
            'rol' => $nombre_rol,
            'facultad' => $facultad,
        );

        return view('vistas.mafi.admin')->with('datos', $datos);
    }

    public function vistasPlaneacion()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        $user = auth()->user();

        $rol_db = DB::table('roles')->where([['id', '=', $user->id_rol]])->get();

        $tabla = 'planeacion';
        $nombre_rol = $rol_db[0]->nombreRol;
        auth()->user()->nombre_rol = $nombre_rol;
        if ($nombre_rol === 'Admin') {
            $nombre_rol = strtolower($nombre_rol);
        }

        return view('vistas.planeacion.adminPlaneacion', ['tabla' => $tabla]);
    }

    public function vistasMoodle()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        $user = auth()->user();

        $rol_db = DB::table('roles')->where([['id', '=', $user->id_rol]])->get();

        $tabla = 'moodle';
        $nombre_rol = $rol_db[0]->nombreRol;
        auth()->user()->nombre_rol = $nombre_rol;
        if ($nombre_rol === 'Admin') {
            $nombre_rol = strtolower($nombre_rol);
        }

        $idRol =  auth()->user()->id_rol;
        $idUser = auth()->user()->id;
        
        return view('vistas.moodle.adminMoodle', ['tabla' => $tabla], ['idRol'=>$idRol], ['idUser'=>$idUser]);
    }

    public function vistasMoodlecerrados()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        $user = auth()->user();

        $rol_db = DB::table('roles')->where([['id', '=', $user->id_rol]])->get();

        $tabla = 'moodleCerrado';
        $nombre_rol = $rol_db[0]->nombreRol;
        auth()->user()->nombre_rol = $nombre_rol;
        if ($nombre_rol === 'Admin') {
            $nombre_rol = strtolower($nombre_rol);
        }

        $idRol =  auth()->user()->id_rol;
        $idUser = auth()->user()->id;
        
        return view('vistas.moodle.matriculasmoodle', ['tabla' => $tabla], ['idRol'=>$idRol], ['idUser'=>$idUser]);
    }
    // funcion para traer todos los usuarios a la vista de administracion

    public function userView()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        /**Se retorna la vista del listado usuarios */
        return view('vistas.admin.usuarios');
    }

    public function get_users()
    {
        
        /**Realiza la consulta anidada para onbtener el usuario con su rol */
        $users = DB::table('users')->join('roles', 'roles.id', '=', 'users.id_rol')
            ->select('users.id', 'users.id_banner', 'users.documento', 'users.activo', 'users.nombre', 'users.email', 'roles.nombreRol','users.fecha_inicio_sesion','users.ingresos_a_plataforma')->get();
        /**mostrar los datos en formato JSON */
        header("Content-Type: application/json");
        /**Se pasa a formato JSON el arreglo de users */
        echo json_encode(array('data' => $users));
    }

    public function get_roles()
    {
        
        $roles = DB::table('roles')->get();
        /**mostrar los datos en formato JSON */
        header("Content-Type: application/json");
        /**Se pasa a formato JSON el arreglo de users */
        echo json_encode(array('data' => $roles));
    }

    public function facultad_view()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        /**Se retorna la vista del listado de facultades */
        return view('vistas.admin.administracionfacultades');
    }

    public function roles_view()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        /**Se retorna la vista del listado usuarios */
        return view('vistas.admin.roles');
    }

    ///** funcion para cargar vistas de facultades */
    public function get_facultades()
    {
        /* Consulta para obtener las facultades */
        $facultades = DB::table('facultad')->select('facultad.id', 'facultad.codFacultad', 'facultad.nombre', 'facultad.activo')->get();
        /* Mostrar los datos en formato JSON*/
        header("Content-Type: application/json");
        /* Se pasa a formato JSON el arreglo de facultades */
        echo json_encode(array('data' => $facultades));
    }

    // *Método para mostrar todos sus datos al Usuario
    public function perfil()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        // *Se llama la función para obtener facultad y programa del usuario*
        list($nombre_programas, $facultad) = $this->getfacultadyprograma();
        // * Función para obtener el rol del usuario
        $roles = $this->getrol();
        // *Array para retornar todos los datos obtenidos
        $datos = array(
            'facultad' => $facultad,
            'rol' => $roles[0]->nombreRol,
            'programa' => $nombre_programas
        );
        // *Retornar vista y arreglo con los datos*
        return view('vistas.perfil')->with('datos', $datos);
    }

    // *Método para cargar la vista de edicion de datos del usuario*
    public function editar($id_llegada)
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        $id = base64_decode(urldecode($id_llegada));

        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }

        $consulta = DB::table('users')->select('*')->where('id', '=', $id)->get();

        // *Condicional para determinar si el usuario cuenta con una facultad
        if ($consulta[0]->id_facultad != NULL) {
            // *Consulta para obtener el nombre de la facultad
            $facultad = DB::table('facultad')->select('facultad.nombre')->where('id', '=', $consulta[0]->id_facultad)->first();
            $facultad = $facultad->nombre;
            // *Explode para que muestre los programas por separado
            $programa = trim($consulta[0]->programa, ';');
            $programas = explode(";", $programa);
            //$programas = explode(";", $user->programa);
            // *Una vez obtenido el arreglo, se procede a obtener el nombre cada uno según su id
            //dd($programas);
            if (empty($programa)) :
                $nombre_programas = NULL;
            else :
                foreach ($programas as $key => $value) {
                    $nombres = DB::table('programas')->select('programa')->where('id', '=', $value)->get();
                    $nombre_programas[$value] = $nombres[0]->programa;
                }
            endif;
        }
        // *Si el usuario no tiene un facultad se precede a dejar vacío dicho campo
        else {
            $facultad =  $nombre_programas = NULL;
        }
        $rol = DB::table('roles')->select('roles.nombreRol')->where('id', '=', $consulta[0]->id_rol)->get();
        $roles = Roles::all();
        $facultades = DB::table('facultad')->get();
        // *Arreglo con los datos obtenudos dentro del método
        $datos = array(
            'id' => $id,
            'facultad' => $facultad,
            'rol' => $rol[0]->nombreRol,
            'programa' => $nombre_programas,
            'user' => $consulta[0]
        );
        return view('vistas.editarperfil', ['datos' => $datos, 'roles' => $roles, 'facultades' => $facultades]);
    }

    // *Función que captura la facultad y el programa del usuario
    public function getfacultadyprograma()
    {
        // *Obtenemos los datos del usuario*
        $user = auth()->user();
        // *Validación para determinar si el usuario cuenta con una facultad*
        if ($user->id_facultad != "NULL" || $user->programa != "NULL") {
            if ($user->id_facultad != "NULL" && !empty($user->id_facultad)) {
                $facultad = DB::table('facultad')->select('facultad.nombre')->where('id', '=', $user->id_facultad)->first();
                $facultad = $facultad->nombre;
            } else {
                $facultad = NULL;
            }
            $programa = trim($user->programa, ';');
            $programas = explode(";", $programa);
            //$programas = explode(";", $user->programa);
            // *Una vez obtenido el arreglo, se procede a obtener el nombre cada uno según su id
            if (empty($programa) || $programa == NULL) :
                $nombre_programas = NULL;
            else :
                foreach ($programas as $key => $value) {
                    $consulta = DB::table('programas')->select('programa')->where('id', '=', $value)->get();
                    $nombre_programas[$value] = $consulta[0]->programa;
                }
            endif;
        } else {
            $facultad =  $nombre_programas = NULL;
        }
        // *Retornar programas y facultad
        return [$nombre_programas, $facultad];
    }

    // *Función que captura el rol del usuario

    public function getrol()
    {
        // *Obtenemos los datos del usuario
        $user = auth()->user();
        // *Se obtiene el nombre del rol del usuario
        $roles = DB::table('roles')->select('roles.nombreRol')->where('id', '=', $user->id_rol)->get();
        // *Retornar nombre del rol
        return $roles;
    }

    /**
     * Metodo para obtener todos los datos de un usuario
     * @param id Id del usuario a actualizar 
     * @return usuarioActualizar Objeto con los datos del usuario
     */
    public function obtenerUsuario($id)
    {
        $usuarioActualizar = DB::table('users')->where('id', '=', $id)->select('*')->get();
        return $usuarioActualizar;
    }

    /**
     * Metodo que actualiza la tabla Log de Usuarios
     * @param id Id del usuario a actualizar 
     */
    public function registrarLog($id, $informacionOriginal, $request)
    {
        $request->merge(['id' => $id]);
        $parametros = collect($request->all())->except(['_token'])->toArray();
        $request->replace($parametros);
        LogUsuariosController::registrarLog('UPDATE', "El usuario " . $informacionOriginal[0]->nombre . " fue actualizado", 'Users',  json_encode($informacionOriginal), json_encode($request->all()));
    }

    // *Método que actualiza en la base de datos la edición del usuario
    public function actualizar($id, Request $request)
    {
        $id = decrypt($id);
        $informacionOriginal = $this->obtenerUsuario($id);
        $idUserLogueado = auth()->user()->id_rol;
        if ($idUserLogueado != '9') {

            $nombre = $request->nombre;

            if ($nombre != $informacionOriginal[0]->nombre) {
                $actualizar = DB::table('users')->where('id', $id)
                    ->update([
                        'nombre' => $nombre
                    ]);
                if ($actualizar) {
                    $this->registrarLog($id, $informacionOriginal, $request);
                    return  redirect()->route('user.perfil', ['id' => encrypt($id)])->with('success', 'Actualizacion exitosa!');
                }
            } else {
                return redirect()->route('user.perfil', ['id' => encrypt($id)])->withErrors(['errors' => 'No realizaste ningún cambio']);
            }
        } else {
            $id_banner = $request->id_banner;
            $documento = $request->documento;
            $nombre = $request->nombre;
            $email = $request->email;
            $idRol = $request->id_rol;
            $idFacultad = $request->facultades;
            $programa = $request->programa;
            $Programas = '';
            if ($idFacultad == 0) :
                $idFacultad = NULL;
            endif;
            /**se comprueba que el campo no este vacio*/
            if (isset($programa)) :
                /** Se recorre el arreglo recibido, y se añade a la variable $Programa
                 *  en cada iteracion, añadiendole el ; como separador
                 */
                foreach ($request->programa as $programa) :
                    $Programas .= $programa . ";";
                endforeach;
                /**En el campo programa se añade el contenido de la variable $Programa */

            else :
                /** Si el valor recibido es vacio se pasa al campo este valor vacio */
                $Programas = '';
            endif;
            //return $Programas;
            //return $activo;
            if (isset($request->estado)) {
                if ($request->estado != 'on') :
                    $activo = 0;
                else :
                    $activo = 1;
                endif;
            } else {
                $activo = 1;
            }

            $actualizar = DB::table('users')->where('id', $id)
                ->update([
                    'id_banner' => $id_banner,
                    'documento' => $documento,
                    'nombre' => $nombre,
                    'email' => $email,
                    'id_rol' => $idRol,
                    'id_facultad' => $idFacultad,
                    'programa' => $Programas,
                    'activo' => $activo,
                ]);

            if ($id === auth()->user()->id) :
                if ($actualizar) :
                    $this->registrarLog($id, $informacionOriginal, $request);
                    return  redirect()->route('user.perfil', ['id' => encrypt($id)])->with('success', 'Actualizacion exitosa!');
                else :
                    return redirect()->route('user.perfil', ['id' => encrypt($id)])->withErrors(['errors' => 'Error al actuaizar los datos del usuario']);
                endif;
            else :
                if ($actualizar) :
                    $this->registrarLog($id, $informacionOriginal, $request);
                    return  redirect()->route('admin.users')->with('success', 'Actualizacion exitosa!');
                else :
                    return redirect()->route('admin.users')->withErrors(['errors' => 'Error al actuaizar los datos del usuario']);
                endif;
            endif;
        }
    }

    /** Funcion para activar o inactivar usuario */
    public function inactivar_usuario()
    {
        $id = $_POST['id'];
        $informacionOriginal = DB::table('users')->where('id', '=', $id)->select('id', 'nombre', 'activo')->get();
        $inactivarUsuario = DB::table('users')->where('id', '=', $id)->update(['activo' => 0]);
        $informacionActualizada = DB::table('users')->where('id', '=', $id)->select('id', 'nombre', 'activo')->get();
        if ($inactivarUsuario) :
            LogUsuariosController::registrarLog('UPDATE', "El usuario " . $informacionActualizada[0]->nombre . " fue inactivado", 'Users', json_encode($informacionOriginal), json_encode($informacionActualizada));
            return  "deshabilitado";
        else :
            return "false";
        endif;
    }

    /** Funcion para activar o inactivar */
    public function activar_usuario()
    {
        $id = $_POST['id'];
        $informacionOriginal = DB::table('users')->where('id', '=', $id)->select('id', 'nombre', 'activo')->get();
        $activarUsuario = DB::table('users')->where('id', '=', $id)->update(['activo' => 1]);
        $informacionActualizada = DB::table('users')->where('id', '=', $id)->select('id', 'nombre', 'activo')->get();
        if ($activarUsuario) :
            LogUsuariosController::registrarLog('UPDATE', "El usuario " . $informacionActualizada[0]->nombre . " fue activado", 'Users', json_encode($informacionOriginal), json_encode($informacionActualizada));
            return  "habilitado";
        else :
            return "false";
        endif;
    }

    public function inactivar_rol()
    {
        $id = $_POST['id'];
        $informacionOriginal = DB::table('roles')->where('id', '=', $id)->select('id', 'nombreRol', 'activo')->get();
        $inactivarRol = DB::table('roles')->where('id', '=', $id)->update(['activo' => 0]);
        $informacionActualizada = DB::table('roles')->where('id', '=', $id)->select('id', 'nombreRol', 'activo')->get();
        if ($inactivarRol) :
            LogUsuariosController::registrarLog('UPDATE', "El rol " . $informacionActualizada[0]->nombreRol . " fue inactivado", 'Roles', json_encode($informacionOriginal), json_encode($informacionActualizada));
            return  "deshabilitado";
        else :
            return "false";
        endif;
    }

    public function activar_rol()
    {
        $id = $_POST['id'];
        $informacionOriginal = DB::table('roles')->where('id', '=', $id)->select('id', 'nombreRol', 'activo')->get();
        $activarRol = DB::table('roles')->where('id', '=', $id)->update(['activo' => 1]);
        $informacionActualizada = DB::table('roles')->where('id', '=', $id)->select('id', 'nombreRol', 'activo')->get();
        if ($activarRol) :
            LogUsuariosController::registrarLog('UPDATE', "El rol " . $informacionActualizada[0]->nombreRol . " fue activado", 'Roles', json_encode($informacionOriginal), json_encode($informacionActualizada));
            return  "habilitado";
        else :
            return "false";
        endif;
    }

    /** @author Ruben Charry
     * Método para obtener los datos de la tabla roles del usuario según su id
     */
    public function obtenerRol($id)
    {
        $rolActualizar = DB::table('roles')->where('id', '=', $id)->select('*')->get();
        return $rolActualizar;
    }


    public function update_rol(Request $request)
    {
        $id_llegada = $_POST['id'];
        $nombre = $_POST['nombre'];


        $id = base64_decode(urldecode($id_llegada));
        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }
        $informacionOriginal = $this->obtenerRol($id);

        $update = DB::table('roles')->where('id', '=', $id)->update(['nombreRol' => $nombre]);

        $request->merge(['id' => $id]);
        $informacionAcualizada = $request->except(['_token']);

        if ($update) :
            LogUsuariosController::registrarLog('UPDATE', "El rol " . $informacionOriginal[0]->nombreRol . " fue actualizado", 'Roles', json_encode($informacionOriginal), json_encode($informacionAcualizada));
            /** Redirecciona al formulario registro mostrando un mensaje de exito */
            return "actualizado";
        else :
            /** Redirecciona al formulario registro mostrando un mensaje de error */
            return "false";
        endif;
    }

    public function crear_rol(Request $request)
    {
        $nombre = $_POST['nombre'];

        $crear = DB::table('roles')->insert([
            'nombreRol' => $nombre,
        ]);

        $parametros = collect($request->all())->except(['_token'])->toArray();
        $request->replace($parametros);

        if ($crear) :
            LogUsuariosController::registrarLog('INSERT', "Rol creado", 'Roles', json_encode($request->all()), NULL);
            /** Redirecciona al formulario registro mostrando un mensaje de exito */
            return redirect()->route('admin.roles')->with('success', 'Rol creado correctamente');
        else :
            /** Redirecciona al formulario registro mostrando un mensaje de error */
            return redirect()->route('admin.roles')->with(['errors' => 'El rol no ha podido ser creado']);
        endif;
    }

    /**
     * Metodo que trae los programas de varias facultades
     * @param request recibe los nombres de los programas
     * @return JSON retorna los ids y nombres de programas según las facultades seleccionadas
     */
    public function traerProgramas(Request $request)
    {
      //dd("entro");die;
        $idsFacultad = $request->input('idfacultad');
      
        $programas = DB::table('programas')->whereIn('Facultad', $idsFacultad)->select('id', 'programa', 'codprograma')->get();

        return $programas;
    }

    public function traerProgramasUsuarios(Request $request)
    {
        $codFacultad = $request->input('codfacultad');
        $nombreFacultad = DB::table('facultad')
            ->whereIn('id', $codFacultad)
            ->pluck('nombre')
            ->toArray();

        $programas = DB::table('programas')->whereIn('Facultad', $nombreFacultad)->select('id', 'programa', 'codprograma')->get();

        header("Content-Type: application/json");
        echo json_encode($programas);
    }

    public function solicitudesSistema()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        return view('vistas.admin.solicitudes');
    }

    public function traerSolicitudes()
    {
        $solicitudes = DB::table('solicitudes_sistema as s')
            ->join('users as u', 's.usuario', '=', 'u.id')
            ->join('roles as r', 'r.id', '=', 'u.id_rol')
            ->where('pendiente', 1)->get();

        return $solicitudes;
    }

    public function resolverSoliciud()
    {
        $id = $_POST['id'];

        $informacionOriginal = DB::table('solicitudes_sistema')->where('id', $id)->first();

        $resolver = DB::table('solicitudes_sistema')->where('id', '=', $id)->update(['pendiente' => 0]);

        if ($resolver) {
            LogUsuariosController::registrarLog('UPDATE', "La solicitud de id " . $id . " fue atendida", 'Roles', json_encode($informacionOriginal), NULL);

            // Enviar correo electronico

            $destinatario = DB::table('users')->where('id', $informacionOriginal->usuario)->select('email')->first();
            $asunto = "Solicitud resuelta";
            $mensaje = "Tu solicitud en Hermes" . $informacionOriginal->solicitud . " ha sido atendida por nuestro equipo de desarrolladores";

            $mail = mail($destinatario->email, $asunto, $mensaje);

            return "resuelto";
        }
    }

    public function verificarPendientes()
    {

        $pendientes = DB::table('solicitudes_sistema')->selectRaw('COUNT(id) as total')->where('pendiente', 1)->get();

        if ($pendientes[0]->total > 0) {
            return $pendientes[0]->total;
        } else {
            return null;
        }
    }

    public function buscarProgramas()
    {

        $programas = DB::table('programas')->select('codprograma', 'programa', 'Facultad')->get();

        return $programas;
    }


    public function asteroides()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        return view('vistas.pausaActiva.asteroide');
    }

    public function renovarPassword()
    {
        $idUsuario = $_POST['id'];

        $documento = DB::table('users')->select('documento')->where('id', $idUsuario)->first();

        $cambioPass = User::where('id', '=', $idUsuario)
            ->update(
                [
                    'password' => bcrypt($documento->documento),
                    'ingreso_plataforma' => 0
                ]
            );

        if ($cambioPass) {
            return 'Exito';
        } else {
            return false;
        }
    }
}
