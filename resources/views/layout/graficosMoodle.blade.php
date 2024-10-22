<style>
    #tablaCursos, #tablaCursosMoocs {
        overflow-x: auto;
        table-layout: auto; 
        width: 100%; 
    }
</style>

<div class="row justify-content-center mt-3">
    <div class="col-11">
        <div class="card">
            <div class="card-header text-center">
                <ul class="nav nav-tabs card-header-tabs">

                    <li class="nav-item">
                        <a class="nav-link  menuMoodle active" id="navausentismoMoocs" href="#ausentismoMoocs">Ausentismo MOOCS</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link  menuMoodle" id="navausentismocursos" href="#ausentismocursos">Ausentismo Cursos Activos</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menuMoodle" id="navcursosmoocs" href="#cursosmoocs">Cursos activos MOOCS</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menuMoodle" id="navcursos" href="#cursos">Cursos activos</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link menuMoodle" id="navestudiantes" href="#estudiantes">Análisis de estudiantes</a>
                    </li>

                </ul>
            </div>
            <div class="card-body">

                <div id="ausentismoMoocs" class="content">

                    <!-- Sección de análisis de ausentismo para estudiantes matriculados en cursos de ingreso temprano -->
                    <div class="row mt-12 mb-12">
                        <div class="col-12 text-center">
                            <h4><strong>Análisis de ausentismo para cada estudiante matriculado en cursos de estrategia de ingreso temprano</strong></h4>
                            <br>
                        </div>
                
                        <div class="col-5 text-right">
                            <!-- Formulario para búsqueda de estudiantes -->
                            <form class="form-inline" id="formBuscar">
                                @csrf
                                <div class="form-group mx-sm-3 mb-2">
                                    <input type="text" class="form-control" placeholder="Buscar estudiante" id="idBannerAusentismo">
                                </div>
                                <button type="submit" class="btn botonModal mb-2 botonBuscador" data-id="idBannerAusentismo">Buscar</button>
                            </form>
                        </div>
                    </div>
                
                    <div class="container">
                        <!-- Carrusel de matrículas para MOOCS -->
                        <div id="carruselMatriculasMoocs" class="carousel slide carousel-fade" data-bs-interval="false">
                            <div class="carousel-inner">
                
                                <!-- Primera gráfica: Inactivos MOOCS -->
                                <div class="carousel-item active">
                                    <div class="row justify-content-center mt-3 columnas">
                                        <div class="col-4 text-center" id="colRiesgoInactivos">
                                            <div class="card shadow mb-4 graficosRiesgo">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip" title="¿Qué estás visualizando en este momento?" data-placement="right">
                                                                <button type="button" id="tuBotonID" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom" data-bs-toggle="collapse" data-bs-target="#totalIngreso" aria-expanded="false" aria-controls="totalIngreso">
                                                                    <i class="fa-solid fa-arrow-down"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                        <div class="col-8 justify-content-center">
                                                            <p><strong>Inactivos</strong></p>
                                                        </div>
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip" title="Esta gráfica muestra el total de inscripciones en cursos asociadas a estudiantes inactivos en Banner." data-placement="right">
                                                                <button type="button" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom">
                                                                    <i class="fa-solid fa-circle-question"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div id="totalIngresoMoocs" class="collapse mt-3">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoInactivo"><strong>Inactivos</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body center-chart fondocharts" style="position: relative;">
                                                    <!-- Sección de gráfica para Inactivos MOOCS -->
                                                    <canvas id="InactivosMoocsCanvas"></canvas>
                                                    <div>
                                                        <div class="custom-text totalMatriculasMoocs"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    <button class="btn botonInfoMoodle filtro-moocs" data-value="INACTIVOS" style="margin-right: 10px;" data-toggle="modal" data-target="#modalInformacionMoodle">Filtros</button>
                                                    <a class="btn botonModal botonVerMasMoocs" data-value="INACTIVOS">Reporte</a>
                                                </div>
                                            </div>
                                        </div>
                
                                        <!-- Gráfica: Sin ingreso MOOCS -->
                                        <div class="col-4 text-center" id="colRiesgoIngresoMoocs">
                                            <div class="card shadow mb-4 graficosRiesgo">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip" title="¿Qué estás visualizando en este momento?" data-placement="right">
                                                                <button type="button" id="tuBotonID" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom" data-bs-toggle="collapse" data-bs-target="#totalIngreso" aria-expanded="false" aria-controls="totalIngreso">
                                                                    <i class="fa-solid fa-arrow-down"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                        <div class="col-8 justify-content-center">
                                                            <p><strong>Sin ingreso</strong></p>
                                                        </div>
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip" title="Esta gráfica muestra el total de inscripciones en cursos sin ingreso a la plataforma." data-placement="right">
                                                                <button type="button" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom">
                                                                    <i class="fa-solid fa-circle-question"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div id="totalIngresoMoocs" class="collapse mt-3">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoIngreso"><strong>Sin ingreso</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body center-chart fondocharts" style="position: relative;">
                                                    <!-- Sección de gráfica para Sin ingreso MOOCS -->
                                                    <canvas id="sinIngresoMoocsCanvas"></canvas>
                                                    <div>
                                                        <div class="custom-text totalMatriculasMoocs"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    <button class="btn botonInfoMoodle filtro-moocs" data-value="INGRESO" style="margin-right: 10px;" data-toggle="modal" data-target="#modalInformacionMoodle">Filtros</button>
                                                    <a class="btn botonModal botonVerMasMoocs" data-value="INGRESO">Reporte</a>
                                                </div>
                                            </div>
                                        </div>
                
                                        <!-- Gráfica: Riesgo alto MOOCS -->
                                        <div class="col-4 text-center" id="colRiesgoAltoMoocs">
                                            <div class="card shadow mb-4 graficosRiesgo">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip" title="¿Qué estás visualizando en este momento?" data-placement="right">
                                                                <button type="button" id="tuBotonID" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom" data-bs-toggle="collapse" data-bs-target="#totalAlto" aria-expanded="false" aria-controls="totalAlto">
                                                                    <i class="fa-solid fa-arrow-down"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                        <div class="col-8 d-flex align-items-center justify-content-center">
                                                            <p><strong>Riesgo alto</strong></p>
                                                        </div>
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip" title="Esta gráfica muestra el total de inscripciones en cursos sin ingreso en los últimos 8 días." data-placement="right">
                                                                <button type="button" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom">
                                                                    <i class="fa-solid fa-circle-question"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div id="totalAltoMoocs" class="collapse mt-3">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoAlto"><strong>Riesgo alto</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body center-chart fondocharts" style="position: relative;">
                                                    <!-- Sección de gráfica para Riesgo alto MOOCS -->
                                                    <canvas id="altoMoocsCanvas"></canvas>
                                                    <div>
                                                        <div class="custom-text totalMatriculasMoocs"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    <button class="btn botonInfoMoodle filtro-moocs" data-value="ALTO" style="margin-right: 10px;" data-toggle="modal" data-target="#modalInformacionMoodle">Filtros</button>
                                                    <a class="btn botonModal botonVerMasMoocs" data-value="ALTO">Reporte</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                
                                <!-- Segunda gráfica: Riesgo medio y bajo -->
                                <div class="carousel-item">
                                    <div class="row justify-content-center mt-3 columnas">
                
                                        <!-- Gráfica: Riesgo medio MOOCS -->
                                        <div class="col-4 text-center" id="colRiesgoMedioMoocs">
                                            <div class="card shadow mb-4 graficosRiesgo">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip" title="¿Qué estás visualizando en este momento?" data-placement="right">
                                                                <button type="button" id="tuBotonID" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom" data-bs-toggle="collapse" data-bs-target="#totalMedio" aria-expanded="false" aria-controls="totalMedio">
                                                                    <i class="fa-solid fa-arrow-down"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                        <div class="col-8 d-flex align-items-center justify-content-center">
                                                            <p><strong>Riesgo medio</strong></p>
                                                        </div>
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip" title="Esta gráfica muestra el total de inscripciones en cursos cuyo último ingreso fue entre 4 y 8 días." data-placement="right">
                                                                <button type="button" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom">
                                                                    <i class="fa-solid fa-circle-question"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div id="totalMedioMoocs" class="collapse mt-3">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoMedio"><strong>Riesgo medio</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body center-chart fondocharts">
                                                    <!-- Sección de gráfica para Riesgo medio MOOCS -->
                                                    <canvas id="medioMoocsCanvas"></canvas>
                                                    <div>
                                                        <div class="custom-text totalMatriculasMoocs"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    <button class="btn botonInfoMoodle filtro-moocs" data-value="MEDIO" style="margin-right: 10px;" data-toggle="modal" data-target="#modalInformacionMoodle">Filtros</button>
                                                    <a class="btn botonModal botonVerMasMoocs" data-value="MEDIO">Reporte</a>
                                                </div>
                                            </div>
                                        </div>
                
                                        <!-- Gráfica: Riesgo bajo MOOCS -->
                                        <div class="col-4 text-center" id="colRiesgoBajoMoocs">
                                            <div class="card shadow mb-4 graficosRiesgo">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip" title="¿Qué estás visualizando en este momento?" data-placement="right">
                                                                <button type="button" id="tuBotonID" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom" data-bs-toggle="collapse" data-bs-target="#totalbajo" aria-expanded="false" aria-controls="totalbajo">
                                                                    <i class="fa-solid fa-arrow-down"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                        <div class="col-8 d-flex align-items-center justify-content-center">
                                                            <p><strong>Riesgo bajo</strong></p>
                                                        </div>
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip" title="Esta gráfica muestra el total de inscripciones en cursos cuyo último ingreso fue en los últimos 4 días." data-placement="right">
                                                                <button type="button" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom">
                                                                    <i class="fa-solid fa-circle-question"></i>
                                                                </button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div id="totalbajoMoocs" class="collapse">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoBajo"><strong>Riesgo bajo</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body center-chart fondocharts">
                                                    <!-- Sección de gráfica para Riesgo bajo MOOCS -->
                                                    <canvas id="bajoMoocsCanvas"></canvas>
                                                    <div>
                                                        <div class="custom-text totalMatriculasMoocs"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    <button class="btn botonInfoMoodle filtro-moocs" data-value="BAJO" style="margin-right: 10px;" data-toggle="modal" data-target="#modalInformacionMoodle">Filtros</button>
                                                    <a class="btn botonModal botonVerMasMoocs" data-value="BAJO">Reporte</a>
                                                </div>
                                            </div>
                                        </div>
                
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Botones de control del carrusel -->
                            <button class="carousel-control-prev custom-carousel-control" type="button" data-bs-target="#carruselMatriculasMoocs" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden"></span>
                            </button>
                
                            <button class="carousel-control-next custom-carousel-control display-flex align-content-center" type="button" data-bs-target="#carruselMatriculasMoocs" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden"></span>
                            </button>
                        </div>
                    </div>
                
                    <!-- Botones para descargar informes -->
                    <div class="row text-center justify-content-center mt-3">
                        <button class="btn botonDescargarInforme mx-2 descargar-todo" data-descarga="moocs">Descargar informe de Ausentismo</button>
                        <button class="btn botonDescargarInforme mx-2" id="descargarTodoFlashMoocs" data-descarga="moocs">Descargar informe versión corta</button>
                    </div>
                
                    <!-- Tabla de datos -->
                    <div class="card shadow mt-4 hidden" id="colTablaMoocs">
                        <div class="card-body">
                            <div class="table">
                                <table id="datatableMoocs" class="display" style="width:100%"></table>
                            </div>
                        </div>
                        <br>
                    </div>
                
                </div>
                
                <div id="ausentismocursos" class="content">
                    {{-- buscador  estudiante --}}
                    <div class="row mt-12 mb-12">
                        <div class="col-12 text-center">
                            <h4><strong> Análisis de ausentismo para cada estudiante matriculado en cada curso incluyendo MOOCS</strong></h4>
                            {{-- <a class="btn botonDescargarInforme mx-2" style="width: auto;" href="https://moocs.ibero.edu.co/hermes/front/public/assets/documentos/Notas-26Feby24Abr-17Jun.xlsx" class=" btn button-informe" type="button" >
                                Descargar informe de
                                Notas cursos Cerrados febrero 16 semanas , abril de 8 semanas
                            </a> --}}

                            <br>
                        </div>
                       
                        <div class="col-5 text-right">
                            <form class="form-inline" id="formBuscar">
                                @csrf
                                <div class="form-group mx-sm-3 mb-2">
                                    <input type="text" class="form-control" placeholder="Buscar estudiante"
                                        id="idBannerAusentismo">
                                </div>
                                <button type="submit" class="btn botonModal mb-2 botonBuscador"
                                    data-id="idBannerAusentismo">Buscar</button>
                            </form>
                        </div>
                    </div>

                    <div class="container">

                        {{-- carrusel de matriculas --}}
                        <div id="carruselMatriculas" class="carousel slide carousel-fade"  data-bs-interval="false">
                            <div class="carousel-inner">
                                {{-- PRIMERAS 3 GRAFICAS INACTIVOS, SIN INGRESO, RIESGO ALTO --}}
                                <div class="carousel-item active">
                                    <div class="row justify-content-center mt-3 columnas">

                                        {{-- inactivos moocs --}}
                                        <div class="col-4 text-center " id="colRiesgoInactivos">
                                            <div class="card shadow mb-4 graficosRiesgo">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip" title="¿Que estás visualizando en este momento?"
                                                                data-placement="right">
                                                                <button type="button" id="tuBotonID" class="btn"
                                                                    style="background-color: #dfc14e; border-color: #dfc14e; color: white;"
                                                                    data-toggle="tooltip" data-placement="bottom"
                                                                    data-bs-toggle="collapse" data-bs-target="#totalIngreso"
                                                                    aria-expanded="false" aria-controls="totalIngreso">
                                                                    <i class="fa-solid fa-arrow-down"></i></button>
                                                            </span>
                                                        </div>
                                                        <div class="col-8 justify-content-center">
                                                            <p><strong>Inactivos</strong></p>
                                                        </div>
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="En esta gráfica se muestra el total de inscripciones en cursos asociadas a estudiantes inactivos en Banner."
                                                                data-placement="right">
                                                                <button type="button" class="btn"
                                                                    style="background-color: #dfc14e;border-color: #dfc14e; color:white;"
                                                                    data-toggle="tooltip" data-placement="bottom"><i
                                                                        class="fa-solid fa-circle-question"></i></button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div id="totalIngresoMoocs" class="collapse mt-3">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoInactivo"><strong>Inactivos</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>
                
                                                </div>
                                                <div class="card-body center-chart fondocharts" style="position: relative;">
                                                    <canvas id="Inactivos"></canvas>
                                                    <div>
                                                        <div class="custom-text totalMatriculas"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    <button class="btn botonInfoMoodle" data-value="INACTIVOS" style="margin-right: 10px;"
                                                        data-toggle="modal" data-target="#modalInformacionMoodle"> Filtros </button>
                                                    <a class="btn botonModal botonVerMas" data-value="INACTIVOS"> Reporte </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 text-center " id="colRiesgoIngreso">
                                            <div class="card shadow mb-4 graficosRiesgo">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip" title="¿Que estás visualizando en este momento?"
                                                                data-placement="right">
                                                                <button type="button" id="tuBotonID" class="btn"
                                                                    style="background-color: #dfc14e; border-color: #dfc14e; color: white;"
                                                                    data-toggle="tooltip" data-placement="bottom"
                                                                    data-bs-toggle="collapse" data-bs-target="#totalIngreso"
                                                                    aria-expanded="false" aria-controls="totalIngreso">
                                                                    <i class="fa-solid fa-arrow-down"></i></button>
                                                            </span>
                                                        </div>
                                                        <div class="col-8 justify-content-center">
                                                            <p><strong>Sin ingreso</strong></p>
                                                        </div>
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="En esta gráfica se muestra el total de inscripciones en cursos sin ingreso a la plataforma."
                                                                data-placement="right">
                                                                <button type="button" class="btn"
                                                                    style="background-color: #dfc14e;border-color: #dfc14e;; color:white;"
                                                                    data-toggle="tooltip" data-placement="bottom"><i
                                                                        class="fa-solid fa-circle-question"></i></button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div id="totalIngresoMoocs" class="collapse mt-3">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoIngreso"><strong>Sin ingreso</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>
                
                                                </div>
                                                <div class="card-body center-chart fondocharts" style="position: relative;">
                                                    <canvas id="sinIngreso"></canvas>
                                                    <div>
                                                        <div class="custom-text totalMatriculas"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    <button class="btn botonInfoMoodle" data-value="INGRESO" style="margin-right: 10px;"
                                                        data-toggle="modal" data-target="#modalInformacionMoodle"> Filtros </button>
                                                    <a class="btn botonModal botonVerMas" data-value="INGRESO"> Reporte </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 text-center " id="colRiesgoAlto">
                                            <div class="card shadow mb-4 graficosRiesgo">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip" title="¿Que estás visualizando en este momento?"
                                                                data-placement="right">
                                                                <button type="button" id="tuBotonID" class="btn"
                                                                    style="background-color: #dfc14e; border-color: #dfc14e; color: white;"
                                                                    data-toggle="tooltip" data-placement="bottom"
                                                                    data-bs-toggle="collapse" data-bs-target="#totalAlto"
                                                                    aria-expanded="false" aria-controls="totalAlto">
                                                                    <i class="fa-solid fa-arrow-down"></i></button>
                                                            </span>
                                                        </div>
                                                        <div class="col-8 d-flex align-items-center justify-content-center">
                                                            <p><strong>Riesgo alto</strong></p>
                                                        </div>
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="En esta gráfica se muestra el total de inscripciones en cursos sin ingreso en los últimos 8 días."
                                                                data-placement="right">
                                                                <button type="button" class="btn"
                                                                    style="background-color: #dfc14e;border-color: #dfc14e;; color:white;"
                                                                    data-toggle="tooltip" data-placement="bottom"><i
                                                                        class="fa-solid fa-circle-question"></i></button>
                                                            </span>
                                                        </div>
                
                                                    </div>
                                                    <div id="totalAltoMoocs" class="collapse mt-3">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoAlto"><strong>Riesgo alto</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body center-chart fondocharts" style="position: relative;">
                                                    <canvas id="alto"></canvas>
                                                    <div>
                                                        <div class="custom-text totalMatriculas"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    <button class="btn botonInfoMoodle" data-value="ALTO" style="margin-right: 10px;"
                                                        data-toggle="modal" data-target="#modalInformacionMoodle"> Filtros </button>
                                                    <a class="btn botonModal botonVerMas" data-value="ALTO"> Reporte </a>
                                                </div>
                                            </div>
                                        </div>         
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="row justify-content-center mt-3 columnas">
                                        <div class="col-4 text-center " id="colRiesgoMedio">
                                            <div class="card shadow mb-4 graficosRiesgo">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="¿Que estás visualizando en este momento?"
                                                                data-placement="right">
                                                                <button type="button" id="tuBotonID" class="btn"
                                                                    style="background-color: #dfc14e; border-color: #dfc14e; color: white;"
                                                                    data-toggle="tooltip" data-placement="bottom"
                                                                    data-bs-toggle="collapse" data-bs-target="#totalMedio"
                                                                    aria-expanded="false" aria-controls="totalMedio">
                                                                    <i class="fa-solid fa-arrow-down"></i></button>
                                                            </span>
                                                        </div>
                                                        <div class="col-8 d-flex align-items-center justify-content-center">
                                                            <p><strong>Riesgo medio</strong></p>
                                                        </div>
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="En esta gráfica se muestra el total de inscripciones en cursos cuyo último ingreso fue entre 4 y 8 días."
                                                                data-placement="right">
                                                                <button type="button" class="btn"
                                                                    style="background-color: #dfc14e;border-color: #dfc14e;; color:white;"
                                                                    data-toggle="tooltip" data-placement="bottom"><i
                                                                        class="fa-solid fa-circle-question"></i></button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div id="totalMedio" class="collapse mt-3">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoMedio"><strong>Riesgo medio</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body center-chart fondocharts">
                                                    <canvas id="medio"></canvas>
                                                    <div>
                                                        <div class="custom-text totalMatriculas"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    <button class="btn botonInfoMoodle" data-value="MEDIO"
                                                        style="margin-right: 10px;" data-toggle="modal"
                                                        data-target="#modalInformacionMoodle"> Filtros </button>
                                                    <a class="btn botonModal botonVerMas" data-value="MEDIO"> Reporte
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 text-center " id="colRiesgoBajo">
                                            <div class="card shadow mb-4 graficosRiesgo">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="¿Que estás visualizando en este momento?"
                                                                data-placement="right">
                                                                <button type="button" id="tuBotonID" class="btn"
                                                                    style="background-color: #dfc14e; border-color: #dfc14e; color: white;"
                                                                    data-toggle="tooltip" data-placement="bottom"
                                                                    data-bs-toggle="collapse" data-bs-target="#totalbajo"
                                                                    aria-expanded="false" aria-controls="totalbajo">
                                                                    <i class="fa-solid fa-arrow-down"></i></button>
                                                            </span>
                                                        </div>
                                                        <div class="col-8 d-flex align-items-center justify-content-center">
                                                            <p><strong>Riesgo bajo</strong></p>
                                                        </div>
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="En esta gráfica se muestra el total de inscripciones en cursos cuyo último ingreso fue en los últimos 4 días"
                                                                data-placement="right">
                                                                <button type="button" class="btn"
                                                                    style="background-color: #dfc14e;border-color: #dfc14e; color:white;"
                                                                    data-toggle="tooltip" data-placement="bottom"><i
                                                                        class="fa-solid fa-circle-question"></i></button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div id="totalbajo" class="collapse">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoBajo"><strong>Riesgo bajo</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body center-chart fondocharts">
                                                    <canvas id="bajo"></canvas>
                                                    <div>
                                                        <div class="custom-text totalMatriculas"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    <button class="btn botonInfoMoodle" data-value="BAJO" style="margin-right: 10px;"
                                                        data-toggle="modal" data-target="#modalInformacionMoodle"> Filtros </button>
                                                    <a class="btn botonModal botonVerMas" data-value="BAJO"> Reporte
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="carousel-control-prev custom-carousel-control" type="button"
                                data-bs-target="#carruselMatriculas" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden"></span>
                            </button>

                            <!-- Botón de control Siguiente -->
                            <button
                                class="carousel-control-next custom-carousel-control display-flex align-content-center"
                                type="button" data-bs-target="#carruselMatriculas" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden"></span>
                            </button>
                        </div>
                    </div>
                    

                    <div class="row text-center justify-content-center mt-3">
                        <button class="btn botonDescargarInforme mx-2 descargar-todo" data-descarga="cursos">Descargar informe de
                            Ausentismo</button>
                        <button class="btn botonDescargarInforme mx-2 descargarTodoFlash">Descargar informe versión
                            corta</button>
                    </div>

                    <div class="card shadow mt-4 hidden" id="colTabla">
                        <!-- Card Body -->
                        <div class="card-body">
                            <!--Datatable-->
                            <div class="table">
                                <table id="datatableCursos" class="display" style="width:100%">
                                </table>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>

                <div id="cursosmoocs" class="content" style="font-size: 12px;">
                    <div class="">
                        <table id="tablaCursosMoocs" class="display" style="width:100%">
                        </table>
                    </div>
                </div>

                <div id="cursos" class="content" style="font-size: 12px;">
                    <div class="">
                        <br>
                        <table id="tablaCursos" class="display" style="width:100%">
                        </table>
                    </div>
                </div>

                <div id="estudiantes" class="content" style="font-size: 12px;">
                    <div class="row mt-4 mb-4">
                        <div class="col-7 text-center">
                            <h4><strong>Análisis de estudiantes por riesgo académico </strong></h4>
                            <h5><b>NOTA:</b> En este análisis solo se incluyen estudiantes  de los periodos activos en plataforma, excluyendo los de ingreso temprano.</h5>
                        </div>
                        <div class="col-5 text-right">
                            <form class="form-inline" id="formBuscar">
                                @csrf
                                <div class="form-group mx-sm-3 mb-2">
                                    <input type="text" class="form-control" placeholder="Buscar estudiante"
                                        id="idBannerEstudiantes">
                                </div>
                                <button type="submit" class="btn botonModal mb-2 botonBuscador"
                                    data-id="idBannerEstudiantes">Buscar</button>
                            </form>
                        </div>
                    </div>
                    <div class="container">
                        <div id="carrusel" class="carousel slide" data-bs-interval="false">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <div class="row justify-content-center mt-3 columnas">
                                    <div class="col-4 text-center " id="colRiesgoInactivoEstudiantes">
                                            <div class="card shadow mb-4 graficosRiesgo">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="¿Que estás visualizando en este momento?"
                                                                data-placement="right">
                                                                <button type="button" id="tuBotonID" class="btn"
                                                                    style="background-color: #dfc14e; border-color: #dfc14e; color: white;"
                                                                    data-toggle="tooltip" data-placement="bottom"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#totalbajoEstudiantes"
                                                                    aria-expanded="false"
                                                                    aria-controls="totalbajoEstudiantes">
                                                                    <i class="fa-solid fa-arrow-down"></i></button>
                                                            </span>
                                                        </div>
                                                        <div
                                                            class="col-8 d-flex align-items-center justify-content-center">
                                                            <p><strong>Riesgo Inactivos</strong></p>
                                                        </div>
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="Estudiantes con estado inactivo en Banner y cursos matriculados en plataforma."
                                                                data-placement="right">
                                                                <button type="button" class="btn"
                                                                    style="background-color: #dfc14e;border-color: #dfc14e; color:white;"
                                                                    data-toggle="tooltip" data-placement="bottom"><i
                                                                        class="fa-solid fa-circle-question"></i></button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div id="totalinactivoEstudiantes" class="collapse">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoInactivoEstudiantes"><strong> Inactivos </strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body center-chart fondocharts">
                                                    <canvas id="inactivoEstudiantes"></canvas>
                                                    <div>
                                                        <div class="custom-text totalEstudiantes"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    <button class="btn botonInfoMoodle" data-value="inactivo"
                                                        style="margin-right: 10px;" data-toggle="modal"
                                                        data-target="#modalInformacionMoodle"> Filtros </button>
                                                    <a class="btn botonModal botonVerMasEstudiantes"
                                                        data-value="inactivo"> Reporte
                                                    </a>
                                                </div>
                                            </div>
                                        </div>    
                                    <div class="col-4 text-center" id="colRiesgoIngresoEstudiantes">
                                            <div class="card shadow mb-4 graficosRiesgo">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="¿Que estás visualizando en este momento?"
                                                                data-placement="right">
                                                                <button type="button" id="tuBotonID" class="btn"
                                                                    style="background-color: #dfc14e; border-color: #dfc14e; color: white;"
                                                                    data-toggle="tooltip" data-placement="bottom"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#totalIngresoEstudiantes"
                                                                    aria-expanded="false"
                                                                    aria-controls="totalIngresoEstudiantes">
                                                                    <i class="fa-solid fa-arrow-down"></i></button>
                                                            </span>
                                                        </div>
                                                        <div
                                                            class="col-8 d-flex align-items-center justify-content-center">
                                                            <p><strong>Sin ingreso</strong></p>
                                                        </div>
                                                        <div class="col-2 ">
                                                            <span data-toggle="tooltip"
                                                                title="Estudiantes sin ingreso a la plataforma posterior a la fecha de inicio de los cursos activos."
                                                                data-placement="right">
                                                                <button type="button" class="btn"
                                                                    style="background-color: #dfc14e;border-color: #dfc14e;; color:white;"
                                                                    data-toggle="tooltip" data-placement="bottom"><i
                                                                        class="fa-solid fa-circle-question"></i></button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div id="totalIngresoEstudiantes" class="collapse mt-3">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoIngresoEstudiantes"><strong>Sin
                                                                    ingreso</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="card-body center-chart fondocharts"
                                                    style="position: relative;">
                                                    <canvas id="sinIngresoEstudiantes"></canvas>
                                                    <div>
                                                        <div class="custom-text totalEstudiantes"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    <button class="btn botonInfoMoodle" data-value="Sin ingreso"
                                                        style="margin-right: 10px;" data-toggle="modal"
                                                        data-target="#modalInformacionMoodle"> Filtros </button>
                                                    <a class="btn botonModal botonVerMasEstudiantes"
                                                        data-value="Sin ingreso"> Reporte </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 text-center " id="colRiesgoCriticoEstudiantes">
                                            <div class="card shadow mb-4 graficosRiesgo">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="¿Que estás visualizando en este momento?"
                                                                data-placement="right">
                                                                <button type="button" id="tuBotonID" class="btn"
                                                                    style="background-color: #dfc14e; border-color: #dfc14e; color: white;"
                                                                    data-toggle="tooltip" data-placement="bottom"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#totalcriticoEstudiantes"
                                                                    aria-expanded="false"
                                                                    aria-controls="totalcriticoEstudiantes">
                                                                    <i class="fa-solid fa-arrow-down"></i></button>
                                                            </span>
                                                        </div>
                                                        <div
                                                            class="col-8 d-flex align-items-center justify-content-center">
                                                            <p><strong>Riesgo crítico</strong></p>
                                                        </div>
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="En esta gráfica se muestra el total de estudiantes cuyo promedio proyectado por Hermes de los cursos activos está entre 0 - 1.2 para pregrados y 0 - 1.3 para posgrados."
                                                                data-placement="right">
                                                                <button type="button" class="btn"
                                                                    style="background-color: #dfc14e;border-color: #dfc14e; color:white;"
                                                                    data-toggle="tooltip" data-placement="bottom"><i
                                                                        class="fa-solid fa-circle-question"></i></button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div id="totalcriticoEstudiantes" class="collapse">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoCriticoEstudiantes"><strong>Riesgo
                                                                    crítico</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body center-chart fondocharts">
                                                    <canvas id="criticoEstudiantes"></canvas>
                                                    <div>
                                                        <div class="custom-text totalEstudiantes"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    <button class="btn botonInfoMoodle" data-value="critico"
                                                        style="margin-right: 10px;" data-toggle="modal"
                                                        data-target="#modalInformacionMoodle"> Filtros </button>
                                                    <a class="btn botonModal botonVerMasEstudiantes"
                                                        data-value="critico"> Reporte
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                <div class="carousel-item">
                                    <div class="row justify-content-center mt-3 columnas">
                                        <div class="col-4 text-center" id="colRiesgoAltoEstudiantes">
                                            <div class="card shadow mb-4 graficosRiesgo">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="¿Que estás visualizando en este momento?"
                                                                data-placement="right">
                                                                <button type="button" id="tuBotonID" class="btn"
                                                                    style="background-color: #dfc14e; border-color: #dfc14e; color: white;"
                                                                    data-toggle="tooltip" data-placement="bottom"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#totalAltoEstudiantes"
                                                                    aria-expanded="false"
                                                                    aria-controls="totalAltoEstudiantes">
                                                                    <i class="fa-solid fa-arrow-down"></i></button>
                                                            </span>
                                                        </div>
                                                        <div
                                                            class="col-8 d-flex align-items-center justify-content-center">
                                                            <p><strong>Riesgo alto</strong></p>
                                                        </div>
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="En esta gráfica se muestra el total de estudiantes cuyo promedio proyectado por Hermes de los cursos activos está entre 1.3 - 2.4 para pregrados y 1.4 - 2.7 para posgrados."
                                                                data-placement="right">
                                                                <button type="button" class="btn"
                                                                    style="background-color: #dfc14e;border-color: #dfc14e;; color:white;"
                                                                    data-toggle="tooltip" data-placement="bottom"><i
                                                                        class="fa-solid fa-circle-question"></i></button>
                                                            </span>
                                                        </div>

                                                    </div>
                                                    <div id="totalAltoEstudiantes" class="collapse mt-3">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoAltoEstudiantes"><strong>Riesgo
                                                                    alto</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body center-chart fondocharts"
                                                    style="position: relative;">
                                                    <canvas id="altoEstudiantes"></canvas>
                                                    <div>
                                                        <div class="custom-text totalEstudiantes"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    <button class="btn botonInfoMoodle" data-value="alto"
                                                        style="margin-right: 10px;" data-toggle="modal"
                                                        data-target="#modalInformacionMoodle"> Filtros </button>
                                                    <a class="btn botonModal botonVerMasEstudiantes"
                                                        data-value="alto"> Reporte </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 text-center" id="colRiesgoMedioEstudiantes">
                                            <div class="card shadow mb-4 graficosRiesgo">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="¿Que estás visualizando en este momento?"
                                                                data-placement="right">
                                                                <button type="button" id="tuBotonID" class="btn"
                                                                    style="background-color: #dfc14e; border-color: #dfc14e; color: white;"
                                                                    data-toggle="tooltip" data-placement="bottom"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#totalMedioEstudiantes"
                                                                    aria-expanded="false"
                                                                    aria-controls="totalMedioEstudiantes">
                                                                    <i class="fa-solid fa-arrow-down"></i></button>
                                                            </span>
                                                        </div>
                                                        <div
                                                            class="col-8 d-flex align-items-center justify-content-center">
                                                            <p><strong>Riesgo medio</strong></p>
                                                        </div>
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="En esta gráfica se muestra el total de estudiantes cuyo promedio proyectado por Hermes de los cursos activos está entre 2.5 - 3.5 para pregrados y 2.8 - 3.9 para posgrados."
                                                                data-placement="right">
                                                                <button type="button" class="btn"
                                                                    style="background-color: #dfc14e;border-color: #dfc14e;; color:white;"
                                                                    data-toggle="tooltip" data-placement="bottom"><i
                                                                        class="fa-solid fa-circle-question"></i></button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div id="totalMedioEstudiantes" class="collapse mt-3">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoMedioEstudiantes"><strong>Riesgo
                                                                    medio</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body center-chart fondocharts">
                                                    <canvas id="medioEstudiantes"></canvas>
                                                    <div>
                                                        <div class="custom-text totalEstudiantes"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    <button class="btn botonInfoMoodle" data-value="medio"
                                                        style="margin-right: 10px;" data-toggle="modal"
                                                        data-target="#modalInformacionMoodle"> Filtros </button>
                                                    <a class="btn botonModal botonVerMasEstudiantes"
                                                        data-value="medio"> Reporte
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4 text-center" id="colRiesgoBajoEstudiantes">
                                            <div class="card shadow mb-4 graficosRiesgo">
                                                <div class="card-header">
                                                    <div class="row">
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="¿Que estás visualizando en este momento?"
                                                                data-placement="right">
                                                                <button type="button" id="tuBotonID" class="btn"
                                                                    style="background-color: #dfc14e; border-color: #dfc14e; color: white;"
                                                                    data-toggle="tooltip" data-placement="bottom"
                                                                    data-bs-toggle="collapse"
                                                                    data-bs-target="#totalbajoEstudiantes"
                                                                    aria-expanded="false"
                                                                    aria-controls="totalbajoEstudiantes">
                                                                    <i class="fa-solid fa-arrow-down"></i></button>
                                                            </span>
                                                        </div>
                                                        <div
                                                            class="col-8 d-flex align-items-center justify-content-center">
                                                            <p><strong>Riesgo bajo</strong></p>
                                                        </div>
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="En esta gráfica se muestra el total de estudiantes cuyo promedio proyectado por Hermes de los cursos activos es mayor a 3.5 para pregrados y mayor a 4.0 para posgrados."
                                                                data-placement="right">
                                                                <button type="button" class="btn"
                                                                    style="background-color: #dfc14e;border-color: #dfc14e;; color:white;"
                                                                    data-toggle="tooltip" data-placement="bottom"><i
                                                                        class="fa-solid fa-circle-question"></i></button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div id="totalbajoEstudiantes" class="collapse">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoBajoEstudiantes"><strong>Riesgo
                                                                    bajo</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body center-chart fondocharts">
                                                    <canvas id="bajoEstudiantes"></canvas>
                                                    <div>
                                                        <div class="custom-text totalEstudiantes"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    <button class="btn botonInfoMoodle" data-value="bajo"
                                                        style="margin-right: 10px;" data-toggle="modal"
                                                        data-target="#modalInformacionMoodle"> Filtros </button>
                                                    <a class="btn botonModal botonVerMasEstudiantes"
                                                        data-value="bajo"> Reporte
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button class="carousel-control-prev custom-carousel-control" type="button"
                                data-bs-target="#carrusel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden"></span>
                            </button>

                            <!-- Botón de control Siguiente -->
                            <button
                                class="carousel-control-next custom-carousel-control display-flex align-content-center"
                                type="button" data-bs-target="#carrusel" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden"></span>
                            </button>
                        </div>
                    </div>
                    <div class="row text-center justify-content-center mt-3">
                        <button class="btn botonDescargarInforme mx-2 descargarTodoFlash">Descargar informe de
                            matrículas</button>
                        <button class="btn botonDescargarInforme" id="descargarInformeAcademico">Descargar informe de
                            riesgo academico</button>
                    </div>

                    <div class="card shadow mt-4 hidden" id="colTablaEstudiantes">
                        <!-- Card Body -->
                        <div class="card-body">
                            <!--Datatable-->
                            <div class="tableEstudiantes">
                                <table id="datatableEstudiantes" class="display" style="width:100%">
                                </table>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
