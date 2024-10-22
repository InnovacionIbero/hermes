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
use App\Models\Docente;
use Exception;


class PlaneacionDocentesController extends Controller
{

    public static $filtrosProgramas = null;

    /**
     * Filtros para programas -> Se usan en varias consultas de todo el controlador
     */
    public function __construct()
    {
        self::$filtrosProgramas = function ($query) {
            return $query->whereIn('estado', [1, 2])
                ->select('codprograma', 'programa');
        };
    }

    /**
     * Consulta la malla curricular de un array de programas
     *
     * @param [] $programas
     * @return consultaMalla
     */
    function consultarMalla($programas)
    {
        $user = auth()->user();
        $idRol = $user->id_rol;

        $consultarMalla = DB::table('mallaCurricular')
            ->whereIn('codprograma', $programas)
            ->when($idRol != 9, function ($query) {
                $query->where(function ($query) {
                    $query->whereNull('id_facultad_transversal')
                        ->orWhere('id_facultad_transversal', 0);
                });
            })
            ->select('codigoCurso', 'codprograma', 'curso', 'id_facultad_transversal')
            ->get()
            ->groupBy('codprograma');

        return $consultarMalla;
    }


    public function mallaTransversal($id = NULL)
    {
        if (empty($id)) {
            $user = auth()->user();
            $id = $user->id_facultad;
        }

        $consultarMalla = DB::table('mallaCurricular')
            ->join('facultad', 'mallaCurricular.id_facultad_transversal', '=', 'facultad.id')
            ->where('mallaCurricular.id_facultad_transversal', $id)
            ->select('mallaCurricular.codigoCurso', 'mallaCurricular.curso', 'mallaCurricular.id_facultad_transversal', 'facultad.codFacultad')
            ->groupBy('mallaCurricular.codigoCurso', 'mallaCurricular.curso', 'mallaCurricular.id_facultad_transversal', 'facultad.codFacultad')
            ->get()
            ->toArray();

        return $consultarMalla;
    }

    function consultarTablaDocentes($parametros, $disponibilidad)
    {
        $tabla = DB::table('docentes_disponibles')->where('nombre', '!=', '');

        $tabla->where(function ($query) use ($parametros) {
            foreach ($parametros as $parametro) {
                $query->orWhere('codigos_programa_materia', 'LIKE', '%' . $parametro . '%');
            }
        });

        if ($disponibilidad == 'activos') {
            $tabla->where('disponibilidad', 1);
        } else {
            $tabla->where('disponibilidad', 0);
        }

        return $tabla->get();
    }

    function filtrosDocente($docente)
    {
        $filtrosDocente = function ($query) use ($docente) {
            $query->where('nombre', $docente[0])->where('id_banner', $docente[2]);

            if (!is_null($docente[1])) {
                $query->where('email', $docente[1]);
            }

            return $query;
        };

        return $filtrosDocente;
    }

    /**
     * Valida si en el request llegan programas.
     *
     * @param [Request] $request
     * @return [] programas
     */
    function validarProgramas($request)
    {
        $facultades = $request->facultades;
        $programas = $request->programas;

        if (empty($programas)) {
            $programas = DB::table('programas')->whereIn('Facultad', $facultades)->pluck('codprograma')->toArray();
        }

        foreach ($facultades as $facultad) {
            if (isset($facultad['transversal']) && $facultad['transversal'] == 1) {
                $programas[] = $facultad['codFacultad'];
            }
        }

        return $programas;
    }

    /**
     * Consulta la malla curricular de un array de cursos 
     *
     * @param [String] $cursosSeleccionados
     * @return void
     */
    function obtenerCodigosCursos($cursosSeleccionados)
    {
        $dataCursos = DB::table('mallaCurricular')
            ->whereIn('curso', $cursosSeleccionados)
            ->distinct()
            ->select('codigoCurso')
            ->pluck('codigoCurso')
            ->groupBy('codigoCurso')
            ->toArray();

        return $dataCursos;
    }

    /**
     * Verifica cuántas asignaturas tiene un docente asginadas en preferentes o estandar
     *
     * @param String $campo (preferentes o estandar)
     * @param [String] $docente datos del docente
     * @return int
     */
    function verificarTotalAsignaturas($campo, $docente)
    {

        $filtrosDocente = function ($query) use ($docente) {
            return $query->where('nombre', $docente[0])->where('email', $docente[1])->where('id_banner', $docente[2]);
        };

        $stringAsignaturas = DB::table('docentes_disponibles')->where($filtrosDocente)->select($campo)->get();

        if (!empty($stringAsignaturas[0]->$campo)) {
            $arrayAsignaturas = explode(",", $stringAsignaturas);

            $total = count($arrayAsignaturas);

            return $total;
        } else {
            return 0;
        }
    }

    /**
     * Esta función retorna el array de las asignaturas del docente con los nombres de curso correspondientes.
     *
     * @param [String] Programas o cursos según corresponda
     * @param [Eloquent] Consulta de la malla curricular 
     * @param [String] Arreglo de las asignaturas y programas asignadas del docente
     * @param [String] Arreglo de asignaturas preferentes del docente
     * @param boolean Verifica si es transversal
     * @return [[String], [String]] Arrays de asignaturas asignadas del docente y el nombre de estas
     */
    function obtenerArrayProgramasNombreCurso($parametroFiltros, $consultarMalla, $codigoProgramaMateriaArray, $arrayMateriasPreferentes, $istransversal = false)
    {
        $arrayCodigosMateria = [];

        $arrayFiltered = array_map(function ($item) use ($parametroFiltros, $consultarMalla, &$arrayCodigosMateria, $arrayMateriasPreferentes, $istransversal) {

            /** Verificar si la asignatura se encuentra entre las preferentes del docente */
            $isChecked = false;
            if (in_array($item, $arrayMateriasPreferentes)) {
                $isChecked = true;
            }

            $codPrograma = trim(explode(" - ", $item)[0]);
            /** Limpiar el codigo de programa en caso de que sea de plan 2 */
            $codProgramaClean = trim(explode(" - ", $item)[0], '-2');

            $codigoMateria = trim(explode(" - ", $item)[1]);

            if (!$istransversal) {
                $arrayCodigosMateria[] = $codigoMateria;
            }

            $cursoEncontrado = $consultarMalla->flatten()->first(function ($curso) use ($codigoMateria) {
                return $curso->codigoCurso == $codigoMateria;
            });

            if ($istransversal) {
                if (in_array($codigoMateria, $parametroFiltros) && $cursoEncontrado != NULL) {

                    if (!in_array($cursoEncontrado->curso, $arrayCodigosMateria)) {
                        $arrayCodigosMateria[] = $cursoEncontrado->curso;
                    }
                    return [
                        'codMateria' => $codigoMateria,
                        'codFacultad' => $codPrograma,
                        'curso' => $cursoEncontrado,
                        'isChecked' => $isChecked,
                    ];
                }
            }

            if (in_array($codProgramaClean, $parametroFiltros) && $cursoEncontrado != NULL) {
                return [
                    'codPrograma' => $codPrograma,
                    'codMateria' => $codigoMateria,
                    'curso' => $cursoEncontrado,
                    'isChecked' => $isChecked,
                ];
            } else {
                return null;
            }
        }, $codigoProgramaMateriaArray);

        $arrayFiltered = array_filter($arrayFiltered);

        return [
            $arrayFiltered,
            $arrayCodigosMateria
        ];
    }

    /**
     * Carga la vista de planeación docentes con los datos del usuario.
     */
    public function planeacionDocentesView()
    {
        $menu = session('menu');

        if (is_null($menu)):
            return redirect()->route('login.index');
        endif;
        $user = auth()->user();
        $idFacultad = $user->id_facultad;

        $rol_db = DB::table('roles')->where([['id', '=', $user->id_rol]])->get();
        $isTransversal = 0;
        if (!empty($idFacultad)) {
            $facultad = DB::table('facultad')->where([['id', '=', $idFacultad]])->select('transversal')->get();
            $isTransversal = $facultad[0]->transversal;
        }

        $nombre_rol = $rol_db[0]->nombreRol;
        auth()->user()->nombre_rol = $nombre_rol;

        $nombre_rol = ($nombre_rol === 'Admin') ? strtolower($nombre_rol) : $nombre_rol;

        return view('vistas.planeacion-docentes.admin-planeacion-docentes')->with('idRol', $user->id_rol)->with('isTransversal', $isTransversal);
    }

    /**
     * Carga la data para la tabla "docentes-disponibles".
     *
     * @param Request Datos del usuario
     * @return [Array]
     */
    public function tablaDocentesDisponibles(Request $request)
    {
        $programas = $this->validarProgramas($request);

        $data = [];

        $disponibilidad = $request->disponibilidad;
        $tabla = $this->consultarTablaDocentes($programas, $disponibilidad);

        $consultarFacultadesTransversales = DB::table('facultad')->where('transversal', 1)->get();

        $arrayTransversal = [];

        /** Obtener ids de las facultades transversales */
        foreach ($consultarFacultadesTransversales as $transversales) {
            $arrayTransversal[$transversales->codFacultad] = $transversales->id;
        }

        $programasSelect = DB::table('programas')->select('programa', 'codprograma')->whereIn('codprograma', $programas)->get();

        /** Consultar la malla de los programas seleccionados */
        $consultarMalla = $this->consultarMalla($programas);

        foreach ($tabla as $key) {

            $codigoProgramaMateriaArray = explode(',', $key->codigos_programa_materia);

            /** Generar array de las asignaturas preferentes del docente */
            $materiasPrefentes = $key->codigos_materias_preferencia;
            if (!empty($materiasPrefentes)) {
                $arrayMateriasPreferentes = explode(",", $materiasPrefentes);
            } else {
                $arrayMateriasPreferentes = [];
            }
            /** Obtener array de programas - materias que solo correspondan a los filtros. */
            $arrays = $this->obtenerArrayProgramasNombreCurso($programas, $consultarMalla, $codigoProgramaMateriaArray, $arrayMateriasPreferentes);
            /** Obtener array de los programas que tiene el docente y coincide con los filtros seleccionados */
            $programasRegistrados = [];
            foreach ($arrays[0] as $array) {
                $programasRegistrados[] = $array['codPrograma'];
            }
            // Eliminar valores repetidos
            $programasRegistrados = array_unique($programasRegistrados);
            // Reindexar array
            $programasRegistrados = array_values($programasRegistrados);

            /** Asignar las mallas que le corresponden al docente */
            /** En caso de que pertenezca a una facultad transversal, añadirá los cursos correspondientes a esa facultad */
            $malla = [];
            $programasConsultados = [];

            foreach ($programasRegistrados as $programa) {

                if ((!in_Array($programa, $programasConsultados))) {
                    $programasConsultados[] = $programa;
                    if (isset($consultarMalla[$programa])) {
                        $malla = array_merge($malla, $consultarMalla[$programa]->toArray());
                    } else {
                        /** Si no encuentra una mallaCurricular para el programa, verifica si es una facultad transversal para añadirle dichas asignaturas. */
                        if (array_key_exists($programa, $arrayTransversal)) {
                            $idFacultadTransversal = $arrayTransversal[$programa];

                            $malla = array_merge($malla, $this->mallaTransversal($idFacultadTransversal));
                        }
                    }
                }
            }

            if (count($arrays[0]) > 0) {
                $data[] = [
                    'nombre' => $key->nombre,
                    'email' => $key->email,
                    'id_banner' => $key->id_banner,
                    'codigos_programa_materia' => $arrays[0],
                    'programas_docente' => $programasRegistrados,
                    'programasSelect' => $programasSelect,
                    'codigos_materia' => $arrays[1],
                    'malla' => $malla,
                    'cupo' => $key->cupo_disponible,
                    'cupo_16_semanas' => $key->cupo_16_semanas,
                    'disponibilidad' => $key->disponibilidad,
                ];
            }
        }
        return $data;
    }

    /**
     * Carga la data para la tabla "docentes-disponibles" pero de las facultades transvesales
     *
     * @param Request Datos del usuario
     * @return Array Retorna un arreglo de docentes con información filtrada.
     * El array retornado tiene la siguiente estructura:
     * [
     *     'nombre' => string,             // Nombre del docente
     *     'email' => string,              // Correo electrónico del docente
     *     'id_banner' => string,          // ID de Banner del docente
     *     'codFacultad' => string,        // Código de la facultad del usuario autenticado
     *     'codigos_programa_materia' => array[], // Array de cursos que el docente puede dictar
     *     'cursos_docente' => array[],    // Cursos ya asignados al docente
     *     'cursos_disponibles' => array[], // Cursos disponibles para el docente, con estructura:
     *                                      // [
     *                                      //     'codigoCurso' => string, // Código del curso
     *                                      //     'curso' => string        // Nombre del curso
     *                                      // ]
     *     'cupo' => int,                  // Cupo disponible del docente
     *     'cupo_16_semanas' => int,       // Cupo disponible en cursos de 16 semanas
     *     'disponibilidad' => string      // Disponibilidad del docente (e.g., "disponible", "no disponible")
     * ]
     */
    public function tablaDocentesTransversalesDisponibles(Request $request)
    {
        $cursosSeleccionados = $request->cursos;

        $user = auth()->user();

        $consultarCodFacultad = DB::table('facultad')->select('codFacultad')->where('id', $user->id_facultad)->get();

        $data = [];

        $disponibilidad = $request->disponibilidad;

        $tabla = $this->consultarTablaDocentes($cursosSeleccionados, $disponibilidad);

        $consultarMalla = DB::table('mallaCurricular')->whereIn('codigoCurso', $cursosSeleccionados)->select('curso', 'codigoCurso')->get();
        /** Remover aquellas asignaturas que no corresponden a materias de transversales */
        foreach ($tabla as $key) {

            $codigoProgramaMateriaArray = explode(',', $key->codigos_programa_materia);

            /** Generar array de las asignaturas preferentes del docente */
            $materiasPrefentes = $key->codigos_materias_preferencia;
            if (!empty($materiasPrefentes)) {
                $arrayMateriasPreferentes = explode(",", $materiasPrefentes);
            } else {
                $arrayMateriasPreferentes = [];
            }

            /** Obtener array de programas - materias que solo correspondan a los filtros. */
            $arrays = $this->obtenerArrayProgramasNombreCurso($cursosSeleccionados, $consultarMalla, $codigoProgramaMateriaArray, $arrayMateriasPreferentes, true);

            /** Obtener array de los cursos que tiene el docente y coincide con los filtros seleccionados */
            $cursosRegistrados = [];
            foreach ($arrays[0] as $array) {
                $cursosRegistrados[] = $array['curso']->curso;
            }

            $cursosDisponibles = array_diff($cursosSeleccionados, $arrays[1]);

            $arrayCursosDisponibles = [];

            foreach ($cursosDisponibles as $cursoDisponible) {
                $cursoEncontrado = $consultarMalla->firstWhere('codigoCurso', $cursoDisponible);
                $arrayCursosDisponibles[] = [
                    'codigoCurso' => $cursoDisponible,
                    'curso' => $cursoEncontrado->curso,
                ];
            }

            if (count($arrays[0]) > 0) {
                $data[] = [
                    'nombre' => $key->nombre,
                    'email' => $key->email,
                    'id_banner' => $key->id_banner,
                    'codFacultad' => $consultarCodFacultad[0]->codFacultad,
                    'codigos_programa_materia' => $arrays[0],
                    'cursos_docente' => $arrays[1],
                    'cursos_disponibles' => $arrayCursosDisponibles,
                    'cupo' => $key->cupo_disponible,
                    'cupo_16_semanas' => $key->cupo_16_semanas,
                    'disponibilidad' => $key->disponibilidad,
                ];
            }
        }
        return $data;
    }

    /**
     * Se encarga de retornar los datos para los filtros que le corresponden al usuario.
     *
     * @return Array Con la data del usuario para sus filtros.
     */
    public function filtrosProgramaFacultad()
    {
        $facultadesUser = auth()->user()->id_facultad;
        $programasUser = auth()->user()->programa;

        /* Programas asignados del usuario */
        if (!empty($programasUser)) {
            $programasArray = explode(';', $programasUser);

            $programas = DB::table('programas')
                ->whereIn('id', $programasArray)
                ->where(self::$filtrosProgramas)
                ->orderBy('programa', 'asc')
                ->get();

            return $data = [
                'programas' => $programas,
            ];
        }
        /* Facultad del usuario */ else if (!empty($facultadesUser)) {
            $facultad = DB::table('facultad')->where('id', $facultadesUser)->select('id', 'nombre', 'transversal')->get();

            /* Verificar si es transversal */
            if ($facultad[0]->transversal == 0) {
                $programas = DB::table('programas')
                    ->where('Facultad', $facultad[0]->nombre)
                    ->where(self::$filtrosProgramas)
                    ->orderBy('programa', 'asc')
                    ->get();

                return $data = [
                    'programas' => $programas,
                ];
            } else {

                $cursos = DB::table('mallaCurricular')
                    ->where('id_facultad_transversal', $facultadesUser)
                    ->select('curso', 'codigoCurso')
                    ->orderBy('curso')
                    ->groupBy('codigoCurso', 'curso')
                    ->get();

                return $data = [
                    'cursos' => $cursos,
                ];
            }
        }
        /* Retornar todo */ else {
            $todosProgramas = DB::table('programas')->where(self::$filtrosProgramas)->orderBy('programa', 'asc')->get();
            $todasFacultades = DB::table('facultad')->get();

            return $data = [
                'programas' => $todosProgramas,
                'facultades' => $todasFacultades,
            ];
        }
    }

    /**
     * Consulta los cursos de la facultad transversal de un usuario
     *
     * @return Eloquent Consulta de cursos.
     */
    public function cursosFacultad()
    {
        $facultadUser = auth()->user()->id_facultad;

        $consultarMalla = DB::table('mallaCurricular')
            ->where('id_facultad_transversal', $facultadUser)
            ->select('codigoCurso')
            ->get()
            ->groupBy('codigoCurso');

        return $consultarMalla;
    }

    /**
     * Devuelve los filtros correspondientes a partir de la data que recibe
     *
     * @param Request 
     * @return array Arreglo de los filtros
     */
    public function filtrosChangeFacultad(Request $request)
    {
        $facultades = $request->facultades;
        $array = [];
        foreach ($facultades as $facultad) {
            $array[] = $facultad['nombre'];
        }

        $programas = DB::table('programas')
            ->whereIn('Facultad', $array)
            ->where(self::$filtrosProgramas)
            ->get();

        return $data = [
            'programas' => $programas,
        ];
    }

    /**
     * Agregar nueva asignatura al docente, como máximo 15 asignaturas
     *
     * @param Request 
     * @return void
     */
    public function updateAsignaturasDocente(Request $request)
    {

        $docente = $request->arrayData;
        $codigoNuevo = $request->newData;

        $filtrosDocente = $this->filtrosDocente($docente);

        /** Validar si no se ha excedido el límite de 10 asignaturas */
        $total = $this->verificarTotalAsignaturas('codigos_programa_materia', $docente) + 1;

        if ($total > 15):
            return 'Cupo lleno';
        endif;
        /**Agregar nueva asignatura al docente */
        $updateAsignaturas = DB::table('docentes_disponibles')->where($filtrosDocente)
            ->update([
                'codigos_programa_materia' => DB::raw("CONCAT(codigos_programa_materia, ',{$codigoNuevo}')")
            ]);

        $codigoCurso = explode(" - ", $codigoNuevo)[1];
        $codPrograma = explode(" - ", $codigoNuevo)[0];

        $consultarCurso = DB::table('mallaCurricular')->select('curso')->where('codigoCurso', $codigoCurso)->pluck('curso');

        if (!empty($consultarCurso[0])) {
            return [
                'codPrograma' => $codPrograma,
                'codMateria' => $codigoCurso,
                'curso' => $consultarCurso[0],
                'isChecked' => false,
            ];
        } else {
            return $codigoNuevo;
        }
    }

    /**
     * Agregar nueva asignatura a las preferencias del docente, como máximo se tienen 3
     *
     * @param Request $request
     * @return void
     */
    public function updatePreferenciaDocente(Request $request)
    {

        $docente = $request->arrayData;
        $materiaPreferente = $request->newData;
        $isChecked = $request->isChecked;

        $filtrosDocente = $this->filtrosDocente($docente);

        /** Obtener materias preferentes del docente */
        $materiasPreferentesDocente = DB::table('docentes_disponibles')->where($filtrosDocente)
            ->select('codigos_materias_preferencia')->get();

        if (!empty($materiasPreferentesDocente[0]->codigos_materias_preferencia)) {
            /** Obtener array de las materias preferentes del docente */
            $arrayMateriasPreferentes = explode(",", $materiasPreferentesDocente[0]->codigos_materias_preferencia);

            if ($isChecked !== 'false') {

                $total = $this->verificarTotalAsignaturas('codigos_materias_preferencia', $docente) + 1;

                if ($total > 3):
                    return 'Cupo lleno';
                endif;

                DB::table('docentes_disponibles')->where($filtrosDocente)
                    ->update([
                        'codigos_materias_preferencia' => DB::raw("CONCAT(codigos_materias_preferencia, ',{$materiaPreferente}')")
                    ]);
            } else {
                $stringMaterias = '';
                $nuevoArray = array_diff($arrayMateriasPreferentes, [$materiaPreferente]);

                foreach ($nuevoArray as $materia) {
                    $stringMaterias .= $materia . ',';
                }

                $stringMaterias = trim($stringMaterias, ',');

                /** Se actualizan las materias preferentes del docente removiendo la asignatura des-ckeckeada */
                DB::table('docentes_disponibles')->where($filtrosDocente)->update(['codigos_materias_preferencia' => $stringMaterias]);
            }
        } else {
            /** Si están vacías las materias preferentes del docente, simplemente la asigna la que ha sido checkeada */
            DB::table('docentes_disponibles')->where($filtrosDocente)->update(['codigos_materias_preferencia' => $materiaPreferente]);
        }

        return true;
    }

    /**
     * Actualizar el cupo disponible del docente
     *
     * @param Request 
     * @return void
     */
    public function updateCupoDisponibleDocente(Request $request)
    {
        $docente = $request->arrayData;
        $nuevoCupo = $request->newData;
        $filtrosDocente = $this->filtrosDocente($docente);

        $updateCupo = DB::table('docentes_disponibles')->where($filtrosDocente)
            ->update([
                'cupo_disponible' => $nuevoCupo
            ]);

        return $updateCupo;
    }

    /**
     * Inhabilitar o habilitar docente
     *
     * @param Request 
     * @return void
     */
    public function inhabilitarDocente(Request $request)
    {
        $docente = $request->arrayData;

        $disponibilidad = $request->newData == 'Activar' ? 1 : 0;

        $filtrosDocente = $this->filtrosDocente($docente);

        $updateCupo = DB::table('docentes_disponibles')->where($filtrosDocente)->update([
            'disponibilidad' => $disponibilidad
        ]);

        return $updateCupo;
    }

    /**
     * Remover asignatura entre las asignadas del docente
     *
     * @param Request $request
     * @return void
     */
    public function removerAsignaturaDocente(Request $request)
    {
        $docente = $request->arrayData;
        $removerMateria = $request->newData;

        $filtrosDocente = $this->filtrosDocente($docente);

        $verificarAsignaturas = DB::table('docentes_disponibles')->where($filtrosDocente)
            ->select('codigos_programa_materia', 'codigos_materias_preferencia')->get();

        $asignaturas = explode(",", $verificarAsignaturas[0]->codigos_programa_materia);
        $preferencias = explode(",", $verificarAsignaturas[0]->codigos_materias_preferencia);

        /** Remover de las asignaturas */
        $asignaturas = array_diff($asignaturas, [$removerMateria]);
        $preferencias = array_diff($preferencias, [$removerMateria]);

        if (count($asignaturas) > 0) {

            $stringAsignaturas = '';
            $stringPreferencias = '';

            foreach ($asignaturas as $asignatura) {
                $stringAsignaturas .= $asignatura . ',';
            }

            foreach ($preferencias as $preferencia) {
                $stringPreferencias .= $preferencia . ',';
            }

            $stringAsignaturas = trim($stringAsignaturas, ',');
            $stringPreferencias = trim($stringPreferencias, ',');

            $updateDocente = DB::table('docentes_disponibles')->where($filtrosDocente)
                ->update([
                    'codigos_programa_materia' => $stringAsignaturas,
                    'codigos_materias_preferencia' => $stringPreferencias
                ]);

            return 'success';
        } else {
            return 'cupo vacio';
        }
    }

    /**
     * Consultar la malla curricular de uno o varios programas
     *
     * @param Request $request
     * @return void
     */
    public function traerMallaPrograma(Request $request)
    {
        $programa[] = $request->programa;

        $consultarMalla = $this->consultarMalla($programa);
        return $consultarMalla;
    }

    /**
     * Crear docente nuevo
     *
     * @param Request $request
     * @return void
     */
    public function crearDocente(Request $request)
    {
        try {
            // Intentamos validar la solicitud
            $validatedData = $request->validate([
                'email' => 'required|email|unique:docentes_disponibles,email',
                'asignaturas' => 'required|array',
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        $asignaturasDocente = '';
        $asignaturasPreferentes = '';

        $asignaturas = $validatedData['asignaturas'];

        /** Crear arrays de las materias asignadas y las preferentes del docente */
        foreach ($asignaturas as $asignatura) {
            $materia = $asignatura['materia'];
            $asignaturasDocente .= $materia . ',';
            if ($asignatura['isChecked'] == 'true') {
                $asignaturasPreferentes .= $materia . ',';
            }
        }

        $asignaturasDocente = trim($asignaturasDocente, ',');
        $asignaturasPreferentes = trim($asignaturasPreferentes, ',');

        $crearDocente = Docente::create([
            'nombre' => $request->nombre,
            'email' => $validatedData['email'],
            'cupo_disponible' => $request->cupo,
            'id_banner' => $request->idBanner,
            'codigos_programa_materia' => $asignaturasDocente,
            'codigos_materias_preferencia' => $asignaturasPreferentes,
            'disponibilidad' => 1,
        ]);

        if ($crearDocente) {
            return 'success';
        } else {
            return 'creacion errada';
        }
    }

    /**
     * Tabla de planeación docentes
     *
     * @param Request 
     * @return void
     */
    public function tablaPlaneacionDocentes(Request $request)
    {
        $filtro = $request->programas !== null ? $this->validarProgramas($request) : $request->cursos;

        $filtrosAsignaturas = function ($query) use ($filtro, $request) {
            return $query->whereIn($request->programas !== null ? 'cod_programa' : 'cod_materia', $filtro);
        };

        $filtroMalla = function ($query) use ($filtro, $request) {
            return $query->whereIn($request->programas !== null ? 'codprograma' : 'codigoCurso', $filtro);
        };

        $filtroPlaneacion = function ($query) use ($filtro, $request) {
            return $query->whereIn($request->programas !== null ? 'codprograma' : 'codMateria', $filtro);
        };

        $asignaturas = $request->tabla;

        $tablaPlaneacionDocentes = DB::table('necesidad_docente')
            ->where($filtrosAsignaturas)
            ->when($asignaturas == 'pendientes', function ($query) {
                return $query->where('cupo_sin_cubrir', '>', 0);
            }, function ($query) {
                return $query->where('cupo_sin_cubrir', 0)->where('docentes', '!=', '[]');
            })
            ->distinct()
            ->get();

        $mallaCurricular = DB::table('mallaCurricular')
            ->where($filtroMalla)
            ->select('codigoCurso', 'curso', 'codprograma')
            ->distinct()
            ->get();

        $planeacion = DB::table('planeacion')
            ->where($filtroPlaneacion)
            ->select('ciclo', 'semestre', 'codprograma', 'codMateria')
            ->groupBy('ciclo', 'semestre', 'codprograma', 'codMateria')
            ->get();

        $mallaCompleta = [];

        foreach ($mallaCurricular as $malla) {
            $codPrograma = $malla->codprograma;
            $codMateria = $malla->codigoCurso;
            $cursoEncontrado = $planeacion->flatten()->first(function ($curso) use ($codPrograma, $codMateria) {
                return $curso->codMateria == $codMateria && $curso->codprograma == $codPrograma;
            });
            if (!empty($cursoEncontrado->ciclo)) {

                $mallaCompleta[] = [
                    'codigoCurso' => $codMateria,
                    'curso' => $malla->curso,
                    'codPrograma' => $codPrograma,
                    'ciclo' => $cursoEncontrado->ciclo ? $cursoEncontrado->ciclo : '',
                    'semestre' => $cursoEncontrado->semestre ? $cursoEncontrado->semestre : '',
                ];
            }
        }

        return [
            $tablaPlaneacionDocentes,
            $mallaCompleta
        ];
    }

    public function tablaDocentesAsignados(Request $request)
    {

        $filtro = $request->programas !== null ? $this->validarProgramas($request) : $request->cursos;

        $filtrosAsignaturas = function ($query) use ($filtro, $request) {
            return $query->whereIn($request->programas !== null ? 'cod_programa' : 'cod_materia', $filtro);
        };

        $filtroMalla = function ($query) use ($filtro, $request) {
            return $query->whereIn($request->programas !== null ? 'codprograma' : 'codigoCurso', $filtro);
        };


        /**Consultar los docentes correspondientes. */
        $traerDocentes = DB::table('docentes_disponibles')->select('id_banner', 'nombre', 'email', 'cupo_ocupado')
            ->where(function ($query) use ($filtro) {
                foreach ($filtro as $filt) {
                    $query->orWhere('codigos_programa_materia', 'like', '%' . $filt . '%');
                }
            })
            ->where('cupo_ocupado', '>', '0')
            ->where('cupo_ocupado', '!=', DB::raw('cupo_16_semanas'))
            ->get();

        $mallaCurricular = DB::table('mallaCurricular')
            ->where($filtroMalla)
            ->select('codigoCurso', 'curso', 'codprograma')
            ->distinct()
            ->get();


        /** Consultar que asignaturas están asignadas a cada docente */
        $traerAsignaturas = DB::table('necesidad_docente')
            ->where($filtrosAsignaturas)
            ->get();

        $dataDocentes = [];

        foreach ($traerDocentes as $docente) {

            $idBanner = $docente->id_banner;

            $arrayAsignaturas = [];

            /** Consultar que asignaturas tiene el docennte cargadas */
            $asignaturas = $traerAsignaturas->filter(function ($asignatura) use ($idBanner) {
                return strpos($asignatura->docentes, $idBanner) !== false;
            });

            foreach ($asignaturas as $asignatura) {
                $docentes = json_decode($asignatura->docentes, true);
                $codPrograma = $asignatura->cod_programa;
                $codMateria = $asignatura->cod_materia;

                $docenteEncontrado = array_filter($docentes, function ($docente) use ($idBanner) {
                    return $docente['id_banner'] === $idBanner;
                });

                /**Consultar el nombre de la asignatura */
                $nombreAsignatura =  $mallaCurricular->flatten()->first(function ($curso) use ($codPrograma, $codMateria) {
                    return $curso->codigoCurso == $codMateria && $curso->codprograma == $codPrograma;
                });

                $docenteEncontrado = reset($docenteEncontrado);

                $docenteEncontrado['cupo'] =intval($docenteEncontrado['cupo']);

                /**Verificar si la asignatura ya está en el array y si es así sumar el cupo */
                if(isset($arrayAsignaturas[$codMateria]))
                {
                    $arrayAsignaturas[$codMateria]['cupo'] += $docenteEncontrado['cupo'];
                    if (!in_array($codPrograma, $arrayAsignaturas[$codMateria]['codPrograma'])) {
                        array_push($arrayAsignaturas[$codMateria]['codPrograma'], $codPrograma);
                    }
                }else{
                    $arrayAsignaturas[$codMateria] = [
                        'codigoMateria' => $codMateria,
                        'codPrograma' => [$codPrograma],
                        'nombreMateria' => $nombreAsignatura->curso,
                        'cupo' => $docenteEncontrado['cupo'],
                    ];
                }
            }

            $dataDocentes[] = [
                'nombre' => $docente->nombre,
                'email' => $docente->email,
                'id_banner' => $docente->id_banner,
                'cupoAsignado' => $docente->cupo_ocupado,
                'asignaturas' => $arrayAsignaturas,
            ];
        }

        return $dataDocentes;
    }
}