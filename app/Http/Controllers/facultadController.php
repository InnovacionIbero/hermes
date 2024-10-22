<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CambioPassRequest;
use App\Http\Requests\UsuarioLoginRequest;
use App\Http\Requests\CrearFacultadRequest;
use App\Http\Requests\ProgramasRequest;
use App\Models\Facultad;
use App\Models\Roles;
use App\Models\User;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
// use Yajra\DataTables\DataTables;
use App\Http\Util\Constantes;
use DateTime;
use App\Http\Controllers\LogUsuariosController;

/**
 * Controlador de facultades
 */
class facultadController extends Controller
{
    /** 
     * Función para cargar la vista de los programas 
     * @return view de los programas de pregrado
     * */
    public function view_programas()
    {
        $menu= session('menu');
        //dd($menu);die;
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        return view('vistas.admin.programas');
    }

    /** 
     * Función para cargar la vista de las especializaciones
     * @return view de los programas de especialización
     * */
    public function view_especializacion()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        return view('vistas.admin.especializacion');
    }

    /** 
     * Función para cargar la vista de las maestrías
     * @return view de los programas de maestría
     * */
    public function view_maestria()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        return view('vistas.admin.maestria');
    }

    /** 
     * Función para cargar la vista de educación continua
     * @return view de los programas de educación continua
     * */
    public function view_continua()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        return view('vistas.admin.educacioncontinua');
    }

    /** 
     * Función para cargar la vista de los periodos
     * @return view de los periodos de inscripción la Universidad
     * */
    public function view_periodos()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        return view('vistas.admin.periodos');
    }

    /** 
     * Función para cargar las reglas de negocio
     * Estas son las condiciones para inscribir materias en cada uno de los programas
     * según varios criterios como la cantidad de créditos, cantidad de materias
     * @return view de las reglas negocio
     * */
    public function view_reglas()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        return view('vistas.admin.reglasnegocio');
    }

    public function view_planeacion()
    {
        /* $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        return view('vistas.admin.planeacion'); */
        $fechaHoraActual = date("Y-m-d H:m:s");
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        $periodos = DB::table('periodo')->where('activoCiclo1', 1)->select('periodos','fechaInicioCiclo1','fechaCierreCiclo1','fechaProgramacionPrimerCiclo')->get();
        foreach ($periodos as $key => $periodo) {
            if ($key == 0 || $key == 5):
                $fechaInicioCiclo1 = $periodo->fechaInicioCiclo1;
                $fechaInicioProyeccion = $periodo->fechaProgramacionPrimerCiclo;
            endif;
        }
        $fechaInicioProyeccion = $fechaInicioProyeccion . ' 00:00:00';
        $fechaInicioProyeccion = date('Y-m-d 00:00:00', strtotime($fechaInicioProyeccion . "-5 day"));
        $fechaCierreProyeccion = date("Y-m-d 23:59:59", strtotime($fechaInicioCiclo1 . "- 10 day"));
        $fechaInicioProgramacion = date("Y-m-d 00:59:59", strtotime($fechaInicioCiclo1 . "- 8 day"));
        $fechaCierreProgramacion = date("Y-m-d 23:59:59",strtotime($fechaInicioCiclo1."+ 23 day"));
        if ($fechaHoraActual >= $fechaInicioProyeccion && $fechaHoraActual <= $fechaCierreProyeccion) {
            $fechaInicio = date('Y-m-d', strtotime($fechaInicioProyeccion . "+1 day"));
        } elseif($fechaHoraActual >= $fechaInicioProgramacion && $fechaHoraActual <= $fechaCierreProgramacion) {
            $fechaInicio = date('Y-m-d', strtotime($fechaInicioProgramacion . "+1 day"));
        }
        return view('vistas.admin.planeacion', ['fechaInicio' => $fechaInicio]);
    }

    /** 
     * Función para obtener todos los programas de pregrado
     * Esta función hace una consulta a la base de datos para traer los datos de los programas
     * de pregrado en un arreglo y lo convierte a formato json para mostrarlo en la vista
     * @return json(array())
     */
    public function get_programas()
    {
        $programas = DB::table('programas')->where('nivelFormacion', '=', 'PROFESIONAL')->get();
        header("Content-Type: application/json");
        echo json_encode(array('data' => $programas));
    }

    /** 
     * Función para obtener todos los programas de especialización
     * Esta función hace una consulta a la base de datos para traer los datos de los programas
     * de especialización en un arreglo y lo convierte a formato json para mostrarlo en la vista
     * @return json(array())
     */
    public function get_especializacion()
    {
        $especializacion = DB::table('programas')->where('nivelFormacion', '=', 'ESPECIALISTA')->get();
        header("Content-Type: application/json");
        echo json_encode(array('data' => $especializacion));
    }

    /** 
     * Función para obtener todos los programas de maestría
     * Esta función hace una consulta a la base de datos para traer los datos de los programas
     * de maestría en un arreglo y lo convierte a formato json para mostrarlo en la vista
     * @return json(array())
     */
    public function get_maestria()
    {
        $programas = DB::table('programas')->where('nivelFormacion', '=', 'MAESTRIA')->get();
        header("Content-Type: application/json");
        echo json_encode(array('data' => $programas));
    }

    /** 
     * Función para obtener todos los programas de maestría
     * Esta función hace una consulta a la base de datos para traer los datos de los programas
     * de maestría en un arreglo y lo convierte a formato json para mostrarlo en la vista
     * @return json(array())
     */
    public function get_continua()
    {
        $programas = DB::table('programas')->where('nivelFormacion', '=', 'EDUCACION CONTINUA')->get();
        header("Content-Type: application/json");
        echo json_encode(array('data' => $programas));
    }

    /** 
     * Función para obtener todos los periodos de inscripción
     * Esta función hace una consulta a la base de datos para traer los datos de los periodos
     * en un arreglo y lo convierte a formato json para mostrarlo en la vista
     * @return json(array())
     */
    public function get_periodos()
    {
        $periodos = DB::table('periodo')->get();
        header("Content-Type: application/json");
        echo json_encode(array('data' => $periodos));
    }

    /** 
     * Función para obtener todas las reglas de negocio
     * Esta función hace una consulta a la base de datos para traer los datos de las reglas de negocio
     *  y lo convierte a formato json para mostrarlo en la vista
     * @return json(array())
     */
    public function get_reglas()
    {
        $reglas = DB::table('reglasNegocio as r')
            ->join('programas as p', 'p.codprograma', '=', 'r.programa')
            ->select(
                'r.id',
                'p.codprograma',
                'r.creditos',
                'r.materiasPermitidas',
                'r.ciclo',
                'r.activo',
                'p.programa',
                'p.nivelFormacion',
                'p.Facultad'
            )
            ->get();
        header("Content-Type: application/json");
        echo json_encode(array('data' => $reglas));
    }

    /** 
     * Función para obtener los datos de la tabla planeación
     * Esta función hace una consulta a la base de datos para traer los datos de la tabla de planeación
     * y lo convierte a formato json para mostrarlo en la vista
     * @return json(array())
     */
    /* public function get_planeacion()
    {

        $fechaActual = date("d-m-Y");

        $fecha = DB::table('periodo')->where('activoCiclo1', 1)->select('fechaInicioCiclo1')->first();

        if(empty($fecha) || !isset($fecha)){
            $consultaFecha = DB::table('periodo')->where('periodos','LIKE', '%17%')
            ->select('fechaCierreCiclo1')->first();

            $fechaFormateada = $consultaFecha->fechaCierreCiclo1;

        }else{
            $fechaFormateada = $fecha->fechaInicioCiclo1;
        } 

        $fechaFormateada = new DateTime($fechaFormateada);

        $fechaFormateada->modify('-1 week');

        $fechaFormateada = $fechaFormateada->format('d-m-Y');

        $fechaActual = DateTime::createFromFormat('d-m-Y', $fechaActual);
        $fechaFormateada = DateTime::createFromFormat('d-m-Y', $fechaFormateada);
        //dd($fechaActual <  $fechaFormateada);die;
        if ($fechaActual <  $fechaFormateada) {

            $tablaConsulta = 'planeacion';
        } else {
            $tablaConsulta = 'programacion';
        }
      
        $planeacion = DB::table($tablaConsulta)
        ->select('codBanner','codprograma','codMateria' ,'curso', 'operador','fecha_registro','periodo')
        ->get();
        
        header("Content-Type: application/json");
        echo json_encode(array('data' => $planeacion));
    } */
    public function get_planeacion()
    {
        //dd($_POST);
        $fechaHoraActual = date("Y-m-d H:m:s");
        //$fechaHoraActual = date("2024-10-07 06:00:01");

        $periodos = DB::table('periodo')->where('activoCiclo1', 1)->select('periodos','fechaInicioCiclo1','fechaCierreCiclo1','fechaProgramacionPrimerCiclo')->get();
        
        $marcaIngreso = "";
        foreach ($periodos as $key => $periodo) {
            $marcaIngreso .= (int)$periodo->periodos . ",";
            $codPeriodo = substr($periodo->periodos, -2);
            if ($key == 0 || $key == 5):
                $fechaInicioCiclo1 = $periodo->fechaInicioCiclo1;
                $fechaInicioProyeccion = $periodo->fechaProgramacionPrimerCiclo;
            endif;
        }
        $fechaInicioProyeccion = $fechaInicioProyeccion . ' 00:00:00';
        $fechaInicioProyeccion = date('Y-m-d 00:00:00', strtotime($fechaInicioProyeccion . "-5 day"));
        $fechaCierreProyeccion = date("Y-m-d 23:59:59", strtotime($fechaInicioCiclo1 . "- 10 day"));
        $fechaInicioProgramacion = date("Y-m-d 00:59:59", strtotime($fechaInicioCiclo1 . "- 8 day"));
        $fechaCierreProgramacion = date("Y-m-d 23:59:59",strtotime($fechaInicioCiclo1."+ 23 day"));
        
        if ($fechaHoraActual >= $fechaInicioProyeccion && $fechaHoraActual <= $fechaCierreProyeccion) {
            $tablaConsulta = 'planeacion';
            $incioInformeProyeccionDiaUno = date("Y-m-d 12:24:59", strtotime($fechaInicioProyeccion . "+ 1 day"));
            $cierreInformeProyeccionDiaUno = date("Y-m-d 12:00:00", strtotime($fechaInicioProyeccion . "+ 1 day"));
            $cierreInformeProyeccion = date("Y-m-d 12:00:00", strtotime($fechaHoraActual));
            $incioInformeProyeccion = $fechaHoraActual < $incioInformeProyeccionDiaUno ? $cierreInformeProyeccionDiaUno : date("Y-m-d 12:00:00", strtotime($fechaHoraActual . "- 1 day"));
        } elseif($fechaHoraActual >= $fechaInicioProgramacion && $fechaHoraActual <= $fechaCierreProgramacion) {
            $tablaConsulta = 'programacion';
            $incioInformeProyeccionDiaUno = date("Y-m-d 06:24:59", strtotime($fechaInicioProgramacion . "+ 1 day"));
            $cierreInformeProyeccionDiaUno = date("Y-m-d 12:00:00", strtotime($fechaInicioProgramacion . "+ 1 day"));
            $cierreInformeProyeccion = date("Y-m-d 12:00:00", strtotime($fechaHoraActual));
            $incioInformeProyeccion = $fechaHoraActual < $incioInformeProyeccionDiaUno ? $cierreInformeProyeccionDiaUno : date("Y-m-d 12:00:00", strtotime($fechaHoraActual . "- 1 day"));
        }
        

        $horaActual = date('H:i:s');
        if($horaActual < '12:00:00'){
            $fechaHoraActual = date('Y-m-d H:i:s', strtotime($fechaHoraActual . " -1 day"));
        }
        //dd($fechaHoraActual,$incioInformeProyeccionDiaUno,$cierreInformeProyeccionDiaUno,$incioInformeProyeccion,$cierreInformeProyeccion);
                
        if ($_POST['estado'] == 'Valida') {
            //dd($fechaHoraActual < $cierreInformeProyeccionDiaUno);
            if ($fechaHoraActual < $cierreInformeProyeccionDiaUno) {
                $planeacion = DB::table($tablaConsulta)
                ->select('codBanner','codprograma','codMateria','fecha_registro','periodo','validacion','fecha_ultimo_update')
                ->where('validacion','=','Valida')
                ->where([['fecha_registro','>=',$incioInformeProyeccionDiaUno],['fecha_registro','<',$cierreInformeProyeccionDiaUno]])
                ->orderBy('id','asc')
                ->get();
            } else {
                $planeacion = DB::table($tablaConsulta)
                ->select('codBanner','codprograma','codMateria','fecha_registro','periodo','validacion','fecha_ultimo_update')
                ->where('validacion','=','Valida')
                ->where([['fecha_registro','>=',$incioInformeProyeccion],['fecha_registro','<',$cierreInformeProyeccion]])
                ->where('fecha_ultimo_update','=','0000-00-00 00:00:00')
                ->orderBy('id','asc')
                ->get();
            }
        }else {
            if ($fechaHoraActual < $cierreInformeProyeccionDiaUno) {
                $planeacion = DB::table($tablaConsulta)
                ->select('codBanner','codprograma','codMateria','fecha_registro','periodo','validacion','fecha_ultimo_update')
                ->where('validacion','=','No Valida')
                ->where([['fecha_ultimo_update','>=',$incioInformeProyeccionDiaUno],['fecha_ultimo_update','<',$cierreInformeProyeccionDiaUno]])
                ->orderBy('id','asc')
                ->get();
            } else {
                
                $planeacion = DB::table($tablaConsulta)
                ->select('codBanner','codprograma','codMateria','fecha_registro','periodo','validacion','fecha_ultimo_update')
                ->where('validacion','=','No Valida')
                ->where([['fecha_ultimo_update','>=',$incioInformeProyeccion],['fecha_ultimo_update','<',$cierreInformeProyeccion]])
                ->orderBy('id','asc')
                ->get();
            }
        }
        header("Content-Type: application/json");
        echo json_encode(array('data' => $planeacion));
    }

    /** 
     * Función para obtener los datos de una facultad según su id y retornar a la vista de
     * administración de facultades
     * @param Request $request id de la facultad
     * @return view(vista.admin) $Vista de administración de facultades
     * $[request->id] id de facultad 
     * $[nombre[0]->nombre] nombre de la facultad
     * */
    public function facultad(Request $request)
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        $nombre = DB::table('facultad')->select('nombre')->where('id', '=', decrypt($request->id))->get();
        return view('vistas.admin.facultad', ['id' => $request->id], ['nombre' => $nombre[0]->nombre]);
    }

    /** 
     * Función para obtener el nombre de un programa a partir de su código y retornar a la vista de malla curricular
     * de cada programa
     * @param codigo $codigo del programa
     * @return view(vista.malla) $Vista de administración de malla curricular 
     * $[request->id] id de facultad 
     * $[nombre[0]->nombre] nombre de la facultad
     * */
    public function malla($codigo)
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        $nombre = DB::table('programas')->select('programa')->where('codprograma', '=', $codigo)->get();

        return view('vistas.admin.malla', ['codigo' => $codigo], ['nombre' => $nombre[0]->programa]);
    }

    public function mostrarmallacurricular($id)
    {
        $codigo = DB::table('programas')->where('id', '=', $id)->select('codprograma')->get();
        // Consulta para obtener la malla curricular del programa
        $malla = DB::table('mallaCurricular')->where('codprograma', '=', $codigo[0]->codprograma)->get();
        /**mostrar los datos en formato JSON */
        header("Content-Type: application/json");
        /**Se pasa a formato JSON el arreglo de users */
        echo json_encode(array('data' => $malla));
    }

    public function getDatosPrograma($codigo)
    {
        $datos = DB::table('programas')->where('codprograma', '=', $codigo)->select('tabla', 'id', 'programa', 'activo')->get();
        return $datos;
    }


    /* Método para inactivar programa */
    public function inactivar_programa()
    {
        $cod_llegada = $_POST['codigo'];
        $informacionOriginal = $this->getDatosPrograma($cod_llegada);
        $inactivarPrograma = DB::table('programas')->where('codprograma', '=', $cod_llegada)->update(['activo' => 0]);
        $informacionActualizada = $this->getDatosPrograma($cod_llegada);

        if ($inactivarPrograma) :
            $this->updateLogUsuarios("El programa " . $informacionOriginal[0]->programa . " fue desactivado", "programa", $informacionOriginal, $informacionActualizada);
            return  "deshabilitado";
        else :
            return "false";
        endif;
    }

    public function activar_programa()
    {
        $cod_llegada = $_POST['codigo'];
        $informacionOriginal = $this->getDatosPrograma($cod_llegada);
        $activarPrograma = DB::table('programas')->where('codprograma', '=', $cod_llegada)->update(['activo' => 1]);
        $informacionActualizada = $this->getDatosPrograma($cod_llegada);

        $datos = $this->getDatosPrograma($cod_llegada);
        if ($activarPrograma) :
            $this->updateLogUsuarios("El programa " . $informacionOriginal[0]->programa . " fue activado", "programa", $informacionOriginal, $informacionActualizada);

            return  "habilitado";
        else :
            return "false";
        endif;
    }

    public function crear_programa(Request $request)
    {
        // Recibe los parámetros del formulario por Post
        //dd($request);die();
        $codigo = $_POST['codPrograma'];
        $nombre = $_POST['nombre'];
        $codFacultad = $_POST['codFacultad'];
        $nivelFormacion = $_POST['nivelformacion'];
        $nivelFormacion = $_POST['nivelformacion'];
        $opcionGrado = $_POST['opciongrado']??null;

        $consultaFacultad = DB::table('facultad')->where('id','=',$codFacultad)->select('*')->first();
        $facultad = $consultaFacultad->nombre;
        //dd(is_null($opcionGrado));
        if (is_null($opcionGrado)) :
            // Consulta para insertar nuevo programa
            $crear = DB::table('programas')->insert([
                'codprograma' => $codigo,
                'programa' => $nombre,
                'nivelFormacion' => $nivelFormacion,
                'Facultad' => $facultad,
                'estado' => 1,
            ]);
        else :
            $crear = DB::table('programas')->insert([
                'codprograma' => $codigo,
                'programa' => $nombre,
                'nivelFormacion' => $nivelFormacion,
                'Facultad' => $facultad,
                'estado' => 1,
                'opcionGrado' => $opcionGrado,
            ]);
        endif;
        $request->merge(['tabla' => 'pregrado']);
        $informacionOriginal = $request->except(['_token']);

        switch ($nivelFormacion) {
            case 'PROFESIONAL':
                if ($crear) :
                    $this->insertLogUsuarios("Programa creado", "programa", $informacionOriginal);
                    /** Redirecciona al formulario registro mostrando un mensaje de exito */
                    return redirect()->route('facultad.programas')->with('sucess', 'Programa creado correctamente');
                else :
                    /** Redirecciona al formulario registro mostrando un mensaje de error */
                    return redirect()->route('facultad.programas')->with(['errors' => 'El programa no ha podido ser creado']);
                endif;
                break;
            case 'ESPECIALISTA':
                if ($crear) :
                    $this->insertLogUsuarios("Programa creado", "programa", $informacionOriginal);
                    /** Redirecciona al formulario registro mostrando un mensaje de exito */
                    return redirect()->route('facultad.especializacion')->with('sucess', 'Especialización creada correctamente');
                else :
                    /** Redirecciona al formulario registro mostrando un mensaje de error */
                    return redirect()->route('facultad.especializacion')->with(['errors' => 'La especialización no ha podido ser creada']);
                endif;
                break;
            case 'MAESTRIA':
                if ($crear) :
                    $this->insertLogUsuarios("Programa creado", "programa", $informacionOriginal);
                    /** Redirecciona al formulario registro mostrando un mensaje de exito */
                    return redirect()->route('facultad.maestria')->with('sucess', 'Maestria creada correctamente');
                else :
                    /** Redirecciona al formulario registro mostrando un mensaje de error */
                    return redirect()->route('facultad.maestria')->with(['errors' => 'La maestria no ha podido ser creada']);
                endif;
                break;
            case 'EDUCACION CONTINUA':
                if ($crear) :
                    $this->insertLogUsuarios("Programa creado", "programa", $informacionOriginal);
                    /** Redirecciona al formulario registro mostrando un mensaje de exito */
                    return redirect()->route('facultad.continua')->with('sucess', 'Programa creado correctamente');
                else :
                    /** Redirecciona al formulario registro mostrando un mensaje de error */
                    return redirect()->route('facultad.continua')->with(['errors' => 'El programa no ha podido ser creado']);
                endif;
                break;
            
            default:
                # code...
                break;
        }
        
    }

    public function crear_esp(Request $request)
    {
        $codigo = $_POST['codEsp'];
        $nombre = $_POST['nombre'];
        $codFacultad = $_POST['idFacultad'];

        // Consulta para insertar nueva especialización
        $crear = DB::table('programas')->insert([
            'codprograma' => $codigo,
            'programa' => $nombre,
            'idFacultad' => $codFacultad,
            'tabla' => 'especializacion',
        ]);
        $request->merge(['tabla' => 'especializacion']);
        $informacionOriginal = $request->except(['_token']);
        if ($crear) :
            $this->insertLogUsuarios("Especialización creada", "programa", $informacionOriginal);
            /** Redirecciona al formulario registro mostrando un mensaje de exito */
            return redirect()->route('facultad.especializacion')->with('sucess', 'Especialización creada correctamente');
        else :
            /** Redirecciona al formulario registro mostrando un mensaje de error */
            return redirect()->route('facultad.especializacion')->with(['errors' => 'La especialización no ha podido ser creada']);
        endif;
    }

    public function crear_maestria(Request $request)
    {
        $codigo = $_POST['codMaestria'];
        $nombre = $_POST['nombre'];
        $codFacultad = $_POST['idFacultad'];
        // Consulta para insertar nueva especialización
        $crear = DB::table('programas')->insert([
            'codprograma' => $codigo,
            'programa' => $nombre,
            'idFacultad' => $codFacultad,
            'tabla' => 'MAESTRIA',
        ]);
        $request->merge(['tabla' => 'maestria']);
        $informacionOriginal = $request->except(['_token']);
        if ($crear) :
            $this->insertLogUsuarios("Maestría creada", "programa", $informacionOriginal);
            /** Redirecciona al formulario registro mostrando un mensaje de exito */
            return redirect()->route('facultad.maestria')->with('success', 'Maestria creada correctamente');
        else :
            /** Redirecciona al formulario registro mostrando un mensaje de error */
            return redirect()->route('facultad.maestria')->with(['errors' => 'La maestria no ha podido ser creada']);
        endif;
    }

    /** Metodo para crear programa de educacion continua */
    public function crear_edudacioncont(Request $request)
    {
        $codigo = $_POST['codigo'];
        $nombre = $_POST['nombre'];
        $codFacultad = $_POST['codFacultad'];
        // Consulta para insertar nueva especialización
        $crear = DB::table('programas')->insert([
            'codprograma' => $codigo,
            'programa' => $nombre,
            'idFacultad' => $codFacultad,
            'tabla' => 'EDUCACION CONTINUA',
        ]);
        $request->merge(['tabla' => 'educacion continua']);
        $informacionOriginal = $request->except(['_token']);
        if ($crear) :
            $this->insertLogUsuarios("Programa de educación continua creado", "programa", $informacionOriginal);
            /** Redirecciona al formulario registro mostrando un mensaje de exito */
            return redirect()->route('facultad.continua')->with('success', 'Programa creado correctamente');
        else :
            /** Redirecciona al formulario registro mostrando un mensaje de error */
            return redirect()->route('facultad.continua')->with(['errors' => 'El programa no ha podido ser creado']);
        endif;
    }

    /** Función que actualiza los datos de programa */
    public function update_programa(Request $request)
    {
        $id_llegada = $_POST['id'];
        $codigo = $_POST['codigo'];
        $nombre = $_POST['programa'];
        $idfacultad = $_POST['idfacultad'];

        $id = base64_decode(urldecode($id_llegada));
        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }

        $informacionOriginal = DB::table('programas')->where('id', '=', $id)->get();

        $update = DB::table('programas')->where('id', '=', $id)->update(['codprograma' => $codigo, 'programa' => $nombre, 'idFacultad' => $idfacultad]);

        $request->merge(['id' => $id]);
        $informacionActualizada = $request->except(['_token']);

        if ($update) :
            $this->updateLogUsuarios("El programa " . $informacionOriginal[0]->programa . " fue actualizado", "programa", $informacionOriginal, $informacionActualizada);
            /** Redirecciona al formulario registro mostrando un mensaje de exito */
            return "actualizado";
        else :
            /** Redirecciona al formulario registro mostrando un mensaje de error */
            return "false";
        endif;
    }

    /** 
     * Función para visualizar la vista de los programas del usuario 
     * */
    public function programasUsuario($nombre)
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        return view('vistas.admin.facultades', ['nombre' => $nombre]);
    }

    /**
     * Función para visualizar los estudiantes de cada facultad 
     * */
    public function estudiantesFacultad($id)
    {
        $consulta = DB::table('programas')->where('id', '=', $id)->get();
        $codigo = $consulta[0]->codprograma;
        $estudiantes = DB::table('estudiantes')->where('programa', '=', $codigo)->get();
        header("Content-Type: application/json");
        echo json_encode(array('data' => $estudiantes));
    }

    public function planeacionPrograma($id)
    {
        $consulta = DB::table('programas')->where('id', '=', $id)->get();
        $codigo = $consulta[0]->codprograma;
        $planeacion = DB::table('planeacion')->where('codprograma', '=', $codigo)->get();
        /**mostrar los datos en formato JSON */
        header("Content-Type: application/json");
        /**Se pasa a formato JSON el arreglo de users */
        echo json_encode(array('data' => $planeacion));
    }

    /** 
     * Método para obtener los datos de la tabla facultad del usuario según su id
     */
    public function obtenerFacultad($id)
    {
        $facultadActualizar = DB::table('facultad')->where('id', '=', $id)->select('*')->get();
        return $facultadActualizar;
    }

    public function savefacultad(Request $request)
    {
        /** Consulta para insertar los datos obtenidos en el Request a la base de datos de facultad */
        $facultad = DB::table('facultad')->insert([
            'codFacultad' => $_POST['codFacultad'],
            'nombre' => $_POST['nombre'],
        ]);
        $informacionOriginal = $request->except(['_token']);
        if ($facultad) :
            $this->insertLogUsuarios("Facultad creada", 'facultad', $informacionOriginal);
            /** Redirecciona al formulario registro mostrando un mensaje de exito */
            return redirect()->route('admin.facultades')->with('success', 'Facultad creada correctamente');
        else :
            /** Redirecciona al formulario registro mostrando un mensaje de error */
            return redirect()->route('admin.facultades')->withErrors(['errors' => 'La facultad no se ha podido crear']);
        endif;
    }

    public function updatefacultad(Request $request)
    {
        $id_llegada = $_POST['id'];
        $codFacultad = $_POST['codFacultad'];
        $nombre = $_POST['nombre'];
        $id = base64_decode(urldecode($id_llegada));
        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }
        $informacionOriginal = $this->obtenerFacultad($id);
        /** Consulta para actualizar facultad */
        $facultad = DB::table('facultad')
            ->where('id', $id)
            ->update([
                'codFacultad' => $codFacultad,
                'nombre' => $nombre
            ]);
        $request->merge(['id' => $id]);
        $informacionActualizada = $request->except(['_token']);
        if ($facultad) :
            $this->updateLogUsuarios("La facultad " . $informacionOriginal[0]->nombre . " fue actualizada", 'facultad', $informacionOriginal, $informacionActualizada);
            /** Redirecciona al formulario registro mostrando un mensaje de exito */
            return "actualizado";
        else :
            /** Redirecciona al formulario registro mostrando un mensaje de error */
            return "false";
        endif;
    }

    /** Metodo para inactivar facultad */
    public function inactivar_facultad()
    {
        $id = $_POST['id'];
        $informacionOriginal = DB::table('facultad')->where('id', '=', $id)->select('nombre', 'id', 'activo')->get();
        $inactivarFacultad = DB::table('facultad')->where('id', '=', $id)->update(['activo' => 0]);
        $informacionActualizada = DB::table('facultad')->where('id', '=', $id)->select('nombre', 'id', 'activo')->get();
        if ($inactivarFacultad) :
            $this->updateLogUsuarios("La facultad " . $informacionOriginal[0]->nombre . " fue desactivada", 'facultad', $informacionOriginal, $informacionActualizada);
            return  "deshabilitado";
        else :
            return "false";
        endif;
    }

    /** Metodo para activar facultad */
    public function activar_facultad()
    {
        $id = $_POST['id'];
        $informacionOriginal = DB::table('facultad')->where('id', '=', $id)->select('nombre', 'id', 'activo')->get();
        $activarPrograma = DB::table('facultad')->where('id', '=', $id)->update(['activo' => 1]);
        $informacionActualizada = DB::table('facultad')->where('id', '=', $id)->select('nombre', 'id', 'activo')->get();
        if ($activarPrograma) :
            $this->updateLogUsuarios("La facultad " . $informacionOriginal[0]->nombre . " fue activada", 'facultad', $informacionOriginal, $informacionActualizada);
            return  "habilitado";
        else :
            return "false";
        endif;
    }

    public function obtenerPeriodo($id)
    {
        $periodoActualizar = DB::table('periodo')->where('id', '=', $id)->select('*')->get();
        return $periodoActualizar;
    }

    public function crear_periodo(Request $request)
    {
        $nombre = $_POST['name'];
        $fecha1 = $_POST['ciclo1'];
        $fecha2 = $_POST['ciclo2'];
        $fechaFin = $_POST['ciclo1Fin'];
        $fecha2Fin = $_POST['ciclo2Fin'];
        $ciclo1Proyeccion = $_POST['ciclo1Proyeccion'];
        $ciclo2Proyeccion = $_POST['ciclo2Proyeccion'];
        $periodo = $_POST['periodo'];
        $año = $_POST['fecha'];

        $crear = DB::table('periodo')->insert([
            'periodos' => $nombre,
            'fechaInicioCiclo1' => $fecha1,
            'fechaInicioCiclo2' => $fecha2,
            'fechaCierreCiclo1' => $fechaFin,
            'fechaCierreCiclo2' => $fecha2Fin,
            'fechaProgramacionPrimerCiclo' => $ciclo1Proyeccion,
            'fechaProgramacionSegundoCiclo' => $ciclo2Proyeccion,
            'fechaInicioPeriodo' => $periodo,
            'activoCiclo1' => 0,
            'activoCiclo2' => 0,
            'periodoActivo' => 0,
            'year' => $año,
        ]);
        $informacionOriginal = $request->except(['_token']);
        if ($crear) :
            $this->insertLogUsuarios("Periodo creado", 'periodo', $informacionOriginal);
            /** Redirecciona al formulario registro mostrando un mensaje de exito */
            return redirect()->route('facultad.periodos')->with('success', 'Periodo creado correctamente');
        else :
            /** Redirecciona al formulario registro mostrando un mensaje de error */
            return redirect()->route('facultad.periodos')->with(['errors' => 'El periodo no ha podido ser creado']);
        endif;
    }

    /** Metodo para actualizar los datos de periodo */
    public function updateperiodo(Request $request)
    {
        //dd($request);die();
        $id_llegada = $_POST['id'];
        $nombre = $_POST['nombre'];
        $fecha1 = $_POST['ciclo1'];
        $fecha2 = $_POST['ciclo2'];
        $fechaFin = $_POST['ciclo1Fin'];
        $fecha2Fin = $_POST['ciclo2Fin'];
        $ciclo1Proyeccion = $_POST['ciclo1Proyeccion'];
        $ciclo2Proyeccion = $_POST['ciclo2Proyeccion'];
        $periodo = $_POST['periodo'];
        $año = $_POST['año'];

        $id = base64_decode(urldecode($id_llegada));
        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }
        //dd(empty($ciclo1Proyeccion));die();
        /** Consulta para actualizar facultad */
        $informacionOriginal = $this->obtenerPeriodo($id);
        if(empty($ciclo1Proyeccion)){
            $periodo = DB::table('periodo')
                ->where('id', $id)
                ->update([
                    'periodos' => $nombre,
                    'fechaInicioCiclo1' => $fecha1,
                    'fechaInicioCiclo2' => $fecha2,
                    'fechaCierreCiclo1' => $fechaFin,
                    'fechaCierreCiclo2' => $fecha2Fin,
                    'fechaProgramacionSegundoCiclo' => $ciclo2Proyeccion,
                    'fechaInicioPeriodo' => $periodo,
                    'year' => $año,
                ]);
        } else {
            $periodo = DB::table('periodo')
                ->where('id', $id)
                ->update([
                    'periodos' => $nombre,
                    'fechaInicioCiclo1' => $fecha1,
                    'fechaInicioCiclo2' => $fecha2,
                    'fechaCierreCiclo1' => $fechaFin,
                    'fechaCierreCiclo2' => $fecha2Fin,
                    'fechaProgramacionPrimerCiclo' => $ciclo1Proyeccion,
                    'fechaProgramacionSegundoCiclo' => $ciclo2Proyeccion,
                    'fechaInicioPeriodo' => $periodo,
                    'year' => $año,
                ]);
        }
        $request->merge(['id' => $id]);
        $informacionActualizada = $request->except(['_token']);
        if ($periodo) :
            /** Redirecciona al formulario registro mostrando un mensaje de exito */
            $this->updateLogUsuarios("El periodo " . $informacionOriginal[0]->periodos . " fue actualizado ", 'periodo', $informacionOriginal, $informacionActualizada);
            return "actualizado";
        else :
            /** Redirecciona al formulario registro mostrando un mensaje de error */
            return "false";
        endif;
    }

    /** Función para activar los periodos */
    public function activar_periodo()
    {
        $id_llegada = $_POST['id'];
        $id = base64_decode(urldecode($id_llegada));
        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }
        $informacionOriginal = DB::table('periodo')->where('id', '=', $id)->select('periodos', 'id', 'periodoActivo')->get();
        $activarPeriodo = DB::table('periodo')->where('id', '=', $id)->update(['periodoActivo' => 1]);
        $informacionActualizada = DB::table('periodo')->where('id', '=', $id)->select('periodos', 'id', 'periodoActivo')->get();
        if ($activarPeriodo) :
            $this->updateLogUsuarios("El periodo " . $informacionOriginal[0]->periodos . " fue activado ", 'periodo', $informacionOriginal, $informacionActualizada);
            return  "habilitado";
        else :
            return "false";
        endif;
    }

    /** Función para desactivar los periodos */
    public function inactivar_periodo()
    {
        $id_llegada = $_POST['id'];
        $id = base64_decode(urldecode($id_llegada));
        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }
        $informacionOriginal = DB::table('periodo')->where('id', '=', $id)->select('periodos', 'id', 'periodoActivo')->get();
        $inactivarPeriodo = DB::table('periodo')->where('id', '=', $id)->update(['periodoActivo' => 0]);
        $informacionActualizada = DB::table('periodo')->where('id', '=', $id)->select('periodos', 'id', 'periodoActivo')->get();
        if ($inactivarPeriodo) :
            $this->updateLogUsuarios("El periodo " . $informacionOriginal[0]->periodos . " fue inactivado ", 'periodo', $informacionOriginal, $informacionActualizada);
            return  "deshabilitado";
        else :
            return "false";
        endif;
    }


    /** Método para obtener regla de negocio según si id
     * 
     */
    public function obtenerRegla($id)
    {
        $reglaActualizar = DB::table('reglasNegocio')->where('id', '=', $id)->select('*')->get();
        return $reglaActualizar;
    }

    public function crear_regla(Request $request)
    {
        $programa = $_POST['codigo'];
        $creditos = $_POST['creditos'];
        $materias = $_POST['materias'];
        $estudiante = $_POST['estudiante'];

        if (isset($_POST['ciclo1'])) {
            $ciclo = $_POST['ciclo1'];
        } else {
            $ciclo = $_POST['ciclo2'];
        }

        $crear = DB::table('reglasNegocio')->insert([
            'programa' => $programa,
            'creditos' => $creditos,
            'materiasPermitidas' => $materias,
            'tipoEstudiante' => $estudiante,
            'ruta' => 0,
            'ciclo' => $ciclo,
        ]);
        $informacionOriginal = $request->except(['_token']);
        if ($crear) :
            $this->insertLogUsuarios("Regla creada", 'ReglasNegocio', $informacionOriginal);
            /** Redirecciona al formulario registro mostrando un mensaje de exito */
            return redirect()->route('facultad.reglas')->with('success', 'Regla creada correctamente');
        else :
            /** Redirecciona al formulario registro mostrando un mensaje de error */
            return redirect()->route('facultad.reglas')->with(['errors' => 'La regla no ha podido ser creada']);
        endif;
    }

    public function updateregla(Request $request)
    {
        $id_llegada = $_POST['id'];
        $programa = $_POST['programa'];
        $creditos = $_POST['creditos'];
        $materias = $_POST['materias'];
        $estudiante = $_POST['estudiante'];
        $ciclo = $_POST['ciclo'];
        
        $id_llegada = $_POST['id'];
        $id = base64_decode(urldecode($id_llegada));
        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }
        //dd($id);die();

        $informacionOriginal = $this->obtenerRegla($id);
        $regla = DB::table('reglasNegocio')
            ->where('id', $id)
            ->update([
                'programa' => $programa,
                'creditos' => $creditos,
                'materiasPermitidas' => $materias,
                'tipoEstudiante' => $estudiante,
                'ciclo' => $ciclo,
            ]);

        $request->merge(['id' => $id]);
        $informacionActualizada = $request->except(['_token']);

        if ($regla) :
            $this->updateLogUsuarios("La regla " . $informacionOriginal[0]->programa . " fue actualizada ", 'reglasNegocio', $informacionOriginal, $informacionActualizada);
            /** Redirecciona al formulario registro mostrando un mensaje de exito */
            return "actualizado";
        else :
            /** Redirecciona al formulario registro mostrando un mensaje de error */
            return "false";
        endif;
    }

    public function activarregla()
    {
        $id_llegada = $_POST['id'];
        $id = base64_decode(urldecode($id_llegada));
        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }
        $informacionOriginal = DB::table('reglasNegocio')->where('id', '=', $id)->select('programa', 'id', 'activo')->get();
        $activarRegla = DB::table('reglasNegocio')->where('id', '=', $id)->update(['activo' => 1]);
        $informacionActualizada = DB::table('reglasNegocio')->where('id', '=', $id)->select('programa', 'id', 'activo')->get();
        if ($activarRegla) :
            $this->updateLogUsuarios("La regla " . $informacionOriginal[0]->programa . " fue activada ", 'reglasNegocio', $informacionOriginal, $informacionActualizada);
            return  "habilitado";
        else :
            return "false";
        endif;
    }

    public function inactivarregla()
    {
        $id_llegada = $_POST['id'];
        $id = base64_decode(urldecode($id_llegada));
        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }
        $informacionOriginal = DB::table('reglasNegocio')->where('id', '=', $id)->select('programa', 'id', 'activo')->get();
        $inactivarRegla = DB::table('reglasNegocio')->where('id', '=', $id)->update(['activo' => 0]);
        $informacionActualizada = DB::table('reglasNegocio')->where('id', '=', $id)->select('programa', 'id', 'activo')->get();
        if ($inactivarRegla) :
            $this->updateLogUsuarios("La regla " . $informacionOriginal[0]->programa . " fue activada ", 'reglasNegocio', $informacionOriginal, $informacionActualizada);
            return  "deshabilitado";
        else :
            return "false";
        endif;
    }

    /**
     * Método para registrar en el Log de Usuarios la acción de update  
     */

    public function updateLogUsuarios($mensaje, $tabla, $informacionOriginal, $informacionActualizada)
    {

        LogUsuariosController::registrarLog('UPDATE', $mensaje, $tabla, json_encode($informacionOriginal), json_encode($informacionActualizada));
    }

    /**
     * Método para registrar en el Log de Usuarios la acción de insert 
     */

    public function insertLogUsuarios($mensaje, $tabla, $informacionOriginal)
    {
        LogUsuariosController::registrarLog('INSERT', $mensaje, $tabla, json_encode($informacionOriginal), NULL);
    }

    public function vistaProgramasPeriodos()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        return view('vistas.admin.programasPeriodos');
    }


    public function get_programasPeriodos(){
        $programasPeriodos = DB::table('programasPeriodos as pp')
        ->join('programas as p', 'p.codprograma', '=', 'pp.codPrograma')
        ->where('p.estado','=',1)
        ->select('pp.*','p.programa')
        ->get();
        header("Content-Type: application/json");
        echo json_encode(array('data' => $programasPeriodos));
    }

    public function update_programaPeriodo(Request $request){
        $id_llegada = $_POST['id'];
        $programa = $_POST['programa'];
        $periodo = $_POST['periodo'];
        $plan = $_POST['plan'];
        $fecha = $_POST['fecha'];
        $id_llegada = $_POST['id'];
        $id = base64_decode(urldecode($id_llegada));
        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }
        if(empty($fecha)){
            $informacionOriginal = $this->obtenerRegla($id);
            $regla = DB::table('programasPeriodos')
                ->where('id', $id)
                ->update([
                    'codPrograma' => $programa,
                    'periodo' => $periodo,
                    'plan' => $plan,
                ]);
        } else {
            $informacionOriginal = $this->obtenerRegla($id);
            $regla = DB::table('programasPeriodos')
                ->where('id', $id)
                ->update([
                    'codPrograma' => $programa,
                    'periodo' => $periodo,
                    'plan' => $plan,
                    'fecha_inicio' => $fecha,
                ]);
        }
        $request->merge(['id' => $id]);
        $informacionActualizada = $request->except(['_token']);

        if ($regla) :
            $this->updateLogUsuarios("La regla " . $informacionOriginal[0]->programa . " fue actualizada ", 'reglasNegocio', $informacionOriginal, $informacionActualizada);
            /** Redirecciona al formulario registro mostrando un mensaje de exito */
            return "actualizado";
        else :
            /** Redirecciona al formulario registro mostrando un mensaje de error */
            return "false";
        endif;
    }

    public function inactivar_programaPeriodo(Request $request){
        $id_llegada = $_POST['id'];
        $id = base64_decode(urldecode($id_llegada));
        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }
        $informacionOriginal = DB::table('programasPeriodos')->where('id', '=', $id)->select('programa', 'id', 'estado')->get();
        $inactivarRegla = DB::table('programasPeriodos')->where('id', '=', $id)->update(['estado' => 0]);
        $informacionActualizada = DB::table('programasPeriodos')->where('id', '=', $id)->select('programa', 'id', 'estado')->get();
        if ($inactivarRegla) :
            $this->updateLogUsuarios("El programa fue inactivado para el periodo " . $informacionOriginal[0]->programa . " fue activada ", 'programasPeriodos', $informacionOriginal, $informacionActualizada);
            return  "deshabilitado";
        else :
            return "false";
        endif;
    }
    
    public function activar_programaPeriodo(Request $request){
        $id_llegada = $_POST['id'];
        $id = base64_decode(urldecode($id_llegada));
        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }
        $informacionOriginal = DB::table('programasPeriodos')->where('id', '=', $id)->select('programa', 'id', 'estado')->get();
        $inactivarRegla = DB::table('programasPeriodos')->where('id', '=', $id)->update(['estado' => 1]);
        $informacionActualizada = DB::table('programasPeriodos')->where('id', '=', $id)->select('programa', 'id', 'estado')->get();
        if ($inactivarRegla) :
            $this->updateLogUsuarios("El programa fue activado para el periodo " . $informacionOriginal[0]->programa . " fue activada ", 'programasPeriodos', $informacionOriginal, $informacionActualizada);
            return  "deshabilitado";
        else :
            return "false";
        endif;
    }
    public function getProgramasPeriodos(Request $request)
    {
        $periodos = $request->input('periodos');

        $data = DB::table('programasPeriodos')->whereIn('periodo', $periodos)->get();

        header("Content-Type: application/json");
        echo json_encode(array('data' => $data));
    }

    public function getProgramasPeriodosFacultad(Request $request)
    {
        $periodos = $request->input('periodos');
        $facultades = $request->input('idfacultad');
        $data = DB::table('programasPeriodos as Pp')
            ->join('programas as p', 'Pp.codPrograma', '=', 'p.codprograma')
            ->whereIn('Pp.periodo', $periodos)
            ->whereIn('p.Facultad', $facultades)
            ->get();

        header("Content-Type: application/json");
        echo json_encode(array('data' => $data));
    }

    /** Función para desactivar los periodos */
    public function inactivarProgramaPeriodo(Request $request)
    {
        $id_llegada = $_POST['id'];
        $id = base64_decode(urldecode($id_llegada));
        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }
        $informacionOriginal = DB::table('programasPeriodos')->where('id', '=', $id)->select('codPrograma', 'id', 'periodo', 'estado')->get();
        $inactivarPeriodo = DB::table('programasPeriodos')->where('id', '=', $id)->update(['estado' => 0]);
        $informacionActualizada = DB::table('programasPeriodos')->where('id', '=', $id)->select('codPrograma', 'id', 'periodo', 'estado')->get();
        if ($inactivarPeriodo) :
            $this->updateLogUsuarios("El periodo " . $informacionOriginal[0]->codPrograma . " - " . $informacionOriginal[0]->periodo . " fue inactivado ", 'programasPeriodos', $informacionOriginal, $informacionActualizada);
            return  "deshabilitado";
        else :
            return "false";
        endif;
    }

    public function agregar_programaPeriodo(Request $request){
        //var_dump($_POST);die();
        $codPrograma = $_POST['codPrograma'];
        $periodos = $_POST['periodos'];
        $ingresos = $_POST['ingresos'];
        $ingreso1 = $ingresos[0];
        $ingreso2 = $ingresos[1];
        $planes = $_POST['plan'];
        //dd($ingreso1);die();
        switch ($periodos) {
            case 1:
                $periodosArray = [202404,202405,202406,202407,202408];
                break;
            case 2:
                $periodosArray = [202411,202412,202413,202416,202417];
                break;
            case 3:
                $periodosArray = [202431,202432,202433,202434,202435];
                break;
            case 4:
                $periodosArray = [202441,202442,202443,202444,202445];
                break;
            case 5:
                $periodosArray = [202451,202452,202453,202454,202455];
                break;
        }
        
        /* $fechasInicioPeriodo = DB::table('periodo')->whereIn('periodos',$periodosArray)->select('periodos','fechaInicioPeriodo')->get();
        dd($fechasInicioPeriodo);die(); */
        foreach ($planes as $key => $plan) {
            foreach ($periodosArray as $key => $periodo) {
                $key1 = $key + 1;
                $estado = in_array($key1,$ingresos) == true ? 1 : 0;
                $fechaInicioPeriodo = DB::table('periodo')->where('periodos',$periodo)->select('periodos','fechaInicioPeriodo')->first();
                // var_dump($key,$fechaInicioPeriodo->fechaInicioPeriodo);
                $crear = DB::table('programasPeriodos')->insert([
                    'codPrograma' => $codPrograma,
                    'periodo' => $periodo,
                    'estado' => $estado,
                    'plan' => $plan,
                    'fecha_inicio' => $fechaInicioPeriodo->fechaInicioPeriodo,
                ]);
            }
        }

        $informacionOriginal = $request->except(['_token']);
        if ($crear) :
            $this->insertLogUsuarios("Programa en periodo creado", 'programasPeriodos', $informacionOriginal);
            /** Redirecciona al formulario registro mostrando un mensaje de exito */
            return redirect()->route('programasPeriodos.view')->with('success', 'Regla creada correctamente');
        else :
            /** Redirecciona al formulario registro mostrando un mensaje de error */
            return redirect()->route('programasPeriodos.view')->with(['errors' => 'La regla no ha podido ser creada']);
        endif;
        
    }

    /** 
     * Función para activar los periodos 
     * */
    public function activarProgramaPeriodo(Request $request)
    {
        $id_llegada = $_POST['id'];
        $id = base64_decode(urldecode($id_llegada));
        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }
        $informacionOriginal = DB::table('programasPeriodos')->where('id', '=', $id)->select('codPrograma', 'id', 'periodo', 'estado')->get();
        $activarPeriodo = DB::table('programasPeriodos')->where('id', '=', $id)->update(['estado' => 1]);
        $informacionActualizada = DB::table('programasPeriodos')->where('id', '=', $id)->select('codPrograma', 'id', 'periodo', 'estado')->get();
        if ($activarPeriodo) :
            $this->updateLogUsuarios("El periodo " . $informacionOriginal[0]->codPrograma . " - " . $informacionOriginal[0]->periodo . " fue activado ", 'programasPeriodos', $informacionOriginal, $informacionActualizada);
            return  "habilitado";
        else :
            return "false";
        endif;
    }

    /**
     * Método que trae los periodos activos de cada programas
     */
    public function programasActivos()
    {
        $periodosActivos = DB::table('periodo')->where('periodoActivo', 1)->select('periodos')->get();

        $periodos = [];

        foreach ($periodosActivos as $key) {
            $dosUltimosDigitos = substr($key->periodos, -2);
            $periodos[] = $dosUltimosDigitos;
        }

        $nivelFormacion = DB::table('programasPeriodos as pP')
            ->join('programas as p', 'pP.codPrograma', '=', 'p.codprograma')
            ->select('p.nivelFormacion', 'pP.periodo')
            ->whereIn('pP.periodo', $periodos)
            ->groupBy('p.nivelFormacion', 'pP.periodo')
            ->get(); 

        return $nivelFormacion;
    }

    public function periodosActivos()
    {

        $tabla = $_POST['tabla'];

        if($tabla == 'Mafi'){
            $periodosActivos = DB::connection('sqlsrv')->table('MAFI')->select('periodo')->groupBy('periodo')->get();
        }elseif($tabla == 'planeacion'){
            $periodosActivos = DB::table('planeacion')->select('periodo')->groupBy('periodo')->get();
        }elseif($tabla == 'moodle'){
            $periodosActivos = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')->select('Periodo_Rev')->groupBy('Periodo_Rev')->get();
        }

     

        foreach ($periodosActivos as $key) {

            if($tabla == 'Mafi' ){
                $dato = $key->periodo;
            }elseif($tabla == 'planeacion')
            {
                $dato = $key->periodo;
            }
            else{
                $dato = $key->Periodo_Rev;
            }

            $dos= substr($dato, -2);
         
         
            switch ($dos) {

                case '04':
                case '05':
                case '06':
                case '07':
                case '08':
                 
                    $periodo[]=[
                        'nivelFormacion'=>'EDUCACION CONTINUA',
                        'periodo'=> $dato
                    ];
                
                break;
                case '11':
                case '12':
                case '13':
                case '16':
                case '17':
                case '31':
                case '32':
                case '33':
                case '34':
                case '35':
                    
                    $periodo[]=[
                        'nivelFormacion'=>'PROFESIONAL',
                        'periodo'=> $dato
                    ];
                
                break;
                case '41':
                case '42':
                case '43':
                case '44':
                case '45':
                    
                    $periodo[]=[
                        'nivelFormacion'=>'ESPECIALISTA',
                        'periodo'=> $dato
                    ];
                
                break;

                case '51':
                case '52':
                case '53':
                case '54':
                case '55':
                    
                    $periodo[]=[
                        'nivelFormacion'=>'MAESTRIA',
                        'periodo'=> $dato
                    ];
                
                break;

              
    
            }

           

        }
    
    
        return $periodo;
    }

    public function periodosActivosPrograma(Request $request){
        $programas = $request->input('programas');

        $tabla = $_POST['tabla'];
       //dd($tabla);
        if($tabla == 'Mafi'){
            $periodosActivos = DB::connection('sqlsrv')->table('MAFI')->select('periodo')->whereIn('codprograma',$programas)->groupBy('periodo')->get();
        }elseif($tabla == 'planeacion'){
            $periodosActivos = DB::table('planeacion')
            ->whereIn('codprograma', $programas)
            ->select('periodo')->groupBy('periodo')->get();
        }elseif($tabla == 'moodle'){
            $periodosActivos = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')->select('Periodo_Rev')
            ->where(function ($query) use ($programas) {
                foreach ($programas as $programa) {
                    $query->orWhere('Grupo', 'LIKE', '%' . $programa . '%');
                }
            })
            ->groupBy('Periodo_Rev')
            ->get();
        }

        foreach ($periodosActivos as $key) {

            if($tabla == 'Mafi' ){
                $dato = $key->periodo;
            }elseif($tabla == 'planeacion')
            {
                $dato = $key->periodo;
            }
            else{
                $dato = $key->Periodo_Rev;
            }

            $dos= substr($dato, -2);
         
         
            switch ($dos) {

                case '04':
                case '05':
                case '06':
                case '07':
                case '08':
                 
                    $periodo[]=[
                        'nivelFormacion'=>'EDUCACION CONTINUA',
                        'periodo'=> $dato
                    ];
                
                break;
                case '11':
                case '12':
                case '13':
                case '16':
                case '17':
                case '31':
                case '32':
                case '33':
                case '34':
                case '35':
                    
                    $periodo[]=[
                        'nivelFormacion'=>'PROFESIONAL',
                        'periodo'=> $dato
                    ];
                
                break;
                case '41':
                case '42':
                case '43':
                case '44':
                case '45':
                    
                    $periodo[]=[
                        'nivelFormacion'=>'ESPECIALISTA',
                        'periodo'=> $dato
                    ];
                
                break;

                case '51':
                case '52':
                case '53':
                case '54':
                case '55':
                    
                    $periodo[]=[
                        'nivelFormacion'=>'MAESTRIA',
                        'periodo'=> $dato
                    ];
                
                break;

              
    
            }

           

        }
    
  
        return $periodo;

    }

    public function actualizarProgramaPeriodo(Request $request)
    {
        $id_llegada = $_POST['id'];
        $fecha = $_POST['fecha'];

        $id = base64_decode(urldecode($id_llegada));
        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }

        $informacionOriginal = DB::table('programasPeriodos')->where('id', $id)->first();

        $periodo = DB::table('programasPeriodos')
            ->where('id', $id)
            ->update([
                'fecha_inicio' => $fecha
            ]);

        $informacionActualizada = $request->except(['_token']);

        if ($periodo) :
            /** Redirecciona al formulario registro mostrando un mensaje de exito */
            $this->updateLogUsuarios("El periodo " . $informacionOriginal->codPrograma . " - " . $informacionOriginal->periodo . " fue actualizado ", 'programasPeriodos', $informacionOriginal, $informacionActualizada);
            return "actualizado";
        else :
            /** Redirecciona al formulario registro mostrando un mensaje de error */
            return "false";
        endif;
    }

    public function vistaMetas()
    {
        return view('vistas.admin.metas');
    }

    public function traerMetasActivas()
    {
        $periodosActivos = DB::table('periodo')->select('periodos')->where('activoCiclo1', 1)->pluck('periodos')->toArray();

        $dos = [];

        foreach ($periodosActivos as $periodo) {
            $dos[] = substr($periodo, -2);
        }
        
        $year = date("Y");

        $consulta = DB::table('programas_metas as pm')
        ->join('programas as p', 'pm.programa', '=', 'p.codprograma')
        ->select('pm.id','pm.programa','p.programa','pm.meta', 'pm.año', 'pm.periodo')
        ->whereIn('periodo', $dos)
        ->where('año', $year)
        ->get();

        return $consulta;
    }

    public function traerTodosProgramas()
    {
        
        $consulta = DB::table('programas')->orderBy('programa', 'asc')->get();

        return $consulta;
    }

    public function periodosActivosCiclo1()
    {
        $periodosActivos = DB::table('periodo')->select('periodos')->where('activoCiclo1', 1)
        ->get();

        return $periodosActivos;
    }

    public function crearMeta(Request $request)
    {
        $programa = $_POST['programa'];
        $meta = $_POST['meta'];
        $periodo = $_POST['periodo'];

        $dos = substr($periodo, -2);
        $año = substr($periodo, 0, 4);

        $crear = DB::table('programas_metas')->insert([
            'programa' => $programa,
            'meta' => $meta,
            'periodo' => $dos,
            'año' => $año,
        ]);
        $informacionOriginal = $request->except(['_token']);
        if ($crear) :
            $this->insertLogUsuarios("Meta creada", 'programas_metas', $informacionOriginal);
            /** Redirecciona al formulario registro mostrando un mensaje de exito */
            return redirect()->route('metas.view')->with('success', 'Meta creada correctamente');
        else :
            /** Redirecciona al formulario registro mostrando un mensaje de error */
            return redirect()->route('metas.view')->with(['errors' => 'La meta no ha podido ser creada']);
        endif; 
    }

    public function updateMeta(Request $request)
    {
        $id_llegada = $_POST['id'];
        $metanueva = $_POST['metanueva'];
        $id = base64_decode(urldecode($id_llegada));
        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }

        $informacionOriginal = $this->obtenerMeta($id);

        
        /** Consulta para actualizar facultad */
        $update = DB::table('programas_metas')
            ->where('id', $id)
            ->update([
                'meta' => $metanueva,
            ]);

        $request->merge(['id' => $id]);
        $informacionActualizada = $request->except(['_token']);
        if ($update) :
            $this->updateLogUsuarios("La meta" . $informacionOriginal[0]->programa . " fue actualizada", 'programas_metas', $informacionOriginal, $informacionActualizada);
            /** Redirecciona al formulario registro mostrando un mensaje de exito */
            return "actualizado";
        else :
            /** Redirecciona al formulario registro mostrando un mensaje de error */
            return "false";
        endif;
    }

    public function obtenerMeta($id)
    {
        $meta = DB::table('programas_metas')->where('id',$id)->get();
        return $meta;
    }
}
