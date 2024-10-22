/**
 * 
 * @param {object} data 
 */
export const headerDataTableDocentesAsignados = (data) => {
    console.log(data)
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
                    $(td).css("width", "25%", "vertical-align", "top");
                },
            },
            {
                data: "asignaturas",
                title: "Asignaturas docente",
                render: function (data) {
                    let datos = Object.values(data);
                    let html = ''
                    datos.forEach(element => {
                        html += `${element.codigoMateria} - ${element.nombreMateria} - Cupo ocupado: ${element.cupo} <br>`
                    });
                    return html;
                }
            },
            {
                data:"cupoAsignado",
                title: "Cupo total asignado docente",
                className: "text-center",
                createdCell: function (td) {
                    $(td).css("width", "5%");
                },
            }
        ]
    }
};
