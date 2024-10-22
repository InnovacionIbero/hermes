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
                <div>
                    <input type="text" id="facultadEditar" value='' name="facultadEditar" hidden>
                </div>
            </div>

        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Programas</h1>
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
                            <button href="#" class="agregar btn btn-secondary" data-toggle="modal" data-target="#nuevoprograma" data-whatever="modal">Agregar nuevo programa</button>
                        </div>
                        <br>
                    </div>
                </div>
            </div>

            <!--Modal para agregar un programa nuevo-->
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
                            <form id="miForm" method="post" action="{{ route('programa.crear') }}">
                                @csrf
                                <div>
                                    <label for="recipient-name" class="col-form-label">Codigo del programa</label>
                                    <input type="text" class="form-control" id="codPrograma" name="codPrograma">
                                </div>
                                <div>
                                    <label for="message-text" class="col-form-label">Nombre del programa</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre">
                                </div>
                                <input type="hidden" class="form-control" id="nivelformacion" name="nivelformacion" value="PROFESIONAL">
                                <div>
                                    <label for="message-text" class="col-form-label">Facultad a la que pertenece</label>
                                    <select class="form-control" name="codFacultad" id="codFacultad"></select>
                                </div>
                                <label for="ciclo" class="col-form-label">Opción de grado</label>
                                <br>
                                <div class="form-check form-check-inline" id="ciclo">
                                    <input class="form-check-input" type="checkbox" value="1" id="opciongrado" name="opciongrado">
                                    <label class="form-check-label" for="opciongrado">Tiene opción de grado</label>
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
    </div>
    <!-- /.container-fluid -->

    
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

    $('#menuProgramas').addClass('activo');    
    $('#collapseConfig').addClass('collapse show');

    facultades();
    function facultades() {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('registro.facultades') }}",
            method: 'post',
            success: function(data) {
                data.forEach(facultad => {
                    $('#nuevoprograma select#codFacultad').append(`<option value="${facultad.id}">${facultad.nombre}</option>`);
                    
                })
            }
        })
    }
    
    // * Datatable para mostrar todas las Facultades *
    var xmlhttp = new XMLHttpRequest();
    var url = "{{ route('facultad.getprogramas') }}";
    xmlhttp.open("GET", url, true);
    xmlhttp.send();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var data = JSON.parse(this.responseText);
            var table = $('#example').DataTable({
                "data": data.data,
                "columns": [{
                    data: 'codprograma',
                    title: 'Codigo de programa'
                },
                {
                    data: 'programa',
                    title: 'Programa'
                    },
                    {
                        data: 'Facultad',
                        title: 'Facultad'
                    },
                ],
                
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                },
                
            });

            /** Función para editar  */
            function obtener_data_editar(tbody, table) {
                $(tbody).on("click", "button.editar", function() {
                    var data = table.row($(this).parents("tr")).data();
                    $('#facultadEditar').val(data.idFacultad);
                    const {
                        value: facultad
                    } = Swal.fire({
                        title: 'Actualizar información',
                        html: '<form>' +
                            '<label for="codprograma"> Codigo del programa </label>'+
                            '<input type="text" id="codprograma" name="codprograma" value="' + data.codprograma + '" class="form-control" placeholder="codprograma"> <br>' +
                            '<label for="programa"> Nombre del programa </label>'+
                            '<input type="text" id="programa" name="programa" value="' + data.programa + '" class="form-control" placeholder="programa"> <br>' +
                            '<label for="facultades"> Facultad a la que pertenece el programa </label>'+
                            ' <select class="form-control" name="facultades" id="facultades"> <option value="' + data.idFacultad + '" selected>' + data.nombre + '</option> </select>',
                            icon: 'info',
                            showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        cancelButtonText: "Cancelar",
                        confirmButtonText: 'Editar'
                    }).then(result => {
                        if (result.value) {
                            $.post("{{ route('programa.update')}}", {
                                '_token': $('meta[name=csrf-token]').attr('content'),
                                id: encodeURIComponent(window.btoa(data.id)),
                                codigo: $(document).find('#codprograma').val(),
                                programa: $(document).find('#programa').val(),
                                idfacultad: $(document).find('#facultades').val(),
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
                    facultades();

                    function facultades() {
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            url: "{{ route('registro.facultades') }}",
                            method: 'post',
                            success: function(data) {
                                data.forEach(facultad => {
                                    if ($('#facultadEditar').val() != facultad.id) {
                                        $('#facultades').append(`<option value="${facultad.id}">${facultad.nombre}</option>`);
                                    };
                                })
                            }
                        })
                    }
                });
            }
            obtener_data_editar("#example tbody", table);
        }
    }
</script>

@include('layout.footer');
</div>