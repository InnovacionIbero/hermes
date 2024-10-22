/**
 * Headers para renderizar datatable de docentes disponibles.
 * @param {object} data
 * @returns
 */
export const headerDataTableDocentesDisponibles = async (data) => {
    return {
        data: data,
        pageLength: 10,
        dom: "Bfrtip",
        //scrollX: true,
        columns: [
            {
                data: "nombre",
                title: "Docente - Email - Id Banner",
                render: function (data, type, row) {
                    if (row) {
                        let nombre = row.nombre ? row.nombre : "";
                        let email = row.email ? row.email : "";
                        let id_banner = row.id_banner ? row.id_banner : "";

                        return (
                            nombre + " - <br>" + email + " - <br>" + id_banner
                        );
                    }
                    return "";
                },
                createdCell: function (td) {
                    $(td).css("width", "20%", "vertical-align", "top");
                },
            },
            {
                data: "codigos_programa_materia",
                title: "Asignaturas",
                render: function (data) {
                    let checkBoxHtml = "";
                    let datos = Object.values(data);
                    datos.forEach((index, value) => {
                        checkBoxHtml += `<input type="checkbox" class="checkbox-codigos-materia" data-codigo="${
                            index.codPrograma
                        } - ${index.codMateria}" ${
                            index.isChecked ? "checked" : ""
                        }>
                            ${index.codPrograma} - ${
                            index.curso.curso
                        }. <i class="button-remover-asignatura fa-solid fa-xmark"></i><br>`;
                    });

                    return checkBoxHtml;
                },
                createdCell: function (td) {
                    $(td).css("width", "25%", "vertical-align", "top");
                },
            },
            {
                data: "programasSelect",
                title: "Agregar programa malla",
                render: function (data, type, row) {
                    let element = '<select class="select-añadir-programa">';

                    element +=
                        "<option value='' selected disabled hidden>Añadir programa</option>";
                    data.programas.forEach((item) => {
                        if (
                            row.programas_docente.length > 0 &&
                            !row.programas_docente.includes(item.codprograma)
                        ) {
                            element += `<option value='${item.codprograma}'>${item.programa.substring(0,30)}${item.programa.length > 30 ? "..." : ""}</option>`;
                        }
                    });

                    element += `</select>`;
                    return element;
                },
                className: "text-center",
            },
            {
                data: "malla",
                title: "Asignaturas disponibles",
                render: function (data, type, row) {
                    let select =
                        "<select name='select' class='select-data-table'>";
                    select +=
                        "<option value='' selected disabled hidden>Añadir asignatura</option>";
                    let datos = Object.values(data);

                    datos.forEach((item) => {
                        if (!row.codigos_materia.includes(item.codigoCurso)) {
                            if (item.hasOwnProperty("codFacultad")) {
                                select += `<option value='${item.codFacultad} - ${item.codigoCurso}'>${item.codigoCurso} - ${item.curso}</option>`;
                            } else {
                                select += `<option value='${item.codprograma} - ${item.codigoCurso}'>${item.codprograma} - ${item.curso}</option>`;
                            }
                        }
                    });

                    select += "</select>";

                    return select;
                },
                className: "text-center",
            },
            {
                data: "cupo_16_semanas",
                title: "Cupo ocupado cursos 16 semanas",
                className: "text-center"
            },
            {
                data: "cupo",
                title: "Cupo docente",
                render: function (data) {
                    let input = `<input class="cupo-disponible-docente" id="number" type="number" value="${data}" />`;
                    return input;
                },
                createdCell: function (td) {
                    $(td).css("width", "5%");
                },
            },
            {
                data: 'disponibilidad',
                title: "Disponibilidad",
                className: "text-center",
                createdCell: function (td, cellData) {
                    const btnClass = cellData == 1 ? 'btn-success' : 'btn-danger';
                    const icon = cellData == 1 ? 'fa-unlock' : 'fa-lock';
                    
                    $(td).html(
                        `<button class='button-inactivar btn ${btnClass}' type='button'>
                            <i class='fa-solid ${icon}'></i>
                        </button>`
                    );
            
                    $(td).css("width", "5%");
                }
            },
            
        ],
    };
};

/**
 * Headers para renderizar datatable de docentes disponibles de facultades transversales.
 * @param {object} data
 * @returns
 */
export const headerDataTableDocentesTrasnversalesDisponibles = async (data) => {
    return {
        data: data,
        pageLength: 10,
        dom: "Bfrtip",
        columns: [
            {
                data: "nombre",
                title: "Docente - Email - Id Banner",
                render: function (data, type, row) {
                    if (row) {
                        let nombre = row.nombre ? row.nombre : "";
                        let email = row.email ? row.email : "";
                        let id_banner = row.id_banner ? row.id_banner : "";

                        return (
                            nombre + " - <br>" + email + " - <br>" + id_banner
                        );
                    }
                    return "";
                },
                createdCell: function (td) {
                    $(td).css("width", "20%", "vertical-align", "top");
                },
            },
            {
                data: "codigos_programa_materia",
                title: "Asignaturas",
                render: function (data) {
                    let checkBoxHtml = "";
                    let datos = Object.values(data);
                    datos.forEach((index) => {
                        checkBoxHtml += `<input type="checkbox" class="checkbox-codigos-materia" data-codigo="${
                            index.codFacultad
                        } - ${index.codMateria}" ${
                            index.isChecked ? "checked" : ""
                        }>
                            ${index.codMateria} - ${
                            index.curso.curso
                        }. <i class="button-remover-asignatura fa-solid fa-xmark"></i><br>`;
                    });

                    return checkBoxHtml;
                },
                createdCell: function (td) {
                    $(td).css("width", "25%", "vertical-align", "top");
                },
            },
            {
                data: "cursos_disponibles",
                title: "Cursos disponibles",
                render: function (data, type, row) {
                    let select =
                        "<select name='select' class='select-data-table'>";
                    select +=
                        "<option value='' selected disabled hidden>Añadir asignatura</option>";

                    let datos = Object.values(data);
                    datos.forEach((item) => {
                        select += `<option value='${row.codFacultad} - ${item.codigoCurso}'>${item.codigoCurso} - ${item.curso}</option>`;
                    });

                    select += "</select>";

                    return select;
                },
                className: "text-center",
            },
            {
                data: "cupo_16_semanas",
                title: "Cupo ocupado cursos 16 semanas",
                className: "text-center"
            },
            {
                data: "cupo",
                title: "Cupo docente",
                render: function (data) {
                    let input = `<input class="cupo-disponible-docente" id="number" type="number" value="${data}" />`;
                    return input;
                },
                createdCell: function (td) {
                    $(td).css("width", "5%");
                },
            },
            {
                data: 'disponibilidad',
                title: "Disponibilidad",
                className: "text-center",
                createdCell: function (td, cellData) {
                    const btnClass = cellData == 1 ? 'btn-success' : 'btn-danger';
                    const icon = cellData == 1 ? 'fa-unlock' : 'fa-lock';
                    
                    $(td).html(
                        `<button class='button-inactivar btn ${btnClass}' type='button'>
                            <i class='fa-solid ${icon}'></i>
                        </button>`
                    );
            
                    $(td).css("width", "5%");
                }
            },
        ],
    };
};

export const destruirTabla = (tabla) => {
    if ($.fn.DataTable.isDataTable(tabla)) {
        tabla.DataTable().destroy();
        tabla.empty();
    }
};
