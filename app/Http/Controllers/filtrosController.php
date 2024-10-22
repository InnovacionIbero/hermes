<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class filtrosController extends Controller
{
    //
    public function render(){
        return view('vistas.moodle.pruebafiltros');
    }
    

    public function getFacultades(){
        $tabla = $_POST['tabla'];
        $facultadesArray = [];
        $facultadesnombreArray = [];
        $facultadesTransversalesidsArray = [];
        $programasArray = [];
        $programasArray2 = [];
        $programasTransversalesArray = [];
        $codProgramasArray = [];
        $codProgramasArray2 = [];
        $codProgramasTransversalArray = [];
        $rol = auth()->user()->id_rol;
        $periodo =[];

        $peridosActivos = DB::table('periodo')
        ->select('periodos')
        ->where('ver_periodo', 1)
        ->get();
        $periodos = $peridosActivos->pluck('periodos')->toArray();
        if($rol == 9 || $rol == 19 || $rol == 20){
            $consultaFacultades = DB::table('facultad')->where('activo',1)->select('id','nombre','transversal')->get();
            foreach ($consultaFacultades as $key => $facultad) {
                array_push($facultadesArray,$facultad);
                if ($facultad->transversal == 1) {
                    array_push($facultadesTransversalesidsArray,$facultad->id);
                }else{
                    array_push($facultadesnombreArray,$facultad->nombre);
                }
            }
            //dd($facultadesTransversalesnombreArray,$facultadesnombreArray);
            $consultaProgramas = DB::table('programas')->select('id','codprograma','programa','Facultad')->whereIn('estado',[1,2])->whereIn('Facultad',$facultadesnombreArray)->get();
            foreach ($consultaProgramas as $key => $programa) {
                array_push($programasArray,$programa);
                array_push($codProgramasArray,$programa->codprograma);
            }

            //dd($facultadesTransversalesidsArray);
            $consultasCursosTransvesales = DB::table('programas as p')
            ->join('mallaCurricular as m', 'm.codprograma', '=', 'p.codprograma')
            ->select('p.id', 'm.codprograma', 'p.programa', 'p.Facultad')
                ->whereIn('p.estado', [1, 2])
                ->whereIn('id_facultad_transversal', $facultadesTransversalesidsArray)
                ->groupBy('m.codprograma', 'p.programa', 'p.id', 'p.Facultad')
                ->get();
            foreach ($consultasCursosTransvesales as $key => $programa) {
                array_push($programasTransversalesArray, $programa);
                array_push($codProgramasTransversalArray, $programa->codprograma);
            };

            //dd($programasArray,$programasTransversalesArray);
            $programasArrayMerge = array_merge($programasArray,$programasTransversalesArray);
            //dd($programasArrayMerge);
            foreach ($this->eliminarDuplicados($programasArrayMerge,['id','codprograma','programa','Facultad']) as $item) {
                if ($item) {
                    array_push($programasArray2,$item);
                }
            }
            //dd($codProgramasArray,$codProgramasTransversalArray);
            $codProgramasArrayMerge = array_merge($codProgramasArray,$codProgramasTransversalArray);
            //dd($codProgramasArrayMerge);
            foreach ($this->eliminarDuplicados($codProgramasArrayMerge,['id']) as $item) {
                if ($item) {
                    array_push($codProgramasArray2,$item);
                }
            }
            //dd($codProgramasArray2);
            //dd($programasArray2);
        }else {
            $facultad = auth()->user()->id_facultad;
            $facultad = trim($facultad,';');
            $facultades = explode(';',$facultad);
            $programa = auth()->user()->programa;
            $programa = trim($programa,';');
            $programas = explode(';',$programa);
            //dd(!empty($facultades[0]));
            if (!empty($facultades[0])) {
                //dd(!empty($programas[0]));
                
                    $consultaFacultades = DB::table('facultad')->select('id', 'nombre', 'transversal')->whereIn('id', $facultades)->get();
                    foreach ($consultaFacultades as $key => $facultad) {
                        array_push($facultadesArray, $facultad);
                        if ($facultad->transversal == 1) {
                            array_push($facultadesTransversalesidsArray, $facultad->id);
                        } else {
                            array_push($facultadesnombreArray, $facultad->nombre);
                        }
                    }
                if(empty($programas[0])){
                    //dd($facultadesTransversalesnombreArray,$facultadesnombreArray);
                    $consultaProgramas = DB::table('programas')->select('id', 'codprograma', 'programa', 'Facultad')->whereIn('estado', [1, 2])->whereIn('Facultad', $facultadesnombreArray)->get();
                    foreach ($consultaProgramas as $key => $programa) {
                        array_push($programasArray, $programa);
                        array_push($codProgramasArray, $programa->codprograma);
                    }

                    //dd($facultadesTransversalesidsArray);
                    $consultasCursosTransvesales = DB::table('programas as p')
                    ->join('mallaCurricular as m', 'm.codprograma', '=', 'p.codprograma')
                    ->select('p.id', 'm.codprograma', 'p.programa', 'p.Facultad')
                    ->whereIn('p.estado', [1, 2])
                    ->whereIn('id_facultad_transversal', $facultadesTransversalesidsArray)
                        ->groupBy('m.codprograma', 'p.programa', 'p.id', 'p.Facultad')
                        ->get();
                    foreach ($consultasCursosTransvesales as $key => $programa) {
                        array_push($programasTransversalesArray, $programa);
                        array_push($codProgramasTransversalArray, $programa->codprograma);
                    };

                    //dd($codProgramasArray,$codProgramasTransversalArray);
                    $programasArrayMerge = array_merge($programasArray, $programasTransversalesArray);
                    //dd($programasArrayMerge);
                    foreach ($this->eliminarDuplicados($programasArrayMerge, ['id', 'codprograma']) as $item) {
                        if ($item) {
                            array_push($programasArray2, $item);
                        }
                    }

                    //dd($codProgramasArray,$codProgramasTransversalArray);
                    $codProgramasArrayMerge = array_merge($codProgramasArray, $codProgramasTransversalArray);
                    //dd($codProgramasArrayMerge);
                    foreach ($this->eliminarDuplicados($codProgramasArrayMerge, ['id']) as $item) {
                        if ($item) {
                            array_push($codProgramasArray2, $item);
                        }
                    }
                    //dd($codProgramasArray2);
                    //dd($programasArray2,$codProgramasArray2);
                } else {
                    $consultaProgramas = DB::table('programas')->select('id', 'codprograma', 'programa', 'Facultad')->whereIn('estado', [1, 2])->whereIn('id', $programas)->get();
                    foreach ($consultaProgramas as $key => $programa) {
                        array_push($programasArray2, $programa);
                        array_push($codProgramasArray2, $programa->codprograma);
                    }
                    //dd($programasArray2,$codProgramasArray2);
                }
            } else {
                $consultaProgramas = DB::table('programas')->select('id', 'codprograma', 'programa', 'Facultad')->whereIn('estado', [1, 2])->whereIn('id', $programas)->get();
                foreach ($consultaProgramas as $key => $programa) {
                    array_push($programasArray2, $programa);
                    array_push($codProgramasArray2, $programa->codprograma);
                }
            }
        }
        //dd($programasArray2,$codProgramasArray2);
        //dd($tabla,$programasArray2);

        $consultaFecha = $this->comprobacionFecha();
        if ($consultaFecha == true) {
            $tablaConsulta = 'planeacion';
        } else {
            $tablaConsulta = 'programacion';
        }

        //dd($tabla);

        switch ($tabla) {
            case 'Mafi':
                $periodosActivos = DB::connection('mysql')->table('datosMafi_memory')->select('periodo')
                    ->whereIn('codprograma', $codProgramasArray2)
                    ->whereIn('periodo', $periodos) // Usar el array de períodos como filtro IN
                    ->groupBy('periodo')
                    ->get();
                //dd($periodosActivos);
                break;
            case 'moodle':
                $periodosActivos = DB::connection('sqlsrv')
                    ->table('V_Reporte_Ausentismo')
                    ->where(function ($query) use ($codProgramasArray2) {
                        foreach ($codProgramasArray2 as $programa) {
                            $query->orWhere('Grupo', 'LIKE', '%' . $programa . '%');
                        }
                    })
                    ->select('Periodo_Rev')
                    ->groupBy('Periodo_Rev')
                    ->get();
                //dd($periodosActivos);
                break;
            case 'moodlecerrados':
                $periodosActivos = DB::table('cierrematriculas')
                ->select('Periodo as Periodo_Rev')
                ->where(function ($query) use ($codProgramasArray2) {
                    foreach ($codProgramasArray2 as $programa) {
                        $query->orWhere('Programa', 'LIKE', '%' . $programa . '%');
                    }
                })
                ->groupBy('Periodo')
                ->get();
                //dd($periodosActivos);
                break;
            case 'planeacion':
                //dd($tablaConsulta);
                $periodosActivos = DB::table($tablaConsulta)->select('periodo')
                    ->whereIn('codprograma', $codProgramasArray2)
                    ->groupBy('periodo')
                    ->get();
                //dd($periodosActivos);
                break;
            
            default:
                # code...
                break;
        }

        if ($periodosActivos) {
            foreach ($periodosActivos as $key => $value) {

                //--- traemos los periodos 
                if ($tabla == 'Mafi' || $tabla == 'planeacion') {

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
        //dd($periodo,$facultadesArray,$programasArray2);
        $data = [
            'periodo'   =>  $periodo,
            'facultades' =>  $facultadesArray,
            'programas' =>  $programasArray2,
        ];
        return $data;
    }

    public function comprobacionFecha()
    {
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

    function eliminarDuplicados(array $array, array $keys =[], $idem = true):array
    {
        // Comparo los valores e identifico que operador de desigualdad usar
        $comparar = function ($a, $b, $key) use ($idem) {
            if ($idem) {
            return $this->getValor($a, $key) !== $this->getValor($b, $key);
            }
            return $this->getValor($a, $key) != $this->getValor($b, $key);
        };

        $determinarDuplicidad = function ($a, $b) use ($keys, $comparar) {
            return array_reduce(
                $keys,
                fn ($acumulador, $key) => ($acumulador === null ? $comparar($a, $b, $key) : $acumulador) || $comparar($a, $b, $key)
            );
        };

        return array_reduce(
            $array,
            fn ($acumulador, $valor) => array_merge(
                array_filter(
                    $acumulador,
                    fn ($valor_filter) => $determinarDuplicidad($valor_filter, $valor)
                ),
                [$valor]
            ),
            []
        );
    }

    function getValor($valor, $key = null)
    {
        switch (gettype($valor)) {
            case 'object':
                return $valor->{$key};
            case 'array':
                return $valor[$key];
            default:
                return $valor;
        }
    }
}
