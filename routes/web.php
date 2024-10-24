<?php

/** definimos losb controladores para que funcionen las rutas  */

use App\Http\Controllers\AlertasTempranasController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistroController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\contrasenaController;
use App\Http\Controllers\cambioController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\MafiController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\facultadController;
use App\Http\Controllers\filtrosController;
use App\Http\Controllers\VistasTransversalesController;
use App\Http\Controllers\HistorialEstudianteController;
use App\Http\Controllers\InformeMafiController;
use App\Http\Controllers\InformeMoodleMejoradoController;
use App\Http\Controllers\InformePlaneacionController;
use App\Http\Controllers\PruebaController;
use App\Http\Controllers\MicrosoftLoginController;
use App\Http\Controllers\MicrosoftGraphController;
use App\Http\Controllers\RegistrosMoodleController;
use App\Http\Controllers\MicrosoftAuthController;
use App\Http\Controllers\PlaneacionDocentesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    //return view('welcome');
    return view('login/index');
});
Route::get('/auth/microsoft', [MicrosoftAuthController::class,'redirectToMicrosoft'])->name('login.microsoft');;
Route::get('/auth/microsoft/callback', [MicrosoftAuthController::class,'handleMicrosoftCallback']);



Route::get('/hola',[PruebaController::class,'index']);

/** definimos las rutas por controlador en este caso son las del usuario logueado */
Route::controller(UserController::class)->group(function () {

    /** cuando el login es correcto y existe la sesion del usuario va a la pagina de inicio  */
    Route::get('/home', 'home')->middleware('auth')->name('home.index');

    /** Vista al pulsar el botón "Informe Mafi" */
    Route::get('/home/Mafi', 'vistasMafi')->middleware('auth')->name('home.mafi');

    /** Vista al pulsar el botón "Planeación" */
    Route::get('/home/Planeacion', 'vistasPlaneacion')->middleware('auth')->name('home.planeacion');

    /** Vista al pulsar el botón "Moodle" */
    Route::get('/home/Moodle', 'vistasMoodle')->middleware('auth')->name('home.moodle');
    Route::get('/home/MoodleCerrado', 'vistasMoodlecerrados')->middleware('auth')->name('home.moodleCerrado');

    /** para cargar las vistas predefinidas en la facultad */
    Route::get('/home/facultad/', 'facultad')->middleware('auth')->name('facultad.index');
    /** cargamos la vista del perfil del usuario */
    Route::get('/home/perfil/{id}', 'perfil')->middleware('auth')->name('user.perfil');
    /** cargamos la vista para editar los datos del usuario */
    Route::get('/home/editar/{id}', 'editar')->middleware('auth')->name('user.editar');
    /** actualizar los datos del usuario */
    Route::post('/home/actualizar/{id}', 'actualizar')->middleware('auth')->name('user.actualizar');
    //** Ruta para inactivar usuario */
    Route::post('/home/inactivarusuario', 'inactivar_usuario')->middleware('auth')->name('user.inactivar');
    //** Ruta para activar usuario */
    Route::post('/home/activarusuario', 'activar_usuario')->middleware('auth')->name('user.activar');

    /** cargamos la vista de administracion de usuarios */
    Route::get('/home/usuarios', 'userView')->middleware('auth','admin')->name('admin.users');
    /** cargamos la vista para mostarar todos los usuarios */
    Route::get('/home/users', 'get_users')->middleware('auth','admin')->name('admin.getusers');
    /** cargamos la vista de administracion de facultades */
    Route::get('/home/amdministracionfacultades', 'facultad_view')->middleware('auth','admin')->name('admin.facultades');
    /** cargamos la vista para mostrar todas las facultades */
    Route::get('/home/facultades', 'get_facultades')->middleware('auth','admin')->name('admin.getfacultades');
    //** Ruta para cargar vista con los roles */
    Route::get('/home/roles', 'roles_view')->middleware('auth','admin')->name('admin.roles');
    //** Ruta para mostrar todos los roles */
    Route::get('/home/getroles', 'get_roles')->middleware('auth','admin')->name('admin.getroles');

    //** Ruta para inactivar Rol */
    Route::post('/home/inactivarRol', 'inactivar_rol')->middleware('auth')->name('rol.inactivar');
    //** Ruta para activar Rol */
    Route::post('/home/activarRol', 'activar_rol')->middleware('auth')->name('rol.activar');
    //* Ruta para actualizar rol */
    Route::post('/home/updateRol', 'update_rol')->middleware('auth')->name('rol.update');
    /** Ruta para crear Rol */
    Route::post('/home/crearRol', 'crear_rol')->middleware('auth')->name('rol.crear');

    /** Ruta para traer los programas */
    Route::post('/home/programas', 'traerprogramas')->name('traer.programas');
    /** Ruta para traer programas en la vista Usuarios */
    Route::post('/home/programasUsuarios', 'traerProgramasUsuarios')->name('traer.programas.usuarios');

    Route::get('/home/solicitudes', 'solicitudesSistema')->middleware('auth')->name('solicitudes.sistema');

    Route::get('/home/ultimoIngreso', 'ultimoIngreso')->middleware('auth')->name('ultimo.ingreso');

    Route::get('/home/traerSolicitudes', 'traerSolicitudes')->middleware('auth')->name('traer.solicitudes');

    Route::get('/home/ultimoIngresoUsuarios', 'ultimoIngresoUsuarios')->middleware('auth')->name('ultimo.ingreso.usuarios');

    Route::post('/home/resolverSoliciud', 'resolverSoliciud')->middleware('auth')->name('solicitud.resuelta');

    Route::post('/home/verificarPendientes', 'verificarPendientes')->middleware('auth')->name('verificar.pendientes');

    Route::post('/home/programasBuscador', 'buscarProgramas')->middleware('auth')->name('programas.buscador');

    Route::post('/home/renovarpassword', 'renovarPassword')->middleware('auth')->name('renovar.password');
});

Route::controller(InformeMafiController::class)->group(function () {
    /** Ruta para traer los periodos activos */
    Route::post('/home/periodos', 'periodosActivos')->name('periodos.activos');

    /** Ruta para cargar gráfica de estudiantes activos e inactivos */
    Route::post('/home/estudiantes', 'estudiantesActivosGeneral')->middleware('auth')->name('estudiantes.activos');
    /** Ruta para cargar gráfica de el sello financiero de los estudiantes */
    Route::post('/home/estudiantesActivos/{tabla}', 'selloEstudiantesActivos')->middleware('auth')->name('sello.activos');
    /** Ruta para cargar gráfica de estudiantes activos con retenciòn */
    Route::post('/home/retencionActivos/{tabla}', 'estudiantesRetencion')->middleware('auth')->name('retencion.activos');
    /** Ruta para cargar gráfica de estudiantes de primer ingreso */
    Route::post('/home/estudiantesPrimerIngreso/{tabla}', 'estudiantesPrimerIngreso')->middleware('auth')->name('sello.estudiantes');
    /** Ruta para cargar gráfica de estudiantes antiguos - sello finaciero */
    Route::post('/home/estudiantesAntiguos/{tabla}', 'estudiantesAntiguos')->middleware('auth')->name('antiguos.estudiantes');
    /** Ruta para cargar gráfica de estudiantes tipos de estudiantes */
    Route::post('/home/tipoEstudiantes/{tabla}', 'tiposEstudiantes')->middleware('auth')->name('tipo.estudiantes');
    /** Ruta para cargar gráfica de los operadores que mas estudiantes traen */
    Route::post('/home/operadores/{tabla}', 'operadores')->middleware('auth')->name('operadores.estudiantes');
    /** Ruta para cargar gráfica de los programas que mas estudiantes tienen inscritos */
    Route::post('/home/estudiantesProgramas/{tabla}' ,'estudiantesProgramas')->middleware('auth')->name('programas.estudiantes');

    /** Ruta para cargas gráfica de estudiantes activos e inactivos de cada facultad */
    Route::post('/home/estudiantesFacultad', 'estudiantesActivosFacultad')->middleware('auth')->name('estudiantes.activos.facultad');
    /** Ruta para cargar gráfica de el sello financiero de los estudiantes de cada facultad */
    Route::post('/home/estudiantesSelloFacultad/{tabla}', 'selloEstudiantesFacultad')->middleware('auth')->name('estudiantes.sello.facultad');
    /** Ruta para cargar gráfica de estudiantes activos con retención de cada facultad */
    Route::post('/home/estudiantesRetencionFacultad/{tabla}','retencionEstudiantesFacultad')->middleware('auth')->name('estudiantes.retencion.facultad');
    /** Ruta para cargar gráfica de estudiantes de primer ingreso de cada facultad*/
    Route::post('/home/estudiantesPrimerIngresoFacultad/{tabla}', 'primerIngresoEstudiantesFacultad')->middleware('auth')->name('estudiantes.primerIngreso.facultad');


    /** Ruta para cargar gráfica de estudiantes de primer ingreso de cada facultad*/
    Route::post('/home/tiposEstudiantes/{tabla}', 'tiposEstudiantesFacultad')->middleware('auth')->name('estudiantes.tipo.facultad');
     /** Ruta para cargar gráfica de los operadores que mas estudiantes traen por facultad */
    Route::post('/home/operadoresFacultad/{tabla}', 'operadoresFacultad')->middleware('auth')->name('estudiantes.operador.facultad');
    /** Ruta para cargar gráfica de los programas que mas estudiantes tienen inscritos por facultad*/
    Route::post('/home/estudiantesProgramasFacultad/{tabla}' ,'estudiantesProgramasFacultad')->middleware('auth')->name('programas.estudiantes.facultad');

    /** Ruta para cargas gráfica de estudiantes activos e inactivos de cada facultad */
    Route::post('/home/estudiantesPrograma', 'estudiantesActivosPrograma')->middleware('auth')->name('estudiantes.activos.programa');
    /** Ruta para cargar gráfica de el sello financiero de los estudiantes de cada programa */
    Route::post('/home/estudiantesSelloPrograma/{tabla}', 'selloEstudiantesPrograma')->middleware('auth')->name('estudiantes.sello.programa');
    /** Ruta para cargar gráfica de estudiantes activos con retención de cada programa */
    Route::post('/home/estudiantesRetencionPrograma/{tabla}', 'retencionEstudiantesPrograma')->middleware('auth')->name('estudiantes.retencion.programa');
    /** Ruta para cargar gráfica de estudiantes de primer ingreso de cada programa*/
    Route::post('/home/estudiantesPrimerIngresoPrograma/{tabla}', 'primerIngresoEstudiantesPrograma')->middleware('auth')->name('estudiantes.primerIngreso.programa');
    /** Ruta para cargar gráfica de estudiantes antiguos - sello finaciero */
    Route::post('/home/estudiantesAntiguosFacultad/{tabla}', 'estudiantesAntiguosFacultad')->middleware('auth')->name('antiguos.estudiantes.facultad');
    /** Ruta para cargar gráfica de estudiantes antiguos - sello finaciero */
    Route::post('/home/estudiantesAntiguosPrograma/{tabla}', 'estudiantesAntiguosPrograma')->middleware('auth')->name('antiguos.estudiantes.programa');
    /** Ruta para cargar gráfica de estudiantes de primer ingreso de cada facultad*/
    Route::post('/home/tiposPrograma/{tabla}', 'tiposEstudiantesPrograma')->middleware('auth')->name('estudiantes.tipo.programa');
    /** Ruta para cargar gráfica de los operadores que mas estudiantes traen por programa */
    Route::post('/home/operadoresPrograma/{tabla}', 'operadoresPrograma')->middleware('auth')->name('estudiantes.operador.programa');

    /** Ruta para cargar gráfica de los operadores ordenados de forma descendente por Facultad*/
    Route::post('/home/operadoresFacultadTotal/{tabla}', 'operadoresFacultadTotal')->middleware('auth')->name('operadores.facultad.estudiantes');
    /** Ruta para cargar gráfica de los operadores ordenados de forma descendente */
    Route::post('/home/operadoresTotal/{tabla}', 'operadoresTotal')->middleware('auth')->name('operadoresTotal.estudiantes');
    /** Ruta para cargar gráfica de los operadores ordenados de forma descendente por Programa*/
    Route::post('/home/operadoresProgramaTotal/{tabla}', 'operadoresProgramaTotal')->middleware('auth')->name('operadores.programa.estudiantes');

    /** Ruta para cargar gráfica de los programas y la cantidad de estudiantes inscritos*/
    Route::post('/home/estudiantesProgramasTotal/{tabla}' ,'estudiantesProgramasTotal')->middleware('auth')->name('programasTotal.estudiantes');
    /** Ruta para cargar gráfica de los programas y la cantidad de estudiantes inscritos de cada facultad */
    Route::post('/home/estudiantesFacultadTotal/{tabla}' ,'estudiantesFacultadTotal')->middleware('auth')->name('FacultadTotal.estudiantes');

    /** Ruta para cargar gráfica de los operadores ordenados de forma descendente por Facultad*/
    Route::post('/home/tiposEstudiantesFacultadTotal/{tabla}', 'tiposEstudiantesFacultadTotal')->middleware('auth')->name('tiposEstudiantes.facultad.estudiantes');
    /** Ruta para cargar gráfica de los operadores ordenados de forma descendente */
    Route::post('/home/tiposEstudiantesTotal/{tabla}', 'tiposEstudiantesTotal')->middleware('auth')->name('tiposEstudiantes.total.estudiantes');
    /** Ruta para cargar gráfica de los operadores ordenados de forma descendente por Programa*/
    Route::post('/home/tiposEsudiantesProgramaTotal/{tabla}', 'tiposEstudiantesProgramaTotal')->middleware('auth')->name('tiposEstudiantes.programa.estudiantes');

    Route::get('/historial_graficos', 'historial_graficos')->middleware('auth','admin')->name('historial_graficos');

    Route::get('/home/probar', 'tablaProgramasPeriodos')->middleware('auth','admin')->name('funcion.probar');

    /** Ruta para cargar gráfico de metas */
    Route::post('/home/mafi/graficoMetasTotal', 'graficoMetasTotal')->middleware('auth')->name('metasTotal.programa');
    /** Ruta para cargar gráfico de metas por facultad*/
    Route::post('/home/mafi/graficoMetasFacultadTotal', 'graficoMetasFacultadTotal')->middleware('auth')->name('metasTotalFacultad.programa');

    Route::post('/home/mafi/graficoMetasProgramasTotal', 'graficoMetasProgramasTotal')->middleware('auth')->name('metasTotalPrograma.programa');
    /** Ruta para cargar gráfico de metas 5 mayores*/
    Route::post('/home/mafi/graficoMetas', 'graficoMetas')->middleware('auth')->name('metas.programa');
    /** Ruta para cargar gráfico de metas 5 mayores por facultad*/
    Route::post('/home/mafi/graficoMetasFacultad', 'graficoMetasFacultad')->middleware('auth')->name('metasFacultad.programa');
     /** Ruta para cargar gráfico de metas 5 mayores por programa*/
    Route::post('/home/mafi/graficoMetasPrograma', 'graficoMetasProgramas')->middleware('auth')->name('metasPrograma.programa');

    /** Ruta para cargar dataTable de programas */
    Route::post('/home/planeacion/tablaProgramas', 'tablaProgramas')->middleware('auth')->name('planeacionProgramas.tabla');

    /** Traer programas activos */
    Route::post('/home/programasAct', 'traerProgramas')->name('programasPeriodo.activos');
    /** Traer todos los programas activos */
    Route::post('/home/todosProgramasActivos', 'todosProgramas')->name('todosProgramas.activos');
    /** Data Excel Mafi */
    Route::post('/home/dataMafi', 'excelMafi')->name('data.Mafi');
    /** Data Excel Mafi por facultad*/
    Route::post('/home/dataMafiFacultad', 'excelMafiFacultad')->name('data.Mafi.facultad');
    /** Data Excel Mafi por programa*/
    Route::post('/home/dataMafiPrograma', 'excelMafiPrograma')->name('data.Mafi.programa');

    // Route::get('/home/Moodle/probar', 'tablaProgramasPeriodos')->middleware('auth')->name('moodle.probar');
     /** Ruta para traer los programas */
     Route::post('/home/programas', 'traerprogramas')->name('traer.programas.filtro');
     Route::post('/home/traercursos', 'traerCursos')->name('traer.cursos.filtro');


    Route::post('/home/estudiantesPorPrograma/{tabla}', 'estudiantesPrograma')->middleware('auth')->name('estudiantes.PorPrograma');
    Route::post('/home/estudiantesPorProgramaTotal/{tabla}', 'estudiantesPorProgramasTotal')->middleware('auth')->name('estudiantes.PorPrograma.total');

    Route::post('/home/enviarEmail', 'enviarEmail')->middleware('auth')->name('email.solicitud');

});


Route::controller(MafiController::class)->group(function () {
    //carga de mafis
    Route::get('/home/admin/mafi', 'inicioMafi')->middleware('auth','admin')->name('admin.mafi');
    Route::get('/home/admin/datamafi', 'getDataMafi')->middleware('auth','admin')->name('admin.getdatamafi');
    Route::get('/home/admin/datamafireplica', 'getDataMafiReplica')->middleware('auth','admin')->name('admin.getdatamafireplica');
    Route::get('/home/admin/periodo', 'periodo')->middleware('auth','admin')->name('admin.periodo');
    Route::get('/home/admin/Generar_faltantes', 'materiasPorVer')->middleware('auth','admin')->name('admin.Generar_faltantes');
    Route::get('/home/admin/probarfunciones', 'probarfunciones')->middleware('auth','admin')->name('admin.probarfunciones');
   Route::post('/filtros/programas', 'filtros')->name('filtros.programas');

});

/** definimos las rutas para el registro de usuarios */
Route::controller(RegistroController::class)->group(function () {
    /** esta primera es la encargada de llevarme al formulario de registro de usuarios para el aplicativo */
    Route::get('/registro', 'index')->name('registro.index');
    /** esta es para realizar el registro de  mas roles  */
    Route::get('/registro/roles', 'roles')->name('registro.roles');
    /** esta es para registrar nuevas facultades  */
    Route::post('/registro/facultades', 'facultades')->name('registro.facultades');
    /**Todas las facultades */
    Route::post('/registro/todasFacultades', 'todasFacultades')->name('registro.todas.facultades');
    /** para registrar nuevos programas */
    Route::post('/registro/programas', 'programas')->name('registro.programas');
    /** para salvar todos los registros */
    Route::post('/registro/save', 'saveRegistro')->name('registro.saveregistro');
    /** crear usuario */
    Route::post('/home/crearusuario', 'crearUsuario')->middleware('auth')->name('user.crear');
});


/*** definimos las rutas para el login */
Route::controller(LoginController::class)->group(function () {
    /** cargamosn el inicio de la app el login */
    Route::get('/login', 'index')->name('login.index');
    /** para cargar y llamar las funciones del login */
    Route::post('login/login', 'login')->name('login.login');
    /** si los datos son correctos  enviamos al home */
    Route::get('/login/home/', 'home')->middleware('auth')->name('login.home');
    /** para los cambios de contraseña */
    Route::get('/login/cambio/', 'cambio')->name('login.cambio');
    /** cargamos el formulario de cambio */
    Route::post('/login/cambiopass', 'cambioPass')->name('login.cambiopass');
    /** ruta para cerar sesion */
    Route::get('/logout', 'logout')->name('logout');
    /// para cambiar el password interno
    Route::post('/login/admin', 'cambio_Pass')->name('login_interno.cambiopass');
});

Route::controller(cambioController::class)->group(function () {
    Route::get('/cambio', 'index')->name('cambio.index');
    Route::get('/nueva/{id}', 'nueva')->name('cambio.nueva');
    Route::post('/confirmar', 'consultar')->name('cambio.consultar');
    Route::post('/confirmar/nueva', 'actualizar')->name('cambio.actualizar');
    Route::get('/home/cambiopassword/{idbanner}', 'consultaCambio')->middleware('auth')->name('cambio.cambio');
    Route::post('/home/cambiopassword/', 'cambioSave')->middleware('auth')->name('cambio.cambiosave');
});

/** Controlador para el menú desplegable de facultades */
Route::controller(facultadController::class)->group(function () {
    /** Ruta para cargar la vista de programas*/
    Route::get('/home/programas', 'view_programas')->middleware('auth','admin')->name('facultad.programas');
    /** Ruta para cargar la vista de especializaciones*/
    Route::get('/home/especializacion', 'view_especializacion')->middleware('auth','admin')->name('facultad.especializacion');
    /** Ruta para cargar la vista de maestrias*/
    Route::get('/home/maestria', 'view_maestria')->middleware('auth','admin')->name('facultad.maestria');
    /** Ruta para cargar la vista de educacion continua*/
    Route::get('/home/educacioncontinua', 'view_continua')->middleware('auth','admin')->name('facultad.continua');
    /** Ruta cargar la vista de los periodos */
    Route::get('/home/periodos', 'view_periodos')->middleware('auth','admin')->name('facultad.periodos');
    /** Ruta para visualizar todas las reglas de negocio */
    Route::get('/home/reglasdenegocio', 'view_reglas')->middleware('auth','admin')->name('facultad.reglas');
    /** Ruta para obtener todos los programas (pregrados) */
    Route::get('/home/getprogramas', 'get_programas')->middleware('auth','admin')->name('facultad.getprogramas');
    /** Ruta para obtener todos las especializaciones*/
    Route::get('/home/getespecializacion', 'get_especializacion')->middleware('auth','admin')->name('facultad.getespecializacion');
    /** Ruta para obtener todos las especializaciones maestrias */
    Route::get('/home/getmaestria', 'get_maestria')->middleware('auth','admin')->name('facultad.getmaestria');
    /** Ruta para obtener todos los programas de educación continua */
    Route::get('/home/getcontinua', 'get_continua')->middleware('auth','admin')->name('facultad.getcontinua');
    /** Ruta para obtener todos los periodos */
    Route::get('/home/getperiodos', 'get_periodos')->middleware('auth','admin')->name('facultad.getperiodos');
    /** Ruta para obtener todas las reglas de negocio */
    Route::get('/home/getreglas', 'get_reglas')->middleware('auth','admin')->name('facultad.getreglas');

    /** Ruta para ver los programas por facultad */
    Route::get('/home/facultad/{id}', 'facultad')->middleware('auth')->name('facultad.facultad');
    /** Ruta para traer los programas por facultad */
    Route::get('/home/programas/{id}', 'mostrarfacultad')->middleware('auth')->name('facultad.mostrarprogramas');

    /** Ruta para visualizar la malla curricular */
    Route::get('/home/malla/{codigo}', 'malla')->middleware('auth')->name('facultad.malla');
    /** Ruta para visualizar la malla curricular */
    Route::get('/home/getmalla/{id}', 'mostrarmallacurricular')->middleware('auth')->name('facultad.getmalla');

    //** Ruta para inactivar programa */
    Route::post('/home/inactivarprograma', 'inactivar_programa')->middleware('auth')->name('programa.inactivar');
    //** Ruta para activar programa */
    Route::post('/home/activarprograma', 'activar_programa')->middleware('auth')->name('programa.activar');
    /** Ruta para crear programa */
    Route::post('/home/crearprograma', 'crear_programa')->middleware('auth')->name('programa.crear');
    /** Ruta para actualizar programa */
    Route::post('/home/updateprograma', 'update_programa')->middleware('auth')->name('programa.update');
    /** Ruta para nombres de facultades */
    Route::get('/home/nombresfacultades', 'nombresFacultades')->middleware('auth')->name('programa.nombresfac');

    /** Ruta para visualizar los programas de la facultad del usuario */
    Route::get('/home/facultades/{nombre}', 'programasUsuario')->middleware('auth')->name('programa.usuario');
    /**Ruta para visaulizar los estudiantes de cada programa */
    Route::get('/home/facultades/estudiantes/{id}', 'estudiantesFacultad')->middleware('auth')->name('programa.estudiantes');

    /** para salvar las facultades */
    Route::post('/home/savefacultades', 'savefacultad')->middleware('auth','admin')->name('admin.guardarfacultad');

    /** para actualizar las facultades */
    Route::post('/home/updatefacultades', 'updatefacultad')->middleware('auth','admin')->name('admin.updatefacultad');
    //** Ruta para inactivar facultad*/
    Route::post('/home/inactivarfacultad', 'inactivar_facultad')->middleware('auth')->name('facultad.inactivar');
    //** Ruta para activar facultad */
    Route::post('/home/activarfacultad', 'activar_facultad')->middleware('auth')->name('facultad.activar');

    /** Ruta para crear especializacion */
    Route::post('/home/crearespecializacion', 'crear_esp')->middleware('auth')->name('especializacion.crear');
    /** Ruta para crear maestría */
    Route::post('/home/crearmaestria', 'crear_maestria')->middleware('auth')->name('maestria.crear');
    /** Ruta para crear programa de educacion continua*/
    Route::post('/home/crearcontinua', 'crear_edudacioncont')->middleware('auth')->name('continua.crear');

    /** Ruta para crear periodos */
    Route::post('/home/createperiodo', 'crear_periodo')->middleware('auth')->name('periodo.crear');
    /** Ruta para editar periodos */
    Route::post('/home/updateperiodo', 'updateperiodo')->middleware('auth')->name('periodo.update');
    //** Ruta para inactivar periodo */
    Route::post('/home/inactivarperiodo', 'inactivar_periodo')->middleware('auth')->name('periodo.inactivar');
    //** Ruta para activar periodo */
    Route::post('/home/activarperiodo', 'activar_periodo')->middleware('auth')->name('periodo.activar');

    /** Ruta para crear regla */
    Route::post('/home/createregla', 'crear_regla')->middleware('auth')->name('regla.crear');
    /** Ruta para actualizar regla */
    Route::post('/home/updateregla', 'updateregla')->middleware('auth')->name('regla.update');
    /** Ruta para inactivar regla */
    Route::post('/home/inactivarregla', 'inactivarregla')->middleware('auth')->name('regla.inactivar');
    /** Ruta para activar regla */
    Route::post('/home/activarregla', 'activarregla')->middleware('auth')->name('regla.activar');

    /** Ruta para cargar la vista de planeación*/
    Route::get('/home/planeacion', 'view_planeacion')->middleware('auth','admin')->name('planeacion.view');
    /** Ruta para visualizar la planeación de todos los programas */
    Route::post('/home/getplaneacion', 'get_planeacion')->middleware('auth')->name('programas.planeacion');
    /** Ruta para visualizar la planeación de cada programa */
    Route::get('/home/facultades/planeacion/{id}', 'planeacionPrograma')->middleware('auth')->name('planeacion.programa');

    /** Ruta para cargar la vista de planeación*/
    Route::get('/home/programasPeriodos', 'vistaProgramasPeriodos')->middleware('auth','admin')->name('programasPeriodos.view');
    Route::get('/home/getprogramasPeriodos', 'get_programasPeriodos')->middleware('auth','admin')->name('programasPeriodos.getprogramasPeriodos');
    Route::post('/home/updateprogramaPeriodo', 'update_programaPeriodo')->middleware('auth','admin')->name('programasPeriodos.update');
    Route::post('/home/agregarprogramaPeriodo', 'agregar_programaPeriodo')->middleware('auth','admin')->name('programasPeriodos.agregar');
    /** Ruta para cargar tabla programasPeriodos */
    Route::post('/home/tablaProgramasPeriodos', 'getProgramasPeriodos')->middleware('auth')->name('programasPeriodos.tabla');
    /** Ruta para cargar tabla programasPeriodos por Facultad*/
    Route::post('/home/tablaProgramasPeriodosFacultad', 'getProgramasPeriodosFacultad')->middleware('auth')->name('programasPeriodos.tabla.facultad');
    /** Ruta para inactivar periodo */
    Route::post('/home/inactivarProgramaPeriodo', 'inactivarProgramaPeriodo')->middleware('auth')->name('programasPeriodos.inactivar');
    /** Ruta para activar periodo */
    Route::post('/home/activarProgramaPeriodo', 'activarProgramaPeriodo')->middleware('auth')->name('programasPeriodos.activar');

    /** Ruta para traer periodos activos*/
    Route::post('/home/programasActivos', 'periodosActivos')->name('programas.activos');
    /** Ruta para traer periodos activos*/
    //Route::post('/home/programasActivos', 'periodosActivos')->name('periodos.activos');
    /** Ruta para traer periodos activos de un programa */
    Route::post('/home/periodosProgramasActivos', 'periodosActivosPrograma')->name('periodosPrograma.activos');

    /** Ruta para editar los periodos activos*/
    Route::post('/home/editarProgramasPeriodos', 'actualizarProgramaPeriodo')->middleware('auth','admin')->name('programasPeriodos.actualizar');

    Route::get('/home/metasprogramas', 'vistaMetas')->middleware('auth','admin')->name('metas.view');

    Route::get('/home/traermetasprogramas', 'traerMetasActivas')->middleware('auth','admin')->name('metas.activas');

    Route::post('/home/traertodosprogramas', 'traerTodosProgramas')->middleware('auth','admin')->name('get.todos.programas');

    Route::post('/home/periodosactivosciclo1', 'periodosActivosCiclo1')->middleware('auth','admin')->name('get.periodos.ciclo1');

    Route::post('/home/crearmeta', 'crearMeta')->middleware('auth','admin')->name('meta.crear');

    Route::post('/home/editarmeta', 'updateMeta')->middleware('auth','admin')->name('meta.actualizar');

});

Route::controller(EstudianteController::class)->group(function(){
    Route::get('/historialestudiante','inicio')->name('historial.inicio');
    Route::post('/historialestudiante/consulta','consultaEstudiante')->name('historial.consulta');
    Route::post('/historialestudiante/consultanombre','consultaNombre')->name('historial.consultanombre');
    Route::post('/historialestudiante/consultamalla','consultaMalla')->name('historial.consultamalla');
    Route::post('/historialestudiante/consultaHistorial','consultaHistorial')->name('historial.consultaHistorial');
    Route::post('/historialestudiante/consultaprogramacion','consultaProgramacion')->name('historial.consultaprogramacion');
    Route::post('/historialestudiante/consultaporver','consultaPorVer')->name('historial.consultaporver');
    Route::post('/historialestudiante/consultaprogramas','consultaProgramas')->name('historial.consultaprogramas');
    Route::post('/historialestudiante/countsemestres','countSemestres')->name('historial.countsemestres');
    Route::post('/historialestudiante/consultamalla','consultarMalla')->name('historial.consultamalla');
    Route::post('/historialestudiante/consultareglas','consultarReglas')->name('historial.reglas');
});

Route::controller(AlertasTempranasController::class)->group(function(){
    Route::get('/alertastempranas','index')->middleware('auth')->name('alertas.inicio');

    Route::get('/alertastempranas/rector','vistaRectorVicerector')->middleware('auth')->name('alertas.inicio.rector');
    Route::get('/alertastempranas/decano','vistaRectorDecano')->middleware('auth')->name('alertas.inicio.decano');
    Route::get('/alertastempranas/coordinador','vistaCoordinadorLider')->middleware('auth')->name('alertas.inicio.coordinador');
    Route::get('/alertastempranas/transversal','vistaTransversal')->middleware('auth')->name('alertas.inicio.transversal');
    Route::post('/alertastempranas/tablaAlertasP','tablaAlertasP')->middleware('auth')->name('alertas.tabla.programa');
    Route::post('/alertastempranas/tablaAlertasFacultad','tablaAlertasFacultad')->middleware('auth')->name('alertas.tabla.facultad');
    Route::post('/alertastempranas/tablaAlertas','tablaAlertas')->middleware('auth')->name('alertas.tabla');

    Route::post('/alertastempranas/graficoAlertas','graficaAlertas')->middleware('auth')->name('alertas.grafico');
    Route::post('/alertastempranas/graficoAlertasFacultad','graficaAlertasFacultad')->middleware('auth')->name('alertas.grafico.facultad');
    Route::post('/alertastempranas/graficoAlertasPrograma','graficaAlertasProgramas')->middleware('auth')->name('alertas.grafico.programa');

    Route::get('/alertastempranas/numeroalertas','numeroAlertas')->middleware('auth')->name('alertas.notificaciones');
    Route::get('/alertastempranas/numeroalertasfacultad','numeroAlertasFacultad')->middleware('auth')->name('alertas.notificacionesfacultad');
    Route::get('/alertastempranas/numeroalertasprograma','numeroAlertasPrograma')->middleware('auth')->name('alertas.notificacionesprograma');
    Route::get('/alertastempranas/numeroalertascurso','numeroAlertasCurso')->middleware('auth')->name('alertas.notificacionescurso');

    Route::post('/alertastempranas/tiposAlertas','tiposAlertas')->middleware('auth')->name('tipos.alertas');

    Route::post('/alertastempranas/alertaResulta','inactivarAlerta')->middleware('auth')->name('alerta.resuelta');
});

Route::controller(VistasTransversalesController::class)->group(function(){

    // Traer programas del usuario
    Route::post('/Moodle/programasCurso','programas')->name('programas.curso');

    // Traer periodos activos
    Route::post('/Moodle/periodosActivosCursos/{tabla}','periodosActivosCursos')->name('periodos.activos.cursos');

    //Estudiantes en riesgo
    Route::post('/Moodle/riesgoCursos','riesgoCursos')->name('moodle.riesgo.cursos');

    Route::post('/Moodle/descargarriesgocursos','descargarTodoEstudiantesRiesgoCurso')->name('descarga.moodle.riesgo.cursos');

    // Route::post('/Moodle/descargarriesgocursosflash','descargarTodoEstudiantesRiesgoCursoFlash')->name('descarga.moodle.riesgo.cursos');

    // Estudiantes según tipo de riesgo
    Route::post('/home/Moodle/estudiantesCurso/{riesgo}', 'estudiantesRiesgoCurso')->middleware('auth')->name('moodle.estudiantes.curso');
     // Estudiantes según tipo de riesgo



    // Tabla cursos
    Route::post('/home/Moodle/tablaCursosVista', 'tablaCursos')->middleware('auth')->name('moodle.tabla.cursos');

    Route::post('/home/Moodle/tablaCursosVistaCerrados', 'tablaCursoscerrados')->middleware('auth')->name('moodle.tabla.cursos.cerrados');

    // Data alumno
    Route::post('/home/Moodle/dataAlumnoCurso', 'dataAlumnoCurso')->middleware('auth')->name('data.alumno.cursos');

    // Graficos de riesgo
    Route::post('/home/Moodle/riesgoAsistenciaCurso', 'riesgoAsistenciaCurso')->middleware('auth')->name('riesgo.asistencia.cursos');

    Route::post('/home/estudiantesActivosTransversal', 'estudiantesActivos')->middleware('auth')->name('estudiantes.activos.transversal');

    // Sello financiero
    Route::post('/home/selloEstudiantesCursos', 'selloEstudiantesCursos')->middleware('auth')->name('estudiantes.sello.curso');

    Route::post('/home/selloRetencion', 'retencionEstudiantesCursos')->middleware('auth')->name('estudiantes.retencion.curso');

    Route::post('/home/primerIngresoCursos', 'primerIngresoCursos')->middleware('auth')->name('primerIngreso.curso');

    Route::post('/home/estudiantesAntiguosCursos', 'estudiantesAntiguosCursos')->middleware('auth')->name('estudiantesAntiguos.curso');

    Route::post('/home/tiposEstudiantesCursos', 'tiposEstudiantesCursos')->middleware('auth')->name('tipos.estudiantes.curso');

    Route::post('/home/tiposEstudiantesCursosTotal', 'tiposEstudiantesCursosTotal')->middleware('auth')->name('tipos.estudiantes.curso.total');

    Route::post('/home/operadoresCursos', 'operadoresCursos')->middleware('auth')->name('operadores.curso');

    Route::post('/home/operadoresCursosTotal', 'operadoresCursosTotal')->middleware('auth')->name('operadores.curso.total');

    Route::post('/home/estudiantesProgramasCursos', 'estudiantesProgramasCursos')->middleware('auth')->name('estudiantes.programas.curso');

    Route::post('/home/estudiantesProgramasCursosTotal', 'estudiantesProgramasCursosTotal')->middleware('auth')->name('estudiantes.programas.curso.total');

    Route::post('/home/tablaProgramasCursos', 'tablaProgramasCursos')->middleware('auth')->name('tabla.programas.curso');

    Route::post('/home/mallaProgramaCurso', 'mallaProgramaCurso')->middleware('auth')->name('malla.programas.curso');

    Route::post('/home/estudiantesMateriaCurso', 'estudiantesMateriaCurso')->middleware('auth')->name('estudiantes.materia.curso');

    Route::post('/home/datosEstudianteCurso', 'datosEstudianteCurso')->middleware('auth')->name('datos.estudiantes.curso');

    Route::post('/home/sellocursos', 'selloMoodleCurso')->middleware('auth')->name('sello.moodle.cursos');

    Route::post('/home/sellocursosestudiantes', 'selloMoodleCursoEstudiantes')->middleware('auth')->name('sello.estudiantes.cursos');

    Route::post('/home/operadorescursosmoodle', 'operadoresMoodleCurso')->middleware('auth')->name('operadores.moodle.cursos');

    Route::post('/home/operadorescursosmoodleestudiantes', 'operadoresMoodleCursoEstudiantes')->middleware('auth')->name('operadores.estudiantes.cursos');

    Route::post('/home/tipoestudiantescursos', 'tiposEstudiantesMoodleCurso')->middleware('auth')->name('tipos.moodle.cursos');

    Route::post('/home/tipoestudiantecursosestudiantes', 'tiposEstudianteMoodleCursoEstudiantes')->middleware('auth')->name('tipos.estudiantes.cursos');

    Route::post('/home/Moodle/riesgoacademicoestudiantes', 'riesgoEstudiantes')->middleware('auth')->name('riesgo.academico.cursos');

    Route::post('/prueba/moodle/tablariesgoacademicoestudiantes/{riesgo}','tablaEstudiantesRiesgo')->middleware('auth')->name('tabla.riesgo.estudiantes.cursos');

    Route::post('/prueba/moodle/tablariesgoacademicoestudiantescerrados/{riesgo}','tablaEstudiantesRiesgoCerrado')->middleware('auth')->name('tabla.riesgo.estudiantes.cursos.cerrados');

    Route::post('/prueba/moodle/descargarriesgoacademicocursos','descargarInformeRiesgoAcademico')->middleware('auth')->name('descargar.riesgo.cursos');

    Route::post('cierre/matriculascursos','matriculasCursos')->middleware('auth')->name('cierre.matricula.cursos');

    Route::post('/Moodle/riesgoestudiantescerradocursos','riesgoEstudiantesCerradoCursos')->middleware('auth')->name('cierre.matricula.cursos');

    Route::post('/home/Moodlecerrado/estudiantescursos/{riesgo}', 'estudiantesRiesgoCursocerrado')->middleware('auth')->name('moodle.estudiantes.cerrado');
});

Route::controller(RegistrosMoodleController::class)->group(function(){

    Route::get('/crearRegistros','crearRegistroMoodle')->name('crear.registros.moodle');

    Route::get('/verificarRegistros','verificacionRiesgoAcademico')->name('verificar.registros.moodle');

    // Route::get('/pruebaUpdate','crearRegistro')->name('verificar.registros.moodle');

});

Route::controller(InformeMoodleMejoradoController::class)->group(function(){

    // Prueba función Riesgo
    Route::post('/prueba/moodle/riesgo','riesgo')->middleware('auth')->name('prueba.riesgo');
    Route::post('/RiesgoMoocs','riesgoMoocs')->middleware('auth')->name('riesgo.mocs');


    Route::post('/prueba/moodle/tablaRiesgo/{riesgo}','estudiantesRiesgo')->middleware('auth')->name('prueba.tablariesgo');

    Route::post('/prueba/Moodle/datosEstudiante', 'dataAlumno')->middleware('auth')->name('prueba.moodle.data');

    Route::post('/prueba/Moodle/riesgoAsistencia', 'riesgoAsistencia')->name('prueba.moodle.riesgo.asistencia');

    Route::post('/prueba/Moodle/tablaCursos', 'tablaCursos')->name('prueba.tabla.cursos');

    Route::post('/prueba/Moodle/tablaCursos/cerrados', 'tablaCursoscerrados')->name('prueba.tabla.cursoscerrados');

    Route::get('/prueba/moodle','vistaPrueba')->middleware('auth')->name('prueba.moodle');

    Route::post('/prueba/moodle/descargardatos', 'descargarTodoEstudiantesRiesgo')->middleware('auth')->name('descargar.ausentismo.moodle');

    Route::post('/prueba/moodle/descargardatosflash', 'descargarInformeFlash')->middleware('auth')->name('descargar.ausentismo.moodle.flash');

    Route::post('/prueba/Moodle/descargardatacurso', 'descargarDatosCurso')->name('descargar.datos.curso');

    Route::post('/prueba/Moodle/sellomoodle', 'selloMoodle')->name('sello.moodle');

    Route::post('/prueba/Moodle/sellomoodleestudiantes', 'selloMoodleEstudiantes')->name('sello.moodle.estudiantes');

    Route::post('/prueba/Moodle/operadoresmoodle', 'operadoresMoodle')->name('operador.moodle');

    Route::post('/prueba/Moodle/operadoresmoodleestudiantes', 'operadoresMoodleEstudiantes')->name('operador.moodle.estudiantes');

    Route::post('/prueba/Moodle/tiposestudiantesmoodle', 'tiposEstudiantesMoodle')->name('tipoestudiantes.moodle');

    Route::post('/prueba/Moodle/tiposestudiantesmoodleestudiantes', 'tiposEstudiantesMoodleEstudiantes')->name('tipoestudiantes.moodle.estudiantes');

    Route::post('/prueba/Moodle/riesgoEstudiantes', 'riesgoEstudiantes')->name('riesgo.estudiantes');

    Route::post('/prueba/moodle/tablaRiesgoEstudiantes/{riesgo}','tablaEstudiantesRiesgo')->middleware('auth')->name('tabla.riesgo.estudiantes');

    Route::post('Estudiantescerrados/{riesgo}','tablaEstudiantescerrados')->middleware('auth')->name('tabla.cerrados.estudiantes');

    Route::get('/prueba/moodle/llenartabla','llenartabla')->middleware('auth')->name('llenar.tabla');

    Route::get('/prueba/moodle/llenartablaprogramas','llenartablaprogramas')->middleware('auth')->name('llenar.tabla.programas');


    Route::post('/prueba/moodle/descargarriesgoacademico','descargarInformeRiesgoAcademico')->middleware('auth')->name('descargar.riesgo.academico');
    Route::post('/Moodle/riesgoEstudiantescerrado', 'riesgoEstudiantescerrado')->name('riesgo.estudiantes.cerrado');
    Route::post('cierre/matriculas','matriculas')->middleware('auth')->name('cierre.matricula');
    Route::post('cierre/estudiantes','academico')->middleware('auth')->name('cierre.academico');
    Route::post('cierre/cursos','cursos')->middleware('auth')->name('cierre.cursos');
    Route::post('/home/Moodlecerrado/estudiantes/{riesgo}', 'estudiantesRiesgoCerrado')->middleware('auth')->name('moodle.estudiantes.cerrado');



    Route::get('estudiantes/duplicados','duplicados')->middleware('auth')->name('duplicados.sistema');
    Route::get('estudiantes/data','getduplicados')->middleware('auth')->name('aula.getduplicados');


});

Route::controller(InformePlaneacionController::class)->group(function(){
    Route::post('/planeacion/estudiantesactivos','selloEstudiantesActivos')->middleware('auth')->name('sello.planeacion');

    Route::post('/planeacion/estudiantesretencion','estudiantesRetencion')->middleware('auth')->name('retencion.planeacion');

    Route::post('/planeacion/estudiantesprimeringreso','estudiantesPrimerIngreso')->middleware('auth')->name('primeringreso.planeacion');

    Route::post('/planeacion/estudiantesantiguos','estudiantesAntiguos')->middleware('auth')->name('antiguos.planeacion');

    Route::post('/planeacion/tiposestudiantes','tiposEstudiantes')->middleware('auth')->name('tiposestudiantes.planeacion');

    Route::post('/planeacion/operadores','operadores')->middleware('auth')->name('operadores.planeacion');

    Route::post('/planeacion/estudiantesprograma','estudiantesPorPrograma')->middleware('auth')->name('estudiantesprograma.planeacion');

    Route::post('/planeacion/tiposestudiantestotal','tiposEstudiantesTotal')->middleware('auth')->name('tiposestudiantes.planeacion.total');

    Route::post('/planeacion/operadorestotal','operadoresTotal')->middleware('auth')->name('operadores.planeacion.total');

    Route::post('/planeacion/estudiantesprogramatotal','estudiantesPorProgramaTotal')->middleware('auth')->name('estudiantesprograma.planeacion.total');

    Route::post('/planeacion/mallaCurricular', 'mallaPrograma')->middleware('auth')->name('mallaPrograma.tabla');

    Route::post('/planeacion/estudiantesMateria', 'estudiantesMateria')->middleware('auth')->name('estudiantesMateria.tabla');

    Route::post('/planeacion/datosEstudiante', 'datosEstudiante')->middleware('auth')->name('datos.estudiante.planeacion');

    Route::post('/planeacion/materiasEstudiante', 'buscarEstudiante')->name('materias.estudiante');

    Route::post('/planeacion/tablafacultad', 'tablaProgramasFacultad')->name('tabla.planeacion.facultad');

    Route::post('/planeacion/tablaprogramas', 'tablaProgramasP')->name('tabla.planeacion.programas');
});


/**Controlador de prueba para filtros, hacerle mejoras y correcciones  */
Route::controller(filtrosController::class)->group(function(){
    Route::get('/home/filtrosprueba', 'render')->middleware('auth')->name('filtros.vista');
    Route::post('/home/filtrosprueba/getFacultades', 'getFacultades')->middleware('auth')->name('filtros.getfacultades');
});

Route::controller(PlaneacionDocentesController::class)->group(function(){
    Route::get('/home/planeaciondocentes', 'planeacionDocentesView')->middleware('auth')->name('home.planeacion.docentes');
    Route::post('/tabladocentesdisponibles', 'tablaDocentesDisponibles')->middleware('auth')->name('tabla.docentes.disponibles');
    Route::post('/filtrosprogramafacultad', 'filtrosProgramaFacultad')->middleware('auth')->name('filtros.programas.facultad');
    Route::post('/filtroschangefacultad', 'filtrosChangeFacultad')->middleware('auth')->name('filtros.change.facultad');
    Route::post('/updateasignaturasdocente', 'updateAsignaturasDocente')->middleware('auth')->name('update.asignaturas.docente');
    Route::post('/updatepreferenciasdocente', 'updatePreferenciaDocente')->middleware('auth')->name('update.preferencias.docente');
    Route::post('/updatecupodisponibledocente', 'updateCupoDisponibleDocente')->middleware('auth')->name('update.cupo.disponible.docente');
    Route::post('/inhabilitardocente', 'inhabilitarDocente')->middleware('auth')->name('inhabilitar.docente');
    Route::post('/removerasignaturadocente', 'removerAsignaturaDocente')->middleware('auth')->name('remover.asignatura.docente');
    Route::post('/traermallaprograma', 'traerMallaPrograma')->middleware('auth')->name('traer.malla.programa');
    Route::post('/traermallatransversal', 'mallaTransversal')->middleware('auth')->name('traer.malla.transversal');
    Route::post('/creardocente', 'crearDocente')->middleware('auth')->name('crear.docente');

    Route::post('/cursosfacultad', 'cursosFacultad')->middleware('auth')->name('cursos.transversal');
    Route::post('/tabladocentestransversalesdisponibles', 'tablaDocentesTransversalesDisponibles')->middleware('auth')->name('tabla.docentes.transversales.disponibles');

    Route::post('/tablaplaneaciondisponibles', 'tablaPlaneacionDocentes')->middleware('auth')->name('tabla.planeacion.docentes');
    Route::post('/tablaasignaturaspendientes', 'tablaAsignaturasPendientes')->middleware('auth')->name('tabla.asignaturas.pendientes');
    Route::post('/tabladocentesasignados', 'tablaDocentesAsignados')->middleware('auth')->name('tabla.docentes.asisgnados');

});
