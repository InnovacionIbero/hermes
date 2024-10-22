import { alertaCupoPreferentesLleno } from "../../utils/alertas.js";
import { updateDocente } from "../../utils/update-docente.js";

/**
 * Actualizar las materias preferentes del docente.
 * @param {HTMLElement} element
 * @param {object} filtros
 * @param {url} urlUpdatePreferenciasDocente
 */
export const actualizarPreferenciasDocente = async (
    element,
    filtros,
    urlUpdatePreferenciasDocente
) => {
    let dataUpdate = false;
    let row = element.closest("tr");
    let checkboxValue = element.data("codigo");
    let isChecked = element.prop("checked");

    dataUpdate = await updateDocente(
        row,
        checkboxValue,
        filtros,
        urlUpdatePreferenciasDocente,
        isChecked
    );

    if (dataUpdate == "Cupo lleno") {
        element.prop("checked", false);

        alertaCupoPreferentesLleno();
    }
};
