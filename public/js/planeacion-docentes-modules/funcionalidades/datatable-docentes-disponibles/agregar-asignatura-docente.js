import { alertaCupoAsginaturasLleno } from "../../utils/alertas.js";
import { updateDocente } from "../../utils/update-docente.js";
/**
 * Función para agregar una asignatura del select de la malla curricular a la columna de 'Asignaturas'.
 * También se hace update a la base de datos añadiendo la asignatura a las que tiene el docente.
 * @param {HTMLElement} element
 * @param {url} urlUpdateDocente 
 * @param {object} filtros 
 */
export const agregarAsignaturaDocente = async (
    element,
    urlUpdateDocente,
    filtros,
    transversal = false
) => {
    let data = "";
    let row = element.closest("tr");
    let selectedValue = element.val();

    data = await updateDocente(row, selectedValue, filtros, urlUpdateDocente);

    if (data) {
        console.log(data);
        if (data !== "Cupo lleno") {
            row.find("td")
                .eq(1)
                .append(
                    `<input type="checkbox" class="checkbox-codigos-materia" data-codigo="${data.codPrograma} - ${data.codMateria}"> ` +
                        `${transversal ? data.codMateria : data.codPrograma} - ${data.curso}. <i class="button-remover-asignatura fa-solid fa-xmark"></i><br>`
                );

            element.find('option[value="' + selectedValue + '"]').remove();
        } else {
            alertaCupoAsginaturasLleno();
        }

        /** Reiniciar al valor por defecto */
        element.val("");
        element.find("option[value='']").prop("selected", true);
    }
};
