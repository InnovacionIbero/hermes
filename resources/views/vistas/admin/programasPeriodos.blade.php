<!-- incluimos el header para el html -->
@include('layout.header')

<!-- incluimos el menu -->
@include('menus.menu')
<style>
    .card {
        display: flex;
        flex-direction: column;
        width: 100%;
    }

    .card-body {
        flex: 1;
        width: 100%;
    }

    #facultades {
        font-size: 14px;
    }

    #programas {
        font-size: 14px;
    }

    .center-chart {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
    }

    .button-informe {
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

    #btn-table {
        width: 60px;
    }

    #generarReporte {
        width: 250px;
        height: 45px;
        font-size: 20px;
    }

    .deshacer {
        background-color: #dfc14e;
        border-color: #dfc14e;
        color: white;
        width: 140px;
        height: 30px;
        border-radius: 10px;
        font-weight: 800;
        place-items: center;
        font-size: 12px;
    }

    .botonModal {
        display: flex;
        justify-content: center;
        align-items: center;
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
        min-height: 350px;
        max-height: 350px;
    }

    #cardNivel {
        background: #FFFFFF;
    }

    .card {
        margin-bottom: 3%;
    }

    .hidden {
        display: none;
    }


    .graficosRiesgo {
        min-height: 450px;
        max-height: 450px;
    }
</style>
<!--  creamos el contenido principal body -->

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
                    <h3> Bienvenido {{ auth()->user()->nombre }}</h3>
                </div>
                <div>
                    <input type="text" id="facultadEditar" value='' name="facultadEditar" hidden>
                </div>
            </div>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Programas activos por periodo</h1>
                {{-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> --}}
            </div>

            <!-- Content Row -->
            <div class="row">

                <!-- Area Chart -->
                <div class="col-xl-12 col-lg-12">
                    <div class="card shadow mb-4">
                        <!-- Card Body -->
                        <div class="card-body">
                            <div class="table">
                                <table id="example" class="display" style="width:100%">
                                </table>
                            </div>
                        </div>
                        <div class="col-4 justify-content-center">
                            <button href="#" class="agregar btn btn-secondary" data-toggle="modal" data-target="#nuevoprograma" data-whatever="modal">Agregar nueva regla</button>
                        </div>
                        <br>
                    </div>
                </div>
            </div>

            <!--Modal para agregar un programa nuevo-->
            <div class="modal fade" id="nuevoprograma" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Agregar nuevo programa en periodo</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="miForm" method="post" action="{{ route('programasPeriodos.agregar') }}">
                                @csrf
                                <div>
                                    <label for="recipient-name" class="col-form-label">Codigo del programa</label>
                                    <input type="text" class="form-control" id="codPrograma" name="codPrograma">
                                </div>
                                <div>
                                    <label for="periodos" class="col-form-label">Periodos</label>
                                    <select class="form-control" id="periodos" name="periodos" required>
                                        <option value="0">Selecciones los periodos</option>
                                        <option value="1">202404 - 202405 - 202406 - 202407 - 202408</option>
                                        <option value="2">202411 - 202412 - 202413 - 202416 - 202417</option>
                                        <option value="3">202431 - 202432 - 202433 - 202434 - 202435</option>
                                        <option value="4">202441 - 202442 - 202443 - 202444 - 202445</option>
                                        <option value="5">202451 - 202452 - 202453 - 202454 - 202455</option>
                                    </select>
                                </div>
                                <label for="ciclo" class="col-form-label">Ingresos activos</label>
                                <br>
                                <div class="form-check form-check-inline" id="ciclo">
                                    <input class="form-check-input" type="checkbox" value="1" id="Ingreso1" name="ingresos[]">
                                    <label class="form-check-label" for="Ingreso1"> Febrero </label>
                                    &nbsp
                                    <input class="form-check-input" type="checkbox" value="2" id="Ingreso2" name="ingresos[]">
                                    <label class="form-check-label" for="Ingreso2"> Abril </label>
                                    &nbsp
                                    <input class="form-check-input" type="checkbox" value="3" id="Ingreso3" name="ingresos[]">
                                    <label class="form-check-label" for="Ingreso3"> Junio </label>
                                    &nbsp
                                    <input class="form-check-input" type="checkbox" value="4" id="Ingreso4" name="ingresos[]">
                                    <label class="form-check-label" for="Ingreso4"> Agosto </label>
                                    &nbsp
                                    <input class="form-check-input" type="checkbox" value="5" id="Ingreso5" name="ingresos[]">
                                    <label class="form-check-label" for="Ingreso5"> Octubre </label>
                                </div>
                                <br>
                                <label for="ciclo" class="col-form-label">Plan</label>
                                <br>
                                <div class="form-check form-check-inline" id="ciclo">
                                    <input class="form-check-input" type="checkbox" value="P1" id="plan1" name="plan[]">
                                    <label class="form-check-label" for="plan1"> Plan 1 </label>
                                    &nbsp
                                    <input class="form-check-input" type="checkbox" value="P2" id="plan2" name="plan[]">
                                    <label class="form-check-label" for="plan2"> Plan 2</label>
                                </div>
                                <br>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="crear btn btn-success">Crear</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->


    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    @if(session('success'))
    <script>
        Swal.fire("Éxito", "{{ session('success') }}", "success");
    </script>
    @endif

    @if($errors->any())
    <script>
        Swal.fire("Error", "{{ $errors->first() }}", "error");
    </script>
    @endif

    <script>
        $('#menuPeridosProgramas').addClass('activo');
        $('#collapseConfig').addClass('collapse show');
        $(document).ready(function() {

        });
        var xmlhttp = new XMLHttpRequest();
        var url = "{{ route('programasPeriodos.getprogramasPeriodos') }}";
        xmlhttp.open("GET", url, true);
        xmlhttp.send();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var data = JSON.parse(this.responseText);
                var table = $('#example').DataTable({
                    "data": data.data,
                    "columns": [{
                            data: 'codPrograma',
                            title: 'Codigo de programa'
                        },
                        {
                            data:'programa',
                            title: 'Nombre del programa'
                        },
                        {
                            data:'periodo',
                            title:'Periodo',
                        },
                        {
                            data: 'plan',
                            title: 'Plan',
                        },
                        {
                            data: 'fecha_inicio',
                            title: 'Fecha Inicio',
                        },
                        {
                            data: 'estado',
                            defaultContent: "",
                            title: "Estado",
                            className: "text-center",
                            render: function(data, type, row) {
                                if (data == '1') {
                                    return 'Activo';
                                } else if (data == '0') {
                                    return 'Inactivo';
                                }
                            }
                        },
                        {
                            defaultContent: "<button type='button' id='editarbtn' class='editar btn btn-warning' data-toggle='modal' data-target='#editar_facultad' data-whatever='modal'><i class='fa-solid fa-pen-to-square'></i></button>",
                            title: 'Editar',
                            className: "text-center",
                        },
                        {
                            data: 'estado',
                            defaultContent: "",
                            title: 'Inactivar / Activar',
                            className: "text-center",
                            render: function(data, type, row) {
                                if (data == '1') {
                                    return "<button class='inactivar btn btn-success' type='button' id='boton'><i class='fa-solid fa-unlock'></i></button>";
                                } else if (data == '0') {
                                    return "<button class='inactivar btn btn-danger' type='button' id='boton'><i class='fa-solid fa-lock'></i></button>";
                                }
                            }
                        },
                    ],
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                    },
                    //lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                });

                function obtener_data_inactivar(tbody, table) {
                    $(tbody).on("click", "button.inactivar", function(event) {
                        var data = table.row($(this).parents("tr")).data();
                        if (data.estado == 1) {
                            Swal.fire({
                                title: "¿Desea inactivar el programa " + data.codPrograma + " para el periodo "+ data.periodo +" y plan " + data.plan +"?",
                                icon: 'warning',
                                showCancelButton: true,
                                showCloseButton: true,
                                cancelButtonColor: '#DC3545',
                                cancelButtonText: "No, Cancelar",
                                confirmButtonText: "Si"
                            }).then(result => {
                                if (result.value) {
                                    $.post("{{ route('programasPeriodos.inactivar') }}", {
                                            '_token': $('meta[name=csrf-token]').attr('content'),
                                            id: encodeURIComponent(window.btoa(data.id)),
                                        },
                                        function(result) {
                                            console.log(result);
                                            if (result == "deshabilitado") {
                                                Swal.fire({
                                                    title: "Programa desactivado en el periodo",
                                                    html: "El programa <strong>" + data.codPrograma +
                                                        " para el periodo "+ data.periodo +" y plan " + data.plan + "</strong> ha sido inactivado",
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

                        } else {
                            Swal.fire({
                                title: "¿Desea activar el programa " + data.codPrograma + " para el periodo "+ data.periodo +" y plan " + data.plan +"",
                                icon: 'warning',
                                showCancelButton: true,
                                showCloseButton: true,
                                cancelButtonColor: '#DC3545',
                                cancelButtonText: "No, Cancelar",
                                confirmButtonText: "Si"
                            }).then(result => {
                                if (result.value) {
                                    $.post("{{ route('programasPeriodos.activar') }}", {
                                            '_token': $('meta[name=csrf-token]').attr('content'),
                                            id: encodeURIComponent(window.btoa(data.id)),
                                        },
                                        function(result) {
                                            if (result == "habilitado") {
                                                Swal.fire({
                                                    title: "Programa activado en el periodo",
                                                    html: "El programa <strong>" + data.codPrograma +
                                                        " para el periodo "+ data.periodo +" y plan " + data.plan + "</strong> ha sido activado",
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

                function obtener_data_editar(tbody, table) {
                    $(tbody).on("click", "button.editar", function() {
                        var data = table.row($(this).parents("tr")).data();
                        var fechaActual = new Date();
                        var añoActual = fechaActual.getFullYear();
                        var fechaLimite = new Date(fechaActual.getFullYear() + 1, fechaActual.getMonth(), fechaActual.getDate());
                        var fechaLimiteISO = fechaLimite.toISOString().split('T')[0];
                        var añoSiguiente = añoActual + 1;
                        Swal.fire({
                            title: 'Actualizar información',
                            html: '<form>' +
                                '<label for="editcodigo" class="col-form-label">Codigo del programa</label>' +
                                '<input type="text" class="form-control" id="editcodigo" name="editcodigo" value="' + data.codPrograma + '">' +
                                '<label for="editcreditos" class="col-form-label">Número de créditos</label>' +
                                '<input type="number" class="form-control" id="editperiodo" name="editperiodo" value="' + data.periodo + '">' +
                                '<label for="editmaterias" class="col-form-label">Materias permitidas</label>' +
                                '<input type="text" class="form-control" id="editplan" name="editplan" value="' + data.plan + '">' +
                                '<label for="fecha1Edit"> Fecha de inicio </label>' +
                                '<input type="date" min="' + fechaActual.toISOString().split('T')[0] + '" max="' + fechaLimiteISO + '" id="fecha" name="fecha" value="' + data.fecha_inicio + '" class="form-control"> <br>' +
                                '</form>',
                            icon: 'info',
                            showCancelButton: true,
                            confirmButtonColor: '#3085d6',
                            cancelButtonColor: '#d33',
                            cancelButtonText: "Cancelar",
                            confirmButtonText: 'Editar'
                        }).then(result => {
                            if (result.isConfirmed) {
                                if (result.value) {

                                    $.post("{{ route('programasPeriodos.update')}}", {
                                            '_token': $('meta[name=csrf-token]').attr('content'),
                                            id: encodeURIComponent(window.btoa(data.id)),
                                            programa: $(document).find('#editcodigo').val(),
                                            periodo: $(document).find('#editperiodo').val(),
                                            plan: $(document).find('#editplan').val(),
                                            fecha: $(document).find('#fecha').val()
                                        },
                                        function(result) {
                                            if (result == "actualizado") {
                                                Swal.fire({
                                                    title: "Información actualizada",
                                                    icon: 'success'
                                                }).then(result => {
                                                    location.reload();
                                                });
                                            }
                                        }
                                    )
                                }
                            }
                        })
                    });
                }
                obtener_data_editar("#example tbody", table);
                obtener_data_inactivar("#example tbody", table);
            }
        }

        
    </script>

    @include('layout.footer');
</div>