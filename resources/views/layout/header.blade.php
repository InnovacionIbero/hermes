<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="https://aulavirtual.ibero.edu.co/pluginfile.php?file=%2F1%2Ftheme_adaptable%2Ffavicon%2F1693948501%2FImagen-5.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <title>Hermes - Ibero | Sistema para la gestión de programas virtuales</title>

    <!-- ponemos los estilos y complementos necesarios para las paginas -->
    <link rel="stylesheet" href="{{asset('css/app.css')}}">

    <!-- Custom fonts for this template-->
    <link href="{{asset('general/vendor/fontawesome-free/css/all.min.css')}}" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="{{asset('general/css/sb-admin-2.min.css')}}" rel="stylesheet">

    {{-- <script src="{{asset('general/vendor/jquery/jquery.min.js')}}"></script> --}}
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>

    {{-- Datatable --}}
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" defer>
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

    {{-- Charts.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
    <script src="https://unpkg.com/chart.js-plugin-labels-dv/dist/chartjs-plugin-labels.min.js"></script>

    {{--Excel con JS--}}
    <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"> </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</head>


<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <style>
            .rounded-button {
                border-radius: 50%;
                width: 50px;
                height: 50px;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            #botonSolicitud {
                position: fixed;
                bottom: 20px;
                right: 20px;
                z-index: 9999;
            }

            #botonCodProgramas {
                position: fixed;
                bottom: 80px;
                right: 20px;
                z-index: 9999;
            }

            #mensajeBoton {
                position: absolute;
                transform: translateX(-65%);
                width: 200px;
                height: 30px;
                align-items: center;
                justify-content: center;
                background-color: #333;
                color: #fff;
                padding: 5px;
                border-radius: 5px;
                display: none;
                font-size: 14px;
            }

            .button-container {
                position: relative;
                display: inline-block;
            }

            .rounded-button:hover #mensajeBoton {
                display: block;
            }

            #solicitudContainer, #buscarCodigoPrograma {
                display: none;
                position: fixed;
                bottom: 80px;
                right: 20px;
                background-color: white;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                z-index: 9999;
                width: 300px;
                height: 350px;
            }

            #buscarCodigoPrograma {
                display: none;
                position: fixed;
                bottom: 135px;
                right: 20px;
                background-color: white;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 5px;
                z-index: 9999;
                width: 300px;
                min-height: 200px;
                max-height: 200px;
                overflow: auto;
            }

            #solicitud {
                min-height: 180px;
                max-height: 180px;
            }

        </style>

        <button class="btn btn-primary btn-lg floating-button rounded-button" id="botonSolicitud">
            <i class="fa-solid fa-headset"></i>
            <span id="mensajeBoton">¿Necesitas ayuda? Escríbenos</span>
        </button>

        <button class="btn btn-primary btn-lg floating-button rounded-button" id="botonCodProgramas">
            <i class="fa-solid fa-folder-open"></i>
            <span id="mensajeBoton">Códigos de programas</span>
        </button>

        <div id="solicitudContainer">
            <form action="#" id="formSolicitud">
                <select name="tipoSolicitud" id="tipoSolicitud" class="form-control mb-2" required>
                    <option value="" selected disabled>Escoge una opción</option>
                    <option value="Carga">Se queda cargando</option>
                    <option value="Informacion">Información herrada</option>
                    <option value="Graficos">No cargan gráficos</option>
                    <option value="Descarga">No descarga excel</option>
                    <option value="Otro">Otro</option>
                </select>
                <p><textarea id="solicitud" class="form-control" name="mensaje" placeholder="Cuentanos tu problema..." required></textarea></p>
                <input id="idUsuario" name="idUsuario" type="number" style="display:none;" value="{{auth()->user()->id}}">
                <button type="submit" id="enviarData" name="enviarData" class="btn btn-primary mt-2">Enviar solicitud</button>
            </form>
        </div>

        <div id="buscarCodigoPrograma">
            <div name="buscarProgramas">
                <input type="text" class="form-control mb-2" id="buscadorCodigosProgramas" placeholder="Busca programa">
                <ul id="codigosProgramas"></ul>
            </div>
        </div>

        <!--@yield('content')-->