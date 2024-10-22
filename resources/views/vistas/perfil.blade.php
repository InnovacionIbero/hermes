@include('layout.header')

@auth
    @include('menus.menu')    
@endauth

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
                <div class="input-group-append text-gray-800 text-center">
                    <h3><strong> Bienvenido {{ auth()->user()->nombre }} a tu perfil!</strong></h3>
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
                                    <h5 class="my-3">{{ auth()->user()->nombre }}</h5>
                                    <p class="text-muted mb-1"> {{ $datos['rol'] }}</p>

                                    <p class="text-muted mb-1">{{ $datos['facultad'] }}</p>
                                    @if ($datos['programa'] != NULL)
                                    <p class="text-muted mb-1">Programas</p>
                                    @foreach($datos['programa'] as $programa)
                                    <p class="text-muted mb-1">{{ $programa }}</p>
                                    @endforeach
                                    <br>
                                    @endif
                                    <div class="d-flex justify-content-center mb-2">
                                        <!--Botón que permite actualizar los datos del Usuario-->
                                        <a href="{{ route('user.editar',['id'=>encrypt(auth()->user()->id)]) }}">
                                            <button type="button" class="btn btn-outline-primary ms-1">Actualizar datos</button>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                            <!--Datos del Usuario-->
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3 text-dark">
                                            <p class="mb-0">Id Banner</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">{{ auth()->user()->id_banner }}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-3 text-dark">
                                            <p class="mb-0">Documento de identidad</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">{{auth()->user()->documento }}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-3 text-dark">
                                            <p class="mb-0">Email</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">{{auth()->user()->email }}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-sm-3 text-dark">
                                            <p class="mb-0">Rol</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">{{ $datos['rol']}}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    @unless(in_array($datos['rol'], ['Admin', 'Rector', 'Vicerrector']))
                                    <div class="row" >
                                        <div class="col-sm-3 text-dark">
                                            <p class="mb-0">Facultad</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">{{$datos['facultad'] }}</p>
                                        </div>
                                    </div>
                                    <hr>
                                    @if($datos['rol'] != 'Decano')
                                    <div class="row">
                                        <div class="col-sm-3 text-dark">
                                            <p class="mb-0">Programas</p>
                                        </div>
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">
                                                <!--Validación para saber si el usuario tiene algún programa-->
                                                @if($datos['programa'])
                                                <!--Ciclo para recorrer el array de programas e imprimirlos en pantalla-->
                                                @foreach ($datos['programa'] as $key => $value)
                                                {{$value}} <br>
                                                @endforeach
                                                @endif
                                            </p>

                                        </div>
                                    </div>
                                    <hr>
                                    @endif
                                    @endunless
                                    <div class="row">
                                        <div class="col-sm-3 text-dark">
                                            <p class="mb-0">Estado</p>
                                        </div>
                                        <!--Validación para verificar si el usuario se encuentra activo o no-->
                                        @if (auth()->user()->activo = 1)
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">Activo</p>
                                        </div>
                                        @else
                                        <div class="col-sm-9">
                                            <p class="text-muted mb-0">Inactivo</p>
                                        </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <a class="" href="{{ route('cambio.cambio', ['idbanner' => encrypt(auth()->user()->id_banner)]) }}" role="button"><u>Cambiar Contraseña</u></a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <script>
    $(document).ready(function(){
        $('#menuVerPerfil').addClass('activo');
        $('#botonPerfil').removeClass('collapsed');
        $('#collapseFive').addClass('collapse show');
    });
</script>
    @include('layout.footer')
</div>

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

