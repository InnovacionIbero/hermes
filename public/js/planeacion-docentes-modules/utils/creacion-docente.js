import {
    alertaCupoAsginaturasLleno,
    alertaCupoDocenteInsuficiente,
    alertaCupoPreferentesLleno,
    alertaDocenteCreado,
    alertaErrorCrearDocente,
} from "./alertas.js";
import { enviarAjax } from "./enviar-ajax.js";

/**
 * Cargar los programas que tiene en la base de datos el usuario al modal de crear docente.
 * @param {url} url
 */
export const cargarProgramasModal = async (url) => {
    let data = await enviarAjax("", url);

    const select = $("#modal-nuevo-docente select#programas-docente");
    
    select.empty();
    
    if (select.children().length == 0) {
        $("#modal-nuevo-docente select#programas-docente").append(
            `"<option value='' selected disabled hidden>Añadir programa</option>";`
        );
        if (data.programas) {
            $.each(data.programas, function (index, value) {
                $("#modal-nuevo-docente select#programas-docente").append(
                    `<option value="${value.codprograma}"> ${value.codprograma}-${value.programa}</option>`
                );
            });
        }
    }
};

/**
 * Carga en el select de seleccionar asignaturas la malla curricular del programa seleccionado.
 * @param {url} url
 * @param {String} programa
 */
export const cargarMalla = async (url, programa) => { 
    const selectModal = $('#modal-nuevo-docente select#asignaturas-docente');
    let malla = await enviarAjax({ programa }, url);

    if (malla) {
        selectModal.append(
            `"<option value='' selected disabled hidden>Añadir asignatura</option>";`
        );
        $.each(malla[programa], function (index, value) {
            selectModal.append(
                `<option value="${value.codprograma} - ${value.codigoCurso}" data-codprograma="${value.codprograma}"> ${value.codprograma} - ${value.curso}</option>`
            );
        });
    }
};

/**
 * Carga en el select de seleccionar asignaturas la malla curricular del programa seleccionado para las facultades transversales.
 */
export const cargarMallaTransversal = async (url) => {
    const selectModal = $('#modal-nuevo-docente select#asignaturas-docente');
    let malla = await enviarAjax({}, url);

    if(malla){
        selectModal.empty();
        selectModal.append(
            `"<option value='' selected disabled hidden>Añadir asignatura</option>";`
        );
        $.each(malla, function(index, value) {
            selectModal.append(
                `<option value="${value.codFacultad} - ${value.codigoCurso}"> ${value.codigoCurso} - ${value.curso}</option>`
            );
        });
    }
}

/**
 * Remover de la malla aquel programa que se ha removido de los asignados al docente.
 * @param {programa} programa
 */
export const removerMalla = async (programa) => {
    const select = $("#modal-nuevo-docente select#asignaturas-docente");
    const listaAsignaturas = $("#asignaturas-seleccionadas-docente");

    // Removemos los <option> cuyo atributo data-codprograma es igual a programa
    select
        .find("option")
        .filter(function () {
            return $(this).data("codprograma") === programa;
        })
        .remove();

    listaAsignaturas
        .find(`li`)
        .has(`input[data-codprograma='${programa}']`)
        .remove();
};

/**
 * Función que crea un nuevo docente, validando que cumpla con todas las condiciones necesarias.
 * 1. Mínimo 1 materia asignado.
 * 2. Máximo 3 materias preferentes asignadas.
 * 3. Máximo 10 materias asignadas.
 * @param {Array} formData
 * @param {url} url
 */
export const crearDocente = async (formData, url) => {
    let totalAsignaturas = $("#asignaturas-seleccionadas-docente li").length;
    let asignaturasPreferentes = $(
        "#asignaturas-seleccionadas-docente li input[type='checkbox']:checked"
    ).length;
    let data = {};
    /** Validación */
    switch (totalAsignaturas) {
        case 0:
            return alertaCupoDocenteInsuficiente();
        case 15:
            return alertaCupoAsginaturasLleno();
        default:
            if (asignaturasPreferentes > 3) {
                alertaCupoPreferentesLleno();
            } else {
                let array = [];
                /**Añadir asignaturas a la data */
                let asignaturasDocente = $(
                    "#asignaturas-seleccionadas-docente li"
                );
                asignaturasDocente.each(function (index, element) {
                    let input = $(element).find("input[type='checkbox']");
                    array.push({
                        materia: input.val(),
                        isChecked: input.is(":checked"),
                    });
                });
                data = {
                    nombre: formData[1].value,
                    idBanner: formData[2].value,
                    email: formData[3].value,
                    cupo: formData[4].value,
                    asignaturas: array,
                };

                let crearDocente = await enviarAjax(data, url);
                if (crearDocente == "success") {
                    await alertaDocenteCreado();
                    location.reload();
                } else {
                    await alertaErrorCrearDocente(crearDocente);
                }
            }
    }
};
