<!-- incluimos el header para el html -->
@include('layout.header')

<!-- incluimos el menu -->
@include('menus.menu')
<!--  creamos el contenido principal body -->
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
                <h1 class="h3 mb-0 text-gray-800"> Solicitudes del sistema </h1>
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

                        <br>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        $('#Solicitudes').addClass('activo');

        var xmlhttp = new XMLHttpRequest();
        var url = "{{ route('traer.solicitudes') }}";
        xmlhttp.open("GET", url, true);
        xmlhttp.send();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                var data = JSON.parse(this.responseText);
                var table = $('#example').DataTable({
                    "data": data,
                    "columnDefs": [{ width: '20%', targets: 0 }],
                    "columns": [{
                            data: 'nombre',
                            title: 'Usuario'
                        },{
                            data: 'rol',
                            data:'nombreRol'
                        },
                        {
                            data: 'tipo_solicitud',
                            title: 'Tipo de solicitud'
                        },
                        {
                            data: 'solicitud',
                            title: 'Solicitud'
                        },
                        {
                            data: 'url',
                            title: 'URL'
                        },
                        {
                            data: 'fecha',
                            title: 'Fecha'
                        },
                        {
                            data: 'pendiente',
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
                });

                function obtener_data_inactivar(tbody, table) {
                    $(tbody).on("click", "button.inactivar", function(event) {
                        var data = table.row($(this).parents("tr")).data();
                        if (data.pendiente == 1) {
                            Swal.fire({
                                title: "¿La solicitud quedó resuelta?",
                                icon: 'warning',
                                showCancelButton: true,
                                showCloseButton: true,
                                cancelButtonColor: '#DC3545',
                                cancelButtonText: "No, Cancelar",
                                confirmButtonText: "Si"
                            }).then(result => {
                                if (result.value) {
                                    $.post("{{ route('solicitud.resuelta') }}", {
                                            '_token': $('meta[name=csrf-token]').attr('content'),
                                            id: data.id,
                                        },
                                        function(result) {
                                            if (result == "resuelto") {
                                                Swal.fire({
                                                    title: "Solicitud resuelta",
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
                obtener_data_inactivar("#example tbody", table);
            }
        }
    </script>

    @include('layout.footer')
</div>