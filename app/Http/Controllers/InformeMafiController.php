<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CambioPassRequest;
use App\Http\Requests\UsuarioLoginRequest;
use App\Http\Requests\CrearFacultadRequest;
use App\Models\Facultad;
use App\Models\Roles;
use App\Models\User;
use App\Models\Usuario;
use App\Http\Util\Constantes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use DateTime;

class InformeMafiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function comprobacionFecha()
    {
        /* $fechaActual = date("d-m-Y");

        $consultaFecha = DB::table('periodo')->where('activoCiclo1', 1)->select('fechaProgramacionPrimerCiclo', 'fechaInicioPeriodo')->first();

        $fechaInicial = $consultaFecha->fechaProgramacionPrimerCiclo;
        $fechaLimite = $consultaFecha->fechaInicioPeriodo;
        $fechaLimiteFormateada = date('Y-m-d', strtotime($fechaLimite . '-10 days'));

        $fechaActual = date("Y-m-d");

        if ( $fechaInicial > $fechaActual && $fechaActual < $fechaLimiteFormateada ) {
            return true;
        } else {
            return false;
        } */

        $fechaActual = date("Y-m-d H:i:s");
        // $fechaActual = date("2024-06-10 H:i:s");

        $periodos = DB::table('periodo')->where('periodoActivo',1)->get();
        $marcaIngreso = "";
        $primerCiclo = [];
        $segundoCiclo = [];
        foreach ($periodos as $key => $periodo) {            
            if($periodo->activoCiclo1 == 1):
                array_push($primerCiclo,$periodo->periodos);
            elseif ($periodo->activoCiclo2 == 1) :
                array_push($segundoCiclo, $periodo->periodos);
            endif;
            $codPeriodo = substr($periodo->periodos, -2);
            $marcaIngreso .= (int)$periodo->periodos . ",";
            if ($key == 0 || $key == 5) :
                $fechaInicioCiclo1 = $periodo->fechaInicioCiclo1;
                $fechaInicioProyeccion = $periodo->fechaProgramacionPrimerCiclo;
            endif;
        }
        $fechaInicioProyeccion = $fechaInicioProyeccion .' 00:00:00';
        $fechaInicioProyeccion = date('Y-m-d 00:00:00', strtotime($fechaInicioProyeccion . "-1 day"));
        $fechaCierreProyeccion = date("Y-m-d 23:59:59", strtotime($fechaInicioCiclo1 . "- 10 day"));
        $fechaInicioProgramacion = date("Y-m-d 02:00:00", strtotime($fechaInicioCiclo1 . "- 7 day"));
        $fechaCierreProgramacion = date("Y-m-d 23:59:59", strtotime($fechaInicioCiclo1 . "+ 30 day"));
        //dd($fechaActual,$fechaInicioProyeccion,$fechaCierreProyeccion,$fechaInicioProgramacion,$fechaCierreProgramacion);
        //dd($fechaActual >= $fechaInicioProgramacion && $fechaActual < $fechaCierreProgramacion);

        if ($fechaActual >= $fechaInicioProyeccion && $fechaActual < $fechaCierreProyeccion ) {
            return true;
        } elseif($fechaActual >= $fechaInicioProgramacion && $fechaActual < $fechaCierreProgramacion ) {
            return false;
        }
    }

    /**
     * Método que trae los periodos activos
     * @return JSON Retorna un Json con los periodos activos
     */
    public function periodosActivos()
    {
        $tabla = $_POST['tabla'];
        //dd($tabla);

     
        $id_facultad = auth()->user()->id_facultad;
      
        $programa = auth()->user()->programa;

        $periodo = [];

        $consultaFecha = $this->comprobacionFecha();
        //dd($consultaFecha);

        if ($consultaFecha == true) {
            $tablaConsulta = 'planeacion';
        } else {
            $tablaConsulta = 'programacion';
        }
        //dd($tablaConsulta);
        //--- traemos los periodos activos y los ciclos que estan igualmente activos
        $periodosActivosCiclos =  DB::table('periodo')
            ->select('periodos')
            ->where('ver_periodo', 1)
            ->get();


        // Extraer los períodos de la colección y convertirlos en un array
        $periodos = $periodosActivosCiclos->pluck('periodos')->toArray();
        
        //dd($periodos);

    
      


        //-- traemos las facultades
        $facultad_db = DB::table('facultad')->select('nombre', 'id')->where('id', $id_facultad)->where('activo', 1)->get();
      

       //var_dump(empty($programa) && empty($id_facultad));die;
        //-- si no tiene facultad ni programas asignados
        if (empty($programa) && empty($id_facultad)) {
           
            //-- traemos las facultades
            $facultad_db = DB::table('facultad')->select('nombre', 'id')->where('activo', 1)->get();

            $programas_db = DB::table('programas')->select('codprograma', 'programa', 'id', 'Facultad')->whereIn('estado', [1,2])->get();

        }
     

        //--- traemos  los programas y facultades para realizar la consulta
        //dd(isset($id_facultad) && !empty($id_facultad));
        if (isset($programa) && !empty($programa)) {
          
            //-- extraemos  los programas de la base de datos que tienen asignado el usuario
            $program = explode(';', $programa);
            //--- traemos los periodos 
            
            $periodos_van = [];

            //--- y traemos la informacion completa del programa                    
            $programas_db = DB::table('programas')
                ->select('codprograma', 'programa', 'id', 'Facultad')
                ->whereIn('estado', [1,2])
                ->whereIn('id', $program)
                ->get();   

            foreach ($programas_db as $programas_van) {
                $cod_programa[] = $programas_van->codprograma;
            }
         
           
            if ($tabla == 'Mafi') {
                
                    $periodosActivos = DB::connection('mysql')->table('datosMafi_memory')->select('periodo')
                        ->whereIn('codprograma', $cod_programa)
                        ->whereIn('periodo', $periodos) // Usar el array de períodos como filtro IN
                        ->groupBy('periodo')
                        ->get();
                
            } elseif ($tabla == 'planeacion') {
               
                //-- periodos activos en planeacion
                $periodosActivos = DB::table($tablaConsulta)->select('periodo')
                    ->whereIn('codprograma', $cod_programa)
                    ->groupBy('periodo')
                    ->get();

            } elseif ($tabla == 'moodle') {


                //    $periodosActivos=NULL;
                //--- periodos activos en moodle
              /*  $periodosActivos = DB::connection('sqlsrv')
                    ->table('V_Reporte_Ausentismo')
                    ->where(function ($query) use ($cod_programa) {
                        foreach ($cod_programa as $programa) {
                            $query->orWhere('Grupo', 'LIKE', '%' . $programa . '%');
                        }
                    })
                    ->select('Periodo_Rev')
                    ->groupBy('Periodo_Rev')
                    ->get();*/


                  //  if($periodosActivos->isEmpty()){

                        //var_dump("entro");die;
                        $periodosActivos = DB::table('V_Reporte_Ausentismo_memory')
                        ->where(function ($query) use ($cod_programa) {
                            foreach ($cod_programa as $programa) {
                                $query->orWhere('Cod_programa', 'LIKE', '%' . $programa . '%');
                            }
                        })
                        ->select('Periodo_Rev')
                        ->groupBy('Periodo_Rev')
                        ->get();
                       
                       
                  //  }


            } elseif ($tabla== 'moodlecerrados') {

                $periodosActivos = DB::table('cierrematriculas')
                ->where(function ($query) use ($cod_programa) {
                    foreach ($cod_programa as $programa) {
                        $query->orWhere('Programa', 'LIKE', '%' . $programa . '%');
                    }
                })
                ->select('Periodo')
                ->groupBy('Periodo')
                ->get();
            }
         
            //--- organizamos los periodos
            if($periodosActivos){
                foreach ($periodosActivos as $key => $value) {
    

                   // var_dump( $tabla);die;

                    if($tabla == 'moodlecerrados'){
                        $dato = $value->Periodo;
                    }elseif ($tabla == 'Mafi' || $tabla == 'planeacion') {
    
                        $dato = $value->periodo;
                    } else {
    
                        $dato = $value->Periodo_Rev;
                    }
    
                    $dos = substr($dato, -2);
    

                    switch ($dos) {
    
                        case '04':
                        case '05':
                        case '06':
                        case '07':
                        case '08':
    
                            $periodo[$key] = [
                                'nivelFormacion' => 'EDUCACION CONTINUA',
                                'periodo' => $dato
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
    
                            $periodo[$key] = [
                                'nivelFormacion' => 'PROFESIONAL',
                                'periodo' => $dato
                            ];
    
                            break;
                        case '41':
                        case '42':
                        case '43':
                        case '44':
                        case '45':
    
                            $periodo[$key] = [
                                'nivelFormacion' => 'ESPECIALISTA',
                                'periodo' => $dato
                            ];
    
                            break;
    
                        case '51':
                        case '52':
                        case '53':
                        case '54':
                        case '55':
                            
                            $periodo[$key] = [
                                'nivelFormacion' => 'MAESTRIA',
                                'periodo' => $dato
                            ];
    
                            break;
                    }
                }
            }
            
        
        }else if (isset($id_facultad) && !empty($id_facultad)) {
           

            //-- traemos las facultades
            //$facultad_db = DB::table('facultad')->select('nombre','id')->where('id',$id_facultad)->get();
            $id_facultad = trim($id_facultad, ';');
            $id_facultad = explode(';', $id_facultad);
            //dd($id_facultad);

            $facultad_db = DB::table('facultad')->select('nombre', 'id', 'transversal')->whereIn('id', $id_facultad)->get();
            $transversal = $facultad_db[0]->transversal;
            //dd($transversal == 1);

            if ($transversal == 1) {
                $programas_db = DB::table('programas as p')
                    ->join('mallaCurricular as m', 'm.codprograma', '=', 'p.codprograma')
                    ->select('m.codprograma', 'p.programa', 'p.id', 'p.Facultad')
                    ->whereIn('p.estado', [1, 2])
                    ->where('id_facultad_transversal', $facultad_db[0]->id)
                    ->groupBy('m.codprograma', 'p.programa', 'p.id', 'p.Facultad')
                    ->get();

                $cursos_db = DB::table('mallaCurricular')
                    ->select('curso', 'codigoCurso')
                    ->where('id_facultad_transversal', $facultad_db[0]->id)
                    ->groupBy('curso', 'codigoCurso')
                    ->get();

                $nombresCurso = [];
                $codigosCursos= [];

                foreach ($cursos_db as $curso) {
                    $nombre = trim($curso->curso);

                    if (!in_array($nombre, $nombresCurso)) {
                        $nombresCurso[] = $nombre;
                    }
                    $codigosCursos[] = $curso->codigoCurso;
                }
                if($tabla == 'moodle'){

                 // $periodosActivos = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')
                 // ->select('Periodo_Rev')
                 // ->where(function ($query) use ($codigosCursos) {
                 //     foreach ($codigosCursos as $curso) {
                 //         $query->orWhere('Grupo', 'LIKE', '%' . $curso . '%');
                 //     }
                 // })
                 // ->groupBy('Periodo_Rev')
                 // ->get();
                 // $periodosActivos();

                  //  if($periodosActivos->isEmpty()){

                        //var_dump("entro");die;
                        $periodosActivos = DB::table('V_Reporte_Ausentismo_memory')
                        ->select('Periodo_Rev')
                        ->where(function ($query) use ($codigosCursos) {
                            foreach ($codigosCursos as $curso) {
                                $query->orWhere('Cod_materia', 'LIKE', '%' . $curso . '%');
                            }
                        })
                        ->groupBy('Periodo_Rev')
                        ->get();
    
                       
                   // }


                }elseif ($tabla== 'moodlecerrados') {
                    
                    $periodosActivos = DB::table('cierrematriculas')
                    ->where(function ($query) use ($codigosCursos) {
                        foreach ($codigosCursos as $programa) {
                            $query->orWhere('CodMateria', 'LIKE', '%' .$programa . '%');
                        }
                    })
                    ->select('Periodo')
                    ->groupBy('Periodo')
                    ->get();
                 }
              
              
                if($tabla == 'planeacion')
                {
                    //dd($codigosCursos);
                    $periodosActivos = DB::table($tablaConsulta)
                    ->select('periodo')
                    ->whereIn('codMateria', $codigosCursos)
                    ->groupBy('periodo')
                    ->get();

                }
//dd($periodosActivos);
                foreach ($periodosActivos as $key => $value) {

                    if($tabla == 'moodlecerrados'){
                        $dato = $value->Periodo;
                    }elseif ($tabla == 'Mafi' || $tabla == 'planeacion') {

                        $dato = $value->periodo;
                    } else {
    
                        $dato = $value->Periodo_Rev;
                    }
                    
                    $dos = substr($dato, -2);
 
    
                    switch ($dos) {
    
                        case '04':
                        case '05':
                        case '06':
                        case '07':
                        case '08':
    
                            $periodo[$key] = [
                                'nivelFormacion' => 'EDUCACION CONTINUA',
                                'periodo' => $dato
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
    
                            $periodo[$key] = [
                                'nivelFormacion' => 'PROFESIONAL',
                                'periodo' => $dato
                            ];
    
                            break;
                        case '41':
                        case '42':
                        case '43':
                        case '44':
                        case '45':
    
                            $periodo[$key] = [
                                'nivelFormacion' => 'ESPECIALISTA',
                                'periodo' => $dato
                            ];
    
                            break;
    
                        case '51':
                        case '52':
                        case '53':
                        case '54':
                        case '55':
    
                            $periodo[$key] = [
                                'nivelFormacion' => 'MAESTRIA',
                                'periodo' => $dato
                            ];
    
                            break;
                    }
                }

            }

          
            //dd(auth()->user()->id_rol,$id_facultad,$programa);

            if (isset($id_facultad) && empty($programa)) :

             
               
                foreach ($facultad_db as $facultad) :
                    
                    if($facultad->id != 8 && $facultad->id != 9 && $facultad->id != 10){

                    // dd($facultad->id);
                        $programas_db = DB::table('programas')
                            ->select('codprograma', 'programa', 'id', 'Facultad')
                            ->whereIn('estado', [1, 2])
                            ->where('Facultad', $facultad->nombre)
                            ->get();

                        foreach ($programas_db as $programas_van) {
                            $cod_programa[] = $programas_van->codprograma;
                        }

                        $programas_periodos = DB::table('programasPeriodos')
                            ->select('periodo')
                            ->whereIn('codPrograma', $cod_programa)
                            ->whereIn('estado', [1,2])
                            ->groupBy('periodo')
                            ->get();

                        foreach ($programas_periodos as $programas_periodo) {
                            $programas_periodo_act[] = $programas_periodo->periodo;
                        }

                        foreach ($periodosActivosCiclos as $key => $value) {

                            $dos = substr($value->periodos, -2);

                            if (in_array($dos, $programas_periodo_act)) {
                                $periodos_van[] = $dos;
                                $periodos_Activos_Ciclos[$dos] = $value->periodos;
                            }
                        }

                        $programas_periodos = DB::table('programasPeriodos')
                            ->select('codPrograma')
                            ->whereIn('periodo', $periodos_van)
                            ->whereIn('codPrograma', $cod_programa)
                            ->whereIn('estado', [1,2])
                            ->groupBy('codPrograma')
                            ->get();


                           

                        //  dd($programas_periodos);
                        //--- extraemos la informacion que nos devuelbe la base de datos
                        foreach ($programas_periodos as $program) {
                            $pro[] = $program->codPrograma;
                        }
                       
                        $programas_db = DB::table('programas')
                            ->select('codprograma', 'programa', 'id', 'Facultad')
                            ->whereIn('estado', [1, 2])
                            ->whereIn('codprograma', $pro)
                            ->get();

                        $arrayProgramas = [];

                        foreach($programas_db as $key)
                        {
                            $arrayProgramas[] = $key->codprograma; 
                        }

                      

                        if ($tabla == 'Mafi') {

                                $periodosActivos = DB::connection('mysql')->table('datosMafi_memory')->select('periodo')
                                    ->whereIn('codprograma', $cod_programa)
                                    ->whereIn('periodo', $periodos) // Usar el array de períodos como filtro IN
                                    ->groupBy('periodo')
                                    ->get();
                            
                        } elseif ($tabla == 'planeacion') {
                            
                            //-- periodos activos en planeacion
                            $periodosActivos = DB::table($tablaConsulta)->select('periodo')
                                ->whereIn('codprograma', $arrayProgramas)       
                                ->groupBy('periodo')
                                ->get();

                                
            
                        } elseif ($tabla == 'moodle') {
            
                            //--- periodos activos en moodle
                          // $periodosActivos = DB::connection('sqlsrv')
                          //     ->table('V_Reporte_Ausentismo')
                          //     ->where(function ($query) use ($arrayProgramas) {
                          //         foreach ($arrayProgramas as $programa) {
            
                          //             $query->orWhere('Grupo', 'LIKE', '%' . $programa . '%');
                          //         }
                          //     })
                          //     ->select('Periodo_Rev')
                          //     ->groupBy('Periodo_Rev')
                          //     ->get();
                          //     $periodosActivos();

                          //     if($periodosActivos->isEmpty()){

                                    //var_dump("entro");die;
                                    $periodosActivos = DB::table('V_Reporte_Ausentismo_memory')
                                    ->where(function ($query) use ($arrayProgramas) {
                                        foreach ($arrayProgramas as $programa) {
                
                                            $query->orWhere('Cod_programa', 'LIKE', '%' . $programa . '%');
                                        }
                                    })
                                    ->select('Periodo_Rev')
                                    ->groupBy('Periodo_Rev')
                                    ->get();
                                    
                               // }
                        }elseif ($tabla== 'moodlecerrados') {
                           
                            $periodosActivos = DB::table('cierrematriculas')
                            ->where(function ($query) use ($arrayProgramas) {
                                foreach ($arrayProgramas as $programa) {
        
                                    $query->orWhere('Programa', 'LIKE', '%' . $programa . '%');
                                }
                            })
                            ->select('Periodo')
                            ->groupBy('Periodo')
                            ->get();



                         }
                      

                        foreach ($periodosActivos as $key => $value) {
                          
                            if ($tabla == 'moodlecerrados'){
                                
                                $dato = $value->Periodo;
                               
                            }elseif ($tabla == 'Mafi' || $tabla == 'planeacion') {
            
                                $dato = $value->periodo;
                            } else {
            
                                $dato = $value->Periodo_Rev;
                            }
                            
                            $dos = substr($dato, -2);
            
                        
                            switch ($dos) {
            
                                case '04':
                                case '05':
                                case '06':
                                case '07':
                                case '08':
            
                                    $periodo[$key] = [
                                        'nivelFormacion' => 'EDUCACION CONTINUA',
                                        'periodo' => $dato
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
            
                                    $periodo[$key] = [
                                        'nivelFormacion' => 'PROFESIONAL',
                                        'periodo' => $dato
                                    ];
            
                                    break;
                                case '41':
                                case '42':
                                case '43':
                                case '44':
                                case '45':
            
                                    $periodo[$key] = [
                                        'nivelFormacion' => 'ESPECIALISTA',
                                        'periodo' => $dato
                                    ];
            
                                    break;
            
                                case '51':
                                case '52':
                                case '53':
                                case '54':
                                case '55':
            
                                    $periodo[$key] = [
                                        'nivelFormacion' => 'MAESTRIA',
                                        'periodo' => $dato
                                    ];
            
                                    break;
                            }
                        }
                        
                    }

                endforeach;

            endif;
        }

     
     
       
        if (empty($programa) && empty($id_facultad) ) {
           // var_dump($tabla);die;
            //--- traemos los periodos 
            if ($tabla == 'Mafi') {
                

                    // Segunda consulta usando los períodos obtenidos como filtro
                $periodosActivos = DB::connection('mysql')
                ->table('datosMafi_memory')
                ->select('periodo')
                ->whereIn('periodo', $periodos) // Usar el array de períodos como filtro IN
                ->groupBy('periodo')
                ->get();
          
                
            } elseif ($tabla == 'planeacion') {
                //-- periodos activos en planeacion
                $periodosActivos = DB::table($tablaConsulta)->select('periodo')->groupBy('periodo')->get();
                //dd($periodosActivos,"ento");die;
            } elseif ($tabla == 'moodle') {
                
                //--- periodos activos en moodle
              //  $periodosActivos = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')->select('Periodo_Rev')->groupBy('Periodo_Rev')->get();
              //  $periodosActivos();
   //
              //  if($periodosActivos->isEmpty()){

                    //var_dump("entro");die;
                    $periodosActivos = DB::table('V_Reporte_Ausentismo_memory')->select('Periodo_Rev')->groupBy('Periodo_Rev')->get();
               // }
              // var_dump($periodosActivos);die;
            }elseif ($tabla== 'moodlecerrados') {

                $periodosActivos = DB::table('cierrematriculas')->select('Periodo')->groupBy('Periodo')->get();
                //var_dump($periodosActivos);die;
             }
          
     
            //--- organizamos los periodos
            foreach ($periodosActivos as $key => $value) {
                
                if($tabla == 'moodlecerrados'){
                    //var_dump( $value->Periodo);die;
                    $dato = $value->Periodo;
                }elseif ($tabla == 'Mafi' || $tabla == 'planeacion') {
                   
                    $dato = $value->periodo;
                } else {

                    $dato = $value->Periodo_Rev;
                }

                $dos = substr($dato, -2);

               // var_dump($dos);die;
                switch ($dos) {

                    case '04':
                    case '05':
                    case '06':
                    case '07':
                    case '08':

                        $periodo[$key] = [
                            'nivelFormacion' => 'EDUCACION CONTINUA',
                            'periodo' => $dato
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

                        $periodo[$key] = [
                            'nivelFormacion' => 'PROFESIONAL',
                            'periodo' => $dato
                        ];

                        break;
                    case '41':
                    case '42':
                    case '43':
                    case '44':
                    case '45':

                        $periodo[$key] = [
                            'nivelFormacion' => 'ESPECIALISTA',
                            'periodo' => $dato
                        ];

                        break;

                    case '51':
                    case '52':
                    case '53':
                    case '54':
                    case '55':

                        $periodo[$key] = [
                            'nivelFormacion' => 'MAESTRIA',
                            'periodo' => $dato
                        ];

                        break;
                }
            }
        }

        if (isset($cursos_db) && !empty($cursos_db)) {
            $data = [
                'periodo'   =>  $periodo,
                'facultades' =>  $facultad_db,
                'programas' =>  $programas_db,
                'nombresCursos' => $nombresCurso,
                'cursos' => $cursos_db,
            ];
        } else {
            //--- 
            $data = [
                'periodo'   =>  $periodo,
                'facultades' =>  $facultad_db,
                'programas' =>  $programas_db,
            ];
        }
        //dd( $data);
        return $data;
    }

    /**
     * Método que trae los estudiantes activos de toda la Ibero
     * @return JSON retorna los estudiantes agrupados en activos e inactivos
     */
    public function estudiantesActivosGeneral()
    {
        /**
         * SELECT COUNT(estado) AS TOTAL, estado FROM `datosMafi`
         *GROUP BY estado
         */
        $periodos = DB::table('periodo')
            ->where('ver_periodo', 1)
            ->pluck('periodos')
            ->unique()
            ->toArray();

        $estudiantes = DB::connection('mysql')->table('datosMafi_memory')
            ->whereIn('periodo', $periodos)
            ->select(DB::raw('COUNT(estado) AS TOTAL, estado'))
            ->groupBy('estado')
            ->get();

        header("Content-Type: application/json");
        echo json_encode(array('data' => $estudiantes));
    }

    /**
     * Método que muestra el estado del sello financiero de todos los estudiantes
     * @return JSON retorna los estudiantes agrupados según su sello financiero
     */
    public function selloEstudiantesActivos($tabla)
    {

        $tabla = trim($tabla);
        $periodos = DB::table('periodo')
            ->where('ver_periodo', 1)
            ->pluck('periodos')
            ->unique()
            ->toArray();

        if ($tabla == 'Mafi') {
            /**
             * SELECT COUNT(sello) AS TOTAL, sello FROM `datosMafi`
             *GROUP BY sello
             */
            $consulta = DB::connection('mysql')->table('datosMafi_memory')
                ->where('ESTADO', 'Activo')
                ->whereIn('periodo', $periodos)
                ->select('sello', 'autorizado_asistir')
                ->get();
        }

        $selloFinanciero = 0;
        $Retencion = 0;
        $ASP = 0;
        $Vacio = 0;

        foreach ($consulta as $dato) {
            $sello = $dato->sello;
            $estado = $dato->autorizado_asistir;

            if ($sello == 'TIENE SELLO FINANCIERO') {
                $selloFinanciero += 1;
            }

            if ($sello == 'TIENE RETENCION' && empty($estado)) {
                $Retencion += 1;
            }

            if ($sello == 'TIENE RETENCION' && !empty($estado)) {
                $ASP += 1;
            }

            if ($sello == 'NO EXISTE') {
                $Vacio += 1;
            }
        }

        if ($Vacio != 0) {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP,
                'NO EXISTE' => $Vacio
            ];
        } else {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP
            ];
        }

        return $data;
    }

    /**
     * Método que trae los estudiantes con retención
     * @return JSON retorna los estudiantes que tienen retención agrupados según 'autorizado_asistir'
     */
    public function estudiantesRetencion($tabla)
    {
        $tabla = trim($tabla);

        $periodos = DB::table('periodo')
            ->where('ver_periodo', 1)
            ->pluck('periodos')
            ->unique()
            ->toArray();

        if ($tabla == "Mafi") {
            $retencion = DB::connection('mysql')->table('datosMafi_memory')
                ->where('sello', 'TIENE RETENCION')
                ->where('estado', 'Activo')
                ->whereIn('periodo', $periodos)
                ->select(DB::raw('COUNT(autorizado_asistir) AS TOTAL, autorizado_asistir'))
                ->groupBy('autorizado_asistir')
                ->get();
        }

        if ($tabla == 'planeacion') {
            $retencion = DB::table('estudiantes')
                ->select(DB::raw('COUNT(autorizado_asistir) as TOTAL'), 'autorizado_asistir')
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->where('estado', 'Activo')
                ->where('sello', 'TIENE RETENCION')
                ->whereIn('marca_ingreso', $periodos)
                ->groupBy('autorizado_asistir')
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $retencion));
    }

    /**
     * Método que muestra el sello de los estudiantes de primer ingreso 
     * @return JSON retorna los estudiantes de primer ingreso, agrupados por sello
     */
    public function estudiantesPrimerIngreso($tabla)
    {
        $tabla = trim($tabla);
        $tiposEstudiante = [
            'PRIMER INGRESO',
            'PRIMER INGRESO PSEUDO INGRES',
            'TRANSFERENTE EXTERNO',
            'TRANSFERENTE EXTERNO (ASISTEN)',
            'TRANSFERENTE EXTERNO PSEUD ING',
            'TRANSFERENTE INTERNO',
        ];

        $periodos = DB::table('periodo')
            ->where('ver_periodo', 1)
            ->pluck('periodos')
            ->unique()
            ->toArray();

        if ($tabla == "Mafi") {
            $consulta = DB::connection('mysql')->table('datosMafi_memory')
                ->where('ESTADO', 'Activo')
                ->whereIn('periodo', $periodos)
                ->whereIn('tipoestudiante', $tiposEstudiante)
                ->select('sello', 'autorizado_asistir')
                ->get();
        }

        if ($tabla == "planeacion") {
            $consulta = DB::table('estudiantes')
                ->whereIn('tipo_estudiante', $tiposEstudiante)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->select('homologante', 'sello', 'autorizado_asistir')
                ->get();
        }
        $selloFinanciero = 0;
        $Retencion = 0;
        $ASP = 0;
        $Vacio = 0;

        foreach ($consulta as $dato) {
            $sello = $dato->sello;
            $estado = $dato->autorizado_asistir;

            if ($sello == 'TIENE SELLO FINANCIERO') {
                $selloFinanciero += 1;
            }

            if ($sello == 'TIENE RETENCION' && empty($estado)) {
                $Retencion += 1;
            }

            if ($sello == 'TIENE RETENCION' && !empty($estado)) {
                $ASP += 1;
            }

            if ($sello == 'NO EXISTE') {
                $Vacio += 1;
            }
        }

        if ($Vacio != 0) {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP,
                'NO EXISTE' => $Vacio
            ];
        } else {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP
            ];
        }

        return $data;
    }

    public function estudiantesAntiguos($tabla)
    {
        $tabla = trim($tabla);
        $tiposEstudiante = [
            'PRIMER INGRESO',
            'PRIMER INGRESO PSEUDO INGRES',
            'TRANSFERENTE EXTERNO',
            'TRANSFERENTE EXTERNO (ASISTEN)',
            'TRANSFERENTE EXTERNO PSEUD ING',
            'TRANSFERENTE INTERNO',
        ];

        $periodos = DB::table('periodo')
            ->where('ver_periodo', 1)
            ->pluck('periodos')
            ->unique()
            ->toArray();

        if ($tabla == "Mafi") {
            $consulta = DB::connection('mysql')->table('datosMafi_memory')
                ->where('ESTADO', 'Activo')
                ->whereIn('periodo', $periodos)
                ->whereNotIn('tipoestudiante', $tiposEstudiante)
                ->select('sello', 'autorizado_asistir')
                ->get();
        }

        if ($tabla == "planeacion") {
            $consulta = DB::table('estudiantes')
                ->whereNotIn('tipo_estudiante', $tiposEstudiante)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('marca_ingreso', $periodos)
                ->where('estado', 'Activo')
                ->select('homologante', 'sello', 'autorizado_asistir')
                ->get();
        }

        $selloFinanciero = 0;
        $Retencion = 0;
        $ASP = 0;
        $Vacio = 0;

        foreach ($consulta as $dato) {
            $sello = $dato->sello;
            $estado = $dato->autorizado_asistir;

            if ($sello == 'TIENE SELLO FINANCIERO') {
                $selloFinanciero += 1;
            }

            if ($sello == 'TIENE RETENCION' && empty($estado)) {
                $Retencion += 1;
            }

            if ($sello == 'TIENE RETENCION' && !empty($estado)) {
                $ASP += 1;
            }

            if ($sello == 'NO EXISTE') {
                $Vacio += 1;
            }
        }

        if ($Vacio != 0) {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP,
                'NO EXISTE' => $Vacio
            ];
        } else {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP
            ];
        }

        return $data;
    }

    /**
     * Método que trae todos los 5 tipos de estudiantes con mayor cantidad de datos
     * @return JSON retorna todos los tipos de estudiantes
     */
    public function tiposEstudiantes($tabla)
    {
        $tabla = trim($tabla);
        $periodos = DB::table('periodo')
            ->where('ver_periodo', 1)
            ->pluck('periodos')
            ->unique()
            ->toArray();
        if ($tabla == "Mafi") {

            $tipoEstudiantes = DB::connection('mysql')->table('datosMafi_memory')
                ->where('ESTADO', 'Activo')
                ->whereIn('periodo', $periodos)
                ->select(DB::raw('COUNT(tipoestudiante) AS TOTAL, tipoestudiante'))
                ->groupBy('tipoestudiante')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }
        if ($tabla == "planeacion") {
            $tipoEstudiantes = DB::table('estudiantes')
                ->selectRaw('COUNT(homologante) as TOTAL, tipo_estudiante')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->where('estado', 'Activo')
                ->groupBy('tipo_estudiante')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $tipoEstudiantes));
    }

    /**
     * Método que muestra los 5 operadores que mas estudiantes traen
     * @return JSON retorna un JSON con estos 5 operadores, agrupados por operador
     */
    public function operadores($tabla)
    {
        $periodos = DB::table('periodo')
            ->where('ver_periodo', 1)
            ->pluck('periodos')
            ->unique()
            ->toArray();
        $tabla = trim($tabla);
        if ($tabla == "Mafi") {
            $operadores = DB::connection('mysql')->table('datosMafi_memory')
                ->where('ESTADO', 'Activo')
                ->whereIn('periodo', $periodos)
                ->select(DB::raw('COUNT(operador) AS TOTAL, operador'))
                ->groupBy('operador')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }

        if ($tabla == "planeacion") {
            $operadores = DB::table('estudiantes')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->where('estado', 'Activo')
                ->selectRaw('COUNT(homologante) as TOTAL, operador')
                ->groupBy('operador')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $operadores));
    }

    /**
     * Método que muestra los 5 programas con mayor cantidad de estudiantes inscritos
     * @return JSON retorna un JSON con estos 5 programas, agrupados por programa
     */

    public function estudiantesProgramas($tabla)
    {
        $tabla = trim($tabla);

        $periodos = DB::table('periodo')
            ->where('ver_periodo', 1)
            ->pluck('periodos')
            ->unique()
            ->toArray();

        if ($tabla == 'Mafi') {

            $programas = DB::connection('mysql')->table('datosMafi_memory')
                ->where('ESTADO', 'Activo')
                ->whereIn('periodo', $periodos)
                ->select(DB::raw('COUNT(codprograma) AS TOTAL, codprograma'))
                ->groupBy('codprograma')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }

        if ($tabla == 'planeacion') {

            $programas = DB::table('estudiantes')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->where('estado', 'Activo')
                ->selectRaw('COUNT(DISTINCT homologante) as TOTAL, programa')
                ->groupBy('programa')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $programas));
    }

    /**
     * Método que trae los estudiantes activos e inactivos de las facultades seleccionadas por el usuario
     * @return JSON retorna los estudiantes agrupados en activos e inactivos
     */
    public function estudiantesActivosFacultad(Request $request)
    {
        /**
         *SELECT  COUNT(dm.ESTADO) AS TOTAL, dm.ESTADO, p.Facultad FROM `datosMafi` dm
         *INNER JOIN programas p ON p.codprograma = dm.programa
         *WHERE p.Facultad IN ('') -- Reemplaza con las facultades específicas
         *GROUP BY dm.ESTADO
         */
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');

        $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();

        $estudiantes = DB::connection('sqlsrv')->table('MAFI  as dm')
            ->whereIn('dm.PERIODO', $periodos)
            ->whereIn('dm.codprograma', $programas)
            ->select(DB::raw('COUNT(dm.idbanner) AS TOTAL'), 'dm.estado')
            ->groupBy('dm.estado')
            ->get();

        header("Content-Type: application/json");
        echo json_encode(array('data' => $estudiantes));
    }

    /**
     * Método que muestra el estado del sello financiero de los estudiantes de las facultades seleccionadas por el usuario
     * @return JSON retorna los estudiantes agrupados según su sello financiero
     */
    public function selloEstudiantesFacultad(Request $request, $tabla)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);
        $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        //dd($programas);



        if ($tabla == "Mafi") {

            $consulta = DB::connection('sqlsrv')->table('MAFI  as dm')
                ->whereIn('dm.PERIODO', $periodos)
                ->whereIn('dm.codprograma', $programas)
                ->where('dm.ESTADO', 'Activo')
                ->select('dm.sello', 'dm.autorizado_asistir')
                ->get();
        }

        if ($tabla == "planeacion") {

            $consulta = DB::table('estudiantes')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->where('estado', 'Activo')
                ->select('homologante', 'sello', 'autorizado_asistir')
                ->whereIn('programa', $programas)
                ->get();
        }

        $selloFinanciero = 0;
        $Retencion = 0;
        $ASP = 0;
        $Vacio = 0;

        foreach ($consulta as $dato) {
            $sello = $dato->sello;
            $estado = $dato->autorizado_asistir;

            if ($sello == 'TIENE SELLO FINANCIERO') {
                $selloFinanciero += 1;
            }

            if ($sello == 'TIENE RETENCION' && empty($estado)) {
                $Retencion += 1;
            }

            if ($sello == 'TIENE RETENCION' && !empty($estado)) {
                $ASP += 1;
            }

            if ($sello == 'NO EXISTE') {
                $Vacio += 1;
            }
        }

        if ($Vacio != 0) {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP,
                'NO EXISTE' => $Vacio
            ];
        } else {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP
            ];
        }

        return $data;
    }

    /**
     * Método que trae los estudiantes con retención de las facultades seleccionadas por el usuario
     * @return JSON retorna los estudiantes que tienen retención agrupados según 'autorizado_asistir'
     */
    public function retencionEstudiantesFacultad(Request $request, $tabla)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);
        $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();

        if ($tabla == "Mafi") {
            /**
             ** SELECT COUNT(dm.autorizado_asistir) AS TOTAL, dm.**autorizado_asistir FROM datosMafi dm
             ** INNER JOIN programas p ON p.codprograma = dm.codprograma
             ** WHERE p.Facultad IN ('') AND dm.periodo IN ('')
             ** WHERE dm.sello = 'TIENE RETENCION' 
             ** GROUP BY dm.autorizado_asistir
             */
            $retencion = DB::connection('sqlsrv')->table('MAFI  as dm')
                ->where('dm.ESTADO', 'Activo')
                ->whereIn('dm.PERIODO', $periodos)
                ->whereIn('dm.codprograma', $programas)
                ->where('dm.sello', 'TIENE RETENCION')
                ->select(DB::raw('COUNT(dm.autorizado_asistir) AS TOTAL, dm.autorizado_asistir'))
                ->groupBy('dm.autorizado_asistir')
                ->orderByDesc('TOTAL')
                ->get();
        }

        if ($tabla == "planeacion") {
            $retencion = DB::table('estudiantes')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('programa', $programas)
                ->where('estado', 'Activo')
                ->where('sello', 'TIENE RETENCION')
                ->selectRaw('COUNT(homologante) as TOTAL, autorizado_asistir')
                ->groupBy('autorizado_asistir')
                ->orderByDesc('TOTAL')
                ->get();
        }
        header("Content-Type: application/json");
        echo json_encode(array('data' => $retencion));
    }

    /**
     * Método que muestra el sello de los estudiantes de primer ingreso de las facultades seleccionadas por el usuario
     * @return JSON retorna los estudiantes de primer ingreso, agrupados por sello
     */
    public function primerIngresoEstudiantesFacultad(Request $request, $tabla)
    {

        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);

        $tiposEstudiante = [
            'PRIMER INGRESO',
            'PRIMER INGRESO PSEUDO INGRES',
            'TRANSFERENTE EXTERNO',
            'TRANSFERENTE EXTERNO (ASISTEN)',
            'TRANSFERENTE EXTERNO PSEUD ING',
            'TRANSFERENTE INTERNO',
        ];

        $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();

        if ($tabla == "Mafi") {

            $consulta = DB::connection('sqlsrv')->table('MAFI  as dm')
                ->where('dm.ESTADO', 'Activo')
                ->whereIn('dm.PERIODO', $periodos)
                ->whereIn('dm.codprograma', $programas)
                ->whereIn('dm.tipoestudiante', $tiposEstudiante)
                ->select('dm.sello', 'dm.autorizado_asistir')
                ->get();
        }

        if ($tabla == "planeacion") {
            $consulta = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('programa', $programas)
                ->whereIn('tipo_estudiante', $tiposEstudiante)
                ->select('homologante', 'sello', 'autorizado_asistir')

                ->get();
        }
        $selloFinanciero = 0;
        $Retencion = 0;
        $ASP = 0;
        $Vacio = 0;

        foreach ($consulta as $dato) {
            $sello = $dato->sello;
            $estado = $dato->autorizado_asistir;

            if ($sello == 'TIENE SELLO FINANCIERO') {
                $selloFinanciero += 1;
            }

            if ($sello == 'TIENE RETENCION' && empty($estado)) {
                $Retencion += 1;
            }

            if ($sello == 'TIENE RETENCION' && !empty($estado)) {
                $ASP += 1;
            }

            if ($sello == 'NO EXISTE') {
                $Vacio += 1;
            }
        }

        if ($Vacio != 0) {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP,
                'NO EXISTE' => $Vacio
            ];
        } else {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP
            ];
        }

        return $data;
    }

    public function estudiantesAntiguosFacultad(Request $request, $tabla)
    {

        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);

        $tiposEstudiante = [
            'PRIMER INGRESO',
            'PRIMER INGRESO PSEUDO INGRES',
            'TRANSFERENTE EXTERNO',
            'TRANSFERENTE EXTERNO (ASISTEN)',
            'TRANSFERENTE EXTERNO PSEUD ING',
            'TRANSFERENTE INTERNO',
        ];

        $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();

        if ($tabla == "Mafi") {
            $consulta = DB::connection('sqlsrv')->table('MAFI  as dm')
                ->where('dm.ESTADO', 'Activo')
                ->whereIn('dm.PERIODO', $periodos)
                ->whereIn('dm.codprograma', $programas)
                ->whereNotIn('dm.tipoestudiante', $tiposEstudiante)
                ->select('dm.sello', 'dm.autorizado_asistir')
                ->get();
        }
        if ($tabla == "planeacion") {
            $consulta = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('programa', $programas)
                ->whereNotIn('tipo_estudiante', $tiposEstudiante)
                ->select('homologante', 'sello', 'autorizado_asistir')
                ->get();
        }

        $selloFinanciero = 0;
        $Retencion = 0;
        $ASP = 0;
        $Vacio = 0;

        foreach ($consulta as $dato) {
            $sello = $dato->sello;
            $estado = $dato->autorizado_asistir;

            if ($sello == 'TIENE SELLO FINANCIERO') {
                $selloFinanciero += 1;
            }

            if ($sello == 'TIENE RETENCION' && empty($estado)) {
                $Retencion += 1;
            }

            if ($sello == 'TIENE RETENCION' && !empty($estado)) {
                $ASP += 1;
            }

            if ($sello == 'NO EXISTE') {
                $Vacio += 1;
            }
        }

        if ($Vacio != 0) {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP,
                'NO EXISTE' => $Vacio
            ];
        } else {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP
            ];
        }

        return $data;
    }


    /**
     * Método que muestra los 5 tipos de estudiantes con mayor cantidad de datos, de algunas facultades en específico
     * @return JSON retorna los tipos de estudiantes, agrupados por tipo de estudiante
     */
    public function tiposEstudiantesFacultad(Request $request, $tabla)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);


        $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();

        if ($tabla == "Mafi") {
            $tipoEstudiantes = DB::connection('sqlsrv')->table('MAFI  as dm')
                ->where('dm.ESTADO', 'Activo')
                ->whereIn('dm.PERIODO', $periodos)
                ->whereIn('dm.codprograma', $programas)
                ->select(DB::raw('COUNT(dm.tipoestudiante) AS TOTAL, dm.tipoestudiante'))
                ->groupBy('dm.tipoestudiante')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }

        if ($tabla == "planeacion") {
            $tipoEstudiantes = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('programa', $programas)
                ->selectRaw('COUNT(homologante) as TOTAL, tipo_estudiante')
                ->groupBy('tipo_estudiante')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $tipoEstudiantes));
    }

    /**
     * Método que muestra los tipos de estudiantes de las facultades seleccionadas por el usuario
     * @return JSON retorna un JSON con estos 5 operadores, agrupados por operador
     */
    public function operadoresFacultad(Request $request, $tabla)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);

        $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();

        if ($tabla == "Mafi") {
            $operadores = DB::connection('sqlsrv')->table('MAFI  as dm')
                ->where('dm.ESTADO', 'Activo')
                ->whereIn('dm.PERIODO', $periodos)
                ->whereIn('dm.codprograma', $programas)
                ->select(DB::raw('COUNT(dm.operador) AS TOTAL, dm.operador'))
                ->groupBy('dm.operador')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }

        if ($tabla == "planeacion") {
            $operadores = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('programa', $programas)
                ->selectRaw('COUNT(homologante) as TOTAL, operador')
                ->groupBy('operador')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $operadores));
    }

    /**
     * Método que muestra los 5 programas con mayor cantidad de estudiantes inscritos de las facultades seleccionadas por el usuario
     * @return JSON retorna un JSON con estos 5 programas, agrupados por programa
     */

    public function estudiantesProgramasFacultad(Request $request, $tabla)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);

        $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();

        if ($tabla == "Mafi") {
            $programas = DB::connection('sqlsrv')->table('MAFI  as dm')
                ->where('dm.ESTADO', 'Activo')
                ->whereIn('dm.PERIODO', $periodos)
                ->whereIn('dm.codprograma', $programas)
                ->select(DB::raw('COUNT(dm.codprograma) AS TOTAL, dm.codprograma'))
                ->groupBy('dm.codprograma')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }

        if ($tabla == "planeacion") {
            $programas = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('programa', $programas)
                ->selectRaw('COUNT(homologante) as TOTAL, programa')
                ->groupBy('programa')
                ->orderBy('TOTAL', 'DESC')
                ->limit(5)
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $programas));
    }

    /**
     * Métodos para gráficos de programas
     */

    /**
     * Método que trae los estudiantes activos e inactivos de las facultades seleccionadas por el usuario
     * @return JSON retorna los estudiantes agrupados en activos e inactivos
     */
    public function estudiantesActivosPrograma(Request $request)
    {
        /**
         * SELECT  COUNT(estado) AS TOTAL, estado FROM `datosMafi`
         *WHERE programa IN ('') -- Reemplaza con los programas específicos
         *GROUP BY estado
         */
        $programas = $request->input('programa');
        $periodos = $request->input('periodos');

        $estudiantes = DB::connection('mysql')->table('datosMafi_memory')
            ->whereIn('periodo', $periodos)
            ->whereIn('codprograma', $programas)
            ->select(DB::raw('COUNT(estado) AS TOTAL'), 'estado')
            ->groupBy('estado')
            ->get();



        header("Content-Type: application/json");
        echo json_encode(array('data' => $estudiantes));
    }

    /**
     * Método que muestra el estado del sello financiero de los estudiantes de los programas seleccionados por el usuario
     * @return JSON retorna los estudiantes agrupados según su sello financiero
     */
    public function selloEstudiantesPrograma(Request $request, $tabla)
    {
        $programas = $request->input('programa');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);
        $consulta = [];

        if ($tabla == "Mafi") {
            $consulta = DB::connection('mysql')->table('datosMafi_memory')
                ->where('ESTADO', 'Activo')
                ->whereIn('periodo', $periodos)
                ->whereIn('codprograma', $programas)
                ->select('sello', 'autorizado_asistir')
                ->get();
        }

        $selloFinanciero = 0;
        $Retencion = 0;
        $ASP = 0;
        $Vacio = 0;

        foreach ($consulta as $dato) {
            $sello = $dato->sello;
            $estado = $dato->autorizado_asistir;

            if ($sello == 'TIENE SELLO FINANCIERO') {
                $selloFinanciero += 1;
            }

            if ($sello == 'TIENE RETENCION' && empty($estado)) {
                $Retencion += 1;
            }

            if ($sello == 'TIENE RETENCION' && !empty($estado)) {
                $ASP += 1;
            }

            if ($sello == 'NO EXISTE') {
                $Vacio += 1;
            }
        }

        if ($Vacio != 0) {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP,
                'NO EXISTE' => $Vacio
            ];
        } else {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP
            ];
        }

        return $data;
    }

    /**
     * Método que trae los estudiantes con retención de los programas seleccionados por el usuario
     * @return JSON retorna los estudiantes que tienen retención agrupados según 'autorizado_asistir'
     */
    public function retencionEstudiantesPrograma(Request $request, $tabla)
    {
        $programas = $request->input('programa');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);

        if ($tabla == "Mafi") {
            /**
             * SELECT COUNT(autorizado_asistir) AS TOTAL, autorizado_asistir FROM datosMafi
             *WHERE programa IN ('') -- Reemplaza con los programas específicos
             *WHERE sello = 'TIENE RETENCION' 
             *GROUP BY autorizado_asistir
             */
            $retencion = DB::connection('mysql')->table('datosMafi_memory')
                ->where('ESTADO', 'Activo')
                ->whereIn('periodo', $periodos)
                ->whereIn('codprograma', $programas)
                ->where('sello', 'TIENE RETENCION')
                ->select(DB::raw('COUNT(autorizado_asistir) AS TOTAL, autorizado_asistir'))
                ->groupBy('autorizado_asistir')
                ->get();
        }
        if ($tabla == "planeacion") {
            $retencion = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('programa', $programas)
                ->where('sello', 'TIENE RETENCION')
                ->selectRaw('COUNT(homologante) as TOTAL, autorizado_asistir')
                ->groupBy('autorizado_asistir')
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $retencion));
    }

    /**
     * Método que muestra el sello de los estudiantes de primer ingreso de los programas seleccionados por el usuario
     * @return JSON retorna los estudiantes de primer ingreso, agrupados por sello
     */
    public function primerIngresoEstudiantesPrograma(Request $request, $tabla)
    {
        $programas = $request->input('programa');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);

        $tiposEstudiante = [
            'PRIMER INGRESO',
            'PRIMER INGRESO PSEUDO INGRES',
            'TRANSFERENTE EXTERNO',
            'TRANSFERENTE EXTERNO (ASISTEN)',
            'TRANSFERENTE EXTERNO PSEUD ING',
            'TRANSFERENTE INTERNO',
            'REINGRESO'
        ];

        if ($tabla == "Mafi") {
            /**
             * SELECT COUNT(sello) AS TOTAL, sello
             *FROM datosMafi
             *WHERE programa IN ('') -- Reemplaza con los programas específicos
             *AND tipoestudiante = 'PRIMER INGRESO'
             *GROUP BY sello;
             */

            $consulta = DB::connection('mysql')->table('datosMafi_memory')
                ->where('ESTADO', 'Activo')
                ->whereIn('periodo', $periodos)
                ->whereIn('codprograma', $programas)
                ->whereIn('tipoestudiante', $tiposEstudiante)
                ->select('sello', 'autorizado_asistir')
                ->get();
        }
        if ($tabla == "planeacion") {
            $consulta = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('programa', $programas)
                ->whereIn('tipo_estudiante', $tiposEstudiante)
                ->select('homologante', 'sello', 'autorizado_asistir')
                ->get();
        }

        $selloFinanciero = 0;
        $Retencion = 0;
        $ASP = 0;
        $Vacio = 0;

        foreach ($consulta as $dato) {
            $sello = $dato->sello;
            $estado = $dato->autorizado_asistir;

            if ($sello == 'TIENE SELLO FINANCIERO') {
                $selloFinanciero += 1;
            }

            if ($sello == 'TIENE RETENCION' && empty($estado)) {
                $Retencion += 1;
            }

            if ($sello == 'TIENE RETENCION' && !empty($estado)) {
                $ASP += 1;
            }

            if ($sello == 'NO EXISTE') {
                $Vacio += 1;
            }
        }

        if ($Vacio != 0) {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP,
                'NO EXISTE' => $Vacio
            ];
        } else {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP
            ];
        }

        return $data;
    }

    public function estudiantesAntiguosPrograma(Request $request, $tabla)
    {
        $programas = $request->input('programa');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);

        $tiposEstudiante = [
            'PRIMER INGRESO',
            'PRIMER INGRESO PSEUDO INGRES',
            'TRANSFERENTE EXTERNO',
            'TRANSFERENTE EXTERNO (ASISTEN)',
            'TRANSFERENTE EXTERNO PSEUD ING',
            'TRANSFERENTE INTERNO',
            'REINGRESO'
        ];

        if ($tabla == "Mafi") {
            $consulta = DB::connection('mysql')->table('datosMafi_memory')
                ->where('ESTADO', 'Activo')
                ->whereIn('periodo', $periodos)
                ->whereIn('codprograma', $programas)
                ->whereNotIn('tipoestudiante', $tiposEstudiante)
                ->select('sello', 'autorizado_asistir')
                ->get();
        }

        if ($tabla == "planeacion") {
            $consulta = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('programa', $programas)
                ->whereNotIn('tipo_estudiante', $tiposEstudiante)
                ->select('homologante', 'sello', 'autorizado_asistir')
                ->get();
        }

        $selloFinanciero = 0;
        $Retencion = 0;
        $ASP = 0;
        $Vacio = 0;

        foreach ($consulta as $dato) {
            $sello = $dato->sello;
            $estado = $dato->autorizado_asistir;

            if ($sello == 'TIENE SELLO FINANCIERO') {
                $selloFinanciero += 1;
            }

            if ($sello == 'TIENE RETENCION' && empty($estado)) {
                $Retencion += 1;
            }

            if ($sello == 'TIENE RETENCION' && !empty($estado)) {
                $ASP += 1;
            }

            if ($sello == 'NO EXISTE') {
                $Vacio += 1;
            }
        }

        if ($Vacio != 0) {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP,
                'NO EXISTE' => $Vacio
            ];
        } else {
            $data = [
                'CON SELLO' => $selloFinanciero,
                'TIENE RETENCION' => $Retencion,
                'ASP' => $ASP
            ];
        }

        return $data;
    }


    /**
     * Método que muestra los 5 tipos de estudiantes con mayor cantidad de datos de los programas seleccionados por el usuario
     * @return JSON retorna los tipos de estudiantes, agrupados por tipo de estudiante
     */
    public function tiposEstudiantesPrograma(Request $request, $tabla)
    {
        $programas = $request->input('programa');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);


        if ($tabla == "Mafi") {
            /**
             * SELECT COUNT(tipoestudiante) AS 'TOTAL', tipoestudiante
             * FROM datosMafi
             * WHERE programa IN ('') -- Reemplaza con los programas específicos
             * GROUP BY tipoestudiante
             */

            $tipoEstudiantes = DB::connection('mysql')->table('datosMafi_memory')
                ->where('ESTADO', 'Activo')
                ->whereIn('periodo', $periodos)
                ->whereIn('codprograma', $programas)
                ->select(DB::raw('COUNT(tipoestudiante) AS TOTAL, tipoestudiante'))
                ->groupBy('tipoestudiante')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }

        if ($tabla == "planeacion") {
            $tipoEstudiantes = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('programa', $programas)
                ->selectRaw('COUNT(homologante) as TOTAL, tipo_estudiante')
                ->groupBy('tipo_estudiante')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $tipoEstudiantes));
    }

    /**
     * Método que muestra los tipos de estudiantes de los programas seleccionados por el usuario
     * @return JSON retorna un JSON con estos 5 operadores, agrupados por operador
     */
    public function operadoresPrograma(Request $request, $tabla)
    {
        $programas = $request->input('programa');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);

        if ($tabla == "Mafi") {
            $operadores = DB::connection('mysql')->table('datosMafi_memory')
                ->where('ESTADO', 'Activo')
                ->whereIn('periodo', $periodos)
                ->whereIn('codprograma', $programas)
                ->select(DB::raw('COUNT(operador) AS TOTAL, operador'))
                ->groupBy('operador')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }

        if ($tabla == "planeacion") {
            $operadores = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('programa', $programas)
                ->selectRaw('COUNT(homologante) as TOTAL, operador')
                ->groupBy('operador')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }


        header("Content-Type: application/json");
        echo json_encode(array('data' => $operadores));
    }

    public function estudiantesPrograma(Request $request, $tabla)
    {
        $programas = $request->input('programa');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);

        if ($tabla == "Mafi") {
            $programas = DB::connection('sqlsrv')->table('MAFI  as dm')
                ->where('dm.ESTADO', 'Activo')
                ->whereIn('dm.PERIODO', $periodos)
                ->whereIn('dm.codprograma', $programas)
                ->select(DB::raw('COUNT(dm.codprograma) AS TOTAL, dm.codprograma'))
                ->groupBy('dm.codprograma')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }

        if ($tabla == "planeacion") {
            $programas = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('programa', $programas)
                ->selectRaw('COUNT(homologante) as TOTAL, programa')
                ->groupBy('programa')
                ->orderBy('TOTAL', 'DESC')
                ->limit(5)
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $programas));
    }

    public function estudiantesPorProgramasTotal(Request $request, $tabla)
    {
        $programas = $request->input('programa');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);

        if ($tabla == "Mafi") {
            $programas = DB::connection('sqlsrv')->table('MAFI  as dm')
                ->where('dm.ESTADO', 'Activo')
                ->whereIn('dm.PERIODO', $periodos)
                ->whereIn('dm.codprograma', $programas)
                ->select(DB::raw('COUNT(dm.codprograma) AS TOTAL, dm.codprograma'))
                ->groupBy('dm.codprograma')
                ->orderByDesc('TOTAL')
                ->limit(15)
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $programas));
    }

    /**
     * Método que muestra todos los operadores que traen estudiantes de los programas seleccionados por el usuario
     * @return JSON retorna los tipos de estudiantes, agrupados por tipo de estudiante
     */
    public function operadoresProgramaTotal(Request $request, $tabla)
    {

        $programas = $request->input('programa');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);
        if ($tabla == "Mafi") {
            $operadores = DB::connection('mysql')->table('datosMafi_memory')
                ->where('ESTADO', 'Activo')
                ->whereIn('periodo', $periodos)
                ->whereIn('codprograma', $programas)
                ->select(DB::raw('COUNT(operador) AS TOTAL, operador'))
                ->groupBy('operador')
                ->orderByDesc('TOTAL')
                ->limit(20)
                ->get();
        }

        if ($tabla == "planeacion") {
            $operadores = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('programa', $programas)
                ->selectRaw('COUNT(homologante) as TOTAL, operador')
                ->groupBy('operador')
                ->orderByDesc('TOTAL')
                ->limit(20)
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $operadores));
    }

    /**
     * Método que muestra todos los operadores que traen estudiantes de las facultades seleccionadas por el usuario
     * @return JSON retorna los operadores, agrupados por operador
     */
    public function operadoresFacultadTotal(Request $request, $tabla)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);

        $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();

        if ($tabla == "Mafi") {
            $operadores = DB::connection('sqlsrv')->table('MAFI  as dm')
                ->where('dm.ESTADO', 'Activo')
                ->whereIn('dm.PERIODO', $periodos)
                ->whereIn('dm.codprograma', $programas)
                ->select(DB::raw('COUNT(dm.operador) AS TOTAL, dm.operador'))
                ->groupBy('dm.operador')
                ->orderByDesc('TOTAL')
                ->limit(20)
                ->get();
        }

        if ($tabla == "planeacion") {
            $operadores = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('programa', $programas)
                ->selectRaw('COUNT(homologante) as TOTAL, operador')
                ->groupBy('operador')
                ->orderByDesc('TOTAL')
                ->limit(20)
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $operadores));
    }

    /**
     * Método que muestra los operadores ordenados de forma descendente en función de la cantidad de estudiantes que traen
     * @return JSON retorna un JSON con los operadores, agrupados por operador
     */
    public function operadoresTotal($tabla)
    {
        $tabla = trim($tabla);

        if ($tabla == "Mafi") {
            $operadores = DB::connection('mysql')->table('datosMafi_memory')
                ->select(DB::raw('COUNT(operador) AS TOTAL, operador'))
                ->where('estado', 'Activo')
                ->groupBy('operador')
                ->orderByDesc('TOTAL')
                ->limit(20)
                ->get();
        }

        if ($tabla == "planeacion") {
            $periodos = DB::table('periodo')
                ->where('periodoActivo', 1)
                ->pluck('periodos')
                ->unique()
                ->toArray();

            $operadores = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->selectRaw('COUNT(homologante) as TOTAL, operador')
                ->groupBy('operador')
                ->orderByDesc('TOTAL')
                ->limit(20)
                ->get();
        }


        header("Content-Type: application/json");
        echo json_encode(array('data' => $operadores));
    }

    /**
     * Método que muestra los 5 programas con mayor cantidad de estudiantes inscritos
     * @return JSON retorna un JSON con estos 5 programas, agrupados por programa
     */

    public function estudiantesProgramasTotal($tabla)
    {
        $tabla = trim($tabla);
        if ($tabla == "Mafi") {
            /**
             * SELECT COUNT(codprograma) AS TOTAL, codprograma FROM `datosMafi`
             *GROUP BY codprograma
             *ORDER BY TOTAL DESC
             */

            $programas = DB::connection('mysql')->table('datosMafi_memory')
                ->where('ESTADO', 'Activo')
                ->select(DB::raw('COUNT(codprograma) AS TOTAL, codprograma'))
                ->groupBy('codprograma')
                ->orderByDesc('TOTAL')
                ->limit(15)
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $programas));
    }

    /**
     * Método que muestra los estudiantes inscritos en cada programa, organizados de forma descendente
     * @return JSON retorna un JSON todos los programas, agrupados por programa
     */

    public function estudiantesFacultadTotal(Request $request, $tabla)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $tabla = trim($tabla);
        $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();


        if ($tabla == "Mafi") {
            $programas = DB::connection('sqlsrv')->table('MAFI  as dm')
                ->where('dm.ESTADO', 'Activo')
                ->whereIn('dm.PERIODO', $periodos)
                ->whereIn('dm.codprograma', $programas)
                ->select(DB::raw('COUNT(dm.codprograma) AS TOTAL, dm.codprograma'))
                ->groupBy('dm.codprograma')
                ->orderByDesc('TOTAL')
                ->limit(20)
                ->get();
        }

        if ($tabla == "planeacion") {
            $programas = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('programa', $programas)
                ->selectRaw('COUNT(homologante) as TOTAL, programa')
                ->groupBy('programa')
                ->orderBy('TOTAL', 'DESC')
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $programas));
    }

    /**
     * Método que trae todos los tipos de estudiantes
     * @return JSON retorna todos los tipos de estudiantes
     */
    public function tiposEstudiantesTotal($tabla)
    {
        $tabla = trim($tabla);

        if ($tabla == "Mafi") {
            /**
             * SELECT COUNT(tipoestudiante) AS 'TOTAL', 
             * tipoestudiante FROM `datosMafi` 
             * GROUP BY tipoestudiante
             */
            $tipoEstudiantes = DB::connection('mysql')->table('datosMafi_memory')

                ->select(DB::raw('COUNT(tipoestudiante) AS TOTAL, tipoestudiante'))
                ->where('estado', 'Activo')
                ->groupBy('tipoestudiante')
                ->orderByDesc('TOTAL')
                ->get();
        }

        if ($tabla == "planeacion") {

            $periodos = DB::table('periodo')
                ->where('periodoActivo', 1)
                ->pluck('periodos')
                ->unique()
                ->toArray();

            $tipoEstudiantes = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->selectRaw('COUNT(homologante) as TOTAL, tipo_estudiante')
                ->groupBy('tipo_estudiante')
                ->orderByDesc('TOTAL')
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $tipoEstudiantes));
    }

    /**
     * Método que trae todos los tipos de estudiantes por facultad
     * @return JSON retorna todos los tipos de estudiantes
     */
    public function tiposEstudiantesFacultadTotal(Request $request, $tabla)
    {
        $periodos = $request->input('periodos');
        $facultades = $request->input('idfacultad');
        $tabla = trim($tabla);
        $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();

        if ($tabla ==  "Mafi") {
            $tipoEstudiantes = DB::connection('sqlsrv')->table('MAFI  as dm')
                ->where('dm.ESTADO', 'Activo')
                ->whereIn('dm.PERIODO', $periodos)
                ->whereIn('dm.codprograma', $programas)
                ->select(DB::raw('COUNT(dm.tipoestudiante) AS TOTAL, dm.tipoestudiante'))
                ->groupBy('dm.tipoestudiante')
                ->orderByDesc('TOTAL')
                ->get();
        }

        if ($tabla == "planeacion") {
            $tipoEstudiantes = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('programa', $programas)
                ->selectRaw('COUNT(homologante) as TOTAL, tipo_estudiante')
                ->groupBy('tipo_estudiante')
                ->orderByDesc('TOTAL')
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $tipoEstudiantes));
    }

    /**
     * Método que muestra los tipos de estudiantes de los programas seleccionados por el usuario
     * @return JSON retorna los tipos de estudiantes, agrupados por tipo de estudiante
     */
    public function tiposEstudiantesProgramaTotal(Request $request, $tabla)
    {
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');
        $tabla = trim($tabla);

        if ($tabla == "Mafi") {
            $tipoEstudiantes = DB::connection('mysql')->table('datosMafi_memory')
                ->where('ESTADO', 'Activo')
                ->whereIn('periodo', $periodos)
                ->whereIn('codprograma', $programas)
                ->select(DB::raw('COUNT(tipoestudiante) AS TOTAL, tipoestudiante'))
                ->groupBy('tipoestudiante')
                ->orderByDesc('TOTAL')
                ->get();
        }

        if ($tabla == "planeacion") {
            $tipoEstudiantes = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('programa', $programas)
                ->selectRaw('COUNT(homologante) as TOTAL, tipo_estudiante')
                ->groupBy('tipo_estudiante')
                ->orderByDesc('TOTAL')
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $tipoEstudiantes));
    }

    public function graficoMetas()
    {
        $tiposEstudiante = [
            'PRIMER INGRESO',
            'PRIMER INGRESO PSEUDO INGRES',
            'TRANSFERENTE EXTERNO',
            'TRANSFERENTE EXTERNO (ASISTEN)',
            'TRANSFERENTE EXTERNO PSEUD ING',
            'TRANSFERENTE INTERNO',
            'REINGRESO'
        ];

        $periodos = DB::table('periodo')->where('activoCiclo1', 1)->select('periodos')->get();

        $periodosActivos = ['202406', '202411', '202431', '202441', '202451'];
        
        $matriculasSello = [];

        $consultaSello = DB::connection('mysql')->table('datosMafi_memory')
            ->where('sello', 'TIENE SELLO FINANCIERO')
            ->whereIn('periodo', $periodosActivos)
            ->whereIn('tipoestudiante', $tiposEstudiante)
            ->select(DB::raw('COUNT(idbanner) AS TOTAL, codprograma'))
            ->groupBy('codprograma')
            ->orderByDesc('TOTAL')
            ->limit(5)
            ->get();

        foreach ($consultaSello as $registro) {

            $codprograma = $registro->codprograma;
            $matriculasSello[$codprograma] = $registro->TOTAL;

            $consultaRetencion = DB::connection('mysql')->table('datosMafi_memory')
                ->select(DB::raw('COUNT(idbanner) AS TOTAL'))
                ->where('sello', 'TIENE RETENCION')
                ->where('autorizado_asistir', 'LIKE', 'ACTIVO%')
                ->whereIn('periodo', $periodosActivos)
                ->where('codprograma', $codprograma)
                ->whereIn('tipoestudiante', $tiposEstudiante)
                ->get();

            $consultaMetas = DB::table('programas_metas')
                ->where('programa', $codprograma)
                ->whereNotNull('meta')
                ->select('meta')
                ->first();

            $metas[$codprograma] = $consultaMetas->meta;

            if ($consultaRetencion) {
                $matriculasRetencion[$codprograma] = $consultaRetencion[0]->TOTAL;
            } else {
                $matriculasRetencion[$codprograma] = 0;
            }
        }

        $datos = [
            'metas' => $metas,
            'matriculaSello' => $matriculasSello,
            'matriculaRetencion' => $matriculasRetencion,
        ];

        return $datos;
    }

    public function graficoMetasFacultad(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        $tiposEstudiante = [
            'PRIMER INGRESO',
            'PRIMER INGRESO PSEUDO INGRES',
            'TRANSFERENTE EXTERNO',
            'TRANSFERENTE EXTERNO (ASISTEN)',
            'TRANSFERENTE EXTERNO PSEUD ING',
            'TRANSFERENTE INTERNO',
            'REINGRESO'
        ];

        $consultaPeriodosActivos = DB::table('periodo')->where('activoCiclo1', 1)
        ->whereIn('periodos',$periodos)
        ->select('periodos')
        ->get();
    
        $periodosActivos = [];
        $dos = [];

        foreach($consultaPeriodosActivos as $periodo)
        {
            $periodosActivos[] = $periodo->periodos;
            $dos[] = substr($periodo->periodos, -2);
        }

        $matriculasSello = [];

        $programasConsulta = DB::table('programas_metas as pm')
            ->select('programa')
            ->whereNotNull('meta')
            ->where('meta', '!=', 0)
            ->where('año', '2024')
            ->whereIn('programa', $programas)
            ->whereIn('periodo', $dos)
            ->distinct()
            ->groupBy('programa')
            ->limit(5)
            ->get(); 


        if ($programasConsulta->isEmpty()) {
            return null;
        } else {
            foreach ($programasConsulta as $programa) {

                $consultaNombres = DB::table('programas')->where('codprograma', $programa->programa)->select('programa')->get();
                $consultaMetas = DB::table('programas_metas')->select('meta')->where('programa', $programa->programa)->whereNotNull('meta')
                ->where('año', 2024)
                ->get();

                $consultaSello = DB::connection('sqlsrv')->table('MAFI  as dm')
                    ->select(DB::raw('COUNT(dm.idbanner) AS TOTAL'))
                    ->where('dm.sello', 'TIENE SELLO FINANCIERO')
                    ->whereIn('dm.periodo', $periodosActivos)
                    ->where('dm.codprograma', $programa->programa)
                    ->whereIn('dm.tipoestudiante', $tiposEstudiante)
                    ->limit(5)
                    ->get();

                $consultaRetencion = DB::connection('sqlsrv')->table('MAFI  as dm')
                    ->select(DB::raw('COUNT(dm.idbanner) AS TOTAL'))
                    ->where('dm.sello', 'TIENE RETENCION')
                    ->whereIn('dm.periodo', $periodosActivos)
                    ->where('dm.codprograma', $programa->programa)
                    ->whereIn('dm.tipoestudiante', $tiposEstudiante)
                    ->limit(5)
                    ->get();

                if ($consultaSello) {
                    $matriculasSello[$programa->programa] = $consultaSello[0]->TOTAL;
                } else {
                    $matriculasSello[$programa->programa] = 0;
                }

                if ($consultaRetencion) {
                    $matriculasRetencion[$programa->programa] = $consultaRetencion[0]->TOTAL;
                } else {
                    $matriculasRetencion[$programa->programa] = 0;
                }

                $nombres[$programa->programa] = $consultaNombres[0]->programa;
                $metas[$programa->programa] = $consultaMetas[0]->meta;
            }

            $datos = [
                'nombres' => $nombres,
                'metas' => $metas,
                'matriculaSello' => $matriculasSello,
                'matriculaRetencion' => $matriculasRetencion,
            ];

            return $datos;
        }
    }

    public function graficoMetasTotal()
    {
        $consultaMetas = DB::table('programas_metas')->get();

        $metas = [];
        foreach ($consultaMetas as $meta) {
            $dato = $meta->meta;
            if ($dato != null) {
                $metas[$meta->programa] = $dato;
            }
        }

        $tiposEstudiante = [
            'PRIMER INGRESO',
            'PRIMER INGRESO PSEUDO INGRES',
            'TRANSFERENTE EXTERNO',
            'TRANSFERENTE EXTERNO (ASISTEN)',
            'TRANSFERENTE EXTERNO PSEUD ING',
            'TRANSFERENTE INTERNO',
            'REINGRESO'
        ];

        $programasConsulta = DB::table('programas_metas')
            ->select('programa')
            ->whereNotNull('meta')
            ->where('meta', '!=',0)
            ->groupBy('programa')
            ->get();


        $nombres = [];

        $periodos = DB::table('periodo')->where('activoCiclo1', 1)->select('periodos')->get();

        $periodosActivos = ['202406', '202411', '202431', '202441', '202451'];

        $matriculasSello = [];

        foreach ($programasConsulta as $programa) {

            $consultaNombres = DB::table('programas')->where('codprograma', $programa->programa)->select('programa')->get();

            $consultaSello = DB::connection('mysql')->table('datosMafi_memory')
                ->select(DB::raw('COUNT(idbanner) AS TOTAL'))
                ->where('sello', 'TIENE SELLO FINANCIERO')
                ->whereIn('periodo', $periodosActivos)
                ->where('codprograma', $programa->programa)
                ->whereIn('tipoestudiante', $tiposEstudiante)
                ->get();

            $consultaRetencion = DB::connection('mysql')->table('datosMafi_memory')
                ->select(DB::raw('COUNT(idbanner) AS TOTAL'))
                ->where('sello', 'TIENE RETENCION')
                ->whereIn('periodo', $periodosActivos)
                ->where('codprograma', $programa->programa)
                ->whereIn('tipoestudiante', $tiposEstudiante)
                ->get();

            if ($consultaSello) {
                $matriculasSello[$programa->programa] = $consultaSello[0]->TOTAL;
            } else {
                $matriculasSello[$programa->programa] = 0;
            }

            if ($consultaRetencion) {
                $matriculasRetencion[$programa->programa] = $consultaRetencion[0]->TOTAL;
            } else {
                $matriculasRetencion[$programa->programa] = 0;
            }

            $nombres[$programa->programa] = $consultaNombres[0]->programa;
        }

        $datos = [
            'nombres' => $nombres,
            'metas' => $metas,
            'matriculaSello' => $matriculasSello,
            'matriculaRetencion' => $matriculasRetencion,
        ];

        return $datos;
    }

    public function graficoMetasFacultadTotal(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $metas = [];
        $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();

        $nombres = [];

        $tiposEstudiante = [
            'PRIMER INGRESO',
            'PRIMER INGRESO PSEUDO INGRES',
            'TRANSFERENTE EXTERNO',
            'TRANSFERENTE EXTERNO (ASISTEN)',
            'TRANSFERENTE EXTERNO PSEUD ING',
            'TRANSFERENTE INTERNO',
            'REINGRESO'
        ];

        $consultaPeriodosActivos = DB::table('periodo')->where('activoCiclo1', 1)
        ->whereIn('periodos',$periodos)
        ->select('periodos')
        ->get();
    
        $periodosActivos = [];
        $dos = [];

        foreach($consultaPeriodosActivos as $periodo)
        {
            $periodosActivos[] = $periodo->periodos;
            $dos[] = substr($periodo->periodos, -2);
        }

        $matriculasSello = [];

        $programasConsulta = DB::table('programas_metas as pm')
            ->select('programa')
            ->whereNotNull('meta')
            ->where('meta', '!=', 0)
            ->where('año', '2024')
            ->whereIn('programa', $programas)
            ->whereIn('periodo', $dos)
            ->distinct()
            ->groupBy('programa')
            ->get(); 

        $matriculasSello = [];

        foreach ($programasConsulta as $programa) {

            $consultaNombres = DB::table('programas')->where('codprograma', $programa->programa)->select('programa')->get();
            $consultaMetas = DB::table('programas_metas')->where('programa', $programa->programa)->select('meta')->whereNotNull('meta')->get();

            $consultaSello = DB::connection('sqlsrv')->table('MAFI  as dm')
                ->select(DB::raw('COUNT(dm.idbanner) AS TOTAL'))
                ->where('dm.sello', 'TIENE SELLO FINANCIERO')
                ->whereIn('dm.periodo', $periodosActivos)
                ->where('dm.codprograma', $programa->programa)
                ->whereIn('dm.tipoestudiante', $tiposEstudiante)
                ->get();

            $consultaRetencion = DB::connection('sqlsrv')->table('MAFI  as dm')
                ->select(DB::raw('COUNT(dm.idbanner) AS TOTAL'))
                ->where('dm.sello', 'TIENE RETENCION')
                ->whereIn('dm.periodo', $periodosActivos)
                ->where('dm.codprograma', $programa->programa)
                ->whereIn('dm.tipoestudiante', $tiposEstudiante)
                ->get();

            if ($consultaSello) {
                $matriculasSello[$programa->programa] = $consultaSello[0]->TOTAL;
            } else {
                $matriculasSello[$programa->programa] = 0;
            }

            if ($consultaRetencion) {
                $matriculasRetencion[$programa->programa] = $consultaRetencion[0]->TOTAL;
            } else {
                $matriculasRetencion[$programa->programa] = 0;
            }

            $nombres[$programa->programa] = $consultaNombres[0]->programa;
            $metas[$programa->programa] = $consultaMetas[0]->meta;
        }

        $datos = [
            'nombres' => $nombres,
            'metas' => $metas,
            'matriculaSello' => $matriculasSello,
            'matriculaRetencion' => $matriculasRetencion,
        ];

        return $datos;
    }

    public function graficoMetasProgramas(Request $request)
    {
        $programas = $request->input('programa');
        $periodos = $request->input('periodos');
       
        $tiposEstudiante = [
            'PRIMER INGRESO',
            'PRIMER INGRESO PSEUDO INGRES',
            'TRANSFERENTE EXTERNO',
            'TRANSFERENTE EXTERNO (ASISTEN)',
            'TRANSFERENTE EXTERNO PSEUD ING',
            'TRANSFERENTE INTERNO',
            'REINGRESO'
        ];
        
        $consultaPeriodosActivos = DB::table('periodo')
            ->where('activoCiclo1', 1)
            //->orWhere('activoCiclo2', 1)
            ->select('periodos')
            ->get();
        //dd($consultaPeriodosActivos);
        $periodosActivos = [];
        $dos = [];
        
        foreach($consultaPeriodosActivos as $periodo)
        {
            $periodosActivos[] = $periodo->periodos;
            $dos[] = substr($periodo->periodos, -2);
        }
        
        $matriculasSello = [];
        
        $programasConsulta = DB::table('programas_metas as pm')
        ->select('programa')
        ->whereNotNull('meta')
        ->where('meta', '!=', 0)
        ->where('año', '2024')
        ->whereIn('programa', $programas)
        ->whereIn('periodo', $dos)
        ->distinct()
        ->groupBy('programa')
        ->orderBy('programa', 'DESC')
        ->limit(12)
        ->get();   
        //dd($programasConsulta);
        //var_dump( $programasConsulta);die;
        if ($programasConsulta->isEmpty()) {
            return null;
        } else {
            foreach ($programasConsulta as $programa) {
                //var_dump($dos);die;
                $consultaNombres = DB::table('programas')->where('codprograma', $programa->programa)->select('programa')->get();
                $consultaMetas = DB::table('programas_metas')
                ->select('meta')
                ->where('programa', $programa->programa)
                ->whereIn('periodo', $dos)
                ->whereNotNull('meta')
                ->where('meta', '!=', 0)
                ->where('año', '2024')
                ->orderBy('meta', 'ASC')
                ->get();
                $consultaSello = DB::connection('mysql')->table('datosMafi_memory')
                ->select(DB::raw('COUNT(idbanner) AS TOTAL'))
                ->where('ESTADO', 'Activo')
                ->where('sello', 'TIENE SELLO FINANCIERO')
                ->whereIn('periodo', $periodosActivos)
                ->where('codprograma', $programa->programa)
                ->whereIn('tipoestudiante', $tiposEstudiante)
                ->get();
                
               
                $consultaRetencion = DB::connection('sqlsrv')->table('MAFI  as dm')
                    ->select(DB::raw('COUNT(dm.idbanner) AS TOTAL'))
                    ->where('dm.sello', 'TIENE RETENCION')
                    ->whereIn('dm.periodo', $periodosActivos)
                    ->where('dm.codprograma', $programa->programa)
                    ->whereIn('dm.tipoestudiante', $tiposEstudiante)
                    ->get();
                 //var_dump($consultaMetas);die;
                if ($consultaSello) {
                    $matriculasSello[$programa->programa] = $consultaSello[0]->TOTAL;
                } else {
                    $matriculasSello[$programa->programa] = 0;
                }

                if ($consultaRetencion) {
                    $matriculasRetencion[$programa->programa] = $consultaRetencion[0]->TOTAL;
                } else {
                    $matriculasRetencion[$programa->programa] = 0;
                }

                $nombres[$programa->programa] = $consultaNombres[0]->programa;

                if($consultaMetas){
                    $metas[$programa->programa] = $consultaMetas[0]->meta;
                }
            }

            $datos = [
                'nombres' => $nombres,
                'metas' => $metas,
                'matriculaSello' => $matriculasSello,
                'matriculaRetencion' => $matriculasRetencion,
            ];
            //dd($datos);
            return $datos;
        }
    }

    public function graficoMetasProgramasTotal(Request $request)
    {
        $programas = $request->input('programa');
        $periodos = $request->input('periodos');
        $metas = [];

        $nombres = [];

        $tiposEstudiante = [
            'PRIMER INGRESO',
            'PRIMER INGRESO PSEUDO INGRES',
            'TRANSFERENTE EXTERNO',
            'TRANSFERENTE EXTERNO (ASISTEN)',
            'TRANSFERENTE EXTERNO PSEUD ING',
            'TRANSFERENTE INTERNO',
            'REINGRESO'
        ];

            $consultaPeriodosActivos = DB::table('periodo')
            ->where('activoCiclo1', 1)
            //->orWhere('activoCiclo2', 1)
            ->select('periodos')
            ->get();
        
            $periodosActivos = [];
            $dos = [];
    
            foreach($consultaPeriodosActivos as $periodo)
            {
                $periodosActivos[] = $periodo->periodos;
                $dos[] = substr($periodo->periodos, -2);
            }

            $programasConsulta = DB::table('programas_metas as pm')
            ->select('programa')
            ->whereNotNull('meta')
            ->where('meta', '!=', 0)
            ->where('año', '2024')
            ->whereIn('programa', $programas)
            ->whereIn('periodo', $dos)
            ->distinct()
            ->groupBy('programa')
            ->get();


        $matriculasSello = [];

        foreach ($programasConsulta as $programa) {

            $consultaNombres = DB::table('programas')->where('codprograma', $programa->programa)->select('programa')->get();
            $consultaMetas = DB::table('programas_metas')
            ->whereIn('periodo', $dos)
            ->select('meta')
            ->where('programa', $programa->programa)
            ->whereNotNull('meta')
            ->where('año', 2024)
            ->get();

            $consultaSello = DB::connection('sqlsrv')->table('MAFI  as dm')
                ->select(DB::raw('COUNT(dm.idbanner) AS TOTAL'))
                ->where('dm.sello', 'TIENE SELLO FINANCIERO')
                ->whereIn('dm.periodo', $periodosActivos)
                ->where('dm.codprograma', $programa->programa)
                ->whereIn('dm.tipoestudiante', $tiposEstudiante)
                ->get();

            $consultaRetencion = DB::connection('sqlsrv')->table('MAFI  as dm')
                ->select(DB::raw('COUNT(dm.idbanner) AS TOTAL'))
                ->where('dm.sello', 'TIENE RETENCION')
                ->whereIn('dm.periodo', $periodosActivos)
                ->where('dm.codprograma', $programa->programa)
                ->whereIn('dm.tipoestudiante', $tiposEstudiante)
                ->get();

            if ($consultaSello) {
                $matriculasSello[$programa->programa] = $consultaSello[0]->TOTAL;
            } else {
                $matriculasSello[$programa->programa] = 0;
            }

            if ($consultaRetencion) {
                $matriculasRetencion[$programa->programa] = $consultaRetencion[0]->TOTAL;
            } else {
                $matriculasRetencion[$programa->programa] = 0;
            }

            $nombres[$programa->programa] = $consultaNombres[0]->programa;
            $metas[$programa->programa] = $consultaMetas[0]->meta;
        }

        $datos = [
            'nombres' => $nombres,
            'metas' => $metas,
            'matriculaSello' => $matriculasSello,
            'matriculaRetencion' => $matriculasRetencion,
        ];

        return $datos;
    }

    public function tablaProgramas(Request $request)
    {
        $periodos = $request->input('periodos');

        $estudiantesPrograma = DB::table('estudiantes')
            ->select(DB::raw('COUNT(homologante) as TOTAL'), 'programa')
            ->whereIn('marca_ingreso', $periodos)
            ->where(function ($query) {
                $query->where('planeado_ciclo1', 'OK')
                    ->orWhere('planeado_ciclo2', 'OK');
            })
            ->where('estado', 'Activo')
            ->groupBy('programa')
            ->get();

        $nombre = [];
        $estudiantes = [];

        foreach ($estudiantesPrograma as $key) {
            $programa = $key->programa;

            $consultaNombre = DB::table('programas')->where('codprograma', $programa)->select('programa')->first();
            $nombre[$programa] = $consultaNombre->programa;
            $estudiantes[$programa] = $key->TOTAL;
        }

        $consultaSello = DB::table('estudiantes')
            ->selectRaw('COUNT(programa) as total, programa, sello, autorizado_asistir')
            ->whereIn('marca_ingreso', $periodos)
            ->where(function ($query) {
                $query->where('planeado_ciclo1', 'OK')
                    ->orWhere('planeado_ciclo2', 'OK');
            })
            ->where('estado', 'Activo')
            ->groupBy('sello', 'programa', 'autorizado_asistir')
            ->get();

        foreach ($consultaSello as $key) {
            $programa = $key->programa;
            $sello = $key->sello;
            $total = $key->total;
            $estado = $key->autorizado_asistir;

            if ($sello == 'TIENE SELLO FINANCIERO') {
                if (isset($estudiantesSello[$programa])) {
                    $estudiantesSello[$programa] = $total + $estudiantesSello[$programa];
                } else {
                    $estudiantesSello[$programa] = $total;
                }
            }

            if ($sello == 'TIENE RETENCION' && empty($estado)) {
                if (isset($estudiantesRetencion[$programa])) {
                    $estudiantesRetencion[$programa] = $total;
                } else {
                    $estudiantesRetencion[$programa] = $total;
                }
            }

            if ($sello == 'TIENE RETENCION' && !empty($estado)) {
                if (isset($estudiantesASP[$programa])) {
                    $estudiantesASP[$programa] = $total + $estudiantesASP[$programa];
                } else {
                    $estudiantesASP[$programa] = $total;
                }
            }

            if (isset($estudiantesTotal[$programa])) {
                $estudiantesTotal[$programa] = $total + $estudiantesTotal[$programa];
            } else {
                $estudiantesTotal[$programa] = $total;
            }
        }

        $data = [];

        foreach ($estudiantes as $key => $value) {
            $data[$key] = [
                'programa' => isset($nombre[$key]) ? $nombre[$key] : 0,
                'Total' => isset($estudiantesTotal[$key]) ? $estudiantesTotal[$key] : 0,
                'Sello' => isset($estudiantesSello[$key]) ? $estudiantesSello[$key] : 0,
                'ASP' => isset($estudiantesASP[$key]) ? $estudiantesASP[$key] : 0,
                'Retencion' => isset($estudiantesRetencion[$key]) ? $estudiantesRetencion[$key] : 0,
            ];
        }

        $Data = (object) $data;

        return $Data;
    }

    public function tablaProgramasFacultad(Request $request)
    {
        /* SELECT COUNT(p.codBanner) AS TOTAL, p.codprograma
            FROM planeacion p
            INNER JOIN programas pr ON p.codprograma = pr.codprograma
            WHERE p.periodo IN ('202313', '202333') AND pr.Facultad = 'FAC CIENCIAS EMPRESARIALES'
            GROUP BY p.codprograma;
         */

        $periodos = $request->input('periodos');
        $facultades = $request->input('facultad');

        $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();

        $estudiantesPrograma = DB::table('estudiantes')
            ->whereIn('marca_ingreso', $periodos)
            ->where(function ($query) {
                $query->where('planeado_ciclo1', 'OK')
                    ->orWhere('planeado_ciclo2', 'OK');
            })
            ->select(DB::raw('COUNT(homologante) AS TOTAL'), 'programa')
            ->whereIn('programa', $programas)
            ->where('estado', 'Activo')
            ->groupBy('programa')
            ->get();

        foreach ($estudiantesPrograma as $key) {
            $programa = $key->programa;

            $consultaNombre = DB::table('programas')->where('codprograma', $programa)->select('programa')->first();
            $nombre[$programa] = $consultaNombre->programa;
            $estudiantes[$programa] = $key->TOTAL;
        }

        $consultaSello = DB::table('estudiantes')
            ->whereIn('marca_ingreso', $periodos)
            ->where(function ($query) {
                $query->where('planeado_ciclo1', 'OK')
                    ->orWhere('planeado_ciclo2', 'OK');
            })
            ->select(DB::raw('COUNT(programa) AS total'), 'programa', 'sello', 'autorizado_asistir')
            ->whereIn('programa', $programas)
            ->where('estado', 'Activo')
            ->groupBy('sello', 'programa', 'autorizado_asistir')
            ->get();

        foreach ($consultaSello as $key) {
            $programa = $key->programa;
            $sello = $key->sello;
            $total = $key->total;
            $estado = $key->autorizado_asistir;

            if ($sello == 'TIENE SELLO FINANCIERO') {
                if (isset($estudiantesSello[$programa])) {
                    $estudiantesSello[$programa] = $total + $estudiantesSello[$programa];
                } else {
                    $estudiantesSello[$programa] = $total;
                }
            }

            if ($sello == 'TIENE RETENCION' && empty($estado)) {
                if (isset($estudiantesRetencion[$programa])) {
                    $estudiantesRetencion[$programa] = $total;
                } else {
                    $estudiantesRetencion[$programa] = $total;
                }
            }

            if ($sello == 'TIENE RETENCION' && !empty($estado)) {
                if (isset($estudiantesASP[$programa])) {
                    $estudiantesASP[$programa] = $total + $estudiantesASP[$programa];
                } else {
                    $estudiantesASP[$programa] = $total;
                }
            }

            if (isset($estudiantesTotal[$programa])) {
                $estudiantesTotal[$programa] = $total + $estudiantesTotal[$programa];
            } else {
                $estudiantesTotal[$programa] = $total;
            }
        }

        $data = [];

        foreach ($estudiantes as $key => $value) {
            $data[$key] = [
                'programa' => isset($nombre[$key]) ? $nombre[$key] : 0,
                'Total' => isset($estudiantesTotal[$key]) ? $estudiantesTotal[$key] : 0,
                'Sello' => isset($estudiantesSello[$key]) ? $estudiantesSello[$key] : 0,
                'ASP' => isset($estudiantesASP[$key]) ? $estudiantesASP[$key] : 0,
                'Retencion' => isset($estudiantesRetencion[$key]) ? $estudiantesRetencion[$key] : 0,
            ];
        }

        $Data = (object) $data;

        return $Data;
    }

    public function tablaProgramasP(Request $request)
    {
        $periodos = $request->input('periodos');
        $programas = $request->input('programas');

        $estudiantesPrograma = DB::table('estudiantes')
            ->whereIn('marca_ingreso', $periodos)
            ->select(DB::raw('COUNT(homologante) as TOTAL'), 'programa')
            ->where(function ($query) {
                $query->where('planeado_ciclo1', 'OK')
                    ->orWhere('planeado_ciclo2', 'OK');
            })
            ->whereIn('programa', $programas)
            ->where('estado', 'Activo')
            ->groupBy('programa')
            ->get();

        foreach ($estudiantesPrograma as $key) {
            $programa = $key->programa;
            $consultaNombre = DB::table('programas')->where('codprograma', $programa)->select('programa')->first();
            $nombre[$programa] = $consultaNombre->programa;
            $estudiantes[$programa] = $key->TOTAL;
        }

        $consultaSello = DB::table('estudiantes')
            ->whereIn('marca_ingreso', $periodos)
            ->where(function ($query) {
                $query->where('planeado_ciclo1', 'OK')
                    ->orWhere('planeado_ciclo2', 'OK');
            })
            ->selectRaw('COUNT(programa) as total, programa, sello, autorizado_asistir')
            ->whereIn('programa', $programas)
            ->where('estado', 'Activo')
            ->groupBy('sello', 'programa', 'autorizado_asistir')
            ->get();

        foreach ($consultaSello as $key) {
            $programa = $key->programa;
            $sello = $key->sello;
            $total = $key->total;
            $estado = $key->autorizado_asistir;

            if ($sello == 'TIENE SELLO FINANCIERO') {
                if (isset($estudiantesSello[$programa])) {
                    $estudiantesSello[$programa] = $total + $estudiantesSello[$programa];
                } else {
                    $estudiantesSello[$programa] = $total;
                }
            }

            if ($sello == 'TIENE RETENCION' && empty($estado)) {
                if (isset($estudiantesRetencion[$programa])) {
                    $estudiantesRetencion[$programa] = $total;
                } else {
                    $estudiantesRetencion[$programa] = $total;
                }
            }

            if ($sello == 'TIENE RETENCION' && !empty($estado)) {
                if (isset($estudiantesASP[$programa])) {
                    $estudiantesASP[$programa] = $total + $estudiantesASP[$programa];
                } else {
                    $estudiantesASP[$programa] = $total;
                }
            }

            if (isset($estudiantesTotal[$programa])) {
                $estudiantesTotal[$programa] = $total + $estudiantesTotal[$programa];
            } else {
                $estudiantesTotal[$programa] = $total;
            }
        }

        $data = [];

        foreach ($estudiantes as $key => $value) {
            $data[$key] = [
                'programa' => isset($nombre[$key]) ? $nombre[$key] : 0,
                'Total' => isset($estudiantesTotal[$key]) ? $estudiantesTotal[$key] : 0,
                'Sello' => isset($estudiantesSello[$key]) ? $estudiantesSello[$key] : 0,
                'ASP' => isset($estudiantesASP[$key]) ? $estudiantesASP[$key] : 0,
                'Retencion' => isset($estudiantesRetencion[$key]) ? $estudiantesRetencion[$key] : 0,
            ];
        }

        $Data = (object) $data;

        return $Data;
    }


    public function mallaPrograma(Request $request)
    {
        $programa = $request->input('programa');

        $fechaActual = date("d-m-Y");

        $fecha = DB::table('periodo')->where('activoCiclo1', 1)->select('fechaInicioCiclo1')->first();

        $fechaFormateada = $fecha->fechaInicioCiclo1;

        $fechaFormateada = new DateTime($fechaFormateada);

        $fechaFormateada->modify('-1 week');

        if ($fechaActual <  $fechaFormateada) {
            $tablaConsulta = 'planeacion';
        } else {
            $tablaConsulta = 'programacion';
        }

        $consultaMalla = DB::table($tablaConsulta)
            ->selectRaw('COUNT(codMateria) as TOTAL, codMateria')
            ->where('codprograma', $programa)
            ->groupBy('codMateria')
            ->get();

        $data = [];
        $nombre = [];

        foreach ($consultaMalla as $key) {
            $total = $key->TOTAL;
            $codMateria = $key->codMateria;

            $consultaNombre = DB::table('mallaCurricular')
                ->select('curso')
                ->where('codigoCurso', $codMateria)
                ->first();

            $nombre[$codMateria] = $consultaNombre->curso;
            $estudiantes[$codMateria] = $total;
        }

        $estudiantesSello = [];
        $estudiantesRetencion = [];
        $estudiantesASP = [];
        $materiaTotal = [];

        $consultaSello = DB::table($tablaConsulta . ' as p')
            ->join('estudiantes as e', 'p.codBanner', '=', 'e.homologante')
            ->selectRaw('COUNT(p.codMateria) as total, p.codMateria, e.sello, e.autorizado_asistir')
            ->where('p.codprograma', $programa)
            ->where('e.estado', 'Activo')
            ->groupBy('e.sello', 'p.codMateria', 'e.autorizado_asistir')
            ->get();

        foreach ($consultaSello as $sello) {
            $dato = $sello->sello;
            $conteo = $sello->total;
            $materia = $sello->codMateria;
            $estado = $sello->autorizado_asistir;

            if ($dato == 'TIENE SELLO FINANCIERO') {
                if (isset($estudiantesSello[$materia])) {
                    $estudiantesSello[$materia] = $conteo + $estudiantesSello[$materia];
                } else {
                    $estudiantesSello[$materia] = $conteo;
                }
            }
            if ($dato == 'TIENE RETENCION' && empty($estado)) {
                if (isset($estudiantesRetencion[$materia])) {
                    $estudiantesRetencion[$materia] = $conteo + $estudiantesRetencion[$materia];
                } else {
                    $estudiantesRetencion[$materia] = $conteo;
                }
            }
            if ($dato == 'TIENE RETENCION' && !empty($estado)) {
                if (isset($estudiantesASP[$materia])) {
                    $estudiantesASP[$materia] = $conteo + $estudiantesASP[$materia];
                } else {
                    $estudiantesASP[$materia] = $conteo;
                }
            }

            if (isset($materiaTotal[$materia])) {
                $materiaTotal[$materia] = $conteo + $materiaTotal[$materia];
            } else {
                $materiaTotal[$materia] = $conteo;
            }
        }

        $data = [];

        foreach ($estudiantes as $key => $value) {
            $data[$key] = [
                'nombreMateria' => isset($nombre[$key]) ? $nombre[$key] : 0,
                'Total' => isset($materiaTotal[$key]) ? $materiaTotal[$key] : 0,
                'Sello' => isset($estudiantesSello[$key]) ? $estudiantesSello[$key] : 0,
                'Retencion' => isset($estudiantesRetencion[$key]) ? $estudiantesRetencion[$key] : 0,
                'ASP' => isset($estudiantesASP[$key]) ? $estudiantesASP[$key] : 0,
            ];
        }

        $Data = (object) $data;
        return $Data;
    }

    public function estudiantesMateria(Request $request)
    {
        $programa = $request->input('programa');
        $idsBanner = [];

        $fechaActual = date("d-m-Y");

        $fecha = DB::table('periodo')->where('activoCiclo1', 1)->select('fechaInicioCiclo1')->first();

        $fechaFormateada = $fecha->fechaInicioCiclo1;

        $fechaFormateada = new DateTime($fechaFormateada);

        $fechaFormateada->modify('-1 week');

        if ($fechaActual <  $fechaFormateada) {
            $tablaConsulta = 'planeacion';
        } else {
            $tablaConsulta = 'programacion';
        }

        $estudiantes = DB::table($tablaConsulta . ' as p')
            ->join('mallaCurricular as m', 'p.codMateria', '=', 'm.codigoCurso')
            
            ->where('p.codPrograma', $programa)
            ->select('p.codBanner', 'p.codMateria', 'm.curso', 'm.creditos')
            ->groupBy('p.codBanner', 'p.codMateria', 'm.curso', 'm.creditos')
            ->get();

        return $estudiantes;
    }

    public function traerProgramas()
    {
        //dd($_POST);
        //var_dump($_POST['periodos']);die();
        $Facultades = $_POST['codfacultad'];
        $periodos = $_POST['periodos'];
        $tabla = $_POST['tabla'];
        //dd( $Facultades, $periodos, $tabla);
        // Verificamos si el usuario tiene programas asignados
        $programa=auth()->user()->programa;
        //dd($programa);
        //dd($Facultades[0] === "");
        //dd(count($Facultades) === 1 && $Facultades[0] !== "");
        $transversal = "";
        if(count($Facultades) === 1 && $Facultades[0] === ""){
            $facultades=auth()->user()->id_facultad;
            $facts=explode(';',$facultades);
            $Facultades = DB::table('facultad')->select('nombre', 'id', 'transversal')->whereIn('id',$facts)->pluck('nombre')->toArray();
        }
        
        //dd($transversal == 1);
        //dd(isset($programa) && !empty($programa));
        $codigosPrograma = array();
        if(isset($programa) && !empty($programa)){
            $program=explode(';',$programa);
            
            $codigosPrograma = DB::table('programas')->select('codprograma')->whereIn('id',$program)->pluck('codprograma')->toArray();
        }else{
            $codigosPrograma = DB::table('programas')->select('codprograma')->whereIn('Facultad',$Facultades)->pluck('codprograma')->toArray();  
        }
        //var_dump($codigosPrograma);die();

        //dd($codigosPrograma);
        $periodo_actual = date("Y");
        //dd($tabla);
        if($tabla == 'Mafi'){

            //-- periodos activos en mafi
            $programas = DB::connection('mysql')->table('datosMafi_memory')->select('codprograma')
            ->where('periodo', 'LIKE', '%'.$periodo_actual.'%')
            ->whereIn('periodo', $periodos)->whereIn('codprograma',$codigosPrograma)->groupBy('codprograma')->pluck('codprograma')->toArray();
       
        
        }elseif($tabla == 'planeacion'){

            //-- periodos activos en planeacion
            
            $programas = DB::table('planeacion')->select('codprograma')->whereIn('periodo', $periodos)->whereIn('codprograma',$codigosPrograma)->groupBy('codprograma')->pluck('codprograma')->toArray();

           
        }elseif($tabla == 'moodle'){

           $programas = [];
           //--- periodos activos en moodle
           $consultaProgramas = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')
           ->select('Grupo')
           ->whereIn('Periodo_Rev', $periodos)
           ->where(function ($query) use ($codigosPrograma) {
               foreach ($codigosPrograma as $programa) {
                   $query->orWhere('Grupo', 'LIKE', '%' . $programa . '%');
               }
           })
           ->groupBy('Grupo')
           ->get();

           
          if($consultaProgramas->isEmpty()){

                //var_dump("entro");die;
                $consultaProgramas = DB::table('V_Reporte_Ausentismo_memory')
                ->select('Cod_programa')
                ->whereIn('Periodo_Rev', $periodos)
                ->where(function ($query) use ($codigosPrograma) {
                    foreach ($codigosPrograma as $programa) {
                        $query->orWhere('Cod_programa', 'LIKE', '%' . $programa . '%');
                    }
                })
                ->groupBy('Cod_programa')
                ->get();
            }


          
            foreach($consultaProgramas as $dato)
            {
                $grupo = $dato->Grupo;
                $grupoExplode = explode('_', $grupo);

                $programa = $grupoExplode[1];

                if (!in_array($programa, $programas)) {
                    $programas[] = $programa;
                }
            }   
           
        }elseif($tabla == 'Alertas'){
            $programas = DB::table('alertas_tempranas')->select('codprograma') ->whereIn('periodo', $periodos)->whereIn('codprograma',$codigosPrograma)->groupBy('codprograma')->pluck('codprograma')->toArray();
        }
        //

   
        if ($tabla== 'moodlecerrados') {
    
            $programas = [];
          
        
                 //var_dump("entro");die;
                 $consultaProgramas = DB::table('cierrematriculas')
                 ->select('Programa')
                 ->whereIn('Periodo', $periodos)
                 ->where(function ($query) use ($codigosPrograma) {
                     foreach ($codigosPrograma as $programa) {
                         $query->orWhere('Programa', 'LIKE', '%' . $programa . '%');
                     }
                 })
                 ->groupBy('Programa')
                 ->get();
        
 
 
             foreach($consultaProgramas as $dato)
             {
                 
                 $programa = $dato->Programa;
 
                 if (!in_array($programa, $programas)) {
                     $programas[] = $programa;
                 }
             }   
            
        }
        // Consulta para enviar unicamente los programas activos de las facultades y periodos correspondientes
        $programasRetorno = DB::table('programas as p')
        ->join('programasPeriodos as pP', 'p.codprograma', '=', 'pP.codPrograma')
            ->whereIn('p.Facultad', $Facultades)
            ->whereIn('p.codprograma', $programas)
            ->select('p.programa', 'p.codprograma','p.Facultad')
            ->groupBy('p.codprograma', 'p.programa', 'p.Facultad')
            ->get();
            //dd($Facultades,$programas, $programasRetorno);
        $arreglo = [];

        foreach ($programasRetorno as $programa) {
            $arreglo[] = [
                'programa' => $programa->programa,
                'codprograma' => $programa->codprograma
            ];
        }
        if ($arreglo != []) {
            return $arreglo;
        } else {
            return null;
        }
    }

    public function traerCursos()
    {
        $tabla = $_POST['tabla'];
        $periodos = $_POST['periodos'];

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
            $tablaConsulta = 'planeacion';
        } else {
            $tablaConsulta = 'programacion';
        }

        $facultadTransversal = auth()->user()->id_facultad;

        $cursos_db = DB::table('mallaCurricular')
            ->select('curso', 'codigoCurso')
            ->where('id_facultad_transversal', $facultadTransversal)
            ->groupBy('curso', 'codigoCurso')
            ->get();

        $codigosCursos = [];

        foreach ($cursos_db as $curso) {
            $codigosCursos[] = $curso->codigoCurso;
        }

        if ($tabla == 'moodle') {
            $consultaCursos = DB::connection('mysql')->table('V_Reporte_Ausentismo_memory')
                ->select('Cod_materia')
                ->whereIn('Periodo_Rev', $periodos)
                ->whereIn('Cod_materia', $codigosCursos)
                ->groupBy('Cod_materia')
                ->pluck('Cod_materia')->toArray();
        } elseif ($tabla == 'moodlecerrados') {
            $consultaCursos = DB::connection('mysql')->table('cursos_moodle_cerrados')
                ->select('Cod_materia')
                ->whereIn('Periodo', $periodos)
                ->whereIn('Codigo_materia', $codigosCursos)
                ->groupBy('Codigo_materia')
                ->pluck('Codigo_materia')->toArray();
        } else if ($tabla == 'planeacion') {
            $consultaCursos = DB::table($tablaConsulta)
                ->select('codMateria')
                ->whereIn('periodo', $periodos)
                ->whereIn('codMateria', $codigosCursos)
                ->groupBy('codMateria')
                ->pluck('codMateria')->toArray();
        }

        $arreglo = DB::table('mallaCurricular')
            ->select('curso')
            ->whereIn('codigoCurso', $consultaCursos)
            ->groupBy('curso')
            ->orderBy('curso', 'ASC')
            ->get()
            ->map(function ($item) {
                return [
                    'programa' => $item->curso,
                ];
            })
            ->toArray();

        return $arreglo;
    }


    public function todosProgramas()
    {
        $programas = DB::table('programas as p')
            ->join('programasPeriodos as pP', 'p.codprograma', '=', 'pP.codPrograma')
            ->where('pP.estado', 1)
            ->whereIn('p.estado', [1, 2])
            ->select('p.programa', 'p.codprograma')
            ->groupBy('p.codprograma', 'p.programa')
            ->get();

        foreach ($programas as $programa) {
            $arreglo[] = [
                'nombre' => $programa->programa,
                'codprograma' => $programa->codprograma
            ];
        }

        header("Content-Type: application/json");
        echo json_encode($arreglo);
    }

    public function excelMafi()
    {
        $data = DB::connection('mysql')->table('datosMafi_memory')
            ->get();

        $dataExcel = $data->map(function ($item) {
            return collect($item)->except('id')->toArray();
        });

        return $dataExcel;
    }

    public function excelMafiFacultad(Request $request)
    {
        $periodos = $request->input('periodos');
        $facultades = $request->input('idfacultad');

        $dataExcel = DB::connection('sqlsrv')->table('MAFI  as dm')
            ->whereIn('dm.periodo', $periodos)
            ->whereIn('dm.Facultad', $facultades)
            ->select(
                'dm.idbanner',
                'dm.primer_apellido',
                'dm.programa',
                'dm.codprograma',
                'dm.cadena',
                'dm.periodo',
                'dm.ESTADO',
                'dm.tipoestudiante',
                'dm.ruta_academica',
                'dm.sello',
                'dm.operador',
                'dm.autorizado_asistir'
            )
            ->get();

        return $dataExcel;
    }

    public function excelMafiPrograma(Request $request)
    {
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

        $data = DB::connection('mysql')->table('datosMafi_memory')
            ->whereIn('periodo', $periodos)
            ->whereIn('codprograma', $programas)
            ->select('*')
            ->get();
        $dataExcel = $data->map(function ($item) {
            return collect($item)->except('id')->toArray();
        });

        return $dataExcel;
    }

    function buscarEstudiante()
    {

        $id = $_POST['id'];
        $programa = $_POST['programa'];

        $datosEstudiante = DB::connection('sqlsrv')
            ->table('MAFI')
            ->select('primer_apellido', 'sello', 'operador', 'tipoestudiante')
            ->where('idbanner', $id)
            ->where('codprograma', $programa)
            ->first();

        $fechaActual = date("d-m-Y");

        $fecha = DB::table('periodo')->where('activoCiclo1', 1)->select('fechaInicioCiclo1')->first();

        $fechaFormateada = $fecha->fechaInicioCiclo1;

        $fechaFormateada = new DateTime($fechaFormateada);

        $fechaFormateada->modify('-1 week');

        if ($fechaActual <  $fechaFormateada) {
            $tablaConsulta = 'planeacion';
        } else {
            $tablaConsulta = 'programacion';
        }


        if ($datosEstudiante != NULL && $datosEstudiante != null) {
            $materias = DB::table($tablaConsulta . ' as p')
                ->join('mallaCurricular as m', 'p.codMateria', '=', 'm.codigoCurso')
                ->where('p.codBanner', $id)
                ->where('m.codprograma', $programa)
                ->select('p.codMateria', 'm.curso', 'p.semestre')
                ->distinct()
                ->get();

            if ($materias->count() > 0) {
                $datos = [
                    'materias' => $materias,
                    'estudiante' => $datosEstudiante
                ];
            } else {
                $datos = [
                    'materias' => 'Vacio',
                    'estudiante' => $datosEstudiante
                ];
            }
        } else {
            $datos = [];
        }

        return $datos;
    }

    public function datosEstudiante()
    {
        $idBanner = $_POST['idBanner'];
        $totalCreditosMoodle = 0;

        $fechaActual = date("d-m-Y");

        $fecha = DB::table('periodo')->where('activoCiclo1', 1)->select('fechaInicioCiclo1')->first();

        $fechaFormateada = $fecha->fechaInicioCiclo1;

        $fechaFormateada = new DateTime($fechaFormateada);

        $fechaFormateada->modify('-1 week');

        if ($fechaActual <  $fechaFormateada) {
            $tablaConsulta = 'planeacion';
        } else {
            $tablaConsulta = 'programacion';
        }

        $infoEstudiante = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')
            ->select('Nombre', 'Apellido', 'Id_Banner', 'Facultad', 'Programa', 'Codigo_Programa', 'No_Documento', 'Emailpersonal', 'Email', 'Sello', 'Estado_Banner', 'Tipo_Estudiante', 'Autorizado_ASP', 'Operador', 'Convenio')
            ->where('Id_Banner', $idBanner)
            ->first();

        if ($infoEstudiante != null) {

            $codigoPrograma = $infoEstudiante->Codigo_Programa;

            $datosMoodle = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')
                ->select('codigomateria', 'grupo')
                ->where('Id_Banner', $idBanner)
                ->distinct()
                ->get();

            $materiasMoodle = [];

            foreach ($datosMoodle as $dato) {
                $codigo = $dato->codigomateria;
                $consultaMoodle = DB::table('mallaCurricular')
                    ->select('creditos', 'curso')
                    ->where('codigoCurso', $codigo)
                    ->first();
                $materiasMoodle[] = [
                    'creditos' => $consultaMoodle->creditos,
                    'codigoMateria' => $codigo,
                    'materia' => $consultaMoodle->curso,
                ];

                $totalCreditosMoodle += $consultaMoodle->creditos;
            }

            $materiasPlaneadas = DB::table($tablaConsulta . ' as p')
                ->select('p.codMateria', 'm.curso', 'm.creditos', 'm.codigoCurso')
                ->join('mallaCurricular as m', 'm.codigoCurso', '=', 'p.codMateria')
                ->where('codBanner', $idBanner)
                ->where('m.codprograma', $codigoPrograma)
                ->groupBy('p.codMateria', 'm.curso', 'm.creditos', 'm.codigoCurso')
                ->get();

            $totalCreditosPlaneacion = $materiasPlaneadas->sum('creditos');

            $historialAcademico = DB::connection('sqlsrv')->table('mafi_hist_acad')
                ->where('idbanner', $idBanner)
                ->where('cod_programa', $codigoPrograma)
                ->select('materia', 'calificacion', 'creditos', 'id_curso')
                ->get();

            $totalCreditosHistorial = $historialAcademico->sum('creditos');

            $data = [
                'infoEstudiante' => $infoEstudiante,
                'materiasMoodle' => $materiasMoodle,
                'materiasPlaneadas' => $materiasPlaneadas,
                'historialAcademico' => $historialAcademico,
                'totalMoodle' => $totalCreditosMoodle,
                'totalPlaneacion' => $totalCreditosPlaneacion,
                'totalHistorial' => $totalCreditosHistorial,
            ];

            return $data;
        } else {
            return null;
        }
    }

    public function enviarEmail(Request $request)
    {

        $formDataString = $request->input('formData');

        parse_str($formDataString, $formDataArray);

        $crearRegistro = DB::table('solicitudes_sistema')->insert([
            'usuario' => $formDataArray['idUsuario'],
            'tipo_solicitud' => $formDataArray['tipoSolicitud'],
            'solicitud' => $formDataArray['mensaje'],
            'url' => $request->url,
            'fecha' => now(),
        ]);

        if ($crearRegistro) {
            $destinatario = 'ruben.charry@iberoamericana.edu.co, juan.quitian@iberoamericana.edu.co, yeiner.bejarano@ibero.edu.co';
            $asunto = "Nueva Solicitud Hermes";
            $mensaje = "El usuario de id " . $formDataArray['idUsuario'] . " ha hecho la siguiente solicitud: " . $formDataArray['mensaje'] . " con la categoría: " . $formDataArray['tipoSolicitud'];


            // Envío del correo
            $mail = mail($destinatario, $asunto, $mensaje);

            if ($mail) {
                return 'enviado';
            } else {
                return 'Error al enviar el correo';
            }
        } else {
            return null;
        }
    }

    /*
    public function tablaProgramasPeriodos()
    {
        $programas = DB::table('programas')->get();

        $periodosContinua = ['04', '05', '06', '07', '08'];
        $periodosPregradoC = ['11', '12', '13', '16', '17'];
        $periodosPregradoS = ['31', '32', '33', '34', '35'];
        $periodosEspecializacion = ['41', '42', '43', '44', '45'];
        $periodosMaestria = ['51', '52', '53', '54', '55'];


        foreach ($programas as $key) {

            $nivel = $key->nivelFormacion;
            $codprograma = $key->codprograma;

            if ($nivel == 'EDUCACION CONTINUA') {
                $periodos = $periodosContinua;
                foreach ($periodos as $periodo) {
                    DB::table('programasPeriodos')->insert([
                        'codprograma' => $codprograma,
                        'periodo' => $periodo,
                        'estado' => NULL,
                        'fecha_inicio' => NULL
                    ]);
                }
            }

            if ($nivel == 'PROFESIONAL' && $codprograma == 'PPSV' || $codprograma == 'PCPV') {
                $periodos = $periodosPregradoC;
                foreach ($periodos as $periodo) {
                    DB::table('programasPeriodos')->insert([
                        'codprograma' => $codprograma,
                        'periodo' => $periodo,
                        'estado' => NULL,
                        'fecha_inicio' => NULL
                    ]);
                }
            }

            if ($nivel == 'PROFESIONAL' || $nivel == 'TECNOLOGICO' && ($codprograma != 'PPSV' && $codprograma != 'PCPV')) {
                $periodos = $periodosPregradoS;
                foreach ($periodos as $periodo) {
                    DB::table('programasPeriodos')->insert([
                        'codprograma' => $codprograma,
                        'periodo' => $periodo,
                        'estado' => NULL,
                        'fecha_inicio' => NULL
                    ]);
                }
            }

            if ($nivel == 'ESPECIALISTA') {
                $periodos = $periodosEspecializacion;
                foreach ($periodos as $periodo) {
                    DB::table('programasPeriodos')->insert([
                        'codprograma' => $codprograma,
                        'periodo' => $periodo,
                        'estado' => NULL,
                        'fecha_inicio' => NULL
                    ]);
                }
            }

            if ($nivel == 'MAESTRIA') {
                $periodos = $periodosMaestria;
                foreach ($periodos as $periodo) {
                    DB::table('programasPeriodos')->insert([
                        'codprograma' => $codprograma,
                        'periodo' => $periodo,
                        'estado' => NULL,
                        'fecha_inicio' => NULL
                    ]);
                }
            }
        }

        $periodosActivos = DB::table('periodo')->where('periodoActivo', 1)->get();

        $periodos = [];

        foreach ($periodosActivos as $key) {
            $periodo = $key->periodos;
            $periodos[] = substr($periodo, -2);
        }

        var_dump($periodos);

        $update = DB::table('programasPeriodos')->whereIn('periodo', $periodos)->update(['estado' => 1]);
    }
     */

    /**
     * Método para guardar todo los historicos de los graficos
     * @return JSON retorna los historicos da cada grafico mafi
     */

    public function historial_graficos()
    {

        //**/ traemos los periodos activos */

        $periodos = DB::table('periodo')->where('periodoActivo', 1)->get();

        /// traemos todos los programas
        $programas = DB::table('programas')->get();

        foreach ($periodos as $key => $value) {
            foreach ($programas as $key_periodos => $val_programas) {

                // dd($val_programas);
                //-- estado financiero
                $Estado_Financiero = DB::connection('mysql')->table('datosMafi_memory')
                    ->select(DB::raw('COUNT(sello) AS TOTAL, sello'))
                    ->where('codprogram', $val_programas->codprograma)
                    ->groupBy('sello')
                    ->orderByDesc('TOTAL')
                    ->get();
                //  dd($Estado_Financiero);
                // //--- insertamos los datos  del Estado_Financiero todos
                // DB::table('historico_graficos')->insert([
                //     'grafico'=>'Estado Financiero',
                //     'numeros'=>json_encode($Estado_Financiero),
                //     'periodo'=>'todos',
                //     'facultad'=>'todos',
                //     'programa'=>'todos',
                //     'fecha'=>date("d-m-Y"),


                // ]);



                // estado financiero retencion
                $Estado_Financiero_Retencion = DB::connection('mysql')->table('datosMafi_memory')
                    ->select(DB::raw('COUNT(autorizado_asistir) AS TOTAL, autorizado_asistir'))
                    ->groupBy('autorizado_asistir')
                    ->orderByDesc('TOTAL')
                    ->get();

                // //--- insertamos los datos  del Estado_Financiero todos
                // DB::table('historico_graficos')->insert([
                //     'grafico'=>'Estado Financiero',
                //     'numeros'=>json_encode($Estado_Financiero),
                //     'periodo'=>'todos',
                //     'facultad'=>'todos',
                //     'programa'=>'todos',
                //     'fecha'=>date("d-m-Y"),


                // ]);



                //$Estudiantes_nuevos_Estado_Financiero
                $Estudiantes_nuevos_Estado_Financiero = DB::connection('mysql')->table('datosMafi_memory')
                    ->select(DB::raw('COUNT(autorizado_asistir) AS TOTAL, autorizado_asistir'))
                    ->groupBy('autorizado_asistir')
                    ->orderByDesc('TOTAL')
                    ->get();

                // //--- insertamos los datos  del Estado_Financiero todos
                // DB::table('historico_graficos')->insert([
                //     'grafico'=>'Estado Financiero',
                //     'numeros'=>json_encode($Estado_Financiero),
                //     'periodo'=>'todos',
                //     'facultad'=>'todos',
                //     'programa'=>'todos',
                //     'fecha'=>date("d-m-Y"),


                // ]);



                //// $Tipos_de_estudiantes
                $Tipos_de_estudiantes = DB::connection('mysql')->table('datosMafi_memory')
                    ->select(DB::raw('COUNT(autorizado_asistir) AS TOTAL, autorizado_asistir'))
                    ->groupBy('autorizado_asistir')
                    ->orderByDesc('TOTAL')
                    ->get();

                // //--- insertamos los datos  del Estado_Financiero todos
                // DB::table('historico_graficos')->insert([
                //     'grafico'=>'Estado Financiero',
                //     'numeros'=>json_encode($Estado_Financiero),
                //     'periodo'=>'todos',
                //     'facultad'=>'todos',
                //     'programa'=>'todos',
                //     'fecha'=>date("d-m-Y"),


                // ]);




                //  $Operadores
                $Operadores = DB::connection('mysql')->table('datosMafi_memory')
                    ->select(DB::raw('COUNT(autorizado_asistir) AS TOTAL, autorizado_asistir'))
                    ->groupBy('autorizado_asistir')
                    ->orderByDesc('TOTAL')
                    ->get();

                // //--- insertamos los datos  del Estado_Financiero todos
                // DB::table('historico_graficos')->insert([
                //     'grafico'=>'Estado Financiero',
                //     'numeros'=>json_encode($Estado_Financiero),
                //     'periodo'=>'todos',
                //     'facultad'=>'todos',
                //     'programa'=>'todos',
                //     'fecha'=>date("d-m-Y"),


                // ]);

                //  $Programas_con_mayor_cantidad_de_admitidos
                $Programas_con_mayor_cantidad_de_admitidos = DB::connection('mysql')->table('datosMafi_memory')
                    ->select(DB::raw('COUNT(autorizado_asistir) AS TOTAL, autorizado_asistir'))
                    ->groupBy('autorizado_asistir')
                    ->orderByDesc('TOTAL')
                    ->get();

                // //--- insertamos los datos  del Estado_Financiero todos
                // DB::table('historico_graficos')->insert([
                //     'grafico'=>'Estado Financiero',
                //     'numeros'=>json_encode($Estado_Financiero),
                //     'periodo'=>'todos',
                //     'facultad'=>'todos',
                //     'programa'=>'todos',
                //     'fecha'=>date("d-m-Y"),


                // ]);



                ///Programas con mayor cantidad de admitidos

                $Estado_Financiero_Retención = DB::connection('mysql')->table('datosMafi_memory')
                    ->select(DB::raw('COUNT(autorizado_asistir) AS TOTAL, autorizado_asistir'))
                    ->groupBy('autorizado_asistir')
                    ->orderByDesc('TOTAL')
                    ->get();

                // //--- insertamos los datos  del Estado_Financiero todos
                // DB::table('historico_graficos')->insert([
                //     'grafico'=>'Estado Financiero',
                //     'numeros'=>json_encode($Estado_Financiero),
                //     'periodo'=>'todos',
                //     'facultad'=>'todos',
                //     'programa'=>'todos',
                //     'fecha'=>date("d-m-Y"),


                // ]);

                // dd(
                //     $Total_estudiantes_Banner,
                //     $Estado_Financiero,
                //     $Estado_Financiero_Retencion,
                //     $Estudiantes_nuevos_Estado_Financiero,
                //     $Tipos_de_estudiantes,
                //     $Operadores,
                //     $Programas_con_mayor_cantidad_de_admitidos
                // );
            }
        }


        # code...


        /**traemos los datos Total estudiantes Banner 
        SELECT count(estado)as total, estado FROM `datosMafi` GROUP BY estado;
         id	periodo	facultad	programa	grafico	data	fecha	* 
         */
    }
}
