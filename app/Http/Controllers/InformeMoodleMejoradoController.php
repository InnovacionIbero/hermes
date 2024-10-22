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
use App\Models\ReporteAusentismo;

class InformeMoodleMejoradoController extends Controller
{

    private function calcularRiesgos($riesgos)
    {
        $bajo = $inactivos = $sinIngreso = $medio = $alto = 0;

        foreach ($riesgos as $key) {
            if ($key->Estado_Banner == 'Inactivo') {
                $inactivos += 1;
            } else if ($key->Ultacceso_Plataforma < $key->FechaInicio) {
                $sinIngreso += 1;
            } else {
                if ($key->Riesgo == 'BAJO' && $key->Estado_Banner == 'Activo') {
                    $bajo += 1;
                } else if ($key->Riesgo == 'MEDIO' && $key->Estado_Banner == 'Activo') {
                    $medio += 1;
                } else if ($key->Riesgo == 'ALTO' && $key->Estado_Banner == 'Activo') {
                    $alto += 1;
                }
            }
        }

        return $datos = array(
            'alto' => $alto,
            'medio' => $medio,
            'bajo' => $bajo,
            'ingreso' => $sinIngreso,
            'inactivos' => $inactivos,
        );
    }

    public function riesgoMoocs(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');
        $tipo = $request->input('tipo');

        if (isset($facultades) && !empty($facultades)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $filtros = function ($query) use ($programas, $periodos, $tipo) {
            $query->whereIn('Cod_programa', $programas)
                ->whereIn('Periodo_Rev', $periodos)
                ->where('Nombrecorto', $tipo == 'MOOC' ? 'like' : 'not like', '%MOOC%')
                ->distinct();
        };


        $riesgos = ReporteAusentismo::where($filtros)->get();

        $Total = ReporteAusentismo::selectRaw('COUNT(Id_Banner) AS TOTAL')
            ->where($filtros)
            ->distinct()
            ->pluck('TOTAL')
            ->first();

        $datos = $this->calcularRiesgos($riesgos);

        $datos['total'] = $Total;

        return $datos;
    }

    public function riesgo(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

        //dd($periodos);
        if (isset($facultades) && !empty($facultades)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $riesgos = DB::connection('mysql')->table('V_Reporte_Ausentismo_memory')
            ->select('Riesgo', 'Ult_AccesoACurso', 'Estado_Banner', 'Ultacceso_Plataforma', 'FechaInicio', 'Id_Banner')
            ->whereIn('Cod_programa', $programas)
            ->whereIn('Periodo_Rev', $periodos)
            ->where('Nombrecorto', 'not like', '%MOOC%')
            ->get();

        $Total = DB::connection('mysql')->table('V_Reporte_Ausentismo_memory')
            ->selectRaw('COUNT(Id_Banner) AS TOTAL')
            ->whereIn('Cod_programa', $programas)
            ->whereIn('Periodo_Rev', $periodos)
            ->where('Nombrecorto', 'not like', '%MOOC%')
            ->get();

        $inactivos = 0;
        $alto = 0;
        $medio = 0;
        $bajo = 0;
        $sinIngreso = 0;

        foreach ($riesgos as $key) {
            //var_dump($key->Ultacceso_Plataforma < $key->FechaInicio);die();
            if ($key->Ultacceso_Plataforma > $key->FechaInicio && empty($key->Riesgo)) {
                $bajo += 1;
                //var_dump($key->Id_Banner);die();
            } else if ($key->Estado_Banner == 'Inactivo') {
                $inactivos += 1;
            } else if ($key->Ultacceso_Plataforma < $key->FechaInicio) {
                $sinIngreso += 1;
            } else if ($key->Riesgo == 'BAJO') {
                $bajo += 1;
            } else if ($key->Riesgo == 'MEDIO') {
                $medio += 1;
            } else if ($key->Riesgo == 'ALTO') {
                $alto += 1;
            }
        }

        // var_dump($riesgonoentra);var_dump($noentra);die();


        $total = $Total[0]->TOTAL;

        $datos = array(
            'alto' => $alto,
            'medio' => $medio,
            'bajo' => $bajo,
            'ingreso' => $sinIngreso,
            'inactivos' => $inactivos,
            'total' => $total
        );

        return $datos;
    }


    /**
     * Optimizar consultas y añadir caso de uso MOOCS
     */
    function estudiantesRiesgo(Request $request, $riesgo)
    {
        $riesgo = trim($riesgo);
        // dd($riesgo);
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

        if (isset($facultades) && !empty($facultades)) {

            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $filtros = function ($query) use ($programas, $periodos) {
            $query->whereIn('Periodo_Rev', $periodos)
                ->whereIn('Cod_programa', $programas)
                ->where('Nombrecorto', !empty($_POST['tipo']) ? 'like' : 'not like', '%MOOC%')
                ->select('*')
                ->distinct();
        };

        if ($riesgo == 'INGRESO') {
            $estudiantes = ReporteAusentismo::where('Estado_Banner', 'Activo')
                ->whereColumn('Ultacceso_Plataforma', '<', 'fechaInicio')
                ->where($filtros)
                ->get();
        } else if ($riesgo == 'INACTIVOS') {
            $estudiantes = ReporteAusentismo::where('Estado_Banner', 'Inactivo')
                ->where($filtros)
                ->get();
        } else {
            $estudiantes = ReporteAusentismo::where('Estado_Banner', 'Activo')
                ->where('Riesgo', $riesgo)
                ->whereColumn('Ultacceso_Plataforma', '>', 'fechaInicio')
                ->where($filtros)
                ->get();
        }

        echo json_encode(array('data' => $estudiantes));
    }

    function descargarTodoEstudiantesRiesgo(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');
        $tipo = $request->input('tipo');

        if (isset($facultades) && !empty($facultades)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $estudiantes = ReporteAusentismo::whereIn('Periodo_Rev', $periodos)
            ->whereIn('Cod_programa', $programas)
            ->where('Nombrecorto', $tipo == 'MOOC' ? 'like' : 'not like', '%MOOC%')
            ->select('*')
            ->distinct()
            ->get();

        return $estudiantes;
    }

    function descargarInformeFlash(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');
        $tipo = $request->input('tipo');

        if (isset($facultades) && !empty($facultades)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $estudiantes = ReporteAusentismo::whereIn('Periodo_Rev', $periodos)
            ->whereIn('Cod_programa', $programas)
            ->where('Nombrecorto', $tipo == 'MOOC' ? 'like' : 'not like', '%MOOC%')
            ->select('*')
            ->distinct()
            ->get();

        return $estudiantes;
    }

    function dataAlumno(Request $request)
    {
        //dd($request->input('idBanner'));die;
        $idBanner = $request->input('idBanner');
        $tipo = $request->input('tipo');

        $data = ReporteAusentismo::where('Id_Banner', $idBanner)
            ->where('Nombrecorto', $tipo == 'MOOC' ? 'like' : 'not like', '%MOOC%')
            ->select('*')
            ->get();

        if ($data->isNotEmpty()) {

            header("Content-Type: application/json");
            echo json_encode(array('data' => $data));
        } 
        //dd( $idBanner);die;

    }

    function riesgoAsistencia(Request $request)
    {
        $idBanner = $request->input('idBanner');


        $riesgosAsistencia = DB::connection('mysql')->table('V_Reporte_Ausentismo_memory')->where('Id_Banner', $idBanner)->select('Riesgo', 'Nombrecurso')
            ->groupBy('Riesgo', 'Nombrecurso')
            ->get();

        $Notas = DB::connection('mysql')->table('V_Reporte_Ausentismo_memory')
            ->where('Id_Banner', $idBanner)
            ->select("Nombrecurso", "Nota_Acumulada", "Nota_Primer_Corte", "Nota_Segundo_Corte", "Nota_Tercer_Corte", "FechaInicio", "Duracion_8_16_Semanas", "Cod_programa", "Actividades_Por_Calificar", "Id_Banner", "Cod_materia")
            ->groupBy('nombreCurso', 'Nota_Acumulada', 'Nota_Primer_Corte', 'Nota_Segundo_Corte', 'Nota_Tercer_Corte', 'FechaInicio', 'Duracion_8_16_Semanas', "Cod_programa", 'Actividades_Por_Calificar', 'Id_Banner', "Cod_materia")
            ->get();

        $fechaActual = date("d-m-Y");
        $fechaObj1 = DateTime::createFromFormat("d-m-Y", $fechaActual);
        $definitivas = [];
        $datoDias = [];

        foreach ($Notas as $nota) {

            $actividades = $nota->Actividades_Por_Calificar;

            $codmateria = $nota->Cod_materia;
            $codprograma = $nota->Cod_programa;
            $idBanner = $nota->Id_Banner;

            if ($nota->Nota_Primer_Corte != "Sin Actividad") {
                $nota1 = floatval($nota->Nota_Primer_Corte);
            } else {
                $nota1 = $nota->Nota_Primer_Corte;
            }

            if ($nota->Nota_Segundo_Corte != "Sin Actividad") {
                $nota2 = floatval($nota->Nota_Segundo_Corte);
            } else {
                $nota2 = $nota->Nota_Segundo_Corte;
            }

            if ($nota->Nota_Tercer_Corte != "Sin Actividad") {
                $nota3 = floatval($nota->Nota_Tercer_Corte);
            } else {
                $nota3 = $nota->Nota_Tercer_Corte;
            }

            $notaAcum = floatval($nota->Nota_Acumulada);

            $fechaInicio = (new DateTime($nota->FechaInicio))->format("d-m-Y");
            $nombreCurso = $nota->Nombrecurso;
            $posicionParentesis = strpos($nombreCurso, '(');
            $nombre = ($posicionParentesis !== false) ? substr($nombreCurso, 0, $posicionParentesis) : $nombreCurso;
            $duracion = $nota->Duracion_8_16_Semanas;
            $fechaObj2 = DateTime::createFromFormat("d-m-Y", $fechaInicio);
            $diferencia = $fechaObj1->diff($fechaObj2);
            $diasdif = $diferencia->days;

            $semestreMateria = DB::table('mallaCurricular')->select('semestre')
                ->where('codigoCurso', $codmateria)
                ->where('codprograma', $codprograma)
                ->first();

            /** Validación Notas */
            if ($nota1 != 0 && $nota2 != 0 && $nota3 != 0 && !in_array("Sin Actividad", [$nota1, $nota2, $nota3])) {
                $definitivas[$nombre] = $notaAcum;
            } else {
                if ($nota1 == 0 && $nota2 == 0 && $nota3 == 0 || in_array("Sin Actividad", [$nota1, $nota2, $nota3])) {

                    if ($actividades != NULL) {
                        if ($actividades > 1) {
                            $definitivas[$nombre] =  $nota1 + 3.8;
                        } else {
                            $definitivas[$nombre] =  $nota1 + 2.4;
                        }
                    } else {
                        $definitivas[$nombre] = $notaAcum;
                    }
                } else {
                    if ($duracion == "8 SEMANAS") {
                        if ($nota1 != 0 && $nota2 != 0 && !in_array("Sin Actividad", [$nota1, $nota2])) {
                            if ($diasdif >= 56) {
                                if ($nota3 != "Sin Actividad") {
                                    $definitivas[$nombre] =  1.48 + $nota1 * 0.3 + $nota2 * 0.3;
                                } else {
                                    $definitivas[$nombre] =  $notaAcum;
                                }
                            } else {
                                if ($actividades != NULL) {
                                    $definitivas[$nombre] = $notaAcum * (10 / 6) + 0.6;
                                } else {
                                    $definitivas[$nombre] = $notaAcum * (10 / 6);
                                }
                            }
                        } else {
                            if ($nota1 != 0 && $nota1 != "Sin Actividad") {
                                if ($diasdif >= 42) {
                                    if ($nota2 != "Sin Actividad") {
                                        if ($actividades != NULL) {
                                            if ($actividades > 1) {
                                                $definitivas[$nombre] =  $nota1 + 3.8;
                                            } else {
                                                $definitivas[$nombre] =  $nota1 + 2.4;
                                            }
                                        } else {
                                            $definitivas[$nombre] =  $nota1;
                                        }
                                    } else {
                                        if ($actividades != NULL) {
                                            $definitivas[$nombre] =  ($nota1 * 0.3 + ($nota2 + 2.4)) * (10 / 6);
                                        } else {
                                            $definitivas[$nombre] =  $notaAcum * (10 / 6);
                                        }
                                    }
                                } else {

                                    if ($actividades != NULL) {
                                        if ($actividades > 1) {
                                            $definitivas[$nombre] =  $nota1 + 3.8;
                                        } else {
                                            $definitivas[$nombre] =  $nota1 + 2.4;
                                        }
                                    } else {
                                        $definitivas[$nombre] =  $nota1;
                                    }
                                }
                            } else {

                                if ($nota1 == "Sin Actividad" || $nota1 == 0) {
                                    if ($actividades != NULL) {
                                        if ($actividades > 1) {
                                            $definitivas[$nombre] =  $nota1 + 3.8;
                                        } else {
                                            $definitivas[$nombre] =  $nota1 + 2.4;
                                        }
                                    } else {
                                        $definitivas[$nombre] =  $notaAcum;
                                    }
                                }
                            }
                        }
                    } else {
                        if ($nota1 != 0 && $nota2 != 0 && !in_array("Sin Actividad", [$nota1, $nota2])) {
                            if ($diasdif >= 112) {
                                if ($nota3 != "Sin Actividad") {
                                    $definitivas[$nombre] =  1.48 + $nota1 * 0.3 + $nota2 * 0.3;
                                } else {
                                    $definitivas[$nombre] =  $notaAcum;
                                }
                            } else {
                                if ($actividades != NULL) {
                                    $definitivas[$nombre] = $notaAcum * (10 / 6) + 0.6;
                                } else {
                                    $definitivas[$nombre] = $notaAcum * (10 / 6);
                                }
                            }
                        } else {
                            if ($nota1 != 0 && $nota1 != "Sin Actividad") {
                                if ($diasdif >= 77) {
                                    if ($nota2 != "Sin Actividad") {
                                        if ($actividades != NULL) {
                                            if ($actividades > 1) {
                                                $definitivas[$nombre] =  $nota1 + 3.8;
                                            } else {
                                                $definitivas[$nombre] =  $nota1 + 2.4;
                                            }
                                        } else {
                                            $definitivas[$nombre] =  $nota1;
                                        }
                                    } else {
                                        if ($actividades != NULL) {
                                            $definitivas[$nombre] =  ($nota1 * 0.3 + ($nota2 + 2.4)) * (10 / 6);
                                        } else {
                                            if ($actividades != NULL) {
                                                $definitivas[$nombre] = $notaAcum * (10 / 6) + 0.6;
                                            } else {
                                                $definitivas[$nombre] = $notaAcum * (10 / 6);
                                            }
                                        }
                                    }
                                } else {
                                    if ($actividades != NULL) {
                                        if ($actividades > 1) {
                                            $definitivas[$nombre] =  $nota1 + 3.8;
                                        } else {
                                            $definitivas[$nombre] =  $nota1 + 2.4;
                                        }
                                    } else {
                                        $definitivas[$nombre] =  $nota1;
                                    }
                                }
                            } else {
                                if ($nota1 == "Sin Actividad" || $nota1 == 0) {
                                    if ($actividades != NULL) {
                                        if ($actividades > 1) {
                                            $definitivas[$nombre] =  $nota1 + 3.8;
                                        } else {
                                            $definitivas[$nombre] =  $nota1 + 2.4;
                                        }
                                    } else {
                                        $definitivas[$nombre] =  $notaAcum;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $consultaHistorial = DB::connection('sqlsrv')->table('MAFI_HIST_ACAD')
                ->where('id_curso', $codmateria)
                ->where('idbanner', $idBanner)
                ->where('cod_programa', $codprograma)
                ->first();

            if ($consultaHistorial != null) {
                $definitivas[$nombre] += 0.2;
            }

            if ($definitivas[$nombre] > 5) {
                $semestreMateria = intval($semestreMateria->semestre);
                switch ($semestreMateria) {
                    case 1:
                        $definitivas[$nombre] = 4.5;
                        break;
                    case 2:
                        $definitivas[$nombre] = 4.5;
                        break;
                    case 3:
                        $definitivas[$nombre] = 4.6;
                        break;
                    case 4:
                        $definitivas[$nombre] = 4.6;
                        break;
                    case 5:
                        $definitivas[$nombre] = 4.7;
                        break;
                    case 6:
                        $definitivas[$nombre] = 4.7;
                        break;
                    default:
                        $definitivas[$nombre] = 4.8;
                        break;
                }
            }

            if ($duracion == "8 SEMANAS") {
                if ($diasdif < 21) {
                    $definitivas[$nombre] .= ' - No hay datos suficientes';
                } else {
                    $definitivas[$nombre] .= ' - hay datos';
                }
            } else {
                if ($diasdif < 42) {
                    $definitivas[$nombre] .= ' - No hay datos suficientes';
                } else {
                    $definitivas[$nombre] .= ' - hay datos';
                }
            }
        }

        $datos = array(
            'riesgoAsistencia' => $riesgosAsistencia,
            'notas' => $definitivas,
            'semestre' => $semestreMateria,
        );

        header("Content-Type: application/json");
        echo json_encode(array('data' => $datos));
    }

    function tablaCursos(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');
        $tipo = $request->input('tipo');

        $filtros = function ($query) use ($programas, $periodos, $tipo) {
            $query->whereIn('Programa', $programas)
                ->whereIn('Periodo', $periodos)
                ->whereNotNull('Riesgo_inactivos')
                ->where('Activo', 1)
                ->where('Nombrecorto', $tipo == 'MOOC' ? 'like' : 'not like', '%MOOC%');
        };
        $filtroTipo = function ($query) use ($tipo) {
            $query->where('Nombrecorto', $tipo == 'MOOC' ? 'like' : 'not like', '%MOOC%');
        };

        if (isset($programas) && !empty($programas)) {

            $total = count($programas);

            if ($total > 80) {
                $consultaCursos = DB::table('registros_moodle')->where('Activo', 1)->where($filtroTipo)->whereNotNull('Nombre_curso')->get();
                return $consultaCursos;
            }

            $consultaCursos = DB::table('registros_moodle')
                ->where($filtros)
                ->get();
        } elseif (isset($facultades) && !empty($facultades)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();

            $consultaCursos = DB::table('registros_moodle')
                ->where($filtros)
                ->get();
        } else {
            $consultaCursos = DB::table('registros_moodle')
                ->where('Activo', 1)
                ->whereNotNull('Riesgo_inactivos')
                ->where($filtroTipo)
                ->get();
        }
        return $consultaCursos;
    }


    function tablaCursoscerrados(Request $request)
    {


        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

        if (isset($programas) && !empty($programas)) {

            $total = count($programas);

            if ($total > 80) {
                $consultaCursos = DB::table('cursos_moodle_cerrados')->get();
                return $consultaCursos;
            }

            $consultaCursos = DB::table('cursos_moodle_cerrados')
                ->whereIn('Programa', $programas)
                ->whereIn('Periodo', $periodos)
                ->get();
        } elseif (isset($facultades) && !empty($facultades)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();

            $consultaCursos = DB::table('cursos_moodle_cerrados')
                ->whereIn('Programa', $programas)
                ->whereIn('Periodo', $periodos)

                ->get();
        } else {
            $consultaCursos = DB::table('cursos_moodle_cerrados')->get();
        }
        return $consultaCursos;
    }

    function descargarDatosCurso(Request $request)
    {
        $idCurso = $request->input('id');
        $programa = $request->input('programa');

        $Notas = DB::connection('mysql')->table('V_Reporte_Ausentismo_memory')
            ->select('Nota_Acumulada', 'Nota_Primer_Corte', 'Nota_Segundo_Corte', 'Nota_Tercer_Corte', 'FechaInicio', 'Cod_materia', 'Cod_programa', 'Duracion_8_16_Semanas', 'Actividades_Por_Calificar', 'Id_Banner', 'FechaInicio', 'Ult_AccesoACurso', 'Periodo_Rev', 'Estado_Banner')
            ->where('Idcurso', $idCurso)
            ->where('Cod_Programa', $programa)
            ->groupBy('Nota_Acumulada', 'Nota_Primer_Corte', 'Nota_Segundo_Corte', 'Nota_Tercer_Corte', 'FechaInicio', 'Cod_materia', 'Cod_programa', 'Duracion_8_16_Semanas', 'Actividades_Por_Calificar', 'Id_Banner', 'FechaInicio', 'Ult_AccesoACurso', 'Periodo_Rev', 'Estado_Banner')
            ->get();

        $fechaActual = date("d-m-Y");
        $fechaObj1 = DateTime::createFromFormat("d-m-Y", $fechaActual);
        $definitiva = '';
        $datos = [];

        foreach ($Notas as $nota) {

            $actividades = $nota->Actividades_Por_Calificar;
            $codmateria = $nota->Cod_materia;
            $codprograma = $nota->Cod_programa;
            $idBanner = $nota->Id_Banner;
            $periodo = $nota->Periodo_Rev;

            if ($nota->Nota_Primer_Corte != "Sin Actividad") {
                $nota1 = floatval($nota->Nota_Primer_Corte);
            } else {
                $nota1 = $nota->Nota_Primer_Corte;
            }

            if ($nota->Nota_Segundo_Corte != "Sin Actividad") {
                $nota2 = floatval($nota->Nota_Segundo_Corte);
            } else {
                $nota2 = $nota->Nota_Segundo_Corte;
            }

            if ($nota->Nota_Tercer_Corte != "Sin Actividad") {
                $nota3 = floatval($nota->Nota_Tercer_Corte);
            } else {
                $nota3 = $nota->Nota_Tercer_Corte;
            }

            $notaAcum = floatval($nota->Nota_Acumulada);

            $fechaInicio = (new DateTime($nota->FechaInicio))->format("d-m-Y");
            $duracion = $nota->Duracion_8_16_Semanas;
            $fechaObj2 = DateTime::createFromFormat("d-m-Y", $fechaInicio);
            $diferencia = $fechaObj1->diff($fechaObj2);
            $diasdif = $diferencia->days;

            $semestreMateria = DB::table('mallaCurricular')->select('semestre')
                ->where('codigoCurso', $codmateria)
                ->where('codprograma', $codprograma)
                ->first();

            /** Validación Notas */
            if ($nota1 != 0 && $nota2 != 0 && $nota3 != 0 && !in_array("Sin Actividad", [$nota1, $nota2, $nota3])) {
                if ($actividades != NULL) {
                    if ($actividades > 1) {
                        $definitiva = $notaAcum + 1.6;
                    } else {
                        $definitiva = $notaAcum + 0.8;
                    }
                } else {
                    $definitiva = $notaAcum;
                }
            } else {
                if ($nota1 == 0 && $nota2 == 0 && $nota3 == 0 || in_array("Sin Actividad", [$nota1, $nota2, $nota3])) {

                    if ($actividades != NULL) {
                        if ($actividades > 1) {
                            $definitiva =  $nota1 + 3.8;
                        } else {
                            $definitiva =  $nota1 + 2.4;
                        }
                    } else {
                        $definitiva = $notaAcum;
                    }
                } else {
                    if ($duracion == "8 SEMANAS") {
                        if ($nota1 != 0 && $nota2 != 0 && !in_array("Sin Actividad", [$nota1, $nota2])) {
                            if ($diasdif >= 56) {
                                if ($nota3 != "Sin Actividad") {
                                    $definitiva =  1.48 + $nota1 * 0.3 + $nota2 * 0.3;
                                } else {
                                    $definitiva =  $notaAcum;
                                }
                            } else {
                                if ($actividades != NULL) {
                                    $definitiva = $notaAcum * (10 / 6) + 0.6;
                                } else {
                                    $definitiva = $notaAcum * (10 / 6);
                                }
                            }
                        } else {
                            if ($nota1 != 0 && $nota1 != "Sin Actividad") {
                                if ($diasdif >= 42) {
                                    if ($nota2 != "Sin Actividad") {
                                        if ($actividades != NULL) {
                                            if ($actividades > 1) {
                                                $definitiva =  $nota1 + 3.8;
                                            } else {
                                                $definitiva =  $nota1 + 2.4;
                                            }
                                        } else {
                                            $definitiva =  $nota1;
                                        }
                                    } else {
                                        if ($actividades != NULL) {
                                            $definitiva =  ($nota1 * 0.3 + ($nota2 + 2.4)) * (10 / 6);
                                        } else {
                                            $definitiva =  $notaAcum * (10 / 6);
                                        }
                                    }
                                } else {

                                    if ($actividades != NULL) {
                                        if ($actividades > 1) {
                                            $definitiva =  $nota1 + 3.8;
                                        } else {
                                            $definitiva =  $nota1 + 2.4;
                                        }
                                    } else {
                                        $definitiva =  $nota1;
                                    }
                                }
                            } else {

                                if ($nota1 == "Sin Actividad" || $nota1 == 0) {
                                    if ($actividades != NULL) {
                                        if ($actividades > 1) {
                                            $definitiva =  $nota1 + 3.8;
                                        } else {
                                            $definitiva =  $nota1 + 2.4;
                                        }
                                    } else {
                                        $definitiva =  $notaAcum;
                                    }
                                }
                            }
                        }
                    } else {
                        if ($nota1 != 0 && $nota2 != 0 && !in_array("Sin Actividad", [$nota1, $nota2])) {
                            if ($diasdif >= 112) {
                                if ($nota3 != "Sin Actividad") {
                                    $definitiva =  1.48 + $nota1 * 0.3 + $nota2 * 0.3;
                                } else {
                                    $definitiva =  $notaAcum;
                                }
                            } else {
                                if ($actividades != NULL) {
                                    $definitiva = $notaAcum * (10 / 6) + 0.6;
                                } else {
                                    $definitiva = $notaAcum * (10 / 6);
                                }
                            }
                        } else {
                            if ($nota1 != 0 && $nota1 != "Sin Actividad") {
                                if ($diasdif >= 77) {
                                    if ($nota2 != "Sin Actividad") {
                                        if ($actividades != NULL) {
                                            if ($actividades > 1) {
                                                $definitiva =  $nota1 + 3.8;
                                            } else {
                                                $definitiva =  $nota1 + 2.4;
                                            }
                                        } else {
                                            $definitiva =  $nota1;
                                        }
                                    } else {
                                        if ($actividades != NULL) {
                                            $definitivas =  ($nota1 * 0.3 + ($nota2 + 2.4)) * (10 / 6);
                                        } else {
                                            if ($actividades != NULL) {
                                                $definitiva = $notaAcum * (10 / 6) + 0.6;
                                            } else {
                                                $definitiva = $notaAcum * (10 / 6);
                                            }
                                        }
                                    }
                                } else {
                                    if ($actividades != NULL) {
                                        if ($actividades > 1) {
                                            $definitiva =  $nota1 + 3.8;
                                        } else {
                                            $definitiva =  $nota1 + 2.4;
                                        }
                                    } else {
                                        $definitiva =  $nota1;
                                    }
                                }
                            } else {
                                if ($nota1 == "Sin Actividad" || $nota1 == 0) {
                                    if ($actividades != NULL) {
                                        if ($actividades > 1) {
                                            $definitiva =  $nota1 + 3.8;
                                        } else {
                                            $definitiva =  $nota1 + 2.4;
                                        }
                                    } else {
                                        $definitiva =  $notaAcum;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $consultaHistorial = DB::connection('sqlsrv')->table('MAFI_HIST_ACAD')
                ->where('id_curso', $codmateria)
                ->where('idbanner', $idBanner)
                ->where('cod_programa', $codprograma)
                ->first();

            if ($consultaHistorial != null) {
                $definitiva += 0.2;
            }

            $limiteRiesgoCritico = 1.2;
            $limiteRiesgoAlto = 2.4;
            $limiteRiesgoMedio = 3.5;

            if (isset($semestreMateria) && !empty($semestreMateria)) {
                $semestre = intval($semestreMateria->semestre);
                switch ($semestre) {
                    case 1:
                        $limiteRiesgoAlto = 3;
                        $limiteRiesgoMedio = 3.8;
                        break;
                    case 2:
                        $limiteRiesgoAlto = 2.9;
                        $limiteRiesgoMedio = 3.8;
                        break;
                    case 3:
                        $limiteRiesgoAlto = 2.8;
                        $limiteRiesgoMedio = 3.7;
                        break;
                    case 4:
                        $limiteRiesgoAlto = 2.7;
                        $limiteRiesgoMedio = 3.7;
                        break;
                    case 5:
                        $limiteRiesgoAlto = 2.6;
                        $limiteRiesgoMedio = 3.7;
                        break;
                }

                if ($definitiva > 5) {

                    switch ($semestre) {
                        case 1:
                            $definitiva = 4.5;
                            break;
                        case 2:
                            $definitiva = 4.5;
                            break;
                        case 3:
                            $definitiva = 4.6;
                            break;
                        case 4:
                            $definitiva = 4.6;
                            break;
                        case 5:
                            $definitiva = 4.7;
                            break;
                        case 6:
                            $definitiva = 4.7;
                            break;
                        default:
                            $definitiva = 4.8;
                            break;
                    }
                }
            }
            if ($definitiva < 0.5) {
                if ($duracion == "8 SEMANAS") {
                    if ($diasdif < 15) {
                        $definitiva = 'No hay datos suficientes';
                    }
                } elseif ($duracion == "16 SEMANAS") {
                    if ($diasdif < 35) {
                        $definitiva = 'No hay datos suficientes';
                    }
                }
            }

            $dos = substr($periodo, -2);

            switch ($dos) {
                case '41':
                case '42':
                case '43':
                case '44':
                case '45':
                case '51':
                case '52':
                case '53':
                case '54':
                case '55':

                    $limiteRiesgoCritico = 1.3;
                    $limiteRiesgoAlto = 2.7;
                    $limiteRiesgoMedio = 3.9;

                    break;
            }

            $fechaUltimoIngreso = $nota->Ult_AccesoACurso;

            if ($fechaUltimoIngreso == NULL) {
                $definitiva = ' ';
            }

            if ($nota->Estado_Banner == 'Inactivo') {
                $riesgo = 'Inactivo';
            } else {
                if ($definitiva == ' ') {
                    $riesgo = 'Sin ingreso a plataforma';
                } else {
                    if ($definitiva <= $limiteRiesgoCritico) {
                        $riesgo = "critico";
                    } else {
                        if ($definitiva <= $limiteRiesgoAlto) {
                            $riesgo = "alto";
                        } else {
                            if ($definitiva <= $limiteRiesgoMedio) {
                                $riesgo = "medio";
                            } else {
                                $riesgo = "bajo";
                            }
                        }
                    }
                }
            }

            $datos[] = [
                "Id_Banner" => $idBanner,
                "Programa" => $codprograma,
                "Nota1" => $nota1,
                "Nota2" => $nota2,
                "Nota3" => $nota3,
                "NotaAcum" => $notaAcum,
                "Proyeccion" => $definitiva,
                "Riesgo" => $riesgo
            ];
        }

        return $datos;
    }

    function selloMoodle(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');
        $riesgo = $request->input('riesgo');

        if (!isset($programas) && empty($programas)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $filtros = function ($query) use ($programas, $periodos, $request) {
            $query->whereIn('Cod_programa', $programas)
                ->whereIn('Periodo_Rev', $periodos)
                ->where('Nombrecorto', $request->input('tipo') == 'MOOCS' ? 'like' : 'not like', '%MOOC%')
                ->select('Sello', 'Autorizado_ASP');
        };

        if ($riesgo == 'ALTO') {
            $consultaSelloMoodle = ReporteAusentismo::wherenotNull('Ult_AccesoACurso')->where('Riesgo', $riesgo)->where($filtros)->get();
        } elseif ($riesgo == 'INGRESO') {
            $consultaSelloMoodle = ReporteAusentismo::where('Estado_Banner', 'Activo')->whereColumn('Ultacceso_Plataforma', '<', 'fechaInicio')
                ->where($filtros)->get();
        } else if ($riesgo == 'INACTIVOS') {
            $consultaSelloMoodle = ReporteAusentismo::where('Estado_Banner', 'Inactivo')->where($filtros)->get();
        } else {
            $consultaSelloMoodle = ReporteAusentismo::where('Riesgo', $riesgo)->where($filtros)->get();
        }

        $selloFinanciero = 0;
        $Retencion = 0;
        $ASP = 0;
        $Vacio = 0;

        foreach ($consultaSelloMoodle as $dato) {
            $sello = $dato->Sello;
            $estado = $dato->Autorizado_ASP;

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

        $data = [
            'CON SELLO' => $selloFinanciero,
            'TIENE RETENCION' => $Retencion,
            'ASP' => $ASP
        ];

        if ($Vacio != 0) {
            $data['NO EXISTE'] = $Vacio;
        }
        return $data;
    }

    function operadoresMoodle(Request $request)
    {
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');
        $riesgo = $request->input('riesgo');
        $facultades = $request->input('idfacultad');

        if (!isset($programas) && empty($programas)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $filtros = function ($query) use ($programas, $periodos, $request) {
            $query->whereIn('Cod_programa', $programas)
                ->whereIn('Periodo_Rev', $periodos)
                ->where('Nombrecorto', $request->input('tipo') == 'MOOCS' ? 'like' : 'not like', '%MOOC%');
        };

        if ($riesgo == 'ALTO') {
            $operadores = ReporteAusentismo::where('Riesgo', $riesgo)->wherenotNull('Ult_AccesoACurso')->where($filtros)
                ->select(DB::raw('COUNT(Operador) AS TOTAL, Operador'))
                ->groupBy('Operador')
                ->orderByDesc('TOTAL')
                ->limit(20)->get();
        } else if ($riesgo == 'INGRESO') {
            $operadores = ReporteAusentismo::where('Estado_Banner', 'Activo')->where($filtros)->whereColumn('Ultacceso_Plataforma', '<', 'fechaInicio')
                ->select(DB::raw('COUNT(Operador) AS TOTAL, Operador'))
                ->groupBy('Operador')
                ->orderByDesc('TOTAL')
                ->limit(20)->get();
        } else if ($riesgo == 'INACTIVOS') {
            $operadores = ReporteAusentismo::where('Estado_Banner', 'Inactivo')->where($filtros)
                ->select(DB::raw('COUNT(Operador) AS TOTAL, Operador'))
                ->groupBy('Operador')
                ->orderByDesc('TOTAL')
                ->limit(20)->get();
        } else {
            $operadores = ReporteAusentismo::where('Riesgo', $riesgo)->where($filtros)
                ->select(DB::raw('COUNT(Operador) AS TOTAL, Operador'))
                ->groupBy('Operador')
                ->orderByDesc('TOTAL')
                ->limit(20)->get();
        }


        return $operadores;
    }

    function tiposEstudiantesMoodle(Request $request)
    {
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');
        $riesgo = $request->input('riesgo');
        $facultades = $request->input('idfacultad');

        if (!isset($programas) && empty($programas)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $filtros = function ($query) use ($programas, $periodos, $request) {
            $query->whereIn('Cod_programa', $programas)
                ->whereIn('Periodo_Rev', $periodos)
                ->where('Nombrecorto', $request->input('tipo') == 'MOOCS' ? 'like' : 'not like', '%MOOC%');
        };

        if ($riesgo == 'ALTO') {
            $tiposDeEstudiantes = ReporteAusentismo::where('Riesgo', $riesgo)
                ->where($filtros)
                ->wherenotNull('Ult_AccesoACurso')
                ->select(DB::raw('COUNT(Tipo_Estudiante) AS TOTAL, Tipo_Estudiante'))
                ->groupBy('Tipo_Estudiante')
                ->orderByDesc('TOTAL')
                ->limit(20)
                ->get();
        } elseif ($riesgo == 'INGRESO') {
            $tiposDeEstudiantes = ReporteAusentismo::where('Estado_Banner', 'Activo')
                ->where($filtros)
                ->whereColumn('Ultacceso_Plataforma', '<', 'fechaInicio')
                ->select(DB::raw('COUNT(Tipo_Estudiante) AS TOTAL, Tipo_Estudiante'))
                ->groupBy('Tipo_Estudiante')
                ->orderByDesc('TOTAL')
                ->limit(20)
                ->get();
        } else if ($riesgo == 'INACTIVOS') {
            $tiposDeEstudiantes = ReporteAusentismo::where('Estado_Banner', 'Inactivo')
                ->where($filtros)
                ->select(DB::raw('COUNT(Tipo_Estudiante) AS TOTAL, Tipo_Estudiante'))
                ->groupBy('Tipo_Estudiante')
                ->orderByDesc('TOTAL')
                ->limit(20)
                ->get();
        } else {
            $tiposDeEstudiantes = ReporteAusentismo::where('Riesgo', $riesgo)
                ->where($filtros)
                ->select(DB::raw('COUNT(Tipo_Estudiante) AS TOTAL, Tipo_Estudiante'))
                ->groupBy('Tipo_Estudiante')
                ->orderByDesc('TOTAL')
                ->limit(20)
                ->get();
        }

        return $tiposDeEstudiantes;
    }

    function riesgoEstudiantes(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

        if (!isset($programas) && empty($programas)) {
            if (isset($facultades) && !empty($facultades)) {
                $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
            }
        }

        $riesgoEstudiantes = DB::table('estudiantes_moodle')
            ->select(DB::raw('COUNT(riesgo) AS TOTAL, riesgo'))
            ->whereIn('periodo', $periodos)->whereIn('cod_programa', $programas)->whereNotNull('riesgo')
            ->groupBy('riesgo')
            ->get();

        return $riesgoEstudiantes;
    }

    /**
     * tree el conteo de los estudiantes por los riesgos
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Support\Collection
     */
    function riesgoEstudiantescerrado(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

        if (!isset($programas) && empty($programas)) {
            if (isset($facultades) && !empty($facultades)) {
                $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
            }
        }

        $riesgoEstudiantes = DB::table('estudiantes_moodle_cerrados')
            ->select(DB::raw('COUNT(riesgo) AS TOTAL, riesgo'))
            ->whereIn('periodo', $periodos)->whereIn('cod_programa', $programas)
            ->groupBy('riesgo')
            ->get();

        return $riesgoEstudiantes;
    }

    function tablaEstudiantesRiesgo(Request $request, $riesgo)
    {
        $riesgo = trim($riesgo);
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

        if (!isset($programas) && empty($programas)) {
            if (isset($facultades) && !empty($facultades)) {
                $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
            }
        }

        $riesgoEstudiantes = DB::table('estudiantes_moodle')
            ->select('*')
            ->whereIn('periodo', $periodos)->whereIn('cod_programa', $programas)->whereNotNull('riesgo')
            ->where('riesgo', $riesgo)
            ->get();

        return $riesgoEstudiantes;
    }

    function tablaEstudiantescerrados(Request $request, $riesgo)
    {
        $riesgo = trim($riesgo);
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

        if (!isset($programas) && empty($programas)) {
            if (isset($facultades) && !empty($facultades)) {
                $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
            }
        }

        $riesgoEstudiantes = DB::table('estudiantes_moodle_cerrados')
            ->select('*')
            ->whereIn('periodo', $periodos)->whereIn('cod_programa', $programas)->whereNotNull('riesgo')
            ->where('riesgo', $riesgo)
            ->get();

        if ($riesgo == "todo") {
            $riesgoEstudiantes = DB::table('estudiantes_moodle_cerrados')
                ->select('*')
                ->whereIn('periodo', $periodos)->whereIn('cod_programa', $programas)
                ->get();
        }


        return $riesgoEstudiantes;
    }

    function selloMoodleEstudiantes(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');
        $riesgo = $request->input('riesgo');

        if (!isset($programas) && empty($programas)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        // $estudiantes = ->where('riesgo', $riesgo)->whereIn('cod_programa', $programas)->whereIn('periodo',$periodos)->pluck('id_banner')->toArray();

        $sello = DB::connection('mysql')->table('estudiantes_moodle')
            ->whereIn('cod_programa', $programas)
            ->whereIn('periodo', $periodos)
            ->wherenotNull('sello')
            ->where('riesgo', $riesgo)
            ->select('sello', 'autorizado')
            ->get();

        $selloFinanciero = 0;
        $Retencion = 0;
        $ASP = 0;
        $Vacio = 0;

        foreach ($sello as $dato) {
            $sello = $dato->sello;
            $estado = $dato->autorizado;

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

    function operadoresMoodleEstudiantes(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');
        $riesgo = $request->input('riesgo');

        if (!isset($programas) && empty($programas)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $operadores = DB::connection('mysql')->table('estudiantes_moodle')
            ->select(DB::raw('COUNT(Operador) AS TOTAL, Operador'))
            ->where('riesgo', $riesgo)
            ->whereIn('cod_programa', $programas)
            ->whereIn('periodo', $periodos)
            ->whereNotNull('Operador')
            ->groupBy('Operador')
            ->orderByDesc('TOTAL')
            ->limit(20)
            ->get();

        return $operadores;
    }

    function tiposEstudiantesMoodleEstudiantes(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');
        $riesgo = $request->input('riesgo');

        if (!isset($programas) && empty($programas)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $tiposDeEstudiantes = DB::connection('mysql')->table('estudiantes_moodle')
            ->select(DB::raw('COUNT(Tipo_Estudiante) AS TOTAL, Tipo_Estudiante'))
            ->where('riesgo', $riesgo)
            ->whereIn('cod_programa', $programas)
            ->whereIn('periodo', $periodos)
            ->whereNotNull('Tipo_Estudiante')
            ->groupBy('Tipo_Estudiante')
            ->orderByDesc('TOTAL')
            ->limit(20)
            ->get();


        return $tiposDeEstudiantes;
    }

    function llenartabla()
    {

        $estudiantes = DB::table('estudiantes_moodle')->select('id_banner')->get();

        foreach ($estudiantes as $estudiante) {
            $datos = DB::connection('mysql')->table('V_Reporte_Ausentismo_memory')->select('Autorizado_ASP', 'Sello', 'Operador', 'Tipo_Estudiante')->where('Id_Banner', $estudiante->id_banner)->first();

            $autorizado = $datos->Autorizado_ASP ?? NULL;
            $sello = $datos->Sello ?? NULL;
            $operador = $datos->Operador ?? NULL;
            $tipo = $datos->Tipo_Estudiante ?? NULL;

            if ($datos) {
                $update = DB::table('estudiantes_moodle')->where('id_banner', $estudiante->id_banner)
                    ->update([
                        'autorizado' => $autorizado,
                        'sello' => $sello,
                        'Operador' => $operador,
                        'Tipo_estudiante' => $tipo
                    ]);
            }
        }
    }

    function llenartablaprogramas()
    {
        $estudiantes = DB::table('estudiantes_moodle')
            ->select('cod_programa', 'id')
            ->whereNull('nombre_programa')
            ->get();

        foreach ($estudiantes as $estudiante) {
            $programa = DB::table('programas')->select('programa')->where('codprograma', $estudiante->cod_programa)->first();

            if ($programa) :
                $update = DB::table('estudiantes_moodle')->where('id', $estudiante->id)
                    ->update([
                        'nombre_programa' => $programa->programa
                    ]);
            endif;
        }
    }

    function descargarInformeRiesgoAcademico(Request $request)
    {
        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

        if (!isset($programas) && empty($programas)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $datos = DB::table('estudiantes_moodle')->select('*')
            ->whereIn('cod_programa', $programas)
            ->whereIn('periodo', $periodos)
            ->whereNotNull('sello')->get();

        return $datos;
    }

    function estudiantesRiesgoCerrado($riesgo)
    {

        $programas = $_POST['programa'];
        $periodos = $_POST['periodos'];

        if ($riesgo == 'SinActividad') {

            $estudiantes = DB::connection('mysql')
                ->table('cierrematriculas')
                ->select('*')
                ->whereIn('periodo', $periodos)
                ->whereIn('Programa', $programas)
                ->where('riesgo', 'Sin Actividad')
                ->get();
        } else if ($riesgo == 'critico') {

            $estudiantes = DB::connection('mysql')
                ->table('cierrematriculas')
                ->select('*')
                ->whereIn('periodo', $periodos)
                ->whereIn('Programa', $programas)
                ->where('riesgo', 'Perdida crítica')
                ->get();
        } else if ($riesgo == 'Perdida') {

            $estudiantes = DB::connection('mysql')
                ->table('cierrematriculas')
                ->select('*')
                ->whereIn('periodo', $periodos)
                ->whereIn('Programa', $programas)
                ->where('riesgo', 'Perdida')
                ->get();
        } else if ($riesgo == 'todo') {

            $estudiantes = DB::connection('mysql')
                ->table('cierrematriculas')
                ->select('*')
                ->whereIn('periodo', $periodos)
                ->whereIn('Programa', $programas)
                ->get();
        } else {

            $estudiantes = DB::connection('mysql')
                ->table('cierrematriculas')
                ->select('*')
                ->whereIn('periodo', $periodos)
                ->whereIn('Programa', $programas)
                ->where('riesgo', 'Aprobado')
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $estudiantes));
    }

    public function matriculas(Request $request)
    {

        $facultades = $request->input('idfacultad');
        $periodos = $request->input('periodos');
        $programas = $request->input('programa');

        if (isset($facultades) && !empty($facultades)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        $riesgos = DB::connection('mysql')->table('cierrematriculas')
            ->select('riesgo', 'ID_BANNER')
            ->whereIn('Programa', $programas)
            ->whereIn('periodo', $periodos)
            ->get();

        $Total = DB::connection('mysql')->table('cierrematriculas')
            ->selectRaw('COUNT(ID_BANNER) AS TOTAL')
            ->whereIn('Programa', $programas)
            ->whereIn('periodo', $periodos)
            ->get();

        $Perdidacritica = 0;
        $Perdida = 0;
        $Aprobado = 0;
        $sinActividad = 0;

        foreach ($riesgos as $key) {
            if ($key->riesgo == "Aprobado") {
                $Aprobado += 1;
            } else if ($key->riesgo == 'Perdida') {
                $Perdida += 1;
            } else if ($key->riesgo == 'Perdida crítica') {
                $Perdidacritica += 1;
            } else if ($key->riesgo == 'Sin Actividad') {
                $sinActividad += 1;
            }
        }

        $total = $Total[0]->TOTAL;

        $datos = array(
            'Aprobado' => $Aprobado,
            'Perdida' => $Perdida,
            'Perdidacritica' => $Perdidacritica,
            'sinActividad' => $sinActividad,
            'total' => $total
        );

        return $datos;
    }

    public function academico()
    {

        var_dump("entro");
    }
    public function cursos()
    {

        var_dump("entro");
    }

   public function duplicados(){

    return view('vistas.admin.duplicados');

   }


   public function getduplicados(){

    $facultades = DB::table('duplicados')->select('IdCurso','Id_Banner','Nombre','Apellido','Nombrecurso','Email')->get();
    /* Mostrar los datos en formato JSON*/
    header("Content-Type: application/json");
    /* Se pasa a formato JSON el arreglo de facultades */
    echo json_encode(array('data' => $facultades));

   }
}
