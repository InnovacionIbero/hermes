<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    {{-- <script type="text/javascript" src="{{asset('js/app.js')}}"></script> --}}
    <title>@yield('title')</title>

</head>

<body>
@yield('content')

</body>

</html>
