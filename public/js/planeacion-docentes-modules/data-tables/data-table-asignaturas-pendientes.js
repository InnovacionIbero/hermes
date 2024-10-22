export const headersDataTableAsignaturasPendientes = async (data) => {
    let mallaCurricular = [];

    data[1].forEach((element) => {
        mallaCurricular[element.codigoCurso] = element.curso;
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
                render: function (data) {
                    return mallaCurricular[data];
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
                data: "cupo_sin_cubrir",
                title: "Cupo sin cubrir",
                className: "text-center",
            },
        ],
    };
};
