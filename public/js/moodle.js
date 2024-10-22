
var table;
let contador_tabla_cursos = 0;
let contador_tabla_cursosMoocs = 0;
contador_riesgo_estudiantes = 0;
vistaEstudiantes = 0;
// Agregar clase activo al menú
$(document).find("#Moodle").addClass("activo");

$(".content").hide();
$("#ausentismoMoocs").show();


// Navergar entre las dos pestañas de Moodle
$(".menuMoodle").click(function () {
    $(".menuMoodle").removeClass("active");
    $(".content").hide();

    var target = $(this).attr("href").substring(1);

    $("#" + target).show();
    $("#nav" + target).addClass("active");
    return false;
});

// Deshabilitar los checkboxes cuando comienza una solicitud AJAX
$(document).ajaxStart(function () {
    $('div #facultades input[type="checkbox"]').prop("disabled", true);
    $('div #programas input[type="checkbox"]').prop("disabled", true);
    $("#generarReporte").prop("disabled", true);
    $(".botonModal").prop("disabled", true);
});

// Volver a habilitar los checkboxes cuando finaliza una solicitud AJAX
$(document).ajaxStop(function () {
    $('div #facultades input[type="checkbox"]').prop("disabled", false);
    $('div #programas input[type="checkbox"]').prop("disabled", false);
    $("#generarReporte").prop("disabled", false);
    $(".botonModal").prop("disabled", false);
});

var periodosSeleccionados = [];
var programasSeleccionados = [];

/**
 * 
 * @param {Filtros: periodos / programas / cursos} filtros 
 * @param {Urls para cada función según corresponda} urls 
 * @param {¿Necesita filtrar por MOOC?} tipo 
 * @returns 
 */
const validarFiltros = ( filtros , urls, tipo = '' ) => {

    if (filtros.cursos && filtros.cursos.length > 0) {
        data = {
            idcurso: filtro.cursos,
            periodos: filtro.periodos,
            tipo: tipo,
        };
        url = urls.cursos;
        
    } else if (filtros.programa && filtros.programa.length > 0) {
        (data = {
            programa: filtro.programa,
            periodos: filtro.periodos,
            tipo: tipo,
        }),
            url = urls.programas;
            
    } else if (filtros.facultades && filtros.facultades.length > 0) {
        (data = {
            idfacultad: filtro.facultades,
            periodos: filtro.periodos,
            tipo: tipo,
        }),
            url = urls.programas;
    }

    return {
        data, url
    }
}

function inactivarMoocs(){
    $('#ausentismoMoocs').hide();
    $("#navausentismoMoocs").parent().remove();
    $("#navcursosmoocs").parent().remove();
    $("#navausentismocursos").addClass("active");
    $('#ausentismocursos').show();
}


let chartRiesgoAltoMoocs, chartRiesgoMedioMoocs, chartRiesgoBajoMoocs, chartRiesgoIngresoMoocs, chartRiesgoInactivosMoocs;

function riesgoconMoocs(filtro) {
    if (
        chartRiesgoAltoMoocs ||
        chartRiesgoMedioMoocs ||
        chartRiesgoBajoMoocs ||
        chartRiesgoIngresoMoocs ||
        chartRiesgoInactivosMoocs
        
    ) {
        [
            chartRiesgoAltoMoocs,
            chartRiesgoMedioMoocs,
            chartRiesgoBajoMoocs,
            chartRiesgoIngresoMoocs,
            chartRiesgoInactivosMoocs,
        ].forEach((chart) => chart.destroy());
    }
    destruirTabla();

    let urls = {
        cursos: '../Moodle/riesgoCursos',
        programas: '../RiesgoMoocs',
    }

    let { data, url } = validarFiltros(filtro, urls, 'MOOC');

    var datos = $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "post",
        url: url,
        data: data,
        success: function (data) {
            if (data) {
                if (data.total == 0)
                    {
                        inactivarMoocs()
                    }
                    $(".totalMatriculasMoocs").empty();
                    $(".totalMatriculasMoocs").text(data.total);
                var ctx = document.getElementById("altoMoocsCanvas").getContext("2d");
                var TotalAlto = data.total - data.alto;
                var TotalMedio = data.total - data.medio;
                var TotalBajo = data.total - data.bajo;
                var TotalInactivos = data.total - data.inactivos;
                var TotalIngreso = data.total - data.ingreso;
                
                if (TotalAlto <= 0) {
                    TotalAlto = 0;
                }
                if (TotalMedio <= 0) {
                    TotalMedio = 0;
                }
                if (TotalBajo <= 0) {
                    TotalBajo = 0;
                }
                if (TotalIngreso <= 0) {
                    TotalIngreso = 0;
                }
                if (TotalInactivos <= 0) {
                    TotalInactivos = 0;
                }

                chartRiesgoAltoMoocs = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [data.alto, TotalAlto],
                                backgroundColor: [
                                    "rgba(255, 0, 0, 1)",
                                    "rgba(181, 178, 178, 0.5)",
                                ],
                                borderWidth: 1,
                                cutout: "70%",
                                circumference: 180,
                                rotation: 270,
                            },
                        ],
                    },

                    options: {
                        responsive: true,
                        cutoutPercentage: 50,
                        plugins: {
                            datalabels: {
                                color: "transparent",
                                weight: "semibold",
                                size: 16,
                            },
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: data.alto + " Matrículas - " +((data.alto / data.total) * 100).toFixed(2) + "%",
                                color: "red",
                                position: "bottom",
                                font: {
                                    size: 14,
                                },
                                fullSize: false,
                            },
                            tooltip: {
                                enabled: false,
                            },
                        },
                    },
                    plugins: [ChartDataLabels],
                });

                ctx = document.getElementById("InactivosMoocsCanvas").getContext("2d");
                chartRiesgoInactivosMoocs = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [data.inactivos, TotalInactivos], // Aquí puedes ajustar el valor para representar la semicircunferencia deseada
                                backgroundColor: [
                                    "rgba(255, 128, 0, 1)",
                                    "rgba(181, 178, 178, 0.5)",
                                ], // Color de fondo para la semicircunferencia
                                borderWidth: 1,
                                cutout: "70%",
                                circumference: 180,
                                rotation: 270,
                            },
                        ],
                    },

                    options: {
                        responsive: true,
                        cutoutPercentage: 50,
                        plugins: {
                            datalabels: {
                                color: "transparent",
                                weight: "semibold",
                                size: 16,
                            },
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: data.inactivos + " Matrículas - " +((data.inactivos / data.total) * 100).toFixed(2) + "%",
                                color: "orange",
                                position: "bottom",
                                font: {
                                    size: 14,
                                },
                            },
                            tooltip: {
                                enabled: false,
                            },
                        },
                    },
                    plugins: [ChartDataLabels],
                });

                ctx = document.getElementById("sinIngresoMoocsCanvas").getContext("2d");
                var TotalAlto = data.total - data.alto;
                chartRiesgoIngresoMoocs = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [data.ingreso, TotalIngreso],
                                backgroundColor: [
                                    "rgba(161, 130, 98, 0.5)",
                                    "rgba(181, 178, 178, 0.5)",
                                ],
                                borderWidth: 1,
                                cutout: "70%",
                                circumference: 180,
                                rotation: 270,
                            },
                        ],
                    },

                    options: {
                        responsive: true,
                        cutoutPercentage: 50,
                        plugins: {
                            datalabels: {
                                color: "transparent",
                                weight: "semibold",
                                size: 16,
                            },
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: data.ingreso + " Matrículas - " +((data.ingreso / data.total) * 100).toFixed(2) + "%",
                                color: "brown",
                                position: "bottom",
                                font: {
                                    size: 14,
                                },
                                fullSize: false,
                            },
                            tooltip: {
                                enabled: false,
                            },
                        },
                    },
                    plugins: [ChartDataLabels],
                });

                ctx = document.getElementById("medioMoocsCanvas").getContext("2d");
                chartRiesgoMedioMoocs = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [data.medio, TotalMedio], // Aquí puedes ajustar el valor para representar la semicircunferencia deseada
                                backgroundColor: [
                                    "rgba(223, 193, 78, 1)",
                                    "rgba(181, 178, 178, 0.5)",
                                ], // Color de fondo para la semicircunferencia
                                borderWidth: 1,
                                cutout: "70%",
                                circumference: 180,
                                rotation: 270,
                            },
                        ],
                    },

                    options: {
                        responsive: true,
                        cutoutPercentage: 50,
                        plugins: {
                            datalabels: {
                                color: "transparent",
                                weight: "semibold",
                                size: 16,
                            },
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: data.medio + " Matrículas - " +((data.medio / data.total) * 100).toFixed(2) + "%",
                                color: "rgba(121, 85, 61, 1)",
                                position: "bottom",
                                font: {
                                    size: 14,
                                },
                            },
                            tooltip: {
                                enabled: false,
                            },
                        },
                    },
                    plugins: [ChartDataLabels],
                });

                ctx = document.getElementById("bajoMoocsCanvas").getContext("2d");
                chartRiesgoBajoMoocs = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [data.bajo, TotalBajo], // Aquí puedes ajustar el valor para representar la semicircunferencia deseada
                                backgroundColor: [
                                    "rgba(0, 255, 0, 1)",
                                    "rgba(181, 178, 178, 0.5)",
                                ], // Color de fondo para la semicircunferencia
                                borderWidth: 1,
                                cutout: "70%",
                                circumference: 180,
                                rotation: 270,
                            },
                        ],
                    },

                    options: {
                        responsive: true,
                        cutoutPercentage: 50,
                        plugins: {
                            datalabels: {
                                color: "transparent",
                                weight: "semibold",
                                size: 16,
                            },
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: data.bajo + " Matrículas - " +((data.bajo / data.total) * 100).toFixed(2) + "%",
                                color: "Green",
                                position: "bottom",
                                font: {
                                    size: 14,
                                },
                            },
                            tooltip: {
                                enabled: false,
                            },
                        },
                    },
                    plugins: [ChartDataLabels],
                });
                
                if (
                    chartRiesgoAltoMoocs.data.labels.length == 0 &&
                    chartRiesgoAltoMoocs.data.datasets[0].data.length == 0
                ) {
                    $("#colRiesgoAlto").addClass("hidden");
                } else {
                    $("#colRiesgoAlto").removeClass("hidden");
                }
                if (
                    chartRiesgoMedioMoocs.data.labels.length == 0 &&
                    chartRiesgoMedioMoocs.data.datasets[0].data.length == 0
                ) {
                    $("#colRiesgoMedio").addClass("hidden");
                } else {
                    $("#colRiesgoMedio").removeClass("hidden");
                }
                if (
                    chartRiesgoBajoMoocs.data.labels.length == 0 &&
                    chartRiesgoBajoMoocs.data.datasets[0].data.length == 0
                ) {
                    $("#colRiesgoBajo").addClass("hidden");
                } else {
                    $("#colRiesgoBajo").removeClass("hidden");
                }

                if (
                    chartRiesgoIngresoMoocs.data.labels.length == 0 &&
                    chartRiesgoIngresoMoocs.data.datasets[0].data.length == 0
                ) {
                    $("#colRiesgoIngreso").addClass("hidden");
                } else {
                    $("#colRiesgoIngreso").removeClass("hidden");
                }

                if (
                    chartRiesgoInactivosMoocs.data.labels.length == 0 &&
                    chartRiesgoInactivosMoocs.data.datasets[0].data.length == 0
                ) {
                    $("#colRiesgoInactivosMoocs").addClass("hidden");
                } else {
                    $("#colRiesgoInactivosMoocs").removeClass("hidden");
                }
                
                Swal.close();
            } else {
                //Swal.close();
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "No hay Información En este Momento",
                    confirmButtonColor: "#dfc14e",
                });
            }
        },
    });
}

let chartRiesgoAlto, chartRiesgoMedio, chartRiesgoBajo, chartRiesgoIngresoM, chartRiesgoInactivos;

function riesgossinMoocs(filtro) {
    if (
        chartRiesgoAlto ||
        chartRiesgoMedio ||
        chartRiesgoBajo ||
        chartRiesgoIngresoM ||
        chartRiesgoInactivos
        
    ) {
        [
            chartRiesgoAlto,
            chartRiesgoMedio,
            chartRiesgoBajo,
            chartRiesgoIngresoM,
            chartRiesgoInactivos,
        ].forEach((chart) => chart.destroy());
    }
    destruirTabla();

    let urls = {
        cursos: '../Moodle/riesgoCursos',
        programas: '../RiesgoMoocs',
    }

    let { data, url } = validarFiltros(filtro, urls);

    var datos = $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "post",
        url: url,
        data: data,
        success: function (data) {
            if (data) {
                $(".totalMatriculas").empty();
                $(".totalMatriculas").text(data.total);
                var ctx = document.getElementById("alto").getContext("2d");
                var TotalAlto = data.total - data.alto;
                var TotalMedio = data.total - data.medio;
                var TotalBajo = data.total - data.bajo;
                var TotalInactivos = data.total - data.inactivos;
                var TotalIngreso = data.total - data.ingreso;

                if (TotalAlto <= 0) {
                    TotalAlto = 0;
                }
                if (TotalMedio <= 0) {
                    TotalMedio = 0;
                }
                if (TotalBajo <= 0) {
                    TotalBajo = 0;
                }
                if (TotalIngreso <= 0) {
                    TotalIngreso = 0;
                }
                if (TotalInactivos <= 0) {
                    TotalInactivos = 0;
                }

                chartRiesgoAlto = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [data.alto, TotalAlto],
                                backgroundColor: [
                                    "rgba(255, 0, 0, 1)",
                                    "rgba(181, 178, 178, 0.5)",
                                ],
                                borderWidth: 1,
                                cutout: "70%",
                                circumference: 180,
                                rotation: 270,
                            },
                        ],
                    },

                    options: {
                        responsive: true,
                        cutoutPercentage: 50,
                        plugins: {
                            datalabels: {
                                color: "transparent",
                                weight: "semibold",
                                size: 16,
                            },
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: data.alto + " Matrículas - " +((data.alto / data.total) * 100).toFixed(2) + "%",
                                color: "red",
                                position: "bottom",
                                font: {
                                    size: 14,
                                },
                                fullSize: false,
                            },
                            tooltip: {
                                enabled: false,
                            },
                        },
                    },
                    plugins: [ChartDataLabels],
                });

                ctx = document.getElementById("Inactivos").getContext("2d");
                chartRiesgoInactivos = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [data.inactivos, TotalInactivos], // Aquí puedes ajustar el valor para representar la semicircunferencia deseada
                                backgroundColor: [
                                    "rgba(255, 128, 0, 1)",
                                    "rgba(181, 178, 178, 0.5)",
                                ], // Color de fondo para la semicircunferencia
                                borderWidth: 1,
                                cutout: "70%",
                                circumference: 180,
                                rotation: 270,
                            },
                        ],
                    },

                    options: {
                        responsive: true,
                        cutoutPercentage: 50,
                        plugins: {
                            datalabels: {
                                color: "transparent",
                                weight: "semibold",
                                size: 16,
                            },
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: data.inactivos + " Matrículas - " +((data.inactivos / data.total) * 100).toFixed(2) + "%",
                                color: "orange",
                                position: "bottom",
                                font: {
                                    size: 14,
                                },
                            },
                            tooltip: {
                                enabled: false,
                            },
                        },
                    },
                    plugins: [ChartDataLabels],
                });

                ctx = document.getElementById("sinIngreso").getContext("2d");
                var TotalAlto = data.total - data.alto;
                chartRiesgoIngresoM = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [data.ingreso, TotalIngreso],
                                backgroundColor: [
                                    "rgba(161, 130, 98, 0.5)",
                                    "rgba(181, 178, 178, 0.5)",
                                ],
                                borderWidth: 1,
                                cutout: "70%",
                                circumference: 180,
                                rotation: 270,
                            },
                        ],
                    },

                    options: {
                        responsive: true,
                        cutoutPercentage: 50,
                        plugins: {
                            datalabels: {
                                color: "transparent",
                                weight: "semibold",
                                size: 16,
                            },
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: data.ingreso + " Matrículas - " +((data.ingreso / data.total) * 100).toFixed(2) + "%",
                                color: "brown",
                                position: "bottom",
                                font: {
                                    size: 14,
                                },
                                fullSize: false,
                            },
                            tooltip: {
                                enabled: false,
                            },
                        },
                    },
                    plugins: [ChartDataLabels],
                });

                ctx = document.getElementById("medio").getContext("2d");
                chartRiesgoMedio = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [data.medio, TotalMedio], // Aquí puedes ajustar el valor para representar la semicircunferencia deseada
                                backgroundColor: [
                                    "rgba(223, 193, 78, 1)",
                                    "rgba(181, 178, 178, 0.5)",
                                ], // Color de fondo para la semicircunferencia
                                borderWidth: 1,
                                cutout: "70%",
                                circumference: 180,
                                rotation: 270,
                            },
                        ],
                    },

                    options: {
                        responsive: true,
                        cutoutPercentage: 50,
                        plugins: {
                            datalabels: {
                                color: "transparent",
                                weight: "semibold",
                                size: 16,
                            },
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: data.medio + " Matrículas - " +((data.medio / data.total) * 100).toFixed(2) + "%",
                                color: "rgba(121, 85, 61, 1)",
                                position: "bottom",
                                font: {
                                    size: 14,
                                },
                            },
                            tooltip: {
                                enabled: false,
                            },
                        },
                    },
                    plugins: [ChartDataLabels],
                });

                ctx = document.getElementById("bajo").getContext("2d");
                chartRiesgoBajo = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [data.bajo, TotalBajo], // Aquí puedes ajustar el valor para representar la semicircunferencia deseada
                                backgroundColor: [
                                    "rgba(0, 255, 0, 1)",
                                    "rgba(181, 178, 178, 0.5)",
                                ], // Color de fondo para la semicircunferencia
                                borderWidth: 1,
                                cutout: "70%",
                                circumference: 180,
                                rotation: 270,
                            },
                        ],
                    },

                    options: {
                        responsive: true,
                        cutoutPercentage: 50,
                        plugins: {
                            datalabels: {
                                color: "transparent",
                                weight: "semibold",
                                size: 16,
                            },
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: data.bajo + " Matrículas - " +((data.bajo / data.total) * 100).toFixed(2) + "%",
                                color: "Green",
                                position: "bottom",
                                font: {
                                    size: 14,
                                },
                            },
                            tooltip: {
                                enabled: false,
                            },
                        },
                    },
                    plugins: [ChartDataLabels],
                });
                
                if (
                    chartRiesgoAlto.data.labels.length == 0 &&
                    chartRiesgoAlto.data.datasets[0].data.length == 0
                ) {
                    $("#colRiesgoAlto").addClass("hidden");
                } else {
                    $("#colRiesgoAlto").removeClass("hidden");
                }
                if (
                    chartRiesgoMedio.data.labels.length == 0 &&
                    chartRiesgoMedio.data.datasets[0].data.length == 0
                ) {
                    $("#colRiesgoMedio").addClass("hidden");
                } else {
                    $("#colRiesgoMedio").removeClass("hidden");
                }
                if (
                    chartRiesgoBajo.data.labels.length == 0 &&
                    chartRiesgoBajo.data.datasets[0].data.length == 0
                ) {
                    $("#colRiesgoBajo").addClass("hidden");
                } else {
                    $("#colRiesgoBajo").removeClass("hidden");
                }

                if (
                    chartRiesgoIngresoM.data.labels.length == 0 &&
                    chartRiesgoIngresoM.data.datasets[0].data.length == 0
                ) {
                    $("#colRiesgoIngreso").addClass("hidden");
                } else {
                    $("#colRiesgoIngreso").removeClass("hidden");
                }

                if (
                    chartRiesgoInactivos.data.labels.length == 0 &&
                    chartRiesgoInactivos.data.datasets[0].data.length == 0
                ) {
                    $("#colRiesgoInactivos").addClass("hidden");
                } else {
                    $("#colRiesgoInactivos").removeClass("hidden");
                }

                //quitar para cerrar auto modal
                
                Swal.close();
            } else {
                //Swal.close();
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "No hay Información En este Momento",
                    confirmButtonColor: "#dfc14e",
                });
            }
        },
    });
}

let riesgo = ''
$(".botonVerMas").on("click", function () {
    Swal.fire({
        imageUrl:
            "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
    });
    riesgo = $(this).data("value");
    dataTable(riesgo);
});

$(".botonVerMasMoocs").on("click", function () {
    Swal.fire({
        imageUrl:
            "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
    });
    riesgo = $(this).data("value");
    dataTableMoocs(riesgo);
});


var idHistorial = "";

const dataTableRiesgoMatriculas = (datos, columnasVisibles) => {
    return {
        data: datos,
        pageLength: 10,
        dom: "Bfrtip",
        buttons: [
            {
                extend: "excel",
                exportOptions: {
                    columns: columnasVisibles,
                },
            },
            {
                extend: "pdf",
                exportOptions: {
                    columns: columnasVisibles,
                },
            },
        ],
        columns: [
            {
                data: "IdCurso",
                title: "Id Curso",
                visible: false,
            },
            {
                data: "Nombrecurso",
                title: "Nombre curso",
                visible: false,
            },
            {
                data: "Semestre_cuatrimestre",
                title: "Semestre o Cuatrimestre",
                visible: false,
            },
            {
                data: "IdTutor",
                title: "Id Tutor",
                visible: false,
            },
            {
                data: "NombreTutor",
                title: "Nombre Tutor",
                visible: false,
            },
            {
                data: "Id_Banner",
                title: "Id Banner",
            },
            {
                data: null,
                title: "Nombre Completo",
                render: function (data, type, row) {
                    return data.Nombre + " " + data.Apellido;
                },
            },
            {
                data: "Ciclo",
                title: "Ciclo",
                visible: false,
            },
            {
                data: "Duracion_8_16_Semanas",
                title: "Duracion",
                visible: false,
            },
            {
                data: "Nivel",
                title: "Nivel",
                visible: false,
            },
            {
                data: "FechaInicio",
                title: "Fecha Inicio",
                visible: false,
            },
            {
                data: "Fecha_Creacion_Matricula",
                title: "Fecha creacion matricula",
                visible: false,
            },
            {
                data: "Periodo_Rev",
                title: "Periodo",
                visible: false,
            },
            {
                data: "Tipo_Estudiante",
                title: "Tipo estudiante",
                visible: false,
            },
            {
                data: "No_Documento",
                title: "No. Documento",
                visible: false,
            },
            {
                data: "Estado_Banner",
                title: "Estado Banner",
                visible: false,
            },
            {
                data: "Autorizado_ASP",
                title: "Autorizado asistir",
                visible: false,
            },
            {
                data: "Sello",
                title: "Sello financiero",
                visible: false,
            },
            {
                data: "Operador",
                title: "Operador",
                visible: false,
            },
            {
                data: "Facultad",
                title: "Facultad",
            },
            {
                data: "Programa",
                title: "Programa",
            },
            {
                data: "Email",
                title: "Email",
                visible: false,
            },
            {
                data: "Ult_AccesoACurso",
                title: "Ultimo acceso al curso",
                visible: false,
            },
            {
                data: "Riesgo",
                title: "Riesgo",
                visible: false,
            },
            {
                data: "Total_Actividades",
                title: "Total actividades",
                visible: false,
            },
            {
                data: "Actividades_Por_Calificar",
                title: "Actividades por calificar",
                visible: false,
            },
            {
                data: "Nota_Primer_Corte",
                title: "Nota primer corte",
                visible: false,
            },
            {
                data: "Nota_Segundo_Corte",
                title: "Nota segundo corte",
                visible: false,
            },
            {
                data: "Nota_Tercer_Corte",
                title: "Nota tercer corte",
                visible: false,
            },
            {
                data: "Nota_Acumulada",
                title: "Nota acumulada",
                visible: false,
            },
            {
                defaultContent:
                    "<button type='button' id='btn-table' class='data btn btn-warning' data-toggle='modal' data-target='#modaldataEstudiante'><i class='fa-solid fa-user'></i></button>",
                title: "Datos Estudiante",
                className: "text-center",
            },
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json",
        },
    }
}


function dataTable(riesgo) {
    destruirTabla();
    $("#colTabla").removeClass("hidden");

    let urls = {
        cursos: `../home/Moodle/estudiantesCurso/${riesgo}`,
        programas: `../prueba/moodle/tablaRiesgo/${riesgo}`,
    }

    let { data, url } = validarFiltros(filtro, urls);

    var datos = $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "post",
        url: url,
        data: data,
        success: function (data) {
            var datos;
            if (data.data) {
                datos = data.data;
            } else {
                var data = jQuery.parseJSON(data);
                datos = data.data;
            }
            var columnasVisibles = [];
            for (var i = 0; i <= 30; i++) {
                columnasVisibles.push(i);
            }
            table = $("#datatableCursos").DataTable(dataTableRiesgoMatriculas(datos, columnasVisibles));
            riesgoaux = riesgo.toLowerCase();
            var titulo =
                "Estudiantes con riesgo " + riesgoaux + " por Ausentismo";
            $(
                '<div id="tituloTable" class="dataTables_title text-center"> <h4>' +
                    titulo +
                    "</h4></div>"
            ).insertBefore("#datatableCursos");

            function obtenerData(tbody, table) {
                $(tbody).on("click", "button.data", function () {
                    var datos = table.row($(this).parents("tr")).data();
                    idHistorial = datos.Id_Banner;
                    dataAlumno(datos.Id_Banner, ' ');
                });
            }
            obtenerData("#datatableCursos tbody", table);
            Swal.close();
        },
    });
}

function dataTableMoocs(riesgo) {
    destruirTablaMoocs();
    $("#colTablaMoocs").removeClass("hidden");

    let urls = {
        cursos: `../home/Moodle/estudiantesCurso/${riesgo}`,
        programas: `../prueba/moodle/tablaRiesgo/${riesgo}`,
    }

    let { data, url } = validarFiltros(filtro, urls, 'MOOC');

    var datos = $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "post",
        url: url,
        data: data,
        success: function (data) {
            var datos;
            if (data.data) {
                datos = data.data;
            } else {
                var data = jQuery.parseJSON(data);
                datos = data.data;
            }
            var columnasVisibles = [];
            for (var i = 0; i <= 30; i++) {
                columnasVisibles.push(i);
            }
            tableMoocs = $("#datatableMoocs").DataTable(dataTableRiesgoMatriculas(datos, columnasVisibles));
            riesgoaux = riesgo.toLowerCase();
            var titulo =
                "Estudiantes con riesgo " + riesgoaux + " por Ausentismo";
            $(
                '<div id="tituloTableMoocs" class="dataTables_title text-center"> <h4>' +
                    titulo +
                    "</h4></div>"
            ).insertBefore("#datatableMoocs");

            function obtenerData(tbody, tableMoocs) {
                $(tbody).on("click", "button.data", function () {
                    var datos = tableMoocs.row($(this).parents("tr")).data();
                    idHistorial = datos.Id_Banner;
                    dataAlumno(datos.Id_Banner, 'MOOC');
                });
            }
            obtenerData("#datatableMoocs tbody", tableMoocs);
            Swal.close();
        },
    });
}

function destruirTabla() {
    $("#colTabla").addClass("hidden");
    if ($.fn.DataTable.isDataTable("#datatableCursos")) {
        $("#tituloTable").remove();
        table.destroy();
        $("#datatableCursos").DataTable().destroy();
        $("#datatableCursos thead").empty();
        $("#datatableCursos tbody").empty();
        $("#datatableCursos tfooter").empty();
        $("#datatableCursos tbody").off("click", "button.data");
    }
}

function destruirTablaMoocs() {
    $("#colTablaMoocs").addClass("hidden");
    if ($.fn.DataTable.isDataTable("#datatableMoocs")) {
        $("#tituloTableMoocs").remove();
        tableMoocs.destroy();
        $("#datatableMoocs").DataTable().destroy();
        $("#datatableMoocs thead").empty();
        $("#datatableMoocs tbody").empty();
        $("#datatableMoocs tfooter").empty();
        $("#datatableMoocs tbody").off("click", "button.data");
    }
}

var chartRiesgoIngreso;
var chartRiesgoNotas;

/**
 * Función para llenar modal con la data del estudiante seleccionado
 */

function dataAlumno(id, tipo) {

    $(".multi-collapse").removeClass("collapse show");
    $(".multi-collapse").addClass("collapse");

    if (filtro.cursos && filtro.cursos.length > 0) {
        data = {
            idcurso: filtro.cursos,
            idBanner: id,
            tipo: tipo
        },
            url = "../home/Moodle/dataAlumnoCurso";
    } else if (filtro.programa && filtro.programa.length > 0) {
        data = {
            idBanner: id,
            tipo: tipo
        },
            url = "../prueba/Moodle/datosEstudiante";
    } else if (filtro.facultades && filtro.facultades.length > 0) {
        data = {
            idBanner: id,
            tipo: tipo
        };
        url = "../prueba/Moodle/datosEstudiante";
    }

    return new Promise(function (resolve, reject) {
        limpiarModal();
        alertaPreload();
        var datos = $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            url: url,
            data: data,
            method: "post",
            success: function (data) {
                var primerArray;
                if (data.data) {
                    primerArray = data.data[0];
                } else {
                    var data = jQuery.parseJSON(data);
                    primerArray = data.data[0];
                }

                if (data.data.length > 0) {
                    $("#tituloEstudiante strong").append(
                        "Datos estudiante: " +
                            primerArray.Nombre +
                            " " +
                            primerArray.Apellido +
                            " - " +
                            primerArray.Id_Banner
                    );
                    $("#nombreModal").append(
                        "<strong>" +
                            primerArray.Nombre +
                            " " +
                            primerArray.Apellido +
                            "</strong>"
                    );
                    $("#idModal").append(
                        "<strong>" + primerArray.Id_Banner + "</strong>"
                    );
                    $("#facultadModal").append(
                        "<strong>" + primerArray.Facultad + "</strong>"
                    );
                    $("#programaModal").append(
                        "<strong>" + primerArray.Programa + "</strong>"
                    );

                    /** Segunda Card */
                    $("#documentoModal").append(
                        "<strong>Documento de identidad: </strong>" +
                            primerArray.No_Documento
                    );
                    $("#correoModal").append(
                        "<strong>Correo institucional: </strong>" +
                            primerArray.Email
                    );
                    $("#selloModal").append(
                        "<strong>Sello financiero: </strong>" +
                            primerArray.Sello
                    );
                    $("#estadoModal").append(
                        "<strong>Estado: </strong>" + primerArray.Estado_Banner
                    );
                    $("#tipoModal").append(
                        "<strong>Tipo estudiante: </strong>" +
                            primerArray.Tipo_Estudiante
                    );
                    if (
                        primerArray.Autorizado_ASP !== undefined &&
                        primerArray.Autorizado_ASP !== null
                    ) {
                        $("#autorizadoModal").append(
                            "<strong>Autorizado: </strong>" +
                                primerArray.Autorizado_ASP
                        );
                    }

                    if (
                        primerArray.Operador !== undefined &&
                        primerArray.Operador !== null
                    ) {
                        $("#operadorModal").append(
                            "<strong>Operador: </strong>" + primerArray.Operador
                        );
                    }
                    $("#convenioModal").append(
                        "<strong>Convenio: </strong>" + primerArray.Convenio
                    );

                    data.data.forEach((dato) => {
                        $("#tablaNotas tbody").append(`<tr>
                        <td>${
                            dato.Nombrecurso !== undefined &&
                            dato.Nombrecurso !== null
                                ? dato.Nombrecurso
                                : "-"
                        }</td>
                        <td>${
                            dato.Total_Actividades !== undefined &&
                            dato.Total_Actividades !== null
                                ? dato.Total_Actividades
                                : "-"
                        }</td>
                        <td>${
                            dato.Actividades_Por_Calificar !== undefined &&
                            dato.Actividades_Por_Calificar !== null
                                ? dato.Actividades_Por_Calificar
                                : "-"
                        }</td>
                        <td>${
                            dato.Cuestionarios_Intentos_Realizados !==
                                undefined &&
                            dato.Cuestionarios_Intentos_Realizados !== null
                                ? dato.Cuestionarios_Intentos_Realizados
                                : "-"
                        }</td>
                        <td>${
                            dato.Nota_Primer_Corte != undefined &&
                            dato.Nota_Primer_Corte != null
                                ? dato.Nota_Primer_Corte
                                : "-"
                        }</td>
                        <td>${
                            dato.Nota_Segundo_Corte != undefined &&
                            dato.Nota_Segundo_Corte != null
                                ? dato.Nota_Segundo_Corte
                                : "-"
                        }</td>
                        <td>${
                            dato.Nota_Tercer_Corte != undefined &&
                            dato.Nota_Tercer_Corte != null
                                ? dato.Nota_Tercer_Corte
                                : "-"
                        }</td>
                        <td>${
                            dato.Nota_Acumulada != undefined &&
                            dato.Nota_Acumulada != null
                                ? dato.Nota_Acumulada
                                : "-"
                        }</td>
                    </tr>`);
                    });
                    graficosModal(id);
                    resolve(data);
                } else {
                    reject("No se encontraron datos");
                }
                /** Primera Card */
            },
        });
    });
}

/*
 * Método que grafica los datos en el Modal
 */
function graficosModal(id) {
    if (filtro.cursos && filtro.cursos.length > 0) {
        (data = {
            idcurso: filtro.cursos,
            idBanner: id,
        }),
            (url = "../home/Moodle/riesgoAsistenciaCurso");
    } else if (filtro.programa && filtro.programa.length > 0) {
        (data = {
            idBanner: id,
        }),
            (url = "../prueba/Moodle/riesgoAsistencia");
    } else if (filtro.facultades && filtro.facultades.length > 0) {
        data = {
            idBanner: id,
        };
        url = "../prueba/Moodle/riesgoAsistencia";
    }

    var charts = $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        url: url,
        data: data,
        method: "post",
        success: function (data) {
            try {
                data = jQuery.parseJSON(data);
            } catch {
                data = data;
            }
            console.log(data)
            var ctx = document.getElementById("riesgoIngreso").getContext("2d");

            var labelsAsistencia = [];
            var coloresRiesgo = [];
            var valoreAsistencia = [];
            data.data.riesgoAsistencia.forEach(function (valor) {
                if (valor.Riesgo === "ALTO") {
                    coloresRiesgo.push("rgba(255, 0, 0, 0.8)");
                } else if (valor.Riesgo === "MEDIO") {
                    coloresRiesgo.push("rgba(220, 205, 48, 1)");
                } else if (valor.Riesgo === "BAJO") {
                    coloresRiesgo.push("rgba(0, 255, 0, 0.8)");
                }

                valoreAsistencia.push(1);
                labelsAsistencia.push(valor.Nombrecurso.slice(0, 10) + "...");
            });

            chartRiesgoIngreso = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labelsAsistencia,
                    datasets: [
                        {
                            label: "Riesgo por asistencia",
                            data: valoreAsistencia,
                            backgroundColor: coloresRiesgo,
                            datalabels: {
                                anchor: "end",
                                align: "top",
                                formatter: (value) => {
                                    if (value === "Sin Actividad") {
                                        return value;
                                    } else {
                                        return value.toFixed(1);
                                    }
                                },
                            },
                        },
                    ],
                },
                options: {
                    scales: {
                        y: {
                            max: 1,
                        },
                    },
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        datalabels: {
                            color: "black",
                            font: {
                                weight: "semibold",
                            },
                            formatter: Math.round,
                        },
                        legend: {
                            position: "bottom",
                            labels: {
                                font: {
                                    size: 12,
                                },
                            },
                        },
                        tooltips: {
                            callbacks: {
                                label: function (context) {
                                    return labelsCompleto[context.index];
                                },
                            },
                        },
                    },
                },
                plugins: [ChartDataLabels],
            });

            var labels = [];
            var labelsCompleto = [];
            var valores = [];
            var colores = [];

            Object.keys(data.data.notas).forEach((curso) => {
                labelsCompleto.push(curso);
                const cursoCorto = curso.slice(0, 10);
                labels.push(cursoCorto + "...");
                var valor = data.data.notas[curso];

                valor = valor.split("-");
                const valorFormateado = parseFloat(valor[0]).toFixed(2);
                valores.push(valorFormateado);

                const $tabla = $("#tablaNotas tbody tr");
                $tabla.each(function () {
                    const $fila = $(this);
                    const primerCelda = $fila.find("td:first").text();

                    // Verificar si la primera celda contiene 'curso' o parte de él
                    if (primerCelda.includes(curso)) {
                        
                        if(valor[1] == " No hay datos suficientes")
                        {
                            $fila.append(`<td>Sin datos por analizar</td>`);
                        }else{
                            $fila.append(`<td>${valorFormateado}</td>`);
                        }

                    }
                });

                if (valor[1] == " No hay datos suficientes") {
                    colores.push("rgba(128, 128, 128, 0.8)");
                } else {
                    valor = parseFloat(valor[0]);
                    if (valor < 3) {
                        colores.push("rgba(255, 0, 0, 0.8)");
                    }
                    if (valor >= 3 && valor <= 3.5) {
                        colores.push("rgba(220, 205, 48, 1)");
                    }
                    if (valor > 3.5) {
                        colores.push("rgba(0, 255, 0, 0.8)");
                    }
                }
            });


            ctx = document.getElementById("riesgoNotas").getContext("2d");
            const dataArray = Object.values(data.data.notas);

            chartRiesgoNotas = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: "Riesgo según notas",
                            data: valores.map((value) =>
                                value == "Sin Actividad"
                                    ? value
                                    : parseFloat(value)
                            ),
                            backgroundColor: colores,
                            datalabels: {
                                anchor: "end",
                                align: "top",
                                formatter: (value) => {
                                    if (value === "Sin Actividad") {
                                        return value;
                                    } else {
                                        return value.toFixed(1);
                                    }
                                },
                            },
                        },
                    ],
                },
                options: {
                    scales: {
                        y: {
                            max: 6,
                        },
                    },
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        datalabels: {
                            color: "black",
                            font: {
                                weight: "semibold",
                            },
                            formatter: Math.round,
                        },
                        legend: {
                            position: "bottom",
                            labels: {
                                font: {
                                    size: 12,
                                },
                            },
                        },
                        tooltips: {
                            callbacks: {
                                label: function (context) {
                                    return labelsCompleto[context.index];
                                },
                            },
                        },
                    },
                },
                plugins: [ChartDataLabels],
            });
            Swal.close();
        },
    });
}

/**
 * Función para limpiar el modal de la data de un estudiante
 */
function limpiarModal() {
    $(
        "#tituloEstudiante strong, #nombreModal, #idModal, #facultadModal, #programaModal, #documentoModal, #correoModal, #selloModal, #estadoModal, #tipoModal, #autorizadoModal, #operadorModal, #convenioModal, #tablaNotas tbody"
    ).empty();

    if (chartRiesgoIngreso && chartRiesgoNotas) {
        [chartRiesgoIngreso, chartRiesgoNotas].forEach((chart) =>
            chart.destroy()
        );
    }
}

$("#navcursos").click(function () {
    if (contador_tabla_cursos == 0) {
        contador_tabla_cursos++;

        tablaCursos()
            .then(function (respuesta) {
                Swal.close();
            })
            .catch(function (error) {
                Swal.close();
            });
    }
});

$("#navcursosmoocs").click(function () {
    if (contador_tabla_cursosMoocs == 0) {
        contador_tabla_cursosMoocs++;

        tablaCursosMoocs()
            .then(function (respuesta) {
                Swal.close();
            })
            .catch(function (error) {
                Swal.close();
            });
    }
});

$("#navestudiantes").click(function () {
    vistaEstudiantes = 1;
    if (contador_riesgo_estudiantes == 0) {
        contador_riesgo_estudiantes++;

        riesgoEstudiantes();
    }
});

$("#navausentismocursos").click(function () {
    vistaEstudiantes = 0;
});

$("#navausentismoMoocs").click(function () {
    vistaEstudiantes = 0;
});

$(".descargar-todo").click(function () {

    Swal.fire({
        title: "Descargando...",
        text: "Descargando la información solicitada, este proceso puede tardar unos segundos dependiendo de tu conexión a internet.",
        imageUrl:
            "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
    });

    urls = {
        cursos: '../Moodle/descargarriesgocursos',
        programas: '../prueba/moodle/descargardatos',
    }

    let { data, url } = validarFiltros(filtro, urls, $(this).data('descarga') == 'moocs' ? 'MOOC' : '');

    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "post",
        url: url,
        data: data,
        success: function (data) {
            try {
                data = jQuery.parseJSON(data);
            } catch {
                data = data;
            }
            var ultimoIngreso;
            var accesoCurso;
            var newData = [];
            var headers = [
                "Id Curso",
                "Codigo Materia",
                "Nombre Corto",
                "Nombre Curso",
                "Semestre_Cuatrimestre",
                "Id Tutor",
                "Nombre Tutor",
                "Username Tutor",
                "Email Tutor",
                "Operacion",
                "Categoria Curso",
                "Ciclo",
                "Duracion",
                "Nivel",
                "Visibilidad Curso",
                "Fecha Inicio",
                "Fecha Creación Matrícula",
                "Fecha Modificación Matrícula",
                "Periodo",
                "Tipo Estudiante",
                "No Documento",
                "Id Banner",
                "Estado Banner",
                "Autorizado ASP",
                "Sello",
                "Convenio",
                "Operador",
                "Programa",
                "Facultad",
                "Id Moodle",
                "Nombre",
                "Apellido",
                "Email",
                "Email personal",
                "Último Acceso Plataforma",
                "Último Acceso Curso",
                "Riesgo",
                "Total Actividades",
                "Actividades por calificar",
                "Intentos realizados cuestionarios",
                "Nota Primer Corte",
                "Nota Segundo Corte",
                "Nota Tercer Corte",
                "Nota Acumulada",
            ];
            newData.push(headers);
            data.forEach(function (item) {
                if (item.Ultacceso_Plataforma == "1969-12-31 19:00:00.000") {
                    ultimoIngreso = "NUNCA";
                } else {
                    ultimoIngreso = item.Ultacceso_Plataforma;
                }

                if (item.Ult_AccesoACurso == null) {
                    accesoCurso = "NUNCA";
                } else {
                    accesoCurso = item.Ult_AccesoACurso;
                }

                var fila = [
                    item.IdCurso,
                    item.Cod_materia,
                    item.Nombrecorto,
                    item.Nombrecurso,
                    item.Semestre_cuatrimestre,
                    item.IdTutor,
                    item.NombreTutor,
                    item.usernameTutor,
                    item.email_userTutor,
                    item.Operacion,
                    item.Categoria_Curso,
                    item.Ciclo,
                    item.Duracion_8_16_Semanas,
                    item.Nivel,
                    item.VisibilidadCurso,
                    item.FechaInicio,
                    item.Fecha_Creacion_Matricula,
                    item.Fecha_Modificacion_Matricula,
                    item.Periodo_Rev,
                    item.Tipo_Estudiante,
                    item.No_Documento,
                    item.Id_Banner,
                    item.Estado_Banner,
                    item.Autorizado_ASP,
                    item.Sello,
                    item.Convenio,
                    item.Operador,
                    item.Programa,
                    item.Facultad,
                    item.Idmoodle,
                    item.Nombre,
                    item.Apellido,
                    item.Email,
                    item.Emailpersonal,
                    ultimoIngreso,
                    accesoCurso,
                    item.Riesgo,
                    item.Total_Actividades,
                    item.Actividades_Por_Calificar,
                    item.Cuestionarios_Intentos_Realizados,
                    item.Nota_Primer_Corte,
                    item.Nota_Segundo_Corte,
                    item.Nota_Tercer_Corte,
                    item.Nota_Acumulada,
                ];
                newData.push(fila);
            });
            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.aoa_to_sheet(newData);
            XLSX.utils.book_append_sheet(wb, ws, "Informe");
            XLSX.writeFile(wb, "informe de ausentismo.xlsx");
            Swal.close();
        },
    });
});

$(".descargarTodoFlash").click(function () {
    Swal.fire({
        title: "Descargando...",
        text: "Descargando la información solicitada, este proceso puede tardar unos segundos dependiendo de tu conexión a internet.",
        imageUrl:
            "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
    });
    urls = {
        cursos: '../Moodle/descargarriesgocursosflash',
        programas: '../prueba/moodle/descargardatosflash',
    }

    let { data, url } = validarFiltros(filtro, urls, $(this).data('descarga') == 'moocs' ? 'MOOC' : '');

    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "post",
        url: url,
        data: data,
        success: function (data) {
            try {
                data = jQuery.parseJSON(data);
            } catch {
                data = data;
            }
            var ultimoIngreso;
            var accesoCurso;
            var newData = [];
            var headers = [
                "Id Curso",
                "Nombre Curso",
                "Semestre_Cuatrimestre",
                "Id Tutor",
                "Nombre Tutor",
                "Ciclo",
                "Duracion",
                "Nivel",
                "Visibilidad Curso",
                "Fecha Inicio",
                "Grupo",
                "Fecha Creación Matrícula",
                "Periodo",
                "Tipo Estudiante",
                "No Documento",
                "Id Banner",
                "Estado Banner",
                "Autorizado ASP",
                "Sello",
                "Operador",
                "Programa",
                "Facultad",
                "Nombre",
                "Apellido",
                "Email",
                "Último Acceso Curso",
                "Riesgo",
                "Total Actividades",
                "Actividades por calificar",
                "Nota Primer Corte",
                "Nota Segundo Corte",
                "Nota Tercer Corte",
                "Nota Acumulada",
            ];
            newData.push(headers);
            data.forEach(function (item) {
                if (item.Ult_AccesoACurso == null) {
                    accesoCurso = "NUNCA";
                } else {
                    accesoCurso = item.Ult_AccesoACurso;
                }

                var fila = [
                    item.IdCurso,
                    item.Nombrecurso,
                    item.Semestre_cuatrimestre,
                    item.IdTutor,
                    item.NombreTutor,
                    item.Ciclo,
                    item.Duracion_8_16_Semanas,
                    item.Nivel,
                    item.VisibilidadCurso,
                    item.FechaInicio,
                    item.Grupo,
                    item.Fecha_Creacion_Matricula,
                    item.Periodo_Rev,
                    item.Tipo_Estudiante,
                    item.No_Documento,
                    item.Id_Banner,
                    item.Estado_Banner,
                    item.Autorizado_ASP,
                    item.Sello,
                    item.Operador,
                    item.Programa,
                    item.Facultad,
                    item.Nombre,
                    item.Apellido,
                    item.Email,
                    accesoCurso,
                    item.Riesgo,
                    item.Total_Actividades,
                    item.Actividades_Por_Calificar,
                    item.Nota_Primer_Corte,
                    item.Nota_Segundo_Corte,
                    item.Nota_Tercer_Corte,
                    item.Nota_Acumulada,
                ];
                newData.push(fila);
            });
            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.aoa_to_sheet(newData);
            XLSX.utils.book_append_sheet(wb, ws, "Informe");
            XLSX.writeFile(wb, "informe de ausentismo corto.xlsx");
            Swal.close();
        },
    });
});

$("#descargarInformeAcademico").click(function () {
    url = "../prueba/moodle/descargarriesgoacademico";
    Swal.fire({
        title: "Descargando...",
        text: "Descargando la información solictada, este proceso puede tardar unos segundos dependiendo de tu conexión a internet.",
        imageUrl:
            "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
    });

    if (filtro.cursos && filtro.cursos.length > 0) {
        (data = {
            idcurso: filtro.cursos,
            periodos: filtro.periodos,
        }),
            (url = "../prueba/moodle/descargarriesgoacademicocursos");
    } else if (filtro.programa && filtro.programa.length > 0) {
        data = {
            programa: filtro.programa,
            periodos: filtro.periodos,
        };
    } else if (filtro.facultades && filtro.facultades.length > 0) {
        data = {
            idfacultad: filtro.facultades,
            periodos: filtro.periodos,
        };
    }

    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "post",
        url: url,
        data: data,
        success: function (data) {
            try {
                data = jQuery.parseJSON(data);
            } catch {
                data = data;
            }
            var newData = [];
            var headers = [
                "Id Banner",
                "Nombre",
                "Código Programa",
                "Programa",
                "Periodo",
                "Sello",
                "Operador",
                "Tipo estudiante",
                "Autorizado asistir",
                "Riesgo",
                "Materias en riesgo alto",
                "Materias en riesgo medio",
                "Materias en riesgo bajo",
                "Materias sin ingreso",
                "Total de materias activas",
                "Promedio materias activas",
            ];

            newData.push(headers);

            data.forEach(function (item) {
              

                var fila = [
                    item.id_banner,
                    item.nombre,
                    item.cod_programa,
                    item.nombre_programa,
                    item.periodo,
                    item.sello,
                    item.Operador,
                    item.Tipo_Estudiante,
                    item.autorizado,
                    item.riesgo,
                    item.alto,
                    item.medio,
                    item.bajo,
                    item.sin_ingreso,
                    item.total_materias,
                    item.nota,
                ];
                newData.push(fila);
            });

            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.aoa_to_sheet(newData);
            XLSX.utils.book_append_sheet(wb, ws, "Informe");
            XLSX.writeFile(wb, "informe riesgo academico corto.xlsx");
            Swal.close();
        },
    });
});

/**
 * Función que limpia la data de la tabla de cursosMoodle
 */
function destruirTablaCurso() {
    if ($.fn.DataTable.isDataTable("#tablaCursos")) {
        tablacursosMoodle.destroy();
        $("#tablaCursos").DataTable().destroy();
        $("#tablaCursos thead").empty();
        $("#tablaCursos tbody").empty();
        $("#tablaCursos tfooter").empty();
    }
}

function destruirTablaCursoMoocs() {
    console.log('entra')
    if($.fn.DataTable.isDataTable("#tablaCursosMoocs")) {
        console.log('Entra destroy')
        tablacursosMoodleMoocs.destroy();
        $("#tablaCursosMoocs").DataTable().destroy();
        $("#tablaCursosMoocs thead").empty();
        $("#tablaCursosMoocs tbody").empty();
        $("#tablaCursosMoocs tfooter").empty();
    }
}

const objectDataTableCourses = ( data ) => {
    return {
    dom: "Bfrtip",
    data: data,
    responsive: true,
    buttons: [
        "copy",
        {
            extend: "excel",
            title: "Reporte cursos activos - Hermes",
            exportOptions: {
                columns: [
                    0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 16, 17, 18,
                    19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29
                ],
            },
        },
        "pdf",
        "print",
    ],
    pageLength: 10,
    fixedHeader: true,
    columnDefs: [
        {
            width: "20px",
            targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 16],
        },
    ],
    columns: [
        {
            data: "Id_Curso",
            title: "Id Curso",
        },
        {
            data: "Nombre_curso",
            title: "Nombre Curso",
        },
        {
            data: "Programa",
            title: "Programa",
        },
        {
            data: "Nombre_tutor",
            title: "Tutor",
        },
        {
            data: "Id_Tutor",
            title: "Id tutor",
            className: "text-center",
            visible: false,
        },
        {
            data: "Correo_tutor",
            title: "Correo tutor",
            className: "text-center",
            visible: false,
        },
        {
            data: "Total_estudiantes",
            title: "Total estudiantes",
            className: "text-center",
        },
        {
            data: "Sello",
            title: "Con sello",
            className: "text-center",
        },
        {
            data: "Asp",
            title: "ASP",
            className: "text-center",
        },
        {
            data: "Cursos",
            title: "Grupos abiertos",
            className: "text-center",
        },
        {
            data: "Riesgo_critico",
            title: "Riesgo critico",
            className: "text-center",
        },
        {
            data: "Riesgo_alto",
            title: "Riesgo alto",
            className: "text-center",
        },
        {
            data: "Riesgo_medio",
            title: "Riesgo medio",
            className: "text-center",
        },
        {
            data: "Riesgo_bajo",
            title: "Riesgo bajo",
            className: "text-center",
        },
        {
            data: "Riesgo_inactivos",
            title: "Inactivos",
            className: "text-center",
        },
        {
            data: "Sin_ingreso",
            title: "Estudiantes sin ingreso",
            className: "text-center",
        },
        {
            data: "Repitentes",
            title: "Repitentes",
            className: "text-center",
        },
        {
            defaultContent:
                "<button type='button' class='descargar btn btn-warning'><i class='fa-solid fa-download'></i></button>",
            title: "Descargar datos",
            className: "text-center",
        },
        {
            data: "Sin_ingreso",
            title: "Sin ingreso",
            className: "text-center",
            render: function (data, type, row) {
                var partes = data.split("<br>");
                return partes[0] || "";
            },
            visible: false,
        },
        {
            data: "Riesgo_critico",
            title: "Estudiantes en riesgo critico",
            className: "text-center",
            render: function (data, type, row) {
                var partes = data.split("<br>");
                return partes[0] || "";
            },
            visible: false,
        },
        {
            data: "Riesgo_alto",
            title: "Estudiantes en riesgo alto",
            className: "text-center",
            render: function (data, type, row) {
                var partes = data.split("<br>");
                return partes[0] || "";
            },
            visible: false,
        },
        {
            data: "Riesgo_medio",
            title: "Estudiantes en riesgo medio",
            className: "text-center",
            render: function (data, type, row) {
                var partes = data.split("<br>");
                return partes[0] || "";
            },
            visible: false,
        },
        {
            data: "Riesgo_bajo",
            title: "Estudiantes en riesgo bajo",
            className: "text-center",
            render: function (data, type, row) {
                var partes = data.split("<br>");
                return partes[0] || "";
            },
            visible: false,
        },
        {
            data: "Riesgo_inactivos",
            title: "Estudiantes inactivos",
            className: "text-center",
            render: function (data, type, row) {
                var partes = data.split("<br>");
                return partes[0] || "";
            },
            visible: false,
        },
        {
            data: "Sin_ingreso",
            title: "Porcentaje sin ingreso",
            className: "text-center",
            render: function (data, type, row) {
                var partes = data.split("<br>");
                return partes[1] || "";
            },
            visible: false,
        },
        {
            data: "Riesgo_critico",
            title: "Porcentaje riesgo critico",
            className: "text-center",
            render: function (data, type, row) {
                var partes = data.split("<br>");
                return partes[1] || "";
            },
            visible: false,
        },
        {
            data: "Riesgo_alto",
            title: "Porcentaje riesgo alto",
            className: "text-center",
            render: function (data, type, row) {
                var partes = data.split("<br>");
                return partes[1] || "";
            },
            visible: false,
        },
        {
            data: "Riesgo_medio",
            title: "Porcentaje riesgo medio",
            className: "text-center",
            render: function (data, type, row) {
                var partes = data.split("<br>");
                return partes[1] || "";
            },
            visible: false,
        },
        {
            data: "Riesgo_bajo",
            title: "Porcentaje riesgo bajo",
            className: "text-center",
            render: function (data, type, row) {
                var partes = data.split("<br>");
                return partes[1] || "";
            },
            visible: false,
        },
        {
            data: "Riesgo_inactivos",
            title: "Porcentaje inactivos",
            className: "text-center",
            render: function (data, type, row) {
                var partes = data.split("<br>");
                return partes[1] || "";
            },
            visible: false,
        },
    ],
    language: {
        url: "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json",
    },
}
}

/**
 * Tabla cursos Moodle
 */
function tablaCursos() {
    return new Promise(function (resolve, reject) {
        Swal.fire({
            imageUrl:
                "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
        });

        destruirTablaCurso();
        $("#tablaCursos").empty();
        var mensaje = "Cargando mas de 70000 datos, por favor espere...";
        $("#tablaCursos").append(mensaje);

        var data;
        if (filtro.cursos && filtro.cursos.length > 0) {
            (data = {
                idcurso: filtro.cursos,
                periodos: filtro.periodos,
            }),
                (url = "../home/Moodle/tablaCursosVista");
        } else if (filtro.programa && filtro.programa.length > 0) {
            (data = {
                programa: filtro.programa,
                periodos: filtro.periodos,
            }),
                (url = "../prueba/Moodle/tablaCursos");
        } else if (filtro.facultades && filtro.facultades.length > 0) {
            (data = {
                idfacultad: filtro.facultades,
                periodos: filtro.periodos,
            }),
                (url = "../prueba/Moodle/tablaCursos");
        }

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "post",
            url: url,
            data: data,
            success: function (data) {
                try {
                    data = parseJSON(data);
                } catch {
                    data = data;
                }
                $("#tablaCursos").empty();
                tablacursosMoodle = $("#tablaCursos").DataTable(objectDataTableCourses(data));
                
                
                $(document).find("#tablaCursos").removeClass("dataTable");
                function descargarData(tbody, tablacursosMoodle) {
                    Swal.fire({
                        imageUrl:
                            "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    });
                    $(tbody).on("click", "button.descargar", function () {
                        var datos = tablacursosMoodle
                            .row($(this).parents("tr"))
                            .data();
                        descargarDataCurso(datos.Id_Curso, datos.Nombre_curso, datos.Programa);
                    });
                }

                descargarData("#tablaCursos tbody", tablacursosMoodle);

                resolve(data);
            },
            error: function (xhr, status, error) {
                reject(error); // Rechaza la promesa en caso de error
            },
        });
    });
}

/**
 * Tabla cursos Moocs
 */
function tablaCursosMoocs() {
    return new Promise(function (resolve, reject) {
        Swal.fire({
            imageUrl:
                "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
            showConfirmButton: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
        });

        destruirTablaCursoMoocs();
        $("#tablaCursosMoocs").empty();
        var mensaje = "Cargando mas de 70000 datos, por favor espere...";
        $("#tablaCursosMoocs").append(mensaje);

        var data;
        if (filtro.cursos && filtro.cursos.length > 0) {
            (data = {
                idcurso: filtro.cursos,
                periodos: filtro.periodos,
                tipo: 'MOOC',
            }),
            (url = "../home/Moodle/tablaCursosVista");
        } else if (filtro.programa && filtro.programa.length > 0) {
            (data = {
                programa: filtro.programa,
                periodos: filtro.periodos,
                tipo: 'MOOC',
            }),
            (url = "../prueba/Moodle/tablaCursos");
        } else if (filtro.facultades && filtro.facultades.length > 0) {
            (data = {
                idfacultad: filtro.facultades,
                periodos: filtro.periodos,
                tipo: 'MOOC',
            }),
                (url = "../prueba/Moodle/tablaCursos");
        }

        $.ajax({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            type: "post",
            url: url,
            data: data,
            success: function (data) {
                try {
                    data = parseJSON(data);
                } catch {
                    data = data;
                }
                $("#tablaCursosMoocs").empty();
                tablacursosMoodleMoocs = $("#tablaCursosMoocs").DataTable(objectDataTableCourses(data));
                
                $(document).find("#tablaCursosMoocs").removeClass("dataTable");
                
                function descargarData(tbody, tablacursosMoodleMoocs) {
                    Swal.fire({
                        imageUrl:
                            "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    });
                    $(tbody).on("click", "button.descargar", function () {
                        var datos = tablacursosMoodleMoocs
                            .row($(this).parents("tr"))
                            .data();
                        descargarDataCurso(datos.Id_Curso, datos.Nombre_curso, datos.Programa);
                    });
                }

                descargarData("#tablaCursosMoocs tbody", tablacursosMoodleMoocs);

                resolve(data);
            },
            error: function (xhr, status, error) {
                reject(error); // Rechaza la promesa en caso de error
            },
        });
    });
}

function descargarDataCurso(IdCurso, nombreCurso, programa) {
    alertaPreload();
    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "post",
        url: "../prueba/Moodle/descargardatacurso",
        data: {
            id: IdCurso,
            programa: programa
        },
        success: function (data) {
            try {
                data = jQuery.parseJSON(data);
            } catch {
                data = data;
            }

            var newData = [];

            var headers = [
                "Id Banner",
                "Codigo Programa",
                "Nota 1",
                "Nota 2",
                "Nota 3",
                "Nota Acumulada",
                "Nota Proyectada Hermes",
                "Riesgo",
            ];

            var titulo = ["Informe académico curso " + nombreCurso];

            var col1 = [];
            var col2 = [];
            var col3 = [];
            var col4 = [];
            var col5 = [];
            var col6 = [];
            var col7 = [];
            var col8 = [];

            var totalAlto = 0;
            var totalMedio = 0;
            var totalBajo = 0;
            var totalSinIngreso = 0;
            var totalcritico = 0;
            var totalInactivos = 0;

            data.forEach(function (obj) {
                var numero = obj.Proyeccion;

                col1.push(obj.Id_Banner);
                col2.push(obj.Programa);
                col3.push(obj.Nota1);
                col4.push(obj.Nota2);
                col5.push(obj.Nota3);
                col6.push(obj.NotaAcum);
                col7.push(obj.Proyeccion);
                col8.push(obj.Riesgo);
                if (obj.Riesgo == "alto") {
                    totalAlto += 1;
                }
                if (obj.Riesgo == "medio") {
                    totalMedio += 1;
                }
                if (obj.Riesgo == "bajo") {
                    totalBajo += 1;
                }
                if (obj.Riesgo == "Sin ingreso a plataforma") {
                    totalSinIngreso += 1;
                }
                if (obj.Riesgo == "Inactivo") {
                    totalInactivos += 1;
                }
                if (obj.Riesgo == "critico") {
                    totalcritico += 1;
                }
            });

            var riesgos = [
                "Total riesgos - Critico: "+ totalcritico +" Alto: " +
                    totalAlto +
                    " Medio: " +
                    totalMedio +
                    " Bajo: " +
                    totalBajo +
                    " Sin ingreso a plataforma: " +
                    totalSinIngreso + " Inactivos: " + totalInactivos,
            ];

            var titleRow = [];
            var titleRow2 = [];
            for (var i = 0; i < 8; i++) {
                titleRow.push("");
                titleRow2.push("");
            }
            titleRow[0] = titulo[0];
            titleRow2[0] = riesgos[0];

            newData.push(titleRow);
            newData.push(titleRow2);
            newData.push(headers);

            for (var i = 0; i < col1.length; i++) {
                var row = [
                    col1[i],
                    col2[i],
                    col3[i],
                    col4[i],
                    col5[i],
                    col6[i],
                    col7[i],
                    col8[i],
                ];
                newData.push(row);
            }

            var wb = XLSX.utils.book_new();
            var ws = XLSX.utils.aoa_to_sheet(newData);
            ws["!merges"] = [
                { s: { r: 0, c: 0 }, e: { r: 0, c: 7 } },
                { s: { r: 1, c: 0 }, e: { r: 1, c: 7 } },
            ];

            XLSX.utils.book_append_sheet(wb, ws, "Informe Curso");

            // Generar el archivo Excel y descargarlo
            XLSX.writeFile(wb, "Informe Curso.xlsx");

            Swal.close();
        },
    });
}

$(".botonBuscador").on("click", function (e) {
    e.preventDefault();
    var inputValue = $(this).data("id");

    inputValue = $(document)
        .find("#" + inputValue)
        .val();

    limpiarModal();
    dataAlumno(inputValue)
        .then(function (data) {
            $("#modaldataEstudiante").modal("show");
        })
        .catch(function (error) {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: error,
            });
        });
});

$("#botonHistorial").on("click", function () {
    var url =
        "https://moocs.ibero.edu.co/moocs/historialestudiantes/historial.php?var_idbanner=" +
        idHistorial;
    $("#iFrameHistorial").attr("src", url);
});

$(document).on("click", ".modal_hist_estudiante", function () {
    $("#modalHistorial").modal("hide");
});

$(document).on("click", ".modal_dta_estudiante", function () {
    $("#modaldataEstudiante").modal("hide");
});

var chartDatosMoodle;

var riesgoDatos;

$(".botonInfoMoodle").click(function () {
    $("#opcionInfo").val("Sello");
    $("#opcionInfo").change();
    riesgoDatos = $(this).data("value");
    destruirChart();
    let clases = $(this).attr("class");

    let tipo = clases.includes('filtro-moocs') ? 'MOOCS' : ' ';

    graficoModalSello(tipo);
});

function destruirChart() {
    if (chartDatosMoodle !== undefined || chartDatosMoodle) {
        chartDatosMoodle.destroy();
    }
}

/**
 * Validar caso MOOCS
 */

function graficoModalSello(tipo) {
    let urls = {
        cursos: vistaEstudiantes == 0 ? '../home/sellocursos' : '../home/sellocursosestudiantes',
        programas: vistaEstudiantes == 0 ? '../prueba/Moodle/sellomoodle' : '../prueba/Moodle/sellomoodleestudiantes',
    };
    
    let { data, url } = validarFiltros(filtro, urls, tipo);
    data.riesgo = riesgoDatos;

    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "post",
        url: url,
        data: data,
        success: function (data) {
            try {
                data = jQuery.parseJSON(data);
            } catch {
                data = data;
            }
            var labels = [];
            var valores = [];

            for (const key in data) {
                if (Object.prototype.hasOwnProperty.call(data, key)) {
                    labels.push(key);
                    valores.push(data[key]);
                }
            }

            var maxValor = Math.max(...valores);
            var maxValorAux = Math.ceil(maxValor / 1000) * 1000;
            var yMax;
            if (maxValor < 50) {
                yMax = 100;
            } else if (maxValor < 100) {
                yMax = 120;
            } else if (maxValor < 500) {
                yMax = 100 * Math.ceil(maxValor / 100) + 100;
            } else if (maxValor < 1000) {
                yMax = 100 * Math.ceil(maxValor / 100) + 200;
            } else {
                var maxValorAux = 1000 * Math.ceil(maxValor / 1000);
                yMax =
                    maxValorAux - maxValor < 600
                        ? maxValorAux + 1000
                        : maxValorAux;
            }
            // Crear el gráfico de barras
            var ctx = document.getElementById("datosMoodle").getContext("2d");
            chartDatosMoodle = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: "Sello Financiero",
                            data: valores,
                            backgroundColor: [
                                "rgba(74, 72, 72, 1)",
                                "rgba(223, 193, 78, 1)",
                                "rgba(208,171,75, 1)",
                                "rgba(186,186,186,1)",
                                "rgba(56,101,120,1)",
                                "rgba(229,137,7,1)",
                            ],
                            datalabels: {
                                anchor: "end",
                                align: "top",
                            },
                        },
                    ],
                },
                options: {
                    scales: {
                        y: {
                            max: yMax,
                            beginAtZero: true,
                        },
                    },
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        datalabels: {
                            color: "black",
                            font: {
                                weight: "semibold",
                            },
                            formatter: Math.round,
                        },
                        legend: {
                            display: false,
                        },
                    },
                },
                plugins: [ChartDataLabels],
            });
        },
    });
}

function graficoModalOperador() {
    
    let urls = {
        cursos: vistaEstudiantes == 0 ? '../home/operadorescursosmoodle' : '../home/operadorescursosmoodleestudiantes',
        programas: vistaEstudiantes == 0 ? '../prueba/Moodle/operadoresmoodle' : '../prueba/Moodle/operadoresmoodleestudiantes',
    };
    
    let { data, url } = validarFiltros(filtro, urls);
    data.riesgo = riesgoDatos;

    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "post",
        url: url,
        data: data,
        success: function (data) {
            try {
                data = jQuery.parseJSON(data);
            } catch {
                data = data;
            }

            var labels = [];
            var valores = [];

            data.forEach(function (elemento) {
                if (
                    elemento.operador !== null &&
                    elemento.TOTAL !== null &&
                    elemento.TOTAL !== undefined &&
                    elemento.TOTAL !== 0
                ) {
                    labels.push(elemento.Operador);
                    valores.push(elemento.TOTAL);
                }
            });
            var maxValor = Math.max(...valores);
            var maxValorAux = Math.ceil(maxValor / 1000) * 1000;
            var yMax;
            if (maxValor < 50) {
                yMax = 100;
            } else if (maxValor < 100) {
                yMax = 120;
            } else if (maxValor < 500) {
                yMax = 100 * Math.ceil(maxValor / 100) + 100;
            } else if (maxValor < 1000) {
                yMax = 100 * Math.ceil(maxValor / 100) + 200;
            } else {
                var maxValorAux = 1000 * Math.ceil(maxValor / 1000);
                yMax =
                    maxValorAux - maxValor < 600
                        ? maxValorAux + 1000
                        : maxValorAux;
            }
            // Crear el gráfico de barras
            var ctx = document.getElementById("datosMoodle").getContext("2d");
            chartDatosMoodle = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels.map(function (label, index) {
                        if (label == "") {
                            label = "IBERO";
                        }
                        return label;
                    }),
                    datasets: [
                        {
                            label: "Operadores con mayor cantidad de estudiantes",
                            data: valores,
                            backgroundColor: [
                                "rgba(74, 72, 72, 1)",
                                "rgba(223, 193, 78, 1)",
                                "rgba(208,171,75, 1)",
                                "rgba(186,186,186,1)",
                                "rgba(56,101,120,1)",
                                "rgba(229,137,7,1)",
                            ],
                            datalabels: {
                                anchor: "end",
                                align: "top",
                            },
                        },
                    ],
                },
                options: {
                    scales: {
                        y: {
                            max: yMax,
                            beginAtZero: true,
                        },
                    },
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        datalabels: {
                            color: "black",
                            font: {
                                weight: "semibold",
                            },
                            formatter: Math.round,
                        },
                        legend: {
                            display: false,
                        },
                    },
                },
                plugins: [ChartDataLabels],
            });
        },
    });
}

function graficoModalTipoEstudiantes() {
    let urls = {
        cursos: vistaEstudiantes == 0 ? '../home/tipoestudiantescursos' : '../home/tipoestudiantecursosestudiantes',
        programas: vistaEstudiantes == 0 ? '../prueba/Moodle/tiposestudiantesmoodle' : '../prueba/Moodle/tiposestudiantesmoodleestudiantes',
    };
    
    let { data, url } = validarFiltros(filtro, urls);
    data.riesgo = riesgoDatos;

    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "post",
        url: url,
        data: data,
        success: function (data) {
            try {
                data = jQuery.parseJSON(data);
            } catch {
                data = data;
            }
            var labels = data.map(function (elemento) {
                return elemento.Tipo_Estudiante;
            });
            var valores = data.map(function (elemento) {
                return elemento.TOTAL;
            });
            var maxValor = Math.max(...valores);
            var maxValorAux = Math.ceil(maxValor / 1000) * 1000;
            var yMax;

            if (maxValor < 50) {
                yMax = 100;
            } else if (maxValor < 100) {
                yMax = 120;
            } else if (maxValor < 500) {
                yMax = 100 * Math.ceil(maxValor / 100) + 100;
            } else if (maxValor < 1000) {
                yMax = 100 * Math.ceil(maxValor / 100) + 200;
            } else {
                var maxValorAux = 1000 * Math.ceil(maxValor / 1000);
                yMax =
                    maxValorAux - maxValor < 600
                        ? maxValorAux + 1000
                        : maxValorAux;
            }
            // Crear el gráfico circular
            var ctx = document.getElementById("datosMoodle").getContext("2d");
            chartDatosMoodle = new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labels.map(function (label, index) {
                        if (label.includes("ESTUDIANTE ")) {
                            label = label.replace(/ESTUDIANTE\S*/i, "");
                        }
                        return label;
                    }),
                    datasets: [
                        {
                            label: "",
                            data: valores,
                            backgroundColor: [
                                "rgba(74, 72, 72, 1)",
                                "rgba(223, 193, 78, 1)",
                                "rgba(208,171,75, 1)",
                                "rgba(186,186,186,1)",
                                "rgba(56,101,120,1)",
                                "rgba(229,137,7,1)",
                            ],
                            datalabels: {
                                anchor: "end",
                                align: "top",
                            },
                        },
                    ],
                },
                options: {
                    scales: {
                        y: {
                            max: yMax,
                            beginAtZero: true,
                        },
                    },
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        datalabels: {
                            color: "black",
                            font: {
                                weight: "semibold",
                            },
                            formatter: Math.round,
                        },
                        legend: {
                            display: false,
                        },
                    },
                },
                plugins: [ChartDataLabels],
            });
        },
    });
}

$("#opcionInfo").change(function () {
    destruirChart();
    var selectedValue = $(this).val();

    $("#tituloModalInfo strong").empty();
    if (selectedValue == "Operador") {
        $("#tituloModalInfo strong").append("Operadores");
        graficoModalOperador();
    }
    if (selectedValue == "Sello") {
        $("#tituloModalInfo strong").append("Sello Financiero");
        graficoModalSello();
    }
    if (selectedValue == "Tipo") {
        $("#tituloModalInfo strong").append("Tipo de estudiantes");
        graficoModalTipoEstudiantes();
    }
});

let chartRiesgoAltoEstudiantes, chartRiesgoMedioEstudiantes, chartRiesgoBajoEstudiantes, chartRiesgoIngresoEstudiantes, chartRiesgoCriticoEstudiantes, chartRiesgoInactivosEstudiantes;

function riesgoEstudiantes() {
    if (
        chartRiesgoAltoEstudiantes &&
        chartRiesgoMedioEstudiantes &&
        chartRiesgoBajoEstudiantes &&
        chartRiesgoIngresoEstudiantes &&
        chartRiesgoCriticoEstudiantes &&
        chartRiesgoInactivosEstudiantes
    ) {
        [
            chartRiesgoAltoEstudiantes,
            chartRiesgoMedioEstudiantes,
            chartRiesgoBajoEstudiantes,
            chartRiesgoIngresoEstudiantes,
            chartRiesgoCriticoEstudiantes,
            chartRiesgoInactivosEstudiantes
        ].forEach((chart) => chart.destroy());
    }

    let urls = {
        cursos: '../home/Moodle/riesgoacademicoestudiantes',
        programas: '../prueba/Moodle/riesgoEstudiantes',
    };
    
    let { data, url } = validarFiltros(filtro, urls);

    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "post",
        url: url,
        data: data,
        success: function (data) {
            try {
                data = parseJSON(data);
            } catch {
                data = data;
            }
            if (data) {
                $(".totalEstudiantes").empty();
                var totalesPorRiesgo = [];
                var totalEstudiantes = 0;
            
                $.each(data, function (index, elemento) {
                    var riesgo = elemento.riesgo;
                    var total = parseFloat(elemento.TOTAL);
                    console.log(total);
                    totalesPorRiesgo[riesgo] = total;
                    totalEstudiantes += total;
                });

                console.log(totalEstudiantes);

                let datoIngreso = 0, datoCritico = 0, datoAlto = 0, datoMedio = 0, datoBajo = 0;

                datoIngreso = totalEstudiantes - totalesPorRiesgo["Sin ingreso"];
                datoCritico = totalEstudiantes - totalesPorRiesgo["critico"];
                datoAlto = totalEstudiantes - totalesPorRiesgo["alto"];
                datoMedio = totalEstudiantes - totalesPorRiesgo["medio"];
                datoBajo = totalEstudiantes - totalesPorRiesgo["bajo"];
                dataInactivos = totalEstudiantes - totalesPorRiesgo["Inactivo"];

                $(".totalEstudiantes").text(totalEstudiantes);

                var ctx = document.getElementById("altoEstudiantes").getContext("2d");
                chartRiesgoAltoEstudiantes = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [totalesPorRiesgo["alto"],datoAlto],
                                backgroundColor: [
                                    "rgb(255, 128, 0)",
                                    "rgba(181, 178, 178, 0.5)",
                                ],
                                borderWidth: 1,
                                cutout: "70%",
                                circumference: 180,
                                rotation: 270,
                            },
                        ],
                    },

                    options: {
                        responsive: true,
                        cutoutPercentage: 50,
                        plugins: {
                            datalabels: {
                                color: "transparent",
                                weight: "semibold",
                                size: 16,
                            },
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: totalesPorRiesgo["alto"] + " Estudiantes - " +((totalesPorRiesgo["alto"] / totalEstudiantes) * 100).toFixed(2) + "%",
                                color: "orange",
                                position: "bottom",
                                font: {
                                    size: 14,
                                },
                                fullSize: false,
                            },
                            tooltip: {
                                enabled: false,
                            },
                        },
                    },
                    plugins: [ChartDataLabels],
                });

                ctx = document.getElementById("sinIngresoEstudiantes").getContext("2d");
                chartRiesgoIngresoEstudiantes = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [totalesPorRiesgo["Sin ingreso"], datoIngreso],
                                backgroundColor: [
                                    "rgba(161, 130, 98, 0.5)",
                                    "rgba(181, 178, 178, 0.5)",
                                ],
                                borderWidth: 1,
                                cutout: "70%",
                                circumference: 180,
                                rotation: 270,
                            },
                        ],
                    },

                    options: {
                        responsive: true,
                        cutoutPercentage: 50,
                        plugins: {
                            datalabels: {
                                color: "transparent",
                                weight: "semibold",
                                size: 16,
                            },
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: totalesPorRiesgo["Sin ingreso"] + " Estudiantes - " +((totalesPorRiesgo["Sin ingreso"] / totalEstudiantes) * 100).toFixed(2) + "%",
                                color: "brown",
                                position: "bottom",
                                font: {
                                    size: 14,
                                },
                                fullSize: false,
                            },
                            tooltip: {
                                enabled: false,
                            },
                        },
                    },
                    plugins: [ChartDataLabels],
                });

                ctx = document.getElementById("medioEstudiantes").getContext("2d");
                chartRiesgoMedioEstudiantes = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [totalesPorRiesgo["medio"], datoMedio], // Aquí puedes ajustar el valor para representar la semicircunferencia deseada
                                backgroundColor: [
                                    "rgba(223, 193, 78, 1)",
                                    "rgba(181, 178, 178, 0.5)",
                                ], // Color de fondo para la semicircunferencia
                                borderWidth: 1,
                                cutout: "70%",
                                circumference: 180,
                                rotation: 270,
                            },
                        ],
                    },

                    options: {
                        responsive: true,
                        cutoutPercentage: 50,
                        plugins: {
                            datalabels: {
                                color: "transparent",
                                weight: "semibold",
                                size: 16,
                            },
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: totalesPorRiesgo["medio"] + " Estudiantes - " +((totalesPorRiesgo["medio"] / totalEstudiantes) * 100).toFixed(2) + "%",
                                color: "rgba(121, 85, 61, 1)",
                                position: "bottom",
                                font: {
                                    size: 14,
                                },
                            },
                            tooltip: {
                                enabled: false,
                            },
                        },
                    },
                    plugins: [ChartDataLabels],
                });

                ctx = document.getElementById("bajoEstudiantes").getContext("2d");
                chartRiesgoBajoEstudiantes = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [totalesPorRiesgo["bajo"], datoBajo], // Aquí puedes ajustar el valor para representar la semicircunferencia deseada
                                backgroundColor: [
                                    "rgba(0, 255, 0, 1)",
                                    "rgba(181, 178, 178, 0.5)",
                                ], // Color de fondo para la semicircunferencia
                                borderWidth: 1,
                                cutout: "70%",
                                circumference: 180,
                                rotation: 270,
                            },
                        ],
                    },

                    options: {
                        responsive: true,
                        cutoutPercentage: 50,
                        plugins: {
                            datalabels: {
                                color: "transparent",
                                weight: "semibold",
                                size: 16,
                            },
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: totalesPorRiesgo["bajo"] + " Estudiantes - " +((totalesPorRiesgo["bajo"] / totalEstudiantes) * 100).toFixed(2) + "%",
                                color: "Green",
                                position: "bottom",
                                font: {
                                    size: 14,
                                },
                            },
                            tooltip: {
                                enabled: false,
                            },
                        },
                    },
                    plugins: [ChartDataLabels],
                });

                ctx = document.getElementById("criticoEstudiantes").getContext("2d");
                chartRiesgoCriticoEstudiantes = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [totalesPorRiesgo["critico"], datoCritico], // Aquí puedes ajustar el valor para representar la semicircunferencia deseada
                                backgroundColor: [
                                    "rgba(255, 0, 0, 1)",
                                    "rgba(181, 178, 178, 0.5)",
                                ], // Color de fondo para la semicircunferencia
                                borderWidth: 1,
                                cutout: "70%",
                                circumference: 180,
                                rotation: 270,
                            },
                        ],
                    },

                    options: {
                        responsive: true,
                        cutoutPercentage: 50,
                        plugins: {
                            datalabels: {
                                color: "transparent",
                                weight: "semibold",
                                size: 16,
                            },
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: totalesPorRiesgo["critico"] + " Estudiantes - " +((totalesPorRiesgo["critico"] / totalEstudiantes) * 100).toFixed(2) + "%",
                                color: "Red",
                                position: "bottom",
                                font: {
                                    size: 14,
                                },
                            },
                            tooltip: {
                                enabled: false,
                            },
                        },
                    },
                    plugins: [ChartDataLabels],
                });

                ctx = document.getElementById("inactivoEstudiantes").getContext("2d");
                chartRiesgoInactivosEstudiantes = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [totalesPorRiesgo["Inactivo"], dataInactivos], // Aquí puedes ajustar el valor para representar la semicircunferencia deseada
                                backgroundColor: [
                                    "rgb(155, 155, 155, 1)",
                                    "rgba(181, 178, 178, 0.5)",
                                ], // Color de fondo para la semicircunferencia
                                borderWidth: 1,
                                cutout: "70%",
                                circumference: 180,
                                rotation: 270,
                            },
                        ],
                    },

                    options: {
                        responsive: true,
                        cutoutPercentage: 50,
                        plugins: {
                            datalabels: {
                                color: "transparent",
                                weight: "semibold",
                                size: 16,
                            },
                            legend: {
                                display: false,
                            },
                            title: {
                                display: true,
                                text: totalesPorRiesgo["Inactivo"] + " Estudiantes - " +((totalesPorRiesgo["Inactivo"] / totalEstudiantes) * 100).toFixed(2) + "%",
                                color: "Grey",
                                position: "bottom",
                                font: {
                                    size: 14,
                                },
                            },
                            tooltip: {
                                enabled: false,
                            },
                        },
                    },
                    plugins: [ChartDataLabels],
                });

            } else {
            }
        },
    });
}

$(".botonVerMasEstudiantes").on("click", function () {
    Swal.fire({
        imageUrl:
            "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
    });
    var riesgo = $(this).data("value");
    dataTableEstudiantes(riesgo);
});

function dataTableEstudiantes(riesgo) {
    destruirTablaEstudiantes();
    $("#colTablaEstudiantes").removeClass("hidden");

    let urls = {
        cursos: `../prueba/moodle/tablariesgoacademicoestudiantes/${riesgo}`,
        programas: `../prueba/moodle/tablaRiesgoEstudiantes/${riesgo}`,
    };
    
    let { data, url } = validarFiltros(filtro, urls);

    var datos = $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        type: "post",
        url: url,
        data: data,
        success: function (data) {

            try {
                data = parseJSON(data);
            } catch {
                data = data;
            }
            data.forEach(function (estudiante) {
                console.log(estudiante);
                if (estudiante.nota !== "Sin actividad") {
                    console.log(estudiante.nota);
                    estudiante.nota = parseFloat(estudiante.nota).toFixed(3);
                }
            });

            if (data.nota !== "Sin actividad") {
                data.nota = parseFloat(data.nota).toFixed(2);
            }

            tableEstudiantes = $("#datatableEstudiantes").DataTable({
                data: data,
                pageLength: 10,
                dom: "Bfrtip",
                buttons: ["excel"],
                columns: [
                    {
                        data: "id_banner",
                        title: "Id Banner",
                    },
                    {
                        data: "nombre",
                        title: "Nombre",
                    },
                    {
                        data: "programa",
                        title: "Programa",
                    },
                    {
                        data: "periodo",
                        title: "Periodo",
                        className: "text-center",
                    },
                    {
                        data: "riesgo",
                        title: "Riesgo",
                        className: "text-center",
                    },
                    {
                        data: "alto",
                        title: "Materias en riesgo alto",
                        className: "text-center",
                    },
                    {
                        data: "medio",
                        title: "Materias en riesgo medio",
                        className: "text-center",
                    },
                    {
                        data: "bajo",
                        title: "Materias en riesgo bajo",
                        className: "text-center",
                    },
                    {
                        data: "sin_ingreso",
                        title: "Materias sin ingreso",
                        className: "text-center",
                    },
                    {
                        data: "total_materias",
                        title: "Total materias activas",
                        className: "text-center",
                    },
                    {
                        data: "nota",
                        title: "Promedio materias activas",
                        className: "text-center",
                    },
                  
                    {
                        defaultContent:
                            "<button type='button' id='btn-table' class='data btn btn-warning' data-toggle='modal' data-target='#modaldataEstudiante'><i class='fa-solid fa-user'></i></button>",
                        title: "Datos Estudiante",
                        className: "text-center",
                    },
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json",
                },
            });
            function obtenerData(tbody, tableEstudiantes) {
                $(tbody).on("click", "button.data", function () {
                    var datos = tableEstudiantes
                        .row($(this).parents("tr"))
                        .data();
                    idHistorial = datos.id_banner;
                    dataAlumno(idHistorial);
                });
            }
            obtenerData("#datatableEstudiantes tbody", tableEstudiantes);
            $(document).find("#datatableEstudiantes").removeClass("dataTable");
            Swal.close();
        },
    });
}

function destruirTablaEstudiantes() {
    $("#colTablaEstudiantes").addClass("hidden");
    if ($.fn.DataTable.isDataTable("#datatableEstudiantes")) {
        // $("#tituloTable").remove();
        tableEstudiantes.destroy();
        $("#datatableEstudiantes").DataTable().destroy();
        $("#datatableEstudiantes thead").empty();
        $("#datatableEstudiantes tbody").empty();
        $("#datatableEstudiantes tfooter").empty();
        $("#datatableEstudiantes tbody").off("click", "button.data");
    }
}
