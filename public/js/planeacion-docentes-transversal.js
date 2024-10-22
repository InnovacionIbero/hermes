import { actualizarCupoDisponibleDocente } from "./planeacion-docentes-modules/funcionalidades/datatable-docentes-disponibles/actualizar-cupo-disponible-docente.js";
import { actualizarPreferenciasDocente } from "./planeacion-docentes-modules/funcionalidades/datatable-docentes-disponibles/actualizar-preferencias-docente.js";
import { agregarAsignaturaDocente } from "./planeacion-docentes-modules/funcionalidades/datatable-docentes-disponibles/agregar-asignatura-docente.js";
import {
    alertaNoData,
    alertaPreload,
} from "./planeacion-docentes-modules/utils/alertas.js";
import { añadirAsignaturaModalDocente } from "./planeacion-docentes-modules/funcionalidades/modal-crear-docente-estandar/añadir-asignatura-docente.js";
import {
    cargarMallaTransversal,
    crearDocente,
} from "./planeacion-docentes-modules/utils/creacion-docente.js";
import {
    destruirTabla,
    headerDataTableDocentesTrasnversalesDisponibles,
} from "./planeacion-docentes-modules/data-tables/data-table-docentes-disponibles.js";
import { enviarAjax } from "./planeacion-docentes-modules/utils/enviar-ajax.js";
import {
    buscarDato,
    EstadoFiltros,
    seleccionarMenuNav,
    traerDataFiltros,
} from "./planeacion-docentes-modules/filtros/filtros.js";
import { inhabilitarDocente } from "./planeacion-docentes-modules/funcionalidades/datatable-docentes-disponibles/inhabilitar-docente.js";
import { removerAsignaturaDocente } from "./planeacion-docentes-modules/funcionalidades/datatable-docentes-disponibles/remover-asignatura-docente.js";
import { removerAsignaturaModal } from "./planeacion-docentes-modules/funcionalidades/modal-crear-docente-estandar/remover-asignatura-modal.js";
import { headersDataTableAsignaturasPendientes } from "./planeacion-docentes-modules/data-tables/data-table-asignaturas-pendientes.js";
import { headersDataTablePlaneacionDocentes } from "./planeacion-docentes-modules/data-tables/data-table-planeacion-docentes.js";
import { headerDataTableDocentesAsignados } from "./planeacion-docentes-modules/data-tables/data-table-docentes-asignados.js";

const buscador = $("#buscadorCursos");
const buttonGenerarReporte = $("#generarReporte");
const estadoFiltros = new EstadoFiltros();
const tablaAsignaturasPendientes = $("#datatable-asignaturas-pendientes");
const tablaDocentesDisponibles = $("#datatable-docentes-disponibles");
const tablaPlaneacionDocentes = $("#datatable-planeacion-docentes");

/** Poner estado del menú en activo */
$(document).find("#planeacion-docentes").addClass("activo");

/**Llamado a la data de los filtros según lo que tenga asignado el usuario */
await traerDataFiltros(urlFiltrosProgramasFacultad);

$(".content").hide();
$("#docentes-disponibles").show();

$(".menuMoodle")
    .off("click")
    .click((event) => {
        seleccionarMenuNav(event);
    });

$(buttonGenerarReporte).on("click", async function () {
    destruirTabla(tablaDocentesDisponibles);
    destruirTabla(tablaPlaneacionDocentes);
    destruirTabla(tablaAsignaturasPendientes);
    await cargarDataConPreload();
});

/**----- Buscador cursos -----*/
buscador.on("input", (event) => {
    buscarDato(event, true);
});

const renderizarDataTable = async (htmlElement, headers) => {
    $(htmlElement).empty();
    if (headers.data.length == 0) {
        htmlElement.append(
            `<h5>No hay datos disponibles para el filtro que seleccionaste</h5>`
        );
        return;
    }

    $(htmlElement).DataTable(headers);
};

const dataTableDocentes = async (disponibilidad = 'activos') => {
    await estadoFiltros.actualizarEstado();
    let data = estadoFiltros.obtenerFiltros();
    data.disponibilidad = disponibilidad;
    let response = await enviarAjax(data, urlDocentesTransversalesDisponibles);

    if (response.length > 0) {
        Swal.close();
        let headers = await headerDataTableDocentesTrasnversalesDisponibles(
            response
        );
        await renderizarDataTable(tablaDocentesDisponibles, headers);
    } else {
        await alertaNoData();
    }
};

const dataTablePlaneacionDocentes = async () => {
    await estadoFiltros.actualizarEstado();
    let data = estadoFiltros.obtenerFiltros();
    data.tabla = "asignadas";
    let response = await enviarAjax(data, urlPlaneacionDocentes);
    let headers = await headersDataTablePlaneacionDocentes(response);

    await renderizarDataTable(tablaPlaneacionDocentes, headers);
};

const dataTableAsignaturasPendientes = async () => {
    await estadoFiltros.actualizarEstado();
    let data = estadoFiltros.obtenerFiltros();
    data.tabla = "pendientes";
    let response = await enviarAjax(data, urlPlaneacionDocentes);
    let headers = await headersDataTableAsignaturasPendientes(response);

    await renderizarDataTable(tablaAsignaturasPendientes, headers);
};

const dataTableDocentesAsignados = async () =>{
    await estadoFiltros.actualizarEstado();
    let data = estadoFiltros.obtenerFiltros();
    let response = await enviarAjax(data, urlDocentesAsignados);
    /** Data table docentes asignados */
    let headers = await headerDataTableDocentesAsignados(response);

    console.log(headers);

    await renderizarDataTable(tablaPlaneacionDocentes, headers);
};

/** ----- Funcionalidades DataTable -----*/

/** Agregar asignaturas del select a las asignaturas del docente */
$(document).on("change", ".select-data-table", async function () {
    await agregarAsignaturaDocente(
        $(this),
        urlUpdateDocente,
        estadoFiltros.obtenerFiltros(),
        true
    );
});

/**Remover asignaturas de la columa "Asignaturas" del docente */
$(document).on("click", ".button-remover-asignatura", async function () {
    await removerAsignaturaDocente(
        $(this),
        urlRemoverAsignaturaDocente,
        estadoFiltros.obtenerFiltros()
    );
});

/** Actualizar preferencias docente */
$(document).on("change", ".checkbox-codigos-materia", async function () {
    await actualizarPreferenciasDocente(
        $(this),
        estadoFiltros.obtenerFiltros(),
        urlUpdatePreferenciasDocente
    );
});

/** Actualizar cupo disponible del docente */
$(document).on("change", ".cupo-disponible-docente", async function () {
    await actualizarCupoDisponibleDocente(
        $(this),
        urlUpdateCupoDisponibleDocente
    );
});

/** Inhabilitar o habilitar docente */
$(document).on("click", ".button-inactivar", async function () {
    inhabilitarDocente($(this), urlInhabilitarDocente);
});

/*----- Funcionalidades Modal crear docente -----*/

/** Cargar malla transversal */
$(document).on("click", "#button-crear-docente", async function () {
    await cargarMallaTransversal(urlTraerMallaTransversal);
});

/** Añadir asignatura */
$(document).on("change", "#asignaturas-docente", async function () {
    await añadirAsignaturaModalDocente($(this), true);
});

$(document).on(
    "click",
    ".button-remover-asignatura-modal-crear",
    async function () {
        await removerAsignaturaModal($(this), true);
    }
);

/** Buttons data table docentes */
$(document).on("click", ".filtrar-docentes", async function () {
    alertaPreload();
    
    try {
        let tabla = $(this).data("tabla");
        destruirTabla(tablaDocentesDisponibles);
        await dataTableDocentes(tabla);
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Ocurrió un problema al cargar los datos.",
        });
        return;
    }
});

/** Buttons data table asignación */
$(document).on("click", ".filtrar-asignacion-docente", async function () {
    alertaPreload();

    try {
        destruirTabla(tablaPlaneacionDocentes);
        await dataTableDocentesAsignados();

    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Ocurrió un problema al cargar los datos.",
        });
        return;
    }

    // Cerrar la alerta de carga cuando todo haya terminado
    Swal.close();

});

$(document).on("click", ".filtrar-asignacion-asignatura", async function () {
    alertaPreload();

    try {
        destruirTabla(tablaPlaneacionDocentes);
        await dataTablePlaneacionDocentes();

    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Ocurrió un problema al cargar los datos.",
        });
        return;
    }

    // Cerrar la alerta de carga cuando todo haya terminado
    Swal.close();

});

/**Envío formulario */
$(document).on("submit", "#miForm", async function (event) {
    event.preventDefault();
    /** Data Form */
    let formData = $(this).serializeArray();

    await crearDocente(formData, urlCrearDocente);
});

async function cargarDataConPreload() {
    alertaPreload();

    try {
        await dataTableDocentes();
        await dataTablePlaneacionDocentes();
        await dataTableAsignaturasPendientes();
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Ocurrió un problema al cargar los datos.",
        });
        return;
    }

    Swal.close();
}

await cargarDataConPreload();

$("#todos-cursos").on("click", function (event) {
    event.stopPropagation();
});

$("#todos-cursos").change(function () {
    $("#cursos input[type='checkbox']").prop("checked", $(this).is(":checked"));
});
