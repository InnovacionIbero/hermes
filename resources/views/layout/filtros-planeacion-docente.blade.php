<div class="row justify-content-center mt-4 row-filtros">

    @if($idRol == 9)
    <div class="col-4">
        <div class="card" id="card-facultades">
            <div class="card-header text-center fondocards" id="header-facultades" style="width:100%; cursor:pointer;" data-toggle="collapse" data-target="#acordion-facultades" aria-expanded="false" aria-controls="acordionFacultades">
                <h5 class="mb-0 d-flex justify-content-between align-items-center">
                    <button class="btn btn-link text-light">
                        Facultades
                    </button>
                    <div class="custom-checkbox">
                        <label for="todos-facultad" class="text-light" style="font-size:12px;"> Selec. Todos</label>
                        <input type="checkbox" class="todos"
                            data_id="facultades"
                            id="todosFacultad" name="todos-facultad" checked>
                    </div>
                </h5>
            </div>
            <div class="card-body text-start collapse shadow" id="acordion-facultades" aria-labelledby="header-facultades">
                <div name="facultades" id="facultades"></div>
            </div>
        </div>
    </div>
    @endif
    <div class="col-4">
    @if($isTransversal !== 1)
        <div class="card " id="card-programas">
            <div class="card-header text-center fondocards" id="HeadingProgramas" style="width:100%; cursor:pointer;" data-toggle="collapse" data-target="#acordion-programas" aria-expanded="false" aria-controls="acordionProgramas">
                <h5 class="mb-0 d-flex justify-content-between align-items-center">
                    <button class="btn btn-link text-light">
                        Programas
                    </button>
                    <div class="custom-checkbox">
                        <label for="todos-programa" class="text-light" style="font-size:12px;"> Selec. Todos</label>
                        <input type="checkbox" id="todos-programa"
                            data_id="programas"
                            name="todosPrograma" checked>
                    </div>
                </h5>
            </div>
            <div class="card-body text-start collapse shadow" id="acordion-programas" aria-labelledby="headingProgramas" style="overflow: auto;">
                <div name="programas">
                    <input type="text" class="form-control mb-2" id="buscadorProgramas" placeholder="Buscar programas">
                    <ul style="list-style:none" id="programas">
                    </ul>
                </div>
            </div>
        </div>
        @else
        <div class="card " id="card-cursos">
            <div class="card-header text-center fondocards" id="HeadingProgramas" style="width:100%; cursor:pointer;" data-toggle="collapse" data-target="#acordion-cursos" aria-expanded="false" aria-controls="acordionProgramas">
                <h5 class="mb-0 d-flex justify-content-between align-items-center">
                    <button class="btn btn-link text-light">
                        Cursos
                    </button>
                    <div class="custom-checkbox">
                        <label for="todos-cursos" class="text-light" style="font-size:12px;"> Selec. Todos</label>
                        <input type="checkbox" id="todos-cursos"
                            data_id="cursos"
                            name="todosCursos" checked>
                    </div>
                </h5>
            </div>
            <div class="card-body text-start collapse shadow" id="acordion-cursos" aria-labelledby="headingCursos" style="overflow: auto;">
                <div name="cursos">
                    <input type="text" class="form-control mb-2" id="buscadorCursos" placeholder="Buscar curso">
                    <ul style="list-style:none" id="cursos">
                    </ul>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
<div class="row text-center justify-content-center mt-4 mb-4">
    <button class="btn button-informe" type="button" id="generarReporte">
        Generar Reporte
    </button>
</div>
<script>
    const urlDocentesDisponibles = @json(route('tabla.docentes.disponibles'));
    const urlFiltrosProgramasFacultad = @json(route('filtros.programas.facultad'));
    const urlActualizarFiltros = @json(route('filtros.change.facultad'));
    const urlUpdateDocente = @json(route('update.asignaturas.docente'));
    const urlUpdatePreferenciasDocente = @json(route('update.preferencias.docente'));
    const urlUpdateCupoDisponibleDocente = @json(route('update.cupo.disponible.docente'));
    const urlInhabilitarDocente = @json(route('inhabilitar.docente'));
    const urlRemoverAsignaturaDocente = @json(route('remover.asignatura.docente'));
    const urlTraerMallaPrograma = @json(route('traer.malla.programa'));
    const urlCrearDocente = @json(route('crear.docente'));

    const urlDocentesTransversalesDisponibles = @json(route('tabla.docentes.transversales.disponibles'));
    const urlTraerMallaTransversal = @json(route('traer.malla.transversal'));

    const urlPlaneacionDocentes = @json(route('tabla.planeacion.docentes'));
    const urlAsignaturasPendientes = @json(route('tabla.asignaturas.pendientes'));
    const urlDocentesAsignados = @json(route('tabla.docentes.asisgnados'));
</script>