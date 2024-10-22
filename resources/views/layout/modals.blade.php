    <!-- Modal Todos los Operadores de la Ibero -->
    <div class="modal fade" id="modalOperadoresTotal" tabindex="-1" role="dialog" aria-labelledby="modalOperadoresTotal" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="height:1000px;">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title" id="tituloOperadoresTotal"><strong>Estudiantes activos por operador</strong></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <canvas id="operadoresTotal"></canvas>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Todos los Programas de la Ibero -->
    <div class="modal fade" id="modalProgramasTotal" tabindex="-1" role="dialog" aria-labelledby="modalProgramasTotal" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="height:1000px;">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title" id="tituloProgramasTotal"><strong>Programas</strong></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <canvas id="programasTotal"></canvas>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Todos los Tipos de estudiantes -->
    <div class="modal fade" id="modalTiposEstudiantes" tabindex="-1" role="dialog" aria-labelledby="modalTiposEstudiantes" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="height:1000px;">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title" id="tituloTiposTotal"><strong>Tipos de estudiantes</strong></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <canvas id="tiposEstudiantesTotal"></canvas>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Metas -->
    <div class="modal fade" id="modalMetas" tabindex="-1" role="dialog" aria-labelledby="modalMetas" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="height:1000px;">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title" id="tituloMetasTotal"><strong>Metas por programa (Primer ingreso, transferentes y reingresos)</strong></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h6 id="metascumplidastotales"></h6>
                    <canvas id="metasTotal"></canvas>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" id="generarExcel">Descargar datos</button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!--Modal Datos Alumno-->
    <div class="modal fade" id="modaldataEstudiante" tabindex="-1" role="dialog"
        aria-labelledby="modaldataEstudiante" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document"
            style="height:1000px;">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title" id="tituloEstudiante"><strong></strong></h5>
                    <button type="button" id="cerrarModalx2" class="close modal_dta_estudiante"
                        data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-right mb-3">
                        <span data-toggle="tooltip" title="Desplegar" data-placement="right">
                            <button type="button" id="tuBotonID" class="btn"
                                style="background-color: #dfc14e; border-color: #dfc14e; color: white;"
                                data-toggle="tooltip" data-placement="bottom" data-bs-toggle="collapse"
                                data-bs-target=".multi-collapse" aria-expanded="false"
                                aria-controls="multi-collapse">
                                <i class="fa-solid fa-circle-arrow-down"></i>
                            </button>
                        </span>
                    </div>
                    <div class="row mb-2">
                        <div class="col-lg-4 collapse multi-collapse">
                            <div class="card mb-1">
                                <div class="card-body text-center">
                                    <p class="text-muted mb-1" id="nombreModal"></p>
                                    <p class="text-muted mb-1" id="idModal"></p>
                                    <p class="text-muted mb-1" id="facultadModal"></p>
                                    <p class="text-muted mb-1" id="programaModal"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 collapse multi-collapse">
                            <div class="card mb-8">
                                <div class="card-body text-start">
                                    <p class="text-muted mb-1" id="documentoModal"></p>
                                    <p class="text-muted mb-1" id="correoModal"></p>
                                    <p class="text-muted mb-1" id="selloModal"></p>
                                    <p class="text-muted mb-1" id="estadoModal"></p>
                                    <p class="text-muted mb-1" id="tipoModal"></p>
                                    <p class="text-muted mb-1" id="autorizadoModal"></p>
                                    <p class="text-muted mb-1" id="operadorModal"></p>
                                    <p class="text-muted mb-1" id="convenioModal"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center">
                        <table class="table" id="tablaNotas">
                            <thead>
                                <tr>
                                    <th scope="col">Curso</th>
                                    <th scope="col">Total Actividades</th>
                                    <th scope="col">Actividades por calificar</th>
                                    <th scope="col">Cuestionarios realizados</th>
                                    <th scope="col">Primer Corte</th>
                                    <th scope="col">Segundo Corte</th>
                                    <th scope="col">Tercer Corte</th>
                                    <th scope="col">Nota Acumulada</th>
                                    <th scope="col">Nota Proyectada</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <div class="row mt-3 mb-3">
                        <div class="col-lg-6 text-center">
                            <div class="row">
                                <div class="col-10">
                                    <h5 class="text-dark"><strong>Riesgo por desempeño académico</strong></h5>
                                </div>
                                <div class="col-2 text-center">
                                    <span data-toggle="tooltip"
                                        title="Este gráfico muestra el riesgo en el cual se encuentra el estudiante, teniendo en cuenta sus notas, actividades por calificar, el semestre en el que se encuentra, la duración del curso, la fecha de inicio del curso y la fecha actual. El color rojo indica riesgo alto, el amarilla riesgo medio, verde riesgo bajo y el gris indica que aún no se tienen suficientes datos para realizar el análisis que es después del primer corte"
                                        data-placement="right">
                                        <button type="button" class="btn"
                                            style="background-color: #dfc14e;border-color: #dfc14e;; color:white;"
                                            data-toggle="tooltip" data-placement="bottom"><i
                                                class="fa-solid fa-circle-question"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 text-center">
                            <div class="row">
                                <div class="col-10">
                                    <h5 class="text-dark"><strong>Riesgo por ausentismo</strong></h5>
                                </div>
                                <div class="col-2 text-center">
                                    <span data-toggle="tooltip"
                                        title="Este gráfico muestra el riesgo por ausentismo del estudiante, si tiene un ingreso inferior a 4 días tiene riesgo bajo, entre 4 días y una semana riesgo medio y más de una semana riesgo alto."
                                        data-placement="right">
                                        <button type="button" class="btn"
                                            style="background-color: #dfc14e;border-color: #dfc14e;; color:white;"
                                            data-toggle="tooltip" data-placement="bottom"><i
                                                class="fa-solid fa-circle-question"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row text-center mt-4 center-chart">
                        <div class="col-lg-6 center-chart" style="height: 500px;">
                            <canvas id="riesgoNotas"></canvas>
                        </div>
                        <div class="col-lg-6 center-chart">
                            <canvas id="riesgoIngreso" style="height: 500px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning modal_dta_estudiante" id="cerrarModal"
                        data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!--Modal historial-->
    <div class="modal fade" id="modalHistorial" tabindex="-1" role="dialog"
        aria-labelledby="modalHistorial" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document"
            style="height:90vh; width:90vw;">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title"><strong>Historial estudiante</strong></h5>
                    <button type="button" class="close modal_hist_estudiante" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tabla Malla Curricular -->
    <div class="modal fade" id="modalMallaCurricular" tabindex="-1" role="dialog" aria-labelledby="modalMallaCurricular" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="height:1000px;">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title" id="tituloMalla"><strong></strong></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-4">
                        <button class="btn btn-secondary operadorplaneacion mx-2" data-operador="">Planeación Ibero</button>
                        <button class="btn btn-secondary operadorplaneacion mx-2" data-operador="Edupol">Planeación Edupol</button>
                        <button hidden class="btn btn-secondary operadorplaneacion mx-2" id="buttonicetex" data-operador="Icetex">Planeación ICBF/ICETEX</button>
                    </div>

                    <h5 class="mt-3 mb-3">Por defecto ves la planeación para los grupos Ibero.</h5>

                    <!--Datatable-->
                    <div class="table">
                        <table id="mallaCurricular" class="display" style="width:100%">
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Estudiantes planeados -->
    <div class="modal fade" id="modalEstudiantesPlaneados" tabindex="-1" role="dialog" aria-labelledby="modalEstudiantesPlaneados" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document" style="height:1000px;">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title" id="tituloEstudiantes"><strong></strong></h5>
                    <button type="button" class="close" id="cerrarModalPlaneados"data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!--Datatable-->
                    <div class="table">
                        <table id="estudiantesPlaneados" class="display" style="width:100%">
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Buscar estudiante -->
    <div class="modal fade" id="modalBuscarEstudiante" tabindex="-1" role="dialog" aria-labelledby="modalBuscarEstudiante" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document" style="height:1000px;">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title" id="tituloBuscar"><strong>Buscar estudiante</strong></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form class="form-inline" id="formBuscar">
                        @csrf
                        <h5>Id banner del estudiante</h5>
                        <div class="form-group mx-sm-3 mb-2">
                            <label for="idBanner" class="sr-only">Id Banner</label>
                            <input type="text" class="form-control" id="idBanner" placeholder="Id Banner">
                        </div>
                        <button type="submit" class="btn botonModal mb-2" id="botonBuscador">Buscar</button>
                    </form class="mt-2">

                    <div class="hidden mt-3 mb-3" id="dataEstudiante">
                        <h5 id="primerApellido" class="text-black"></h5>
                        <h5 id="Sello" class="text-black"></h5>
                        <h5 id="Operador" class="text-black"></h5>
                        <h5 id="tipEstudiante" class="text-black"></h5>
                    </div>
                    <br>
                    <!--Datatable con id Banner del estudiante-->
                    <div class="text-center text-black hidden" id='tituloTablaBuscar'>
                        <h4>Materias planeadas</h4>
                    </div>
                    <div class="table" id="divTablaBuscador">
                        <table id="buscarEstudiante" class="display" style="width:100%">
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!--Modal Datos Alumno Planeación-->
    <div class="modal fade " id="modaldataEstudiantePlaneacion" tabindex="-1" role="dialog" aria-labelledby="modaldataEstudiantePlaneacion" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl" role="document" style="height:600px;">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h5 class="modal-title" id="tituloEstudiantePlaneacion"><strong></strong></h5>
                    <button type="button" id="cerrarModalx2" class="close modal_dta_estudiante" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mb-2">
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <p class="text-muted mb-1" id="nombreModalPlaneacion"></p>
                                    <p class="text-muted mb-1" id="idModalPlaneacion"></p>
                                    <p class="text-muted mb-1" id="facultadModalPlaneacion"></p>
                                    <p class="text-muted mb-1" id="programaModalPlaneacion"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card mb-8">
                                <div class="card-body text-start">
                                    <p class="text-muted mb-1" id="documentoModalPlaneacion"></p>
                                    <p class="text-muted mb-1" id="correoModalPlaneacion"></p>
                                    <p class="text-muted mb-1" id="selloModalPlaneacion"></p>
                                    <p class="text-muted mb-1" id="estadoModalPlaneacion"></p>
                                    <p class="text-muted mb-1" id="tipoModalPlaneacion"></p>
                                    <p class="text-muted mb-1" id="autorizadoModalPlaneacion"></p>
                                    <p class="text-muted mb-1" id="operadorModalPlaneacion"></p>
                                    <p class="text-muted mb-1" id="convenioModalPlaneacion"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3 mb-3 justify-content-around">
                        <div class= "col-5 text-center">
                            <h5><strong>Materias en Moodle</strong></h5>
                            <h6 id="totalCredMoodle"></h6>
                            <div class="card-body">
                                <!--Datatable-->
                                <div class="table">
                                    <table id="datatableMoodle" class="display" style="width:100%">
                                    </table>
                                </div>
                            </div>    
                        </div>
                        <div class="col-5 text-center">
                            <h5><strong>Materias Proyectadas</strong></h5>
                            <h6 id="totalCredPlaneacion"></h6>
                            <div class="card-body">
                                <!--Datatable-->
                                <div class="table">
                                    <table id="datatablePlaneacion" class="display" style="width:100%">
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3 mb-3 text-center">
                        <div class="col align-self-center">
                            <h5><strong>Historial Académico</strong></h5>
                            <h6 id="totalCredHistorial"></h6>
                            <div class="card-body">
                                <!--Datatable-->
                                <div class="table">
                                    <table id="datatableHistorial" class="display" style="width:100%">
                                    </table>
                                </div>
                            </div> 
                        </div> 
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-warning modal_dta_estudiante" id="cerrarModal" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

     <!--Modal Información Sello - Operador - Tipos de estudiantes en Moodle-->

     <div class="modal fade " id="modalInformacionMoodle" tabindex="-1" role="dialog" aria-labelledby="modalInformacionMoodle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document" style="height:700px;">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 id="tituloModalInfo" class="text-center text-black"><strong>Sello Financiero</strong></h4>
                    <button type="button" id="cerrarModalx2" class="close modal_dta_estudiante"
                        data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row mt-2 mb-4">
                        <label for="opcionInfo" class="col-form-label" style ="margin-right: 10px;"><h4><strong>Selecciona que deseas visualizar</strong></h4></label>
                        <select name="opcionInfo" class="form-select" id="opcionInfo">
                            <option value="Sello"><h4>Sello Financiero</h4></option>
                            <option value="Operador"><h4>Operador</h4></option>
                            <option value="Tipo"><h4>Tipos de estudiantes</h4></option>
                        </select>
                    </div>
                    <div class="row center-chart"style="height: 450px;">
                        <canvas id="datosMoodle"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
         
    <!-- Modal Slides Planeación -->
    <div class="modal" id="modalSlidePlaneacion" tabindex="-1" role="dialog" aria-labelledby="#modalSlidePlaneacion" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="height:500px;">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="text-center text-black"><strong>Actividades quinto ingreso 2024</strong></h4>
                    <button type="button" id="cerrarModalx2" class="close modal_dta_estudiante" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner text-center">
                            <div class="carousel-item active">
                                <img src="{{asset('img/P5-1.png')}}" class="d-block mx-auto" width="700px" alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="{{asset('img/P5-2.png')}}" class="d-block mx-auto" width="700px" alt="...">
                            </div>                            
                            <div class="carousel-item">
                                <img src="{{asset('img/P5-3.png')}}" class="d-block mx-auto" width="700px" alt="...">
                            </div>
                            <div class="carousel-item">
                                <img src="{{asset('img/P5-4.png')}}" class="d-block mx-auto" width="700px" alt="...">
                            </div>
                           
                        </div>
                        <!-- Botón de control Previo -->
                        <button class="carousel-control-prev custom-carousel-control" type="button"
                            data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden"></span>
                        </button>

                            <!-- Botón de control Siguiente -->
                        <button class="carousel-control-next custom-carousel-control display-flex align-content-center" type="button"
                            data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Prerrequisitos Historial -->
    <div class="modal" id="modalPrerrequisitos" tabindex="-1" role="dialog" aria-labelledby="#modalPrerrequisitos" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document" style="height:500px;">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="text-center text-black"><strong> Prerrequisitos - Equivalencias </strong></h4>
                    <button type="button" id="cerrarModalx2" class="close modal_dta_estudiante" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h4 id="tituloprerequisito"></h4>
                    <table class="table" id="prerrequisitos"></table>
                    <br>
                    <h4 id="tituloequivalencias"></h4>
                    <table class="table" id="equivalencias"></table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Reglas de negocio -->
    <div class="modal" id="modalReglas" tabindex="-1" role="dialog" aria-labelledby="#modalReglas" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg" role="document" style="height:500px;">
            <div class="modal-content">
                <div class="modal-header text-center">
                    <h4 class="text-center text-black" id="tituloreglas"><strong> Reglas programa </strong></h4>
                    <button type="button" id="cerrarModalx2" class="close modal_dta_estudiante" data-dismiss="modal"
                        aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="datareglas">

                    </div>
                </div>
            </div>
        </div>
    </div>

