<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AlertasTempranasController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        return view('vistas.alertastempranas.alertas');
    }

    /*public function vistaRectorVicerector()
    {
        if (auth()->check() != true) { 
            return route('login.index');
        }
        if (auth()->user()->id_rol == 19) :
            return view('vistas.alertastempranas.rector');
        endif;
        if (auth()->user()->id_rol == 20) :
            return view('vistas.alertastempranas.vicerector');
        endif;
    }

    public function vistaCoordinador()
    {
        if (auth()->check() != true) { 
            return route('login.index');
        }
        if (auth()->user()->id_rol == 2) :
            return view('vistas.alertastempranas.director');
        endif;
        if (auth()->user()->id_rol == 3) :
            return view('vistas.alertastempranas.coordinador');
        endif;
        if (auth()->user()->id_rol == 4) :
            return view('vistas.alertastempranas.lider');
        endif;
    }

    public function vistaRectorDecano()
    {
        if (auth()->check() != true) { 
            return route('login.index');
        }
        $user = auth()->user();
        $idfacultad = trim($user->id_facultad, ',');
        $facultades = explode(",", $idfacultad);
        foreach ($facultades as $key => $value) {
            $consulta = DB::table('facultad')->where('id', $value)->select('nombre')->first();
            $nombreFacultades[$value] = $consulta->nombre;
        }
        return view('vistas.alertastempranas.decano', ['facultades' => $nombreFacultades]);
    }

    public function vistaCoordinadorLider()
    {
        if (auth()->check() != true) { 
            return route('login.index');
        }
        $user = auth()->user();
        $programa = trim($user->programa, ';');
        $programas = explode(";", $programa);
        foreach ($programas as $key => $value) {
            $consulta = DB::table('programas')->where('id', $value)->select('codprograma', 'programa')->first();
            $data[$value] = $consulta;
        }
        return view('vistas.alertastempranas.coordinador', ['programas' => $data]);
    }

    public function vistaTransversal()
    {
        if (auth()->check() != true) { 
            return route('login.index');
        }
        if (auth()->user()->id_facultad == 8) {
            $consultaCursosComunicativas = DB::table('mallaCurricular')
                ->where('curso', 'like', '%comunicativas%')
                ->select('id', 'curso')
                ->distinct()
                ->get()
                ->toArray();

            $consultaCursosArgumentacion = DB::table('mallaCurricular')
                ->where('curso', 'like', '%argumentacion%')
                ->select('id', 'curso')
                ->distinct()
                ->get()
                ->toArray();

            $cursos = array_merge($consultaCursosComunicativas, $consultaCursosArgumentacion);
        } elseif (auth()->user()->id_facultad == 9) {
            $cursos = DB::table('mallaCurricular')
                ->where('curso', 'like', '%ingles%')
                ->select('id', 'curso')
                ->distinct()
                ->get()
                ->toArray();
        }

        $cursosUnicos = [];

        foreach ($cursos as $curso) {
            $nombre = trim($curso->curso);
            $id = $curso->id;

            $existe = in_array($nombre, $cursosUnicos);

            if (!$existe) {
                $cursosUnicos[$id] = $nombre;
            }
        }
        return view('vistas.alertastempranas.transversal', ['cursos' => $cursos], ['cursosUnicos' => $cursosUnicos]);
    }*/

    public function tablaAlertasP()
    {
        $periodos = $_POST['periodos'];
        $programas = $_POST['programas'];

        if (isset($_POST['tipo']) && $_POST['tipo'] != 'todos' && $_POST['tipo'] != '') {
            $consultaAlertas = DB::table('alertas_tempranas as a')
                ->join('programas as p', 'p.codprograma', '=', 'a.codprograma')
                ->select('a.*', 'p.programa')
                ->where('tipo', $_POST['tipo'])
                ->whereIn('a.periodo', $periodos)
                ->where('a.activo', 1)
                ->whereIn('a.codprograma', $programas)
                ->orderBy('a.created_at', 'desc')
                ->get();
        } else {
            $consultaAlertas = DB::table('alertas_tempranas as a')
                ->join('programas as p', 'p.codprograma', '=', 'a.codprograma')
                ->select('a.*', 'p.programa')
                ->whereIn('a.periodo', $periodos)
                ->where('a.activo', 1)
                ->whereIn('a.codprograma', $programas)
                ->orderBy('a.created_at', 'desc')
                ->get();
        }

        return $consultaAlertas;
    }

    public function tablaAlertasFacultad()
    {
        $periodos = $_POST['periodos'];
        $facultades = $_POST['facultad'];

        if (isset($_POST['tipo']) && $_POST['tipo'] != 'todos' && $_POST['tipo'] != '') {
            $consultaAlertas = DB::table('alertas_tempranas as a')
                ->join('programas as p', 'p.codprograma', '=', 'a.codprograma')
                ->select('a.*', 'p.programa')
                ->where('tipo', $_POST['tipo'])
                ->whereIn('a.periodo', $periodos)
                ->where('a.activo', 1)
                ->whereIn('p.Facultad', $facultades)
                ->orderBy('a.created_at', 'desc')
                ->get();
        } else {
            //var_dump($facultades);die();
            $consultaAlertas = DB::table('alertas_tempranas as a')
                ->join('programas as p', 'p.codprograma', '=', 'a.codprograma')
                ->select('a.*', 'p.programa')
                ->whereIn('a.periodo', $periodos)
                ->where('a.activo', 1)
                ->whereIn('p.Facultad', $facultades)
                ->orderBy('a.created_at', 'desc')
                ->get();
        }

        return $consultaAlertas;
    }

    public function tablaAlertas()
    {
        $periodos = $_POST['periodos'];

        if (isset($_POST['tipo']) && $_POST['tipo'] != 'todos' && $_POST['tipo'] != '') {
            $consultaAlertas = DB::table('alertas_tempranas as a')
                ->join('programas as p', 'p.codprograma', '=', 'a.codprograma')
                ->select('a.*', 'p.programa')
                ->where('a.activo', 1)
                ->where('tipo', $_POST['tipo'])
                ->whereIn('a.periodo', $periodos)
                ->orderBy('a.created_at', 'desc')
                ->get();
        } else {
            $consultaAlertas = DB::table('alertas_tempranas as a')
                ->join('programas as p', 'p.codprograma', '=', 'a.codprograma')
                ->select('a.*', 'p.programa')
                ->where('a.activo', 1)
                ->whereIn('a.periodo', $periodos)
                ->orderBy('a.created_at', 'desc')
                ->get();

                
        }


        return $consultaAlertas;
    }

    public function graficaAlertas()
    {
        $consulta = DB::table('alertas_tempranas')
            ->select(DB::raw('COUNT(idbanner) as TOTAL'), 'codprograma')
            ->where('activo', 1)
            ->groupBy('codprograma')
            ->orderByDesc('TOTAL')
            ->limit(10)
            ->get();
        return $consulta;
    }

    public function graficaAlertasFacultad()
    {
        $periodos = $_POST['periodos'];
        $facultades = $_POST['facultad'];

        $consulta = DB::table('alertas_tempranas as a')
            ->join('programas as p', 'p.codprograma', '=', 'a.codprograma')
            ->whereIn('a.periodo', $periodos)
            ->whereIn('p.Facultad', $facultades)
            ->where('a.activo', 1)
            ->select(DB::raw('COUNT(a.idbanner) as TOTAL'), 'a.codprograma')
            ->groupBy('a.codprograma')
            ->orderByDesc('TOTAL')
            ->limit(10)
            ->get();

        //return $consulta;
        header("Content-Type: application/json");
        echo json_encode(array('data' => $consulta));
    }

    public function graficaAlertasProgramas()
    {
        $periodos = $_POST['periodos'];
        $programas = $_POST['programas'];

        $consulta = DB::table('alertas_tempranas as a')
            ->join('programas as p', 'p.codprograma', '=', 'a.codprograma')
            ->where('a.activo', 1)
            ->whereIn('a.periodo', $periodos)
            ->whereIn('a.codprograma', $programas)
            ->select(DB::raw('COUNT(a.idbanner) as TOTAL'), 'a.codprograma')
            ->groupBy('a.codprograma')
            ->orderByDesc('TOTAL')
            ->limit(10)
            ->get();

        header("Content-Type: application/json");
        echo json_encode(array('data' => $consulta));
        // return $consulta;
    }

    public function numeroAlertas()
    {
        $numeroAlertas = DB::table('alertas_tempranas')->select(DB::raw('count(id) as total_alertas'))->where('activo', 1)->get();
        //var_dump($numeroAlertas[0]->total_alertas);die();
        return $numeroAlertas[0]->total_alertas;
    }

    public function numeroAlertasFacultad()
    {
        $idFacultad = $_GET['id_facultad'];
        $idFacultad = trim($idFacultad, ';');
        $idFacultades = explode(';', $idFacultad);
        $consultaFacultades = DB::table('facultad')->select('nombre')->wherein('id', $idFacultades)->get();
        $facultades = array();
        foreach ($consultaFacultades as $facultad) :
            array_push($facultades, $facultad->nombre);
        endforeach;
        $consultaProgramas = DB::table('programas')->select('codprograma')->whereIn('Facultad', $facultades)->get();
        //$programas = '';
        $programas = array();
        foreach ($consultaProgramas as $programa) :
            //$programas = $programas.','.$programa->codprograma;
            array_push($programas, $programa->codprograma);
        endforeach;
        //var_dump($programas);die();
        $numeroAlertas = DB::table('alertas_tempranas')->select(DB::raw('count(id) as total_alertas'))->where('activo', 1)->whereIn('codprograma', $programas)->get();
        //var_dump($numeroAlertas[0]->total_alertas);die();
        return $numeroAlertas[0]->total_alertas;
    }

    public function numeroAlertasPrograma()
    {
        $idPrograma = $_GET['id_programa'];
        $idPrograma = trim($idPrograma, ';');
        $idProgramas = explode(';', $idPrograma);
        $consultaProgramas = DB::table('programas')->select('codprograma')->whereIn('id', $idProgramas)->get();
        //$programas = '';
        $programas = array();
        foreach ($consultaProgramas as $programa) :
            //$programas = $programas.','.$programa->codprograma;
            array_push($programas, $programa->codprograma);
        endforeach;
        //var_dump($programas);die();
        $numeroAlertas = DB::table('alertas_tempranas')->select(DB::raw('count(id) as total_alertas'))->where('activo', 1)->whereIn('codprograma', $programas)->get();
        //var_dump($numeroAlertas[0]->total_alertas);die();
        return $numeroAlertas[0]->total_alertas;
    }

    public function numeroAlertasCurso()
    {
        $idFacultad = $_GET['id_facultad'];

        if ($idFacultad == 8) {
            $consultaCursosComunicativas = DB::table('mallaCurricular')
                ->where('curso', 'like', '%comunicativas%')
                ->select('codigoCurso', 'curso')
                ->distinct()
                ->get()
                ->toArray();

            $consultaCursosArgumentacion = DB::table('mallaCurricular')
                ->where('curso', 'like', '%argumentacion%')
                ->select('codigoCurso', 'curso')
                ->distinct()
                ->get()
                ->toArray();

            $cursos = array_merge($consultaCursosComunicativas, $consultaCursosArgumentacion);
        } elseif ($idFacultad == 9) {
            $cursos = DB::table('mallaCurricular')
                ->where('curso', 'like', '%ingles%')
                ->select('codigoCurso', 'curso')
                ->distinct()
                ->get()
                ->toArray();
        }

        $cursosConsulta = array();
        foreach ($cursos as $key) :
            array_push($cursosConsulta, $key->codigoCurso);
        endforeach;

        $idsBanner = DB::table('planeacion')->select('codBanner')->where('codMateria', $cursosConsulta)->pluck('codBanner')->toArray();

        $numeroAlertas = DB::table('alertas_tempranas')->select(DB::raw('count(id) as total_alertas'))->where('activo', 1)->whereIn('idbanner', $idsBanner)->get();

        return $numeroAlertas[0]->total_alertas;
    }


    public function updateLogUsuarios($mensaje, $tabla, $informacionOriginal, $informacionActualizada)
    {
        LogUsuariosController::registrarLog('UPDATE', $mensaje, $tabla, json_encode($informacionOriginal), json_encode($informacionActualizada));
    }

    public function inactivarAlerta()
    {
        $id_llegada = $_POST['id'];
        $id = base64_decode(urldecode($id_llegada));
        if (!is_numeric($id)) {
            $id = decrypt($id_llegada);
        }
        $informacionOriginal = DB::table('alertas_tempranas')->where('id', '=', $id)->select('codPrograma', 'desccripcion', 'id', 'periodo', 'activo')->get();
        $inactivarAlerta = DB::table('alertas_tempranas')->where('id', '=', $id)->update(['activo' => 0]);
        $informacionActualizada = DB::table('alertas_tempranas')->where('id', '=', $id)->select('codPrograma', 'desccripcion', 'id', 'periodo', 'activo')->get();
        if ($inactivarAlerta) :
            $this->updateLogUsuarios("La alerta temprana" . $informacionOriginal[0]->desccripcion . " fue inactivada ", 'alertas_tempranas', $informacionOriginal, $informacionActualizada);
            return  "deshabilitado";
        else :
            return "false";
        endif;
    }

    public function tiposAlertas()
    {

        $tiposAlertas = DB::table('alertas_tempranas')->select('tipo')->groupBy('tipo')->get();

        return $tiposAlertas;
    }
}
