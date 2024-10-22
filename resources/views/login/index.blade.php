@extends('layout.plantillaFormularios')

@section('content')

<style>
	#loginMicrosoft {
        background-color: #EAB631;
        border-color: #EAB631;
        color: white;
        width: 100%;
        height: 42.48px;
        border-radius: 10px;
        font-weight: bold;
        place-items: center;
        font-size: 16px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>


<div class="container align-middle shadow-lg rounded" >
	<div class="row" style="background-color:white;border-radius: 35px; box-shadow: 0 0 10px rgba(0, 0, 0, 5);">
		<div class="col text-center" style="background:#FFFFFF;border-top-left-radius: 35px 35px; border-bottom-left-radius: 35px 35px; ">
			
			<div class="align-content-center mt-5 mb-3">
				<img src="https://moocs.ibero.edu.co/hermes/front/public/assets/images/logohermes.png" width="380" alt="">
			</div>
		
			<div class="text-center " style="margin-top: -7%;">
				<p style="color: #4a4a4a; display: inline;"><strong>Integrado al Ecosistema de</strong></p>
				<p style="color: #EAB631; display: inline;"><strong>Innovación Educativa</strong></p>
				<img src="https://moocs.ibero.edu.co/hermes/front/public/assets/images/logo.png" width="30" alt="">
			</div>
			
		

			<div class="align-content-center mt-5 mb-3">
				<img src="https://moocs.ibero.edu.co/hermes/front/public/assets/images/logoHorizontal.png" width="170" alt="">
			</div>

		</div>
		<div class="col" id="colmder" style="border-top-right-radius: 35px 35px; border-bottom-right-radius: 35px 35px;">
			<br>
			<div class="rectangle"></div>
			<br>

			<h3 class="text-center text-white mb-5" style="font-weight: 400;"> Sistema para la gestión de programas virtuales - Ibero</h3>

			<form action="{{ route('login.login') }}" method="POST" class="align-content-center">
			@csrf
				<div class="mb-5 col-10 mx-auto">
					<input type="email" class="form-control custom-input" name="email" placeholder="Usuario" required>
					<span class="input-border"></span>
				</div>
				<div class="form-group mb-5 col-10 mx-auto ">
					<input type="password" class="form-control custom-input" name="password" placeholder="Contraseña" required>
					<span class="input-border"></span>
				</div>
				<div class="row justify-content-center">
					<div class="form-group text-center col-8">
						<button type="submit" style="font-weight: 600;background-color: #EAB631; cursor: pointer; width:100%;" class="btn  text-white" id="btn">Ingresa con tu cuenta Hermes</button>
					</div>
				</div>
				<div>
					<strong><h4 class="text-white text-center">O</h4></strong>
				</div>

				<div class="row justify-content-center">
					<div class="text-center mb-4 mt-3 col-8">
						<span><a href="{{ route('login.microsoft') }}" id="loginMicrosoft">Ingresa con tu correo Ibero </a></span>
					</div>
				</div>
				<div class="row justify-content-center">
					<span><u><a href="{{ route('cambio.index') }}" style="font-weight: 700;color:white;">¿Olvidaste tu Contraseña?</a></u></span>
				</div>
			</div>
				
				
			</form>

		
	</div>

</div>

<div id="dropDownSelect1"></div>


</body>


@if($errors->any())
<script>
    Swal.fire("Error", "{{ $errors->first() }}", "error");
</script>
@endif

@endsection()