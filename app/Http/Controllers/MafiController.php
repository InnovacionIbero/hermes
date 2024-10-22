<?php

namespace App\Http\Controllers;

use App\Models\AlertasTempranas;
use App\Models\Estudiante;
use App\Models\IndiceCambiosMafi;
use App\Models\LogAplicacion;
use App\Models\Mafi;
use App\Models\MafiReplica;
use App\Models\MateriasPorVer;
use App\Models\Periodo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MafiController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function inicioMafi()
    {
        $menu= session('menu');
        //dd($menu);
        if(is_null($menu)):
            return redirect()->route('login.index');
        endif;
        return view('vistas.admin.mafi');
    }

    public function getDataMafi(){
        /// para activar el perodo activo en la base de datos
        $this->periodo();
        /** Año y Mes Actual*/
        $yearActual = date('Y');
        $mesActual =  date('n');
        /** Consulta para traer los periodos por el año actual*/
        $periodos = Periodo::all()->where('year', $yearActual);
        //return $periodos;
        /** Recorrer el array de los periodos optenidos */
        foreach ($periodos as $periodo) :
            /** Comparar de los periodos cual corresponde con mes actual */
            if ($periodo->mes == $mesActual) :
                /** Se crea variables para cada periodo*/
                $formacionContinua = $periodo->formacion_continua;
                $pregradoCuatrimestral = $periodo->year . $periodo->Pregrado_cuatrimestral;
                $pregradoSemestral = $periodo->year . $periodo->Pregrado_semestral;
                $especializacion = $periodo->year . $periodo->especializacion;
                $maestria = $periodo->year . $periodo->maestria;
            endif;
        endforeach;

        $log = DB::table('logAplicacion')->where([['accion', '=', 'Insert'], ['tabla_afectada', '=', 'datosMafiReplica']])->orderBy('id', 'desc')->first();
        //return $log->idFin;
        if (empty($log)) :
            /** Consulta de los datos tabla datMafi */
            $data = DB::table('datosMafi')
                ->where('estado', '=', 'Activo')
                ->whereIn('sello', ['TIENE RETENCION', 'TIENE SELLO FINANCIERO'])
                //->where('autorizado_asistir', 'LIKE', 'ACTIVO%')
                //->whereIn('periodo', [$pregradoCuatrimestral, $pregradoSemestral, $especializacion, $maestria])
                ->orderBy('id')
                ->get()
                ->chunk(200);

        else :
            $data = DB::table('datosMafi')
                ->where('estado', '=', 'Activo')
                ->whereIn('sello', ['TIENE RETENCION', 'TIENE SELLO FINANCIERO'])
                //->where('autorizado_asistir', 'LIKE', 'ACTIVO%')
                //->whereIn('periodo', [$pregradoCuatrimestral, $pregradoSemestral, $especializacion, $maestria])
                ->where('id', '>', $log->idFin)
                ->orderBy('id')
                ->get()
                ->chunk(200);

        endif;

        if (!empty($data[0])) :
            $numeroRegistros = 0;
            $primerId = $data[0][0]->id;
            $ultimoRegistroId = 0;
            $fechaInicio = date('Y-m-d H:i:s');
            foreach ($data as $keys => $estudiantes) :
                foreach ($estudiantes as $key => $value) :
                    //dd($value->sello);
                    if ($value->sello === 'TIENE RETENCION') :
                        $consultaActivoPlataforma = DB::table('datosMafi')->where([['id', '=', $value->id], ['sello', '=', $value->sello]])->whereIn('autorizado_asistir', ['ACTIVO EN PLATAFORMA', 'ACTIVO EN PLATAFORMA ICETEX'])->first();
                        if ($consultaActivoPlataforma) :
                            $insertar = MafiReplica::create([
                                'idbanner' => $value->idbanner,
                                'primer_apellido' => $value->primer_apellido,
                                'programa' => $value->programa,
                                'codprograma' => $value->codprograma,
                                'cadena' => $value->cadena,
                                'periodo' => $value->periodo,
                                'estado' => $value->estado,
                                'tipoestudiante' => $value->tipoestudiante,
                                'ruta_academica' => $value->ruta_academica,
                                'sello' => $value->sello,
                                'operador' => $value->operador,
                                'autorizado_asistir' => $value->autorizado_asistir,
                            ]);
                            $numeroRegistros++;
                        endif;
                    else :
                        $insertar = MafiReplica::create([
                            'idbanner' => $value->idbanner,
                            'primer_apellido' => $value->primer_apellido,
                            'programa' => $value->programa,
                            'codprograma' => $value->codprograma,
                            'cadena' => $value->cadena,
                            'periodo' => $value->periodo,
                            'estado' => $value->estado,
                            'tipoestudiante' => $value->tipoestudiante,
                            'ruta_academica' => $value->ruta_academica,
                            'sello' => $value->sello,
                            'operador' => $value->operador,
                            'autorizado_asistir' => $value->autorizado_asistir,
                        ]);
                        $numeroRegistros++;
                    endif;

                    $ultimoRegistroId = $value->id;
                    $idBannerUltimoRegistro = $value->idbanner;
                endforeach;
            endforeach;
            $fechaFin = date('Y-m-d H:i:s');

            //return "Fecha inicio: " .$fechaInicio. ', Fecha Fin '. $fechaFin;
            $insertLog = LogAplicacion::create([
                'idInicio' => $primerId,
                'idFin' => $ultimoRegistroId,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
                'accion' => 'Insert',
                'tabla_afectada' => 'datosMafiReplica',
                'descripcion' => 'Se realizo la insercion en la tabla datosMafiRelica desde la tabla datosMafi, iniciando en el id ' . $primerId . ' y terminando en el id ' . $ultimoRegistroId . ',insertando ' . $numeroRegistros . ' registros',
            ]);

            $insertIndiceCambio = IndiceCambiosMafi::create([
                'idbanner' => $idBannerUltimoRegistro,
                'accion' => 'Insert',
                'descripcion' => 'Se realizo la insercion en la tabla datosMafiRelica desde la tabla datosMafi, iniciando en el id ' . $primerId . ' y terminando en el id ' . $ultimoRegistroId . ',insertando ' . $numeroRegistros . ' registros',
                'fecha' => date('Y-m-d H:i:s'),
            ]);
            if ($insertLog && $insertIndiceCambio) :
                return "Numero de registros: '.$numeroRegistros.'=> primer id registrado: " . $primerId . ', Ultimo id registrado ' . $ultimoRegistroId;
            endif;
        else :
            return "No hay registros para replicar";
        endif;

        //$contData = count($data);
        /*$contKeys = 0;
        //$data = (array) $data;
        //dd($data);
        $numeroDatos = 200;
        $contadorGeneral = ceil($contData / $numeroDatos);
        dd(array_chunk($data,$contadorGeneral));
        for ($i=0; $i < $contadorGeneral; $i++):

            foreach ($data as $key => $value) {
                if($key === $contKeys):

                    return 'entra';
                else:
                    return 'No entro';
                endif;
            }
        endfor;*/
        /*$data = Mafi::where([['estado','<>','Inactivo']]);
        $dataLongitud = count($data);*/
    }

    public function programarSegundoCiclo(){
        /// para activar el perodo activo en la base de datos
        $periodo = $this->periodo();
        $marcaIngreso = "";
        foreach ($periodo as $key => $value) {
            $marcaIngreso .= (int)$value->periodos . ",";
        }

        // para procesasr las marcas de ingreso en los periodos
        $marcaIngreso = trim($marcaIngreso, ",");
        // Dividir la cadena en elementos individuales
        $marcaIngreso = explode(",", $marcaIngreso);
        // Convertir cada elemento en un número
        $marcaIngreso = array_map('intval', $marcaIngreso);
        $estudiantes = DB::table('estudiantes')
                            ->select('id', 'homologante', 'programa','bolsa','tipo_estudiante')
                            ->where([['id','>',0],['materias_faltantes','=','OK'],['programado_ciclo1','=','OK']])
                            ->whereNull('programado_ciclo2')
                            ->whereIn('marca_ingreso', $marcaIngreso)
                            ->orderBy('id','ASC')
                            ->limit(1000)
                            ->get();
        return $estudiantes;
    }

    public function getDataMafiReplica()
    {
        /**Programar materia de segundo ciclo */
        $estudiantesSC = $this->programarSegundoCiclo();
        foreach($estudiantesSC as $estudiante):
            $fechaInicio = date('Y-m-d H:i:s');
            $primerId = $estudiante->id;
            $ultimoRegistroId = 0;
            $idEstudiante = $estudiante->id;
            $codigoBanner = $estudiante->homologante;
            $programa = $estudiante->programa;
            $ruta = $estudiante->bolsa;
            $materiasPlaneadasConsulta = DB::table('planeacion')
                                        ->select('planeacion.codBanner','planeacion.codMateria')
                                        ->join('mallaCurricular','mallaCurricular.codigoCurso','=','planeacion.codMateria')
                                        ->where([['planeacion.codBanner','=',$codigoBanner],['planeacion.codprograma','=',$programa],['mallaCurricular.codprograma','=',$programa]])
                                        ->get();

            if(!$materiasPlaneadasConsulta):
                echo "Materias planeadas: " . $materiasPlaneadasConsulta . "<br />";
            endif;

            $materiasPlaneadas = [];

            foreach($materiasPlaneadasConsulta as $materiaPlaneada):
                $codMateria = $materiaPlaneada->codMateria;
                array_push($materiasPlaneadas,$codMateria);
                //$materiasPlaneadas = $materiasPlaneadas."'".$codMateria."',";
            endforeach;
            //$materiasPlaneadas = substr($materiasPlaneadas,0,-1);
            //$materiasPlaneadas =$materiasPlaneadas;
           // dd($materiasPlaneadas);

            $materiasPorverConsulta = DB::table('materiasPorVer')
                                    ->select('materiasPorVer.codBanner','materiasPorVer.codMateria','materiasPorVer.orden','mallaCurricular.creditos','mallaCurricular.ciclo')
                                    ->join('mallaCurricular','mallaCurricular.codigoCurso','=','materiasPorVer.codMateria')
                                    ->where([['materiasPorVer.codBanner','=',$codigoBanner],['materiasPorVer.codprograma','=',$programa],['mallaCurricular.codprograma','=',$programa]])
                                    ->whereNotIn('materiasPorVer.codMateria',$materiasPlaneadas)
                                    ->orderBy('materiasPorVer.orden','ASC')
                                    ->get();


            $creditosPlaneadosConsulta = DB::table('mallaCurricular')
                                    ->select('planeacion.codBanner', DB::raw('SUM(mallaCurricular.creditos) AS CreditosPlaneados'))
                                    ->join('planeacion', 'planeacion.codMateria', '=', 'mallaCurricular.codigoCurso')
                                    ->where('planeacion.codBanner', '=', $codigoBanner)
                                    ->where([['mallaCurricular.codprograma','=',$programa],['planeacion.codprograma','=',$programa]])
                                    ->groupBy('planeacion.codBanner')
                                    ->first();

            $creditosPlaneados =  $creditosPlaneadosConsulta->CreditosPlaneados == '' ? 0 : $creditosPlaneadosConsulta->CreditosPlaneados;
            $materiasPorVer = '';
            $numeroMateriasPorVer = $materiasPorverConsulta->count();
            if($numeroMateriasPorVer == 0):
                DB::table('estudiantes')->where([['homologante','=',$codigoBanner],['id','=',$idEstudiante]])->update(['programado_ciclo2'=>'OK']);
                $insertAlerta = AlertasTempranas::create([
                    'idbanner' => $codigoBanner,
                    'tipo_estudiante' => $estudiante->tipoestudiante,
                    'desccripcion' => 'El estudiante con idBanner' . $codigoBanner . ' no tiene materias por ver',
                ]);
                echo "Sin  Materias : " . $codigoBanner . "<br />";
            else:
                echo "con  Materias : " . $codigoBanner . "<br />";
                foreach($materiasPorverConsulta as $materia):
                    $codBanner = $materia->codBanner;
                    $codMateria = $materia->codMateria;
                    $creditoMateria = $materia->creditos;
                    $ciclo = $materia->ciclo;
                   // dd($materia);
                endforeach;
            endif;
            //dd($codigoBanner,$numeroMateriasPorVer);
        endforeach;
        //die();
        /**Programar materia de segundo ciclo */
        //die();
        /**para programar materias del primer ciclo */
        $programado_ciclo1 = NULL;
        /// para activar el perodo activo en la base de datos
        $periodo = $this->periodo();
        $marcaIngreso = "";
        foreach ($periodo as $key => $value) {
            $marcaIngreso .= (int)$value->periodos . ",";
        }

        // para procesasr las marcas de ingreso en los periodos
        $marcaIngreso = trim($marcaIngreso, ",");
        // Dividir la cadena en elementos individuales
        $marcaIngreso = explode(",", $marcaIngreso);
        // Convertir cada elemento en un número
        $marcaIngreso = array_map('intval', $marcaIngreso);
        $estudiantes = DB::table('estudiantes')
        ->select('id', 'homologante', 'programa', 'bolsa', 'tipo_estudiante')
        ->where('materias_faltantes', '=', 'OK')
        ->where('programado_ciclo1', '=', $programado_ciclo1)
        ->whereNull('programado_ciclo2')
        ->whereIn('marca_ingreso', $marcaIngreso)
        ->orderBy('id', 'asc')
        ->count();

        echo $estudiantes.'<br>';
        if ($estudiantes > 0) :
            $limit = 200;
            $numeroEstudiantes = ceil($estudiantes / $limit);
            //dd($numeroEstudiantes);
            for ($i = 0; $i < $numeroEstudiantes; $i++) :
                $log = DB::table('logAplicacion')->where([['accion', '=', 'Insert-PlaneacionPrimerCiclo'], ['tabla_afectada', '=', 'planeacion']])->orderBy('id', 'desc')->first();
                //dd($log);
                if (empty($log)) :
                    $id = 0;
                else :
                    $id = $log->idFin;
                endif;
                /**consulta de estudinates primer ciclo */
                $estudiantesPC = $this->programarPrimerCiclo($id, $limit, $programado_ciclo1);
                //dd($estudiantesPC->count());
                /**recorrer por cada estudiante  */
                foreach ($estudiantesPC as $estudiante) :
                    $fechaInicio = date('Y-m-d H:i:s');
                    $primerId = $estudiante->id;
                    $ultimoRegistroId = 0;
                    $idEstudiante = $estudiante->id;
                    $codigoBanner = $estudiante->homologante;
                    $programa = $estudiante->programa;
                    $ruta = $estudiante->bolsa;
                    if ($ruta != '') :
                        $ruta = 1;
                    endif;
                    $tipoEstudiante = $estudiante->tipo_estudiante;

                    switch ($tipoEstudiante) {
                        case str_contains($tipoEstudiante, 'TRANSFERENTE'):
                            $tipoEstudiante = 'TRANSFERENTE';
                            break;
                        case str_contains($tipoEstudiante, 'ESTUDIANTE ANTIGUO'):
                            $tipoEstudiante = 'ESTUDIANTE ANTIGUO';
                            break;
                        case str_contains($tipoEstudiante, 'PRIMER INGRESO'):
                            $tipoEstudiante = 'PRIMER INGRESO';
                            break;
                        case str_contains($tipoEstudiante, 'PSEUDO ACTIVOS'):
                            $tipoEstudiante = 'ESTUDIANTE ANTIGUO';
                            break;
                        case str_contains($tipoEstudiante, 'REINGRESO'):
                            $tipoEstudiante = 'ESTUDIANTE ANTIGUO';
                            break;
                        case str_contains($tipoEstudiante, 'INGRESO SINGULAR'):
                            $tipoEstudiante = 'PRIMER INGRESO';
                            break;

                        default:
                            # code...
                            break;
                    }
                    $ciclo = [1, 12];
                    /** */
                    $materiasPorVer = $this->materiasPorVer($codigoBanner, $ciclo, $programa);
                    /**select `planeacion`.`codBanner`, SUM(mallaCurricular.creditos) AS CreditosPlaneados from `mallaCurricular` inner join `planeacion` on `planeacion`.`codMateria` = `mallaCurricular`.`codigoCurso` where `planeacion`.`codBanner` = 100074631 group by `planeacion`.`codBanner` */
                    $numeroCreditos = DB::table('mallaCurricular')
                    ->select('planeacion.codBanner', DB::raw('SUM(mallaCurricular.creditos) AS CreditosPlaneados'))
                    ->join('planeacion', 'planeacion.codMateria', '=', 'mallaCurricular.codigoCurso')
                    ->where('planeacion.codBanner', '=', $codigoBanner)
                        ->groupBy('planeacion.codBanner')
                        ->first();
                    $numeroCreditos = $numeroCreditos == '' ? 0 : $numeroCreditos->CreditosPlaneados;
                    $numeroCreditosC1 = DB::table('mallaCurricular')
                    ->select(DB::raw('SUM(mallaCurricular.creditos) AS screditos'), DB::raw('COUNT(mallaCurricular.creditos) AS ccursos'))
                    ->join('planeacion', 'planeacion.codMateria', '=', 'mallaCurricular.codigoCurso')
                    ->where('planeacion.codBanner', '=', $codigoBanner)
                        ->whereIn('mallaCurricular.ciclo', [1, 12])
                        ->first();

                    $sumaCreditosCiclo1 = $numeroCreditosC1->screditos;
                    $sumaCreditosCiclo1 = $sumaCreditosCiclo1 == '' ? 0 : $sumaCreditosCiclo1;
                    $cuentaCursosCiclo1 = $numeroCreditosC1->ccursos;
                    $cuentaCursosCiclo1 = $cuentaCursosCiclo1 == '' ? 0 : $cuentaCursosCiclo1;



                    /**reglas del negocio */
                    $cicloReglaNegocio = 1;
                    $reglaNegocio = DB::table('reglasNegocio')
                    ->select('creditos', 'materiasPermitidas')
                    ->where([['programa', '=', $programa], ['ruta', '=', $ruta], ['tipoEstudiante', '=', $tipoEstudiante], ['ciclo', '=', $cicloReglaNegocio], ['activo', '=', 1]])
                        ->first();

                    $numeroCreditosPermitidos = $reglaNegocio->creditos;
                    $numeroMateriasPermitidos = $reglaNegocio->materiasPermitidas;
                    $orden = 1;
                    DB::beginTransaction();

                    try {

                        foreach ($materiasPorVer as $materia) :

                            if ($cuentaCursosCiclo1 >= $numeroMateriasPermitidos) :
                                break;
                            endif;
                            $codBanner = $materia->codBanner;
                            $codMateria = $materia->codMateria;
                            $creditoMateria = $materia->creditos;
                            $ciclo = $materia->ciclo;
                            $prerequisitosConsulta = $this->prerequisitos($codMateria, $programa);
                            //dd($programa,$codMateria,$prerequisitosConsulta);
                            $prerequisitos = $prerequisitosConsulta->prerequisito;
                            //dd($prerequisitos);
                            if ($prerequisitos == '' && $ciclo != 2 && $cuentaCursosCiclo1 < $numeroMateriasPermitidos) :
                                /**SELECT codMateria FROM planeacion WHERE codMateria="'.$codMateria.'" AND  	codBanner="'.$codBanner.'"; */
                                $estaPlaneacion = DB::table('planeacion')->select('codMateria')->where([['codMateria', '=', $codMateria], ['codBanner', '=', $codBanner]])->first();
                                //dd($numeroCreditos,$creditoMateria);
                                if ($estaPlaneacion == '' && $numeroCreditos < $numeroCreditosPermitidos) :
                                    $numeroCreditos = $numeroCreditos + $creditoMateria;
                                    $insertPlaneacion = DB::table('planeacion')->insert([
                                        'codBanner' => $codBanner,
                                        'codMateria' => $codMateria,
                                        'orden' => $orden,
                                        'semestre' => '1',
                                        'programada' => '',
                                        'codprograma' => $programa,
                                    ]);
                                    $cuentaCursosCiclo1++;
                                endif;
                            //echo $codBanner . '--' . $codMateria . '--' . $prerequisitos . "--" . $ciclo . '---' . $cuentaCursosCiclo1, '----' . 'sin P' . '<br>';
                            else :
                                $prerequisitos = [$prerequisitos];
                                $estaPlaneacion = DB::table('planeacion')->select('codMateria')->whereIn('codMateria', $prerequisitos)->where('codBanner', '=', $codBanner)->first();
                                $estaPorVer = DB::table('materiasPorVer')->select('codMateria')->whereIn('codMateria', $prerequisitos)->where('codBanner', '=', $codBanner)->orderBy('id', 'ASC')->first();
                                //dd($estaPorVer);
                                //dd($numeroCreditos,$creditoMateria);
                                if ($estaPlaneacion == '' && $estaPorVer == '' && $cuentaCursosCiclo1 < $numeroMateriasPermitidos) :
                                    $numeroCreditos = (int)$numeroCreditos + (int)$creditoMateria;
                                    $insertPlaneacion = DB::table('planeacion')->insert([
                                        'codBanner' => $codBanner,
                                        'codMateria' => $codMateria,
                                        'orden' => $orden,
                                        'semestre' => '1',
                                        'programada' => '',
                                        'codprograma' => $programa,
                                    ]);
                                    $cuentaCursosCiclo1++;
                                endif;
                            //echo $codBanner . '--' . $codMateria . '--' . $prerequisitos2 . "--" . $ciclo . '---' . $cuentaCursosCiclo1 . '----' . 'con P' . '<br>';
                            endif;
                        endforeach;
                        DB::table('estudiantes')->where([['homologante', '=', $estudiante->homologante], ['id', '=', $estudiante->id]])->update(['programado_ciclo1' => 'OK']);
                        $ultimoRegistroId = $estudiante->id;
                        $idBannerUltimoRegistro = $estudiante->homologante;
                        $fechaFin = date('Y-m-d H:i:s');
                        $insertLog = LogAplicacion::create([
                            'idInicio' => $primerId,
                            'idFin' => $ultimoRegistroId,
                            'fechaInicio' => $fechaInicio,
                            'fechaFin' => $fechaFin,
                            'accion' => 'Insert-PlaneacionPrimerCiclo',
                            'tabla_afectada' => 'planeacion',
                            'descripcion' => 'Se realizo la insercion en la tabla planeacion insertando las materias delprimer ciclo del estudiante ' . $codBanner . ', iniciando en el id ' . $primerId . ' y terminando en el id ' . $ultimoRegistroId . '.',
                        ]);
                        $insertIndiceCambio = IndiceCambiosMafi::create([
                            'idbanner' => $idBannerUltimoRegistro,
                            'accion' => 'Insert-PlaneacionPrimerCiclo',
                            'descripcion' => 'Se realizo la insercion en la tabla planeacion insertando las materias delprimer ciclor del estudiante ' . $codBanner . ', iniciando en el id ' . $primerId . ' y terminando en el id ' . $ultimoRegistroId . '.',
                            'fecha' => date('Y-m-d H:i:s'),
                        ]);
                        echo "-Fecha Inicio: " . $fechaInicio . "Fecha Fin: " . $fechaFin;
                        DB::commit();
                        echo "Inserción exitosa de la gran cantidad de datos." . $estudiante->homologante;
                        //$registroMPV++;
                    } catch (Exception $e) {
                        // Deshacer la transacción en caso de error
                        DB::rollBack();
                        // Manejar el error
                        echo "Error al insertar la gran cantidad de datos: " . $e->getMessage();
                        //dd($estudiante);
                    }
                    var_dump($cuentaCursosCiclo1);
                endforeach;
                die();
            endfor;
        else :
            echo "No hay estudiantes de primer ciclo para programar <br>";
        endif;
        die();
        /** Replicar los datos en estudiantes desde datosMafiReplica Aplicando los flitros */
        $log = DB::table('logAplicacion')->where([['accion', '=', 'Insert'], ['tabla_afectada', '=', 'estudiantes']])->orderBy('id', 'desc')->first();
        if (empty($log)) :
            $offset = 0;
        else :
            $offset = $log->idFin;
        endif;
        /**SELECT dmr.*,p.activo AS programaActivo FROM `datosMafiReplica` dmr
            INNER JOIN programas p ON p.codprograma=dmr.programa
            INNER JOIN periodo pe ON pe.periodos=dmr.periodo
            WHERE dmr.id > 0
            AND pe.periodoActivo = 1
            ORDER BY dmr.id ASC */
        $data = DB::table('datosMafiReplica')
            ->join('programas', 'datosMafiReplica.programa', '=', 'programas.codprograma')
            ->join('periodo', 'datosMafiReplica.periodo', '=', 'periodo.periodos')
            ->select('datosMafiReplica.*', 'programas.activo AS programaActivo')
            ->where([['datosMafiReplica.id', '>', $offset], ['periodo.periodoActivo', '=', 1]])
            ->orderBy('datosMafiReplica.id')
            ->get()
            ->chunk(200);



        if (!empty($data[0])) :
            $numeroRegistros = 0;
            $numeroRegistrosAlertas = 0;
            $primerId = $data[0][0]->id;
            $ultimoRegistroId = 0;
            $fechaInicio = date('Y-m-d H:i:s');
            foreach ($data as $keys => $estudiantes) :
                foreach ($estudiantes as $key => $value) :
                    if (str_contains($value->tipoestudiante, 'TRANSFERENTE EXTERNO') || str_contains($value->tipoestudiante, 'TRANSFERENTE INTERNO')) :
                        /**SELECT ha.codMateria FROM `datosMafiReplica` dmr
                        INNER JOIN historialAcademico ha ON ha.codBanner=dmr.idbanner
                        WHERE dmr.idbanner = [idbanner] */
                        $historial = DB::table('datosMafiReplica')
                            ->select('historialAcademico.codMateria')
                            ->join('historialAcademico', 'datosMafiReplica.idbanner', '=', 'historialAcademico.codBanner')
                            ->where('datosMafiReplica.idbanner', '=', $value->idbanner)->count();
                        if ($historial == 0) :
                            /**Insert tabla estudiantes en campo  tiene_historial "Sin Historial" */
                            if ($value->programaActivo < 1) :
                                $insertEstudinate = Estudiante::create([
                                    'homologante' => $value->idbanner,
                                    'nombre' => $value->primer_apellido,
                                    'programa' => $value->programa,
                                    'bolsa' => $value->ruta_academica,
                                    'operador' => $value->operador,
                                    'nodo' => 'nodo',
                                    'tipo_estudiante' => $value->tipoestudiante,
                                    'tiene_historial' => 'SIN HISTORIAL',
                                    'programaActivo' => 'NO SE ABRIO PROGRAMA',
                                    'marca_ingreso' => $value->periodo,
                                ]);
                                /**Insert tabla alertas_tempranas, transferente sin historial academico */
                                $insertAlerta = AlertasTempranas::create([
                                    'idbanner' => $value->idbanner,
                                    'tipo_estudiante' => $value->tipoestudiante,
                                    'desccripcion' => 'El estudiante con idBanner' . $value->idbanner . ' es "TRANSFERENTE EXTERENO" O  y no tiene historial academico',
                                ]);

                                if ($insertAlerta) :
                                    $numeroRegistrosAlertas++;
                                endif;
                            else :
                                /**Insert tabla estudiantes */
                                $insertEstudinate = Estudiante::create([
                                    'homologante' => $value->idbanner,
                                    'nombre' => $value->primer_apellido,
                                    'programa' => $value->programa,
                                    'bolsa' => $value->ruta_academica,
                                    'operador' => $value->operador,
                                    'nodo' => 'nodo',
                                    'tipo_estudiante' => $value->tipoestudiante,
                                    'tiene_historial' => 'SIN HISTORIAL',
                                    'marca_ingreso' => $value->periodo,
                                ]);

                                $insertAlerta = AlertasTempranas::create([
                                    'idbanner' => $value->idbanner,
                                    'tipo_estudiante' => $value->tipoestudiante,
                                    'desccripcion' => 'El estudiante con idBanner' . $value->idbanner . ' es "TRANSFERENTE EXTERENO" y no tiene historial academico',
                                ]);

                                if ($insertAlerta) :
                                    $numeroRegistrosAlertas++;
                                endif;

                                $insertAlerta = AlertasTempranas::create([
                                    'idbanner' => $value->idbanner,
                                    'tipo_estudiante' => $value->tipoestudiante,
                                    'desccripcion' => 'NO SE ABRIO PROGRAMA ' . $value->programa,
                                ]);

                            endif;
                            if ($insertAlerta) :
                                $numeroRegistrosAlertas++;
                            endif;

                            if ($insertEstudinate) :
                                $numeroRegistros++;
                            endif;
                        else :
                            if ($value->programaActivo > 0) :
                                /**Insert tabla estudiantes */
                                $insertEstudinate = Estudiante::create([
                                    'homologante' => $value->idbanner,
                                    'nombre' => $value->primer_apellido,
                                    'programa' => $value->programa,
                                    'bolsa' => $value->ruta_academica,
                                    'operador' => $value->operador,
                                    'nodo' => 'nodo',
                                    'tipo_estudiante' => $value->tipoestudiante,
                                    'marca_ingreso' => $value->periodo,
                                ]);
                            else :
                                /**Insert tabla estudiantes */
                                $insertEstudinate = Estudiante::create([
                                    'homologante' => $value->idbanner,
                                    'nombre' => $value->primer_apellido,
                                    'programa' => $value->programa,
                                    'bolsa' => $value->ruta_academica,
                                    'operador' => $value->operador,
                                    'nodo' => 'nodo',
                                    'tipo_estudiante' => $value->tipoestudiante,
                                    'programaActivo' => 'NO SE ABRIO PROGRAMA',
                                    'marca_ingreso' => $value->periodo,
                                ]);

                                $insertAlerta = AlertasTempranas::create([
                                    'idbanner' => $value->idbanner,
                                    'tipo_estudiante' => $value->tipoestudiante,
                                    'desccripcion' => 'NO SE ABRIO PROGRAMA ' . $value->programa,
                                ]);

                            endif;
                            if ($insertAlerta) :
                                $numeroRegistrosAlertas++;
                            endif;

                            if ($insertEstudinate) :
                                $numeroRegistros++;
                            endif;
                        endif;
                    else :
                        if ($value->programaActivo > 0) :
                            /**Insert tabla estudiantes */
                            $insertEstudinate = Estudiante::create([
                                'homologante' => $value->idbanner,
                                'nombre' => $value->primer_apellido,
                                'programa' => $value->programa,
                                'bolsa' => $value->ruta_academica,
                                'operador' => $value->operador,
                                'nodo' => 'nodo',
                                'tipo_estudiante' => $value->tipoestudiante,
                                'marca_ingreso' => $value->periodo,
                            ]);
                        else :
                            /**Insert tabla estudiantes */
                            $insertEstudinate = Estudiante::create([
                                'homologante' => $value->idbanner,
                                'nombre' => $value->primer_apellido,
                                'programa' => $value->programa,
                                'bolsa' => $value->ruta_academica,
                                'operador' => $value->operador,
                                'nodo' => 'nodo',
                                'tipo_estudiante' => $value->tipoestudiante,
                                'programaActivo' => 'NO SE ABRIO PROGRAMA',
                                'marca_ingreso' => $value->periodo,
                            ]);

                            $insertAlerta = AlertasTempranas::create([
                                'idbanner' => $value->idbanner,
                                'tipo_estudiante' => $value->tipoestudiante,
                                'desccripcion' => 'NO SE ABRIO PROGRAMA ' . $value->programa,
                            ]);

                            if ($insertAlerta) :
                                $numeroRegistrosAlertas++;
                            endif;
                        endif;

                        if ($insertEstudinate) :
                            $numeroRegistros++;
                        endif;
                    endif;
                    $ultimoRegistroId = $value->id;
                    $idBannerUltimoRegistro = $value->idbanner;
                endforeach;
            endforeach;
            $fechaFin = date('Y-m-d H:i:s');
            $insertLog = LogAplicacion::create([
                'idInicio' => $primerId,
                'idFin' => $ultimoRegistroId,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
                'accion' => 'Insert',
                'tabla_afectada' => 'estudiantes',
                'descripcion' => 'Se realizo la insercion en la tabla estudiantes desde la tabla datosMafiReplica, iniciando en el id ' . $primerId . ' y terminando en el id ' . $ultimoRegistroId . ',insertando ' . $numeroRegistros . ' registros',
            ]);

            $insertIndiceCambio = IndiceCambiosMafi::create([
                'idbanner' => $idBannerUltimoRegistro,
                'accion' => 'Insert',
                'descripcion' => 'Se realizo la insercion en la tabla estudiantes desde la tabla datosMafiReplica, iniciando en el id ' . $primerId . ' y terminando en el id ' . $ultimoRegistroId . ',insertando ' . $numeroRegistros . ' registros',
                'fecha' => date('Y-m-d H:i:s'),
            ]);
            echo  "Numero de registros: " . $numeroRegistros . "=> primer id registrado: " . $primerId . ', Ultimo id registrado ' . $ultimoRegistroId .
                "<br> Numero de registrosen alertas: " . $numeroRegistrosAlertas .
                "<br> inicio:" . $fechaInicio . "-- Fin:" . $fechaFin;
        else :
            echo "No hay registros para replicar <br>";
        endif;

        /**Ingresar la materias faltantes por ver de los estudiantes de primer ingreso e ingreso singular */
        $log = DB::table('logAplicacion')->where([['accion', '=', 'Insert-PrimerIngreso'], ['tabla_afectada', '=', 'materiasPorVer']])->orderBy('id', 'desc')->first();
        if (empty($log)) :
            $offset = 0;
        else :
            $offset = $log->idFin;
        endif;
        $primerIngreso = $this->falatntesPrimerIngreso($offset);
        if (!empty($primerIngreso[0])) :
            $fechaInicio = date('Y-m-d H:i:s');
            $registroMPV = 0;
            $primerId = $primerIngreso[0]->id;
            $ultimoRegistroId = 0;
            foreach ($primerIngreso as $estudiante) :
                $mallaCurricular = $this->BaseAcademica($estudiante->homologante, $estudiante->programa);
                $orden =1;
                foreach ($mallaCurricular as $key => $malla) :
                    $insertMateriaPorVer = MateriasPorVer::create([
                        "codBanner"      => $malla['codBanner'],
                        "codMateria"      => $malla['codMateria'],
                        "orden"      => $orden,
                        "codprograma"      => $malla['codprograma'],
                    ]);
                    $registroMPV++;
                    $orden++;
                endforeach;
                DB::table('estudiantes')->where([['homologante', '=', $estudiante->homologante], ['id', '=', $estudiante->id]])->update(['materias_faltantes' => 'OK']);
                $ultimoRegistroId = $estudiante->id;
                $idBannerUltimoRegistro = $estudiante->homologante;
            endforeach;
            $fechaFin = date('Y-m-d H:i:s');
            $insertLog = LogAplicacion::create([
                'idInicio' => $primerId,
                'idFin' => $ultimoRegistroId,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
                'accion' => 'Insert-PrimerIngreso',
                'tabla_afectada' => 'materiasPorVer',
                'descripcion' => 'Se realizo la insercion en la tabla materiasPorVer insertando las materias por ver del estudiante de primer ingreso, modificando el valor del campo materias_faltantes en la tabla estudiantes de NULL a "OK" en cada estudiante, iniciando en el id ' . $primerId . ' y terminando en el id ' . $ultimoRegistroId . ',insertando ' . $registroMPV . ' registros',
            ]);
            $insertIndiceCambio = IndiceCambiosMafi::create([
                'idbanner' => $idBannerUltimoRegistro,
                'accion' => 'Insert-PrimerIngreso',
                'descripcion' => 'Se realizo la insercion en la tabla materiasPorVer insertando las materias por ver del estudiante de primer ingreso, modificando el valor del campo materias_faltantes en la tabla estudiantes de NULL a "OK" en cada estudiante, iniciando en el id ' . $primerId . ' y terminando en el id ' . $ultimoRegistroId . ',insertando ' . $registroMPV . ' registros',
                'fecha' => date('Y-m-d H:i:s'),
            ]);
            echo $registroMPV . "-Fecha Inicio: " . $fechaInicio . "Fecha Fin: " . $fechaFin . "<br>";
        else :
            echo "No hay estudiantes de primer ingreso <br>";
        endif;

        /**Ingresar la materias faltantes por ver de los estudiantes transferentes */
        $log = DB::table('logAplicacion')->where([['accion', '=', 'Insert-Transferente'], ['tabla_afectada', '=', 'materiasPorVer']])->orderBy('id', 'desc')->first();
        if (empty($log)) :
            $offset = 0;
        else :
            $offset = $log->idFin;
        endif;
        $estudiantesTransferentes = $this->falatntesTranferentes($offset);
        if (!empty($estudiantesTransferentes[0])) :
            $fechaInicio = date('Y-m-d H:i:s');
            $registroMPV = 0;
            $primerId = $estudiantesTransferentes[0]->id;
            $ultimoRegistroId = 0;
            //dd($transferente);
            foreach ($estudiantesTransferentes as $estudiante) :
                $historial = $this->historialAcademico($estudiante->homologante);
                //dd($historial['codprograma']);
                $mallaCurricular = $this->BaseAcademica($estudiante->homologante,$estudiante->programa);
                //dd($mallaCurricular);
                $diff = array_udiff($mallaCurricular, $historial, function($a, $b) {
                    return $a['codMateria'] <=> $b['codMateria'];
                });
                $orden = 1;
                foreach ($diff as $key => $value) :
                    //dd($value);
                    $insertMateriaPorVer = MateriasPorVer::create([
                        "codBanner"      => $value['codBanner'],
                        "codMateria"      => $value['codMateria'],
                        "orden"      => $orden,
                        "codprograma"      => $value['codprograma'],
                    ]);
                    $orden++;
                    $registroMPV++;
                endforeach;
                DB::table('estudiantes')->where([['homologante','=',$estudiante->homologante],['id','=',$estudiante->id]])->update(['materias_faltantes'=>'OK']);

                $ultimoRegistroId = $estudiante->id;
                $idBannerUltimoRegistro = $estudiante->homologante;
            endforeach;
            $fechaFin = date('Y-m-d H:i:s');
            $insertLog = LogAplicacion::create([
                'idInicio' => $primerId,
                'idFin' => $ultimoRegistroId,
                'fechaInicio' => $fechaInicio,
                'fechaFin' => $fechaFin,
                'accion' => 'Insert-Transferente',
                'tabla_afectada' => 'materiasPorVer',
                'descripcion' => 'Se realizo la insercion en la tabla materiasPorVer insertando las materias por ver del estudiante transferente, iniciando en el id ' . $primerId . ' y terminando en el id ' . $ultimoRegistroId . ',insertando ' . $registroMPV . ' registros',
            ]);

            $insertIndiceCambio = IndiceCambiosMafi::create([
                'idbanner' => $idBannerUltimoRegistro,
                'accion' => 'Insert-Transferente',
                'descripcion' => 'Se realizo la insercion en la tabla materiasPorVer insertando las materias por ver del estudiante transferente, iniciando en el id ' . $primerId . ' y terminando en el id ' . $ultimoRegistroId . ',insertando ' . $registroMPV . ' registros',
                'fecha' => date('Y-m-d H:i:s'),
            ]);
            echo $registroMPV . "-Fecha Inicio: " . $fechaInicio . "Fecha Fin: " . $fechaFin."<br>";
        else :
            echo "No hay estudiantes TRANSFERENTES <br>";
        endif;

        /**Ingresar la materias faltantes por ver de los estudiantes ANTIGUOS  */
        $estudiantesAntiguosC = DB::table('estudiantes')
            ->where('tipo_estudiante', 'LIKE', 'ESTUDIANTE ANTIGUO%')
            ->whereNull('programaActivo')
            ->whereNull('materias_faltantes')
            ->orWhere('tipo_estudiante', 'LIKE', 'PSEUDO ACTIVOS%')
            ->whereNull('programaActivo')
            ->whereNull('materias_faltantes')
            ->orderBy('id')->count();
        if($estudiantesAntiguosC > 0):
        $numeroEstudiantes = ceil($estudiantesAntiguosC/200);
        //dd(ceil($numeroEstudiantes/2));
        for ($i=0; $i < $numeroEstudiantes; $i++) :
            $log = DB::table('logAplicacion')->where([['accion', '=', 'Insert-EstudinatesAntiguos'], ['tabla_afectada', '=', 'materiasPorVer']])->orderBy('id', 'desc')->first();
            if(empty($log)):
                $offset = 0;
            else:
                $offset = $log->idFin;
            endif;
            $limit = 200;
            $estudiantesAntiguos = $this->faltantesAntiguos($offset,$limit);
            foreach($estudiantesAntiguos as $estudiante):
                $fechaInicio = date('Y-m-d H:i:s');
                $primerId = $estudiante->id;
                $ultimoRegistroId = 0;
                /**Consultas:
                 * Historial por Estudiante
                 * Malla Curricular por programa*/
                $historial = $this->historialAcademico($estudiante->homologante);
                $mallaCurricular = $this->BaseAcademica($estudiante->homologante,$estudiante->programa);
                $diff = array_udiff($mallaCurricular, $historial, function($a, $b) {
                    return $a['codMateria'] <=> $b['codMateria'];
                });
                $cantidadDiff = count($diff);
                if(count($diff) > 0):
                    DB::beginTransaction();

                    /**insertar materiasPorVer */
                    try {
                        DB::table('materiasPorVer')->insert($diff);
                        DB::table('estudiantes')->where([['homologante','=',$estudiante->homologante],['id','=',$estudiante->id]])->update(['materias_faltantes'=>'OK']);
                        $ultimoRegistroId = $estudiante->id;
                        $idBannerUltimoRegistro = $estudiante->homologante;
                        $fechaFin = date('Y-m-d H:i:s');
                        $insertLog = LogAplicacion::create([
                            'idInicio' => $primerId,
                            'idFin' => $ultimoRegistroId,
                            'fechaInicio' => $fechaInicio,
                            'fechaFin' => $fechaFin,
                            'accion' => 'Insert-EstudinatesAntiguos',
                            'tabla_afectada' => 'materiasPorVer',
                            'descripcion' => 'Se realizo la insercion en la tabla materiasPorVer insertando las materias por ver del estudiante de primer ingreso, iniciando en el id ' . $primerId . ' y terminando en el id ' . $ultimoRegistroId . '.',
                        ]);
                        $insertIndiceCambio = IndiceCambiosMafi::create([
                            'idbanner' => $idBannerUltimoRegistro,
                            'accion' => 'Insert-EstudinatesAntiguos',
                            'descripcion' => 'Se realizo la insercion en la tabla materiasPorVer insertando las materias por ver del estudiante de primer ingreso, iniciando en el id ' . $primerId . ' y terminando en el id ' . $ultimoRegistroId . '.',
                            'fecha' => date('Y-m-d H:i:s'),
                        ]);
                        echo "-Fecha Inicio: " . $fechaInicio . "Fecha Fin: " . $fechaFin;
                        // Confirmar la transacción
                        DB::commit();
                        echo "Inserción exitosa de la gran cantidad de datos.". $estudiante->homologante;
                        //$registroMPV++;
                    } catch (Exception $e) {
                        // Deshacer la transacción en caso de error
                        DB::rollBack();
                        // Manejar el error
                        echo "Error al insertar la gran cantidad de datos: " . $e->getMessage();
                        //dd($estudiante);
                    }
                else:
                    /**crear alerta temprana estudinate vio todo */
                    $insertAlerta = AlertasTempranas::create([
                        'idbanner' => $estudiante->homologante,
                        'tipo_estudiante' => $estudiante->tipo_estudiante,
                        'desccripcion' => 'El estudiante con idBanner' . $estudiante->homologante . ' es estudiante antiguo y ya vio todo.',
                    ]);
                    DB::table('estudiantes')->where([['homologante','=',$estudiante->homologante],['id','=',$estudiante->id]])
                    ->update(['materias_faltantes'=>'YA VIO TODO','tiene_historial'=>'COMPLETO']);
                    echo "estudinate vio todo". $estudiante->homologante;
                endif;
            endforeach;
        endfor;
        else:
            echo "No hay estudiantes ANTIGUOS,ni PSEUDO INGRESO O REINGRESO <br>";
        endif;


    }

    public function falatntesPrimerIngreso($offset)
    {
        /**SELECT * FROM `estudiantes`
            WHERE `id` > 0
            AND `tipo_estudiante` LIKE 'PRIMER%'
            AND `programaActivo` IS NULL
            AND `materias_faltantes` IS NULL
            OR `tipo_estudiante` LIKE 'INGRESO%'
            AND `programaActivo` IS NULL
            AND `materias_faltantes` IS NULL */
        $estudiantesPrimerIngreso = DB::table('estudiantes')
            ->where('id','>',$offset)
            ->where('tipo_estudiante', 'LIKE', 'PRIMER%')
            ->whereNull('programaActivo')
            ->whereNull('materias_faltantes')
            ->orWhere('tipo_estudiante', 'LIKE', 'INGRESO%')
            ->whereNull('programaActivo')
            ->whereNull('materias_faltantes')
            ->orderBy('id')
            ->get();

        //dd($estudiantesPrimerIngreso);

        return $estudiantesPrimerIngreso;
    }

    public function falatntesTranferentes($offset)
    {
        /**SELECT * FROM `estudiantes`
            WHERE `id` > 0
            AND `tipo_estudiante` like 'TRANSFERENTE%'
            AND `programaActivo` IS NULL
            AND `tiene_historial` IS NULL
            AND `materias_faltantes` IS NULL */
        $estudiantesPrimerIngreso = DB::table('estudiantes')
            ->where('id','>',$offset)
            ->where('tipo_estudiante', 'LIKE', 'TRANSFERENTE%')
            ->whereNull('programaActivo')
            ->whereNull('tiene_historial')
            ->whereNull('materias_faltantes')
            ->orderBy('id')
            ->get();

        return $estudiantesPrimerIngreso;
    }

    public function faltantesAntiguos($offset,$limit)
    {

        /**SELECT * FROM `estudiantes`
            WHERE `id` > 0
            AND `tipo_estudiante` LIKE 'ESTUDIANTE ANTIGUO%'
            AND `programaActivo` IS NULL
            AND `materias_faltantes` IS NULL
            OR `tipo_estudiante` LIKE 'PSEUDO ACTIVOS%'
            AND `programaActivo` IS NULL
            AND `materias_faltantes` IS NULL
            OR `tipo_estudiante` = 'REINGRESO'
            AND `programaActivo` IS NULL
            AND `materias_faltantes` IS NULL
            ORDER BY `id` ASC
            LIMIT 200  */
        $estudiantesAntiguos = DB::table('estudiantes')
            ->where('id','>',$offset)
            ->where('tipo_estudiante', 'LIKE', 'ESTUDIANTE ANTIGUO%')
            ->whereNull('programaActivo')
            ->whereNull('materias_faltantes')
            ->orWhere('tipo_estudiante', 'LIKE', 'PSEUDO ACTIVOS%')
            ->whereNull('programaActivo')
            ->whereNull('materias_faltantes')
            ->orWhere('tipo_estudiante', '=', 'REINGRESO')
            ->whereNull('programaActivo')
            ->whereNull('materias_faltantes')
            ->orderBy('id')
            ->limit($limit)
            ->get();

        return $estudiantesAntiguos;
    }

    public function BaseAcademica($idbanner,$programa)
    {
        //Obtener la base academica del programa seleccionado

        $data = [];
        $mallaCurricular = DB::table('mallaCurricular')
        ->join('programas', 'programas.codprograma', '=', 'mallaCurricular.codprograma')
        ->select('mallaCurricular.codigoCurso', 'mallaCurricular.orden', 'mallaCurricular.codprograma')
        ->where([['programas.activo', '=', 1], ['mallaCurricular.codprograma', '=', $programa]])
            ->orderBy('semestre', 'asc')
            ->orderBy('orden', 'asc')
            ->get();
        $orden =1;
        foreach ($mallaCurricular as $key => $value) :
            $data[] = [
                'codBanner' => $idbanner,
                'codMateria'=>$value->codigoCurso,
                'orden'=>$orden,
                'codprograma'=>$value->codprograma,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $orden++;
        endforeach;
        //return $mallaCurricular;
        return $data;
    }


    public function historialAcademico($idBanner)
    {

        $data = [];
        $historial = DB::table('historialAcademico')
            ->select('codMateria', 'codprograma')
            ->where([['codBanner', '=', $idBanner],['codMateria','<>','na']])
            ->get()->toArray();

        foreach($historial as $key => $value):
            $data[] = [
                'codMateria'=>$value->codMateria,
                'codprograma'=>$value->codprograma];
        endforeach;
        //dd($data);

        return $data;
    }

    //*** funcion para activar los periodos automaticamente */
    public function periodo()
    {

        /** traemos la fecha actual para poder comparar con el periodo */
        $fechaActual = date('Y-m-d');
        $mes = explode('-', $fechaActual);
        $periodo = DB::table('periodo')->get();

        //$mes[1] = 06;


        foreach ($periodo as $key => $value) {



            $ciclo1 = explode('-', $value->fechaInicioCiclo1);
            $ciclo2 = explode('-', $value->fechaInicioCiclo2);


            if (in_array($mes[1], $ciclo1) || in_array($mes[1], $ciclo2)) {

                DB::table('periodo')
                    ->where('id', $value->id)
                    ->update(['periodoActivo' => 1]);

                if (in_array((int)$mes[1], $ciclo1)) {
                    DB::table('periodo')
                        ->where('id', $value->id)
                        ->update(['activoCiclo1' => 1]);
                } else {
                    DB::table('periodo')
                        ->where('id', $value->id)
                        ->update(['activoCiclo1' => 0]);
                }

                if (in_array((int)$mes[1], $ciclo2)) {
                    DB::table('periodo')
                        ->where('id', $value->id)
                        ->update(['activoCiclo2' => 1]);
                } else {
                    DB::table('periodo')
                        ->where('id', $value->id)
                        ->update(['activoCiclo2' => 0]);
                }
            } else {
                DB::table('periodo')
                    ->where('id', $value->id)
                    ->update(['periodoActivo' => 0]);
            }
        }

        $periodo = DB::table('periodo')
            ->where(['periodoActivo' => 1])
            ->get();




        return  $periodo;
    }


    
    public function filtros()
    {
       //var_dump("entro");die();
       $Facultades = $_POST['codfacultad'];
       $periodos = $_POST['periodos'];
       $tabla = $_POST['tabla'];
       // Verificamos si el usuario tiene programas asignados
       $programa=auth()->user()->programa;
       
      
       if(count($Facultades) === 1 && $Facultades[0] === ""){
           $facultades=auth()->user()->id_facultad;
           $facts=explode(';',$facultades);
           $Facultades = DB::table('facultad')->select('nombre')->whereIn('id',$facts)->pluck('nombre')->toArray();
       }
      
       if(isset($programa) && !empty($programa)){
           $program=explode(';',$programa);
           
           $codigosPrograma = DB::table('programas')->select('codprograma')->whereIn('id',$program)->pluck('codprograma')->toArray();
       }else{
           $codigosPrograma = DB::table('programas')->select('codprograma')->whereIn('Facultad',$Facultades)->pluck('codprograma')->toArray();  
       }


           $programas = [];
         
       
            
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
       
                //dd($consultaProgramas);

            foreach($consultaProgramas as $dato)
            {
                
                $programa = $dato->Programa;

                if (!in_array($programa, $programas)) {
                    $programas[] = $programa;
                }
            }   
           
        //dd($programas);
       // Consulta para enviar unicamente los programas activos de las facultades y periodos correspondientes
       $programasRetorno = DB::table('programas as p')
       ->join('programasPeriodos as pP', 'p.codprograma', '=', 'pP.codPrograma')
           ->whereIn('p.Facultad', $Facultades)
           ->whereIn('p.codprograma', $programas)
           ->select('p.programa', 'p.codprograma','p.Facultad')
           ->groupBy('p.codprograma', 'p.programa', 'p.Facultad')
           ->get();
          // dd($Facultades,$programas, $programasRetorno);
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

    /** funcion para traer todos los programas activos en la plataforma */
    public function get_programas(){

        /* consulta sql para traer los programas activos
        * SELECT * FROM `programas` WHERE activo=1;
        */
        $programas= DB::table('programas')
            ->where('activo','=',1)
            ->get();

        return $programas;

    }


        public function Generar_faltantes(){

                /// para activar el perodo activo en la base de datos
                $periodo = $this->periodo();
                $marcaIngreso = "";
                foreach ($periodo as $key => $value) {
                    $marcaIngreso .= (int)$value->periodos . ",";
                }

                // para procesasr las marcas de ingreso en los periodos
                $marcaIngreso=trim($marcaIngreso,",");
                // Dividir la cadena en elementos individuales
                $marcaIngreso = explode(",", $marcaIngreso);
                // Convertir cada elemento en un número
                $marcaIngreso = array_map('intval', $marcaIngreso);
            //traemos todos los programas activos para la consulta
                $programas= $this->get_programas();



                /*select `id`, `homologante`, `programa`
                        from `estudiantes`
                where `materias_faltantes` = 'OK'
                and `programado_ciclo1` is null
                and `programado_ciclo2` is null
                and `programa` = 'PCPV'
                and `marca_ingreso` in (202305, 202312, 202332, 202342, 202352, 202306, 202313, 202333, 202343, 202353);*/


            # code...

            foreach ($programas as $key => $value) {

                // Estudiantes para generar faltantes por programa
                $consulta_homologante= DB::table('estudiantes')
                ->select('id', 'homologante', 'programa')
                ->where('materias_faltantes','OK')
                ->whereNull('programado_ciclo1')
                ->whereNull('programado_ciclo2')
                ->where('programa',$value->codprograma)
                ->whereIn('marca_ingreso',$marcaIngreso)
                ->orderBy('id','ASC')
                ->chunk(200, function($estudiantes){

                    foreach ($estudiantes as $estudiante) :

                        $id_homologante=$estudiante->id;
                        $codHomologante=$estudiante->homologante;
                        $programa_homologante=$estudiante->programa;



                    endforeach;
                });
            }

        }

        /**validar si el estudiante tiene creditos planeados */
        public function programarPrimerCiclo($id,$limit,$programado_ciclo1){

            /**select `planeacion`.`codBanner`, SUM(mallaCurricular.creditos) AS CreditosPlaneados from `mallaCurricular` inner join `planeacion` on `planeacion`.`codMateria` = `mallaCurricular`.`codigoCurso` where `planeacion`.`codBanner` = ? group by `planeacion`.`codBanner` */
            /// para activar el perodo activo en la base de datos
            $periodo = $this->periodo();
            $marcaIngreso = "";
            foreach ($periodo as $key => $value) {
                $marcaIngreso .= (int)$value->periodos . ",";
            }

            // para procesasr las marcas de ingreso en los periodos
            $marcaIngreso=trim($marcaIngreso,",");
            // Dividir la cadena en elementos individuales
            $marcaIngreso = explode(",", $marcaIngreso);
            // Convertir cada elemento en un número
            $marcaIngreso = array_map('intval', $marcaIngreso);

            //$marcaIngreso = [202313,202333];
            $estudiante = DB::table('estudiantes')
                    ->select('id','homologante','programa','bolsa','tipo_estudiante')
                    ->where('id','>',$id)
                    ->where('materias_faltantes','=','OK')
                    ->where('programado_ciclo1','=',$programado_ciclo1)
                    ->whereNull('programado_ciclo2')
                    ->whereIn('marca_ingreso',$marcaIngreso)
                    ->orderBy('id','asc')
                    ->limit($limit)
                    ->get();



            return $estudiante;
        }


        /**Materias por ver de cada estudiante */
        public function materiasPorVer($codBanner,$ciclo,$programa){


            // Materias que debe ver el estudiante por ciclo
          $materiasPorVer = DB::table("materiasPorVer")
                ->select('materiasPorVer.codBanner','materiasPorVer.codMateria','materiasPorVer.orden','mallaCurricular.creditos','mallaCurricular.ciclo')
                ->join('mallaCurricular','mallaCurricular.codigoCurso','=','materiasPorVer.codMateria')
                ->where('materiasPorVer.codBanner','=',$codBanner)
                ->whereIn('mallaCurricular.ciclo',$ciclo)
                ->where('materiasPorVer.codprograma','=',$programa)
                ->where('mallaCurricular.codprograma','=',$programa)
                ->orderBy('materiasPorVer.orden','ASC')
                ->get();

            return $materiasPorVer;
        }

        public function prerequisitos($codMateria,$codPrograma){
            /**SELECT prerequisito FROM mallaCurricular WHERE codigoCurso="?" AND codprograma = "?" */
            $prerequisitos = DB::table('mallaCurricular')
                                ->select('prerequisito')
                                ->where([['codigoCurso','=',$codMateria],['codprograma','=',$codPrograma]])
                                ->first();
            return $prerequisitos;
        }

        // No. de creditos para el homologante
        public function sumar_creditos($codBanner){


            $consulta_sumacreditos = DB::table('mallaCurricular')
            ->select('planeacion.codBanner',DB::raw('SUM(mallaCurricular.creditos) AS CreditosPlaneados'))
            ->join('planeacion','planeacion.codMateria','=','mallaCurricular.codigoCurso')
            ->where('planeacion.codBanner','=',$codBanner)
            ->groupBy('planeacion.codBanner')
            ->first();


            $creditos_homologantes = $consulta_sumacreditos==NULL ? "0" :$consulta_sumacreditos->CreditosPlaneados;

            return $creditos_homologantes;

        }

        public function consulta_creditos($codBanner,$ciclo){

            $numeroCreditosC1 = DB::table('mallaCurricular')
                    ->select(DB::raw('SUM(mallaCurricular.creditos) AS creditos'),DB::raw('COUNT(mallaCurricular.creditos) AS cursos'))
                    ->join('planeacion','planeacion.codMateria','=','mallaCurricular.codigoCurso')
                    ->where('planeacion.codBanner','=',$codBanner)
                    ->whereIn('mallaCurricular.ciclo',$ciclo)
                    ->first();

            return $numeroCreditosC1;

        }

        public function reglas_negocio($ciclo,$programa,$ruta,$tipoEstudiante){

            $cicloReglaNegocio =$ciclo;

            $reglaNegocio =DB::table('reglasNegocio')
                            ->select('creditos','materiasPermitidas')
                            ->where([['programa','=',$programa],['ruta','=',$ruta],['tipoEstudiante','=',$tipoEstudiante],['ciclo','=',$cicloReglaNegocio],['activo','=',1]])
                            ->first();


            return  $reglaNegocio;
        }

        public function esta_en_planeacion($prerequisitos,$codBanner){

            $query=DB::table('planeacion')
            ->select('codMateria')
            ->whereIn('codMateria',[$prerequisitos->prerequisito])
            ->where('codBanner','=',$codBanner)
            ->first();

            return $query;
        }

        public function Planeacion($codBanner,$ciclo,$programa,$codMateria,$codPrograma,$ruta,$tipoEstudiante){

            $materiasPorVer=$this->materiasPorVer($codBanner,$ciclo,$programa);

            $prerequisitos=$this->prerequisitos($codMateria,$codPrograma);

            $creditos_homologantes=$this->sumar_creditos($codBanner);

            $numeroCreditosC1=$this->consulta_creditos($codBanner,$ciclo);

            $sumaCreditosCiclo1 = $numeroCreditosC1->creditos==''?0:$numeroCreditosC1->creditos;

            $cuentaCursosCiclo1 = $numeroCreditosC1->cursos==''?0:$numeroCreditosC1->cursos;

            $reglaNegocio=$this->reglas_negocio($ciclo,$programa,$ruta,$tipoEstudiante);

            $numeroCreditosPermitidos = $reglaNegocio->creditos;
            $numeroMateriasPermitidas = $reglaNegocio->materiasPermitidas;
            $orden2=1;

            foreach ($materiasPorVer as $materias) {
                # code...

                $codBanner      =  $materias->codBanner;
                $codMateria     =  $materias->codMateria;
                $creditoMateria =  $materias->creditos;
                $ciclo          =  $materias->ciclo;



                if($prerequisitos->prerequisito=='' && $cuentaCursosCiclo1 < $numeroMateriasPermitidas) {
                   // echo "entro sin prerequicitos materias  menores y creditos menores a los permitidos";


                    $esta_en_planeacion =$this-> esta_en_planeacion($prerequisitos,$codBanner);

                    if(empty($esta_en_planeacion) && $creditos_homologantes<   $numeroCreditosPermitidos) {

                        $creditos_homologantes = $creditos_homologantes + $creditoMateria;

                        $insert_planeada = 'INSERT INTO planeacion (id, codBanner, codMateria, orden, semestre, programada, codprograma) VALUES (NULL, '.$codBanner.', "'.$codMateria.'", '.$orden2.',"1", "", "'.$programa.'");';

                        $planeadas_insert=DB::insert(  $insert_planeada );


                    }
                } else {

                    $esta_en_planeacion =$this-> esta_en_planeacion($prerequisitos,$codBanner);

//dd($cuentaCursosCiclo1);
                    if(empty($esta_en_planeacion)&& $ciclo!=2 && $cuentaCursosCiclo1< $numeroMateriasPermitidas) {

                        $creditos_homologantes = $creditos_homologantes + $creditoMateria;

                          $insert_planeada = 'INSERT INTO planeacion (id, codBanner, codMateria, orden, semestre, programada, codprograma) VALUES (NULL, '.$codBanner.', "'.$codMateria.'", '.$orden2.',"1", "", "'.$programa.'");';

                        $planeadas_insert=DB::insert(  $insert_planeada);
                        //$cuenta_cursos_ciclo1++;
                        // echo "22  " . $insert_planeada . "<br />";
                        // exit();
                        //echo "Actualziado Crdeditos Hom:" . $creditos_homologantes . "<br />";
                    }



                }
                $orden2++;
           /* $update_homologante = 'UPDATE homologantes SET programado_ciclo1="OK" WHERE homologantes.id='.$id_homologante.';';
            $resultado_updatehomologante = mysql_query($update_homologante, $link);
            echo "Planeación realizada para : " . $codBanner . " y " . $codMateria . "<br />";*/

            }

        }


        // funcion para probar otras funciones
        public function probarfunciones(){

            $this->periodo();
          
        }


    public function estaEnPlaneacion($materia,$codBanner){
        $materia = [$materia];
        //dd($materia);
        /**SELECT codMateria FROM planeacion WHERE codMateria in ('AFV22612') AND codBanner="100147341" */
        $consultaPlaneacion = DB::table('planeacion')
                                    ->select('codMateria')
                                    ->whereIn('codMateria',$materia)
                                    ->where('codBanner','=',$codBanner)
                                    ->get();
        return $consultaPlaneacion;
    }



}
