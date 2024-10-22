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
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow"
            style="background-image: url('https://moocs.ibero.edu.co/hermes/front/public/assets/images/fondoCabecera.png');">

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
                <h1 class="h3 mb-0 text-gray-800">Metas activas por programa</h1>
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
                            <button href="#" class="agregar btn btn-secondary" data-toggle="modal"
                                data-target="#nuevousuario" data-whatever="modal">Agregar nueva meta</button>
                        </div>
                        <br>
                    </div>
                </div>
            </div>

            <!--Modal para agregar un usuario nuevo-->
            <div class="modal fade" id="nuevousuario" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Agregar nueva meta</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="miForm" method="post" action="{{ route('meta.crear') }}">
                                @csrf
                                <div>
                                    <label for="recipient-name" class="col-form-label">Programa</label>
                                    <select class="form-control" name="programa" id="programa" required></select>
                                </div>
                                <div>
                                    <label for="message-text" class="col-form-label">Meta</label>
                                    <input type="number" class="form-control" id="meta"
                                        name="meta" required>
                                </div>
                                <div>
                                    <label for="message-text" class="col-form-label">Periodo</label>
                                    <select class="form-control" name="periodo" id="periodo" required></select>
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
            <!--Fin Modal-->
        </div>

    </div>
    <!-- /.container-fluid -->

</div>

@if (session('success'))
    <script>
        Swal.fire("Éxito", "{{ session('success') }}", "success");
    </script>
@endif

@if ($errors->any())
    <script>
        Swal.fire("Error", "{{ $errors->first() }}", "error");
    </script>
@endif
</div>


</div>
<!-- End of Content Wrapper -->

</div>
<script>
    $('#metasMenu').addClass('activo');
    $('#collapseConfig').addClass('collapse show');

    programas();
    periodos();

    function programas() {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('get.todos.programas') }}",
            method: 'post',
            success: function(data) {
                data.forEach(dato => {
                    $('#programa').append(
                        `<option value="${dato.codprograma}"> ${dato.programa}</option>`);
                })
            }
        })
    }

    function periodos() {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('get.periodos.ciclo1') }}",
            method: 'post',
            success: function(data) {
                data.forEach(dato => {
                    $('#periodo').append(
                        `<option value="${dato.periodos}"> ${dato.periodos}</option>`);
                })
            }
        })
    }

    var xmlhttp = new XMLHttpRequest();
    var url = "{{ route('metas.activas') }}";
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(this.responseText);
            var table = $('#example').DataTable({
                "data": data,
                "columns": [
                    {
                        data: 'id',
                        visible: false,
                    },
                    {
                        data: 'programa',
                        title: 'Programa'
                    },
                    {
                        data: 'meta',
                        title: 'Meta',
                        className: "text-center",
                    },
                    {
                        data: null,
                        title: "Periodo",
                        render: function(data, type, row) {
                            return data.año + data.periodo;
                        },
                        className: "text-center",
                    },
                    {
                        defaultContent: "<button type='button' class='editar btn btn-warning'><i class='fa-solid fa-pen-to-square'></i></button>",
                        title: 'Editar',
                        className: "text-center",
                    },
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                },
            });

            function obtener_data_editar(tbody, table) {
                $(tbody).on("click", "button.editar", function() {
                    var data = table.row($(this).parents("tr")).data();
                    Swal.fire({
                        title: 'Actualizar información',
                        html: '<form>' +
                            '<input type="text" id="editMeta" type="number" name="editMeta" value="' + data.meta + '" class="form-control"> <br>',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        cancelButtonText: "Cancelar",
                        confirmButtonText: 'Editar'
                    }).then(result => {
                        if (result.value) {
                            $.post("{{ route('meta.actualizar')}}", {
                                    '_token': $('meta[name=csrf-token]').attr('content'),
                                    id: encodeURIComponent(window.btoa(data.id)),
                                    metanueva: $(document).find('#editMeta').val(),
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
        }

    }
</script>

@include('layout.footer')
