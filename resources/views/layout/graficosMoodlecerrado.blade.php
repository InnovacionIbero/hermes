<style>
    #tablaCursos {
        overflow-x: auto;
        white-space: nowrap;
    }
</style>

<div class="row justify-content-center mt-3">
    <div class="col-11">
        <div class="card">
            <div class="card-header text-center">
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active menuMoodle" id="navausentismo" href="#ausentismo">Análisis
                            de Matriculas Cerradas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menuMoodle" id="navcursos" href="#cursos">Análisis de cursos Cerrados</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menuMoodle" id="navestudiantes" href="#estudiantes">Análisis de Cierre por estudiante</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div id="ausentismo" class="content">

                    <div class="row mt-12 mb-12">
                        <div class="col-12 text-center">
                            <h4><strong> Análisis de matriculas cerradas </strong></h4>
                                                      <br>
                                                    
                        </div>
                      
                        {{-- <div class="col-5 text-right">
                            <form class="form-inline" id="formBuscar">
                                @csrf
                                <div class="form-group mx-sm-3 mb-2">
                                    <input type="text" class="form-control" placeholder="Buscar estudiante"
                                        id="idBannerAusentismo">
                                </div>
                                <button type="submit" class="btn botonModal mb-2 botonBuscador"
                                    data-id="idBannerAusentismo">Buscar</button>
                            </form>
                        </div> --}}
                    </div>

                    <div class="container">
                        <div id="carruselMatriculas" class="carousel slide carousel-fade"  data-bs-interval="false">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <div class="row justify-content-center mt-3 columnas">
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
                                                            <p><strong>Sin Actividad</strong></p>
                                                        </div>
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="En esta gráfica se muestra el total de inscripciones en cursos asociadas a estudiantes inactivos en Banner."
                                                                data-placement="right">
                                                                <button type="button" class="btn"
                                                                    style="background-color: #dfc14e;border-color: #dfc14e;; color:white;"
                                                                    data-toggle="tooltip" data-placement="bottom"><i
                                                                        class="fa-solid fa-circle-question"></i></button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div id="totalIngreso" class="collapse mt-3">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoInactivo"><strong>Sin Actividad</strong></h6>
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
                                                    {{-- <button class="btn botonInfoMoodle" data-value="SinActividad" style="margin-right: 10px;"
                                                        data-toggle="modal" data-target="#modalInformacionMoodle"> Filtros </button> --}}
                                                    <a class="btn botonModal botonVerMas" data-value="SinActividad"> Reporte </a>
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
                                                            <p><strong>Perdida crítica</strong></p>
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
                                                    <div id="totalAlto" class="collapse mt-3">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoAlto"><strong>Perdida crítica</strong></h6>
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
                                                    {{-- <button class="btn botonInfoMoodle" data-value="ALTO" style="margin-right: 10px;"
                                                        data-toggle="modal" data-target="#modalInformacionMoodle"> Filtros </button> --}}
                                                    <a class="btn botonModal botonVerMas" data-value="critico"> Reporte </a>
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
                                                            <p><strong>Perdida</strong></p>
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
                                                            <h6 id="tituloRiesgoMedio"><strong>Perdida</strong></h6>
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
                                                    {{-- <button class="btn botonInfoMoodle" data-value="MEDIO"
                                                        style="margin-right: 10px;" data-toggle="modal"
                                                        data-target="#modalInformacionMoodle"> Filtros </button> --}}
                                                    <a class="btn botonModal botonVerMas" data-value="Perdida"> Reporte
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
                                                            <p><strong>Aprobado</strong></p>
                                                        </div>
                                                        <div class="col-2">
                                                            <span data-toggle="tooltip"
                                                                title="En esta gráfica se muestra el total de inscripciones en cursos cuyo último ingreso fue en los últimos 4 días"
                                                                data-placement="right">
                                                                <button type="button" class="btn"
                                                                    style="background-color: #dfc14e;border-color: #dfc14e;; color:white;"
                                                                    data-toggle="tooltip" data-placement="bottom"><i
                                                                        class="fa-solid fa-circle-question"></i></button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div id="totalbajo" class="collapse">
                                                        <div class="card card-body titulos">
                                                            <h6 id="tituloRiesgoBajo"><strong>Aprobado</strong></h6>
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
                                                    {{-- <button class="btn botonInfoMoodle" data-value="BAJO" style="margin-right: 10px;"
                                                        data-toggle="modal" data-target="#modalInformacionMoodle"> Filtros </button> --}}
                                                    <a class="btn botonModal botonVerMas" data-value="Aprobado"> Reporte
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
                    
                    <div class="row">
                        <div class="col-12 d-flex justify-content-center mt-3">
                            <a style="width: max-content;" class="btn botonModal botonVerMas" data-value="todo"> ver todo el Reporte </a>
                        </div>
                    </div>

                    <div class="card shadow mt-4 hidden" id="colTabla">
                        <!-- Card Body -->
                        <div class="card-body">
                            <!--Datatable-->
                            <div class="table">
                                <table id="datatable" class="display table-striped table-hover" style="width:100%">
                                </table>
                            </div>
                        </div>
                        <br>
                    </div>


                </div>

                <div id="cursos" class="content" style="font-size: 12px;">
                    <div class="col-12 text-center">
                        <h4><strong>Análisis de Cursos Cerrados</strong><br><br></h4>
                        
                    </div>
                    <div class="">
                      
                        <table id="tablaCursos" class="display table-striped table-hover" style="width:100%">
                        </table>
                    </div>
                </div>

                <div id="estudiantes" class="content" style="font-size: 12px;">
                    <div class="row mt-4 mb-4">
                        {{-- <div class="col-7 text-center"> --}}
                            <div class="col-12 text-center">
                            <h4><strong>Análisis de estudiantes por riesgo académico cierre pereriodo </strong></h4>
                            <h5><b>NOTA:</b> En este análisis solo se incluyen estudiantes  de los periodos cerrados en plataforma, excluyendo los de ingreso temprano.</h5>
                        </div>
                        {{-- <div class="col-5 text-right">
                            <form class="form-inline" id="formBuscar">
                                @csrf
                                <div class="form-group mx-sm-3 mb-2">
                                    <input type="text" class="form-control" placeholder="Buscar estudiante"
                                        id="idBannerEstudiantes">
                                </div>
                                <button type="submit" class="btn botonModal mb-2 botonBuscador"
                                    data-id="idBannerEstudiantes">Buscar</button>
                            </form>
                        </div> --}}
                    </div>
                    <div class="container">
                        <div id="carrusel" class="carousel slide" data-bs-interval="false">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <div class="row justify-content-center mt-3 columnas">

                                       {{-- sin actividad --}}
                                        <div class="col-4 text-center " id="colRiesgoAltoEstudiantes">
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
                                                            <p><strong>Sin Actividad</strong></p>
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
                                                            <h6 id="tituloRiesgoAltoEstudiantes"><strong>Sin Actividad</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body center-chart fondocharts"
                                                    style="position: relative;">
                                                    <canvas id="sinIngreso"></canvas>
                                                    <div>
                                                        <div class="custom-text totalEstudiantes"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    {{-- <button class="btn botonInfoMoodle" data-value="alto"
                                                        style="margin-right: 10px;" data-toggle="modal"
                                                        data-target="#modalInformacionMoodle"> Filtros </button> --}}
                                                    <a class="btn botonModal botonVerMasEstudiantes"
                                                        data-value="sin actividad"> Reporte </a>
                                                </div>
                                            </div>
                                        </div>
                                      
                                        {{-- perdida critica--}}
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
                                                            <p><strong>Perdida crítica</strong></p>
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
                                                            <h6 id="tituloRiesgoCriticoEstudiantes"><strong>Perdida crítica</strong></h6>
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
                                                    {{-- <button class="btn botonInfoMoodle" data-value="critico"
                                                        style="margin-right: 10px;" data-toggle="modal"
                                                        data-target="#modalInformacionMoodle"> Filtros </button> --}}
                                                    <a class="btn botonModal botonVerMasEstudiantes"
                                                        data-value="Perdida crítica"> Reporte
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    
                                </div>
                                <div class="carousel-item">
                                    <div class="row justify-content-center mt-3 columnas">

                                       
                                         {{-- perdida  --}}
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
                                                            <p><strong>Perdida</strong></p>
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
                                                            <h6 id="tituloRiesgoIngresoEstudiantes"><strong>Perdida</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div class="card-body center-chart fondocharts"
                                                    style="position: relative;">
                                                    <canvas id="perdida"></canvas>
                                                    <div>
                                                        <div class="custom-text totalEstudiantes"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    {{-- <button class="btn botonInfoMoodle" data-value="Sin ingreso"
                                                        style="margin-right: 10px;" data-toggle="modal"
                                                        data-target="#modalInformacionMoodle"> Filtros </button> --}}
                                                    <a class="btn botonModal botonVerMasEstudiantes"
                                                        data-value="perdida"> Reporte </a>
                                                </div>
                                            </div>
                                        </div>
                                       

                                        {{-- aprobo --}}
                                        <div class="col-4 text-center " id="colRiesgoBajoEstudiantes">
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
                                                            <p><strong>Aprobado</strong></p>
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
                                                            <h6 id="tituloRiesgoBajoEstudiantes"><strong>Aprobado</strong></h6>
                                                            <h6 class="tituloPeriodo"><strong></strong></h6>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-body center-chart fondocharts">
                                                    <canvas id="Aprobo"></canvas>
                                                    <div>
                                                        <div class="custom-text totalEstudiantes"></div>
                                                    </div>
                                                </div>
                                                <div class="card-footer d-flex justify-content-end">
                                                    {{-- <button class="btn botonInfoMoodle" data-value="bajo"
                                                        style="margin-right: 10px;" data-toggle="modal"
                                                        data-target="#modalInformacionMoodle"> Filtros </button> --}}
                                                    <a class="btn botonModal botonVerMasEstudiantes"
                                                        data-value="Aprobado"> Reporte
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
                    <div class="row">
                        <div class="col-12 d-flex justify-content-center mt-3">
                            <a style="width: max-content;" class="btn botonModal botonVerMasEstudiantes" data-value="todo"> ver todo el Reporte </a>
                        </div>
                    </div>
                    <div class="row text-center justify-content-center mt-3">
                       
                    </div>

                    <div class="card shadow mt-4 hidden" id="colTablaEstudiantes">
                        <!-- Card Body -->
                        <div class="card-body">
                            <!--Datatable-->
                            <div class="tableEstudiantes">
                                <table id="datatableEstudiantes" class="display table-striped table-hover " style="width:100%">
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
