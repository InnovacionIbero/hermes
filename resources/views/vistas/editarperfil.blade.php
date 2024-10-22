@include('layout.header')

@auth
    @include('menus.menu')    
@endauth
<!--  creamos el contenido principal body -->

<!-- Content Wrapper -->
<script src="https://code.jquery.com/jquery-3.5.1.js" integrity="sha256-QWo7LDvxbWT2tbbQ97B53yJnYU3WhH/C8ycbRAkjPDc=" crossorigin="anonymous"></script>

<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow" style="background-image: url('https://moocs.ibero.edu.co/hermes/front/public/assets/images/fondoCabecera.png');">

            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>

            <div class="input-group">
                <div class="input-group-append text-gray-800 text-center">
                    <h3><strong> Bienvenido {{ auth()->user()->nombre }}! - Editar perfil </strong></h3>
                </div>
            </div>

        </nav>

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <section style="background-color: #eee;">
                <div class="container py-5">

                    <div class="row">
                        <div class="col-lg-4">
                            <div class="card mb-4">
                                <div class="card-body text-center">
                                    <img src="https://e7.pngegg.com/pngimages/178/595/png-clipart-user-profile-computer-icons-login-user-avatars-monochrome-black.png" alt="avatar" class="rounded-circle img-fluid" style="width: 150px;">
                                    <h5 class="my-3">{{ $datos['user']->nombre }}</h5>
                                    <p class="text-muted mb-1">{{ $datos['rol'] }}</p>
                                    <p class="text-muted mb-4">{{ $datos['facultad'] }}</p>
                                </div>
                            </div>
                            <div class="text-center mt-4" {{ auth()->user()->id_rol != 9 ? 'style= display:none;' : '' }}>
                                <button type="button" class="btn btn-secondary" id="botonReestablecer" data-toggle='modal' data-target='#modalHistorial'> Reestablecer contraseña </button>
                            </div>
                        </div>
                        <!--Datos del Usuario-->
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <form action="{{ route('user.actualizar', ['id' => encrypt($datos['user']->id)]) }}" method="POST" id="miForm">
                                    @csrf
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-sm-3 text-dark">
                                                <p class="mb-0">Id Banner</p>
                                            </div>
                                            <div class="col-sm-9">
                                                <p class="text-muted mb-0"> <input type="number" class="form-control" name="id_banner" value="{{ $datos['user']->id_banner }}" {{ auth()->user()->id_rol != 9 ? 'disabled' : '' }}></p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3 text-dark">
                                                <p class="mb-0">Documento de identidad</p>
                                            </div>
                                            <div class="col-sm-9">
                                                <p class="text-muted mb-0"><input type="number" class="form-control" name="documento" value="{{ $datos['user']->documento }}" {{ auth()->user()->id_rol != 9 ? 'disabled' : '' }}>
                                                </p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3 text-dark">
                                                <p class="mb-0">Nombre Completo</p>
                                            </div>
                                            <div class="col-sm-9">
                                                <p class="text-muted mb-0"> <input type="text" class="form-control" name="nombre" value="{{ $datos['user']->nombre }}"></p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3 text-dark">
                                                <p class="mb-0">Email</p>
                                            </div>
                                            <div class="col-sm-9">
                                                <p class="text-muted mb-0"><input type="email" class="form-control" name="email" value="{{ $datos['user']->email }}" {{ auth()->user()->id_rol != 9 ? 'disabled' : '' }}></p>
                                            </div>
                                        </div>
                                        <hr>
                                        @if ($roles != '')
                                        <div class="row">
                                            <div class="col-sm-3 text-dark">
                                                <p class="mb-0">Rol</p>
                                            </div>
                                            <div class="col mb-3">
                                                <select class="form-select" name="id_rol" id="rol" {{ auth()->user()->id_rol != 9 ? 'disabled' : '' }}>
                                                    @foreach ($roles as $rol)
                                                    <option {{ $rol->id == $datos['user']->id_rol ? 'selected' : '' }} value="{{ $rol->id }}">{{ $rol->nombreRol }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        @endif
                                        <hr {{ auth()->user()->id_rol != 9 ? 'style=display:none;' : '' }}>
                                        @if ($facultades != '' || ($facultades = null))
                                        <div class="row" {{ auth()->user()->id_rol != 9 ? 'style=display:none;' : '' }}>
                                            <div class="col-sm-3 text-dark">
                                                <p class="mb-0">Facultad</p>
                                            </div>
                                            <select class="form-select" name="facultades" id="facultades">
                                                @if ($datos['user']->id_facultad == '')
                                                <option value="" selected>Seleccione una facultad</option>
                                                @foreach ($facultades as $facultad)
                                                <option value="{{ $facultad->id }}">
                                                    {{ $facultad->nombre }}
                                                </option>
                                                @endforeach
                                                @else
                                                <option value="" selected>Seleccione una facultad</option>
                                                @foreach ($facultades as $facultad)
                                                <option {{ $facultad->id == $datos['user']->id_facultad ? 'selected="selected"' : '' }} value="{{ $facultad->id }}">{{ $facultad->nombre }}
                                                </option>
                                                @endforeach
                                                @endif
                                            </select>
                                        </div>
                                        @endif
                                        <hr>
                                        <div class="row" {{ auth()->user()->id_rol != 9 ? 'style=display:none;' : '' }}>
                                            <div class="col-sm-3 text-dark">
                                                <p class="mb-0">Programas</p>
                                            </div>
                                            <div class="col-sm-7 form-check">
                                                <div id="programas" name="programas"></div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="d-flex justify-content-center mb-2">
                                            <button type="submit" class="btn btn-outline-primary ms-1">Finalizar
                                                Actualización</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    @include('layout.footer')
</div>

<script>
    var idUsuario = <?php echo $datos['id']; ?>;

    $('#facultades').each(function() {

        programas = "{{ $datos['user']->programa }}";

        programasSeparados = programas.split(";").map(Number);

        id_facultad = $(this);

        console.log($('#facultades').val());

        if ($('#facultades').val() != '') {

            $.post('{{ route('registro.programas') }}', {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    idfacultad: id_facultad.val(),
                },
                
                function(data) {
                    /*id_facultades=[];
                    data.forEach(programa => {
                        id_facultades.push(parseInt(programa.id));
                        //console.log(id_facultades);
                        //console.log(programasSeparados.includes(programa.id));

                        //$('#programas').append(`<label><input type="checkbox" id="" name="programa[]" value="${programa.id}"> ${programa.programa}</label><br>`);
                    });*/
                    for (let i = 0; i < data.length; i++) {
                        if (programasSeparados.includes(data[i]['id'])) {
                            $('#programas').append(
                                `<label><input type="checkbox" checked id="" name="programa[]" value="${data[i]['id']}"> ${data[i]['programa']}</label><br>`
                            );
                        } else {
                            $('#programas').append(
                                `<label><input type="checkbox" id="" name="programa[]" value="${data[i]['id']}"> ${data[i]['programa']}</label><br>`
                            );
                        }
                    }

                })
        } else {
            $('#programas').empty();
        }
    });

    $('#facultades').change(function() {
        programas = "{{ $datos['user']->programa }}";
        programasSeparados = programas.split(";").map(Number);
        id_facultad = $(this);

        $.post('{{ route('registro.programas') }}', {
                _token: $('meta[name="csrf-token"]').attr('content'),
                idfacultad: id_facultad.val(),
            },
            function(data) {
                $('#programas').empty();
                $('#facultades').remove('option');
                for (let i = 0; i < data.length; i++) {
                    if (programasSeparados.includes(data[i]['id'])) {
                        $('#programas').append(
                            `<label><input type="checkbox" checked id="" name="programa[]" value="${data[i]['id']}"> ${data[i]['programa']}</label><br>`
                        );
                    } else {
                        $('#programas').append(
                            `<label><input type="checkbox" id="" name="programa[]" value="${data[i]['id']}"> ${data[i]['programa']}</label><br>`
                        );
                    }
                }
            })
    })

    var nombreUsuario = <?php echo json_encode($datos['user']->nombre); ?>;

    $('#botonReestablecer').on('click', function() {

        $.post('{{ route('renovar.password') }}', {
                _token: $('meta[name="csrf-token"]').attr('content'),
                id: idUsuario,
            },
            function(data) {
                if (data == 'Exito') {
                    Swal.fire({
                        title: "Contraseña Renovada",
                        text: "La contraseña del usuario " + nombreUsuario + ' ha sido renovada.',
                        icon: "success"
                    });
                } else {
                    Swal.fire({
                        title: "Error",
                        text: "No  ha sido posible renovar la contraseña",
                        icon: "error"
                    });
                }
            }
        );
    });

</script>