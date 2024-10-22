@extends('layout.plantillaFormularios')
@section('title', 'Registro')
@section('content')


<div class="limiter">
    <div class="container-login100" style="background-image: url({{asset('assets/images/bg-02.jpg')}});">
        <div class="wrap-login100 p-l-55 p-r-55 p-t-65 p-b-54">
            <form action="{{ route('registro.saveregistro') }}" method="post" class="login100-form validate-form" id="miform">
                @csrf
                <span class="login100-form-title p-b-49">
                    Formulario de registro
                </span>

                <div class="wrap-input100 validate-input m-b-23" data-validate="idBanner es requerido">
                    <span class="label-input100">ID Banner</span>
                    <input class="input100" type="number" name="id_banner" placeholder="ID Banner" id="idbanner">
                    <span class="focus-input100" data-symbol="&#xf205;"></span>
                </div>


                <div class="wrap-input100 validate-input m-b-23" data-validate="documento es requerido">
                    <span class="label-input100">Documento de identidad</span>
                    <input class="input100" type="text" name="documento" placeholder="Documento de identidad" id="documento">
                    <span class="focus-input100" data-symbol="&#xf20b;"></span>
                </div>

                <div class="wrap-input100 validate-input m-b-23" data-validate="nombre es requerido">
                    <span class="label-input100">Nombre completo</span>
                    <input class="input100" type="text" name="nombre" placeholder="Nombre completo" id="nombre">
                    <span class="focus-input100" data-symbol="&#xf20e;"></span>
                </div>

                <div class="wrap-input100 validate-input m-b-23" data-validate="correo es requerido">
                    <span class="label-input100">Correo electronico</span>
                    <input class="input100" type="email" name="email" placeholder="Correo electronco" id="correo">
                    <span class="focus-input100" data-symbol="&#xf15a;"></span>
                </div>
                <div class="wrap-input100 validate-input m-b-23" data-validate="rol es requerido">
                    <span class="label-input100">Rol</span>
                    <select class="input100" name="id_rol" id="rol">
                        <option value="">Seleccione el rol</option>
                    </select>
                    <span class="focus-input100" data-symbol="&#xf2f1;"></span>
                </div>

                <div class="wrap-input100 m-b-23">
                    <span class="label-input100">Facultad</span>
                    <select class="input100" name="id_facultad" id="facultades">
                        <option value="">Seleccione la facultad</option>
                    </select>
                    <span class="focus-input100" data-symbol="&#xf2f1;"></span>
                </div>

                <div class="wrap-input100 m-b-23">
                <span class="label-input100">Programas</span>
                <br>
                    <div id="programas"></div>
                </div>
                <br>

                    <div class="container-login100-form-btn">
						<div class="wrap-login100-form-btn">
							<div class="login100-form-bgbtn"></div>
							<button type="submit" class="login100-form-btn">
								Registrar
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
    roles();
    facultades();

    //* Funcion para trear los datos de la tabla roles y cargar los opciones del select/
    function roles() {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('registro.roles') }}",
            method: 'get',
            success: function(data) {
                data.forEach(rol => {
                    $('#rol').append(`<option  value="${rol.id}">${rol.nombreRol}</option>`);
                });
            }
        })
    }

    //* Funcion para trear los datos de la tabla facultades y cargar los opciones del select/
    function facultades() {
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('registro.facultades') }}",
            method: 'post',
            success: function(data) {
                data.forEach(facultad => {
                    $('#facultades').append(`<option value="${facultad.id}">${facultad.nombre}</option>`);
                });
            }
        });
        
    }




    //* Comprueba si el select de facultades cambia de valor/
    $('#facultades').change(function() {
        facultades = $(this);
        //* comprueba que el valor de facultados sea diferente a vacio/
        if ($(this).val() != '') {
            //* se crea un objeto FormData para crear un conjunto depares clave/valor para el envio de los datos/
            var formData = new FormData();
            //* Se añade el par clave/valor con el valor del select/
            formData.append('idfacultad', facultades.val());
            //* Se envia el id de facultad pormedio de ajax para recibir los programas relacionados al id enviado/
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: "{{ route('registro.programas') }}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    facultades.prop('disabled', true);
                },
                success: function(data) {
                    facultades.prop('disabled', false)
                    $('#programas').empty();
                    data.forEach(programa => {
                        //* Se crea un input tipo checkbox para cada programa recibido/
                        $('#programas').append(`<label><input type="checkbox" id="" name="programa[]" value="${programa.id}"> ${programa.programa}</label><br>`);
                    });
                }
            });
        } else {
            $('#programas').empty();
            facultades.prop('disabled', false)
        }
    })

</script>
@endsection
