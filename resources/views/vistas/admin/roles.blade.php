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
                <h1 class="h3 mb-0 text-gray-800">Roles</h1>
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
                        <div class="col-4 justify-content-center">
                            <button href="#" class="agregar btn btn-secondary" data-toggle="modal" data-target="#nuevoRol" data-whatever="modal">Agregar nuevo rol</button>
                        </div>
                        <br>
                    </div>
                </div>
            </div>

            <!--Modal para agragar un rol nuevo-->
            <div class="modal fade" id="nuevoRol" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Agregar nuevo rol</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="miForm" method="post" action="{{ route('rol.crear') }}">
                                @csrf
                                <div>
                                    <label for="message-text" class="col-form-label">Nombre del rol</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="crear btn btn-success">Crear</button>
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

<!-- Alertas al crear usuario -->
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
    // * Datatable para mostrar todas las Facultades *
    $('#menuRoles').addClass('activo');    
    $('#collapseGestiónUsuarios').addClass('collapse show');  

    var xmlhttp = new XMLHttpRequest();
    var url = "{{ route('admin.getroles') }}";
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(this.responseText);
            var table = $('#example').DataTable({
                "data": data.data,
                "columns": [{
                        data: 'nombreRol',
                        title: 'Rol'
                    },
                    {
                        defaultContent: "<button type='button' class='editar btn btn-warning'><i class='fa-solid fa-pen-to-square'></i></button>",
                        title: 'Editar',
                        className: "text-center"
                    },
                    {
                        data: 'activo',
                        defaultContent: "",
                        title: "Estado",
                        className: "text-center",
                        render: function(data, type, row) {
                            if (data == '1') {
                                return 'Activo';
                            } else if (data == '0') {
                                return 'Inactivo';
                            }
                        }
                    },
                    {
                        data: 'activo',
                        defaultContent: "",
                        title: 'Inactivar / Activar',
                        className: "text-center",
                        render: function(data, type, row) {
                            if (data == '1') {
                                return "<button class='inactivar btn btn-success' type='button' id='boton'><i class='fa-solid fa-unlock'></i></button>";
                            } else if (data == '0') {
                                return "<button class='inactivar btn btn-danger' type='button' id='boton'><i class='fa-solid fa-lock'></i></button>";
                            }
                        }
                    }
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                },
                //lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            });
            console.log(table);

            function obtener_data_inactivar(tbody, table) {
                $(tbody).on("click", "button.inactivar", function(event) {
                    var data = table.row($(this).parents("tr")).data();
                    if (data.activo == 1) {
                        Swal.fire({
                            title: "¿Desea inactivar el rol " + data.nombreRol + "?",
                            icon: 'warning',
                            showCancelButton: true,
                            showCloseButton: true,
                            cancelButtonColor: '#DC3545',
                            cancelButtonText: "No, Cancelar",
                            confirmButtonText: "Si"
                        }).then(result => {
                            if (result.value) {
                                $.post("{{ route('rol.inactivar') }}", {
                                        '_token': $('meta[name=csrf-token]').attr('content'),
                                        id: data.id,
                                    },
                                    function(result) {
                                        console.log(result);
                                        if (result == "deshabilitado") {
                                            Swal.fire({
                                                title: "Rol deshabilitado",
                                                html: "El Rol <strong>" + data.nombreRol +
                                                    "</strong> ha sido inactivado",
                                                icon: 'info',
                                                showCancelButton: true,
                                                confirmButtonText: "Aceptar",
                                            }).then(result => {
                                                if (result.value) {
                                                    location.reload();
                                                };
                                            })
                                        }
                                    })
                            }
                        });

                    } else {
                        Swal.fire({
                            title: "¿Desea activar el rol " + data.nombreRol + "?",
                            icon: 'warning',
                            showCancelButton: true,
                            showCloseButton: true,
                            cancelButtonColor: '#DC3545',
                            cancelButtonText: "No, Cancelar",
                            confirmButtonText: "Si"
                        }).then(result => {
                            if (result.value) {
                                $.post("{{ route('rol.activar') }}", {
                                        '_token': $('meta[name=csrf-token]').attr('content'),
                                        id: data.id,
                                    },
                                    function(result) {
                                        if (result == "habilitado") {
                                            Swal.fire({
                                                title: "Rol habilitado",
                                                html: "El rol <strong>" + data.nombreRol +
                                                    "</strong> ha sido habilitado",
                                                icon: 'info',
                                                showCancelButton: true,
                                                confirmButtonText: "Aceptar",
                                            }).then(result => {
                                                if (result.value) {
                                                    location.reload();
                                                };
                                            })
                                        }
                                    })
                            }
                        });
                    }
                });
            }

            // Función para editar Rol
            function obtener_data_editar(tbody, table) {
                $(tbody).on("click", "button.editar", function() {
                    var data = table.row($(this).parents("tr")).data();
                    $('#facultadEditar').val(data.idFacultad);
                    const {
                        value: facultad
                    } = Swal.fire({
                        title: 'Actualizar información',
                        html: '<form>' +
                            '<input type="text" id="nombreRol" name="nombreRol" value="' + data.nombreRol + '" class="form-control" placeholder="nombre Rol"> <br>',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        cancelButtonText: "Cancelar",
                        confirmButtonText: 'Editar'
                    }).then(result => {
                        console.log(1);
                        console.log(result);
                        if (result.value) {
                            console.log(facultad);
                            $.post("{{ route('rol.update')}}", {
                                    '_token': $('meta[name=csrf-token]').attr('content'),
                                    id: encodeURIComponent(window.btoa(data.id)),
                                    nombre: $(document).find('#nombreRol').val(),
                                },
                                function(result) {
                                    console.log(result);
                                    if (result == "actualizado") {
                                        Swal.fire({
                                            title: "Información actualizada",
                                            icon: 'success'
                                        }).then(result => {
                                            location.reload();
                                        });

                                    }
                                }
                            )
                        }
                    })
                });
            }
            obtener_data_editar("#example tbody", table);
            obtener_data_inactivar("#example tbody", table);
        }
    }
</script>
@include('layout.footer')