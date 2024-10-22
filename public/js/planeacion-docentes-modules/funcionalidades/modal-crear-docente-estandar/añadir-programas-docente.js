import { cargarMalla } from "../../utils/creacion-docente.js";

/**
 *  Función para añadir un programa al docente y cargar su malla curricular respectiva.
 * @param {HTMLElement} element
 */
export const añadirProgramaDocente = async (element) => {
    let valorSeleccionado = element.val();
    let textoSeleccionado = $("#programas-docente option:selected").text();

    let nuevoPrograma = `<li data-codprograma='${valorSeleccionado}'>${textoSeleccionado}. <i class="button-remover-programa-modal-crear fa-solid fa-xmark"></li>`;

    $("#programas-seleccionados-docente").append(nuevoPrograma);

    await cargarMalla(urlTraerMallaPrograma, valorSeleccionado);

    /**Retornar al valor original. */
    element.find('option[value="' + valorSeleccionado + '"]').remove();
    element.val("");
    element.find("option[value='']").prop("selected", true);
};
