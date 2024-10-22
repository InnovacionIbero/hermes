<!-- incluimos el header para el html -->
@include('layout.header')

<!-- incluimos el menu -->
@include('menus.menu_admin')
<!--  creamos el contenido principal body -->

<style>
    #facultades {
        font-size: 14px;
    }

    #programas {
        font-size: 14px;
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


<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow" style="background-image: url('https://moocs.ibero.edu.co/hermes/front/public/assets/images/fondoCabecera.png');">

            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

            <div class="input-group">
                <div class="input-group-append">
                    <h3> Bienvenido {{auth()->user()->nombre}}</h3>
                </div>
            </div>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="text-center">
                <h1 class="h3 mb-0 text-gray-800"> <strong>Informe Facultad de {{$nombre}}</strong></h1>
            </div>
            <br>
            <div class="text-center" id="mensaje">
                <h4>Aquí puedes visualizar el informe completo de {{$nombre}}, por defecto visualizas toda la
                    información de la facultad, si quieres ver algunos programas en específico, seleccionalos.
                </h4>
            </div>
            <br>

            <!-- Checkbox Facultades -->
            <div class="row justify-content-start" id="">
                <div class="col-6 text-start">
                    <div class="card shadow mb-5" id="cardProgramas">
                        <div class="card-header text-center">
                            <h5><strong>Seleccionar Programas</strong></h5>
                        </div>
                        <div class="card-body text-star" style="overflow: auto;">
                            <div name="programas" id="programas">
                                <label> <input type="checkbox" value="" id="mostrarTodos" checked> Ver Todo</label><br>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button class="btn" type="button" id="generarReporte">
                                Generar Reporte
                            </button>
                        </div>
                    </div>

                </div>
                <div class=" col-6 text-center" id="colEstudiantes">
                    <div class="card shadow mb-5" id="chartEstudiantes">
                        <div class="card-header">
                            <h5 class="facultadtitulos"><strong>Estudiantes {{$nombre}}</strong></h5>
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


        </div>

        <div class="row justify-content-start mt-5">
            <div class="col-6 text-center" id="colSelloFinanciero">
                <div class="card shadow mb-6 graficos">
                    <div class="card-header">
                        <h5 class="facultadtitulos"><strong>Sello finaciero {{$nombre}}</strong></h5>
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
                        <h5 class="facultadtitulos"><strong>Con Sello de Retención (ASP) {{$nombre}}</strong></h5>
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
                        <h5 class="facultadtitulos"><strong>Estudiantes primer ingreso con tipos de sellos {{$nombre}}</strong></h5>
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
                        <h5 class="facultadtitulos"><strong>Tipos de estudiantes {{$nombre}}</strong></h5>
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
                        <h5 class="facultadtitulos"><strong>Operadores {{$nombre}}</strong></h5>
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
                        <h5 class="facultadtitulos"><strong>Programas con mayor cantidad de admitidos {{$nombre}}</strong></h5>
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

    <script>
        traerProgramas();
        invocarGraficos();
        graficoProgramasFacultad();
        var totalSeleccionado

        /**
         * Método que trae los gráficos de la vista
         */
        function invocarGraficos() {
            graficoEstudiantes();
            grafioSelloFinanciero();
            graficoRetencion();
            graficoSelloPrimerIngreso();
            graficoTiposDeEstudiantes();
            graficoOperadores();
        }

        /**
         * Método que oculta todos los divs de los gráficos, antes de generar algún reporte
         */
        function ocultarDivs() {
            $('#colEstudiantes, #colSelloFinanciero, #colRetencion, #colPrimerIngreso, #colTipoEstudiantes, #colOperadores, #colProgramas').addClass('hidden');
        }

        /**
         * Método que cuenta la cantidad de programas de la facultad correspondiente
         */
        function Contador() {
            totalSeleccionado = $('#programas input[type="checkbox"]').length;
            totalSeleccionado -= 1;
        }

        /**
         * Método que trae los programas correspondientes a la facultad 
         */

        function traerProgramas() {
            var formData = new FormData();
            formData.append('idfacultad[]', "<?= $nombre ?>");
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: "{{ route('traer.programas') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(datos) {
                    try {
                        datos = jQuery.parseJSON(datos);
                    } catch {
                        datos = datos;
                    }
                    $.each(datos, function(key, value) {
                        $('#programas').append(`<label class="hidden todosProgramas"><input type="checkbox" id="" name="programa[]" value="${value.codprograma}"> ${value.nombre}</label><br>`);
                    });
                }
            })
        }

        /**
         * Método para destruir todos los gráficos
         */
        function destruirGraficos() {
            [chartEstudiantes, chartEstudiantesActivos, chartRetencion, chartSelloPrimerIngreso, chartTipoEstudiante, chartOperadores].forEach(chart => chart.destroy());
        }

        /**
         * Controlador del botón mostrarTodos
         */
        $('body').on('change', '#mostrarTodos', function() {
            if ($('#mostrarTodos').prop('checked')) {
                location.reload();
            } else {
                $('.todosProgramas').removeClass('hidden');
                destruirGraficos();
                ocultarDivs();
            }
        });

        function alerta() {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Debes seleccionar al menos un programa',
                confirmButtonColor: '#dfc14e',
            })
        }

        /**
         * Controlador botón generarReporte
         */
        var programasSeleccionados = [];
        $('#generarReporte').on('click', function(e) {
            e.preventDefault();
            if ($('#programas input[type="checkbox"]:checked').length > 0) {
                if ($('#programas input[type="checkbox"]:checked').length == totalSeleccionado) {
                    location.reload();
                }
                var checkboxesProgramas = $('#programas input[type="checkbox"]:checked');
                programasSeleccionados = [];
                checkboxesProgramas.each(function() {
                    programasSeleccionados.push($(this).val());
                });
                graficosporPrograma();
            } else {
                programasSeleccionados = [];
                destruirGraficos();
                ocultarDivs();
                alerta();
            }
        });

        function graficosporPrograma() {
            if (chartEstudiantes || chartEstudiantesActivos || chartRetencion || chartSelloPrimerIngreso ||
                chartTipoEstudiante || chartOperadores) {
                destruirGraficos();
                $(".facultadtitulos").hide();
                $(".programastitulos").show();
                $("#ocultarGraficoProgramas").hide();

                invocarGraficos();
            }
        }

        /** 
         * Método que muestra los estudiantes activos e inactivos de algún programa en específico
         */
        var chartEstudiantes;

        function graficoEstudiantes() {
            var data;
            var url;
            if (programasSeleccionados != undefined) {
                if (programasSeleccionados.length > 0) {
                    url = "{{ route('estudiantes.activos.programa') }}";
                    data = {
                        programa: programasSeleccionados,
                    }
                }
            } else {
                url = "{{ route('estudiantes.activos.facultad') }}";
                data = {
                    idfacultad: ["<?= $nombre ?>"]
                }
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: url,
                data: data,
                success: function(data) {
                    data = jQuery.parseJSON(data);
                    var labels = data.data.map(function(elemento) {
                        return elemento.estado;
                    });
                    var valores = data.data.map(function(elemento) {
                        return elemento.TOTAL;
                    });
                    // Crear el gráfico circular
                    var ctx = document.getElementById('estudiantes').getContext('2d');
                    chartEstudiantes = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: labels.map(function(label, index) {
                                label = label.toUpperCase();
                                return label + 'S: ' + valores[index];
                            }),
                            datasets: [{
                                label: 'Gráfico Circular',
                                data: valores,
                                backgroundColor: ['rgba(223, 193, 78, 1)', 'rgba(74, 72, 72, 1)']
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            plugins: {
                                datalabels: {
                                    formatter: function(value, context) {
                                        return value;
                                    },
                                },
                                labels: {
                                    render: 'percenteaje',
                                    size: '14',
                                    fontStyle: 'bolder',
                                    position: 'outside',
                                    textMargin: 6
                                },
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20,
                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            },
                        },
                        plugin: [ChartDataLabels]
                    });
                    if (chartEstudiantes.data.labels.length == 0 && chartEstudiantes.data.datasets[0].data.length == 0) {
                        $('#colEstudiantes').addClass('hidden');
                    } else {
                        $('#colEstudiantes').removeClass('hidden');
                    }
                }
            });
        }

        /**
         * Método que genera el gráfico de sello financiero de algún programa en específico
         */
        var chartEstudiantesActivos;

        function grafioSelloFinanciero() {
            var data;
            var url;
            if (programasSeleccionados != undefined) {
                if (programasSeleccionados.length > 0) {
                    url = "{{ route('estudiantes.sello.programa') }}";
                    data = {
                        programa: programasSeleccionados,
                    }
                }
            } else {
                url = "{{ route('estudiantes.sello.facultad') }}";
                data = {
                    idfacultad: ["<?= $nombre ?>"]
                }
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: url,
                data: data,
                success: function(data) {
                    data = jQuery.parseJSON(data);
                    var labels = data.data.map(function(elemento) {
                        return elemento.sello;
                    });
                    var valores = data.data.map(function(elemento) {
                        return elemento.TOTAL;
                    });
                    // Crear el gráfico circular
                    var ctx = document.getElementById('activos').getContext('2d');
                    chartEstudiantesActivos = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: labels.map(function(label, index) {
                                if (label == 'NO EXISTE') {
                                    label = 'SIN SELLO';
                                }
                                label = label.toUpperCase();
                                return label + ': ' + valores[index];
                            }),
                            datasets: [{
                                label: 'Gráfico Circular',
                                data: valores,
                                backgroundColor: ['rgba(223, 193, 78, 1)', 'rgba(74, 72, 72, 1)', 'rgba(56,101,120,1)']
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            plugins: {
                                datalabels: {
                                    formatter: function(value, context) {
                                        return value;
                                    },
                                },
                                labels: {
                                    render: 'percenteaje',
                                    size: '14',
                                    fontStyle: 'bolder',
                                    position: 'outside',
                                    textMargin: 6
                                },
                                legend: {
                                    position: 'right',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20,
                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            },
                        },
                        plugin: [ChartDataLabels]
                    });
                    if (chartEstudiantesActivos.data.labels.length == 0 && chartEstudiantesActivos.data.datasets[0].data.length == 0) {
                        $('#colSelloFinanciero').addClass('hidden');
                    } else {
                        $('#colSelloFinanciero').removeClass('hidden');
                    }
                }
            });
        }

        /**
         * Método que genera el gráfico ASP de algún programa en específico
         */
        var chartRetencion;

        function graficoRetencion() {
            var data;
            var url;
            if (programasSeleccionados != undefined) {
                if (programasSeleccionados.length > 0) {
                    url = "{{ route('estudiantes.retencion.programa') }}";
                    data = {
                        programa: programasSeleccionados,
                    }
                }
            } else {
                url = "{{ route('estudiantes.retencion.facultad') }}";
                data = {
                    idfacultad: ["<?= $nombre ?>"]
                }
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: url,
                data: data,
                success: function(data) {
                    data = jQuery.parseJSON(data);

                    var labels = data.data.map(function(elemento) {
                        return elemento.autorizado_asistir;
                    });
                    var valores = data.data.map(function(elemento) {
                        return elemento.TOTAL;
                    });
                    // Crear el gráfico circular
                    var ctx = document.getElementById('retencion').getContext('2d');
                    chartRetencion = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: labels.map(function(label, index) {
                                if (label == '') {
                                    label = 'NO AUTORIZADO A PLATAFORMA'
                                }
                                label = label.toUpperCase();
                                return label + ': ' + valores[index];
                            }),
                            datasets: [{
                                label: 'Gráfico Circular',
                                data: valores,
                                backgroundColor: ['rgba(223, 193, 78, 1)', 'rgba(74, 72, 72, 1)', 'rgba(56,101,120,1)']
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            plugins: {
                                datalabels: {
                                    formatter: function(value, context) {
                                        return value;
                                    },
                                },
                                labels: {
                                    render: 'percenteaje',
                                    size: '14',
                                    fontStyle: 'bolder',
                                    position: 'outside',
                                    textMargin: 6
                                },
                                legend: {
                                    position: 'right',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20,
                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            },
                        },
                        plugin: [ChartDataLabels]
                    });
                    if (chartRetencion.data.labels.length == 0 && chartRetencion.data.datasets[0].data.length == 0) {
                        $('#colRetencion').addClass('hidden');
                    } else {
                        $('#colRetencion').removeClass('hidden');
                    }
                }
            });
        }

        /**
         * Método que genera el gráfico del sello financiero de los estudiantes de primer ingreso de algún programa en específico
         */
        var chartSelloPrimerIngreso;

        function graficoSelloPrimerIngreso() {
            var data;
            var url;
            if (programasSeleccionados != undefined) {
                if (programasSeleccionados.length > 0) {
                    url = "{{ route('estudiantes.primerIngreso.programa') }}";
                    data = {
                        programa: programasSeleccionados,
                    }
                }
            } else {
                url = "{{ route('estudiantes.primerIngreso.facultad') }}";
                data = {
                    idfacultad: ["<?= $nombre ?>"]
                }
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: url,
                data: data,
                success: function(data) {
                    data = jQuery.parseJSON(data);

                    var labels = data.data.map(function(elemento) {
                        return elemento.sello;
                    });

                    var valores = data.data.map(function(elemento) {
                        return elemento.TOTAL;
                    });
                    // Crear el gráfico circular
                    var ctx = document.getElementById('primerIngreso').getContext('2d');
                    chartSelloPrimerIngreso = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: labels.map(function(label, index) {
                                if (label == 'NO EXISTE') {
                                    label = 'SIN SELLO';
                                }
                                label = label.toUpperCase();
                                return label + ': ' + valores[index];
                            }),
                            datasets: [{
                                label: 'Gráfico Circular',
                                data: valores,
                                backgroundColor: ['rgba(223, 193, 78, 1)', 'rgba(74, 72, 72, 1)', 'rgba(56,101,120,1)']
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            plugins: {
                                datalabels: {
                                    formatter: function(value, context) {
                                        return value;
                                    },
                                },
                                labels: {
                                    render: 'percenteaje',
                                    size: '14',
                                    fontStyle: 'bolder',
                                    position: 'outside',
                                    textMargin: 6
                                },
                                legend: {
                                    position: 'right',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20,
                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            },

                        },
                        plugin: [ChartDataLabels]
                    });
                    if (chartSelloPrimerIngreso.data.labels.length == 0 && chartSelloPrimerIngreso.data.datasets[0].data.length == 0) {
                        $('#colPrimerIngreso').addClass('hidden');
                    } else {
                        $('#colPrimerIngreso').removeClass('hidden');
                    }
                }
            });
        }

        /**
         * Método que genera el gráfico con los tipos de estudiante por programa
         */
        var chartTipoEstudiante;

        function graficoTiposDeEstudiantes() {
            var data;
            var url;
            if (programasSeleccionados != undefined) {
                if (programasSeleccionados.length > 0) {
                    url = "{{ route('estudiantes.tipo.programa') }}";
                    data = {
                        programa: programasSeleccionados,
                    }
                }
            } else {
                url = "{{ route('estudiantes.tipo.facultad') }}";
                data = {
                    idfacultad: ["<?= $nombre ?>"]
                }
            }
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: url,
                data: data,
                success: function(data) {
                    data = jQuery.parseJSON(data);

                    var labels = data.data.map(function(elemento) {
                        return elemento.tipoestudiante;
                    });

                    var valores = data.data.map(function(elemento) {
                        return elemento.TOTAL;
                    });
                    // Crear el gráfico circular
                    var ctx = document.getElementById('tipoEstudiante').getContext('2d');
                    chartTipoEstudiante = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: labels.map(function(label, index) {
                                label = label.toUpperCase();
                                return label + ': ' + valores[index];
                            }),
                            datasets: [{
                                label: 'Gráfico Circular',
                                data: valores,
                                backgroundColor: ['rgba(223, 193, 78, 1)', 'rgba(74, 72, 72, 1)', 'rgba(56,101,120,1)']
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            plugins: {
                                datalabels: {
                                    formatter: function(value, context) {
                                        return value;
                                    },
                                },
                                labels: {
                                    render: 'percenteaje',
                                    size: '14',
                                    fontStyle: 'bolder',
                                    position: 'outside',
                                    textMargin: 6
                                },
                                legend: {
                                    position: 'right',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20,
                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            },

                        },
                        plugin: [ChartDataLabels]
                    });
                    if (chartTipoEstudiante.data.labels.length == 0 && chartTipoEstudiante.data.datasets[0].data.length == 0) {
                        $('#colTipoEstudiantes').addClass('hidden');
                    } else {
                        $('#colTipoEstudiantes').removeClass('hidden');
                    }
                }
            });
        }

        /**
         * Método que genera el gráfico de los 5 operadores que mas estudiantes traen por facultad
         */
        var chartOperadores;

        function graficoOperadores() {
            var data;
            var url;
            if (programasSeleccionados != undefined) {
                if (programasSeleccionados.length > 0) {
                    url = "{{ route('estudiantes.operador.programa') }}";
                    data = {
                        programa: programasSeleccionados,
                    }
                }
            } else {

                url = "{{ route('estudiantes.operador.facultad') }}";
                data = {
                    idfacultad: ["<?= $nombre ?>"]
                }
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: url,
                data: data,
                success: function(data) {
                    data = jQuery.parseJSON(data);

                    var labels = data.data.map(function(elemento) {
                        return elemento.operador;
                    });

                    var valores = data.data.map(function(elemento) {
                        return elemento.TOTAL;
                    });
                    var ctx = document.getElementById('operadores').getContext('2d');
                    chartOperadores = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels.map(function(label, index) {
                                if (label == '') {
                                    label = 'IBERO';
                                }
                                return label + ': ' + valores[index];
                            }),
                            datasets: [{
                                label: 'Operadores con mayor cantidad de estudiantes',
                                data: valores,
                                backgroundColor: ['rgba(74, 72, 72, 1)', 'rgba(223, 193, 78, 1)', 'rgba(208,171,75, 1)',
                                    'rgba(186,186,186,1)', 'rgba(56,101,120,1)', 'rgba(229,137,7,1)'
                                ]
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {

                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            },
                        },
                        plugin: [ChartDataLabels]
                    });
                    if (chartOperadores.data.labels.length == 0 && chartOperadores.data.datasets[0].data.length == 0) {
                        $('#colOperadores').addClass('hidden');
                    } else {
                        $('#colOperadores').removeClass('hidden');
                    }
                }
            });
        }

        /**
         * Método que genera el gráfico de los 5 programas con mas estudiantes inscritos por facultad
         */
        var chartProgramas;

        function graficoProgramasFacultad() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: "{{ route('programas.estudiantes.facultad') }}",
                data: {
                    idfacultad: ["<?= $nombre ?>"]
                },
                beforeSend: function() {
                    // Deshabilitar los checkboxes antes de la solicitud AJAX
                    $('div #facultades input[type="checkbox"]').prop('disabled', true);
                },
                success: function(data) {
                    data = jQuery.parseJSON(data);

                    var labels = data.data.map(function(elemento) {
                        return elemento.codprograma;
                    });
                    var valores = data.data.map(function(elemento) {
                        return elemento.TOTAL;
                    });
                    var ctx = document.getElementById('estudiantesProgramas').getContext('2d');
                    chartProgramas = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels.map(function(label, index) {
                                return label + ': ' + valores[index];
                            }),
                            datasets: [{
                                label: 'Operadores con mayor cantidad de estudiantes',
                                data: valores,
                                backgroundColor: ['rgba(74, 72, 72, 1)', 'rgba(223, 193, 78, 1)', 'rgba(208,171,75, 1)',
                                    'rgba(186,186,186,1)', 'rgba(56,101,120,1)', 'rgba(229,137,7,1)'
                                ]
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {

                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            },
                        },
                        plugin: [ChartDataLabels]
                    });
                    if (chartProgramas.data.labels.length == 0 && chartProgramas.data.datasets[0].data.length == 0) {
                        $('#colProgramas').addClass('hidden');
                    } else {
                        $('#colProgramas').removeClass('hidden');
                    }
                }
            });
        }

        $('#botonModalOperador').on("click", function(e) {
            e.preventDefault();
            if (chartOperadoresTotal) {
                chartOperadoresTotal.destroy();
            }
            graficoOperadoresTotal();
        });

        $('#botonModalProgramas').on("click", function(e) {
            e.preventDefault();
            if (chartProgramasTotal) {
                chartProgramasTotal.destroy();
            }
            graficoProgramasTotal();
        });

        /**
         * Método que trae todos los operadores de la Facultad
         */
        var chartOperadoresTotal;

        function graficoOperadoresTotal() {
            var data;
            var url;
            if (programasSeleccionados.length > 0) {
                url = "{{ route('operadores.programa.estudiantes') }}";
                data = {
                    programa: programasSeleccionados,
                }
            } else {
        
                url = "{{ route('operadores.facultad.estudiantes') }}";
                data = {
                    idfacultad: ["<?= $nombre ?>"]
                }
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: url,
                data: data,
                success: function(data) {
                    data = jQuery.parseJSON(data);

                    var labels = data.data.map(function(elemento) {
                        return elemento.operador;
                    });
                    var valores = data.data.map(function(elemento) {
                        return elemento.TOTAL;
                    });
                    // Crear el gráfico de barras
                    var ctx = document.getElementById('operadoresTotal').getContext('2d');
                    chartOperadoresTotal = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels.map(function(label, index) {
                                if (label == '') {
                                    label = 'IBERO';
                                }
                                return label + ': ' + valores[index];
                            }),
                            datasets: [{
                                label: 'Operadores ordenados de forma descendente',
                                data: valores,
                                backgroundColor: ['rgba(74, 72, 72, 1)', 'rgba(223, 193, 78, 1)', 'rgba(208,171,75, 1)',
                                    'rgba(186,186,186,1)', 'rgba(56,101,120,1)', 'rgba(229,137,7,1)'
                                ]
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {

                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            },
                        },
                        plugin: [ChartDataLabels]
                    });
                }
            });

        }

        /**
         * Método que trae todos los programas de la Facultad
         */
        var chartProgramasTotal;

        function graficoProgramasTotal() {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: "{{ route('programasTotal.estudiantes') }}",
                data: {
                    idfacultad: ["<?= $nombre ?>"]
                },
                success: function(data) {
                    data = jQuery.parseJSON(data);
                    var labels = data.data.map(function(elemento) {
                        return elemento.codprograma;
                    });
                    var valores = data.data.map(function(elemento) {
                        return elemento.TOTAL;
                    });
                    // Crear el gráfico circular
                    var ctx = document.getElementById('programasTotal').getContext('2d');
                    chartProgramasTotal = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels.map(function(label, index) {
                                return label + ': ' + valores[index];
                            }),
                            datasets: [{
                                label: 'Programas',
                                data: valores,
                                backgroundColor: ['rgba(74, 72, 72, 1)', 'rgba(223, 193, 78, 1)', 'rgba(208,171,75, 1)',
                                    'rgba(186,186,186,1)', 'rgba(56,101,120,1)', 'rgba(229,137,7,1)'
                                ]
                            }]
                        },
                        options: {
                            maintainAspectRatio: false,
                            responsive: true,

                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {

                                        font: {
                                            size: 12
                                        }
                                    }
                                }
                            },
                        },
                        plugin: [ChartDataLabels]
                    });
                }
            });
        }
    </script>

    <!-- incluimos el footer -->
    @include('layout.footer')
</div>