import { alertaCupoDocenteActualizado, alertaCupoDocenteInsuficiente } from "../../utils/alertas.js";
import { updateDocente } from "../../utils/update-docente.js";

/**
 * Actualizar el cupo disponible TOTAL del docente.
 * @param {HTMLElement} element
 * @param {url} urlUpdateCupoDisponibleDocente
 */
export const actualizarCupoDisponibleDocente = async (element, urlUpdateCupoDisponibleDocente) =>
{
    let row = element.closest("tr");
    let inputValue = element.val();

    element.prop("disabled", true);
    if (inputValue > 0) {
        try {
            await updateDocente(
                row,
                inputValue,
                {},
                urlUpdateCupoDisponibleDocente
            );
            alertaCupoDocenteActualizado(inputValue);
        } catch (error) {
            console.log("Error al actualizar", error);
        } finally {
            element.prop("disabled", false);
        }
    } else {
        alertaCupoDocenteInsuficiente();
        element.prop("disabled", false);
    }
}