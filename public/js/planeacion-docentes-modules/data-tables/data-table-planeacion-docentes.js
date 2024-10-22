export const headersDataTablePlaneacionDocentes = async (data) => {
    let mallaCurricular = [];

    console.log(data);

    data[1].forEach((element) => {

        if (!mallaCurricular[element.codigoCurso]) {
            mallaCurricular[element.codigoCurso] = {};
          }

        mallaCurricular[element.codigoCurso][element.codPrograma] = {
            curso: element.curso,
            ciclo: element.ciclo,
            semestre: element.semestre,
        };
    });


    return {
        data: data[0],
        pageLength: 10,
        dom: "Bfrtip",
        columns: [
            {
                data: "cod_materia",
                title: "Codigo de materia",
                className: "text-center",
            },
            {
                data: "cod_materia",
                title: "Asignatura",
                render: function (data, type, row) {
                    return mallaCurricular[data][row.cod_programa].curso;
                },
            },
            {
                data: "cod_programa",
                title: "Codigo de programa",
                className: "text-center",
            },
            {
                data: "plan",
                title: "Plan",
                className: "text-center",
            },
            {
                data: "cod_materia",
                title: "Ciclo",
                render: function (data, type, row) {
                    return mallaCurricular[data][row.cod_programa].ciclo;
                },
                className: "text-center",
            },
            {
                data: "cod_materia",
                title: "Semestre",
                render: function (data, type, row) {
                    return mallaCurricular[data][row.cod_programa].semestre;
                },
                className: "text-center",
            },
            {
                data: "poblacion",
                title: "Poblacion",
                className: "text-center",
            },
            {
                data: "cupo_necesario",
                title: "Cupo necesario",
                className: "text-center",
            },
            {
                data: "cupo_cubierto",
                title: "Cupo cubierto",
                className: "text-center",
            },
            {
                data: "docentes",
                title: "Docentes",
                render: function (data) {
                    let dataArray = JSON.parse(data);
                    let stringDocentes = "";
                    dataArray.forEach((element) => {
                        stringDocentes += `${element.id_banner} - ${element.nombre} - ${element.email} - Cupo asignado: ${element.cupo}<br>`;
                    });

                    return stringDocentes;
                },
            },
        ],
    };
};
