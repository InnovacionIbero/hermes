<!-- incluimos el header para el html -->
@include('layout.header')

<!-- incluimos el menu -->

@include('menus.menu')
<!--  creamos el contenido principal body -->

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
                    <h3><strong> Bienvenido {{ auth()->user()->nombre }}! - Cambio contraseña </strong></h3>
                </div>
            </div>

            </nav>
        <!-- End of Topbar -->

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
                                    <h5 class="my-3">{{auth()->user()->nombre}}</h5>
                                    <p class="text-muted mb-1">{{ $datos['rol'] }}</p>
                                    @if($datos['facultad'] != NULL)
                                    <p class="text-muted mb-1">{{ $datos['facultad'] }}</p>
                                    @endif
                                    @if ($datos['programa'] != NULL)
                                    <p class="text-muted mb-1">Programas</p>
                                    @foreach($datos['programa'] as $programa)
                                    <p class="text-muted mb-1">{{ $programa }}</p>
                                    @endforeach
                                    <br>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <form action="{{ route('cambio.cambiosave') }}" method="post" id="miForm">
                                    @csrf
                                    <div class="card-body">
                                        <div>
                                            <h3 class="text-center">
                                                Cambio de contraseña
                                            </h3>
                                        </div>
                                        <hr>
                    
                                        <input type="hidden" name="id" value="{{ auth()->user()->id }}">
                                        <div class="row">
                                            <div class="col-sm-3 text-dark">
                                                <p class="mb-0">Contraseña actual</p>
                                            </div>
                                            <div class="col-sm-9">
                                                <p class="text-muted mb-0"><input class="form-control" type="password" name="password_actual" placeholder="Contraseña actual" id="contraseña" required></p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3 text-dark">
                                                <p class="mb-0">Contraseña nueva</p>
                                            </div>
                                            <div class="col-sm-9">
                                                <p class="text-muted mb-0"><input class="form-control" type="password" name="password" placeholder="Contraseña nueva" id="nueva" required></p>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-sm-3 text-dark">
                                                <p class="mb-0">Confirmar contraseña</p>
                                            </div>
                                            <div class="col-sm-9">
                                                <p> <input class="form-control" type="password" name="password_confirmacion" placeholder="Confirmar contraseña" id="confirmar" required></p>
                                            </div>
                                        </div>
                                        <br>
                                        {{-- <button type="submit" class="form-btn" onclick="return validacion()">
                            Cambiar contraseña
                        </button> --}}
                                        <div class="d-flex justify-content-center mb-2">
                                            <button type="submit" class="btn btn-secondary">
                                                Cambiar contraseña
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        </section>
        <script>
    $(document).ready(function(){
        $('#menuCambiar').addClass('activo');
        $('#botonPerfil').removeClass('collapsed');
        $('#collapseFive').addClass('collapse show');
    });
</script>
    </div>


<!-- Alertas al cambiar contraseña -->
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
        // * Función para enviar alerta al usuario *
        function validacion() {

            // * Validación para verificar que todos los campos contengan información *
            if ($('#contraseña').val() && $('#nueva').val() && $('#confirmar').val()) {
                $("#miForm").submit(function(e) {
                    e.preventDefault();
                    // * Sweet alert *
                    Swal.fire({
                        title: '¿Estás seguro?',
                        text: "¡No podrás deshacer este cambio!",
                        icon: 'warning',
                        color: 'white',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Si, cambiar!',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            color: 'white'
                            Swal.fire(
                                'Cambio exitoso',
                                'Tu contraseña fue cambiada.',
                                'success'
                            )
                        }
                    })
                });
            }
        }
    </script>
    @include('layout.footer')
</div>