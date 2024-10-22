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
use DateTime;
use App\Http\Util\Constantes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class InformePlaneacionController extends Controller
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

        if ( $fechaInicial < $fechaActual && $fechaActual < $fechaLimiteFormateada ) {
            return true;
        } else {
            return false;
        } */
        $fechaActual = date("Y-m-d H:i:s");
        //$fechaActual = date("2024-10-22 H:i:s");

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
        $fechaInicioProyeccion = date('Y-m-d 00:00:00', strtotime($fechaInicioProyeccion . "-2 day"));
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

    public function selloEstudiantesActivos(Request $request)
    {

        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

        if (!isset($programas) && empty($programas) && isset($facultades) && !empty($facultades)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }


        $consultaFecha = $this->comprobacionFecha();
        
        if ($consultaFecha == true) {
                $consulta = DB::table('estudiantes')
                    ->select('sello', 'autorizado_asistir')
                    ->where('activo', 1)
                    ->where('estado', 'Activo')
                   ->where(function ($query) {
                       $query->where('planeado_ciclo1', 'OK')
                           ->orWhere('planeado_ciclo2', 'OK');
                   })
                    ->whereIn('marca_ingreso', $periodos)
                    ->whereIn('programa', $programas)
                    ->get();
         // dd($consulta);
        } else {
                $consulta = DB::table('estudiantes')
                    ->select('sello', 'autorizado_asistir')
                    ->whereIn('marca_ingreso', $periodos)
                     ->where(function ($query) {
                        $query->where('programado_ciclo1', 'OK')
                            ->orWhere('programado_ciclo2', 'OK');
                     })
                    ->where('estado', 'Activo')
                    ->where('activo', 1)
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
            } else if ($sello == 'TIENE RETENCION' && stripos($estado, 'inactivo') !== false || empty($estado)) {
                $Retencion += 1;
            } else if ($sello == 'TIENE RETENCION' && stripos($estado, 'activo') !== false) {
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

    public function estudiantesRetencion(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

        if (!isset($programas) && empty($programas)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
                $retencion = DB::table('estudiantes')
                    ->select(DB::raw('COUNT(id) as TOTAL'), 'autorizado_asistir')
                    ->whereIn('programa', $programas)
                    ->where('activo', 1)
                    ->where('sello', 'TIENE RETENCION')
                    ->where('estado', 'Activo')
                    ->where(function ($query) {
                        $query->where('planeado_ciclo1', 'OK')
                            ->orWhere('planeado_ciclo2', 'OK');
                    })
                    ->whereIn('marca_ingreso', $periodos)
                    ->whereIn('programa', $programas)
                    ->groupBy('autorizado_asistir')
                    ->get();
       
        } else {
                $retencion = DB::table('estudiantes')
                    ->select(DB::raw('COUNT(id) as TOTAL'), 'autorizado_asistir')
                    ->where('activo', 1) 
                    ->where('sello', 'TIENE RETENCION')
                    ->whereIn('programa', $programas)
                      ->where(function ($query) {
                          $query->where('programado_ciclo1', 'OK')
                             ->orWhere('programado_ciclo2', 'OK');
                      })
                    ->where('estado', 'Activo')
                    ->whereIn('marca_ingreso', $periodos)
                    ->groupBy('autorizado_asistir')
                    ->get();
            
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $retencion));
    }

    public function estudiantesPrimerIngreso(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

        $tiposEstudiante = [
            'PRIMER INGRESO',
            'PRIMER INGRESO PSEUDO INGRES',
            'TRANSFERENTE EXTERNO',
            'TRANSFERENTE EXTERNO (ASISTEN)',
            'TRANSFERENTE EXTERNO PSEUD ING',
            'TRANSFERENTE INTERNO',
        ];

        if (!isset($programas) && empty($programas)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $consultaFecha = $this->comprobacionFecha();
        if ($consultaFecha == true) {

                $consulta = DB::table('estudiantes')
                    ->select('homologante', 'sello', 'autorizado_asistir')
                    ->whereIn('programa', $programas)
                    ->whereIn('tipo_estudiante', $tiposEstudiante)
                    ->where(function ($query) {
                        $query->where('planeado_ciclo1', 'OK')
                            ->orWhere('planeado_ciclo2', 'OK');
                    })
                    ->where('estado', 'Activo')
                    ->where('activo', 1)
                    ->whereIn('marca_ingreso', $periodos)
                    ->get();
           
        } else {
                $consulta = DB::table('estudiantes')
                    ->select('homologante', 'sello', 'autorizado_asistir')
                    ->whereIn('tipo_estudiante', $tiposEstudiante)
                    ->where(function ($query) {
                          $query->where('programado_ciclo1', 'OK')
                            ->orWhere('programado_ciclo2', 'OK');
                      })
                    ->whereIn('programa', $programas)
                    ->where('estado', 'Activo')
                    ->where('activo', 1)
                    ->whereIn('marca_ingreso', $periodos)
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
            } else if ($sello == 'TIENE RETENCION' && stripos($estado, 'inactivo') !== false || empty($estado)) {
                $Retencion += 1;
            } else if ($sello == 'TIENE RETENCION' && stripos($estado, 'activo') !== false) {
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

    public function estudiantesAntiguos(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

            /*
            'ESTUDIANTE ANTIGUO',
            'ESTUDIANTE ANTIGUO (ASISTENT)',
            'ESTUDIANTE ANTIGUO (PSEUDO EG)',
            'ESTUDIANTE ANTIGUO (RECUPERO)',
            'INGRESO SINGULAR (ASISTENTE)',
            'MOVILIDAD ENTRANTE',
            'MOVILIDAD ENTRANTE EXTRANJERO',
            'OPCION DE GRADO',
            'PRIMER INGRESO',
            'PRIMER INGRESO PSEUDO INGRES',
            'PSEUDO ACTIVOS',
            'REINGRESO',
            'TRANSFERENTE EXTERNO',
            'TRANSFERENTE EXTERNO (ASISTEN)',
            'TRANSFERENTE EXTERNO PSEUD ING',
            'TRANSFERENTE INTERNO ',
        
        */
       /* $tiposEstudiante = [
            'PRIMER INGRESO',
            'PRIMER INGRESO PSEUDO INGRES',
            'TRANSFERENTE EXTERNO',
            'TRANSFERENTE EXTERNO (ASISTEN)',
            'TRANSFERENTE EXTERNO PSEUD ING',
            'TRANSFERENTE INTERNO',
        ];*/

        $tiposEstudiante = [
            'ESTUDIANTE ANTIGUO',
            'ESTUDIANTE ANTIGUO (ASISTENT)',
            'ESTUDIANTE ANTIGUO (PSEUDO EG)',
            'ESTUDIANTE ANTIGUO (RECUPERO)',
            'OPCION DE GRADO',
          
        ];

        if (!isset($programas) && empty($programas)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $consultaFecha = $this->comprobacionFecha();
        //dd( $consultaFecha);
        if ($consultaFecha == true) {

                $consulta = DB::table('estudiantes')
                    ->select('homologante', 'sello', 'autorizado_asistir')
                    ->whereIn('programa', $programas)
                    ->whereNotIn('tipo_estudiante', $tiposEstudiante)
                    ->where(function ($query) {
                        $query->where('planeado_ciclo1', 'OK')
                            ->orWhere('planeado_ciclo2', 'OK');
                    })
                    ->where('estado', 'Activo')
                    ->where('activo', 1)
                    ->whereIn('programa', $programas)
                    ->whereIn('marca_ingreso', $periodos)
                    ->get();
            
        } else {
           
                $consulta = DB::table('estudiantes')
                    ->select('homologante', 'sello', 'autorizado_asistir')
                    ->whereIn('programa', $programas)
                    ->whereIn('tipo_estudiante', $tiposEstudiante)
                    ->where(function ($query) {
                        $query->where('programado_ciclo1', 'OK')
                          ->orWhere('programado_ciclo2', 'OK');
                    })
                    ->where('estado', 'Activo')
                    ->where('activo', 1)
                    ->whereIn('marca_ingreso', $periodos)
                    //->whereNotIn('marca_ingreso', $periodos)
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
            } else if ($sello == 'TIENE RETENCION' && stripos($estado, 'inactivo') !== false || empty($estado)) {
                $Retencion += 1;
            } else if (($sello == 'TIENE RETENCION' && stripos($estado, 'activo') !== false) || ($sello == 'TIENE RETENCION' && stripos($estado, 'ACTIVO') !== false) ) {
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

    public function tiposEstudiantes(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

        if (!isset($programas) && empty($programas)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $consultaFecha = $this->comprobacionFecha();
        if ($consultaFecha == true) {

                $tipoEstudiantes = DB::table('estudiantes')
                    ->selectRaw('COUNT(homologante) as TOTAL, tipo_estudiante')
                    ->whereIn('programa', $programas)
                    ->whereIn('marca_ingreso', $periodos)
                    ->where(function ($query) {
                        $query->where('planeado_ciclo1', 'OK')
                            ->orWhere('planeado_ciclo2', 'OK');
                    })
                    ->where('estado', 'Activo')
                    ->where('activo', 1)
                    ->groupBy('tipo_estudiante')
                    ->orderByDesc('TOTAL')
                    ->limit(5)
                    ->get();
           
        } else {

                $tipoEstudiantes = DB::table('estudiantes')
                    ->where('estado', 'Activo')
                    ->where('activo', 1)
                    ->whereIn('marca_ingreso', $periodos)
                    ->where(function ($query) {
                        $query->where('programado_ciclo1', 'OK')
                            ->orWhere('programado_ciclo2', 'OK');
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

    public function operadores(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

        if (!isset($programas) && empty($programas)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
                $operadores = DB::table('estudiantes')
                    ->selectRaw('COUNT(homologante) as TOTAL, operador')
                    ->whereIn('programa', $programas)
                    ->whereIn('marca_ingreso', $periodos)
                    ->where(function ($query) {
                        $query->where('planeado_ciclo1', 'OK')
                            ->orWhere('planeado_ciclo2', 'OK');
                    })
                    ->where('estado', 'Activo')
                    ->where('activo', 1)
                    ->groupBy('operador')
                    ->orderByDesc('TOTAL')
                    ->limit(5)
                    ->get();

        } else {

                $operadores = DB::table('estudiantes')
                    ->selectRaw('COUNT(homologante) as TOTAL, operador')
                    ->where('estado', 'Activo')
                    ->where('activo', 1)
                    ->whereIn('marca_ingreso', $periodos)
                      ->where(function ($query) {
                          $query->where('programado_ciclo1', 'OK')
                              ->orWhere('programado_ciclo2', 'OK');
                      })
                    ->whereIn('programa', $programas)
                    ->groupBy('operador')
                    ->orderByDesc('TOTAL')
                    ->limit(5)
                    ->get();
            
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $operadores));
    }

    public function EstudiantesPorPrograma(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');


        if (!isset($programas) && empty($programas)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
                $programas = DB::table('estudiantes')
                    ->selectRaw('COUNT(DISTINCT homologante) as TOTAL, programa')
                    ->whereIn('marca_ingreso', $periodos)
                    ->whereIn('programa', $programas)
                    ->where(function ($query) {
                        $query->where('planeado_ciclo1', 'OK')
                            ->orWhere('planeado_ciclo2', 'OK');
                    })
                    ->where('activo', 1)
                    ->groupBy('programa')
                    ->orderByDesc('TOTAL')
                    ->limit(5)
                    ->get();
            
        } else {     
                $programas = DB::table('estudiantes')
                    ->where('estado', 'Activo')
                    ->whereIn('marca_ingreso', $periodos)
                    ->selectRaw('COUNT(homologante) as TOTAL, programa')
                    ->where(function ($query) {
                          $query->where('programado_ciclo1', 'OK')
                              ->orWhere('programado_ciclo2', 'OK');
                     })
                    ->whereIn('programa', $programas)
                    ->groupBy('programa')
                    ->orderBy('TOTAL', 'DESC')
                    ->limit(5)
                    ->get();
             
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $programas));
    }

    public function tiposEstudiantesTotal(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

        if (!isset($programas) && empty($programas)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $consultaFecha = $this->comprobacionFecha();
        if ($consultaFecha == true) {

                $tipoEstudiantes = DB::table('estudiantes')
                    ->selectRaw('COUNT(homologante) as TOTAL, tipo_estudiante')
                    ->whereIn('programa', $programas)
                    ->whereIn('marca_ingreso', $periodos)
                    ->where(function ($query) {
                        $query->where('planeado_ciclo1', 'OK')
                            ->orWhere('planeado_ciclo2', 'OK');
                    })
                    ->where('estado', 'Activo')
                    ->where('activo', 1)
                    ->groupBy('tipo_estudiante')
                    ->orderByDesc('TOTAL')
                    ->limit(20)
                    ->get();
            
        } else {

                $tipoEstudiantes = DB::table('estudiantes')
                    ->where('estado', 'Activo')
                    ->where('activo', 1)
                    ->whereIn('marca_ingreso', $periodos)
                      ->where(function ($query) {
                          $query->where('programado_ciclo1', 'OK')
                              ->orWhere('programado_ciclo2', 'OK');
                      })
                    ->whereIn('programa', $programas)
                    ->selectRaw('COUNT(homologante) as TOTAL, tipo_estudiante')
                    ->groupBy('tipo_estudiante')
                    ->orderByDesc('TOTAL')
                    ->limit(20)
                    ->get();
   
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $tipoEstudiantes));
    }

    public function operadoresTotal(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

        if (!isset($programas) && empty($programas)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
                $operadores = DB::table('estudiantes')
                    ->selectRaw('COUNT(homologante) as TOTAL, operador')
                    ->whereIn('programa', $programas)
                    ->whereIn('marca_ingreso', $periodos)
                    ->where(function ($query) {
                        $query->where('planeado_ciclo1', 'OK')
                            ->orWhere('planeado_ciclo2', 'OK');
                    })
                    ->where('estado', 'Activo')
                    ->where('activo', 1)
                    ->groupBy('operador')
                    ->orderByDesc('TOTAL')
                    ->limit(20)
                    ->get();
 
        } else {
                $operadores = DB::table('estudiantes')
                    ->where('estado', 'Activo')
                    ->where('activo', 1)
                    ->whereIn('marca_ingreso', $periodos)
                     ->where(function ($query) {
                         $query->where('programado_ciclo1', 'OK')
                             ->orWhere('programado_ciclo2', 'OK');
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

    public function estudiantesPorProgramaTotal(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');


        if (!isset($programas) && empty($programas)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
                $programas = DB::table('estudiantes')
                    ->selectRaw('COUNT(DISTINCT homologante) as TOTAL, programa')
                    ->whereIn('marca_ingreso', $periodos)
                    ->whereIn('programa', $programas)
                    ->where(function ($query) {
                        $query->where('planeado_ciclo1', 'OK')
                            ->orWhere('planeado_ciclo2', 'OK');
                    })
                    ->where('estado', 'Activo')
                    ->where('activo', 1)
                    ->groupBy('programa')
                    ->orderByDesc('TOTAL')
                    ->limit(20)
                    ->get();
            
        } else {
                $programas = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->where('activo', 1)
                    ->whereIn('marca_ingreso', $periodos)
                      ->where(function ($query) {
                          $query->where('programado_ciclo1', 'OK')
                             ->orWhere('programado_ciclo2', 'OK');
                      })
                    ->whereIn('programa', $programas)
                    ->selectRaw('COUNT(homologante) as TOTAL, programa')
                    ->groupBy('programa')
                    ->orderBy('TOTAL', 'DESC')
                    ->limit(20)
                    ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $programas));
    }

    public function tablaProgramasFacultad(Request $request)
    {

        $periodos = $request->input('periodos');
        $facultades = $request->input('facultad');

        $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();

        $consultaFecha = $this->comprobacionFecha();

        $variable1 = '';
        $variable2 = '';

        if ($consultaFecha == true) {
            $variable1 = 'planeado_ciclo1';
            $variable2 = 'planeado_ciclo2';
        }else{
            $variable1 = 'programado_ciclo1';
            $variable2 = 'programado_ciclo2';
        }

        $estudiantesPrograma = DB::table('estudiantes')
            ->whereIn('marca_ingreso', $periodos)
            ->where(function ($query) use ($variable1, $variable2) {
                $query->where($variable1, 'OK')
                    ->orWhere($variable2, 'OK');
            })
            ->select(DB::raw('COUNT(homologante) AS TOTAL'), 'programa')
            ->whereIn('programa', $programas)
            ->where('estado', 'Activo')
            ->where('activo', 1)
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
            ->where(function ($query) use ($variable1, $variable2) {
                $query->where($variable1, 'OK')
                    ->orWhere($variable2, 'OK');
            })
            ->select(DB::raw('COUNT(programa) AS total'), 'programa', 'sello', 'autorizado_asistir')
            ->whereIn('programa', $programas)
            ->where('estado', 'Activo')
            ->where('activo', 1)
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

            if ($sello == 'TIENE RETENCION' && empty($estado) || $sello == "NO EXISTE") {
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

    // public function tablaProgramasP(Request $request)
    // {
    //     $periodos = $request->input('periodos');
    //     $programas = $request->input('programa');
    //     $estudiantes=[];
    //     $consultaFecha = $this->comprobacionFecha();

    //     $variable1 = '';
    //     $variable2 = '';

    //     if ($consultaFecha == true) {
    //         $variable1 = 'planeado_ciclo1';
    //         $variable2 = 'planeado_ciclo2';
    //     }else{
    //         $variable1 = 'programado_ciclo1';
    //         $variable2 = 'programado_ciclo2';
    //     }

    //     $estudiantesPrograma = DB::table('estudiantes')
    //         ->whereIn('marca_ingreso', $periodos)
    //         ->select(DB::raw('COUNT(homologante) as TOTAL'), 'programa')
    //       ->where(function ($query) use ($variable1, $variable2) {
    //           $query->where($variable1, 'OK')
    //               ->orWhere($variable2, 'OK');
    //       })
    //         ->whereIn('programa', $programas)
    //         ->where('estado', 'Activo')
    //         ->where('activo', 1)
    //         ->groupBy('programa')
    //         ->get();

    //     foreach ($estudiantesPrograma as $key) {
    //        // var_dump($estudiantesPrograma);die;
    //         $programa = $key->programa;
    //         $consultaNombre = DB::table('programas')->where('codprograma', $programa)->select('programa')->first();
    //         $nombre[$programa] = $consultaNombre->programa;
    //         $estudiantes[$programa] = $key->TOTAL;
    //     }

    //     $consultaSello = DB::table('estudiantes')
    //         ->whereIn('marca_ingreso', $periodos)
    //         ->where(function ($query) use ($variable1, $variable2) {
    //             $query->where($variable1, 'OK')
    //                 ->orWhere($variable2, 'OK');
    //         })
    //         ->selectRaw('COUNT(programa) as total, programa, sello, autorizado_asistir')
    //         ->whereIn('programa', $programas)
    //         ->where('estado', 'Activo')
    //         ->where('activo', 1)
    //         ->groupBy('sello', 'programa', 'autorizado_asistir')
    //         ->get();

    //     foreach ($consultaSello as $key) {
    //         $programa = $key->programa;
    //         $sello = $key->sello;
    //         $total = $key->total;
    //         $estado = $key->autorizado_asistir;

    //         if ($sello == 'TIENE SELLO FINANCIERO') {
    //             if (isset($estudiantesSello[$programa])) {
    //                 $estudiantesSello[$programa] = $total + $estudiantesSello[$programa];
    //             } else {
    //                 $estudiantesSello[$programa] = $total;
    //             }
    //         } else if ($sello == 'TIENE RETENCION' && empty($estado) && stripos($estado, 'inactivo') !== false || empty($estado)) {
    //             if (isset($estudiantesRetencion[$programa])) {
    //                 $estudiantesRetencion[$programa] = $total;
    //             } else {
    //                 $estudiantesRetencion[$programa] = $total;
    //             }
    //         } else if ($sello == 'TIENE RETENCION' && !empty($estado) && stripos($estado, 'activo') !== false) {
    //             if (isset($estudiantesASP[$programa])) {
    //                 $estudiantesASP[$programa] = $total + $estudiantesASP[$programa];
    //             } else {
    //                 $estudiantesASP[$programa] = $total;
    //             }
    //         }

    //         if (isset($estudiantesTotal[$programa])) {
    //             $estudiantesTotal[$programa] = $total + $estudiantesTotal[$programa];
    //         } else {
    //             $estudiantesTotal[$programa] = $total;
    //         }
    //     }

    //     $data = [];
    //    // var_dump($estudiantes);die;
    //     foreach ($estudiantes as $key => $value) {
    //         $data[$key] = [
    //             'programa' => isset($nombre[$key]) ? $nombre[$key] : 0,
    //             'Total' => isset($estudiantesTotal[$key]) ? $estudiantesTotal[$key] : 0,
    //             'Sello' => isset($estudiantesSello[$key]) ? $estudiantesSello[$key] : 0,
    //             'ASP' => isset($estudiantesASP[$key]) ? $estudiantesASP[$key] : 0,
    //             'Retencion' => isset($estudiantesRetencion[$key]) ? $estudiantesRetencion[$key] : 0,
    //         ];
    //     }

    //     $Data = (object) $data;

    //     return $Data;
    // }
    public function tablaProgramasP(Request $request)
    {
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');
        $estudiantes=[];
        $consultaFecha = $this->comprobacionFecha();

        $variable1 = '';
        $variable2 = '';

        if ($consultaFecha == true) {
            $variable1 = 'planeado_ciclo1';
            $variable2 = 'planeado_ciclo2';
        }else{
            $variable1 = 'programado_ciclo1';
            $variable2 = 'programado_ciclo2';
        }

        $estudiantesPrograma = DB::table('estudiantes')
            ->whereIn('marca_ingreso', $periodos)
            ->select(DB::raw('COUNT(homologante) as TOTAL'), 'programa')
           ->where(function ($query) use ($variable1, $variable2) {
               $query->where($variable1, 'OK')
                   ->orWhere($variable2, 'OK');
           })
            ->whereIn('programa', $programas)
            ->where('estado', 'Activo')
            ->where('activo', 1)
            ->groupBy('programa')
            ->get();

        foreach ($estudiantesPrograma as $key) {
           // var_dump($estudiantesPrograma);die;
            $programa = $key->programa;
            $consultaNombre = DB::table('programas')->where('codprograma', $programa)->select('programa')->first();
            $nombre[$programa] = $consultaNombre->programa;
            $estudiantes[$programa] = $key->TOTAL;
        }

        $consultaSello = DB::table('estudiantes')
            ->whereIn('marca_ingreso', $periodos)
            ->where(function ($query) use ($variable1, $variable2) {
                $query->where($variable1, 'OK')
                    ->orWhere($variable2, 'OK');
            })
            ->selectRaw('COUNT(programa) as total, programa, sello, autorizado_asistir')
            ->whereIn('programa', $programas)
            ->where('estado', 'Activo')
            ->where('activo', 1)
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
            } else if ($sello == 'TIENE RETENCION' && empty($estado) && stripos($estado, 'inactivo') !== false || empty($estado)) {
                if (isset($estudiantesRetencion[$programa])) {
                    $estudiantesRetencion[$programa] = $total;
                } else {
                    $estudiantesRetencion[$programa] = $total;
                }
            } else if ($sello == 'TIENE RETENCION' && !empty($estado) && stripos($estado, 'activo') !== false) {
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
       // var_dump($estudiantes);die;
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
    // public function mallaPrograma(Request $request)
    // {
    //     $programa = $request->input('programa');
    //     $periodos = $request->input('periodos');
    //     $operador = $request->input('operador');

    //     //dd($programa);
    //     $consultaFecha = $this->comprobacionFecha();
    //    //dd( $consultaFecha);
    //     if ($consultaFecha == true) {
    //         $tablaConsulta = 'planeacion';
    //     } else {
    //         $tablaConsulta = 'programacion';
    //     }

    //     $data = [];

    //     $facultad = ' ';

    //     $consultaFacultad = DB::table('programas')->select('Facultad')->where('codprograma', $programa)->first();
    //     if ($consultaFacultad) {
    //         $facultad = $consultaFacultad->Facultad;
    //     }

    //     $data = [];
    //     /**Estudiantes planeados del programa consultado */
    //     $consultaSello = DB::table(  $tablaConsulta.' as p')
    //         ->select('p.codMateria')
    //         ->where('p.codprograma', $programa)
    //         ->whereIn('p.periodo', $periodos)
    //         ->when(empty($operador), function($query) {
    //             return $query->where('p.operador', '!=', 'EDUPOL2')
    //                 ->where('p.operador', 'NOT LIKE', '%ICBF/Icetex%');
    //         })
    //         ->when(!empty($operador), function($query) use ($operador) {
    //             if ($operador == 'Edupol') {
    //                 return $query->where('p.operador', 'EDUPOL2');
    //             } elseif ($operador == 'Icetex') {
    //                 return $query->where('p.operador', 'LIKE', '%ICBF/Icetex%');
    //             }
    //         }) 
    //         ->groupBy('p.codMateria')
    //         ->get();


    //         //dd($consultaSello);
    //     foreach ($consultaSello as $sello) {
    //         $materia = $sello->codMateria;

    //         $estudiantesSello = 0;
    //         $estudiantesRetencion = 0;
    //         $estudiantesASP = 0;
    //         $total = 0;

    //         $consultaEstudiantes = DB::table($tablaConsulta . ' as p')
    //             ->join('estudiantes as e', 'p.codBanner', '=', 'e.homologante')
    //             ->select('codBanner')
    //             ->when(empty($operador), function($query) {
    //                 return $query->where('p.operador', '!=', 'EDUPOL2')
    //                     ->where('p.operador', 'NOT LIKE', '%ICBF/Icetex%');
    //             })
    //             ->when(!empty($operador), function($query) use ($operador) {
    //                 if ($operador == 'Edupol') {
    //                     return $query->where('p.operador', 'EDUPOL2');
    //                 } elseif ($operador == 'Icetex') {
    //                     return $query->where('p.operador', 'LIKE', '%ICBF/Icetex%');
    //                 }
    //             }) 
    //             ->where('p.codprograma', $programa)
    //             ->whereIn('p.periodo', $periodos)
    //             ->where('p.codMateria', $materia)
    //             ->groupBy('codBanner')
    //             ->get();

    //         foreach ($consultaEstudiantes as $estudiante) {
                
    //             $consulta = DB::table('estudiantes')->select('sello', 'autorizado_asistir')->where('homologante', $estudiante->codBanner)->where('programa', $programa)->first();
    //             if(!isset($consulta)){
    //                 continue;
    //             }    
    //             $dato = $consulta->sello;
    //             $estado = $consulta->autorizado_asistir;
    //             if ($dato == 'TIENE SELLO FINANCIERO') {

    //                 $estudiantesSello += 1;
    //             } else if ($dato == 'TIENE RETENCION' && empty($estado)) {
    //                 $estudiantesRetencion += 1;               
    //             } else if ($dato == 'TIENE RETENCION' && stripos($estado, 'activo') !== false) {
    //                 $estudiantesASP += 1;
    //             }
    //             $total+=1;
    //         }

    //         $consultaNombre = DB::table('mallaCurricular')
    //             ->select('curso', 'semestre', 'ciclo', 'creditos')
    //             ->where('codprograma', $programa)
    //             ->where('codigoCurso', $materia)
    //             ->first();

    //         $semestre = ' ';
    //         $ciclo = ' ';
    //         $creditos = ' ';
    //         $nombre = ' ';

    //         if ($consultaNombre) {
    //             $semestre = $consultaNombre->semestre;
    //             $ciclo = $consultaNombre->ciclo;
    //             $creditos = $consultaNombre->creditos;
    //             $nombre = $consultaNombre->curso;
    //         }

    //         $data[$materia] = [
    //             'nombreMateria' => $nombre,
    //             'Total' => $total,
    //             'Sello' => $estudiantesSello,
    //             'Retencion' => $estudiantesRetencion,
    //             'ASP' => $estudiantesASP,
    //             'Semestre' => $semestre,
    //             'Ciclo' => $ciclo,
    //             'Creditos' => $creditos,
    //             'Facultad' => $facultad,
    //         ];
    //     }


    //     if(!empty($data)){
    //         $Data = (object) $data;
    //         return $Data;
    //     }else{
    //         return null;
    //     }
    // }


    public function mallaPrograma(Request $request)
    {
        $programa = $request->input('programa');
        $periodos = $request->input('periodos');
        $operador = $request->input('operador');

        if($operador == null ){
            $operador="Ibero";
        }

        $consultaFecha = $this->comprobacionFecha();
       
        if ($consultaFecha == true) {
            $tablaConsulta = 'planeacion';
        } else {
            $tablaConsulta = 'programacion';
        }

        $data = [];

        $facultad = ' ';

        $consultaFacultad = DB::table('programas')->select('Facultad')->where('codprograma', $programa)->first();
        if ($consultaFacultad) {
            $facultad = $consultaFacultad->Facultad;
        }

        $data = [];

            $consultaSello = DB::table('consolidado_docentes as p')
            ->select('*')
            ->where('p.Cod_Progr', $programa)
            ->when($periodos, function($query) use ($periodos) {
                return $query->where(function($query) use ($periodos) {
                    foreach ($periodos as $periodo) {
                        $query->orWhere('p.Periodo', 'LIKE', '%' . $periodo . '%');
                    }
                });
            })
            ->where("p.Poblacion",$operador)
            ->get();


            foreach ($consultaSello as $key =>$sello) {
                
               //dd($sello->codMateria);

            $data[$key] = [
                
                'nombreMateria' => $sello->Curso,
                'Total' =>  $sello->total,
                'Sello' =>  $sello->sellos,
                'Retencion' =>  $sello->Retencion,
                'ASP' =>  $sello->Asp,
                'Semestre' =>  $sello->Semestre,
                'Ciclo' => $sello->Ciclo,
                'Creditos' =>  $sello->Creditos,
                'Facultad' =>  $facultad,
                'Periodo' =>  $sello->Periodo,
                'codMateria' => $sello->codMateria,
                'grupo'=>$sello->grupo

            ];



               
               
        }


        if(!empty($data)){
            $Data = (object) $data;
            return $Data;
        }else{
            return null;
        }
    }


    public function estudiantesMateria(Request $request)
    {
        $programa = $request->input('programa');
        $consultaFecha = $this->comprobacionFecha();

         if ($consultaFecha == true) {
             $tablaConsulta = 'planeacion';
         } else {
             $tablaConsulta = 'programacion';
         }

            $estudiantes =   DB::table($tablaConsulta . ' as p')
            ->join('mallaCurricular AS m', 'p.codMateria', '=', 'm.codigoCurso')
            ->join('estudiantes AS e', 'e.homologante', '=', 'p.codBanner')
            ->select('p.codBanner', 'p.codMateria', DB::raw('MAX(m.curso) as curso'), 'm.creditos')
            ->where('m.codPrograma', $programa)
                    ->where('p.codPrograma', $programa)
            ->where('validacion', 'Valida')
            ->where(function($query) {
                $query->where('e.cod_ruta', '=', DB::raw('m.plan_homologacion'))
                    ->orWhere(function($query) {
                        $query->where('e.cod_ruta', '=', '')
                                ->where('m.plan_homologacion', '=', 0);
                    });
            })
            ->groupBy('p.codBanner', 'p.codMateria', 'p.plan', 'm.creditos')
            ->get();

        return $estudiantes;
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

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
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

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
            $tablaConsulta = 'planeacion';
        } else {
            $tablaConsulta = 'programacion';
        }

        $infoEstudiante = DB::connection('mysql')->table('V_Reporte_Ausentismo_memory')
            ->select('Nombre', 'Apellido', 'Id_Banner', 'Facultad', 'Programa', 'Cod_programa', 'No_Documento', 'Email', 'Sello', 'Estado_Banner', 'Tipo_Estudiante', 'Autorizado_ASP', 'Operador')
            ->where('Id_Banner', $idBanner)
            ->first();    
            
            if($infoEstudiante){
                $codigoPrograma = $infoEstudiante->Cod_programa;
            }else{
                $infoEstudiante = DB::connection('sqlsrv')->table('MAFI')
                    ->select('PRIMER_NOMBRE AS Nombre', 'PRIMER_APELLIDO as Apellido', 'IDBANNER as Id_Banner', 'FACULTAD as Facultad', 'CODPROGRAMA as Cod_programa', 'IDENTIFICACION as No_Documento', 'EMAIL_INSTITUCIONAL as Email', 'SELLO as Sello', 'ESTADO as Estado_Banner', 'TIPOESTUDIANTE as Tipo_Estudiante', 'AUTORIZADO_ASISTIR as Autorizado_ASP', 'OPERADOR as Operador')
                    ->where('IDBANNER', $idBanner)
                    ->first();
                    $codigoPrograma = $infoEstudiante->Cod_programa;
                    $consultaNombrePrograma = DB::table('programas')->where('codprograma', $codigoPrograma)->select('programa')->first();
                    $programa = $consultaNombrePrograma->programa;
                    $infoEstudiante->Programa = $programa;

                //$consultaPrograma = DB::table($tablaConsulta)->where('codBanner', $idBanner)->select('codprograma')->first();
            }

            $datosMoodle = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')
                ->select('codigomateria', 'grupo')
                ->where('Id_Banner', $idBanner)
                ->distinct()
                ->get();

            $materiasMoodle = [];

            foreach ($datosMoodle as $dato) {
                $codigo = $dato->codigomateria;
                $consultaMoodle = DB::table('mallaCurricular')
                    ->select('creditos', 'curso', 'semestre')
                    ->where('codigoCurso', $codigo)
                    ->first();

                if ($consultaMoodle != null) {
                    $materiasMoodle[] = [
                        'creditos' => $consultaMoodle->creditos,
                        'codigoMateria' => $codigo,
                        'materia' => $consultaMoodle->curso,
                        'semestre' => $consultaMoodle->semestre,
                    ];
                    $totalCreditosMoodle += $consultaMoodle->creditos;
                }
            }

            $materiasPlaneadas = DB::table($tablaConsulta . ' as p')
                ->select('p.codMateria', 'm.curso', 'm.creditos', 'm.codigoCurso', 'm.semestre', 'm.orden', 'm.ciclo')
                ->join('mallaCurricular as m', 'm.codigoCurso', '=', 'p.codMateria')
                ->where('codBanner', $idBanner)
                ->where('m.codprograma', $codigoPrograma)
                ->groupBy('p.codMateria', 'm.curso', 'm.creditos', 'm.codigoCurso', 'm.semestre', 'm.orden', 'm.ciclo')
                ->get();

            $totalCreditosPlaneacion = $materiasPlaneadas->sum('creditos');

            $historialAcademico = DB::connection('sqlsrv')->table('mafi_hist_acad')
                ->where('idbanner', $idBanner)
                ->where('cod_programa', $codigoPrograma)
                ->select('materia', 'calificacion', 'creditos', 'id_curso')
                ->get();

            $historialArray = [];

            foreach($historialAcademico as $historial)
            {
                $consultaSemestre = DB::connection('mysql')->table('mallaCurricular')->select('semestre')->where('codprograma', $codigoPrograma)->where('codigoCurso', $historial->id_curso)->first();

                $historialArray[] =[
                    'materia' => $historial->materia,
                    'calificacion' => $historial->calificacion,
                    'creditos' => $historial->creditos,
                    'id_curso' => $historial->id_curso,
                    'semestre' => $consultaSemestre->semestre
                ];
            }
    
            $totalCreditosHistorial = $historialAcademico->sum('creditos');

            $data = [
                'infoEstudiante' => $infoEstudiante,
                'materiasMoodle' => $materiasMoodle,
                'materiasPlaneadas' => $materiasPlaneadas,
                'historialAcademico' => $historialArray,
                'totalMoodle' => $totalCreditosMoodle,
                'totalPlaneacion' => $totalCreditosPlaneacion,
                'totalHistorial' => $totalCreditosHistorial,
            ];

            return $data;
        
    }
}
