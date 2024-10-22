import { alertaCupoVacio } from "../../utils/alertas.js";
import { updateDocente } from "../../utils/update-docente.js";

/**
 *  Función para remover una asignatura de la columna 'Asignaturas' que corresponden a las asignadas al docente.
 * Update a la base de datos removiendo la asignatura correspondiente.
 * @param {HTMLElement} element
 * @param {url} urlRemoverAsignaturaDocente
 * @param {object} filtros
 */
export const removerAsignaturaDocente = async (
    element,
    urlRemoverAsignaturaDocente,
    filtros
) => {
    let remover = "";
    let row = element.closest("tr");
    let inputAnterior = element.prev("input");
    let checkboxValue = inputAnterior.data("codigo");
    let codProgramaMateria = inputAnterior[0].nextSibling.nodeValue
        .trim()
        .replace(/\.$/, "");

    try {
        remover = await updateDocente(
            row,
            checkboxValue,
            filtros,
            urlRemoverAsignaturaDocente
        );

        if (remover !== "success") {
            alertaCupoVacio();
        } else {
            /** Remover de las asignaturas */
            $(inputAnterior[0].nextSibling).remove();
            $(inputAnterior[0].nextSibling.nextSibling).remove();
            inputAnterior.remove();
            element.remove();

            /**Añadir a las opciones de la malla curricular */
            row.find("td")
                .find("select")
                .append(
                    `<option value="${checkboxValue}">${codProgramaMateria}</option>`
                );
        }
    } catch (error) {
        console.log("Error al remover asignatura.", error);
    }
};
