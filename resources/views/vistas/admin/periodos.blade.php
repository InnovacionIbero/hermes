<!-- incluimos el header para el html -->
@include('layout.header')

<!-- incluimos el menu -->
@include('menus.menu')
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
            </div>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Periodos</h1>
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
                            <button href="#" class="agregar btn btn-secondary" data-toggle="modal" data-target="#nuevoperiodo" data-whatever="modal">Agregar nuevo periodo</button>
                        </div>
                        <br>
                    </div>
                </div>
            </div>

            <!--Modal para agregar un programa periodo-->
            <div class="modal fade" id="nuevoperiodo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Agregar nuevo periodo</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="miForm" method="post" action="{{ route('periodo.crear') }}">
                                <?php
                                $fechaactual = date('Y-m-d');
                                $fechalimite = date('Y-m-d', strtotime($fechaactual . ' +1 year'));
                                $añoactual = date('Y');
                                $añosiguiente = date('Y', strtotime('+1 year')); ?>
                                @csrf
                                <div>
                                    <label for="name" class="col-form-label">Periodo</label>
                                    <input type="text" class="form-control" id="name" name="name">
                                </div>
                                <div>
                                    <label for="ciclo1" class="col-form-label">Fecha inicio ciclo 1</label>
                                    <input type="date" min="<?php echo $fechaactual; ?>" max="<?php echo $fechalimite; ?>" class="form-control" id="ciclo1" name="ciclo1">
                                </div>
                                <div>
                                    <label for="ciclo1Fin" class="col-form-label">Fecha cierre ciclo 1</label>
                                    <input type="date" min="<?php echo $fechaactual; ?>" max="<?php echo $fechalimite; ?>" class="form-control" id="ciclo1Fin" name="ciclo1Fin">
                                </div>
                                <div>
                                    <label for="ciclo2" class="col-form-label">Fecha inicio ciclo 2</label>
                                    <input type="date" min="<?php echo $fechaactual; ?>" max="<?php echo $fechalimite; ?>" class="form-control" id="ciclo2" name="ciclo2">
                                </div>
                                <div>
                                    <label for="ciclo2Fin" class="col-form-label">Fecha cierre ciclo 2</label>
                                    <input type="date" min="<?php echo $fechaactual; ?>" max="<?php echo $fechalimite; ?>" class="form-control" id="ciclo2Fin" name="ciclo2Fin">
                                </div>
                                <div>
                                    <label for="ciclo1Proyeccion" class="col-form-label">Fecha primera proyeccion ciclo 1</label>
                                    <input type="date" min="<?php echo $fechaactual; ?>" max="<?php echo $fechalimite; ?>" class="form-control" id="ciclo1Proyeccion" name="ciclo1Proyeccion">
                                </div>
                                <div>
                                    <label for="ciclo2Proyeccion" class="col-form-label">Fecha primera proyeccion ciclo 2</label>
                                    <input type="date" min="<?php echo $fechaactual; ?>" max="<?php echo $fechalimite; ?>" class="form-control" id="ciclo2Proyeccion" name="ciclo2Proyeccion">
                                </div>
                                <div>
                                    <label for="periodo" class="col-form-label">Fecha inicio periodo</label>
                                    <input type="date" min="<?php echo $fechaactual; ?>" max="<?php echo $fechalimite; ?>" class="form-control" id="periodo" name="periodo">
                                </div>
                                <div>
                                    <label for="fecha" class="col-form-label">Año</label>
                                    <select id="fecha" name="fecha" class="form-control">
                                        <option value="<?php echo intval($añoactual); ?>"><?php echo $añoactual; ?></option>
                                        <option value="<?php echo intval($añoactual); ?>"><?php echo $añosiguiente; ?></option>
                                    </select>
                                </div>
                                <br>
                                <div>
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="crear btn btn-success">Crear</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

    </div>

</div>
<!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

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
    $('#menuPeriodos').addClass('activo');
    $('#collapseConfig').addClass('collapse show');

    // * Datatable para mostrar todas las Facultades *
    var xmlhttp = new XMLHttpRequest();
    var url = "{{ route('facultad.getperiodos') }}";
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(this.responseText);
            var table = $('#example').DataTable({
                "data": data.data,
                "columns": [{
                        data: 'periodos',
                        title: 'Periodo'
                    },
                    {
                        data: 'fechaInicioCiclo1',
                        title: 'Fecha de inicio ciclo 1'
                    },
                    {
                        data: 'fechaCierreCiclo1',
                        title: 'Fecha de cierre ciclo 1'
                    },
                    {
                        data: 'fechaInicioCiclo2',
                        title: 'Fecha de inicio ciclo 2'
                    },
                    {
                        data: 'fechaCierreCiclo2',
                        title: 'Fecha de cierre ciclo 2'
                    },
                    {
                        data: 'fechaProgramacionPrimerCiclo',
                        title: 'Fecha primera proyeccion ciclo 1'
                    },
                    {
                        data: 'fechaProgramacionSegundoCiclo',
                        title: 'Fecha primera proyeccion ciclo 2'
                    },
                    {
                        data: 'fechaInicioPeriodo',
                        title: 'Fecha inicio periodo'
                    },
                    {
                        data: 'year',
                        title: 'Año'
                    },
                    {
                        defaultContent: "<button type='button' class='editar btn btn-warning' data-toggle='modal' data-target='#editar_facultad' data-whatever='modal'><i class='fa-solid fa-pen-to-square'></i></button>",
                        title: 'Editar',
                        className: "text-center"
                    },
                    {
                        data: 'periodoActivo',
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
                        data: 'periodoActivo',
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
            console.log(table);

            function obtener_data_inactivar(tbody, table) {
                $(tbody).on("click", "button.inactivar", function(event) {
                    var data = table.row($(this).parents("tr")).data();
                    if (data.periodoActivo == 1) {
                        Swal.fire({
                            title: "¿Desea inactivar el periodo " + data.periodos + "?",
                            icon: 'warning',
                            showCancelButton: true,
                            showCloseButton: true,
                            cancelButtonColor: '#DC3545',
                            cancelButtonText: "No, Cancelar",
                            confirmButtonText: "Si"
                        }).then(result => {
                            if (result.value) {
                                $.post("{{ route('periodo.inactivar') }}", {
                                        '_token': $('meta[name=csrf-token]').attr('content'),
                                        id: encodeURIComponent(window.btoa(data.id)),
                                    },
                                    function(result) {
                                        console.log(result);
                                        if (result == "deshabilitado") {
                                            Swal.fire({
                                                title: "Periodo deshabilitado",
                                                html: "El periodo <strong>" + data.periodos +
                                                    "</strong> ha sido inactivado",
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
                            title: "¿Desea activar el periodo " + data.periodos + "?",
                            icon: 'warning',
                            showCancelButton: true,
                            showCloseButton: true,
                            cancelButtonColor: '#DC3545',
                            cancelButtonText: "No, Cancelar",
                            confirmButtonText: "Si"
                        }).then(result => {
                            if (result.value) {
                                $.post("{{ route('periodo.activar') }}", {
                                        '_token': $('meta[name=csrf-token]').attr('content'),
                                        id: encodeURIComponent(window.btoa(data.id)),
                                    },
                                    function(result) {
                                        if (result == "habilitado") {
                                            Swal.fire({
                                                title: "Periodo habilitado",
                                                html: "El periodo <strong>" + data.periodos +
                                                    "</strong> ha sido habilitado",
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


            /** Editar periodos */
            function obtener_data_editar(tbody, table) {
                $(tbody).on("click", "button.editar", function() {
                    var data = table.row($(this).parents("tr")).data();
                    /** Lìneas de còdigo que determinan las fechas actuales y lìmites */
                    var fechaActual = new Date();
                    var añoActual = fechaActual.getFullYear();
                    var fechaLimite = new Date(fechaActual.getFullYear() + 1, fechaActual.getMonth(), fechaActual.getDate());
                    var fechaLimiteISO = fechaLimite.toISOString().split('T')[0];
                    var añoSiguiente = añoActual + 1;

                    Swal.fire({
                        title: 'Actualizar información',
                        html: '<form>' +
                            '<label for="nombreEdit"> Periodo </label>' +
                            '<input type="text" id="nombreEdit" name="nombreEdit" value="' + data.periodos + '" class="form-control" placeholder="periodo"> <br>' +
                            '<label for="fecha1Edit"> Fecha de inicio ciclo 1 </label>' +
                            '<input type="date" min="' + fechaActual.toISOString().split('T')[0] + '" max="' + fechaLimiteISO + '" id="fecha1Edit" name="fecha1Edit" value="' + data.fechaInicioCiclo1 + '" class="form-control"> <br>' +
                            '<label for="fechaFin1Edit"> Fecha de cierre ciclo 1 </label>' +
                            '<input type="date" min="' + fechaActual.toISOString().split('T')[0] + '" max="' + fechaLimiteISO + '" id="fechaFin1Edit" name="fechaFin1Edit" value="' + data.fechaCierreCiclo1 + '" class="form-control"> <br>' +
                            '<label for="fecha2Edit"> Fecha de inicio ciclo 2 </label>' +
                            '<input type="date" min="' + fechaActual.toISOString().split('T')[0] + '" max="' + fechaLimiteISO + '" id="fecha2Edit" name="fecha2Edit" value="' + data.fechaInicioCiclo2 + '" class="form-control"> <br>' +
                            '<label for="fechaFin2Edit"> Fecha de cierre ciclo 2 </label>' +
                            '<input type="date" min="' + fechaActual.toISOString().split('T')[0] + '" max="' + fechaLimiteISO + '" id="fechaFin2Edit" name="fechaFin2Edit" value="' +  data.fechaCierreCiclo2 + '" class="form-control"> <br>' +
                            '<label for="ciclo1ProyeccionEdit"> Fecha inicio proyeccion ciclo 1</label>' +
                            '<input type="date" min="' + fechaActual.toISOString().split('T')[0] + '" max="' + fechaLimiteISO + '" id="ciclo1ProyeccionEdit" name="ciclo1ProyeccionEdit" value="' + data.fechaProgramacionPrimerCiclo + '" class="form-control"> <br>' +
                            '<label for="ciclo2ProyeccionEdit"> Fecha inicio proyeccion ciclo 2 </label>' +
                            '<input type="date" min="' + fechaActual.toISOString().split('T')[0] + '" max="' + fechaLimiteISO + '" id="ciclo2ProyeccionEdit" name="ciclo2ProyeccionEdit" value="' + data.fechaProgramacionSegundoCiclo + '" class="form-control"> <br>' +
                            '<label for="periodoEdit"> Fecha de inicio periodo </label>' +
                            '<input type="date" min="' + fechaActual.toISOString().split('T')[0] + '" max="' + fechaLimiteISO + '" id="periodoEdit" name="periodoEdit" value="' + data.fechaInicioPeriodo + '" class="form-control"> <br>' +
                            '<div>' +
                            '<label for="yearEdit" class="col-form-label">Año</label>' +
                            '<select id="yearEdit" class="form-control">' +
                            '<option value="' + añoActual + '">' + añoActual + '</option>' +
                            '<option value="' + añoSiguiente + '">' + añoSiguiente + '</option>' +
                            '</select>',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        cancelButtonText: "Cancelar",
                        confirmButtonText: 'Editar'
                    }).then(result => {
                        if (result.value) {
                            $.post("{{ route('periodo.update')}}", {
                                    '_token': $('meta[name=csrf-token]').attr('content'),
                                    id: encodeURIComponent(window.btoa(data.id)),
                                    nombre: $(document).find('#nombreEdit').val(),
                                    ciclo1: $(document).find('#fecha1Edit').val(),
                                    ciclo2: $(document).find('#fecha2Edit').val(),
                                    ciclo1Fin: $(document).find('#fechaFin1Edit').val(),
                                    ciclo2Fin: $(document).find('#fechaFin2Edit').val(),
                                    ciclo1Proyeccion: $(document).find('#ciclo1ProyeccionEdit').val(),
                                    ciclo2Proyeccion: $(document).find('#ciclo2ProyeccionEdit').val(),
                                    periodo: $(document).find('#periodoEdit').val(),
                                    año: $(document).find('#yearEdit').val(),
                                },
                                function(result) {
                                    console.log(result);
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
                    })
                });
            }
            obtener_data_editar("#example tbody", table);
            obtener_data_inactivar("#example tbody", table);
        }
    }
</script>
@include('layout.footer')