import { enviarAjax } from "./enviar-ajax.js";

/**
 *
 * @param {HTMLElement} row
 * @param {object} newData
 * @param {object} filtros
 * @param {url} url
 * @param {boolean} isChecked
 * @returns
 */
export const updateDocente = async (
    row,
    newData,
    filtros,
    url,
    isChecked = false
) => {
    let { facultades, programas } = filtros;

    let otherCells = row.find("td");
    let dataDocente = otherCells.eq(0).text();
    let arrayData = dataDocente.split(" - ").map((item) => item.trim());

    let data = {
        arrayData,
        newData,
        facultades,
        programas,
        isChecked,
    };

    return enviarAjax(data, url);
};
