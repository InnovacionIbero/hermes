import { removerMalla } from "../../utils/creacion-docente.js";

/**
 * 
 * @param {HTMLElement} element 
 */
export const removerProgramaModalCrearDocente = (element) => {
    
    let malla = `<option value="${element.parent().data("codprograma")}"> ${$(
        this
    )
        .parent()
        .text()
        .trim()}</option>`;

    $("#modal-nuevo-docente select#programas-docente").append(malla);

    removerMalla(element.parent().data("codprograma"));

    element.parent().remove();
    element.remove();
};
