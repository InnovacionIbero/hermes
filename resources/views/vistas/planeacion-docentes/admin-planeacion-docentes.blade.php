<!-- incluimos el header para el html -->
@include('layout.header')
<link rel="stylesheet" href="{{asset('css/style-planeacion-docente.css') }}">
@auth
@include('menus.menu')
@endauth

<style>
    .navbar {
        clear: both;
        /* Asegúrate de que el nav se sitúe encima */
        width: 100%;
        /* Asegúrate de que ocupe todo el ancho */
    }
</style>

<div id="content-wrapper" class="d-flex flex-column">
    <!-- Main Content -->
    <div id="content">

        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow" style="background-image: url('https://moocs.ibero.edu.co/hermes/front/public/assets/images/fondoCabecera.png');">
            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

            <div class="input-group">
                <div class="input-group-append text-gray-800">
                    <h3><strong> Bienvenido {{auth()->user()->nombre}}! - Planeación docente </strong></h3>
                </div>
            </div>
        </nav>

        <div class="container-fluid container-planeacion-docentes">

            @include('layout.filtros-planeacion-docente')

            <div class="card shadow mb-4">
                <div class="card-header text-center">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link menuMoodle active" id="navdocentes-disponibles" href="#docentes-disponibles">Docentes disponibles.</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menuMoodle" id="navplaneacion" href="#planeacion">Planeación docentes.</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menuMoodle" id="navasignaturas-pendientes" href="#asignaturas-pendientes">Asignaturas pendientes.</a>
                        </li>
                    </ul>
                </div>
                <!-- Card Body -->
                <div class="card-body">
                    <div id="docentes-disponibles" class="content">
                        <button class="btn btn-success mt-2 mb-3 filtrar-docentes" data-tabla="activos">Docentes activos</button>
                        <button class="btn btn-danger mt-2 mb-3 filtrar-docentes" data-tabla="inactivos">Docentes inactivos</button>
                        <table class="mt display" id="datatable-docentes-disponibles"></table>
                    </div>
                    <div id="planeacion" class="content" >
                        <button class="btn btn-warning mt-2 mb-3 filtrar-asignacion-asignatura" >Filtrar por asignatura</button>
                        <button class="btn btn-info mt-2 mb-3 filtrar-asignacion-docente">Filtrar por docente</button>
                        <table class="mt display" id="datatable-planeacion-docentes" style="width:100%;"></table>
                    </div>
                    <div id="asignaturas-pendientes" class="content">
                        <table class="mt display" id="datatable-asignaturas-pendientes" style="width:100%;"></table>
                    </div>
                </div>
            </div>

            <div class="row justify-content-end">
                <div class="col mt-4 mb-4">
                    <button class="btn btn-secondary btn-sm" id="button-crear-docente" data-toggle="modal" data-target="#modal-nuevo-docente">Agregar nuevo docente</button>
                </div>
            </div>

        </div>

    </div>

    <script>
        /** Función reset Form Modal */
        const cerrarModal = () => {
            $('#modal-nuevo-docente').modal('hide')
            const form = $('#miForm');
            $('#programas-seleccionados-docente').empty();
            $('#asignaturas-docente').empty();
            $('#asignaturas-seleccionadas-docente').empty();
            form.trigger("reset");
        }
    </script>

    <!--Modal para agregar nuevo docente-->
    <div class="modal fade" id="modal-nuevo-docente" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Agregar nuevo docente</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="cerrarModal();">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="miForm" method="post">
                        @csrf
                        <div>
                            <label for="recipient-name" class="col-form-label">Nombre docente</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div>
                            <label for="message-text" class="col-form-label">Id Banner docente</label>
                            <input type="text" class="form-control" id="id-banner" name="id-banner" required>
                        </div>
                        <div>
                            <label for="message-text" class="col-form-label">Email docente</label>
                            <input type="email" class="form-control" id="nombre" name="email" required>
                        </div>
                        <div>
                            <label for="cupo-docente" class="col-form-label">Cupo del docente</label>
                            <input type="number" class="form-control" id="cupo-docente" name="cupo-docente" required>
                        </div>
                        @if($isTransversal !== 1)
                        <div>
                            <label for="programas-docente" class="col-form-label">Programas</label>
                            <select class="form-control" id="programas-docente" name="programas-docente">
                            </select>
                        </div>
                        <div class="mt-2">
                            <label class="col-form-label">Programas Seleccionados</label>
                            <ul id="programas-seleccionados-docente" class="list-group">

                            </ul>
                        </div>
                        @endif
                        <div>
                            <label for="asignaturas-docente" class="col-form-label">Asignaturas</label>
                            <select class="form-control" id="asignaturas-docente" name="asignaturas-docente">
                            </select>
                        </div>
                        <div class="mt-2">
                            <label class="col-form-label">Asignaturas Seleccionadas</label>
                            <p>
                                Las asignaturas que checkees serán las preferentes del docente, recuerda que son máximo 3.
                            </p>
                            <ul id="asignaturas-seleccionadas-docente" class="list-group">

                            </ul>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal" onclick="cerrarModal();">Cancelar</button>
                            <button type="submit" class="crear btn btn-success">Crear</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if($isTransversal !== 1)
    <script type="module" src="{{ asset('js/planeacion-docentes-estandar.js') }}"></script>
    @else
    <script type="module" src="{{ asset('js/planeacion-docentes-transversal.js') }}"></script>
    @endif

    @include('layout.footer')
</div>