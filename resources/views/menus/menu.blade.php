<?php $menu= session('menu'); ?>
<?php 
if(is_null($menu)):
    redirect()->route('login.index');
endif;
?>

<style>
    .textoPequeño {
        font-size: 14px;
        text-transform: lowercase;
    }
    #accordionSidebar {
        width: 260px !important;
    }
    .activo {
        background-color: #dfc14e;
    }
    .activo:hover {
        background-color: #dfc14e;
    }
    .rounded-button {
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .floating-button {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 9999;
    }

    #mensajeBoton {
        position: absolute;
        transform: translateX(-65%);
        width: 200px;
        height: 30px;
        align-items: center;
        justify-content: center;
        background-color: #333;
        color: #fff;
        padding: 5px;
        border-radius: 5px;
        display: none;
        font-size: 14px;
    }

    .button-container {
        position: relative;
        display: inline-block;
    }

    .rounded-button:hover #mensajeBoton {
        display: block;
    }

    #search-container {
        display: none;
        position: fixed;
        bottom: 80px;
        right: 20px;
        background-color: white;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        z-index: 9999;
        width: 300px;
        height: 300px;
    }

    #solicitud {
        max-height: 210px;
    }
    
    .activo{
        background-color: #dfc14e;
    }
</style>
<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" id="menuHome" href="{{ route('home.index') }}">
            <div class="sidebar-brand-icon">
                <img src="https://moocs.ibero.edu.co/hermes/front/public/assets/images/LogoBlanco.png" width="40" alt="">
            </div>
            <div class="sidebar-brand-text mx-3"> {{auth()->user()->nombre_rol}} </div>
        </a>
    <!-- Divider -->
    <hr class="sidebar-divider my-0">



        @foreach($menu as $opcion) 

      
            @if(!isset($opcion['submenu']))

                <?php if( $opcion['id'] == 'AlertasTempranas'):?>
                    <li class="nav-item" id="AlertasTempranas">


                        @include('layout.alertas')
                    </li>

          
                    <?php else: ?>

                    <li class="nav-item menu_navegacion" id="{{ $opcion['id'] }}">
                        <a class="nav-link"  href="{{ route(  $opcion['route']) }}">
                            <i class="{{ $opcion['icon'] }}"></i>    
                            <span> {{ $opcion['title'] }}</span>
                        </a>
                    </li>

                <?php endif ?>
            @endif

            @if(isset($opcion['submenu']))   

                <li class="nav-item menu_navegacion">
                    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapse{{ $opcion['id'] }}" aria-expanded="false" aria-controls="collapse{{ $opcion['id'] }}">
                        <i class="{{ $opcion['icon'] }}"></i>
                        <span>{{ $opcion['title'] }}</span>
                    </a>

                    <div id="collapse{{ $opcion['id'] }}" class="collapse" aria-labelledby="headingFive" data-parent="#accordionSidebar">
                        <div class="bg-white py-2 collapse-inner rounded">
                                @foreach($opcion['submenu'] as $subOpcion)
                                
                                    @if ($subOpcion['title'] == 'Ver perfil')
                                        <a class="collapse-item" href="{{ route('user.perfil',['id'=>encrypt(auth()->user()->id)]) }}" id="{{$subOpcion['id']}}">Ver perfil</a>
                                    @elseif($subOpcion['title'] == 'Cambiar contraseña')
                                        <a class="collapse-item" href="{{ route('cambio.cambio',['idbanner'=>encrypt(auth()->user()->id_banner)]) }}" id="{{$subOpcion['id']}}">Cambiar contraseña</a>
                                    @else
                                        <a class="collapse-item" href="{{ route($subOpcion['route']) }}" id="{{$subOpcion['id']}}">{{ $subOpcion['title'] }}</a>
                                    @endif

                                @endforeach
                        </div>
                    </div>
                </li> 
        
            @endif

        @endforeach
</ul>

