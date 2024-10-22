<div class="row justify-content-start mt-5 columnas">
                <div class="col-6 text-center" id="colEstudiantes">
                    <div class="card shadow mb-5 graficos" id="chartEstudiantes">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-2 text-left">
                                    <span data-toggle="tooltip" title="¿Que estás visualizando en este momento?" data-placement="right">
                                        <button type="button" id="tuBotonID" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom" data-bs-toggle="collapse" data-bs-target="#totalEstudiantes" aria-expanded="false" aria-controls="totalEstudiantes">
                                            <i class="fa-solid fa-arrow-down"></i></button>
                                    </span>
                                </div>
                                <div class="col-8 d-flex align-items-center justify-content-center">
                                    <h5><strong>Total estudiantes Argos</strong></h5>
                                </div>
                                <div class="col-2 text-right">
                                    <span data-toggle="tooltip" title="Este gráfico muestra el total de los estudiantes y los clásifica en activos e inactivos. Adicionalmente cuenta con la opción 'Descargar datos Banner' la cual genera un Excel con los datos de Banner." data-placement="right">
                                        <button type="button" class="btn" style="background-color: #dfc14e;border-color: #dfc14e; color:white;" data-toggle="tooltip" data-placement="bottom"><i class="fa-solid fa-circle-question"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div id="totalEstudiantes" class="collapse mt-3">
                                <div class="card card-body titulos">
                                    <h6 id="tituloEstudiantes"><strong>Total estudiantes Argos</strong></h6>
                                    <h6 class="tituloPeriodo"><strong></strong></h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body center-chart fondocharts">
                            <canvas id="estudiantes"></canvas>
                        </div>
                        <div class="modal-footer">
                            <a type="button" class="btn botonMafi" id="descargarMafi">Descargar datos Banner</a>
                        </div>
                    </div>
                </div>
                <div class="col-6 text-center" id="colSelloFinanciero">
                    <div class="card shadow mb-6 graficos">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-2 text-left">
                                    <span data-toggle="tooltip" title="¿Que estás visualizando en este momento?" data-placement="right">
                                        <button type="button" id="tuBotonID" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom" data-bs-toggle="collapse" data-bs-target="#totalSello" aria-expanded="false" aria-controls="totalSello">
                                            <i class="fa-solid fa-arrow-down"></i></button>
                                    </span>
                                </div>
                                <div class="col-8 d-flex align-items-center justify-content-center">
                                    <h5><strong>Estado Financiero</strong></h5>
                                </div>
                                <div class="col-2 text-right">
                                    <span data-toggle="tooltip" title="Aquí se muestra un resumen del estado financiero (con sello, con retención o ASP) de los estudiantes activos." data-placement="right">
                                        <button type="button" class="btn" style="background-color: #dfc14e;border-color: #dfc14e;; color:white;" data-toggle="tooltip" data-placement="bottom"><i class="fa-solid fa-circle-question"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div id="totalSello" class="collapse mt-3">
                                <div class="card card-body titulos">
                                    <h6 id="tituloEstadoFinanciero"><strong>Estado Financiero</strong></h6>
                                    <h6 class="tituloPeriodo"><strong></strong></h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body center-chart fondocharts">
                            <canvas id="activos"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-6 text-center " id="colRetencion">
                    <div class="card shadow mb-6 graficos">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-2 text-left">
                                    <span data-toggle="tooltip" title="¿Que estás visualizando en este momento?" data-placement="right">
                                        <button type="button" id="tuBotonID" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom" data-bs-toggle="collapse" data-bs-target="#totalRetencion" aria-expanded="false" aria-controls="totalRetencion">
                                            <i class="fa-solid fa-arrow-down"></i></button>
                                    </span>
                                </div>
                                <div class="col-8 d-flex align-items-center justify-content-center">
                                    <h5><strong>Estado Financiero - Retención</strong></h5>
                                </div>
                                <div class="col-2 text-right">
                                    <span data-toggle="tooltip" title="Aquí se muestra un resumen del estado en plataforma de los estudiantes activos que su estado financiero se encuentra en retención." data-placement="right">
                                        <button type="button" class="btn" style="background-color: #dfc14e;border-color: #dfc14e; color:white;" data-toggle="tooltip" data-placement="bottom"><i class="fa-solid fa-circle-question"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div id="totalRetencion" class="collapse mt-3">
                                <div class="card card-body titulos">
                                    <h6 id="tituloRetencion"><strong>Estado Financiero - Retención</strong></h6>
                                    <h6 class="tituloPeriodo"><strong></strong></h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body fondocharts">
                            <canvas id="retencion"></canvas>
                        </div>
                    </div>
                </div>
                <div class=" col-6 text-center " id="colPrimerIngreso">
                    <div class="card shadow mb-6 graficos">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-2 text-left">
                                    <span data-toggle="tooltip" title="¿Que estás visualizando en este momento?" data-placement="right">
                                        <button type="button" id="tuBotonID" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom" data-bs-toggle="collapse" data-bs-target="#totalNuevos" aria-expanded="false" aria-controls="totalNuevos">
                                            <i class="fa-solid fa-arrow-down"></i></button>
                                    </span>
                                </div>
                                <div class="col-8 d-flex align-items-center justify-content-center">
                                    <h5><strong>Estudiantes nuevos - Estado Financiero</strong></h5>
                                </div>
                                <div class="col-2 text-right">
                                    <span data-toggle="tooltip" title="En este gráfico se puede visualizar el Estado financiero de todos los estudiantes activos de primer ingreso, transferentes y reingresos." data-placement="right">
                                        <button type="button" class="btn" style="background-color: #dfc14e;border-color: #dfc14e;; color:white;" data-toggle="tooltip" data-placement="bottom"><i class="fa-solid fa-circle-question"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div id="totalNuevos" class="collapse mt-3">
                                <div class="card card-body titulos">
                                    <h6 id="tituloEstudiantesNuevos"><strong>Estudiantes nuevos - Estado Financiero</strong></h6>
                                    <h6 class="tituloPeriodo"><strong></strong></h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body center-chart fondocharts">
                            <canvas id="primerIngreso"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-6 text-center " id="colAntiguos">
                    <div class="card shadow mb-6 graficos">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-2 text-left">
                                    <span data-toggle="tooltip" title="¿Que estás visualizando en este momento?" data-placement="right">
                                        <button type="button" id="tuBotonID" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom" data-bs-toggle="collapse" data-bs-target="#totalAntiguos" aria-expanded="false" aria-controls="totalAntiguos">
                                            <i class="fa-solid fa-arrow-down"></i></button>
                                    </span>
                                </div>
                                <div class="col-8 d-flex align-items-center justify-content-center">
                                    <h5><strong>Estudiantes antiguos - Estado Financiero</strong></h5>
                                </div>
                                <div class="col-2 text-right">
                                    <span data-toggle="tooltip" title="En este gráfico se puede visualizar el Estado financiero de todos los estudiantes antiguos." data-placement="right">
                                        <button type="button" class="btn" style="background-color: #dfc14e;border-color: #dfc14e;; color:white;" data-toggle="tooltip" data-placement="bottom"><i class="fa-solid fa-circle-question"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div id="totalAntiguos" class="collapse mt-3">
                                <div class="card card-body titulos">
                                    <h6 id="tituloEstudiantesAntiguos"><strong>Estudiantes antiguos - Estado Financiero</strong></h6>
                                    <h6 class="tituloPeriodo"><strong></strong></h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body center-chart fondocharts">
                            <canvas id="antiguos"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-6 text-center " id="colTipoEstudiantes">
                    <div class="card shadow mb-6 graficosBarra">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-2 text-left">
                                    <span data-toggle="tooltip" title="¿Que estás visualizando en este momento?" data-placement="right">
                                        <button type="button" id="tuBotonID" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom" data-bs-toggle="collapse" data-bs-target="#totalTipos" aria-expanded="false" aria-controls="totalTipos">
                                            <i class="fa-solid fa-arrow-down"></i></button>
                                    </span>
                                </div>
                                <div class="col-8 d-flex align-items-center justify-content-center">
                                    <h5><strong>Tipos de estudiantes activos</strong></h5>
                                </div>
                                <div class="col-2 text-right">
                                    <span data-toggle="tooltip" title="Ilustra los tipos de estudiantes activos, además cuenta la opción 'Ver más' para ampliar la cantidad de datos mostrados." data-placement="right">
                                        <button type="button" class="btn" style="background-color: #dfc14e;border-color: #dfc14e;; color:white;" data-toggle="tooltip" data-placement="bottom"><i class="fa-solid fa-circle-question"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div id="totalTipos" class="collapse mt-3">
                                <div class="card card-body titulos">
                                    <h6 id="tituloTipos"><strong>Tipos de estudiantes activos</strong></h6>
                                    <h6 class="tituloPeriodo"><strong></strong></h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body fondocharts">
                            <canvas id="tipoEstudiante"></canvas>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <a href="" id="botonModalTiposEstudiantes" class="btn botonModal" data-toggle="modal" data-target="#modalTiposEstudiantes"> Ver más </a>
                        </div>
                    </div>
                </div>
                <div class="col-6 text-center " id="colOperadores">
                    <div class="card shadow mb-6 graficosBarra">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-2 text-left">
                                    <span data-toggle="tooltip" title="¿Que estás visualizando en este momento?" data-placement="right">
                                        <button type="button" id="tuBotonID" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom" data-bs-toggle="collapse" data-bs-target="#totalOperador" aria-expanded="false" aria-controls="totalOperador">
                                            <i class="fa-solid fa-arrow-down"></i></button>
                                    </span>
                                </div>
                                <div class="col-8 d-flex align-items-center justify-content-center">
                                    <h5><strong>Estudiantes activos por operador</strong></h5>
                                </div>
                                <div class="col-2 text-right">
                                    <span data-toggle="tooltip" title="Muestra la cantidad de estudiantes inscritos por cada operador, también cuenta con la opción de 'Ver más'." data-placement="right">
                                        <button type="button" class="btn" style="background-color: #dfc14e;border-color: #dfc14e;; color:white;" data-toggle="tooltip" data-placement="bottom"><i class="fa-solid fa-circle-question"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div id="totalOperador" class="collapse mt-3">
                                <div class="card card-body titulos">
                                    <h6 id="tituloOperadores"><strong>Estudiantes activos por operador</strong></h6>
                                    <h6 class="tituloPeriodo"><strong></strong></h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body fondocharts">
                            <canvas id="operadores" style="height: 400px;"></canvas>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <a href="" id="botonModalOperador" class="btn botonModal" data-toggle="modal" data-target="#modalOperadoresTotal"> Ver más </a>
                        </div>
                    </div>
                </div>
                <div class="col-6 text-center " id="colProgramas">
                    <div class="card shadow mb-4 graficosBarra" id="ocultarGraficoProgramas">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-2 text-left">
                                    <span data-toggle="tooltip" title="¿Que estás visualizando en este momento?" data-placement="right">
                                        <button type="button" id="tuBotonID" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom" data-bs-toggle="collapse" data-bs-target="#totalProgramas" aria-expanded="false" aria-controls="totalProgramas">
                                            <i class="fa-solid fa-arrow-down"></i></button>
                                    </span>
                                </div>
                                <div class="col-8 d-flex align-items-center justify-content-center">
                                    <h5><strong>Programas con mayor cantidad de admitidos Activos</strong></h5>
                                </div>
                                <div class="col-2 text-right">
                                    <span data-toggle="tooltip" title="Muestra la cantidad de estudiantes inscritos en cada programa, cuenta con la opción de 'Ver más'." data-placement="right">
                                        <button type="button" class="btn" style="background-color: #dfc14e;border-color: #dfc14e;; color:white;" data-toggle="tooltip" data-placement="bottom"><i class="fa-solid fa-circle-question"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div id="totalProgramas" class="collapse mt-3">
                                <div class="card card-body titulos">
                                    <h6 id="tituloProgramas"><strong>Programas con mayor cantidad de admitidos Activos</strong></h6>
                                    <h6 class="tituloPeriodo"><strong></strong></h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body fondocharts">
                            <canvas id="estudiantesProgramas"></canvas>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <a href="" id="botonModalProgramas" class="btn botonModal" data-toggle="modal" data-target="#modalProgramasTotal"> Ver más </a>
                        </div>
                    </div>
                </div>
                <div class="col-6 text-center" id="colMetas">
                    <div class="card shadow mb-4 graficosBarra">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-2 text-left">
                                    <span data-toggle="tooltip" title="¿Que estás visualizando en este momento?" data-placement="right">
                                        <button type="button" id="tuBotonID" class="btn" style="background-color: #dfc14e; border-color: #dfc14e; color: white;" data-toggle="tooltip" data-placement="bottom" data-bs-toggle="collapse" data-bs-target="#totalMetas" aria-expanded="false" aria-controls="totalMetas">
                                            <i class="fa-solid fa-arrow-down"></i></button>
                                    </span>
                                </div>
                                <div class="col-8 d-flex align-items-center justify-content-center">
                                    <h5><strong>Metas periodo activo por programa P5</strong></h5>
                                </div>
                                <div class="col-2 text-right">
                                    <span data-toggle="tooltip" title="Muestra la cantidad de estudiantes inscritos por programa con sello financiero y de primer ingreso VS la meta fijada, además permite descargar un Excel en donde se puede visualizar el porcentaje de cumplimiento de la meta." data-placement="right">
                                        <button type="button" class="btn" style="background-color: #dfc14e;border-color: #dfc14e;; color:white;" data-toggle="tooltip" data-placement="bottom"><i class="fa-solid fa-circle-question"></i></button>
                                    </span>
                                </div>
                            </div>
                            <div id="totalMetas" class="collapse mt-3">
                                <div class="card card-body titulos">
                                    <h6 id="tituloMetas"><strong>Metas por programa (Primer ingreso, transferentes y reingresos)</strong></h6>
                                    <h6 class="tituloPeriodo"><strong></strong></h6>
                                </div>
                            </div>
                        </div>
                        <div class="card-body fondocharts">
                            <h6 id="metascumplidas"></h6>
                            <canvas id="graficoMetas"></canvas>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <a href="" id="botonModalMetas" class="btn botonModal" data-toggle="modal" data-target="#modalMetas"> Ver más </a>
                        </div>
                    </div>
                </div>
            </div>