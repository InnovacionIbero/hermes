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
                <h1 class="h3 mb-0 text-gray-800">Facultades</h1>
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
                        <!--
                        <div class="col-4 justify-content-center">
                            <button href="#" class="agregar btn btn-secondary" data-toggle="modal" data-target="#exampleModal" data-whatever="modal">Agregar nueva Facultad</button>
                        </div>
                -->
                        <br>
                    </div>
                </div>
            </div>

            <!--Modal para agregar nueva facultad-->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Agregar nueva faculad</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="miForm" method="post" action="{{ route('admin.guardarfacultad') }}">
                                @csrf
                                <div>
                                    <label for="recipient-name" class="col-form-label">Codigo de la facultad</label>
                                    <input type="text" class="form-control" id="codFacultad" name="codFacultad">
                                </div>
                                <div>
                                    <label for="message-text" class="col-form-label">Nombre de la facultad</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="crear btn btn-primary">Crear</button>
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
    $('#Duplicados').addClass('activo');    
 
    // * Datatable para mostrar todas las Facultades *
    var xmlhttp = new XMLHttpRequest();
    var url = "{{ route('aula.getduplicados') }}";
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(this.responseText);
            var table = $('#example').DataTable({
                data: data.data,
                dom: "Bfrtip",
                buttons: [
                    {
                        extend: "excel",
                        
                    },
                
                ],
                columns: [{
                        data: 'IdCurso',
                        title: 'id curso',
                    },
                    {
                        data: 'Id_Banner',
                        title: 'Id_Banner'
                    },
                    {
                        data: 'Nombre',
                        title: 'Nombre'
                    },
                    {
                        data: 'Apellido',
                        title: 'Apellido'
                    },
                    {
                        data: 'Nombrecurso',
                        title: 'Nombre curso'
                    },
                    {
                        data: 'Email',
                        title: 'Email'
                    },
                   
                   
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                },
                //lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            });

           

            // Función para editar Facultades
         
        }
    }
</script>
@include('layout.footer')