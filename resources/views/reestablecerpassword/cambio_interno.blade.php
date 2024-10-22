
<!-- incluimos el header para el html -->
@include('layout.header')

<!-- incluimos el menu -->
@include('menus.menu')
<!--  creamos el contenido principal body -->

<div class="limiter">
    <div class="container-login100">
        <div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
            <!--Formulario para cambio de contraseña-->
            <form action="{{ route('cambio.cambiosave') }}" method="post" class="login100-form validate-form" id="miForm">
                @csrf
                <span class="login100-form-title p-b-49">
                    Cambio de contraseña
                </span>
                @if(count($errors)>0)
                    <h4>{{$errors}}</h4>
                @endif
                <input type="hidden" name="id" value="{{ auth()->user()->id }}">
                <div class="wrap-input100 validate-input m-b-23" data-validate="Contraseña actual es requerida">
                    <span class="label-input100">Contraseña actual</span>
                    <input class="input100" type="password" name="password_actual" placeholder="Contraseña actual" id="contraseña">
                    <span class="focus-input100" data-symbol="&#xf183;"></span>
                </div>

                <div class="wrap-input100 validate-input m-b-23" data-validate="Contraseña nueva es requerida">
                    <span class="label-input100">Contraseña nueva</span>
                    <input class="input100" type="password" name="password" placeholder="Contraseña nueva" id="nueva">
                    <span class="focus-input100" data-symbol="&#xf183;"></span>
                </div>

                <div class="wrap-input100 validate-input m-b-23" data-validate="Confirmar es requerido">
                    <span class="label-input100">Confirmar contraseña</span>
                    <input class="input100" type="password" name="password_confirmacion" placeholder="Confirmar contraseña" id="confirmar">
                    <span class="focus-input100" data-symbol="&#xf183;"></span>
                </div>

                <div class="container-login100-form-btn">
                    <div class="wrap-login100-form-btn">
                        <div class="login100-form-bgbtn"></div>
                        {{-- <button type="submit" class="login100-form-btn" onclick="return validacion()">
                            Cambiar contraseña
                        </button> --}}
                        <button type="submit" class="login100-form-btn">
                            Cambiar contraseña
                        </button>
                    </div>
                </div>

                <div class="txt1 text-center p-t-54 p-b-20">
                    <h4> Universidad Iberoamericana</h4>
                    <p>©2023 Todos los derechos reservados.</p>
                </div>
            </form>
        </div>
    </div>
</div>

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
 <!-- incluimos el footer -->
 @include('layout.footer')
