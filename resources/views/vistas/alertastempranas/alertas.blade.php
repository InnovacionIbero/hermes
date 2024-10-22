<!-- incluimos el header para el html -->
@include('layout.header')

<!-- incluimos el menu -->
@auth
    @include('menus.menu')    
@endauth
<link rel="stylesheet" href="{{ asset('css/appalertas.css') }}">
<script>
    tabla = 'planeacion';
</script>
<script src="{{ asset('js/alerta.js') }}"></script> 
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow" style="background-image: url('https://moocs.ibero.edu.co/hermes/front/public/assets/images/fondoCabecera.png');">

            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

            <div class="input-group">
                <div class="input-group-append">
                    <h3> Bienvenido {{ auth()->user()->nombre }}</h3>
                </div>
            </div>
        </nav>

        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                {{-- <h1 class="h3 mb-0 text-gray-800">Malla curricular del programa {{$nombre}}</h1> --}}
                {{-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> --}}
            </div>

            <!-- Content Row -->
            @include('layout.filtrosalertas')   
            

            <div class="row d-flex align-items-center mt-3">
                <div class="col text-center" id="colAlertas">
                    <div class="card shadow mb-4" style="min-height: 450px; max-height: 450px;">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-2"></div>
                                <div class="col-8 d-flex align-items-center justify-content-center">
                                    <h5 id="tituloAlertas"><strong>Alertas por programa</strong></h5>
                                    <h5 class="tituloPeriodo"><strong></strong></h5>
                                </div>
                                <div class="col-2 text-right">
                                    <span data-toggle="tooltip" title="Muestra la cantidad de alertas activas por programa" data-placement="right">
                                        <button type="button" class="btn btn-warning" data-toggle="tooltip" data-placement="bottom"><i class="fa-solid fa-circle-question"></i></button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <canvas id="graficoAlertas"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4 hidden" id="colTabla">
                <div class="col-3 mt-3">
                    <label for="selectAlertas" class="col-form-label">
                        <h5><strong> Filtrar por tipo de alerta </strong></h5>
                    </label>
                    <select class="form-control" name="selectAlertas" id="selectAlertas">
                        <option value="todos">Todos</option>
                    </select>
                </div>
                <hr>
                <!-- Card Body -->
                <div class="card-body">
                    <!--Datatable-->
                    <div class="table">
                        <table id="datatable" class="display" style="width:100%">
                        </table>
                    </div>
                </div>
                <br>
            </div>
        </div>
</div>
<script src="{{ asset('js/filtrospruebas.js') }}"></script>
 <script>
    $(document).ready(function() {
        $('#menuAlertas').addClass('activo');
        let url_periodo = @json(route('periodos.activos'));
        // descomentar para que funcione normal
        var tipo = '';

        filtros(url_periodo);
        tipoAlertas();

        //alertaPrepPlaneacion();
        function alertaPrepPlaneacion() {
            Swal.fire({
                icon: "info",
                title: "Planeación en proceso",
                text: "Apreciado usuario, el módulo de planeación no mostrará información a partir del 24 de septiembre, puesto que se están actualizando los datos para la proyección de P4-P5-2024. Los datos estarán disponibles a partir del 26 de septiembre de 2024.",
                showConfirmButton: true,
            });
        }

        setTimeout(function() {
            $('#generarReporte').click();
        }, 2000);

        $('#generarReporte').on('click', function(e) {
            contador_tabla_cursos = 0;
            tipo = '';
            //--- trae los periodos  seleccionados
            var periodosSeleccionados = [];
            var checkboxesSeleccionados = $('#Continua, #Pregrado, #Esp, #Maestria').find('input[type="checkbox"]:checked');

            //-- verificamos que tenga informacion
            if(checkboxesSeleccionados.length > 0){
                checkboxesSeleccionados.each(function() {
                    periodosSeleccionados.push($(this).val());
                });
            }
            
            //--- se verifica que tenga elmenos 1 periodo seleccionado 
            if (periodosSeleccionados.length > 0) {
                filtro=[];
                //--- traemos los campos seleccionados de programas y facultades 
                var checkboxesProgramas = $('#programas input[type="checkbox"]:checked');
                
                var checkboxesfacultades = $('#facultades input[type="checkbox"]:checked');

                //--- primero verificamos si hay algun programa marcado
                if(checkboxesProgramas.length > 0){
                    programasSeleccionados = [];

                    

                    checkboxesProgramas.each(function() {
                        programasSeleccionados.push($(this).val());
                    });
                    //--- guardamos los datos en un array para enviarlo a las funciones necesarias
                    filtro['programa']=programasSeleccionados;
                    filtro['periodos']=periodosSeleccionados;
                
                //--- si no hay programa marcado verificamos la facultad
                }else if (checkboxesfacultades.length > 0){
                    facultadesSeleccionadas = [];
                    checkboxesfacultades.each(function() {
                        facultadesSeleccionadas.push($(this).val());
                    });
                    
                    //--- guardamos los datos en un array para enviarlo a las funciones necesarias

                    // filtro['programa']=programasSeleccionados;
                    filtro['facultades']=facultadesSeleccionadas;
                    filtro['periodos']=periodosSeleccionados;
                
                }
        
            } else {
                //--- si no tiene periodos seleccionados  mandamos la alerta
                alerta_seleccione_periodo();
            }
            graficoAlertas(filtro);
            destruirTable();
            dataTable(filtro);
        });

        const buscador = $('#buscadorProgramas');
        const listaProgramas = $('.listaProgramas');
        const divProgramas = $('#programas');

        buscador.on('input', function() {
            $('#programas input[type="checkbox"]').prop("checked", false);
            $('#todosPrograma').prop("checked", false);
            var query = $(this).val().toLowerCase();
            divProgramas.find('li').each(function() {
                var label = $(this);
                var etiqueta = label.text().toLowerCase();
                var $checkbox = label.find('input[type="checkbox"]');

                if (etiqueta.includes(query)) {
                    label.removeClass('d-none');

                } else {
                    label.addClass('d-none');

                }
            });
        });
    
    var chartAlertas;
    function graficoAlertas(filtros){
        if (chartAlertas) {
            chartAlertas.destroy();
        }
        var url, data;
            if (filtros.programa && filtros.programa.length > 0) {
                url = "{{ route('alertas.grafico.programa') }}",
                data = {
                    programas: filtros.programa,
                    periodos: filtros.periodos
                }
            } else if (filtros.facultades && filtros.facultades.length > 0) {
                url = "{{ route('alertas.grafico.facultad') }}",
                data = {
                    facultad: filtros.facultades,
                    periodos: filtros.periodos
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
                    try {
                        data = jQuery.parseJSON(data);
                    } catch {
                        data = data;
                    }
                    

                    var labels = data.data.map(function(elemento) {
                        return elemento.codprograma;
                    });
                    var valores = data.data.map(function(elemento) {
                        return elemento.TOTAL;
                    });
                    var maxValor = Math.max(...valores);
                    var maxValorAux = Math.ceil(maxValor / 1000) * 1000;
                    var yMax;
                    if (maxValor < 50) {
                        yMax = 100;
                    } else if (maxValor < 100) {
                        yMax = 120;
                    } else if (maxValor < 500) {
                        yMax = 100 * Math.ceil(maxValor / 100) + 100;
                    } else if (maxValor < 1000) {
                        yMax = 100 * Math.ceil(maxValor / 100) + 200;
                    } else {
                        var maxValorAux = 1000 * Math.ceil(maxValor / 1000);
                        yMax = (maxValorAux - maxValor) < 600 ? maxValorAux + 1000 : maxValorAux;
                    }
                    // Crear el gráfico de barras
                    var ctx = document.getElementById('graficoAlertas').getContext('2d');
                    chartAlertas = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: valores,
                                backgroundColor: ['rgba(74, 72, 72, 1)', 'rgba(223, 193, 78, 1)', 'rgba(208,171,75, 1)',
                                    'rgba(186,186,186,1)', 'rgba(56,101,120,1)', 'rgba(229,137,7,1)'
                                ],
                                datalabels: {
                                    anchor: 'end',
                                    align: 'top',
                                }
                            }]
                        },
                        options: {
                            scales: {
                                y: {
                                    max: yMax,
                                    beginAtZero: true
                                }
                            },
                            maintainAspectRatio: false,
                            responsive: true,
                            plugins: {
                                datalabels: {
                                    color: 'black',
                                    font: {
                                        weight: 'semibold'
                                    },
                                    formatter: Math.round
                                },
                                legend: {
                                    display: false,
                                }
                            },
                        },
                        plugins: [ChartDataLabels]
                    });
                    if (chartAlertas.data.labels.length == 0 && chartAlertas.data.datasets[0].data.length == 0) {
                        $('#colAlertas').addClass('hidden');
                    } else {
                        $('#colAlertas').removeClass('hidden');
                    }
                    swal.close();
                }
            });
    }

    function dataTable(filtros) {

        Swal.fire({
            imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false
        });

        $('#colTabla').removeClass('hidden');
        var url, data;
        var table;
        if (filtros.programa && filtros.programa.length > 0) {
            url = "{{ route('alertas.tabla.programa') }}",
            data = {
                programas: filtros.programa,
                periodos: filtros.periodos,
                tipo: tipo,
            }
        } else if (filtros.facultades && filtros.facultades.length > 0) {
            url = "{{ route('alertas.tabla.facultad') }}",
            data = {
                facultad: filtros.facultades,
                periodos: filtros.periodos,
                tipo: tipo,
            }
        }
        var datos = $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            url: url,
            data: data,
            success: function(data) {
                try {
                    data = parseJSON(data);
                } catch {
                    data = data;
                }
                table = $('#datatable').DataTable({
                    "data": data,
                    buttons: [
                        'copy', 'csv', 'excel', 'pdf', 'print'
                    ],
                    'pageLength': 10,
                    "order": [2, 'desc'],
                    "dom": 'Bfrtip',
                    "buttons": [
                        'copy', 'excel', 'pdf', 'print'
                    ],
                    "columns": [{
                            title: 'Codigo Banner',
                            data: 'idbanner',
                        },
                        {
                            title: 'Programa',
                            render: function(data, type, row) {
                                // esto es lo que se va a renderizar como html
                                return `<b>${row.codprograma}</b> - ${row.programa}`;
                            }
                        },
                        {
                            title: 'Tipo estudiante',
                            data: 'tipo_estudiante',
                        },
                        {
                            title: 'Periodo',
                            data: 'periodo',
                            className: 'dt-center'
                        },
                        {
                            title: 'Tipo alerta',
                            data: 'tipo',
                        },
                        {
                            title: 'Descripción',
                            data: 'desccripcion',
                        },
                        {
                            title: 'Fecha creación',
                            data: 'created_at',
                        },
                        {
                            data: 'activo',
                            defaultContent: "",
                            title: 'Inactivar / Activar',
                            className: "text-center",
                            render: function(data, type, row) {
                                if (data == '1') {
                                    return "<button class='inactivar btn btn-success' type='button' id='boton'><i class='fa-regular fa-eye-slash'></i></button>";
                                } else if (data == '0') {
                                    return "<button class='inactivar btn btn-danger' type='button' id='boton'><i class='fa-regular fa-eye-slash'></i></button>";
                                }
                            }
                        }
                    ]
                });

                function obtener_data_inactivar(tbody, table) {
                    $(tbody).on("click", "button.inactivar", function(event) {
                        var data = table.row($(this).parents("tr")).data();
                        if (data.activo == 1) {
                            Swal.fire({
                                title: "¿Ya se ha resuelto la alerta temprana " + data.desccripcion + "?",
                                icon: 'warning',
                                showCancelButton: true,
                                showCloseButton: true,
                                cancelButtonColor: '#DC3545',
                                cancelButtonText: "No, Cancelar",
                                confirmButtonText: "Si"
                            }).then(result => {
                                if (result.value) {
                                    $.post("{{ route('alerta.resuelta') }}", {
                                            '_token': $('meta[name=csrf-token]').attr('content'),
                                            id: encodeURIComponent(window.btoa(data.id)),
                                        },
                                        function(result) {

                                            if (result == "deshabilitado") {
                                                Swal.fire({
                                                    title: "Alerta solucionada",
                                                    icon: 'info',
                                                    showCancelButton: true,
                                                    confirmButtonText: "Aceptar",
                                                }).then(result => {
                                                    if (result.value) {
                                                        location.reload();
                                                    };
                                                })
                                            }
                                        })
                                }
                            });
                        }
                    });
                }

                obtener_data_inactivar("#datatable tbody", table);
            }

        });
    }

    function destruirTable() {
        $('#colTabla').addClass('hidden');
        if ($.fn.DataTable.isDataTable('#datatable')) {
            $('#datatable').dataTable().fnDestroy();
            $('#datatable thead').empty();
            $('#datatable tbody').empty();
            $('#datatable tfooter').empty();
            $("#datatable tbody").off("click", "button.malla");
            $("#datatable tbody").off("click", "button.estudiantes");
        }
    }

    function tipoAlertas() {
        var datos = $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('tipos.alertas') }}",
            method: 'post',
            async: false,
            success: function(data) {
                data.forEach(data => {
                    $('#selectAlertas').append(`<option value = "${data.tipo}">${data.tipo}</option>)`);
                })
            }
        });
    }

    $('#selectAlertas').on('change', function() {
        tipo = $(this).val();
        destruirTable();
        dataTable(filtro);
        Swal.close();
    });
    })
</script>
@include('layout.footer')