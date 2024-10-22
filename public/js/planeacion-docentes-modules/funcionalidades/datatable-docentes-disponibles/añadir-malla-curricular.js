import { enviarAjax } from "../../utils/enviar-ajax.js";

/**
 * Añadir malla curricular de un prorgama al select de la data table
 * @param {HTMLElement} element 
 * @param {url} urlTraerMallaPrograma 
 */
export const añadirMallaCurricular = async (element, urlTraerMallaPrograma)=>{

    let programa = element.val();
    
    let mallaCurricular = await enviarAjax({programa},urlTraerMallaPrograma);

    if (mallaCurricular) {
        $.each(mallaCurricular[programa], function (index, value) {
            $('.select-data-table').append(
                `<option value="${value.codprograma} - ${value.codigoCurso}"> ${value.codprograma} - ${value.curso}</option>`
            );
        });
    }

    /**Retornar al valor original. */
    element.find('option[value="' + programa + '"]').remove();
    element.val("");
    element.find("option[value='']").prop("selected", true);

}