<!-- incluimos el header para el html -->
@include('layout.header')

@auth
    @include('menus.menu')    
@endauth
<!--  creamos el contenido principal body -->
<style>
    .rounded-button {
        border-radius: 50%;
        width: 50px;
        height: 50px;
    }

    #show-search-button {
        position: fixed;
        bottom: 20px;
        right: 80px;
        z-index: 9999;
    }

    #search-container {
        display: none;
        position: fixed;
        bottom: 80px;
        right: 20px;
        background-color: white;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        z-index: 9999;
        height: 100px;
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

    #slideplaneacion{
        width: 250px;
        height: 45px;
        font-size: 20px;
        margin-left: 3%; 
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

    .titulos {
        overflow: auto;
    }
</style>

<script> tabla='planeacion'</script>

<!-- Botón flotante para mostrar el input de búsqueda -->
<button class="btn btn-primary btn-lg floating-button rounded-button" id="show-search-button"><i class="fas fa-search"></i> </button>

<!-- Contenedor del input de búsqueda (inicialmente oculto) -->
<div id="search-container">
    <input type="text" id="buscarId" class="form-control" placeholder="Buscar estudiante...">
    <button id="buscarEstudiantePlaneacion" class="btn btn-primary mt-2" data-toggle='modal' data-target='#modaldataEstudiante'>Buscar</button>
</div>
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
                <div class="input-group-append text-gray-800">
                    <h3><strong> Bienvenido {{auth()->user()->nombre}}! - Informe Proyección - Planeación </strong></h3>
                </div>
            </div>
        </nav>
        <!-- End of Topbar -->

        

        <br>

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
            @include('layout.graficosPlaneacion')

            <!-- incluimos los modals -->
            @include('layout.modals')

        </div>

    </div>
    <script src="{{ asset('js/alerta.js') }}"></script>
    <script src="{{ asset('js/planeacion.js') }}"></script> 

    <!-- incluimos el footer -->
    @include('layout.footer')
</div>