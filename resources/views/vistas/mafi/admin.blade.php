<!-- incluimos el header para el html -->
@include('layout.header')

<!-- incluimos el menu -->
@auth
    @include('menus.menu')    
@endauth
{{-- @include('menus.menu_Coordinador') --}}
<link rel="stylesheet" href="{{ asset('css/appalertas.css') }}">
<script>
    tabla = 'Mafi';
</script>
<script src="{{ asset('js/alerta.js') }}"></script> 
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
                <div class="input-group-append text-gray-800 text-center">
                    <h3><strong> Bienvenido {{ auth()->user()->nombre }}! - Informe de Admisiones (Argos) </strong></h3>
                </div>
            </div>
        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->


            <div class="text-center" id="mensaje">
                <h5>Por defecto se muestran los datos de todas las facultades, si quieres ver datos en particular, por favor seleccionar según se requiera
                </h5>
            </div>

            <br>

            <!-- incluimos los filtros  -->

            @include('layout.filtros')   
          
            <!-- llamamos los graficos -->
            @include('layout.graficos')
          

           
        </div>
        <!-- incluimos los el archivo de modals -->
        @include('layout.modals')   
        
        
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>

    <script src="{{ asset('js/mafi.js') }}"></script> 
    
    <!-- incluimos el footer -->
    @include('layout.footer')
</div>