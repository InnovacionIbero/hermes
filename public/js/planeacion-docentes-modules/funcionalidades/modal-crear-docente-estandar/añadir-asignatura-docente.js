
/**
 * 
 * @param {HTMLElement} element 
 */
export const añadirAsignaturaModalDocente = (element, transversal = false) => {
    let valorSeleccionado = element.val();
    let textoSeleccionado = $("#asignaturas-docente option:selected").text();

    // Usar un operador ternario para incluir o excluir data-codprograma
    let nuevaAsignatura = `<li><input type="checkbox" value="${valorSeleccionado}" ${transversal ? '' : `data-codprograma="${element.find("option:selected").data("codprograma")}"`}> ${textoSeleccionado}. 
    </input><i class="button-remover-asignatura-modal-crear fa-solid fa-xmark"></i></li>`;

    $("#asignaturas-seleccionadas-docente").append(nuevaAsignatura);

    // Remover la opción seleccionada del elemento original
    element.find('option[value="' + valorSeleccionado + '"]').remove();
    element.val("");
    element.find("option[value='']").prop("selected", true);
};

