<!-- incluimos el header para el html -->
@include('layout.header')


<!--  creamos el contenido principal body -->
<style>
    #facultades {
        font-size: 14px;
    }

    #programas {
        font-size: 14px;
    }
    
    #generarReporte {
        margin-left: 260px;
    }


    .btn {
        background-color: #dfc14e;
        border-color: #dfc14e;
        color: white;
        width: 200px;
        height: 30px;
        border-radius: 10px;
        font-weight: bold;
        place-items: center;
        font-size: 14px;
    }

    #botonModalProgramas, #botonModalOperador{
        background-color: #dfc14e;
        border-color: #dfc14e;
        color: white;
        width: 100px;
        height: 30px;
        border-radius: 10px;
        font-weight: bold;
        place-items: center;
        font-size: 14px;
    }

    #cardFacultades {
        min-height: 405.6px;
        max-height: 405.6px;
    }

    #cardProgramas {
        min-height: 405.6px;
        max-height: 405.6px;
    }

    .card {
        margin-bottom: 3%;
    }

    .hidden {
        display: none;
    }

    #chartEstudiantes {
        min-height: 405.6px;
        max-height: 405.6px;
    }

    #centrar {
        display: flex;
        align-items: center;
    }

    .graficos {
        min-height: 460px;
        max-height: 460px;
    }

    #operadoresTotal,
    #programasTotal {
        height: 600px !important;
    }
</style>

<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

           

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="text-center">
                <h1 class="h3 mb-0 text-gray-800"> <strong>Informe de Facultades</strong></h1>
            </div>
            <br>
            <div class="text-center" id="mensaje">
                <h3>Por defecto se muestran los datos de todas las facultades,
                    si quieres ver datos en especifico, selecciona alguna facultad.
                </h3>
            </div>
            <br>

            <!-- Checkbox Facultades -->
            <div class="row justify-content-between" id="">
                <div class="col-4 text-star">
                    <div class="card shadow mb-5" id="cardFacultades">
                        <div class="card-header text-center">
                            <h5><strong>Seleccionar Facultades</strong></h5>
                        </div>
                        <div class="card-body text-start" id="centrar" style="overflow: auto;">
                            <div class="facultades" name="facultades" id="facultades">
                                <div>
                                    <label> <input type="checkbox" value="" id="mostrarTodos" checked> Ver Todo</label>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4 text-start">
                    <div class="card shadow mb-5" id="cardProgramas">
                        <div class="card-header text-center">
                            <h5><strong>Seleccionar Programas</strong></h5>
                        </div>
                        <div class="card-body text-star" style="overflow: auto;">
                            <div name="programas" id="programas"></div>
                        </div>
                    </div>
                </div>
                <div class=" col-4 text-center" id="colEstudiantes">
                    <div class="card shadow mb-5" id="chartEstudiantes">
                        <div class="card-header">
                            <h5 class="titulos"><strong>Total estudiantes Banner</strong></h5>
                            <h5 class="facultadtitulos" style="display: none;"><strong>Estudiantes por Facultad</strong></h5>
                            <h5 class="programastitulos" style="display: none;"><strong>Estudiantes por Programa</strong></h5>
                        </div>
                        <div class="card-body">
                            <div id="vacioTotalEstudiantes" class="text-center vacio" style="display: none;">
                                <h5>- por mostrar</h5>
                            </div>
                            <canvas id="estudiantes"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <button class="btn" type="button" id="generarReporte">
                    Generar Reporte
                </button>
            </div>

    </div>

    <div class="row justify-content-center mt-5">
        <div class="col-6 text-center" id="colSelloFinanciero">
            <div class="card shadow mb-6 graficos">
                <div class="card-header">
                    <h5 class="titulos"><strong>Total estudiantes con sello financiero</strong></h5>
                    <h5 class="facultadtitulos" style="display: none;"><strong>Sello finaciero por Facultad</strong></h5>
                    <h5 class="programastitulos" style="display: none;"><strong>Sello finaciero por Programa</strong></h5>
                </div>
                <div class="card-body">
                    <div id="vacioTotalSello" class="text-center vacio" style="display: none;">
                        <h5>- por mostrar</h5>
                    </div>
                    <canvas id="activos"></canvas>
                </div>
            </div>
        </div>
        <div class="col-6 text-center" id="colRetencion">
            <div class="card shadow mb-6 graficos">
                <div class="card-header">
                    <h5 class="titulos"><strong>Con Sello de Retención (ASP)</strong></h5>
                    <h5 class="facultadtitulos" style="display: none;"><strong>Con Sello de Retención (ASP) por Facultad</strong></h5>
                    <h5 class="programastitulos" style="display: none;"><strong>Con Sello de Retención (ASP) por Programa</strong></h5>
                </div>
                <div class="card-body">
                    <div id="vacioRetencion" class="text-center vacio" style="display: none;">
                        <h5>- por mostrar</h5>
                    </div>
                    <canvas id="retencion"></canvas>
                </div>
            </div>
        </div>
        <div class="col-6 text-center" id="colPrimerIngreso">
            <div class="card shadow mb-6 graficos">
                <div class="card-header">
                    <h5 class="titulos"><strong>Estudiantes primer ingreso con tipos de sellos</strong></h5>
                    <h5 class="facultadtitulos" style="display: none;"><strong>Estudiantes primer ingreso con tipos de sellos por Facultad</strong></h5>
                    <h5 class="programastitulos" style="display: none;"><strong>Estudiantes primer ingreso con tipos de sellos por Programa</strong></h5>
                </div>
                <div class="card-body">
                    <div id="vacioPrimerIngreso" class="text-center vacio" style="display: none;">
                        <h5>- por mostrar</h5>
                    </div>
                    <canvas id="primerIngreso"></canvas>
                </div>
            </div>
        </div>
        <div class="col-6 text-center" id="colTipoEstudiantes">
            <div class="card shadow mb-6 graficos">
                <div class="card-header">
                    <h5 class="titulos"><strong>Tipos de estudiantes</strong></h5>
                    <h5 class="facultadtitulos" style="display: none;"><strong>Tipos de estudiantes por Facultad</strong></h5>
                    <h5 class="programastitulos" style="display: none;"><strong>Tipos de estudiantes por Programa</strong></h5>
                </div>
                <div class="card-body">
                    <div id="vacioTipoEstudiante" class="text-center vacio" style="display: none;">
                        <h5>- por mostrar</h5>
                    </div>
                    <canvas id="tipoEstudiante"></canvas>
                </div>
            </div>
        </div>
        <div class="col-6 text-center" id="colOperadores">
            <div class="card shadow mb-6 graficos">
                <div class="card-header">
                    <h5 class="titulos"><strong>Operadores</strong></h5>
                    <h5 class="facultadtitulos" style="display: none;"><strong>Operadores por Facultad</strong></h5>
                    <h5 class="programastitulos" style="display: none;"><strong>Operadores por Programa</strong></h5>
                </div>
                <div class="card-body">
                    <div id="vacioOperadores" class="text-center vacio" style="display: none;">
                        <h5>- por mostrar</h5>
                    </div>
                    <canvas id="operadores"></canvas>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <a href="" id="botonModalOperador" class="btn" data-toggle="modal" data-target="#modalOperadoresTotal"> Ver más </a>
                </div>
            </div>
        </div>
        <div class="col-6 text-center" id="colProgramas">
            <div class="card shadow mb-4 graficos" id="ocultarGraficoProgramas">
                <div class="card-header">
                    <h5 class="titulos"><strong>Programas con mayor cantidad de admitidos</strong></h5>
                    <h5 class="facultadtitulos" style="display: none;"><strong>Programas con mayor cantidad de admitidos por Facultad</strong></h5>
                </div>
                <div class="card-body">
                    <div id="vacioProgramas" class="text-center vacio" style="display: none;">
                        <h5>- por mostrar</h5>
                    </div>
                    <canvas id="estudiantesProgramas"></canvas>
                </div>
                <div class="card-footer d-flex justify-content-end">
                    <a href="" id="botonModalProgramas" class="btn" data-toggle="modal" data-target="#modalProgramasTotal"> Ver más </a>
                </div>
            </div>
        </div>
    </div>

    <br>

    <!-- Modal Todos los Operadores de la Ibero -->
    <div class="modal fade" id="modalOperadoresTotal" tabindex="-1" role="dialog" aria-labelledby="modalOperadoresTotal" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="title">Operadores</h5>
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
                <div class="modal-header">
                    <h5 class="modal-title" id="title">Programas</h5>
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


</div>




<!-- incluimos el footer -->
@include('layout.footer')
</div>