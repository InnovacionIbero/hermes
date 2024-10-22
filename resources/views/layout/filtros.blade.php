
        <!-- Checkbox Periodos -->
        <div class=" justify-content-start" id="seccion">
            <!--Columna Niveles de Formación-->
            <div class="col-12 text-start mt-1">
                <div class="card-body" id="cardNivel">
                
                    <div class="row">
                        <div class="text-center col-8">
                            <h5 id="tituloNiveles" class="text-dark"><strong>Periodos Activos</strong></h5>
                        </div>
                        <div class="text-center col-4">
                            <h5 id="tituloNiveles" class="text-dark"><strong>Facultades y Programas</strong></h5>
                        </div>
                    </div>

                <div class="">
                        <div id="periodos" class="row">

                            <div class="col-md-8">


                                <div class="container">
                                    <div class="row">
                                        <div id="formacion_c" class="col">
                                            <!--Formación continua-->
                                            <div class="card" id="cardformacion">
                                                <div class="card-header fondocards" id="heading2" style="width:100%; cursor:pointer;" data-toggle="collapse" data-target="#collapse2" aria-expanded="true" aria-controls="collapse2">
                                                    <h5 class="mb-0 d-flex justify-content-between align-items-center">
                                                        <button class="btn btn-link text-light">
                                                            For. Contínua
                                                        </button>
                                                        <div class="custom-checkbox">
                                                            <label for="todosContinua" class="text-light" style="font-size:12px;"> Selec. Todos</label>
                                                            <input type="checkbox" class="todos inputTodos" 
                                                            data_id="Continua"
                                                            id="todosContinua" name="todosContinua" checked>
                                                        </div>
                                                    </h5>
                                                </div>
                                                <div id="collapse2" class="collapse shadow" aria-labelledby="heading2" data-parent="#periodos">
                                                    <div class="card-body periodos" style="width:100%;" id="Continua"></div>
                                                </div>
                                            </div>
                                        </div>
    
                                        <div id="Pregrado_c" class="col">
                                            <!--Pregrado-->
                                            <div class="card" id="cardPregrado">
                                                <div class="card-header fondocards" id="heading1" style="width:100%;cursor:pointer;" data-toggle="collapse" data-target="#collapse1" aria-expanded="true" aria-controls="collapse1">
                                                    <h5 class="mb-0 d-flex justify-content-between align-items-center">
                                                        <button class="btn btn-link text-light">
                                                            Pregrado
                                                        </button>
                                                        <div class="custom-checkbox">
                                                            <label for="todosPregrado" class="text-light" style="font-size:12px;"> Selec. Todos</label>
                                                            <input type="checkbox" class="todos" id="todosPregrado" 
                                                            data_id="Pregrado"
                                                            name="todosPregrado" checked>
                                                        </div>
                                                    </h5>
                                                </div>
    
                                                <div id="collapse1" class="collapse shadow" aria-labelledby="heading1" data-parent="#periodos">
                                                    <div class="card-body periodos" style="width:100%;" id="Pregrado"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                    <div class="container">
                                            <div class="row"> 
                                        <div id="Especializacion_c" class="col">
                                            <!--Especialización-->
                                            <div class="card" id="cardEspecializacion">
                                                <div class="card-header fondocards" id="heading3" style="width:100%; cursor:pointer;" data-toggle="collapse" data-target="#collapse3" aria-expanded="true" aria-controls="collapse3">
                                                    <h5 class="mb-0 d-flex justify-content-between align-items-center">
                                                        <button class="btn btn-link text-light">
                                                            Especialización
                                                        </button>
                                                        <div class="custom-checkbox">
                                                            <label for="todosEsp" class="text-light" style="font-size:12px;"> Selec. Todos</label>
                                                            <input type="checkbox" class="todos" 
                                                            data_id="Esp"
                                                            id="todosEsp" name="todosEsp" checked>
                                                        </div>
                                                    </h5>
                                                </div>
    
                                                <div id="collapse3" class="collapse shadow" aria-labelledby="heading3" data-parent="#periodos">
                                                    <div class="card-body periodos" style="width:100%;" id="Esp"></div>
                                                </div>
                                            </div>
                                        </div>
    
                                        <div id="Maestrias_c"class="col">
                                            <!--Maestría-->
                                            <div class="card" id="cardMaestrias">
                                                <div class="card-header fondocards" id="heading4" style="width:100%; cursor:pointer;" data-toggle="collapse" data-target="#collapse4" aria-expanded="true" aria-controls="collapse4">
                                                    <h5 class="mb-0 d-flex justify-content-between align-items-center">
                                                        <button class="btn btn-link text-light">
                                                            Maestría
                                                        </button>
                                                        <div class="custom-checkbox">
                                                            <label for="todosMaestria" class="text-light" style="font-size:12px;"> Selec. Todos</label>
                                                            <input type="checkbox" class="todos" data_id="Maestria" id="todosMaestria" name="todosMaestria" checked></div>
                                                    </h5>
                                                </div>
    
                                                <div id="collapse4" class="collapse shadow" aria-labelledby="heading4" data-parent="#periodos">
                                                    <div class="card-body periodos" style="width:100%;" id="Maestria"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                        
                            </div>

                            <div class="col-md-4">

                                <div class="col col">
                                    <div class="card" id="cardFacultades">
                                        <div class="card-header text-center fondocards" id="HeadingFacultades" style="width:100%; cursor:pointer;" data-toggle="collapse" data-target="#acordionFacultades" aria-expanded="false" aria-controls="acordionFacultades">
                                            <h5 class="mb-0 d-flex justify-content-between align-items-center">
                                                <button class="btn btn-link text-light">
                                                    Facultades
                                                </button>
                                                <div class="custom-checkbox">
                                                    <label for="todosFacultad" class="text-light" style="font-size:12px;"> Selec. Todos</label>
                                                    <input type="checkbox" class="todos" 
                                                    data_id="facultades"
                                                    id="todosFacultad" name="todosFacultad" checked>
                                                </div>
                                            </h5>
                                        </div>
                                        <div class="card-body text-start collapse shadow" id="acordionFacultades" aria-labelledby="HeadingFacultades">
                                            <div name="facultades" id="facultades"></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col col">
                                    <div class="card " id="cardProgramas">
                                        <div class="card-header text-center fondocards" id="HeadingProgramas" style="width:100%; cursor:pointer;" data-toggle="collapse" data-target="#acordionProgramas" aria-expanded="false" aria-controls="acordionProgramas">
                                            <h5 class="mb-0 d-flex justify-content-between align-items-center">
                                                <button class="btn btn-link text-light" id="titulosDivPrograma">
                                                    Programas
                                                </button>
                                                <div class="custom-checkbox">
                                                    <label for="todosPrograma" class="text-light" style="font-size:12px;"> Selec. Todos</label>
                                                    <input type="checkbox" id="todosPrograma" 
                                                    data_id="programas"
                                                    name="todosPrograma" checked>
                                                </div>
                                            </h5>
                                        </div>
                                        <div class="card-body text-start collapse shadow" id="acordionProgramas" aria-labelledby="headingProgramas" style="overflow: auto;">
                                            <div name="programas">
                                                <input type="text" class="form-control mb-2" id="buscadorProgramas" placeholder="Buscar programas">
                                                <ul style="list-style:none" id="programas">
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row text-center justify-content-center mt-4">
            <button class="btn button-informe" type="button" id="generarReporte">
                Generar Reporte
            </button>
            <button class="btn button-informe" type="button" id="slideplaneacion" data-toggle='modal' data-target='#modalSlidePlaneacion' hidden>
                Actividades P5 2024
            </button>
        </div>
 
    <script>
            url_periodo = @json(route('periodos.activos'));
            get_program = @json(route('traer.programas.filtro'));
            get_cursos = @json(route('traer.cursos.filtro'));
    </script>

<script src="{{ asset('js/filtros.js') }}"></script>