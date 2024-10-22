<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstudianteController extends Controller
{
    protected $servicio;

    public function __construct()
    {
        $this->servicio = true;
    }

    public function inicio()
    {
        return view('estudiante.index');
    }

    public function consultaProgramas()
    {
        $estudiante = $_POST['codBanner'];
        //$url = "https://services.ibero.edu.co/utilitary/v1/MoodleAulaVirtual/GetPersonByIdBannerQuery/" . $estudiante;
        //$historialAcademico = json_decode(file_get_contents($url), true);
        if($this->servicio)
        {
            $historialAcademico = $this->consultaServicio($estudiante);
        }else{
            $historialAcademico = $this->servicioHistorial($estudiante);
        }
        $programa = [];

        $consultaEstudiante = DB::table('estudiantes')->where('homologante', '=', $estudiante)->get();
        //var_dump($consultaEstudiante);die();

        if ($historialAcademico) {

            foreach ($historialAcademico as $key_historialAcademico => $value_historialAcademico) {

                $programa[$value_historialAcademico['cod_programa']] = ['codprograma' => $value_historialAcademico['cod_programa'], 'programa' => $value_historialAcademico['programa']];
            }
            $programa = array_column($programa, 'codprograma');
            //var_dump($programa);die();
            return $programa;
        }
        else {

            $consultaEstudiante = DB::table('estudiantes')->where('homologante', '=', $estudiante)->first();
            return $consultaEstudiante;
        }
    }

    //public function consultaEstudiante(Request $request){
    public function consultaEstudiante()
    {
        //dd($request->codigo);
        //$estudiante = $request->codigo;
        $estudiante = $_POST['codBanner'];
        $consultaEstudiante = DB::table('estudiantes')->where('homologante', '=', $estudiante)->first();
        //$url = "https://services.ibero.edu.co/utilitary/v1/MoodleAulaVirtual/GetPersonByIdBannerQuery/" . $estudiante;
        // $historialAcademico = json_decode(file_get_contents($url), true);

        //$historialAcademico = $this->consultaServicio($estudiante);
        //var_dump($this->servicio);die;
        if($this->servicio)
        {
            $historialAcademico = $this->consultaServicio($estudiante);
        }else{
            $historialAcademico = $this->servicioHistorial($estudiante);
        }
        $programa = [];

        if ($historialAcademico) {

            foreach ($historialAcademico as $key_historialAcademico => $value_historialAcademico) {

                $programa[$value_historialAcademico['cod_programa']] = ['codprograma' => $value_historialAcademico['cod_programa'], 'programa' => $value_historialAcademico['programa']];
            }

            $programaCod = array_column($programa, 'codprograma');
            $programaNombre = array_column($programa, 'programa');
            $programas = [];
            for ($i = 0; $i < count($programaCod); $i++) {
                $programas[] = [
                    'cod_programa' => $programaCod[$i],
                    'programa' => $programaNombre[$i],
                ];
            }
            
        }


        else {
            $consultaPrograma = DB::table('estudiantes')->where('homologante',$estudiante)->select('programa')->get();

            $programas = [];
            foreach ($consultaPrograma as $programa)
            {
                $codprograma = $programa->programa;

                $nombre = DB::table('programas')->where('codprograma', $codprograma)->select('programa')->get();

                $programas[] = [
                    'cod_programa' => $codprograma,
                    'programa' => $nombre[0]->programa,
                ];
            }

        }
        return $programas;
    }

    public function consultaNombre()
    {
        $estudiante = $_POST['codBanner'];
        $consultaNombre = DB::table('datos_moodle')->where('Id_Banner', '=', $estudiante)->select('Nombre', 'Apellido')->first();
        $nombre = ' ';

        if ($consultaNombre != NULL) :
            $nombre = $consultaNombre->Nombre . " " . $consultaNombre->Apellido;
            else :
                //$url = "https://services.ibero.edu.co/utilitary/v1/MoodleAulaVirtual/GetPersonByIdBannerQuery/" . $estudiante;
    
                if($this->servicio)
                {
                    $consultaNombre = $this->consultaServicio($estudiante);
                }else{
                    $consultaNombre = $this->servicioHistorial($estudiante);
                }

            if ($consultaNombre != NULL) :

                $nombre = $consultaNombre[0]["estudiante"];
            else :
                $consultaNombre = DB::table('estudiantes')->where('homologante', '=', $estudiante)->select('nombre')->first();
                
                if($consultaNombre){
                $nombre = $consultaNombre->nombre;
                }
            endif;
        endif;

        if($nombre != ' '){
            return $nombre;
        }else{
            return 'no tiene historial';
        }
    }

    public function consultaMalla()
    {
        $programa = $_POST['programa'];
        $codBanner = $_POST['codBanner'];
        $mallaCurricular = DB::table('mallaCurricular')->where('codprograma', '=', $programa)->get()->toArray();
        
        return $mallaCurricular;
    }

    public function consultaHistorial()
    {
        $idbanner = $_POST['codBanner'];
        $programa = $_POST['programa'];
        $semestre = 0;


        $consultaPlan = DB::connection('sqlsrv')->table('MAFI')->where('IDBANNER', $idbanner)->select('PLAN_ESTUDIO')->first();

        if($consultaPlan){
            $plan = $consultaPlan->PLAN_ESTUDIO;
            $mallaCurricular = DB::table('mallaCurricular')
                ->where('codprograma', '=', $programa)
                ->where('plan', $plan)
                ->orderBy('semestre', 'ASC')
                ->get()
                ->toArray();

        }else{
            $mallaCurricular = DB::table('mallaCurricular')
            ->where('codprograma', '=', $programa)
            ->orderBy('semestre', 'ASC')
            ->get()
            ->toArray();
        }
        
        $notaAprobacion = 3.0;

        $consultaNivel = DB::table('programas')->select('nivelFormacion')->where('codprograma', $programa)->first();

        if($consultaNivel)
        {
            $nivel = $consultaNivel->nivelFormacion;
        
            if($nivel == 'EDUCACION CONTINUA' || $nivel == 'ESPECIALISTA' || $nivel == 'MAESTRIA'){
                $notaAprobacion = 3.5;
            }

        }

        
        if($this->servicio)
        {   $historialAcademico = $this->consultaServicio($idbanner);
        }else{
            $historialAcademico = $this->servicioHistorial($idbanner);
        }
        

      if (!empty($mallaCurricular)) {
           
            $historial = [];
            $proyectada = [];
            $historialAux = [];
            $contador_historial=0;

            $proyectada = DB::table('programacion')->where('codBanner', '=', $idbanner)->get()->toArray();

            $moodle = DB::connection('mysql')->table('V_Reporte_Ausentismo_memory')->where('Id_Banner', '=', $idbanner)->get()->toArray();


            //if (empty($proyectada)) {
            //    $proyectada = DB::table('planeacion')->where('codBanner', '=', $idbanner)->get()->toArray();
            //}

            /*utilizamos la función array_filter() y in_array() para filtrar los elementos de $array1 que existen en $array2. El resultado se almacena en $intersection. Luego, verificamos si $intersection contiene al menos un elemento utilizando count($intersection) > 0.*/


            foreach ($mallaCurricular as $key_mallaCurricular => $value_mallaCurricular) {

                $palabras = explode(' ', $value_mallaCurricular->curso);

                foreach ($palabras as &$palabra) {
                    if (strpos($palabra, "II") !== false || strpos($palabra, "III") !== false || strpos($palabra, "IV") || strpos($palabra, "VI") || strpos($palabra, "VII")) {
                        continue;
                    }

                    $palabra = mb_convert_case($palabra, MB_CASE_TITLE, "UTF-8");

                    $palabra = str_replace(["Á", "É", "Í", "Ó", "Ú"], ["á", "é", "í", "ó", "ú"], $palabra);
                }

                // Convierte el array resultante de nuevo a una cadena
                $resultado_final = implode(' ', $palabras);
                   // var_dump();
                $materias_malla[$value_mallaCurricular->codigoCurso] = array(
                    'codigo_materia' => $value_mallaCurricular->codigoCurso,
                    'semestre' => $value_mallaCurricular->semestre,
                    'creditos' => $value_mallaCurricular->creditos,
                    'ciclo' => $value_mallaCurricular->ciclo,
                    'nombre_materia' => $resultado_final,
                    'calificacion' => "",
                    'color' => 'bg-secondary',
                    'cursada' => '',
                    'por_ver' => 'Por ver',
                    'programada' => '',
                    'moodle' => '',
                    'codprograma' => $value_mallaCurricular->codprograma,
                    'ciclo' =>$value_mallaCurricular->ciclo, 
                    'prerequisito' =>$value_mallaCurricular->prerequisito, 
                    'equivalencia' =>$value_mallaCurricular->equivalencia
                );

                $value_mallaCurricular->semestre >= $semestre ? $semestre = $value_mallaCurricular->semestre : $semestre = $semestre;
            }

            if (!empty($historialAcademico)) {

                foreach ($historialAcademico as $key_historialAcademico => $value_historialAcademico) {

                    if ($value_historialAcademico['cod_programa'] == $programa) {

                        if (isset($materias_malla[$value_historialAcademico['idCurso']])) {
                            $notaFloat = floatval(str_replace(',', '.', $value_historialAcademico['calificacion']));
                            if($materias_malla[$value_historialAcademico['idCurso']]['codigo_materia'] == 'IIV22261'){
                                    if($materias_malla[$value_historialAcademico['idCurso']]['calificacion'] < $notaFloat){
                                        $value_historialAcademico['calificacion'] = "" . $notaFloat . "";
                                    }else{
                                        $notaFloat = floatval(str_replace(',', '.', $materias_malla[$value_historialAcademico['idCurso']]['calificacion']));
                                        $value_historialAcademico['calificacion'] = "" . $notaFloat . "";
                                    }
                            }
                            
                            if ($notaFloat >= $notaAprobacion) {
                                $color = 'bg-success';
                                $Cursada = 'aprobada';
                                $porver = 'Vista';
                            } else {
                                $color = 'bg-danger';
                                $Cursada = 'perdida';
                                $porver = '';
                            }
                            $contador_historial++;
                            $materias_malla[$value_historialAcademico['idCurso']]['calificacion'] = $value_historialAcademico['calificacion'];
                            //dd($materias_malla[$value_historialAcademico['idCurso']]['calificacion']);
                            $materias_malla[$value_historialAcademico['idCurso']]['color'] = $color;
                            $materias_malla[$value_historialAcademico['idCurso']]['cursada'] = $Cursada;
                            $materias_malla[$value_historialAcademico['idCurso']]['por_ver'] = $porver;
                        }
                    }
                }
            }

            if (!empty($proyectada)){
                foreach ($proyectada as $key_proyectada => $value_proyectada){
                    if (isset($materias_malla[$value_proyectada->codMateria])){
                        $materias_malla[$value_proyectada->codMateria]['color'] = "bg-warning";
                        $materias_malla[$value_proyectada->codMateria]['cursada'] = "";
                        $materias_malla[$value_proyectada->codMateria]['por_ver'] = "Proyectada";
                    }
                }

             }
            

            if (!empty($moodle)) :

                foreach ($moodle as $key_moodle => $value_moodle) {

                    if (isset($materias_malla[$value_moodle->Cod_materia])) {
                        $materias_malla[$value_moodle->Cod_materia]['color'] == "bg-warning" ? $materias_malla[$value_moodle->Cod_materia]['color'] : $materias_malla[$value_moodle->Cod_materia]['color'] = "bg-info";
                        $materias_malla[$value_moodle->Cod_materia]['cursada'] = "";
                        $materias_malla[$value_moodle->Cod_materia]['por_ver'] == "Proyectada" ? $materias_malla[$value_moodle->Cod_materia]['por_ver']: $materias_malla[$value_moodle->Cod_materia]['inscrita']="Viendo";
                    }
                }

            endif;

            $data = array(
                'info' => "con_datos",
                'historial' => $materias_malla,
                'semestre' => $semestre
            );

            return $data;
        } else {

            if (!empty($historialAcademico)) {

                foreach ($historialAcademico as $key_historialAcademico => $value_historialAcademico) {
                    if ($value_historialAcademico['cod_programa'] == $programa) {
                        $semestre = 0;
                        $historial[] = $value_historialAcademico;

                        if ($value_historialAcademico['calificacion'] > 3) {
                            $color = 'bg-success';
                            $Cursada = 'aprobada';
                            $porver = 'Vista';
                        } else {
                            $color = 'bg-danger';
                            $Cursada = 'perdida';
                            $porver = '';
                        }

                        $palabras = explode(' ', $value_historialAcademico['materia']);

                        foreach ($palabras as &$palabra) {
                            if (strpos($palabra, "II") !== false || strpos($palabra, "III") !== false || strpos($palabra, "IV") || strpos($palabra, "VI") || strpos($palabra, "VII")) {
                                continue;
                            }

                            $palabra = mb_convert_case($palabra, MB_CASE_TITLE, "UTF-8");

                            $palabra = str_replace(["Á", "É", "Í", "Ó", "Ú"], ["á", "é", "í", "ó", "ú"], $palabra);
                        }

                        $resultado_final = implode(' ', $palabras);


                        $materias_malla[$value_historialAcademico['idCurso']] = array(
                            'codigo_materia' => $value_historialAcademico['idCurso'],
                            'semestre' => $semestre,
                            'creditos' => $value_historialAcademico['creditos'],
                            'ciclo' => '',
                            'nombre_materia' => $resultado_final,
                            'calificacion' => $value_historialAcademico['calificacion'],
                            'color' => $color,
                            'cursada' => $Cursada,
                            'por_ver' => $porver,
                            'programada' => '',
                            'moodle' => '',
                            'programa' =>  $value_historialAcademico['programa'],
                            'codprograma' =>$value_historialAcademico['cod_programa']
                        );
                    }
                }

                $data = array(
                    'info' => "con_datos",
                    'historial' => $materias_malla,
                    'semestre' => $semestre
                );
            } else {

                $data = array(
                    'info' => "sin_datos"
                );
            }

            return  $data;
        }
    }

    public function consultaProgramacion()
    {
        $estudiante = $_POST['codBanner'];
        $programacion = DB::table('programacion')->where('codBanner', '=', $estudiante)->get();
        return $programacion;
    }

    public function consultaPorVer()
    {
        $estudiante = $_POST['codBanner'];
        $consultaPorVer = DB::table('materiasPorVer')->where('codBanner', '=', $estudiante)->get();
        return $consultaPorVer;
    }
    
    public function consultaServicio($idBanner)
    {
        // Inicializar cURL
        $ch = curl_init();

        // Establecer la URL a la que quieres hacer la solicitud
        $url = "https://services.ibero.edu.co/utilitary/v1/MoodleAulaVirtual/GetPersonByIdBannerQuery/" . $idBanner;



        // Establecer las opciones de cURL
        curl_setopt($ch, CURLOPT_URL, $url); // Establecer la URL
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Devolver el resultado en lugar de imprimirlo

        // Ejecutar la solicitud y obtener la respuesta
        $response = curl_exec($ch);

        // Verificar si hubo algún error durante la solicitud
        if($response === false) {
            $this->servicio = false;
            echo "Error de URL: " . curl_error($ch);
            // Manejar el error
            return false;
        }else{
            $this->servicio = true;
        }

        // Cerrar la sesión cURL
        curl_close($ch);

        $historialAcademico = json_decode(file_get_contents($url), true);

        // Usar el contenido recibido
        return $historialAcademico;
    }

    public function servicioHistorial($idBanner)
    {
        $consulta = DB::connection('sqlsrv')->table('MAFI_HIST_ACAD')
        ->select('estudiante', 'idBanner AS bannerID', 'programa', 'cod_programa', 'id_curso AS idCurso', 'materia', 'calificacion', 'creditos')
        ->where('IDBANNER',$idBanner)->get()->toArray();

        foreach ($consulta as $dato)
        {
            $array[] = [
                'estudiante' => $dato->estudiante,
                'bannerID' => $dato->bannerID,
                'programa' => $dato->programa,
                'cod_programa' => $dato->cod_programa,
                'idCurso' => $dato->idCurso,
                'materia' => $dato->materia,
                'calificacion' => $dato->calificacion,
                'creditos' => $dato->creditos,
            ];

        }

        return $array;

    }

    public function consultarMalla()
    {
        $codMateria = $_POST['materia'];
        $codPrograma = $_POST['programa'];

        $consulta = DB::table('mallaCurricular')->select('prerequisito', 'equivalencia')->where('codigoCurso', $codMateria)->where('codprograma', $codPrograma)->get();

        $data = [];

        if($consulta){
            $prerequisto = $consulta[0]->prerequisito;
    
            $equivalencia = $consulta[0]->equivalencia;
    
            $arrayPrerequisito = explode(',', $prerequisto);
    
            foreach ($arrayPrerequisito as $key => $value) {
                $arrayPrerequisito[$key] = str_replace('"', '', $value);
            }

            $arrayEquivalencia = explode(',', $equivalencia);

            foreach ($arrayEquivalencia as $key => $value) {
                $arrayEquivalencia[$key] = str_replace('"', '', $value);
            }

            $dataPrerequisito = DB::table('mallaCurricular')->select('codigoCurso','curso','semestre')->whereIn('codigoCurso', $arrayPrerequisito)->where('codprograma', $codPrograma)->get();
            
            $dataEquivalencia = DB::table('mallaCurricular')->select('codigoCurso','curso','semestre')->whereIn('codigoCurso', $arrayEquivalencia)->where('codprograma', $codPrograma)->get();

            foreach($dataPrerequisito as $dato)
            {
                $data['Prerequisito'][$dato->codigoCurso] = [
                    'codigoCurso' => $dato->codigoCurso,
                    'curso' => $dato->curso,
                    'semestre' => $dato->semestre
                ];
            }

            foreach ($dataEquivalencia as $dato)
            {
                $data['Equivalencia'][$dato->codigoCurso] = [
                    'codigoCurso' => $dato->codigoCurso,
                    'curso' => $dato->curso,
                    'semestre' => $dato->semestre
                ];
            }

            return $data;
        }

    }

    public function consultarReglas()
    {
        $codPrograma = $_POST['programa'];

        $consultaReglas = DB::table('descripcion_reglas_programas')->select('*')->where('codprograma', $codPrograma)->get();

        return $consultaReglas;
    }

}
