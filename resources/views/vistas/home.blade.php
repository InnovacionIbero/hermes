@include('layout.header')

@auth
    @include('menus.menu')    
@endauth

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

        <!-- Begin Page Content -->
        <div class="container-fluid">
            <!-- Page Heading -->
            <div class="text-center mb-3">
                <h2><strong>Módulos disponibles</strong></h2>
            </div>
            <div class="row justify-content-center">
                <div class="col-10">
                    <div class="card">
                        <div class="card-header text-center">
                            <ul class="nav nav-tabs card-header-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active menuHome" id="navadmisiones" href="#admisiones">Admisiones</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link menuHome" id="navmoodle" href="#moodle">Moodle</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link menuHome" id="navplaneacion" href="#planeacion">Planeación</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <!--Card Body - Módulo Admisiones-->
                            <div id="admisiones" class="content">
                                <div class="row mb-4">
                                    <div class="col-md-6 d-flex justify-content-center align-items-center">
                                        <img class="card-img-center" src="../assets/images/Banner.jpeg" alt="Card image cap" style="width: 70%; height: 100%;">
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-center align-items-center">
                                        <img class="card-img-center" src="../assets/images/informeBanner.JPG" alt="Card image cap" style="width: 70%;height: 100%;">
                                    </div>
                                </div>
                                <h5 class="mt-2"><strong class="text-dark">Descripción:</strong></h5>
                                <p>Este módulo corresponde a la información obtenida directamente desde Argos, aquí podrás
                                    encontrar un resumen de los datos obtenidos allí y filtrarlos según lo necesites, cabe recalcar
                                    que la información mostrada cuenta con 1 día de retraso puesto que es actualizada diariamente.
                                </p>

                                <h5><strong class="text-dark">Información disponible:</strong></h5>
                                <li class="list-group-item"> <strong class="text-dark">Total Estudiantes Argos</strong>: Este gráfico muestra el total de los estudiantes
                                    y los clásifica en activos e inactivos. Adicionalmente cuenta con la opción "Descargar datos Argos" la cual
                                    genera un Excel con los datos de Argos.</li>
                                <li class="list-group-item"> <strong class="text-dark">Estado financiero</strong>: Aquí se muestra un resumen del estado financiero (con sello,
                                    con retención o ASP) de los estudiantes <strong> activos</strong>.</li>
                                <li class="list-group-item"> <strong class="text-dark">Estado financiero - Retención</strong>: Aquí se muestra un resumen del estado en plataforma
                                    de los estudiantes <strong> activos </strong> que su estado financiero se encuentra en retención.</li>
                                <li class="list-group-item"> <strong class="text-dark">Estudiantes nuevos - Estado financiero</strong>: En este gráfico se puede visualizar el Estado
                                    financiero de todos los estudiantes <strong> activos </strong> de primer ingreso y transferentes.</li>
                                <li class="list-group-item"> <strong class="text-dark">Estudiantes antiguos - Estado financiero</strong>: Muestra lo mismo del gráfico anterior pero
                                    para estudiantes antiguos.</li>
                                <li class="list-group-item"> <strong class="text-dark">Tipos de estudiantes</strong>: Ilustra los tipos de estudiantes <strong>activos</strong>, además
                                    cuenta la opción "Ver más" para ampliar la cantidad de datos mostrados.</li>
                                <li class="list-group-item"> <strong class="text-dark">Estudiantes activos por operador</strong>: Muestra la cantidad de estudiantes inscritos por cada
                                    operador, también cuenta con la opción de "Ver más".</li>
                                <li class="list-group-item"> <strong class="text-dark">Programas con mayor cantidad de admitidos Activos</strong>: Muestra la cantidad de estudiantes inscritos
                                    en cada programa, cuenta con la opción de "Ver más".</li>
                                <li class="list-group-item"> <strong class="text-dark">Metas por ciclo</strong>: Muestra la cantidad de estudiantes inscritos por programa con sello financiero
                                    y de primer ingreso VS la meta fijada, además permite descargar un Excel en donde se puede visualizar el porcentaje de cumplimiento de la meta.</li>
                            </div>
                            <!--Card Body - Módulo Moodle-->
                            <div id="moodle" class="content">
                                <div class="row mb-4">
                                    <div class="col-md-6 d-flex justify-content-center align-items-center">
                                        <img class="card-img-center" src="../assets/images/informeMoodleOriginal.jpeg" alt="Card image cap" style="width: 70%; height: 100%;">
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-center align-items-center">
                                        <img class="card-img-center" src="../assets/images/informeMoodle.jpeg" alt="Card image cap" style="width: 70%; height: 100%;">
                                    </div>
                                </div>
                                <h5 class="mt-2"><strong class="text-dark">Descripción:</strong></h5>
                                <p>En este módulo podrás encontrar información proveniente de aula virtual (Moodle)
                                </p>

                                <h5><strong class="text-dark">Información disponible:</strong></h5>
                                <li class="list-group-item"> <strong class="text-dark">Riesgo (alto-medio-bajo)</strong>: Estos gráficos muestran la cantidad de
                                    matrículas que se encuentran en cada tipo de riesgo. Adicionalmente incluyen la opción "Ver más" que permite visualizar los estudiantes
                                    que se encuentran dentro de ese tipo de riesgo en específico, además de incluir un informe detallado de cada alumno en donde se observa
                                    el estado acádemico en el que se encuentra el estudiante, junto con sus datos personales. </li>
                            </div>
                            <!--Card Body - Módulo Planeación-->
                            <div id="planeacion" class="content">
                                <div class="row mb-4">
                                    <div class="col-md-6 d-flex justify-content-center align-items-center">
                                        <img class="card-img-center" src="../assets/images/Planeacion.jpeg" alt="Card image cap" style="width: 70%; height: 100%;">
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-center align-items-center">
                                        <img class="card-img-center" src="../assets/images/informePlaneacion.JPG" alt="Card image cap" style="width: 70%; height: 100%;">
                                    </div>
                                </div>
                                <h5 class="mt-2"><strong class="text-dark">Descripción:</strong></h5>
                                <p>En este módulo podrás encontrar la información correspondiente a la proyección de lo estudiantes inscritos en la Universidad
                                    Iberoamericana, o en su defecto a la programación, según la fecha en que se consulte.
                                </p>

                                <h5><strong class="text-dark">Información disponible:</strong></h5>
                                <li class="list-group-item"> <strong class="text-dark">Estado financiero</strong>: Aquí se muestra un resumen del estado financiero (con sello,
                                    con retención o ASP) de los estudiantes <strong> proyectados o programados</strong>.</li>
                                <li class="list-group-item"> <strong class="text-dark">Estado financiero - Retención</strong>: Aquí se muestra un resumen del estado en plataforma
                                    de los estudiantes <strong>proyectados o programados</strong> que su estado financiero se encuentra en retención.</li>
                                <li class="list-group-item"> <strong class="text-dark">Estudiantes nuevos - Estado financiero</strong>: En este gráfico se puede visualizar el Estado
                                    financiero de todos los estudiantes <strong>proyectados o programados</strong> de primer ingreso y transferentes.</li>
                                <li class="list-group-item"> <strong class="text-dark">Estudiantes antiguos - Estado financiero</strong>: Muestra lo mismo del gráfico anterior pero
                                    para estudiantes antiguos.</li>
                                <li class="list-group-item"> <strong class="text-dark">Tipos de estudiantes</strong>: Ilustra los tipos de estudiantes <strong>activos</strong>, además
                                    cuenta la opción "Ver más" para ampliar la cantidad de datos mostrados.</li>
                                <li class="list-group-item"> <strong class="text-dark">Estudiantes activos por operador</strong>: Muestra la cantidad de estudiantes inscritos por cada
                                    operador, también cuenta con la opción de "Ver más".</li>
                                <li class="list-group-item"> <strong class="text-dark">Programas con mayor cantidad de admitidos Activos</strong>: Muestra la cantidad de estudiantes inscritos
                                    en cada programa, cuenta con la opción de "Ver más". Adicionalente permite ver un informe detallado que incluye la siguiente información:
                                    <ul>
                                        <li>Estudiantes proyectados o programados en cada programa y el estado de su sello financiero (si tienen sello o no).</li>
                                        <li>Malla Curricular de cada programa y la cantidad de matrículas por curso, estos datos pueden ser descargados en varios formatos
                                            como Excel, permitiendo llevar a cabo la planeación de docentes.
                                        </li>
                                        <li>Buscador de estudiantes proyectados o planeados en cada programa, aquí puede observarse que cursos tiene inscrito cada estudiante de un
                                            programa en específico, también pueden ser descargados estos datos en varios formatos.
                                        </li>
                                    </ul>
                                </li>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#menuHome').addClass('activo');

            $(".content").hide();
            $("#admisiones").show();

            $(".menuHome").click(function() {
                $(".menuHome").removeClass('active');
                $(".content").hide();

                var target = $(this).attr("href").substring(1);

                $("#" + target).show();
                $("#nav" + target).addClass('active');

                return false;
            });

          if (<?php echo auth()->user()->id ?> == 5 || <?php echo auth()->user()->id ?> == 6) {

                verificarPendientes();

                function verificarPendientes() {
                    var datos = $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        url: "{{ route('verificar.pendientes') }}",
                        method: 'post',
                        async: false,
                        success: function(data) {
                            if (data > 0) {
                                Swal.fire({
                                    icon: 'info',
                                    title: 'Solicitudes pendientes por resolver',
                                    text: 'Hay ' + data + ' solicitudes pendientes por atender de los usuarios.',
                                    confirmButtonColor: '#3085d6',
                                });
                            }
                        }
                    });

                }
            }
        });
    </script>

    @include('layout.footer')
</div>