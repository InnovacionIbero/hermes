<!-- incluimos el header para el html -->
@include('layout.header')

<!-- incluimos el menu -->
@include('menus.menu_admin')
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
                <h1 class="h3 mb-0 text-gray-800">Malla curricular del programa {{$nombre}}</h1>
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
                            <!--Datatable-->
                            <div class="table">
                                <table id="example" class="display" style="width:100%">
                                </table>
                            </div>
                        </div>


                        <div class="col-4 justify-content-center">
                            <button href="#" class="agregar btn btn-secondary" data-toggle="modal" data-target="#nuevoprograma" data-whatever="modal">Agregar nuevo programa</button>
                        </div>
                        <br>
                    </div>
                </div>
                </div>

                <!--Modal para agragar un programa nuevo-->  
            <div class="modal fade" id="nuevoprograma" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Agregar nuevo programa</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="miForm" method="get" action="#">
                                @csrf
                                <div>
                                    <input type="number" id="id" name="id" hidden>
                                </div>
                                <div>
                                    <label for="recipient-name" class="col-form-label">Codigo del programa</label>
                                    <input type="text" class="form-control" id="editcodFacultad" name="editcodFacultad">
                                </div>
                                <div>
                                    <label for="message-text" class="col-form-label">Nombre del programa</label>
                                    <input type="text" class="form-control" id="editnombre" name="editnombre">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                            <button type="submit" class="crear btn btn-primary">Crear</button>
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

<script>
    // * Datatable para mostrar los programas de la Facultad *
    var xmlhttp = new XMLHttpRequest();
    var url = "{{ route('facultad.getmalla', ['codigo'=>$codigo]) }}";
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(this.responseText);
            var table = $('#example').DataTable({
                "data": data.data,
                "order": [[1,'asc'],[3,'asc']],
                "columns": [{
                        data: 'codprograma', "visible": false,
                        title: 'Codigo de programa'
                    },
                    {
                        data: 'semestre',
                        title: 'Semestre'
                    },
                    {
                        data: 'ciclo',
                        title: 'Ciclo'
                    },
                    {
                        data: 'orden',
                        title: 'Orden'
                    },
                    {
                        data: 'curso',
                        title: 'Curso'
                    },
                    {
                        data: 'codigoCurso',
                        title: 'Codigo curso'
                    },
                    {
                        data: 'creditos',
                        title: 'Numero de créditos'
                    },
                    {
                        data: 'prerequisito',
                        title: 'Pre-requisitos'
                    },
                    {
                        defaultContent: "<button type='button' class='editar btn btn-secondary' data-toggle='modal' data-target='#editar_facultad' data-whatever='modal'><i class='fa-solid fa-pen-to-square'></i></button>",
                        title: 'Editar',
                        className: "text-center"
                    },
                    {
                        defaultContent: "<button type='button' class='eliminar btn btn-danger'><i class='fa-regular fa-square-minus'></i></button>",
                        title: 'Eliminar',
                        className: "text-center"
                    },
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                },
                //lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            });
        }  
    }
    
</script>

@include('layout.footer')

