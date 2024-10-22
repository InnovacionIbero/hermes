<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="https://aulavirtual.ibero.edu.co/pluginfile.php?file=%2F1%2Ftheme_adaptable%2Ffavicon%2F1693948501%2FImagen-5.ico">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    {{-- <script type="text/javascript" src="{{asset('js/app.js')}}"></script> --}}
    <title>Hermes - Ibero | Sistema para la gestión de programas virtuales | Login</title>
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">

    <link rel="stylesheet" href="{{asset('css/app.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/vendor/bootstrap/css/bootstrap.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/fonts/font-awesome-4.7.0/css/font-awesome.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/fonts/iconic/css/material-design-iconic-font.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/vendor/animate/animate.css')}}">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="{{asset('assets/vendor/css-hamburgers/hamburgers.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/vendor/animsition/css/animsition.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/vendor/select2/select2.min.css')}}">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="{{asset('assets/vendor/daterangepicker/daterangepicker.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/util.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('assets/css/main.css')}}">
<!--===============================================================================================-->
    <!--Styles plantilla nueva-->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" type="text/css" href="{{asset('assets/css/formlogin.css')}}">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('assets/vendor/jquery/jquery-3.2.1.min.js')}}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('assets/vendor/animsition/js/animsition.min.js')}}"></script>
    <!--=============assets/==================================================================================-->
    <script src="{{ asset('assets/vendor/bootstrap/js/popper.js')}}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.min.js')}}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('assets/vendor/select2/select2.min.js')}}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('assets/vendor/daterangepicker/moment.min.js')}}"></script>
    <script src="{{ asset('assets/vendor/vendor/daterangepicker/daterangepicker.js')}}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('assets/vendor/countdowntime/countdowntime.js')}}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('assets/js/main.js')}}"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
</head>

<body>
@yield('content')
	
</body>

</html>
