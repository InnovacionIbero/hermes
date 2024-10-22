/**
 * 
 * @param {HTMLElement} element 
 */
export const removerAsignaturaModal = (element, transversal = false) => {
    let input = element.prev("input");
    let text = input[0].nextSibling.nodeValue.trim().replace(/\.$/, "");

    let malla = `<option value="${input.val()}" ${transversal ? '' : `data-codprograma="${input.data("cod-programa")}"`}> ${text}. </option>`;

    
    $("#modal-nuevo-docente select#asignaturas-docente").append(malla);

    element.parent().remove();
    element.remove();
};
