<!-- incluimos el header para el html -->
@include('layout.header')

<!-- incluimos el menu -->
@auth
    @include('menus.menu')
@endauth
<!--  creamos el contenido principal body -->
<style>
    #tablaCursos {
     table-layout: auto; 
     width: 100%; 
     }
 
     #tablaCursos_wrapper {
     position: relative; 
     }
 
     #tablaCursos th, #tablaCursos td {
         overflow: visible; 
         white-space: normal; 
     }
 
     #facultades {
         font-size: 14px;
     }
 
     #programas {
         font-size: 14px;
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
 
     #generarReporte {
         width: 250px;
         height: 45px;
         font-size: 20px;
     }
 
     #btn-table {
         width: 60px;
     }
 
     .botonModal {
         background-color: #dfc14e;
         border-color: #dfc14e;
         color: white;
         width: 100px;
         height: 30px;
         border-radius: 10px;
         font-weight: bold;
         place-items: center;
         font-size: 14px;
         display: flex;
         justify-content: center;
         align-items: center;
     }
 
     #pruebas {
         background-color: #dfc14e;
         border-color: #dfc14e;
         color: white;
         width: 250px;
         height: 50px;
         border-radius: 10px;
         font-weight: bold;
         place-items: center;
         font-size: 16px;
         display: flex;
         justify-content: center;
         align-items: center;
     }
 
     .botonInfoMoodle {
         background-color: #dfc14e;
         border-color: #dfc14e;
         color: white;
         width: 100px;
         height: 30px;
         border-radius: 10px;
         place-items: center;
         font-weight: bold;
         font-size: 14px;
         display: flex;
         justify-content: center;
         align-items: center; 
     }
 
     .botonDescargarInforme {
         background-color: #dfc14e;
         border-color: #dfc14e;
         color: white;
         width: 300px;
         height: 50px;
         border-radius: 10px;
         font-weight: bold;
         place-items: center;
         font-size: 16px;
         display: flex;
         justify-content: center;
         align-items: center;
     }
 
     .botonMafi {
         background-color: #dfc14e;
         border-color: #dfc14e;
         color: white;
         width: 200px;
         height: 30px;
         border-radius: 10px;
         font-weight: bold;
         place-items: center;
         font-size: 14px;
         display: flex;
         justify-content: center;
         align-items: center;
     }
 
     #botondataTable {
         background-color: #dfc14e;
         border-color: #dfc14e;
         color: white;
         width: 250px;
         height: 30px;
         border-radius: 10px;
         font-weight: bold;
         place-items: center;
         font-size: 14px;
         display: flex;
         justify-content: center;
         align-items: center;
     }
 
     .boton {
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
 
     .card {
         margin-bottom: 3%;
     }
 
     .hidden {
         display: none;
     }
 
     .graficos {
         min-height: 450px;
         max-height: 450px;
     }
 
     .graficosRiesgo {
         min-height: 350px;
         max-height: 350px;
         font-size: 13px;
     }
 
     .graficosRiesgo p {
         font-size: 15px;
     }
 
     #cardProgramas {
         max-height: 500px;
     }
 
     .graficosBarra {
         min-height: 450px;
         max-height: 450px;
     }
 
     #tiposEstudiantesTotal,
     #operadoresTotal,
     #programasTotal,
     #metasTotal {
         height: 600px !important;
     }
 
     #seccion {
         background: #DFE0E2;
     }
 
     .center-chart {
         display: flex;
         justify-content: center;
         align-items: center;
     }
 
     .fondocards {
         color: white;
         background-color: #3A6577;
     }
 
     .fondocharts {
         background-color: #DFE0E2;
     }
 
     .custom-text {
         margin-top: 7%;
         font-size: 1em;
         color: black;
         font-family: sans-serif;
     }

     .custom-carousel-control {
        font-size: 12px; 
        width: 40px;
        height: 40px; 
        border:none;
        background-color: #7e7e7e;
        border-radius: 10px;
        margin-top: 21%;
    }
 </style>

<script>
    tabla = 'moodlecerrados';
</script>
<script src="{{ asset('js/alerta.js') }}"></script> 
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc="
    crossorigin="anonymous"></script>
<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">
        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow"
            style="background-image: url('https://moocs.ibero.edu.co/hermes/front/public/assets/images/fondoCabecera.png');">

            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

            <div class="input-group">
                <div class="input-group-append text-gray-800 text-center">
                    <h3><strong> Bienvenido
                     {{ auth()->user()->nombre }}! - Informe de Matriculas Cerradas </strong></h3>
                </div>
            </div>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">
            <!-- Page Heading -->

            <div class="text-center" id="mensaje">
                <h5>Por defecto se muestran los datos de todas las facultades, si quieres ver datos en particular, por
                    favor seleccionar según se requiera</h5>
            </div>
            <br>

            <!-- incluimos los filtros  -->

            @include('layout.filtros')
       
            <!-- incluimos los gráficos-->
            @include('layout.graficosMoodlecerrado')

            <!-- incluimos los modals -->
            @include('layout.modals')

        </div>
    </div>

    <script>
      
        get_program_filtro=@json(route('filtros.programas'));
    </script>
    <script src="{{ asset('js/moodlecerrados.js') }}"></script>  
    <!-- incluimos el footer -->
    @include('layout.footer')
</div>
