<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CambioPassRequest;
use App\Http\Requests\UsuarioLoginRequest;
use App\Http\Requests\CrearFacultadRequest;
use App\Models\Facultad;
use App\Models\Roles;
use App\Models\User;
use DateTime;
use App\Models\Usuario;
use App\Http\Util\Constantes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Models\ReporteAusentismo;


class VistasTransversalesController extends Controller
{

    public function programas()
    {
        $idsCursos = $_POST['cursos'];

        $programas = DB::table('mallaCurricular as m')
            ->join('programas as p', 'p.codprograma', '=', 'm.codprograma')
            ->whereIn('m.id', $idsCursos)->select('m.codprograma', 'p.programa')
            ->groupBy('m.codPrograma', 'p.programa')
            ->get();

        return ($programas);
    }

    function periodosActivosCursos($tabla)
    {
        $idsCursos = $_POST['idcurso'];
        $tabla = trim($tabla);

        $codigoCursos = DB::table('mallaCurricular')
            ->select('codigoCurso')
            ->whereIn('id', $idsCursos)
            ->distinct()
            ->pluck('codigoCurso')
            ->toArray();

        $periodosActivos = ReporteAusentismo::select('Periodo_Rev')
            ->where(function ($query) use ($codigoCursos) {
                foreach ($codigoCursos as $codigoCurso) {
                    $query->orWhere('Cod_materria', 'like', '%' . $codigoCurso . '%');
                }
            })
            ->distinct()
            ->pluck('Periodo_Rev')
            ->toArray();

        $periodos = [];

        $consultaFecha = $this->comprobacionFecha();

        if ($tabla == 'planeacion') {

            if ($consultaFecha == true) {
                $tablaConsulta = 'planeacion';
            } else {
                $tablaConsulta = 'programacion';
            }
            $periodosPlaneacion = DB::table($tablaConsulta)
                ->select('periodo')
                ->whereIn('periodo', $periodosActivos)
                ->distinct()
                ->pluck('periodo')
                ->toArray();

            foreach ($periodosPlaneacion as $periodoActivo) {
                $dosUltimosDigitos = substr($periodoActivo, -2);
                $periodos[] = $dosUltimosDigitos;
            }
        } elseif ($tabla == 'mafi') {
            $periodosMafi = DB::connection('sqlsrv')->table('MAFI')
                ->select('periodo')
                ->whereIn('periodo', $periodosActivos)
                ->distinct()
                ->pluck('periodo')
                ->toArray();

            foreach ($periodosMafi as $periodoActivo) {
                $dosUltimosDigitos = substr($periodoActivo, -2);
                $periodos[] = $dosUltimosDigitos;
            }
        } else {
            foreach ($periodosActivos as $periodoActivo) {
                $dosUltimosDigitos = substr($periodoActivo, -2);
                $periodos[] = $dosUltimosDigitos;
            }
        }

        $nivelFormacion = DB::table('programasPeriodos as pP')
            ->join('programas as p', 'pP.codPrograma', '=', 'p.codprograma')
            ->whereIn('pP.periodo', $periodos)
            ->select('p.nivelFormacion', 'pP.periodo')
            ->groupBy('p.nivelFormacion', 'pP.periodo')
            ->get();

        return $nivelFormacion;
    }

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

    public function riesgoCursos()
    {
        $nombresCursos = $_POST['idcurso'];
        $periodos = $_POST['periodos'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $filtros = function ($query) use ($codigoCursos, $periodos) {
            $query->whereIn('Periodo_Rev', $periodos)
                ->whereIn('Cod_materia', $codigoCursos)
                ->where('Nombrecorto', !empty($_POST['tipo']) ? 'like' : 'not like', '%MOOC%');
        };

        $riesgos = ReporteAusentismo::where($filtros)->select('Riesgo', 'Ultacceso_Plataforma', 'Estado_Banner', 'FechaInicio')->get();
        $Total = ReporteAusentismo::where($filtros)->selectRaw('COUNT(Id_Banner) AS TOTAL')->pluck('TOTAL')->first();

        $datos = $this->calcularRiesgos($riesgos);

        $datos['total'] = $Total;

        return $datos;
        //dd($datos);

        return $datos;
    }
    /**
     * Añadir caso de uso MOOCS
     */
    function estudiantesRiesgoCurso($riesgo)
    {
        $nombresCursos = $_POST['idcurso'];
        $periodos = $_POST['periodos'];
        $riesgo = trim($riesgo);

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $filtros = function ($query) use ($codigoCursos, $periodos) {
            $query->whereIn('Periodo_Rev', $periodos)
                ->select('*')
                ->where('Nombrecorto', !empty($_POST['tipo']) ? 'like' : 'not like', '%MOOC%')
                ->whereIn('Cod_materia', $codigoCursos)
                ->distinct();
        };

        if ($riesgo == 'ALTO') {
            $estudiantes = ReporteAusentismo::where('Riesgo', $riesgo)->where('Estado_Banner', 'Activo')->whereColumn('Ultacceso_Plataforma', '>', 'fechaInicio')->where($filtros)->get();
        } else if ($riesgo == 'INGRESO') {
            $estudiantes = ReporteAusentismo::whereColumn('Ultacceso_Plataforma', '<', 'fechaInicio')->where($filtros)->get();
        } else if ($riesgo == 'INACTIVOS') {
            $estudiantes = ReporteAusentismo::where('Estado_Banner', 'Inactivo')->where($filtros)->get();
        } else {
            $estudiantes = ReporteAusentismo::where('Riesgo', $riesgo)->where('Estado_Banner', 'Activo')->whereColumn('Ultacceso_Plataforma', '>', 'fechaInicio')->where($filtros)->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $estudiantes));
    }

    /**
     * Arreglar basado en la función anterior
     */

    function estudiantesRiesgoCursocerrado($riesgo)
    {
        dd($_POST);
        $nombresCursos = $_POST['idcurso'];
        $periodos = $_POST['periodos'];
        $riesgo = trim($riesgo);

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $filtros = function ($query) use ($codigoCursos, $periodos) {
            $query->whereIn('periodo', $periodos)
                ->whereIn('Codmateria', $codigoCursos)
                ->select('*');
        };

        if ($riesgo == 'SinActividad') {
            $estudiantes = DB::connection('mysql')
                ->table('cierrematriculas')
                ->where($filtros)
                ->where('riesgo', 'Sin Actividad')
                ->get();
        } else if ($riesgo == 'critico') {

            $estudiantes = DB::connection('mysql')
                ->table('cierrematriculas')
                ->where($filtros)
                ->where('riesgo', 'Perdida crítica')
                ->get();
        } else if ($riesgo == 'Perdida') {

            $estudiantes = DB::connection('mysql')
                ->table('cierrematriculas')
                ->where($filtros)
                ->where('riesgo', 'Perdida')
                ->get();
        } else if ($riesgo == 'todo') {

            $estudiantes = DB::connection('mysql')
                ->table('cierrematriculas')
                ->where($filtros)
                ->get();
        } else {

            $estudiantes = DB::connection('mysql')
                ->table('cierrematriculas')
                ->where($filtros)
                ->where('riesgo', 'Aprobado')
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $estudiantes));
    }

    function descargarTodoEstudiantesRiesgoCurso()
    {
        $nombresCursos = $_POST['idcurso'];
        $periodos = $_POST['periodos'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })

            ->pluck('codigoCurso')
            ->toArray();

        $estudiantes = ReporteAusentismo::whereIn('Periodo_Rev', $periodos)
            ->where('Nombrecorto', !empty($_POST['tipo']) ? 'like' : 'not like', '%MOOC%')
            ->whereIn('Cod_materia', $codigoCursos)
            ->select('*')
            ->distinct()
            ->get();

        return $estudiantes;
    }

    function descargarTodoEstudiantesRiesgoCursoFlash()
    {
        $nombresCursos = $_POST['idcurso'];
        $periodos = $_POST['periodos'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $estudiantes = DB::connection('sqlsrv')
            ->table('V_Reporte_Ausentismo')
            ->where('Nombrecorto', !empty($_POST['tipo']) ? 'like' : 'not like', '%MOOC%')
            ->whereIn('Periodo_Rev', $periodos)
            ->whereIn('Cod_materia', $codigoCursos)
            ->select('*')
            ->distinct()
            ->get();

        return $estudiantes;
    }

    function tablaCursos()
    {
        $nombresCursos = $_POST['idcurso'];
        $periodos = $_POST['periodos'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $consultaCursos = DB::table('registros_moodle')
            ->where('Nombrecorto', !empty($_POST['tipo']) ? 'like' : 'not like', '%MOOC%')
            ->whereIn('Codigo_materia', $codigoCursos)
            ->whereIn('Periodo', $periodos)
            ->get();

        return $consultaCursos;

        if (isset($datos)) {
            return $consultaCursos;
        } else {
            return null;
        }
    }

    function tablaCursosCerrados()
    {
        $nombresCursos = $_POST['idcurso'];
        $periodos = $_POST['periodos'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $consultaCursos = DB::table('cursos_moodle_cerrados')
            ->whereIn('Codigo_materia', $codigoCursos)
            ->whereIn('Periodo', $periodos)
            ->get();

        return $consultaCursos;

        if (isset($datos)) {
            return $consultaCursos;
        } else {
            return null;
        }
    }

    function dataAlumnoCurso(Request $request)
    {
        $idBanner = $request->input('idBanner');
        $nombresCursos = $_POST['idcurso'];
        $tipo = $request->input('tipo');

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $data = ReporteAusentismo::where('Id_Banner', $idBanner)
            ->whereIn('Cod_materia', $codigoCursos)
            ->where('Nombrecorto', $tipo == 'MOOC' ? 'like' : 'not like', '%MOOC%')
            ->select('*')
            ->get();


        header("Content-Type: application/json");
        echo json_encode(array('data' => $data));
    }

    function riesgoAsistenciaCurso(Request $request)
    {
        $idBanner = $request->input('idBanner');
        $nombresCursos = $_POST['idcurso'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $bajo = [];
        $medio = [];
        $alto = [];

        $riesgosAsistencia = DB::table('V_Reporte_Ausentismo_memory')
            ->select('Riesgo', 'Nombrecurso')
            ->where('Id_Banner', $idBanner)
            ->where(function ($query) use ($codigoCursos) {
                foreach ($codigoCursos as $codigoCurso) {
                    $query->orWhere('Cod_materia', 'like', '%' . $codigoCurso . '%');
                }
            })
            ->groupBy('Riesgo', 'Nombrecurso')
            ->get();


        $Notas = DB::table('V_Reporte_Ausentismo_memory')
            ->select('Nombrecurso', 'Nota_Acumulada', 'Nota_Primer_Corte', 'Nota_Segundo_Corte', 'Nota_Tercer_Corte', 'FechaInicio', 'Duracion_8_16_Semanas', 'Cod_materia', 'Cod_programa', 'Actividades_Por_Calificar', 'Id_Banner')
            ->where('Id_Banner', $idBanner)
            ->where(function ($query) use ($codigoCursos) {
                foreach ($codigoCursos as $codigoCurso) {
                    $query->orWhere('Cod_materia', 'like', '%' . $codigoCurso . '%');
                }
            })
            ->groupBy('nombreCurso', 'Nota_Acumulada', 'Nota_Primer_Corte', 'Nota_Segundo_Corte', 'Nota_Tercer_Corte', 'FechaInicio', 'Duracion_8_16_Semanas', 'Actividades_Por_Calificar', 'Cod_materia', 'Cod_programa', 'Id_Banner')
            ->get();

        $fechaActual = date("d-m-Y");
        $fechaObj1 = DateTime::createFromFormat("d-m-Y", $fechaActual);
        $definitivas = [];

        $Notas = $Notas->map(function ($nota) {
            $nota->nombreCurso = trim(substr($nota->Nombrecurso, 0, strpos($nota->Nombrecurso, '(')));
            return $nota;
        });

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
            $nombre = $nota->nombreCurso;
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
                if ($diasdif < 15) {
                    $definitivas[$nombre] .= ' - No hay datos suficientes';
                } else {
                    $definitivas[$nombre] .= ' - hay datos';
                }
            } else {
                if ($diasdif < 35) {
                    $definitivas[$nombre] .= ' - No hay datos suficientes';
                } else {
                    $definitivas[$nombre] .= ' - hay datos';
                }
            }
        }

        $datos = array(
            'riesgoAsistencia' => $riesgosAsistencia,
            'notas' => $definitivas,
        );

        header("Content-Type: application/json");
        echo json_encode(array('data' => $datos));
    }

    public function consultaProgramas($facultad)
    {
        if ($facultad == 8) {
            $campo = 'comunicativas';
        } elseif ($facultad == 9) {
            $campo = 'Ingles';
        }

        $programas = DB::table('mallaCurricular as m')
            ->join('programas as p', 'm.codprograma', '=', 'p.codprograma')
            ->select('m.codprograma')
            ->where('curso', 'like', '%' . $campo . '%')
            ->where('p.estado', '!=', '0')
            ->groupBy('m.codprograma')
            ->pluck('m.codprograma')
            ->toArray();

        return $programas;
    }

    public function estudiantesActivos()
    {
        $periodos = $_POST['periodos'];
        $programas = $_POST['programas'];

        $estudiantes = DB::connection('sqlsrv')->table('MAFI')
            ->whereIn('periodo', $periodos)
            ->whereIn('codprograma', $programas)
            ->select(DB::raw('COUNT(estado) AS TOTAL'), 'estado')
            ->groupBy('estado')
            ->get();

        header("Content-Type: application/json");
        echo json_encode(array('data' => $estudiantes));
    }

    public function comprobacionFecha()
    {
        $fechaActual = date("d-m-Y");

        $consultaFecha = DB::table('periodo')->where('activoCiclo1', 1)->select('fechaProgramacionPrimerCiclo', 'fechaInicioPeriodo')->first();

        $fechaInicial = $consultaFecha->fechaProgramacionPrimerCiclo;
        $fechaLimite = $consultaFecha->fechaInicioPeriodo;
        $fechaLimiteFormateada = date('Y-m-d', strtotime($fechaLimite . '-10 days'));

        $fechaActual = date("Y-m-d");

        if ($fechaInicial < $fechaActual && $fechaActual < $fechaLimiteFormateada) {
            return true;
        } else {
            return false;
        }
    }

    public function selloEstudiantesCursos()
    {
        $periodos = $_POST['periodos'];
        $nombresCursos = $_POST['idcurso'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {

            $idsBanner = DB::table('planeacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $consulta = DB::table('estudiantes')
                ->whereIn('marca_ingreso', $periodos)
                ->where('activo', 1)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
                ->select('homologante', 'sello', 'autorizado_asistir')
                ->get();
        } else {
            $idsBanner = DB::table('programacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $consulta = DB::table('estudiantes')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('programado_ciclo1', 'OK')
                        ->orWhere('programado_ciclo2', 'OK');
                })
                ->where('estado', 'Activo')
                ->whereIn('homologante', $idsBanner)
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

    public function retencionEstudiantesCursos()
    {
        $periodos = $_POST['periodos'];

        $nombresCursos = $_POST['idcurso'];
        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();


        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {

            $idsBanner = DB::table('planeacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $retencion = DB::table('estudiantes')
                ->whereIn('marca_ingreso', $periodos)
                ->where('activo', 1)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
                ->where('sello', 'TIENE RETENCION')
                ->selectRaw('COUNT(homologante) as TOTAL, autorizado_asistir')
                ->groupBy('autorizado_asistir')
                ->orderByDesc('TOTAL')
                ->get();
        } else {
            $idsBanner = DB::table('programacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $retencion = DB::table('estudiantes')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('programado_ciclo1', 'OK')
                        ->orWhere('programado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
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

    public function primerIngresoCursos()
    {
        $periodos = $_POST['periodos'];

        $tiposEstudiante = [
            'PRIMER INGRESO',
            'PRIMER INGRESO PSEUDO INGRES',
            'TRANSFERENTE EXTERNO',
            'TRANSFERENTE EXTERNO (ASISTEN)',
            'TRANSFERENTE EXTERNO PSEUD ING',
            'TRANSFERENTE INTERNO',
        ];

        $nombresCursos = $_POST['idcurso'];
        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
            $idsBanner = DB::table('planeacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $consulta = DB::table('estudiantes')
                ->whereIn('marca_ingreso', $periodos)
                ->where('activo', 1)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
                ->whereIn('tipo_estudiante', $tiposEstudiante)
                ->select('homologante', 'sello', 'autorizado_asistir')
                ->get();
        } else {
            $idsBanner = DB::table('programacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $consulta = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('programado_ciclo1', 'OK')
                        ->orWhere('programado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
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

    public function estudiantesAntiguosCursos()
    {
        $periodos = $_POST['periodos'];
        $nombresCursos = $_POST['idcurso'];

        $tiposEstudiante = [
            'PRIMER INGRESO',
            'PRIMER INGRESO PSEUDO INGRES',
            'TRANSFERENTE EXTERNO',
            'TRANSFERENTE EXTERNO (ASISTEN)',
            'TRANSFERENTE EXTERNO PSEUD ING',
            'TRANSFERENTE INTERNO',
        ];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
            $idsBanner = DB::table('planeacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $consulta = DB::table('estudiantes')
                ->whereIn('marca_ingreso', $periodos)
                ->where('activo', 1)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
                ->whereNotIn('tipo_estudiante', $tiposEstudiante)
                ->select('homologante', 'sello', 'autorizado_asistir')
                ->get();
        } else {
            $idsBanner = DB::table('programacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $consulta = DB::table('estudiantes')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('programado_ciclo1', 'OK')
                        ->orWhere('programado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
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

    public function tiposEstudiantesCursos()
    {
        $periodos = $_POST['periodos'];

        $nombresCursos = $_POST['idcurso'];
        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {

            $idsBanner = DB::table('planeacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $tipoEstudiantes = DB::table('estudiantes')
                ->whereIn('marca_ingreso', $periodos)
                ->where('activo', 1)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
                ->selectRaw('COUNT(homologante) as TOTAL, tipo_estudiante')
                ->groupBy('tipo_estudiante')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        } else {

            $idsBanner = DB::table('programacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $tipoEstudiantes = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('programado_ciclo1', 'OK')
                        ->orWhere('programado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
                ->selectRaw('COUNT(homologante) as TOTAL, tipo_estudiante')
                ->groupBy('tipo_estudiante')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }
        header("Content-Type: application/json");
        echo json_encode(array('data' => $tipoEstudiantes));
    }

    public function operadoresCursos()
    {
        $periodos = $_POST['periodos'];

        $nombresCursos = $_POST['idcurso'];
        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
            $idsBanner = DB::table('planeacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $operadores = DB::table('estudiantes')
                ->whereIn('marca_ingreso', $periodos)
                ->where('activo', 1)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
                ->selectRaw('COUNT(homologante) as TOTAL, operador')
                ->groupBy('operador')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        } else {

            $idsBanner = DB::table('programacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $operadores = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('programado_ciclo1', 'OK')
                        ->orWhere('programado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
                ->selectRaw('COUNT(homologante) as TOTAL, operador')
                ->groupBy('operador')
                ->orderByDesc('TOTAL')
                ->limit(5)
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $operadores));
    }

    public function estudiantesProgramasCursos()
    {
        $periodos = $_POST['periodos'];

        $nombresCursos = $_POST['idcurso'];
        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
            $idsBanner = DB::table('planeacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $consultaProgramas = DB::table('estudiantes')
                ->where('activo', 1)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
                ->selectRaw('COUNT(homologante) as TOTAL, programa')
                ->groupBy('programa')
                ->orderBy('TOTAL', 'DESC')
                ->limit(5)
                ->get();
        } else {
            $idsBanner = DB::table('programacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $consultaProgramas = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('programado_ciclo1', 'OK')
                        ->orWhere('programado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
                ->selectRaw('COUNT(homologante) as TOTAL, programa')
                ->groupBy('programa')
                ->orderBy('TOTAL', 'DESC')
                ->limit(5)
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $consultaProgramas));
    }

    public function tiposEstudiantesCursosTotal()
    {
        $periodos = $_POST['periodos'];

        $nombresCursos = $_POST['idcurso'];
        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {

            $idsBanner = DB::table('planeacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $tipoEstudiantes = DB::table('estudiantes')
                ->whereIn('marca_ingreso', $periodos)
                ->where('activo', 1)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
                ->selectRaw('COUNT(homologante) as TOTAL, tipo_estudiante')
                ->groupBy('tipo_estudiante')
                ->orderByDesc('TOTAL')
                ->get();
        } else {

            $idsBanner = DB::table('programacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $tipoEstudiantes = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('programado_ciclo1', 'OK')
                        ->orWhere('programado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
                ->selectRaw('COUNT(homologante) as TOTAL, tipo_estudiante')
                ->groupBy('tipo_estudiante')
                ->orderByDesc('TOTAL')
                ->get();
        }
        header("Content-Type: application/json");
        echo json_encode(array('data' => $tipoEstudiantes));
    }

    public function operadoresCursosTotal()
    {
        $periodos = $_POST['periodos'];

        $nombresCursos = $_POST['idcurso'];
        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
            $idsBanner = DB::table('planeacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $operadores = DB::table('estudiantes')
                ->whereIn('marca_ingreso', $periodos)
                ->where('activo', 1)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
                ->selectRaw('COUNT(homologante) as TOTAL, operador')
                ->groupBy('operador')
                ->orderByDesc('TOTAL')
                ->get();
        } else {

            $idsBanner = DB::table('programacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $operadores = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('programado_ciclo1', 'OK')
                        ->orWhere('programado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
                ->selectRaw('COUNT(homologante) as TOTAL, operador')
                ->groupBy('operador')
                ->orderByDesc('TOTAL')
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $operadores));
    }

    public function estudiantesProgramasCursosTotal()
    {
        $periodos = $_POST['periodos'];

        $nombresCursos = $_POST['idcurso'];
        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
            $idsBanner = DB::table('planeacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $consultaProgramas = DB::table('estudiantes')
                ->where('activo', 1)
                ->where(function ($query) {
                    $query->where('planeado_ciclo1', 'OK')
                        ->orWhere('planeado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
                ->selectRaw('COUNT(homologante) as TOTAL, programa')
                ->groupBy('programa')
                ->orderBy('TOTAL', 'DESC')
                ->get();
        } else {
            $idsBanner = DB::table('programacion')
                ->select('codBanner')
                ->whereIn('codMateria', $codigoCursos)
                ->distinct()
                ->pluck('codBanner')
                ->toArray();

            $consultaProgramas = DB::table('estudiantes')
                ->where('estado', 'Activo')
                ->whereIn('marca_ingreso', $periodos)
                ->where(function ($query) {
                    $query->where('programado_ciclo1', 'OK')
                        ->orWhere('programado_ciclo2', 'OK');
                })
                ->whereIn('homologante', $idsBanner)
                ->selectRaw('COUNT(homologante) as TOTAL, programa')
                ->groupBy('programa')
                ->orderBy('TOTAL', 'DESC')
                ->get();
        }

        header("Content-Type: application/json");
        echo json_encode(array('data' => $consultaProgramas));
    }

    public function tablaProgramasCursos()
    {
        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
            $tablaConsulta = 'planeacion';
            $var1 = 'planeado_ciclo1';
            $var2 = 'planeado_ciclo2';
        } else {
            $tablaConsulta = 'programacion';
            $var1 = 'programado_ciclo1';
            $var2 = 'programado_ciclo2';
        }

        $nombresCursos = $_POST['idcurso'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $consultaMalla = DB::table('mallaCurricular as m')
            ->join('programas as p', 'p.codprograma', '=', 'm.codprograma')
            ->select('curso', 'codigoCurso', 'm.codprograma', 'p.programa')
            ->whereIn('codigoCurso', $codigoCursos)
            ->groupBy('curso', 'codigoCurso', 'p.programa', 'm.codprograma')
            ->get();

        $datos = [];

        foreach ($consultaMalla as $dato) {
            $estudiantesSello = 0;
            $estudiantesRetencion = 0;
            $estudiantesASP = 0;
            $materiaTotal = 0;

            $nombremateria = $dato->curso;
            $codigocurso = $dato->codigoCurso;
            $codprograma = $dato->codprograma;

            $consultaSello = DB::table($tablaConsulta . ' as p')
                ->join('estudiantes as e', 'p.codBanner', '=', 'e.homologante')
                ->selectRaw('p.codMateria, e.sello, e.autorizado_asistir')
                ->where('p.codMateria', $codigocurso)
                ->where('p.codprograma', $codprograma)
                ->where('activo', 1)
                ->where(function ($query) use ($var1, $var2) {
                    $query->where($var1, 'OK')
                        ->orWhere($var2, 'OK');
                })
                ->get();

            $consultaPrograma = DB::table('programas')->select('programa', 'Facultad')->where('codprograma', $codprograma)->first();

            $nombrePrograma = ' ';
            $facultad = ' ';

            if ($consultaPrograma) {
                $nombrePrograma = $consultaPrograma->programa;
                $facultad = $consultaPrograma->Facultad;
            }

            foreach ($consultaSello as $sello) {
                $dato = $sello->sello;
                $materia = $sello->codMateria;
                $estado = $sello->autorizado_asistir;

                if ($dato == 'TIENE SELLO FINANCIERO') {
                    if (isset($estudiantesSello)) {
                        $estudiantesSello += 1;
                    } else {
                        $estudiantesSello = 1;
                    }
                } else if ($dato == 'TIENE RETENCION' && empty($estado) && stripos($estado, 'inactivo') !== false || empty($estado)) {
                    if (isset($estudiantesRetencion)) {
                        $estudiantesRetencion += 1;
                    } else {
                        $estudiantesRetencion = 1;
                    }
                } else if ($dato == 'TIENE RETENCION' && stripos($estado, 'activo') !== false) {
                    if (isset($estudiantesASP)) {
                        $estudiantesASP += 1;
                    } else {
                        $estudiantesASP = 1;
                    }
                }
                if (isset($materiaTotal)) {
                    $materiaTotal += 1;
                } else {
                    $materiaTotal = 1;
                }
            }

            $datos[] = [
                'Codmateria' => $codigocurso,
                'nombreMateria' => $nombremateria,
                'codprograma' => $codprograma,
                'nombreprograma' => $nombrePrograma,
                'facultad' => $facultad,
                'total' => $materiaTotal,
                'sello' => $estudiantesSello,
                'ASP' => $estudiantesASP,
                'retencion' => $estudiantesRetencion
            ];
        }
        return $datos;
    }

    public function mallaProgramaCurso()
    {
        $programa = $_POST['programa'];
        $idsCursos = $_POST['idcurso'];

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
            $tablaConsulta = 'planeacion';
        } else {
            $tablaConsulta = 'programacion';
        }


        $codigoCursos = DB::table('mallaCurricular')
            ->select('codigoCurso')
            ->where('codprograma', $programa)
            ->whereIn('id', $idsCursos)
            ->distinct()
            ->pluck('codigoCurso')
            ->toArray();

        $idsBanner = DB::table($tablaConsulta)
            ->select('codBanner')
            ->where('codprograma', $programa)
            ->whereIn('codMateria', $codigoCursos)
            ->distinct()
            ->pluck('codBanner')
            ->toArray();

        $consultaMalla = DB::table($tablaConsulta)
            ->selectRaw('COUNT(codMateria) as TOTAL, codMateria')
            ->whereIn('codBanner', $idsBanner)
            ->whereIn('codMateria', $codigoCursos)
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
            ->whereIn('p.codBanner', $idsBanner)
            ->whereIn('p.codMateria', $codigoCursos)
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
            if ($dato == 'TIENE RETENCION' && empty($estado) && stripos($estado, 'inactivo') !== false || empty($estado)) {
                if (isset($estudiantesRetencion[$materia])) {
                    $estudiantesRetencion[$materia] = $conteo + $estudiantesRetencion[$materia];
                } else {
                    $estudiantesRetencion[$materia] = $conteo;
                }
            }
            if ($dato == 'TIENE RETENCION' && stripos($estado, 'activo') !== false) {
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

    public function estudiantesMateriaCurso()
    {
        $programa = $_POST['programa'];
        $idsCursos = $_POST['idcurso'];

        $codigoCursos = DB::table('mallaCurricular')
            ->select('codigoCurso')
            ->whereIn('id', $idsCursos)
            ->where('codprograma', $programa)
            ->distinct()
            ->pluck('codigoCurso')
            ->toArray();


        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
            $tablaConsulta = 'planeacion';
        } else {
            $tablaConsulta = 'programacion';
        }


        $estudiantes = DB::table($tablaConsulta . ' as p')
            ->join('mallaCurricular as m', 'p.codMateria', '=', 'm.codigoCurso')
            ->where('p.codPrograma', $programa)
            ->whereIn('p.codMateria', $codigoCursos)
            ->select('p.codBanner', 'p.codMateria', 'm.curso', 'm.creditos')
            ->groupBy('p.codBanner', 'p.codMateria', 'm.curso', 'm.creditos')
            ->get();

        return $estudiantes;
    }

    public function datosEstudianteCurso()
    {
        $idBanner = $_POST['idBanner'];
        $idsCursos = $_POST['idcurso'];

        $codigoCursos = DB::table('mallaCurricular')
            ->select('codigoCurso')
            ->whereIn('id', $idsCursos)
            ->distinct()
            ->pluck('codigoCurso')
            ->toArray();

        $totalCreditosMoodle = 0;

        $consultaFecha = $this->comprobacionFecha();

        if ($consultaFecha == true) {
            $tablaConsulta = 'planeacion';
        } else {
            $tablaConsulta = 'programacion';
        }

        $infoEstudiante = DB::connection('sqlsrv')
            ->table('V_Reporte_Ausentismo')
            ->select('Nombre', 'Apellido', 'Id_Banner', 'Facultad', 'Programa', 'Codigo_Programa', 'No_Documento', 'Emailpersonal', 'Email', 'Sello', 'Estado_Banner', 'Tipo_Estudiante', 'Autorizado_ASP', 'Operador', 'Convenio')
            ->where('Id_Banner', $idBanner)
            ->first();

        if ($infoEstudiante != null) {

            $codigoPrograma = $infoEstudiante->Codigo_Programa;

            $datosMoodle = DB::connection('sqlsrv')
                ->table('V_Reporte_Ausentismo')
                ->select('codigomateria', 'grupo')
                ->where(function ($query) use ($codigoCursos) {
                    foreach ($codigoCursos as $codigoCurso) {
                        $query->orWhere('Grupo', 'like', '%' . $codigoCurso . '%');
                    }
                })
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
                ->whereIn('codMateria', $codigoCursos)
                ->where('m.codprograma', $codigoPrograma)
                ->groupBy('p.codMateria', 'm.curso', 'm.creditos', 'm.codigoCurso')
                ->get();

            $totalCreditosPlaneacion = $materiasPlaneadas->sum('creditos');

            $historialAcademico = DB::connection('sqlsrv')->table('mafi_hist_acad')
                ->where('idbanner', $idBanner)
                ->whereIn('id_curso', $codigoCursos)
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

    public function selloMoodleCurso()
    {
        $nombresCursos = $_POST['idcurso'];
        $periodos = $_POST['periodos'];
        $riesgo = $_POST['riesgo'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $filtros = function ($query) use ($codigoCursos, $periodos) {
            $query->whereIn('Periodo_Rev', $periodos)
                ->whereIn('Cod_materia', $codigoCursos)
                ->select('Sello', 'Autorizado_ASP');
        };

        if ($riesgo == 'ALTO') {
            $consultaSelloMoodle = ReporteAusentismo::whereColumn('Ultacceso_Plataforma', '>', 'fechaInicio')->where('Riesgo', $riesgo)->where($filtros)->get();
        } else if ($riesgo == 'INGRESO') {
            $consultaSelloMoodle = ReporteAusentismo::whereColumn('Ultacceso_Plataforma', '<', 'fechaInicio')->where($filtros)->where('Riesgo', 'ALTO')->get();
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

    public function operadoresMoodleCurso()
    {
        $nombresCursos = $_POST['idcurso'];
        $periodos = $_POST['periodos'];
        $riesgo = $_POST['riesgo'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })

            ->pluck('codigoCurso')
            ->toArray();

        $filtros = function ($query) use ($codigoCursos, $periodos) {
            $query->whereIn('Periodo_Rev', $periodos)
                ->whereIn('Cod_materia', $codigoCursos);
        };

        if ($riesgo == 'ALTO') {
            $operadores = ReporteAusentismo::select(DB::raw('COUNT(Operador) AS TOTAL, Operador'))->select(DB::raw('COUNT(Operador) AS TOTAL, Operador'))
                ->where('Riesgo', $riesgo)
                ->whereColumn('Ultacceso_Plataforma', '>', 'fechaInicio')
                ->where($filtros)
                ->groupBy('Operador')
                ->orderByDesc('TOTAL')
                ->limit(20)
                ->get();
        } else if ($riesgo == 'INGRESO') {
            $operadores = ReporteAusentismo::select(DB::raw('COUNT(Operador) AS TOTAL, Operador'))->select(DB::raw('COUNT(Operador) AS TOTAL, Operador'))
                ->where('Riesgo', 'ALTO')
                ->whereColumn('Ultacceso_Plataforma', '<', 'fechaInicio')
                ->where($filtros)
                ->groupBy('Operador')
                ->orderByDesc('TOTAL')
                ->limit(20)->get();
        } else if ($riesgo == 'INACTIVOS') {
            $operadores = ReporteAusentismo::select(DB::raw('COUNT(Operador) AS TOTAL, Operador'))->select(DB::raw('COUNT(Operador) AS TOTAL, Operador'))
                ->where('Estado_Banner', 'Inactivo')->where($filtros)->groupBy('Operador')->orderByDesc('TOTAL')->limit(20)->get();
        } else {
            $operadores = ReporteAusentismo::select(DB::raw('COUNT(Operador) AS TOTAL, Operador'))->select(DB::raw('COUNT(Operador) AS TOTAL, Operador'))
                ->where('Riesgo', $riesgo)->where($filtros)->groupBy('Operador')->orderByDesc('TOTAL')->limit(20)->get();
        }
        return $operadores;
    }

    public function tiposEstudiantesMoodleCurso()
    {
        $nombresCursos = $_POST['idcurso'];
        $periodos = $_POST['periodos'];
        $riesgo = $_POST['riesgo'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();


        $filtros = function ($query) use ($codigoCursos, $periodos) {
            $query->whereIn('Periodo_Rev', $periodos)
                ->whereIn('Cod_materia', $codigoCursos);
        };

        if ($riesgo == 'ALTO') {
            $tiposDeEstudiantes = ReporteAusentismo::select(DB::raw('COUNT(Tipo_Estudiante) AS TOTAL, Tipo_Estudiante'))
                ->where('Riesgo', $riesgo)
                ->whereColumn('Ultacceso_Plataforma', '>', 'fechaInicio')
                ->where($filtros)
                ->groupBy('Tipo_Estudiante')
                ->orderByDesc('TOTAL')
                ->limit(20)
                ->get();
        } elseif ($riesgo == 'INGRESO') {
            $tiposDeEstudiantes = ReporteAusentismo::select(DB::raw('COUNT(Tipo_Estudiante) AS TOTAL, Tipo_Estudiante'))
                ->where('Riesgo', 'ALTO')
                ->whereColumn('Ultacceso_Plataforma', '<', 'fechaInicio')
                ->where($filtros)
                ->groupBy('Tipo_Estudiante')
                ->orderByDesc('TOTAL')
                ->limit(20)
                ->get();
        } else if ($riesgo == 'INACTIVOS') {
            $tiposDeEstudiantes = ReporteAusentismo::select(DB::raw('COUNT(Tipo_Estudiante) AS TOTAL, Tipo_Estudiante'))
                ->where('Estado_Banner', 'Inactivo')
                ->where($filtros)
                ->groupBy('Tipo_Estudiante')
                ->orderByDesc('TOTAL')
                ->limit(20)
                ->get();
        } else {
            $tiposDeEstudiantes = ReporteAusentismo::select(DB::raw('COUNT(Tipo_Estudiante) AS TOTAL, Tipo_Estudiante'))
                ->where('Riesgo', $riesgo)
                ->where($filtros)
                ->groupBy('Tipo_Estudiante')
                ->orderByDesc('TOTAL')
                ->limit(20)
                ->get();
        }

        return $tiposDeEstudiantes;
    }

    /**
     * Abordar caso de uso MOOCS
     *
     */
    function riesgoEstudiantes()
    {
        $nombresCursos = $_POST['idcurso'];
        $periodos = $_POST['periodos'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();


        $idsBanner = DB::table('V_Reporte_Ausentismo_memory')->whereIn('Cod_materia', $codigoCursos)->distinct()->pluck('Id_Banner')->toArray();

        $riesgoEstudiantes = DB::table('estudiantes_moodle')
            ->select(DB::raw('COUNT(riesgo) AS TOTAL, riesgo'))
            ->whereIn('periodo', $periodos)->whereNotNull('riesgo')
            ->whereIn('id_banner', $idsBanner)
            ->groupBy('riesgo')
            ->get();

        return $riesgoEstudiantes;
    }

    function tablaEstudiantesRiesgo($riesgo)
    {
        $nombresCursos = $_POST['idcurso'];
        $periodos = $_POST['periodos'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $idsBanner = DB::table('V_Reporte_Ausentismo_memory')->whereIn('Cod_materia', $codigoCursos)->distinct()->pluck('Id_Banner')->toArray();

        $riesgoEstudiantes = DB::table('estudiantes_moodle')
            ->select('*')
            ->whereIn('id_banner', $idsBanner)
            ->whereIn('periodo', $periodos)
            ->where('riesgo', $riesgo)
            ->whereNotNull('riesgo')
            ->get();

        return $riesgoEstudiantes;
    }

    function tablaEstudiantesRiesgoCerrado($riesgo)
    {
        $nombresCursos = $_POST['idcurso'];
        $periodos = $_POST['periodos'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $idsBanner = DB::table('V_Reporte_Ausentismo_memory')->whereIn('Cod_materia', $codigoCursos)->distinct()->pluck('Id_Banner')->toArray();

        $riesgoEstudiantes = DB::table('estudiantes_moodle_cerrados')
            ->select('*')
            ->whereIn('periodo', $periodos)->whereIn('id_banner', $idsBanner)->whereNotNull('riesgo')
            ->where('riesgo', $riesgo)
            ->get();

        if ($riesgo == "todo") {
            $riesgoEstudiantes = DB::table('estudiantes_moodle_cerrados')
                ->select('*')
                ->whereIn('periodo', $periodos)->whereIn('id_banner', $idsBanner)
                ->get();
        }

        return $riesgoEstudiantes;
    }

    public function selloMoodleCursoEstudiantes()
    {
        $nombresCursos = $_POST['idcurso'];
        $periodos = $_POST['periodos'];
        $riesgo = $_POST['riesgo'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $idsBanner = ReporteAusentismo::whereIn('Cod_materia', $codigoCursos)->distinct()->pluck('Id_Banner')->toArray();

        $sello = DB::table('estudiantes_moodle')
            ->whereIn('Id_Banner', $idsBanner)
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

    public function operadoresMoodleCursoEstudiantes()
    {
        $nombresCursos = $_POST['idcurso'];
        $periodos = $_POST['periodos'];
        $riesgo = $_POST['riesgo'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $idsBanner = DB::table('V_Reporte_Ausentismo_memory')->whereIn('Cod_materia', $codigoCursos)->distinct()->pluck('Id_Banner')->toArray();

        $operadores = DB::table('estudiantes_moodle')->select(DB::raw('COUNT(Operador) AS TOTAL, Operador'))
            ->where('riesgo', $riesgo)
            ->whereIn('Id_Banner', $idsBanner)
            ->whereIn('periodo', $periodos)
            ->whereNotNull('Operador')
            ->groupBy('Operador')
            ->orderByDesc('TOTAL')
            ->limit(20)
            ->get();

        return $operadores;
    }

    public function tiposEstudianteMoodleCursoEstudiantes()
    {
        $nombresCursos = $_POST['idcurso'];
        $periodos = $_POST['periodos'];
        $riesgo = $_POST['riesgo'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $idsBanner = DB::table('V_Reporte_Ausentismo_memory')->whereIn('Cod_materia', $codigoCursos)->distinct()->pluck('Id_Banner')->toArray();

        $tiposDeEstudiantes = DB::table('estudiantes_moodle')
            ->select(DB::raw('COUNT(Tipo_Estudiante) AS TOTAL, Tipo_Estudiante'))
            ->where('riesgo', $riesgo)
            ->whereIn('periodo', $periodos)
            ->whereIn('Id_Banner', $idsBanner)
            ->whereNotNull('Tipo_Estudiante')
            ->groupBy('Tipo_Estudiante')
            ->orderByDesc('TOTAL')
            ->limit(20)
            ->get();

        return $tiposDeEstudiantes;
    }

    function descargarInformeRiesgoAcademico()
    {
        $nombresCursos = $_POST['idcurso'];
        $periodos = $_POST['periodos'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $idsBanner = DB::table('V_Reporte_Ausentismo_memory')->whereIn('Cod_materia', $codigoCursos)->distinct()->pluck('Id_Banner')->toArray();

        $datos = DB::table('estudiantes_moodle')
            ->select('*')
            ->whereIn('periodo', $periodos)
            ->whereIn('Id_Banner', $idsBanner)
            ->whereNotNull('sello')
            ->get();

        return $datos;
    }

    function matriculasCursos()
    {
        $periodos = $_POST['periodos'];
        $nombresCursos = $_POST['idcurso'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $riesgos = DB::connection('mysql')->table('cierrematriculas')
            ->select('riesgo', 'ID_BANNER')
            ->whereIn('CodMateria', $codigoCursos)
            ->whereIn('periodo', $periodos)
            ->get();

        $Total = DB::connection('mysql')->table('cierrematriculas')
            ->selectRaw('COUNT(ID_BANNER) AS TOTAL')
            ->whereIn('CodMateria', $codigoCursos)
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

    function riesgoEstudiantesCerradoCursos()
    {

        $periodos = $_POST['periodos'];
        $nombresCursos = $_POST['idcurso'];

        $codigoCursos = DB::table('mallaCurricular')->select('codigoCurso')
            ->where(function ($query) use ($nombresCursos) {
                foreach ($nombresCursos as $nombreCursos) {
                    $query->orWhere('curso', 'like', '%' . $nombreCursos . '%');
                }
            })
            ->pluck('codigoCurso')
            ->toArray();

        $idsBanner = DB::table('V_Reporte_Ausentismo_memory')->whereIn('Cod_materia', $codigoCursos)->distinct()->pluck('Id_Banner')->toArray();

        $riesgoEstudiantes = DB::table('estudiantes_moodle_cerrados')
            ->select(DB::raw('COUNT(riesgo) AS TOTAL, riesgo'))
            ->whereIn('Periodo', $periodos)
            ->whereNotNull('riesgo')
            ->whereIn('id_banner', $idsBanner)
            ->groupBy('riesgo')
            ->get();


        return $riesgoEstudiantes;
    }
}
