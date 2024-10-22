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
        <!-- <div class="container-fluid">

            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Planeación</h1>
                {{-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> --}}
            </div>


            <div class="row">

                <div class="col-xl-12 col-lg-12">
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table">
                                <table id="example" class="display" style="width:100%">
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div> -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Planeación por día</h1>
                {{-- <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a> --}}
            </div>

            <!-- Content Row -->
            <div class="row">
                <div class="row mt-12 mb-4">
                    <div class="col-12">
                        <h4><strong>Descargar matriculas por día</strong></h4>
                        <br>
                    </div>

                    <div class="col-4 text-right">
                        <!-- Formulario para búsqueda de estudiantes -->
                        @csrf
                        <div class="form-group mx-sm-3 mb-2">
                            <select class="form-control" id="fechas">
                                <?php
                                $fechaInicio = $fechaInicio;
                                $fechaActual = date('Y-m-d');
                                $horaActual = date('H:i:s');
                                if($horaActual < '12:00:00'){
                                    $fechaActual = date('Y-m-d', strtotime($fechaActual . " -1 day"));
                                }
                                while (strtotime($fechaActual) >= strtotime($fechaInicio)) {
                                    echo '<option value="' . $fechaActual . '">' . $fechaActual . '</option>';
                                    $fechaActual = date('Y-m-d', strtotime($fechaActual . " -1 day"));
                                }                                
                                $fechaActual = date('Y-m-d');
                                if($horaActual < '12:00:00'){
                                    $fechaActual = date('Y-m-d', strtotime($fechaActual . " -1 day"));
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class="col-6 text-right">
                        <div class="container mb-2" id="botonesDecargar">
                            <a href="https://moocs.ibero.edu.co/hermes/front/public/assets/documentos/Programacion_Valida_<?= $fechaActual ?>.xlsx" class="btn btn-success">Valida <?= $fechaActual ?></a>
                            <a href="https://moocs.ibero.edu.co/hermes/front/public/assets/documentos/Programacion_No_Valida_<?= $fechaActual ?>.xlsx" class="btn btn-danger">No Valida <?= $fechaActual ?></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                <!-- Area Chart -->
                <div class="col-xl-12 col-lg-12">
                    <div class="card shadow mb-4">
                        <div class="card-header text-center">
                            <ul class="nav nav-tabs card-header-tabs">

                                <li class="nav-item">
                                    <a class="nav-link  menuMoodle active" id="navValida" href="#Valida">Valida</a>
                                </li>

                                <li class="nav-item">
                                    <a class="nav-link  menuMoodle" id="navNoValida" href="#NoValida">No valida</a>
                                </li>

                            </ul>
                        </div>
                        <!-- Card Body -->
                        <div class="card-body">
                            <div id="Valida" class="content">
                                <div class="table">
                                    <table id="tablaValida" class="display" style="width:100%">
                                    </table>
                                </div>
                            </div>
                            <div id="NoValida" class="content">
                                <div class="tableNoValida">
                                    <table id="tablaNoValida" class="display" style="width:100%">
                                    </table>
                                </div>
                            </div>
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

    <!-- <script>
        $('#menuTablaPlaneacion').addClass('activo');
        $('#collapseConfig').addClass('collapse show');

        // * Datatable para mostrar todas las Facultades *
        $(document).ready(function() {

            tabla().then(function(respuesta) {
                Swal.close();
            }).catch(function(error) {
                Swal.fire({
                    icon: 'info',
                    title: '- disponibles',
                    text: 'Por el momento - de planeación',
                    confirmButtonColor: '#3085d6',
                });
            });


            function tabla() {
                return new Promise(function(resolve, reject) {
                    Swal.fire({

                        imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
                        showConfirmButton: false,
                    });

                    url = "{{ route('programas.planeacion')}}";

                    var datos = $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        type: 'post',
                        url: url,
                        success: function(data) {
                            try {
                                data = parseJSON(data);
                            } catch {
                                data = data;
                            }

                            var table = $('#example').DataTable({
                                "dom": 'Bfrtip',
                                "data": data.data,
                                "buttons": [
                                    'excel', 'pdf', 'print'
                                ],


                                "columns": [{
                                        data: 'codBanner',
                                        title: 'Codigo Banner'
                                    },
                                    {
                                        data: 'codprograma',
                                        title: 'Codigo programa'
                                    },
                                    {
                                        data: 'codMateria',
                                        title: 'Codigo Materia'
                                    },
                                    {
                                        data: 'curso',
                                        title: 'Curso'
                                    },
                                    {
                                        data: 'operador',
                                        title: 'Operador'
                                    },
                                    {
                                        data: 'fecha_registro',
                                        title: 'fecha registro'
                                    },
                                    {
                                        data: 'periodo',
                                        title: 'periodo'
                                    },

                                ],
                                "language": {
                                    "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                                },
                            });
                            resolve(data);
                        },
                        error: function(xhr, status, error) {
                            reject(error); // Rechaza la promesa en caso de error
                        }
                    })

                });

            }
        });
    </script> -->
    <script>
        $(document).ready(async function() {
            $(".content").hide();
            $("#Valida").show();


            $(document).on('change','#fechas', async function(){
                fechasSelect = $('#fechas');
                botones = $('#botonesDecargar');
                const fecha = fechasSelect.val();

                botones.empty();

                botones.html(`<a href="https://moocs.ibero.edu.co/hermes/front/public/assets/documentos/Programacion_Valida_${fecha}.xlsx" class="btn btn-success">Valida ${fecha}</a>
                            <a href="https://moocs.ibero.edu.co/hermes/front/public/assets/documentos/Programacion_No_Valida_${fecha}.xlsx" class="btn btn-danger">No Valida ${fecha}</a>`)
                
            });
            

            $(".menuMoodle").click(function() {
                $(".menuMoodle").removeClass("active");
                $(".content").hide();

                var target = $(this).attr("href").substring(1);


                $("#" + target).show();
                $("#nav" + target).addClass("active");
                return false;
            });

            $('#menuTablaPlaneacion').addClass('activo');
            $('#collapseConfig').addClass('collapse show');

            const DatosTablaValido = await datosTabla('Valida');
            console.log(DatosTablaValido.length);
            const DatosTablaNoValido = await datosTabla('No Valida');
            console.log(DatosTablaNoValido.length);
            Swal.close();

            const tablaValidos = $('#tablaValida');
            const tablaNoValidos = $('#tablaNoValida');

            await renderTablas(tablaValidos, DatosTablaValido, tablaNoValidos, DatosTablaNoValido)

        });

        async function datosTabla(estado) {
            try {
                Swal.fire({

                    imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
                    showConfirmButton: false,
                });
                const data = new FormData();
                data.append('estado', estado);
                const response = await fetch("{{ route('programas.planeacion') }}", {
                    method: 'POST',
                    body: data,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }

                const result = await response.json();

                return result['data'];

            } catch (error) {
                throw new Error('Error al los datos:', error);
                Swal.fire({
                    icon: 'info',
                    title: '- disponibles',
                    text: 'Por el momento - de planeación',
                    confirmButtonColor: '#3085d6',
                });
            }
        }

        async function renderTablas(tablaValidos, DatosTablaValido, tablaNoValidos, DatosTablaNoValido) {
            try {
                DatosTablaValido = parseJSON(DatosTablaValido);
                DatosTablaNoValido = parseJSON(DatosTablaNoValido);
            } catch {
                DatosTablaValido = DatosTablaValido;
                DatosTablaNoValido = DatosTablaNoValido;
            }

            console.log(DatosTablaValido);


            var tableValido = tablaValidos.DataTable({
                //"dom": 'Bfrtip',
                "data": DatosTablaValido,
                "buttons": [
                    'excel', 'pdf', 'print'
                ],


                "columns": [{
                        data: 'codBanner',
                        title: 'Codigo Banner'
                    },
                    {
                        data: 'codprograma',
                        title: 'Codigo programa'
                    },
                    {
                        data: 'codMateria',
                        title: 'Codigo Materia'
                    },
                    {
                        data: 'validacion',
                        title: 'Validación'
                    },
                    {
                        data: 'fecha_registro',
                        title: 'fecha registro'
                    },
                    {
                        data: 'periodo',
                        title: 'periodo'
                    },

                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                },
            });

            var tableNoValido = tablaNoValidos.DataTable({
                //"dom": 'Bfrtip',
                "data": DatosTablaNoValido,
                "buttons": [
                    'excel', 'pdf', 'print'
                ],


                "columns": [{
                        data: 'codBanner',
                        title: 'Codigo Banner'
                    },
                    {
                        data: 'codprograma',
                        title: 'Codigo programa'
                    },
                    {
                        data: 'codMateria',
                        title: 'Codigo Materia'
                    },
                    {
                        data: 'validacion',
                        title: 'Validación'
                    },
                    {
                        data: 'fecha_registro',
                        title: 'fecha registro'
                    },
                    {
                        data: 'periodo',
                        title: 'periodo'
                    },

                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                },
            });
        }
    </script>
    @include('layout.footer')
</div>