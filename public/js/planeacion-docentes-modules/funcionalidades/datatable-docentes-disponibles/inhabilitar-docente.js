import { alertaDocenteActualizado, alertaInactivarDocente } from "../../utils/alertas.js";
import { updateDocente } from "../../utils/update-docente.js";

/**
 * Inhabilitar docente
 * @param {HTMLElement} element
 * @param {url} urlInhabilitarDocente
 */
export const inhabilitarDocente = async (
    element,
    urlInhabilitarDocente
) => {

    console.log(element);

    let newData = '';
    let ejecutar;

    if(element.hasClass('btn-success'))
    {
        ejecutar = await alertaInactivarDocente(false);
        newData = 'Inactivar';
    }
    else{
        ejecutar = await alertaInactivarDocente(true);
        newData = 'Activar';
    }

    let row = element.closest("tr");

    if (ejecutar) {
        try {
            await updateDocente(row, newData, {}, urlInhabilitarDocente);
            await alertaDocenteActualizado();
            location.reload();
        } catch (error) {
            console.log("Error al inhabilitar docente.");
        }
    }
};
