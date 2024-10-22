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

class RegistrosMoodleController extends Controller
{

    function crearRegistroMoodle()
    {
        $consultaCursos = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')
            ->select('Nombrecurso', 'IdCurso', 'NombreTutor', DB::raw('COUNT(id) AS TOTAL'))
            ->groupBy('IdCurso', 'Nombrecurso', 'NombreTutor')
            ->get()
            ->toArray();

        $fechaActual = date("d-m-Y");
        $fechaObj1 = DateTime::createFromFormat("d-m-Y", $fechaActual);

        foreach ($consultaCursos as $Curso) {

            $id = $Curso->IdCurso;
            $total = $Curso->TOTAL;

            $grupo = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')
                ->select('Grupo')
                ->where('IdCurso', $id)
                ->groupBy('Grupo')
                ->get();

            $codMaterias = [];
            $programas = [];
            $periodos = [];

            foreach ($grupo as $key) {
                $grupoExplode = explode('_', $key->Grupo);

                $codMateria = $grupoExplode[0];
                $programa = $grupoExplode[1];
                $periodo = $grupoExplode[2];
                // $operador = $grupoExplode[3];

                if (!in_array($codMateria, $codMaterias)) {
                    $codMaterias[] = $codMateria;
                }

                if (!in_array($programa, $programas)) {
                    $programas[] = $programa;
                }

                if (!in_array($periodo, $periodos)) {
                    $periodos[] = $periodo;
                }
            }

            $codMateriasString = "";
            $programasString = "";
            $periodosString = "";

            foreach ($programas as $dato) {
                $programasString .= $dato . ',';
            }
            foreach ($periodos as $dato) {
                $periodosString .= $dato . ',';
            }

            $codMateriasString = trim($codMateriasString, ",");
            $programasString = trim($programasString, ",");
            $periodosString = trim($periodosString, ",");
            // $operadoresString = trim($operadoresString, ",");

            $consultaSello = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')
                ->where('IdCurso', $id)
                ->where('Sello', 'TIENE SELLO FINANCIERO')
                ->select(DB::raw('COUNT(id) AS TOTAL'))
                ->get();

            $sello = $consultaSello[0]->TOTAL;

            $consultaASP = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')
                ->where('IdCurso', $id)
                ->where('Sello', 'TIENE RETENCION')
                ->select(DB::raw('COUNT(id) AS TOTAL'))
                ->get();

            $ASP = $consultaASP[0]->TOTAL;
            $inactivos = $total - $sello - $ASP;

            $consultaGrupos = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')->where('IdCurso', $id)->selectRaw('COUNT(Grupo) AS TOTAL')->groupBy('Grupo')->get();

            $grupo = $consultaGrupos->count();

            //Riesgo académico

            $Notas = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')
                ->where('IdCurso', $id)
                ->select('Id_Banner', 'Grupo', 'Nota_Acumulada', 'Nota_Primer_Corte', 'Nota_Segundo_Corte', 'Nota_Tercer_Corte', 'FechaInicio', 'Duracion_8_16_Semanas', 'Actividades_Por_Calificar')
                ->get();

            $alto = 0;
            $medio = 0;
            $bajo = 0;

            $fechaInicioCurso =  (new DateTime($Notas[0]->FechaInicio))->format("d-m-Y");
            $fechaInicioCursoFormateada = DateTime::createFromFormat("d-m-Y", $fechaInicioCurso);
            $diferencia = $fechaObj1->diff($fechaInicioCursoFormateada);
            $diasDiferencia = $diferencia->days;
            $estudiantesRepitiendo = 0;

            if ($diasDiferencia < 15) {

                $datoAlto = 'Sin datos por analizar';
                $datoMedio = 'Sin datos por analizar';
                $datoBajo = 'Sin datos por analizar';

                foreach ($Notas as $nota) {
                    $idBanner = $nota->Id_Banner;

                    $consultaHistorial = DB::connection('sqlsrv')->table('MAFI_HIST_ACAD')->select('id_curso')
                        ->where('idbanner', $idBanner)
                        ->where('id_curso', $codMaterias)
                        ->pluck('id_curso')
                        ->toArray();

                    if ($consultaHistorial) {
                        $estudiantesRepitiendo += 1;
                    }
                }
            } else {

                foreach ($Notas as $nota) {

                    $definitiva = 0;

                    $explodeGrupo = explode('_', $nota->Grupo);

                    $programaEstudiante = $explodeGrupo[1];

                    $actividades = $nota->Actividades_Por_Calificar;

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
                    $duracion = $nota->Duracion_8_16_Semanas;
                    $fechaObj2 = DateTime::createFromFormat("d-m-Y", $fechaInicio);
                    $diferencia = $fechaObj1->diff($fechaObj2);
                    $diasdif = $diferencia->days;

                    /** Validación Notas */
                    if ($nota1 != 0 && $nota2 != 0 && $nota3 != 0 && !in_array("Sin Actividad", [$nota1, $nota2, $nota3])) {
                        $definitiva = $notaAcum;
                    } else {
                        if ($nota1 == 0 && $nota2 == 0 && $nota3 == 0 || in_array("Sin Actividad", [$nota1, $nota2, $nota3])) {
                            $definitiva = $notaAcum;
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
                                        $definitiva = $notaAcum * (10 / 6);
                                    }
                                } else {
                                    if ($nota1 != 0 && $nota1 != "Sin Actividad") {
                                        if ($diasdif >= 42) {
                                            if ($nota2 != "Sin Actividad") {
                                                $definitiva =  $nota->Nota_Primer_Corte;
                                            } else {
                                                if ($actividades != NULL) {
                                                    $definitiva = ($nota1 * 0.3 + ($nota2 + 2)) * (10 / 6);
                                                } else {
                                                    $definitiva =  $notaAcum * (10 / 6);
                                                }
                                            }
                                        } else {
                                            $definitiva =  $nota1;
                                        }
                                    } else {
                                        if ($nota1 == "Sin Actividad") {
                                            $definitiva =  $notaAcum;
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
                                        $definitiva = $notaAcum * (10 / 6);
                                    }
                                } else {
                                    if ($nota1 != 0 && $nota1 != "Sin Actividad") {
                                        if ($diasdif >= 77) {
                                            if ($nota2 != "Sin Actividad") {
                                                $definitiva =  $nota->Nota_Primer_Corte;
                                            } else {
                                                if ($actividades != NULL) {
                                                    $definitiva = ($nota1 * 0.3 + ($nota2 + 2)) * (10 / 6);
                                                } else {
                                                    $definitiva =  $notaAcum * (10 / 6);
                                                }
                                            }
                                        } else {
                                            $definitiva =  $nota1;
                                        }
                                    } else {
                                        if ($nota1 == "Sin Actividad") {
                                            $definitiva =  $notaAcum;
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $consultaHistorial = DB::connection('sqlsrv')->table('MAFI_HIST_ACAD')->select('id_curso')
                        ->where('idbanner', $idBanner)
                        ->where('id_curso', $codMaterias)
                        ->pluck('id_curso')
                        ->toArray();

                    if ($consultaHistorial) {
                        $estudiantesRepitiendo += 1;
                        $definitiva += 0.2;
                    }

                    //  Verificar semestre de la materia

                    $semestreMateria = DB::table('mallaCurricular')->select('semestre')->where('codigoCurso', $codMaterias)
                        ->where('codprograma', $programaEstudiante)
                        ->get();

                    // Primer semestre -> alto 3.3 - medio 3.6
                    // Segundo semestre -> alto 3.2 - medio 3.6
                    // Tercer semestre -> alto 3.1 - medio 3.5
                    // Cuarto semestre -> alto 3 - medio 3.5
                    // Quinto semestre -> alto 2.9 - medio 3.5

                    $limiteRiesgoAlto = 2.7;
                    $limiteRiesgoMedio = 3.5;

                    if (isset($semestreMateria[0]->semestre)) {
                        switch ($semestreMateria[0]->semestre) {
                            case '1':
                                $limiteRiesgoAlto = 3.3;
                                $limiteRiesgoMedio = 3.6;
                                break;
                            case '2':
                                $limiteRiesgoAlto = 3.2;
                                $limiteRiesgoMedio = 3.6;
                                break;
                            case '3':
                                $limiteRiesgoAlto = 3.1;
                                $limiteRiesgoMedio = 3.5;
                                break;
                            case '4':
                                $limiteRiesgoAlto = 3;
                                $limiteRiesgoMedio = 3.5;
                                break;
                            case '5':
                                $limiteRiesgoAlto = 2.9;
                                $limiteRiesgoMedio = 3.5;
                                break;
                        }
                    }

                    if ($definitiva <= $limiteRiesgoAlto) {
                        $alto += 1;
                    } else {
                        if ($definitiva <= $limiteRiesgoMedio) {
                            $medio += 1;
                        } else {
                            $bajo += 1;
                        }
                    }
                }


                $porcentajeRiesgoAlto =  round((($alto / $total) * 100), 2);
                $porcentajeRiesgoMedio = round((($medio / $total) * 100), 2);
                $porcentajeRiesgoBajo = round((($bajo / $total) * 100), 2);

                $datoAlto = $alto . '<br>' . $porcentajeRiesgoAlto . '%';
                $datoMedio = $medio . '<br>' . $porcentajeRiesgoMedio . '%';
                $datoBajo = $bajo . '<br>' . $porcentajeRiesgoBajo . '%';
            }


            foreach ($codMaterias as $dato) {
                $codMateriasString .= $dato . ',';
            }

            $codMateriasString = trim($codMateriasString, ",");

            $crear = DB::table('registros_moodle')->insert([
                'Id_Curso' => $id,
                'Nombre_curso' => $Curso[0]->Nombrecurso,
                'Nombre_tutor' => $Curso[0]->NombreTutor,
                'Codigo_materia' => $codMateriasString,
                'Programa' => $programasString,
                'Periodo' => $periodosString,
                'Total_estudiantes' => $total,
                'Sello' => $sello,
                'Asp' => $ASP,
                'Inactivos' => $inactivos,
                'Cursos' => $grupo,
                'Riesgo_alto' => $datoAlto,
                'Riesgo_medio' => $datoMedio,
                'Riesgo_bajo' => $datoBajo,
                'Repitentes' => $estudiantesRepitiendo,
                'fecha' => now(),
            ]);

            echo 'registro creado <br>';
        }
    }

    function verificacionRiesgoAcademico()
    {
        $consultaDatosCursosMoodle = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')
            ->select('IdCurso')
            ->groupBy('IdCurso')
            ->get()
            ->toArray();

        $fechaConsulta = date("d-m-Y H:i:s");
        $fechaConsultaFormateada = new DateTime($fechaConsulta);
        $fechaConsultaFormateada->modify('-16 hours');
        $fechaConsultaFormateada = $fechaConsultaFormateada->format('d-m-Y H:i:s');

        $consultaDatosCursosSistema = DB::table('registros_moodle')
            ->select('Id_Curso')
            ->where('fecha', '>', $fechaConsultaFormateada)
            ->pluck('Id_Curso')
            ->toArray();

        $fechaActual = date("d-m-Y H:i:s");

        foreach ($consultaDatosCursosMoodle as $dato) {
            if (in_array($dato->IdCurso, $consultaDatosCursosSistema)) {

                $fecha = DB::table('registros_moodle')->select('fecha')->where('Id_Curso', $dato->IdCurso)->first();

                $fechaFormateada = $fecha->fecha;

                $fechaFormateada = new DateTime($fechaFormateada);

                $fechaFormateada->modify('+16 hours');

                $fechaFormateada = $fechaFormateada->format('d-m-Y H:i:s');

                if ($fechaActual > $fechaFormateada) {
                    echo $dato->IdCurso . 'entra update <br>';
                    $actualizar = $this->crearRegistro($dato->IdCurso, 'update');
                } else {
                    echo 'No se actualiza porque no han pasado las 16 horas <br>';
                }
            } else {
                echo $dato->IdCurso . 'entra crear <br>';
                $crear = $this->crearRegistro($dato->IdCurso, 'insert');
            }
        }
    }

    //function crearRegistro()
    function crearRegistro($idCurso, $accion)
    {
        $id = $idCurso;

        $Curso = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')
            ->select('Nombrecurso', 'IdCurso', 'NombreTutor', DB::raw('COUNT(id) AS TOTAL'))
            ->groupBy('Nombrecurso', 'IdCurso', 'NombreTutor')
            ->where('IdCurso', $id)
            ->get();

        $fechaActual = date("d-m-Y");
        $fechaObj1 = DateTime::createFromFormat("d-m-Y", $fechaActual);
        $total = $Curso[0]->TOTAL;

        $consultaGrupo = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')
            ->select('Grupo')
            ->where('IdCurso', $id)
            ->groupBy('Grupo')
            ->get();

        $grupo = $consultaGrupo->count();

        $codMaterias = [];
        $programas = [];
        $periodos = [];

        foreach ($consultaGrupo as $key) {
            $grupoExplode = explode('_', $key->Grupo);

            $codMateria = $grupoExplode[0];
            $programa = $grupoExplode[1];
            $periodo = $grupoExplode[2];

            if (!in_array($codMateria, $codMaterias)) {
                $codMaterias[] = $codMateria;
            }

            if (!in_array($programa, $programas)) {
                $programas[] = $programa;
            }

            if (!in_array($periodo, $periodos)) {
                $periodos[] = $periodo;
            }
        }

        $codMateriasString = "";
        $programasString = "";
        $periodosString = "";

        foreach ($codMaterias as $dato) {
            $codMateriasString .= $dato . ',';
        }
        foreach ($programas as $dato) {
            $programasString .= $dato . ',';
        }
        foreach ($periodos as $dato) {
            $periodosString .= $dato . ',';
        }

        $codMateriasString = trim($codMateriasString, ",");
        $programasString = trim($programasString, ",");
        $periodosString = trim($periodosString, ",");

        $consultaSello = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')
            ->where('IdCurso', $id)
            ->where('Sello', 'TIENE SELLO FINANCIERO')
            ->select(DB::raw('COUNT(id) AS TOTAL'))
            ->get();

        $sello = $consultaSello[0]->TOTAL;

        $consultaASP = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')
            ->where('IdCurso', $id)
            ->where('Sello', 'TIENE RETENCION')
            ->select(DB::raw('COUNT(id) AS TOTAL'))
            ->get();

        $ASP = $consultaASP[0]->TOTAL;
        $inactivos = $total - $sello - $ASP;

        //Riesgo académico

        $Notas = DB::connection('sqlsrv')->table('V_Reporte_Ausentismo')
            ->where('IdCurso', $id)
            ->select('Id_Banner', 'Grupo', 'Nota_Acumulada', 'Nota_Primer_Corte', 'Nota_Segundo_Corte', 'Nota_Tercer_Corte', 'FechaInicio', 'Duracion_8_16_Semanas')
            ->get();

        $alto = 0;
        $medio = 0;
        $bajo = 0;

        $fechaInicioCurso =  (new DateTime($Notas[0]->FechaInicio))->format("d-m-Y");
        $fechaInicioCursoFormateada = DateTime::createFromFormat("d-m-Y", $fechaInicioCurso);
        $diferencia = $fechaObj1->diff($fechaInicioCursoFormateada);
        $diasDiferencia = $diferencia->days;
        $estudiantesRepitiendo = 0;

        if ($diasDiferencia < 15) {

            $datoAlto = 'Sin datos por analizar';
            $datoMedio = 'Sin datos por analizar';
            $datoBajo = 'Sin datos por analizar';

            foreach ($Notas as $nota) {
                $idBanner = $nota->Id_Banner;

                $consultaHistorial = DB::connection('sqlsrv')->table('MAFI_HIST_ACAD')->select('id_curso')
                    ->where('idbanner', $idBanner)
                    ->where('id_curso', $codMaterias)
                    ->pluck('id_curso')
                    ->toArray();

                if ($consultaHistorial) {
                    $estudiantesRepitiendo += 1;
                }
            }
        } else {

            foreach ($Notas as $nota) {

                $definitiva = 0;

                $explodeGrupo = explode('_', $nota->Grupo);

                $programaEstudiante = $explodeGrupo[1];

                $actividades = $nota->Actividades_Por_Calificar;

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
                $duracion = $nota->Duracion_8_16_Semanas;
                $fechaObj2 = DateTime::createFromFormat("d-m-Y", $fechaInicio);
                $diferencia = $fechaObj1->diff($fechaObj2);
                $diasdif = $diferencia->days;

                /** Validación Notas */
                if ($nota1 != 0 && $nota2 != 0 && $nota3 != 0 && !in_array("Sin Actividad", [$nota1, $nota2, $nota3])) {
                    $definitiva = $notaAcum;
                } else {
                    if ($nota1 == 0 && $nota2 == 0 && $nota3 == 0 || in_array("Sin Actividad", [$nota1, $nota2, $nota3])) {
                        $definitiva = $notaAcum;
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
                                    $definitiva = $notaAcum * (10 / 6);
                                }
                            } else {
                                if ($nota1 != 0 && $nota1 != "Sin Actividad") {
                                    if ($diasdif >= 42) {
                                        if ($nota2 != "Sin Actividad") {
                                            $definitiva =  $nota->Nota_Primer_Corte;
                                        } else {
                                            if ($actividades != NULL) {
                                                $definitiva = ($nota1 * 0.3 + ($nota2 + 2)) * (10 / 6);
                                            } else {
                                                $definitiva =  $notaAcum * (10 / 6);
                                            }
                                        }
                                    } else {
                                        $definitiva =  $nota1;
                                    }
                                } else {
                                    if ($nota1 == "Sin Actividad") {
                                        $definitiva =  $notaAcum;
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
                                    $definitiva = $notaAcum * (10 / 6);
                                }
                            } else {
                                if ($nota1 != 0 && $nota1 != "Sin Actividad") {
                                    if ($diasdif >= 77) {
                                        if ($nota2 != "Sin Actividad") {
                                            $definitiva =  $nota->Nota_Primer_Corte;
                                        } else {
                                            if ($actividades != NULL) {
                                                $definitiva = ($nota1 * 0.3 + ($nota2 + 2)) * (10 / 6);
                                            } else {
                                                $definitiva =  $notaAcum * (10 / 6);
                                            }
                                        }
                                    } else {
                                        $definitiva =  $nota1;
                                    }
                                } else {
                                    if ($nota1 == "Sin Actividad") {
                                        $definitiva =  $notaAcum;
                                    }
                                }
                            }
                        }
                    }
                }

                $consultaHistorial = DB::connection('sqlsrv')->table('MAFI_HIST_ACAD')->select('id_curso')
                    ->where('idbanner', $idBanner)
                    ->where('id_curso', $codMaterias)
                    ->pluck('id_curso')
                    ->toArray();

                if ($consultaHistorial) {
                    $estudiantesRepitiendo += 1;
                    $definitiva += 0.2;
                }

                //  Verificar semestre de la materia

                $semestreMateria = DB::table('mallaCurricular')->select('semestre')->where('codigoCurso', $codMaterias)
                    ->where('codprograma', $programaEstudiante)
                    ->get();

                // Primer semestre -> alto 3.3 - medio 3.6
                // Segundo semestre -> alto 3.2 - medio 3.6
                // Tercer semestre -> alto 3.1 - medio 3.5
                // Cuarto semestre -> alto 3 - medio 3.5
                // Quinto semestre -> alto 2.9 - medio 3.5

                $limiteRiesgoAlto = 2.7;
                $limiteRiesgoMedio = 3.5;

                if (isset($semestreMateria[0]->semestre)) {
                    switch ($semestreMateria[0]->semestre) {
                        case '1':
                            $limiteRiesgoAlto = 3.3;
                            $limiteRiesgoMedio = 3.6;
                            break;
                        case '2':
                            $limiteRiesgoAlto = 3.2;
                            $limiteRiesgoMedio = 3.6;
                            break;
                        case '3':
                            $limiteRiesgoAlto = 3.1;
                            $limiteRiesgoMedio = 3.5;
                            break;
                        case '4':
                            $limiteRiesgoAlto = 3;
                            $limiteRiesgoMedio = 3.5;
                            break;
                        case '5':
                            $limiteRiesgoAlto = 2.9;
                            $limiteRiesgoMedio = 3.5;
                            break;
                    }
                }

                if ($definitiva <= $limiteRiesgoAlto) {
                    $alto += 1;
                } else {
                    if ($definitiva <= $limiteRiesgoMedio) {
                        $medio += 1;
                    } else {
                        $bajo += 1;
                    }
                }
            }


            $porcentajeRiesgoAlto =  round((($alto / $total) * 100), 2);
            $porcentajeRiesgoMedio = round((($medio / $total) * 100), 2);
            $porcentajeRiesgoBajo = round((($bajo / $total) * 100), 2);

            $datoAlto = $alto . '<br>' . $porcentajeRiesgoAlto . '%';
            $datoMedio = $medio . '<br>' . $porcentajeRiesgoMedio . '%';
            $datoBajo = $bajo . '<br>' . $porcentajeRiesgoBajo . '%';
        }

        foreach ($codMaterias as $dato) {
            $codMateriasString .= $dato . ',';
        }

        $codMateriasString = trim($codMateriasString, ",");

        if ($accion == 'update') {

            $consulta = DB::connection('mysql')->table('registros_moodle')->select('*')->where('Id_Curso', $id)->get();

            $crear = DB::connection('mysql')->table('registros_moodle')->where('Id_Curso', $id)->update([
                'Id_Curso' => $id,
                'Nombre_curso' => $Curso[0]->Nombrecurso,
                'Nombre_tutor' => $Curso[0]->NombreTutor,
                'Codigo_materia' => $codMateriasString,
                'Programa' => $programasString,
                'Periodo' => $periodosString,
                'Total_estudiantes' => $total,
                'Sello' => $sello,
                'Asp' => $ASP,
                'Inactivos' => $inactivos,
                'Cursos' => $grupo,
                'Riesgo_alto' => $datoAlto,
                'Riesgo_medio' => $datoMedio,
                'Riesgo_bajo' => $datoBajo,
                'Repitentes' => $estudiantesRepitiendo,
                'fecha' => now(),
            ]);


            // $datos =[
            //     'Id_Curso' => $id,
            //     'Nombre_curso' => $Curso[0]->Nombrecurso,
            //     'Nombre_tutor' => $Curso[0]->NombreTutor,
            //     'Codigo_materia' => $codMateriasString,
            //     'Programa' => $programasString,
            //     'Periodo' => $periodosString,
            //     'Total_estudiantes' => $total,
            //     'Sello' => $sello,
            //     'Asp' => $ASP,
            //     'Inactivos' => $inactivos,
            //     'Cursos' => $grupo,
            //     'Riesgo_alto' => $datoAlto,
            //     'Riesgo_medio' => $datoMedio,
            //     'Riesgo_bajo' => $datoBajo,
            //     'Repitentes' => $estudiantesRepitiendo,
            //     'fecha' => now(),
            // ];

            // dd($datos);

            $respuesta = 'registro actualizado';
        }

        if ($accion == 'insert') {
            $crear = DB::table('registros_moodle')->insert([
                'Id_Curso' => $id,
                'Nombre_curso' => $Curso[0]->Nombrecurso,
                'Nombre_tutor' => $Curso[0]->NombreTutor,
                'Codigo_materia' => $codMateriasString,
                'Programa' => $programasString,
                'Periodo' => $periodosString,
                'Total_estudiantes' => $total,
                'Sello' => $sello,
                'Asp' => $ASP,
                'Inactivos' => $inactivos,
                'Cursos' => $grupo,
                'Riesgo_alto' => $datoAlto,
                'Riesgo_medio' => $datoMedio,
                'Riesgo_bajo' => $datoBajo,
                'Repitentes' => $estudiantesRepitiendo,
                'fecha' => now(),
            ]);
            $respuesta = 'registro creado';
        }

        return $respuesta;
    }
}
