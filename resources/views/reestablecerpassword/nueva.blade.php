@extends('layout.plantillaFormularios')
@section('title', 'Nueva Contrasena')
@section('content')

<style>
	#recuperar {
    background-color: #dfc14e;
    border-color: #dfc14e;
    width: 250px;
    padding: 10px 30px;
    border-radius: 10px;
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

			<h3 class="text-center text-white mb-5" style="font-weight: 400;"> Cambiar contraseña </h3>

			<form action="{{Route('cambio.actualizar')}}" method="POST" class="align-content-center">
			    @csrf
                <input type="hidden" name="id" value="{{ $id }}">
				<div class="mb-5 col-10 mx-auto">
					<input type="password" class="form-control custom-input" name="nueva" id="nueva" placeholder="Contraseña nueva" required>
					<span class="input-border"></span>
				</div>
				<div class="form-group mb-5 col-10 mx-auto ">
					<input type="password" class="form-control custom-input" name="confirmar" id="confirmar" placeholder="Confirmar contraseña"  required>
					<span class="input-border"></span>
				</div>

				<div class="form-group text-center">
					<button type="submit" style="font-weight: 600;" class="btn btn-warning text-white" id="recuperar">Cambiar contraseña</button>
				</div>
			</form>
		</div>
	</div>
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


@endsection

