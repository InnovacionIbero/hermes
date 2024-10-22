@include('layout.header')

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

    #botonModalProgramas,
    #botonModalOperador {
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
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>
        </nav>
        <div class="container-fluid">
            <div class="container mt-3" id="info">
                <p class="col-md-12" style="margin-top: 2%;">
                    <strong>Historial académico de: </strong> {{ $nombre}}<br>
                    <strong>IdBanner</strong>: {{ $estudiante->homologante}}<br>

                    <b> Recuerde que la información suministrada por este sistema es de carácter informativo.</b> <br>
                    Nota: Si presenta inconsistencias, verifica primero en la plataforma de autogestión <a href="https://www.ibero.edu.co/experiencia-al-estudiante/portal-autogestion" target="_blank" rel="noopener noreferrer">Zafiro.</a>  Si el periodo ha finalizado las calificaciones pueden tardar alrededor de 5 días para verse reflejadas en el historial.
                </p>
            </div>
        </div>
        <div class="container-fluid">
            <div class="col-md-12">
                <ul class="nav nav-tabs taps_programas" role="tablist">
                    @for($i = 0; $i < count($programas); $i++)
                        <li class="nav-item "><a class="nav-link " onclick="consultaMalla('{{ $programas[$i]['cod_programa']}}',{{ $estudiante->homologante}})" data-toggle="pill" role="tab" aria-controls="pills-contact" aria-selected="false">{{ $programas[$i]['programa'] }}</a></li>
                    @endfor
                </ul>
            </div>
            {{-- <div class="tab-content  contenido_taps">
                <div class="tab-pane fade active show" id="tap_1" role="tabpanel" aria-labelledby="pills-contact-tab">
                    <div class="col-md-12">

                        <ul class="nav nav-tabs" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-toggle="pill" id="Malla_li_1" href="#Malla_1" role="tab" aria-controls="pills-home" aria-selected="true">Malla</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link " data-toggle="pill" id="programadas_li_1" href="#programadas_1" role="tab" aria-controls="pills-home" aria-selected="true">Materias programadas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" id="por_ver_li_1" href="#por_ver_1" role="tab" aria-controls="pills-profile" aria-selected="false">Materias por ver</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" id="vistas_li_1" href="#vistas_1" role="tab" aria-controls="pills-contact" aria-selected="false">Materias vistas</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="pill" id="completo_li_1" href="#completo_1" role="tab" aria-controls="pills-contact" aria-selected="false">Historial completo</a>
                            </li>
                        </ul>

                        <div class="tab-content">

                            <div class="tab-pane fade active show " id="Malla_1" role="tabpanel" aria-labelledby="pills-home-tab">

                                <table id="tabla_1" class="table table-striped table-bordered" style="width:100%">
                                    <tbody>
                                        <tr id="EABV_1">
                                            <td><span id="se_1">semestre 1</span> <br> </td>
                                            <td class="bg-success" style=" color:white"><span class="curso" style="font-size: 12px;line-height: 12px;">Electiva integral-BUEN VIVIR CAMINO HACIA EL DESARROLLO SOSTENIBLE (TRSP22100) </span> <br><span class="creditos" style="font-size: 12px; margin-right: 3%;line-height: 12px;">creditos: 2</span> <br><span class="nota" style="font-size: 12px;line-height: 12px;"> calificación: 4,70</span> <br> <span class="nota" style="font-size: 12px;line-height: 12px;">Ciclo:1</span></td>
                                            <td class="bg-success" style=" color:white"><span class="curso" style="font-size: 12px;line-height: 12px;">GESTIÓN DE CONOCIMIENTO Y BIG DATA (ABV32100) </span> <br><span class="creditos" style="font-size: 12px; margin-right: 3%;line-height: 12px;">creditos: 3</span> <br><span class="nota" style="font-size: 12px;line-height: 12px;"> calificación: 4,30</span> <br> <span class="nota" style="font-size: 12px;line-height: 12px;">Ciclo:1</span></td>
                                            <td class="bg-success" style=" color:white"><span class="curso" style="font-size: 12px;line-height: 12px;">COMPETENCIAS Y PROCESOS INVESTIGATIVOS (TFI32100) </span> <br><span class="creditos" style="font-size: 12px; margin-right: 3%;line-height: 12px;">creditos: 2</span> <br><span class="nota" style="font-size: 12px;line-height: 12px;"> calificación: 4,20</span> <br> <span class="nota" style="font-size: 12px;line-height: 12px;">Ciclo:12</span></td>
                                            <td class="bg-success" style=" color:white"><span class="curso" style="font-size: 12px;line-height: 12px;">COMPUTACIÓN EN LA NUBE PARA BIG DATA (ABV32110) </span> <br><span class="creditos" style="font-size: 12px; margin-right: 3%;line-height: 12px;">creditos: 3</span> <br><span class="nota" style="font-size: 12px;line-height: 12px;"> calificación: 4,50</span> <br> <span class="nota" style="font-size: 12px;line-height: 12px;">Ciclo:2</span></td>
                                            <td class="bg-success" style=" color:white"><span class="curso" style="font-size: 12px;line-height: 12px;">COMPUTACIÓN DE ALTO DESEMPEÑO PARA BIG DATA (ABV32120) </span> <br><span class="creditos" style="font-size: 12px; margin-right: 3%;line-height: 12px;">creditos: 3</span> <br><span class="nota" style="font-size: 12px;line-height: 12px;"> calificación: 4,30</span> <br> <span class="nota" style="font-size: 12px;line-height: 12px;">Ciclo:2</span></td>
                                            <td class="bg-success" style=" color:white"><span class="curso" style="font-size: 12px;line-height: 12px;">COMPUTACIÓN COGNITIVA PARA BIG DATA (ABV32130) </span> <br><span class="creditos" style="font-size: 12px; margin-right: 3%;line-height: 12px;">creditos: 3</span> <br><span class="nota" style="font-size: 12px;line-height: 12px;"> calificación: 4,90</span> <br> <span class="nota" style="font-size: 12px;line-height: 12px;">Ciclo:2</span></td>
                                        </tr>
                                        <tr id="EABV_2">
                                            <td><span id="se_2">semestre 2</span> <br> </td>
                                            <td class="bg-secondary" style=" color:white"><span class="curso" style="font-size: 12px;line-height: 12px;">ALGORITMOS Y VISUALIZACIÓN DE DATOS (ABV32190) </span> <br><span class="creditos" style="font-size: 12px; margin-right: 3%;line-height: 12px;">creditos: 3</span> <br><span class="nota" style="font-size: 12px;line-height: 12px;"> calificación: </span> <br> <span class="nota" style="font-size: 12px;line-height: 12px;">Ciclo:2</span></td>
                                            <td class="bg-secondary" style=" color:white"><span class="curso" style="font-size: 12px;line-height: 12px;">PARADIGMAS PARA EL ALMACENAMIENTO Y PROCESAMIENTO DE BIG DATA (ABV32180) </span> <br><span class="creditos" style="font-size: 12px; margin-right: 3%;line-height: 12px;">creditos: 3</span> <br><span class="nota" style="font-size: 12px;line-height: 12px;"> calificación: </span> <br> <span class="nota" style="font-size: 12px;line-height: 12px;">Ciclo:1</span></td>
                                            <td class="bg-secondary" style=" color:white"><span class="curso" style="font-size: 12px;line-height: 12px;">OPCIÓN DE GRADO (ABV32160) </span> <br><span class="creditos" style="font-size: 12px; margin-right: 3%;line-height: 12px;">creditos: 2</span> <br><span class="nota" style="font-size: 12px;line-height: 12px;"> calificación: </span> <br> <span class="nota" style="font-size: 12px;line-height: 12px;">Ciclo:12</span></td>
                                            <td class="bg-secondary" style=" color:white"><span class="curso" style="font-size: 12px;line-height: 12px;">MÉTODOS ESTADÍSTICOS Y ALGORITMOS PARA EL ANÁLISIS DE DATOS (ABV32170) </span> <br><span class="creditos" style="font-size: 12px; margin-right: 3%;line-height: 12px;">creditos: 3</span> <br><span class="nota" style="font-size: 12px;line-height: 12px;"> calificación: </span> <br> <span class="nota" style="font-size: 12px;line-height: 12px;">Ciclo:1</span></td>
                                            <td class="bg-secondary" style=" color:white"><span class="curso" style="font-size: 12px;line-height: 12px;">ELEC PROF: ADQUISICIÓN DE DATOS DAQ OR DAS IOT (ABV32140) </span> <br><span class="creditos" style="font-size: 12px; margin-right: 3%;line-height: 12px;">creditos: 2</span> <br><span class="nota" style="font-size: 12px;line-height: 12px;"> calificación: </span> <br> <span class="nota" style="font-size: 12px;line-height: 12px;">Ciclo:2</span></td>
                                            <td class="bg-secondary" style=" color:white"><span class="curso" style="font-size: 12px;line-height: 12px;">ELEC PROF: CIENCIA DE DATOS PARA LA TOMA DE DECISIONES ESTRATÉGICAS (ABV32150) </span> <br><span class="creditos" style="font-size: 12px; margin-right: 3%;line-height: 12px;">creditos: 2</span> <br><span class="nota" style="font-size: 12px;line-height: 12px;"> calificación: </span> <br> <span class="nota" style="font-size: 12px;line-height: 12px;">Ciclo:2</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="tab-pane fade  " id="programadas_1" role="tabpanel" aria-labelledby="pills-home-tab">
                                <div id="materias_programadas_1_wrapper" class="dataTables_wrapper">
                                    <div class="dataTables_length" id="materias_programadas_1_length"><label>Mostrar <select name="materias_programadas_1_length" aria-controls="materias_programadas_1" class="">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select> registros</label></div>
                                    <div id="materias_programadas_1_filter" class="dataTables_filter"><label>Buscar:<input type="search" class="" placeholder="" aria-controls="materias_programadas_1"></label></div>
                                    <div id="materias_programadas_1_processing" class="dataTables_processing" style="display: none;">Procesando...<div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                    </div>
                                    <table id="materias_programadas_1" class="table table-striped table-bordered dataTable" style="width: 100%;" aria-describedby="materias_programadas_1_info">
                                        <thead>
                                            <tr style="background-color: #ffb700;">
                                                <th style="width: 10%!important;" class="sorting" tabindex="0" aria-controls="materias_programadas_1" rowspan="1" colspan="1" aria-label="codMateria: Activar para ordenar la columna de manera ascendente">codMateria</th>
                                                <th style="width: 80%!important;" class="sorting" tabindex="0" aria-controls="materias_programadas_1" rowspan="1" colspan="1" aria-label="Materias: Activar para ordenar la columna de manera ascendente">Materias</th>
                                                <th style="width: 10%!important;" class="text-center sorting sorting_asc" tabindex="0" aria-controls="materias_programadas_1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Semestre: Activar para ordenar la columna de manera descendente">Semestre</th>
                                                <th style="width: 10%!important;" class="text-center sorting" tabindex="0" aria-controls="materias_programadas_1" rowspan="1" colspan="1" aria-label="Ciclo: Activar para ordenar la columna de manera ascendente">Ciclo</th>
                                                <th style="width: 10%!important;" class="text-center sorting" tabindex="0" aria-controls="materias_programadas_1" rowspan="1" colspan="1" aria-label="Creditos: Activar para ordenar la columna de manera ascendente">Creditos</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <tr class="odd">
                                                <td valign="top" colspan="5" class="dataTables_empty">Ningún dato disponible en esta tabla</td>
                                            </tr>
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <th colspan="4" style="text-align:right" class="text-center" rowspan="1">Total creditos programados:</th>
                                                <th id="total_materias_programadas_1" class="text-center" rowspan="1" colspan="1"> </th>
                                            </tr>
                                        </tfoot>

                                    </table>
                                    <div class="dataTables_info" id="materias_programadas_1_info" role="status" aria-live="polite">Mostrando registros del 0 al 0 de un total de 0 registros</div>
                                    <div class="dataTables_paginate paging_simple_numbers" id="materias_programadas_1_paginate"><a class="paginate_button previous disabled" aria-controls="materias_programadas_1" data-dt-idx="0" tabindex="-1" id="materias_programadas_1_previous">Anterior</a><span></span><a class="paginate_button next disabled" aria-controls="materias_programadas_1" data-dt-idx="1" tabindex="-1" id="materias_programadas_1_next">Siguiente</a></div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="por_ver_1" role="tabpanel" aria-labelledby="pills-profile-tab">
                                <div id="materias_por_ver_1_wrapper" class="dataTables_wrapper">
                                    <div class="dataTables_length" id="materias_por_ver_1_length"><label>Mostrar <select name="materias_por_ver_1_length" aria-controls="materias_por_ver_1" class="">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select> registros</label></div>
                                    <div id="materias_por_ver_1_filter" class="dataTables_filter"><label>Buscar:<input type="search" class="" placeholder="" aria-controls="materias_por_ver_1"></label></div>
                                    <table id="materias_por_ver_1" class="table table-striped table-bordered dataTable" style="width: 100%;" aria-describedby="materias_por_ver_1_info">
                                        <thead>
                                            <tr style="background-color: #ffb700;">
                                                <th style="width: 5%!important;" class="text-center sorting" tabindex="0" aria-controls="materias_por_ver_1" rowspan="1" colspan="1" aria-label="codMateria: Activar para ordenar la columna de manera ascendente">codMateria</th>
                                                <th style="width: 50%!important;" class="sorting" tabindex="0" aria-controls="materias_por_ver_1" rowspan="1" colspan="1" aria-label="Materias: Activar para ordenar la columna de manera ascendente">Materias</th>
                                                <th style="width: 5%!important;" class="text-center sorting sorting_asc" tabindex="0" aria-controls="materias_por_ver_1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Semestre: Activar para ordenar la columna de manera descendente">Semestre</th>
                                                <th style="width: 5%!important;" class="text-center sorting" tabindex="0" aria-controls="materias_por_ver_1" rowspan="1" colspan="1" aria-label="Ciclo: Activar para ordenar la columna de manera ascendente">Ciclo</th>
                                                <th style="width: 5%!important;" class="text-center sorting" tabindex="0" aria-controls="materias_por_ver_1" rowspan="1" colspan="1" aria-label="Creditos: Activar para ordenar la columna de manera ascendente">Creditos</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <tr class="odd">
                                                <td class="  text-center">ABV32190</td>
                                                <td>ALGORITMOS Y VISUALIZACIÓN DE DATOS</td>
                                                <td class="text-center sorting_1">2</td>
                                                <td class="  text-center">2</td>
                                                <td class="  text-center">3</td>
                                            </tr>
                                            <tr class="even">
                                                <td class="  text-center">ABV32180</td>
                                                <td>PARADIGMAS PARA EL ALMACENAMIENTO Y PROCESAMIENTO DE BIG DATA</td>
                                                <td class="text-center sorting_1">2</td>
                                                <td class="  text-center">1</td>
                                                <td class="  text-center">3</td>
                                            </tr>
                                            <tr class="odd">
                                                <td class="  text-center">ABV32160</td>
                                                <td>OPCIÓN DE GRADO</td>
                                                <td class="text-center sorting_1">2</td>
                                                <td class="  text-center">12</td>
                                                <td class="  text-center">2</td>
                                            </tr>
                                            <tr class="even">
                                                <td class="  text-center">ABV32170</td>
                                                <td>MÉTODOS ESTADÍSTICOS Y ALGORITMOS PARA EL ANÁLISIS DE DATOS</td>
                                                <td class="text-center sorting_1">2</td>
                                                <td class="  text-center">1</td>
                                                <td class="  text-center">3</td>
                                            </tr>
                                            <tr class="odd">
                                                <td class="  text-center">ABV32140</td>
                                                <td>ELEC PROF: ADQUISICIÓN DE DATOS DAQ OR DAS IOT</td>
                                                <td class="text-center sorting_1">2</td>
                                                <td class="  text-center">2</td>
                                                <td class="  text-center">2</td>
                                            </tr>
                                            <tr class="even">
                                                <td class="  text-center">ABV32150</td>
                                                <td>ELEC PROF: CIENCIA DE DATOS PARA LA TOMA DE DECISIONES ESTRATÉGICAS</td>
                                                <td class="text-center sorting_1">2</td>
                                                <td class="  text-center">2</td>
                                                <td class="  text-center">2</td>
                                            </tr>
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <th colspan="4" style="text-align:right" class="text-center" rowspan="1">Total creditos por ver:</th>
                                                <th id="total_por_ver_1" class="text-center" rowspan="1" colspan="1"> 15</th>
                                            </tr>
                                        </tfoot>

                                    </table>
                                    <div class="dataTables_info" id="materias_por_ver_1_info" role="status" aria-live="polite">Mostrando registros del 1 al 6 de un total de 6 registros</div>
                                    <div class="dataTables_paginate paging_simple_numbers" id="materias_por_ver_1_paginate"><a class="paginate_button previous disabled" aria-controls="materias_por_ver_1" data-dt-idx="0" tabindex="-1" id="materias_por_ver_1_previous">Anterior</a><span><a class="paginate_button current" aria-controls="materias_por_ver_1" data-dt-idx="1" tabindex="0">1</a></span><a class="paginate_button next disabled" aria-controls="materias_por_ver_1" data-dt-idx="2" tabindex="-1" id="materias_por_ver_1_next">Siguiente</a></div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="vistas_1" role="tabpanel" aria-labelledby="pills-contact-tab">
                                <div id="materias_vistas_1_wrapper" class="dataTables_wrapper">
                                    <div class="dataTables_length" id="materias_vistas_1_length"><label>Mostrar <select name="materias_vistas_1_length" aria-controls="materias_vistas_1" class="">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select> registros</label></div>
                                    <div id="materias_vistas_1_filter" class="dataTables_filter"><label>Buscar:<input type="search" class="" placeholder="" aria-controls="materias_vistas_1"></label></div>
                                    <table id="materias_vistas_1" class="table table-striped table-bordered dataTable" style="width: 100%;" aria-describedby="materias_vistas_1_info">
                                        <thead>
                                            <tr style="background-color: #ffb700;">
                                                <th style="width: 3%!important;" class="text-center sorting" tabindex="0" aria-controls="materias_vistas_1" rowspan="1" colspan="1" aria-label="codMateria: Activar para ordenar la columna de manera ascendente">codMateria</th>
                                                <th style="width: 50%!important;" class="sorting" tabindex="0" aria-controls="materias_vistas_1" rowspan="1" colspan="1" aria-label="Materias: Activar para ordenar la columna de manera ascendente">Materias</th>
                                                <th style="width: 3%!important;" class="text-center sorting sorting_asc" tabindex="0" aria-controls="materias_vistas_1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Semestre: Activar para ordenar la columna de manera descendente">Semestre</th>
                                                <th style="width: 3%!important;" class="text-center sorting" tabindex="0" aria-controls="materias_vistas_1" rowspan="1" colspan="1" aria-label="Ciclo: Activar para ordenar la columna de manera ascendente">Ciclo</th>
                                                <th style="width: 3%!important;" class="text-center sorting" tabindex="0" aria-controls="materias_vistas_1" rowspan="1" colspan="1" aria-label="Creditos: Activar para ordenar la columna de manera ascendente">Creditos</th>
                                                <th style="width: 3%!important;" class="text-center sorting" tabindex="0" aria-controls="materias_vistas_1" rowspan="1" colspan="1" aria-label="Nota: Activar para ordenar la columna de manera ascendente">Nota</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <tr class="odd">
                                                <td class="  text-center">TRSP22100</td>
                                                <td>Electiva integral-BUEN VIVIR CAMINO HACIA EL DESARROLLO SOSTENIBLE</td>
                                                <td class="text-center sorting_1">1</td>
                                                <td class="  text-center">1</td>
                                                <td class="  text-center">2</td>
                                                <td class="  text-center">4,70</td>
                                            </tr>
                                            <tr class="even">
                                                <td class="  text-center">ABV32100</td>
                                                <td>GESTIÓN DE CONOCIMIENTO Y BIG DATA</td>
                                                <td class="text-center sorting_1">1</td>
                                                <td class="  text-center">1</td>
                                                <td class="  text-center">3</td>
                                                <td class="  text-center">4,30</td>
                                            </tr>
                                            <tr class="odd">
                                                <td class="  text-center">TFI32100</td>
                                                <td>COMPETENCIAS Y PROCESOS INVESTIGATIVOS</td>
                                                <td class="text-center sorting_1">1</td>
                                                <td class="  text-center">12</td>
                                                <td class="  text-center">2</td>
                                                <td class="  text-center">4,20</td>
                                            </tr>
                                            <tr class="even">
                                                <td class="  text-center">ABV32110</td>
                                                <td>COMPUTACIÓN EN LA NUBE PARA BIG DATA</td>
                                                <td class="text-center sorting_1">1</td>
                                                <td class="  text-center">2</td>
                                                <td class="  text-center">3</td>
                                                <td class="  text-center">4,50</td>
                                            </tr>
                                            <tr class="odd">
                                                <td class="  text-center">ABV32120</td>
                                                <td>COMPUTACIÓN DE ALTO DESEMPEÑO PARA BIG DATA</td>
                                                <td class="text-center sorting_1">1</td>
                                                <td class="  text-center">2</td>
                                                <td class="  text-center">3</td>
                                                <td class="  text-center">4,30</td>
                                            </tr>
                                            <tr class="even">
                                                <td class="  text-center">ABV32130</td>
                                                <td>COMPUTACIÓN COGNITIVA PARA BIG DATA</td>
                                                <td class="text-center sorting_1">1</td>
                                                <td class="  text-center">2</td>
                                                <td class="  text-center">3</td>
                                                <td class="  text-center">4,90</td>
                                            </tr>
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <th colspan="4" style="text-align:right" class="text-center" rowspan="1">Total creditos Vistos:</th>
                                                <th id="total_materias_vistas_1" class="text-center" rowspan="1" colspan="1"> 16</th>
                                            </tr>

                                        </tfoot>

                                    </table>
                                    <div class="dataTables_info" id="materias_vistas_1_info" role="status" aria-live="polite">Mostrando registros del 1 al 6 de un total de 6 registros</div>
                                    <div class="dataTables_paginate paging_simple_numbers" id="materias_vistas_1_paginate"><a class="paginate_button previous disabled" aria-controls="materias_vistas_1" data-dt-idx="0" tabindex="-1" id="materias_vistas_1_previous">Anterior</a><span><a class="paginate_button current" aria-controls="materias_vistas_1" data-dt-idx="1" tabindex="0">1</a></span><a class="paginate_button next disabled" aria-controls="materias_vistas_1" data-dt-idx="2" tabindex="-1" id="materias_vistas_1_next">Siguiente</a></div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="completo_1" role="tabpanel" aria-labelledby="pills-contact-tab">
                                <div id="materias_1_wrapper" class="dataTables_wrapper no-footer">
                                    <div class="dataTables_length" id="materias_1_length"><label>Mostrar <select name="materias_1_length" aria-controls="materias_1" class="">
                                                <option value="10">10</option>
                                                <option value="25">25</option>
                                                <option value="50">50</option>
                                                <option value="100">100</option>
                                            </select> registros</label></div>
                                    <div class="dt-buttons"> <button class="dt-button buttons-excel buttons-html5" tabindex="0" aria-controls="materias_1" type="button"><span>Excel</span></button> <button class="dt-button buttons-csv buttons-html5" tabindex="0" aria-controls="materias_1" type="button"><span>CSV</span></button> <button class="dt-button buttons-pdf buttons-html5" tabindex="0" aria-controls="materias_1" type="button"><span>PDF</span></button> <button class="dt-button buttons-print" tabindex="0" aria-controls="materias_1" type="button"><span>Print</span></button> <button class="dt-button buttons-copy buttons-html5" tabindex="0" aria-controls="materias_1" type="button"><span>Copy</span></button> </div>
                                    <div id="materias_1_filter" class="dataTables_filter"><label>Buscar:<input type="search" class="" placeholder="" aria-controls="materias_1"></label></div>
                                    <table id="materias_1" class="table table-striped table-bordered dataTable no-footer" style="width: 100%;" aria-describedby="materias_1_info">
                                        <thead>
                                            <tr style="background-color: #ffb700;">
                                                <th style="width: 10%!important;" class="sorting" tabindex="0" aria-controls="materias_1" rowspan="1" colspan="1" aria-label="codMateria: Activar para ordenar la columna de manera ascendente">codMateria</th>
                                                <th style="width: 80%!important;" class="sorting" tabindex="0" aria-controls="materias_1" rowspan="1" colspan="1" aria-label="Materias: Activar para ordenar la columna de manera ascendente">Materias</th>
                                                <th style="width: 10%!important;" class="text-center sorting sorting_asc" tabindex="0" aria-controls="materias_1" rowspan="1" colspan="1" aria-sort="ascending" aria-label="Semestre: Activar para ordenar la columna de manera descendente">Semestre</th>
                                                <th style="width: 3%!important;" class="text-center sorting" tabindex="0" aria-controls="materias_1" rowspan="1" colspan="1" aria-label="Ciclo: Activar para ordenar la columna de manera ascendente">Ciclo</th>
                                                <th style="width: 10%!important;" class="text-center sorting" tabindex="0" aria-controls="materias_1" rowspan="1" colspan="1" aria-label="Cursada: Activar para ordenar la columna de manera ascendente">Cursada</th>
                                                <th style="width: 10%!important;" class="text-center sorting" tabindex="0" aria-controls="materias_1" rowspan="1" colspan="1" aria-label="Materia Por ver: Activar para ordenar la columna de manera ascendente">Materia Por ver</th>
                                                <th style="width: 10%!important;" class="text-center sorting" tabindex="0" aria-controls="materias_1" rowspan="1" colspan="1" aria-label="Programada en periodo actual: Activar para ordenar la columna de manera ascendente">Programada en periodo actual</th>
                                            </tr>
                                        </thead>
                                        <tbody>

                                            <tr class="odd">
                                                <td>TRSP22100</td>
                                                <td>Electiva integral-BUEN VIVIR CAMINO HACIA EL DESARROLLO SOSTENIBLE</td>
                                                <td class="text-center sorting_1">1</td>
                                                <td class="  text-center">1</td>
                                                <td class="  text-center">4,70</td>
                                                <td class="  text-center"></td>
                                                <td class="  text-center"></td>
                                            </tr>
                                            <tr class="even">
                                                <td>ABV32100</td>
                                                <td>GESTIÓN DE CONOCIMIENTO Y BIG DATA</td>
                                                <td class="text-center sorting_1">1</td>
                                                <td class="  text-center">1</td>
                                                <td class="  text-center">4,30</td>
                                                <td class="  text-center"></td>
                                                <td class="  text-center"></td>
                                            </tr>
                                            <tr class="odd">
                                                <td>TFI32100</td>
                                                <td>COMPETENCIAS Y PROCESOS INVESTIGATIVOS</td>
                                                <td class="text-center sorting_1">1</td>
                                                <td class="  text-center">12</td>
                                                <td class="  text-center">4,20</td>
                                                <td class="  text-center"></td>
                                                <td class="  text-center"></td>
                                            </tr>
                                            <tr class="even">
                                                <td>ABV32110</td>
                                                <td>COMPUTACIÓN EN LA NUBE PARA BIG DATA</td>
                                                <td class="text-center sorting_1">1</td>
                                                <td class="  text-center">2</td>
                                                <td class="  text-center">4,50</td>
                                                <td class="  text-center"></td>
                                                <td class="  text-center"></td>
                                            </tr>
                                            <tr class="odd">
                                                <td>ABV32120</td>
                                                <td>COMPUTACIÓN DE ALTO DESEMPEÑO PARA BIG DATA</td>
                                                <td class="text-center sorting_1">1</td>
                                                <td class="  text-center">2</td>
                                                <td class="  text-center">4,30</td>
                                                <td class="  text-center"></td>
                                                <td class="  text-center"></td>
                                            </tr>
                                            <tr class="even">
                                                <td>ABV32130</td>
                                                <td>COMPUTACIÓN COGNITIVA PARA BIG DATA</td>
                                                <td class="text-center sorting_1">1</td>
                                                <td class="  text-center">2</td>
                                                <td class="  text-center">4,90</td>
                                                <td class="  text-center"></td>
                                                <td class="  text-center"></td>
                                            </tr>
                                            <tr class="odd">
                                                <td>ABV32190</td>
                                                <td>ALGORITMOS Y VISUALIZACIÓN DE DATOS</td>
                                                <td class="text-center sorting_1">2</td>
                                                <td class="  text-center">2</td>
                                                <td class="  text-center"></td>
                                                <td class="  text-center">Pendiente</td>
                                                <td class="  text-center"></td>
                                            </tr>
                                            <tr class="even">
                                                <td>ABV32180</td>
                                                <td>PARADIGMAS PARA EL ALMACENAMIENTO Y PROCESAMIENTO DE BIG DATA</td>
                                                <td class="text-center sorting_1">2</td>
                                                <td class="  text-center">1</td>
                                                <td class="  text-center"></td>
                                                <td class="  text-center">Pendiente</td>
                                                <td class="  text-center"></td>
                                            </tr>
                                            <tr class="odd">
                                                <td>ABV32160</td>
                                                <td>OPCIÓN DE GRADO</td>
                                                <td class="text-center sorting_1">2</td>
                                                <td class="  text-center">12</td>
                                                <td class="  text-center"></td>
                                                <td class="  text-center">Pendiente</td>
                                                <td class="  text-center"></td>
                                            </tr>
                                            <tr class="even">
                                                <td>ABV32170</td>
                                                <td>MÉTODOS ESTADÍSTICOS Y ALGORITMOS PARA EL ANÁLISIS DE DATOS</td>
                                                <td class="text-center sorting_1">2</td>
                                                <td class="  text-center">1</td>
                                                <td class="  text-center"></td>
                                                <td class="  text-center">Pendiente</td>
                                                <td class="  text-center"></td>
                                            </tr>
                                            <tr class="odd">
                                                <td>ABV32140</td>
                                                <td>ELEC PROF: ADQUISICIÓN DE DATOS DAQ OR DAS IOT</td>
                                                <td class="text-center sorting_1">2</td>
                                                <td class="  text-center">2</td>
                                                <td class="  text-center"></td>
                                                <td class="  text-center">Pendiente</td>
                                                <td class="  text-center"></td>
                                            </tr>
                                            <tr class="even">
                                                <td>ABV32150</td>
                                                <td>ELEC PROF: CIENCIA DE DATOS PARA LA TOMA DE DECISIONES ESTRATÉGICAS</td>
                                                <td class="text-center sorting_1">2</td>
                                                <td class="  text-center">2</td>
                                                <td class="  text-center"></td>
                                                <td class="  text-center">Pendiente</td>
                                                <td class="  text-center"></td>
                                            </tr>
                                        </tbody>

                                    </table>
                                    <div class="dataTables_info" id="materias_1_info" role="status" aria-live="polite">Mostrando registros del 1 al 12 de un total de 12 registros</div>
                                    <div class="dataTables_paginate paging_simple_numbers" id="materias_1_paginate"><a class="paginate_button previous disabled" aria-controls="materias_1" data-dt-idx="0" tabindex="-1" id="materias_1_previous">Anterior</a><span><a class="paginate_button current" aria-controls="materias_1" data-dt-idx="1" tabindex="0">1</a></span><a class="paginate_button next disabled" aria-controls="materias_1" data-dt-idx="2" tabindex="-1" id="materias_1_next">Siguiente</a></div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div> --}}

        </div>



        <div class="container-fluid">
            <div class="container mt-3">
                <div class="row py-5" id="">
                    <?php var_dump(count($programas)); ?>
                    {{-- @for($i = 0; $i < count($programas); $i++)
                        <div class="col 4 text-center">
                            <a type="button" class="btn boton" onclick="consultaMalla('{{ $programas[$i]}}',{{ $estudiante->homologante}});">
                               {{ $programas[$i]}}
                            </a>
                        </div>
                    @endfor --}}
                    {{-- <div class="col 4 text-center">
                        <a type="button" class="btn boton" onclick="consultaMalla({{ $estudiante->homologante}});">
                            Malla curricular
                        </a>
                    </div>
                    <div class="col 4 text-center">
                        <a type="button" class="btn boton" onclick="consultaHistorial({{ $estudiante->homologante}});">
                            Historial academico
                        </a>
                    </div>
                    <div class="col 4 text-center">
                        <a type="button" class="btn boton" onclick="consultaProgramacion({{ $estudiante->homologante}});">
                            Programado
                        </a>
                    </div>
                    <div class="col 4 text-center">
                        <a type="button" class="btn boton" onclick="consultaPorVer({{ $estudiante->homologante}});">
                            Materias Por Ver
                        </a>
                    </div> --}}

                </div>
            </div>
        </div>
        {{-- <div class="container-fluid">
            <div class="container mt-3">
                <div class="row py-5" id="botones">
                    <div class="col 4 text-center">
                        <a type="button" class="btn boton" onclick="consultaMalla({{ $estudiante->homologante}});">
                            Malla curricular
                        </a>
                    </div>
                    <div class="col 4 text-center">
                        <a type="button" class="btn boton" onclick="consultaHistorial({{ $estudiante->homologante}});">
                            Historial academico
                        </a>
                    </div>
                    <div class="col 4 text-center">
                        <a type="button"class="btn boton" onclick="consultaProgramacion({{ $estudiante->homologante}});">
                            Programado
                        </a>
                    </div>
                    <div class="col 4 text-center">
                        <a type="button"class="btn boton" onclick="consultaPorVer({{ $estudiante->homologante}});">
                            Materias Por Ver
                        </a>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>
</div>
<script>
    function consultaMalla(programa,codBanner) {
        var formData = new FormData();
        formData.append('codBanner',codBanner);
        formData.append('programa', programa);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            url: "{{ route('historial.consultamalla') }}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(data) {
                /*data.forEach(malla => {
                    $('#contenido').append(renderMalla(malla));
                })*/
            }
        });
    }

</script>

