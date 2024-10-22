@extends('layout.plantillaFormularios')
@section('title', 'Nueva Contrasena')
@section('content')

<div class="limiter">
    <div class="container-login100" style="background-image: url({{asset('assets/images/bg-02.jpg')}});">
        <div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
            <!--Formulario para cambio de contraseña-->
            <form action="{{ route('login.cambiopass') }}" method="post" class="login100-form validate-form" id="miForm">
                @csrf
                <span class="login100-form-title p-b-49">
                    Cambio de contraseña
                </span>
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
<script>
$(document).ready(function() {
    Swal.fire({
        title: 'Aviso Usuario',
        text: "Una vez cambies tu constraseña, serás redirigido al formulario de ingreso para que inicies sesión con tu nueva contraseña.",
        icon: 'info',
        color: 'white',
        showCancelButton: false,
        confirmButtonColor: '#3085d6',
        confirmButtonText: 'OK',
    })
})
</script>
@endsection