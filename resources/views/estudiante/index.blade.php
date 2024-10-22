<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="icon"
        href="https://aulavirtual.ibero.edu.co/pluginfile.php?file=%2F1%2Ftheme_adaptable%2Ffavicon%2F1693948501%2FImagen-5.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <title>Historial Academico</title>

    <!-- ponemos los estilos y complementos necesarios para las paginas -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Custom fonts for this template-->
    <link href="{{ asset('general/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{ asset('general/css/sb-admin-2.min.css') }}" rel="stylesheet">
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-VTEQ6832HQ"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
    
      gtag('config', 'G-VTEQ6832HQ');
    </script>
    {{-- <script src="{{asset('general/vendor/jquery/jquery.min.js')}}"></script> --}}
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
        crossorigin="anonymous"></script>

    {{-- Datatable --}}
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" defer>
    <script src="//code.jquery.com/jquery-3.7.0.js" defer></script>
    <script src="//cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js" defer></script>
    <script src="//cdn.datatables.net/fixedcolumns/4.3.0/js/dataTables.fixedColumns.min.js" defer></script>

    <script type="text/javascript" src="//cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js" defer></script>
    <script type="text/javascript" src="//cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js" defer></script>
    <script type="text/javascript" src="//cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js" defer></script>

    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js" defer></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" defer></script>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" defer></script>

    {{-- SweetAlert --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="{{ asset('js/alerta.js') }}"></script>
</head>

<style>
    .card_historial {
        border-radius: 2rem;
        box-shadow: 5px 3px 9px #4a4848;
    }

    #facultades {
        font-size: 14px;
    }

    #programas {
        font-size: 14px;
    }

    #generarReporte {
        margin-left: 260px;
    }

    .taps_inter {
        color: white !important;
        background-color: #4a4848;
    }

    .nav-link.active {
        color: black !important;
        background-color: #dfc14e !important;
    }

    .taps_inter:hover {
        background-color: #dfc14e;
        color: black !important;
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

    .table td,
    .table th {
        border: 13px solid white;
    }

    .dataTables_wrapper {
        width: 100%;
        background-color: white !important;
        color: black !important;
        padding: 3%;
        border-radius: 40px;
    }

    .dataTables_wrapper td {
        padding: 0px 10px !important;
        border: 5px solid white !important;
    }

    .table {
        color: black;
    }

    .datos {
        color: white !important;
        background-color: #4a4848;
        margin-right: 10px;
    }

    .datos:hover {
        background-color: #dfc14e;
        color: black !important;
    }

    .botonreglas:hover {
        background-color: #4a4848 !important;
        color: white !important;
        border: none;
    }

    div .show {
        padding-top: 22px;
        padding-bottom: 40px;
    }

    #buttonConsultar {
        background-color: #4a4848;
        border-color: #4a4848;
        color: white;
        width: 100%;
        height: 45px;
        border-radius: 10px;
        place-items: center;
        font-size: 18px;
    }

    .floating-btn {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 1;
    }

    #recargar {
        position: fixed;
        bottom: 30px;
        left: 30px;
        z-index: 1;
    }

    #recargar span {
        width: 160px;
        height: 40px;
        background-color: #007bff;
        color: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 16px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }



    .floating-btn span {
        width: 60px;
        height: 60px;
        background-color: #007bff;
        color: #fff;
        border-radius: 50%;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 24px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .floating-btn:hover span {
        background-color: #0056b3;
    }

    .options {
        display: none;
        position: absolute;
        bottom: 70px;
        right: 0;
        background: transparent;
    }

    .options li {
        margin-bottom: 14px;
        border-radius: 50px;
        padding: 7px;
        margin-left: 5px;
        margin-right: 5px;
    }

    .options a {
        display: block;
        padding: 10px;
        text-decoration: none;
        color: #333;
    }

    .options a:hover {
        background-color: #f0f0f0;
    }



    .contenedor_semestre {
        display: flex;
        flex-direction: column;
        /* Esto asegura que los elementos se apilen verticalmente */
        align-items: center;
        /* Esto centrará el botón horizontalmente dentro del div */
    }

    .boton_semestre {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%) rotate(-90deg);
        padding: 10px 20px;
        background-color: #007bff;
        color: #ffffff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
</style>

<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <img src="https://moocs.ibero.edu.co/hermes/front/public/assets/images/imagen_edtudiantes.png"
                style="margin-top: 1%;margin-bottom: 2%;width: 100%;">


            {{-- <form action="{{ route('historial.consulta') }}" method="post"> --}}
            {{-- @csrf --}}
            <div class="col align-self-center" id="contenedorBuscador">
                <form action="#" id="miForm">
                    <div class="card-body col-md-12 row justify-content-center mt-3 align-items-center">
                        <div class="col-md-4"
                            style="background-color: #dfc14e;padding: 1rem!important;border-radius:10px;">
                            <div class="row justify-content-center mt-3 align-items-center">
                                <div class="col-4 text-center text-right text-white">
                                    <h5 style="margin-bottom:0px;">Id Banner:</h5>
                                </div>
                                <div class="col-8">
                                    <input class="form-control" type="text" name="codigo"
                                        placeholder="Codigo estudiante" id="codigo" required="">
                                </div>
                            </div>
                            <div class="row justify-content-center mt-3">

                                <div class="col-12 text-center">
                                    <button type="submit" id="buttonConsultar" class="btn mb-3">Consultar</button>
                                </div>
                            </div>
                        </div>
                </form>
            </div>

        </div>
        {{-- </form> --}}

        <br>

        <div class="container-fluid hidden"
            style="background-color:#6e707e;padding: 1%;margin-bottom: 3%;border-radius: 19px;" id="info_1">
            <div class="container mt-3" id="info" style="color:white">

            </div>

        </div>


        <div class="container-fluid hidden contenedor_interno"
            style="background-color: #ffffff;border-radius: 15px;padding-top: 20px;color: white;">
            <div class="col-md-12">

                <ul class="nav nav nav-pills" id="myTabs">
                    <!-- Pestañas se llenarán dinámicamente aquí -->
                </ul>


                <div class="tab-content" id="tabContent">
                    <!-- Contenidos de pestañas se llenarán dinámicamente aquí -->
                </div>


            </div>

        </div>

    </div>
</div>

<div class="floating-btn" id="floatingBtn" style="display:none;">
    <span>+</span>
    <div class="options" id="options">
        <ul style="list-style:none; width: 200px;">
            <li class="bg-success text-white">Mat. Aprobada</a>
            <li class="bg-danger text-white">Mat. Reprobada</a>
            <li class="bg-info text-white">En aula</a>
            <li class="bg-warning text-white">Mat. Proyectada</a>
            <li class="bg-secondary text-white">Mat. Por ver</a>

        </ul>
    </div>
</div>

<div id="recargar" style="display:none">
    <span>Realiza otra consulta</span>
</div>

@include('layout.modals')

<script>
    $(document).ready(function() {
        // var j = jQuery.noConflict();
        // $('[data-toggle="tooltip"]').tooltip();

        $('#miForm').on('submit', function(e) {
            e.preventDefault();
            consultarEstudiante();
            alertaPreload();
        })

        const floatingBtn = document.getElementById('floatingBtn');
        const options = document.getElementById('options');

        floatingBtn.addEventListener('click', function() {
            options.style.display = options.style.display === 'block' ? 'none' : 'block';
        });

        new DataTable('#example');

        let DataPorVer = [];
        let DataVista = [];
        let DataProyectada = [];
        let DataMoodle = [];
        let DataHistorial = [];

        $(document).on("click keypress", ".datos", function(event) {
            var code = event.keyCode ? event.keyCode : event.which;

            if (event.type === "click" || code === 13) {
                $('#contenedorBuscador').hide();
                $('#floatingBtn').show();
                $('#recargar').show();

                $(document).find('.taps_programas').empty()
                $(document).find("#tabContent div .active ").removeClass("active show")
                idbanner = $(this).attr('data-id');
                programa = $(this).attr('data-programa');
                nombrePrograma = $(this).attr('data-nombre');
                tap = $(this).attr('data-tap');
                tap = "#" + tap;
                $(tap).empty()
                var formData = new FormData();
                formData.append('codBanner', idbanner);
                formData.append('programa', programa);

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'post',
                    url: "{{ route('historial.consultaHistorial') }}",
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('#codigo').prop('disabled', true);
                    },
                    success: function(data) {
                        DataPorVer = [];
                        DataVista = [];
                        DataProyectada = [];
                        DataMoodle = [];
                        DataHistorial = [];

                        if (data.info == "con_datos") {
                            const materias = data.historial
                            // Convierte el objeto en un array de objetos
                            const materiasArray = $.map(materias, function(value, key) {
                                return value;
                            });

                            // Ordena el array primero por "semestre" y luego por "ciclo"
                            materiasArray.sort(function(a, b) {
                                if (a.semestre !== b.semestre) {
                                    return a.semestre - b.semestre;
                                } else {
                                    return a.ciclo - b.ciclo;
                                }
                            });

                            // Crea un objeto para agrupar las materias por semestre
                            const materiasPorSemestre = {};

                            // Agrupa las materias por semestre
                            $.each(materiasArray, function(index, materia) {
                                const semestre = materia.semestre;

                                if (!materiasPorSemestre[semestre]) {
                                    materiasPorSemestre[semestre] = [];
                                }

                                materiasPorSemestre[semestre].push(materia);
                            });

                            // Crea la tabla y agrega las filas
                            const $tablas = $(
                                '<div class="container" style="max-width: 100%;"><div class="card-deck"><div class="row"> '
                            );

                            $li_taps_internos = "";


                            $li_taps_internos += '<div class="container">'
                            $li_taps_internos +=
                                '<div class="row align-content-center" style="color:white;">'

                            $li_taps_internos +=
                                '<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">'
                            $li_taps_internos +=
                                '    <li class="nav-item" style="margin-right:10px; margin-bottom:10px;">'
                            $li_taps_internos +=
                                '        <a class="nav-link active taps_inter" id="pills-home-tab" data-toggle="pill" href="#malla" role="tab" aria-controls="pills-home" aria-selected="true">Malla Curricular</a>'
                            $li_taps_internos += '    </li>'
                            $li_taps_internos +=
                                '    <li class="nav-item" style="margin-right:10px; margin-bottom:10px;">'
                            $li_taps_internos +=
                                '        <a class="nav-link taps_inter" id="pills-profile-tab" data-toggle="pill" href="#Viendo" role="tab" data-nombre="tabla_Viendo" data-tabla="DataMoodle" aria-controls="pills-profile" aria-selected="false">Viendo en aula</a>'
                            $li_taps_internos += '    </li>'
                            $li_taps_internos +=
                                '    <li class="nav-item" style="margin-right:10px; margin-bottom:10px;">'
                            $li_taps_internos +=
                                '        <a class="nav-link taps_inter" id="pills-contact-tab" data-toggle="pill" href="#proyectadas" role="tab" data-nombre="tabla_proyectadas" data-tabla="DataProyectada" aria-controls="pills-contact" aria-selected="false">Materias proyectadas</a>'
                            $li_taps_internos += '    </li>'
                            $li_taps_internos +=
                                '    <li class="nav-item" style="margin-right:10px; margin-bottom:10px;">'
                            $li_taps_internos +=
                                '        <a class="nav-link taps_inter" id="pills-contact-tab" data-toggle="pill" href="#por_ver" role="tab" data-nombre="tabla_por_ver" data-tabla="DataPorVer" aria-controls="pills-contact" aria-selected="false">Materias por ver</a>'
                            $li_taps_internos += '    </li>'
                            $li_taps_internos +=
                                '    <li class="nav-item" style="margin-right:10px; margin-bottom:10px;">'
                            $li_taps_internos +=
                                '        <a class="nav-link taps_inter" id="pills-contact-tab" data-toggle="pill" href="#vistas" role="tab" data-nombre="tabla_vistas" data-tabla="DataVista" aria-controls="pills-contact" aria-selected="false">Materias aprobadas</a>'
                            $li_taps_internos += '    </li>'
                            $li_taps_internos +=
                                '    <li class="nav-item" style="margin-right:10px; margin-bottom:10px;">'
                            $li_taps_internos +=
                                '        <a class="nav-link taps_inter" id="pills-contact-tab" data-toggle="pill" href="#historial" role="tab" data-nombre="tabla_Historial" data-tabla="DataHistorial" aria-controls="pills-contact" aria-selected="false">Historial completo </a>'
                            $li_taps_internos += '    </li>'
                            $li_taps_internos +=
                                '<div class="tab-content" id="pills-tabContent">'

                            $tap_malla = $(
                                '<div class="tab-pane fade show active inter" id="malla"  role="tabpanel" ><div class="card-deck"><table>'
                            );

                            $tap_viendo = $(
                                '<div class="tab-pane fade inter" id="Viendo" role="tabpanel" ><div class="container"><div class="row"><table class="table datatablecursos table-striped" id="tabla_Viendo">'
                            )

                            $tap_proyectadas = $(
                                '<div class="tab-pane fade inter" id="proyectadas" role="tabpanel" ><div class="container"><div class="row"><table class="table datatablecursos table-striped" id="tabla_proyectadas">'
                            )

                            $tap_por_ver = $(
                                '<div class="tab-pane fade inter" id="por_ver" role="tabpanel" ><div class="container"><div class="row"><table class="table datatablecursos table-striped" id="tabla_por_ver">'
                            )

                            $tap_vistas = $(
                                '<div class="tab-pane fade inter" id="vistas" role="tabpanel" ><div class="container"><div class="row"><table class="table datatablecursos table-striped" id="tabla_vistas">'
                            )

                            $tap_Historial = $(
                                '<div class="tab-pane fade inter" id="historial" role="tabpanel" ><div class="container"><div class="row"><table class="table datatablecursos table-striped" id="tabla_Historial">'
                            )



                            $tablas.append($li_taps_internos);

                            let currentSemestre =
                                null; // Para mantener un seguimiento del semestre actual
                            var $filaMateria = "";
                            var $filaSemestre = null;
                            var abrirRow = false;
                            var fondo = ' ';

                            $.each(materiasArray, function(index, materia) {
                                if (materia.semestre !== currentSemestre) {
                                    // Si es un nuevo semestre, crea una nueva fila                                    
                                    currentSemestre = materia.semestre;

                                    if (currentSemestre % 2 == 0) {
                                        fondo = '#9b9b9b';
                                    } else {
                                        fondo = '#d1d1d1';
                                    }

                                    // Cerrar el div de la fila anterior si es necesario
                                    if ($filaSemestre) {
                                        $filaSemestre.append($filaMateria);
                                        $filaMateria = "";
                                        $tap_malla.append($filaSemestre);
                                    }

                                    var titulo = ' ';


                                    // Crear una nueva fila para el nuevo semestre
                                    $filaSemestre = $(
                                        '<div class="row mb-4" style="background-color:' +
                                        fondo +
                                        '  ;padding: 10px;border-radius: 15px;">'
                                    );
                                    if (materia.semestre == 0) {
                                        titulo = '<strong> Todo tu historial - ' +
                                            materia.programa + '</strong>';
                                    } else {
                                        titulo = '<strong> Semestre: ' + materia
                                            .semestre + '</strong>';
                                    }
                                    $filaSemestre.append(
                                        '<div class="card card_historial semestre" style="background-color: #dfc14e;color:#4a4848;margin-top: 0%;min-width: 100%;height:40px; margin-right: 1%;"><div class="card-body"><div class="row"><div class="col-11"><h5 style="transform: translateY(-50%);text-align: center;"><span id="semestre">' +
                                        titulo +
                                        '</span></h5></div><div class="col-1"><button type="button" data-toggle="modal" data-target="#modalReglas" class="btn btn-primary botonreglas" style="transform: translateY(-50%);text-align: center;width: 35px" >?</button></div></div></div></div>'
                                        );
                                }

                                if (materia.codprograma == programa) {
                                    if (materia.por_ver == 'Vista') {

                                        var rowDataVista = [
                                            materia.codigo_materia,
                                            materia.ciclo,
                                            materia.nombre_materia,
                                            materia.creditos,
                                            materia.semestre,
                                            materia.calificacion
                                        ]

                                        DataVista.push(rowDataVista);
                                    }
                                    if (materia.por_ver == 'Proyectada') {
                                        var rowDataProyectada = [
                                            materia.codigo_materia,
                                            materia.ciclo,
                                            materia.nombre_materia,
                                            materia.creditos,
                                            materia.semestre,
                                            null
                                        ]

                                        DataProyectada.push(rowDataProyectada);
                                    }
                                    if (materia.por_ver == 'Por ver') {
                                        console.log( materia)
                                        var rowDataPorVer = [
                                            materia.codigo_materia,
                                            materia.ciclo,
                                            materia.nombre_materia,
                                            materia.creditos,
                                            materia.semestre,
                                            null
                                        ]

                                        DataPorVer.push(rowDataPorVer);
                                    }
                                    if (materia.por_ver == 'Viendo'||materia.inscrita == 'Viendo') {
                                        var rowDataMoodle = [
                                            materia.codigo_materia,
                                            materia.ciclo,
                                            materia.nombre_materia,
                                            materia.creditos,
                                            materia.semestre,
                                            materia.calificacion
                                        ]

                                        DataMoodle.push(rowDataMoodle);
                                    }
                                    if (materia.calificacion != '') {
                                        var rowDataHistorial = [
                                            materia.codigo_materia,
                                            materia.ciclo,
                                            materia.nombre_materia,
                                            materia.creditos,
                                            materia.semestre,
                                            materia.calificacion
                                        ]
                                        DataHistorial.push(rowDataHistorial);
                                    }
                                }

                                // Agregar la materia como una columna en la fila actual
                                $filaMateria += '<div class="col-3">';
                                $filaMateria +=
                                    '<td style="color:white;margin-right: 1%">';
                                $filaMateria +=
                                    '  <div class="card " style="background-color:transparent;border: none;height: 250px!important;padding-bottom: 2%;">'
                                $filaMateria +=
                                    '    <div class="card card_historial materias" style="height: 350px;">'
                                $filaMateria +=
                                    '      <div class="card-body" style="padding: 1.2rem;">'
                                $filaMateria +=
                                    '        <div class="" style=" display: flex;color: black;">'
                                $filaMateria += '          <span class="' + materia
                                    .color +
                                    '" style="border-bottom: 2px solid;margin-right: -4px;border-top: 2px solid;border-left:2px solid;border-top-left-radius: 30px;border-bottom-left-radius: 28px;min-width: 27px;height: 42px;">&nbsp;&nbsp;&nbsp;<br><br>'
                                $filaMateria += '          </span>'
                                $filaMateria +=
                                    '          <h6 class="card-title  ' + materia
                                    .color +
                                    '" style="border-top: 2px solid;border-bottom: 2px solid;border-right: 2px solid;border-bottom-right-radius: 30px;border-top-right-radius: 30px;margin-left: 4px;height: 42px;width: 100%;">'

                                $filaMateria +=
                                    '            <div class="row justify-content-between">'
                                $filaMateria += '            <div class="col-7">'
                                $filaMateria +=
                                    '            <strong>Codigo:</strong>'
                                $filaMateria += '            <br>'
                                $filaMateria += '            <span>' + materia
                                    .codigo_materia + '</span>'
                                $filaMateria += '            </div>'

                                if (materia.prerequisito && materia.prerequisito
                                    .length > 0 || materia.equivalencia && materia
                                    .equivalencia.length > 0) {
                                    $filaMateria +=
                                        '            <div class="col-2 mr-4">'
                                    $filaMateria +=
                                        '            <button type="button" class="btn datosmateria" data-toggle="modal" data-target="#modalPrerrequisitos" data-codigo="' +
                                        materia.codigo_materia +
                                        '" data-codprograma="' + materia
                                        .codprograma +
                                        '" style="width: 40px; height: 35px;background-color:transparent;color:white;border:none;"><i class="fa-solid fa-info"></i></button>'
                                    $filaMateria += '            </div>'
                                }
                                $filaMateria += '            </div>'
                                $filaMateria += '          </h6>'


                                $filaMateria += '        </div>'
                                $filaMateria +=
                                    '        <p class="card-text" id="" style="text-align: center;color: black;">'
                                $filaMateria += '          <span><strong>' + materia
                                    .nombre_materia + '</strong></span>'
                                $filaMateria += '        </p>'
                                $filaMateria +=
                                    '        <p class="card-text" id="" style="text-align: center;color: black;">'
                                if (materia.calificacion && materia.calificacion
                                    .length > 0) {
                                    $filaMateria +=
                                        '<span><strong>Calificación:</strong> ' +
                                        materia.calificacion + '</span><br>';
                                }

                                $filaMateria += '<strong>Créditos:</strong> ' +
                                    materia.creditos + ' </span>'
                                if (materia.ciclo) {
                                    if (materia.ciclo == 12) {
                                        materia.ciclo = 'Ciclo completo'
                                    }
                                    $filaMateria += '- <strong>Ciclo:</strong> ' +
                                        materia.ciclo
                                }

                                $filaMateria += '</p>'
                                $filaMateria += '</div>';
                                $filaMateria += '</div>';
                                $filaMateria += '</div>';
                                $filaMateria += '</div>';
                            });

                            $('#codigo').prop('disabled', false);

                            // Cerrar el último div de la fila y agregarlo al contenedor
                            if ($filaSemestre) {
                                $filaSemestre.append($filaMateria);
                                $tap_malla.append($filaSemestre);
                            }

                            $tablas.append($tap_malla);
                            $tablas.append($tap_viendo);
                            $tablas.append($tap_proyectadas);
                            $tablas.append($tap_por_ver);
                            $tablas.append($tap_vistas);
                            $tablas.append($tap_Historial);

                            $(document).find(tap).append($tablas);
                            Swal.close();
                        }
                        if (data.info == "sin_datos") {
                            Swal.close();
                            const $tablas = $(
                                '<div class="container"><div class="row"> <table>');

                            const $filaMateria = $('<td>').text(
                                'En estos momentos no contamos Con información contacta con soporte'
                            );
                            $tablas.children('tr:last').append($filaMateria);

                        }
                    }
                });
            }
        });

        $(document).on("click", "#recargar", function() {
            window.location.reload();
        });

        $(document).on('click', '.taps_inter', function() {

            $(document).find('.inter').removeClass('active')
            $(document).find('.inter').removeClass('show')

            $('.inter').hide();

            $id = $(this).attr('href');

            $($id).show();
            $(document).find($id).addClass('active');
            $(document).find($id).addClass('show');

            var tabla = $(this).data('nombre');

            let data;
            var texto;
            if (tabla == 'tabla_por_ver') {
                data = DataPorVer;
                texto = 'No tienes materias por ver';
            } else if (tabla == 'tabla_proyectadas') {
                data = DataProyectada;
                texto = 'No tienes materias proyectadas para este periodo';
            } else if (tabla == 'tabla_vistas') {
                data = DataVista;
                texto = 'No has aprobado ninguna materia';
            } else if (tabla == 'tabla_Viendo') {
                data = DataMoodle;
                texto = 'No tienes materias en aula virtual';
            } else if (tabla == 'tabla_Historial') {
                data = DataHistorial;
                texto = 'No tienes materias en tu historial';
            }

            var columns = [{
                    title: 'Codigo Materia'
                },
                {
                    title: 'Ciclo'
                },
                {
                    title: 'Nombre Materia'
                },
                {
                    title: 'Creditos',
                    className: 'dt-center'
                },
                {
                    title: 'Semestre',
                    className: 'dt-center'
                }
            ];

            if (tabla == "tabla_vistas" || "tabla_Historial") {
                columns.push({
                    title: 'Calificación',
                    className: 'dt-center',
                });
            }

            if (data.length == 0) {
                Swal.fire({
                    icon: "info",
                    title: "Oops...",
                    text: texto
                });

            } else {

                destruirDataTable(tabla);
                var dataTableOptions = {
                    "data": data,
                    'pageLength': 10,
                    "columns": columns,
                    "order": [3, 'asc']
                };

                if (tabla == "tabla_Historial") {
                    dataTableOptions["dom"] = 'Bfrtip';
                }

                $('#' + tabla).DataTable(dataTableOptions);
            }

        })

        function destruirDataTable(tabla) {
            if ($('#' + tabla) && $.fn.DataTable.isDataTable('#' + tabla)) {
                $('#' + tabla).dataTable().fnDestroy();
                $('#' + tabla + 'thead').empty();
                $('#' + tabla + 'tbody').empty();
                $('#' + tabla + 'tfooter').empty();
            }
        }

        function consultarEstudiante() {
            codBanner = $('#codigo');
            if (codBanner.val() != '') {
                $("#info_1").removeClass('hidden')
                $("#info_2").removeClass('hidden')
                $('#info').html('');
                $('#programas').html('');

                consultaEstudiante(codBanner.val());
                consultaNombre(codBanner.val());


                //consultaHistorial(codBanner.val());
                //consultaProgramacion(codBanner.val());

            } else {

                alert("ingrese su codigo de estudiante");
            }
        }

        function consultaNombre(codBanner) {
            var formData = new FormData();
            formData.append('codBanner', codBanner);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: "{{ route('historial.consultanombre') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                // beforeSend: function(){
                // $('#codigo').prop('disabled',true);
                // },
                success: function(data) {
                    if (data != 'no tiene historial') {
                        $('#codigo').prop('disabled', false);
                        $('#info').html('');
                        $('#info').append(`<p class="col-md-12" style="margin-top: 2%;">
                        <strong>Historial academico de: </strong> ${data} - <strong>IdBanner</strong>: ${codBanner}<br>

                        <b> Recuerde que la información suministrada por este sistema es de carácter informativo. Si presenta inconsistencias, verifica primero en la plataforma de autogestión <a style="color:#dfc14e !important" href="https://www.ibero.edu.co/experiencia-al-estudiante/portal-autogestion" target="_blank" rel="noopener noreferrer">Zafiro.</a></b> <br>
                        Nota: si el periodo ha finalizado las calificaciones pueden tardar alrededor de 5 días para verse reflejadas en el historial.
                    </p>
                    <div class="row align-items-start">
                    <div class="col-2">
                        <div style=" display: flex;color: black;">
                            <span class="bg-success" style="border-bottom: 2px solid;margin-right: -4px;border-top: 2px solid;border-left:2px solid;border-top-left-radius: 30px;border-bottom-left-radius: 28px;min-width: 27px;height: 42px;">&nbsp;&nbsp;&nbsp;<br><br></span>
                            <h6 class="card-title bg-success" style="border-top: 2px solid;border-bottom: 2px solid;border-right: 2px solid;border-bottom-right-radius: 30px;border-top-right-radius: 30px;margin-left: 4px;height: 42px;width: 100%;">
                            <p class="text-light" style="margin-top:9px;">Mat. Aprobada</p></h6>
                        </div>
                    </div>
                    <div class="col-2">
                        <div style=" display: flex;color: black;">
                            <span class="bg-danger" style="border-bottom: 2px solid;margin-right: -4px;border-top: 2px solid;border-left:2px solid;border-top-left-radius: 30px;border-bottom-left-radius: 28px;min-width: 27px;height: 42px;">&nbsp;&nbsp;&nbsp;<br><br></span>
                            <h6 class="card-title bg-danger" style="border-top: 2px solid;border-bottom: 2px solid;border-right: 2px solid;border-bottom-right-radius: 30px;border-top-right-radius: 30px;margin-left: 4px;height: 42px;width: 100%;">
                            <p class="text-light" style="margin-top:9px;">Mat. Reprobada</p></h6>
                        </div>
                    </div>
                    <div class="col-2">
                        <div style=" display: flex;color: black;">
                            <span class="bg-info" style="border-bottom: 2px solid;margin-right: -4px;border-top: 2px solid;border-left:2px solid;border-top-left-radius: 30px;border-bottom-left-radius: 28px;min-width: 27px;height: 42px;">&nbsp;&nbsp;&nbsp;<br><br></span>
                            <h6 class="card-title bg-info" style="border-top: 2px solid;border-bottom: 2px solid;border-right: 2px solid;border-bottom-right-radius: 30px;border-top-right-radius: 30px;margin-left: 4px;height: 42px;width: 100%;">
                            <p class="text-light" style="margin-top:9px;">En aula</p></h6>
                        </div>
                    </div>
                    <div class="col-2">
                        <div style=" display: flex;color: black;">
                            <span class="bg-warning" style="border-bottom: 2px solid;margin-right: -4px;border-top: 2px solid;border-left:2px solid;border-top-left-radius: 30px;border-bottom-left-radius: 28px;min-width: 27px;height: 42px;">&nbsp;&nbsp;&nbsp;<br><br></span>
                            <h6 class="card-title bg-warning" style="border-top: 2px solid;border-bottom: 2px solid;border-right: 2px solid;border-bottom-right-radius: 30px;border-top-right-radius: 30px;margin-left: 4px;height: 42px;width: 100%;">
                            <p class="text-light" style="margin-top:9px;">Mat. Proyectada</p></h6>
                        </div>
                    </div>
                    <div class="col-2">
                        <div style=" display: flex;color: black;">
                            <span class="bg-secondary" style="border-bottom: 2px solid;margin-right: -4px;border-top: 2px solid;border-left:2px solid;border-top-left-radius: 30px;border-bottom-left-radius: 28px;min-width: 27px;height: 42px;">&nbsp;&nbsp;&nbsp;<br><br></span>
                            <h6 class="card-title bg-secondary" style="border-top: 2px solid;border-bottom: 2px solid;border-right: 2px solid;border-bottom-right-radius: 30px;border-top-right-radius: 30px;margin-left: 4px;height: 42px;width: 100%;">
                            <p class="text-light" style="margin-top:9px;">Mat. Por ver</p></h6>
                        </div>
                    </div>
                </div>`);
                    } else {
                        Swal.fire({
                            icon: "info",
                            title: "No hay información disponible",
                            text: 'Verifica el id Banner insertado'
                        });
                    }
                }
            });
        }

        function consultaEstudiante(codBanner) {
            var formData = new FormData();
            formData.append('codBanner', codBanner);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: "{{ route('historial.consulta') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                //beforeSend: function(){
                //  $('#codigo').prop('disabled',true);
                // },
                success: function(data) {
                    $('.contenedor_interno').removeClass('hidden');
                    $('#myTabs').empty();
                    $('#codigo').prop('disabled', false);

                    if (data != 'no tiene historial') {

                        data.forEach(function(tab, index) {
                            // Crear la pestaña
                            var tabLink = $('<a>')
                                .addClass('nav-link datos ')
                                .attr('data-toggle', 'tab')
                                .attr('data-id', codBanner)
                                .attr('data-programa', tab.cod_programa)
                                .attr('data-tap', 'tab' + index)
                                .attr('data-nombre', tab.programa)
                                .attr('href', '#tab' + index)
                                .attr('id', 'tab_li' + index)
                                .append(
                                    $('<span>').text(tab.programa),
                                );

                            // Agregar la pestaña a la lista de pestañas
                            $('#myTabs').append($('<li>').append(tabLink));

                            // Crear el contenido de la pestaña
                            var tabContent = $('<div>')
                                .addClass('tab-pane fade taps_programas ')
                                .attr('id', 'tab' + index);

                            var tabLink = $('<a>')
                                .addClass('nav-link datos ')
                                .attr('data-toggle', 'tab')
                                .attr('data-id', codBanner)
                                .attr('data-programa', tab.cod_programa)
                                .attr('data-tap', 'tab' + index)
                                .attr('href', '#tab' + index)
                                .text(tab.programa);

                            $(tabContent).append()
                            // Puedes poner un mensaje mientras carga el contenido

                            // Agregar el contenido de la pestaña al contenedor
                            $('.tab-content').append(tabContent);

                        });

                        // Agregar el listener para el evento de cambio de pestaña
                        $('#myTabs a').on('shown.bs.tab', function(event) {
                            var targetTab = $(event.target).attr('href');
                            cargarContenido(
                                targetTab); // Llama a la función para cargar contenido
                        });

                        window.setTimeout(function() {
                            elemto = $(document).find('#tab_li0')
                            elemto.addClass("active")

                            $(document).find("#tab0").addClass('active show');

                            elemto.click();
                        }, 700);
                    } else {
                        Swal.fire({
                            icon: "info",
                            title: "Oops...",
                            text: "No tienes historial academico"
                        })
                    }
                }

            });
        }

        function consultaProgramas(codBanner) {
            var formData = new FormData();
            formData.append('codBanner', codBanner);
            var programas;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: "{{ route('historial.consultaprogramas') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                // beforeSend: function(){
                //    $('#codigo').prop('disabled',true);
                // },
                success: function(data) {
                    $('#codigo').prop('disabled', false);
                    programas = data;

                }
            });


        }

        $(document).on("click", ".datosmateria", function() {

            let codprograma = $(this).data('codprograma');
            let codmateria = $(this).data('codigo');

            var formData = new FormData();
            formData.append('programa', codprograma);
            formData.append('materia', codmateria);

            limpiarModal();

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
                    var DataPrerequisito = [];
                    var DataEquivalencia = [];

                    if (data.Prerequisito) {
                        $('#tituloprerequisito').empty();
                        $('#tituloprerequisito').append('Prerequisitos');

                        $.each(data.Prerequisito, function(key, value) {
                            var rowDataPrerequisito = [
                                value.codigoCurso,
                                value.curso,
                                value.semestre,
                            ]
                            DataPrerequisito.push(rowDataPrerequisito);
                        });

                        $('#prerrequisitos').DataTable({
                            "data": DataPrerequisito,
                            "pageLength": 10,
                            "columns": [{
                                    title: 'Codigo materia',
                                    className: 'dt-center'
                                },
                                {
                                    title: 'Curso'
                                },
                                {
                                    title: 'Semestre',
                                    className: 'dt-center'
                                }
                            ],
                            "order": [2, 'asc'],
                            "searching": false,
                            "paging": false,
                            "info": false
                        });
                    } else {
                        $('#tituloprerequisito').empty();
                        $('#tituloprerequisito').append(
                            'Este curso no tiene Prerequisitos');
                    }

                    if (data.Equivalencia) {
                        $('#tituloequivalencias').empty();
                        $('#tituloequivalencias').append('Equivalencias');
                        $.each(data.Equivalencia, function(key, value) {
                            var rowDataEquivalencia = [
                                value.codigoCurso,
                                value.curso,
                                value.semestre,
                            ]
                            DataEquivalencia.push(rowDataEquivalencia);
                        });

                        $('#equivalencias').DataTable({
                            "data": DataEquivalencia,
                            "pageLength": 10,
                            "columns": [{
                                    title: 'Codigo materia',
                                    className: 'dt-center'
                                },
                                {
                                    title: 'Curso'
                                },
                                {
                                    title: 'Semestre',
                                    className: 'dt-center'
                                }
                            ],
                            "order": [2, 'asc'],
                            "searching": false,
                            "paging": false,
                            "info": false
                        });
                    } else {
                        $('#tituloequivalencias').empty();
                        $('#tituloequivalencias').append(
                            'Este curso no tiene Equivalencias');
                    }

                }
            });

        })

        $(document).on("click", ".botonreglas", function(e) {
            e.stopPropagation();
            var formData = new FormData();
            formData.append('programa', programa);
            var programas;
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: "{{ route('historial.reglas') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                // beforeSend: function(){
                //    $('#codigo').prop('disabled',true);
                // },
                success: function(data) {
                    $('#tituloreglas strong').empty();
                    $('#tituloreglas strong').append('Reglas programa: ' + nombrePrograma);

                    $('#datareglas').empty();

                    $('#datareglas').append(data[0].descripcion);

                }
            });
        });

        function limpiarModal() {
            if ($('#prerrequisitos') && $.fn.DataTable.isDataTable('#prerrequisitos')) {
                $('#prerrequisitos').dataTable().fnDestroy();
                $('#prerrequisitos thead').empty();
                $('#prerrequisitos tbody').empty();
                $('#prerrequisitos tfooter').empty();
            }

            if ($('#equivalencias') && $.fn.DataTable.isDataTable('#equivalencias')) {
                $('#equivalencias').dataTable().fnDestroy();
                $('#equivalencias thead').empty();
                $('#equivalencias tbody').empty();
                $('#equivalencias tfooter').empty();
            }
        }

    })
</script>
<!-- End of Main Content -->

<!-- Footer -->
<footer class="sticky-footer bg-white">
    <div class="container my-auto">
        <div class="copyright text-center my-auto">
            <span>Copyright &copy; Corporación Universitaria Iberoamericana 2023 - Integrado al ecosistema de
                innovación
                educativa.</span>
        </div>
    </div>
</footer>

<!--===============================================================================================-->
<!-- Bootstrap core JavaScript-->

<script src="{{ asset('general/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<!-- Core plugin JavaScript-->
<script src="{{ asset('general/vendor/jquery-easing/jquery.easing.min.js') }}"></script>

<!-- Custom scripts for all pages-->
<script src="{{ asset('general/js/sb-admin-2.min.js') }}"></script>

<!-- Font awesome for Icons-->
{{-- <script src="https://kit.fontawesome.com/def3229fdd.js" crossorigin="anonymous"></script> --}}

<!--Tooltip-->
<script>
    $('[data-toggle="tooltip"]').tooltip()
    var j = jQuery.noConflict();
    j(document).ready(function() {
        j('[data-toggle="tooltip"]').tooltip();
    });
</script>

</html>
