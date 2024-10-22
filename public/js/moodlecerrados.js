
var table;
contador_tabla_cursos = 0;
contador_riesgo_estudiantes = 0;
vistaEstudiantes = 0;
// Agregar clase activo al menú
$(document).find("#MoodleCerrado").addClass("activo");

$(".content").hide();
$("#ausentismo").show();


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

// Contador de programas, facultades y periodos
function Contador() {
    totalFacultades = $('#facultades input[type="checkbox"]').length;
    totalProgramas = $('#programas input[type="checkbox"]').length;
    totalPeriodos = $(
        '#Continua, #Pregrado, #Esp, #Maestria input[type="checkbox"]'
    ).length;
}

var chartRiesgoAlto;
var chartRiesgoMedio;
var chartRiesgoBajo;
var chartRiesgoIngreso;
var chartRiesgoInactivos;

//setTimeout(alertainfo2024Moodle, 2000)


function riesgo(filtro) {

    destruirTabla();

    if (
        chartRiesgoAlto &&
        chartRiesgoInactivos &&
        chartRiesgoMedio &&
        chartRiesgoBajo 
    ) {
        [
            chartRiesgoAlto,
            chartRiesgoInactivos,
            chartRiesgoMedio,
            chartRiesgoBajo,
          
        ].forEach((chart) => chart.destroy());
    }
   
    if (filtro.cursos && filtro.cursos.length > 0) {
        data = {
            idcurso: filtro.cursos,
            periodos: filtro.periodos,
        };
        url = "../cierre/matriculas";
    } else if (filtro.programa && filtro.programa.length > 0) {
        (data = {
            programa: filtro.programa,
            periodos: filtro.periodos,
        }),
            (url = "../cierre/matriculas");
    } else if (filtro.facultades && filtro.facultades.length > 0) {
        (data = {
            idfacultad: filtro.facultades,
            periodos: filtro.periodos,
        }),
            (url = "../cierre/matriculas");
    }

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
         
                var Perdidacritica = data.total - data.Perdidacritica;
                var sinActividad = data.total -data.sinActividad;
                var Perdida = data.total - data.Perdida;
                var Aprobado = data.total - data.Aprobado;
                

                if (Perdidacritica <= 0) {
                    Perdidacritica = 0;
                }
                if (sinActividad <= 0) {
                    sinActividad = 0;
                }
                if (Perdida <= 0) {
                    Perdida = 0;
                }
                if (Aprobado <= 0) {
                    Aprobado = 0;
                }
               
                // perdidacritica
                var ctx = document.getElementById("alto").getContext("2d");
                chartRiesgoAlto = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {  
                                data: [data.Perdidacritica, Perdidacritica],
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
                                display: true,
                            },
                            title: {
                                display: true,
                                text: data.Perdidacritica + " Matrículas - " +((data.Perdidacritica / data.total) * 100).toFixed(2) + "%",
                                color: "red",
                                position: "bottom",
                                font: {
                                    size: 14,
                                },
                                fullSize: true,
                            },
                            tooltip: {
                                enabled: true,
                            },
                        },
                    },
                    plugins: [ChartDataLabels],
                });

                // sinActividad
                ctx = document.getElementById("Inactivos").getContext("2d");
                chartRiesgoInactivos = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [data.sinActividad, sinActividad], // Aquí puedes ajustar el valor para representar la semicircunferencia deseada
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
                                text: data.sinActividad + " Matrículas - " +((data.sinActividad / data.total) * 100).toFixed(2) + "%",
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

             
                // Perdida
                ctx = document.getElementById("medio").getContext("2d");
                chartRiesgoMedio = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [data.Perdida, Perdida], // Aquí puedes ajustar el valor para representar la semicircunferencia deseada
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
                                text: data.Perdida + " Matrículas - " +((data.Perdida / data.total) * 100).toFixed(2) + "%",
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

                // Aprobado
                ctx = document.getElementById("bajo").getContext("2d");
                chartRiesgoBajo = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [data.Aprobado, Aprobado], // Aquí puedes ajustar el valor para representar la semicircunferencia deseada
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
                                text: data.Aprobado + " Matrículas - " +((data.Aprobado / data.total) * 100).toFixed(2) + "%",
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

$(".botonVerMas").on("click", function () {
    Swal.fire({
        imageUrl:
            "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
    });
    var riesgo = $(this).data("value");
    dataTable(riesgo);
});


var chartRiesgoaproboEstudiantes;
var chartRiesgoperdioEstudiantes;
var chartRiesgoperdioCriticoEstudiantes;
var chartRiesgosinactividadEstudiantes;

function riesgoEstudiantes() {
    if (

        chartRiesgoaproboEstudiantes &&
        chartRiesgoperdioEstudiantes &&
        chartRiesgoperdioCriticoEstudiantes &&
        chartRiesgosinactividadEstudiantes
    ) {
        [
   
            chartRiesgoaproboEstudiantes,
            chartRiesgoperdioEstudiantes,
            chartRiesgoperdioCriticoEstudiantes,
            chartRiesgosinactividadEstudiantes

        ].forEach((chart) => chart.destroy());
    }

    var data;
    if (filtro.cursos && filtro.cursos.length > 0) {
        (data = {
            idcurso: filtro.cursos,
            periodos: filtro.periodos,
        }),
            url = "../home/Moodle/riesgoacademicoestudiantes";
    } else if (filtro.programa && filtro.programa.length > 0) {
        (data = {
            programa: filtro.programa,
            periodos: filtro.periodos,
        }),
     
            url = "../Moodle/riesgoEstudiantescerrado";
    } else if (filtro.facultades && filtro.facultades.length > 0) {
        (data = {
            idfacultad: filtro.facultades,
            periodos: filtro.periodos,
        }),
            url = "../prueba/Moodle/tablaCursos";
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
            if (data) {
                $(".totalEstudiantes").empty();
           
                var totalesPorRiesgo = [];
                var totalEstudiantes = 0;
            
 

                $.each(data, function (index, elemento) {
                    var riesgo = elemento.riesgo;
                    var total = parseFloat(elemento.TOTAL);
     
                    totalesPorRiesgo[riesgo] = total;
                    totalEstudiantes += total;
                });
                       
                datoAprobo = totalEstudiantes - totalesPorRiesgo["Aprobado"];
                datoPerdio = totalEstudiantes - totalesPorRiesgo["Perdida"];
                datoperdioCritico = totalEstudiantes - totalesPorRiesgo["Perdida crítica"];
                datasinactividades = totalEstudiantes - totalesPorRiesgo["Sin Actividad"];
            

                $(".totalEstudiantes").text(totalEstudiantes);

                /** grafico aprobo */
                ctx = document.getElementById("Aprobo").getContext("2d");
                chartRiesgoaproboEstudiantes = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [totalesPorRiesgo["Aprobado"],  datoAprobo], // Aquí puedes ajustar el valor para representar la semicircunferencia deseada
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
                                text: totalesPorRiesgo["Aprobado"] + " Estudiantes - " +((totalesPorRiesgo["Aprobado"] / totalEstudiantes) * 100).toFixed(2) + "%",
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
                /** grafico perdida */
                ctx = document.getElementById("perdida").getContext("2d");
                chartRiesgoperdioEstudiantes = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [totalesPorRiesgo["Perdida"], datoPerdio],
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
                                text: totalesPorRiesgo["Perdida"] + " Estudiantes - " +((totalesPorRiesgo["Perdida"] / totalEstudiantes) * 100).toFixed(2) + "%",
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

                /** grafico perdida critica */
                ctx = document.getElementById("criticoEstudiantes").getContext("2d");
                chartRiesgoperdioCriticoEstudiantes = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [totalesPorRiesgo["Perdida crítica"], datoperdioCritico], // Aquí puedes ajustar el valor para representar la semicircunferencia deseada
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
                                text: totalesPorRiesgo["Perdida crítica"] + " Estudiantes - " +((totalesPorRiesgo["Perdida crítica"] / totalEstudiantes) * 100).toFixed(2) + "%",
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

                /** grafico sinIngreso */
                var ctx = document.getElementById("sinIngreso").getContext("2d");
                chartRiesgosinactividadEstudiantes = new Chart(ctx, {
                    type: "doughnut",
                    data: {
                        datasets: [
                            {
                                data: [totalesPorRiesgo["Sin Actividad"],datasinactividades],
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
                                text: totalesPorRiesgo["Sin Actividad"] + " Estudiantes - " +((totalesPorRiesgo["Sin Actividad"] / totalEstudiantes) * 100).toFixed(2) + "%",
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



            } else {
            }
        },
    });
}

var idHistorial = "";

function dataTable(riesgo) {
    destruirTabla();
    $("#colTabla").removeClass("hidden");

    var data;
   
    if (filtro.cursos && filtro.cursos.length > 0) {
        (data = {
            idcurso: filtro.cursos,
            periodos: filtro.periodos,
        }),
       // alert("1")
            (url = "../home/Moodlecerrado/estudiantes/" + riesgo);
    } else if (filtro.programa && filtro.programa.length > 0) {
        (data = {
            programa: filtro.programa,
            periodos: filtro.periodos,
        }),
       // alert("2")
            (url = "../home/Moodlecerrado/estudiantes/" + riesgo);
    } else if (filtro.facultades && filtro.facultades.length > 0) {
        data = {
            idfacultad: filtro.facultades,
            periodos: filtro.periodos,
        };
        //alert("3")
        url = "../home/Moodlecerrado/estudiantescursos/" + riesgo;
    }

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

            table = $("#datatable").DataTable({
                data: datos,
                pageLength: 10,
                dom: "Bfrtip",
                buttons: [
                    {
                        extend: "excel",
                        
                    },
                
                ],
                columns: [
                   
                    {
                        data: "Cadena",
                        title: "Cadena",
                        visible:false,
                    },     
                    {
                        data: "Periodo",
                        title: "Periodo",
                        visible:false,
                    },     
                    {
                        data: "IdCurso",
                        title: "IdCurso",
                        visible:false,
                    },     
                    {
                        data: "ID_BANNER",
                        title: "Id Banner",
                    },
                    {
                        data: null, // Utiliza 'null' porque no hay un solo campo que mapee a esta columna
                        title: "Nombre Completo",
                        render: function(data, type, row) {
                            return data.Nombre + ' ' + data.Apellido; // Concatenar los campos Nombre y Apellido
                        },   
                        visible: true,
                    },                          
                    {
                        data: "Programa",
                        title: "cod programa",   
                        visible: true,
                    },
                    {
                        data: "NombreCurso",
                        title: "Nombre del curso",
                        visible: true,
                    },

                
                    {
                        data: "riesgo",
                        title: "Riesgo",
                        visible: true,
                    },
                    {
                        data: "Créditos",
                        title: "Créditos",
                        visible: true,
                    },

                    {
                        data: "Nota",
                        title: "Nota",
                        visible: true,
                    },
                    // {
                    //     defaultContent:
                    //         "<button type='button' id='btn-table' class='data btn btn-warning' data-toggle='modal' data-target='#modaldataEstudiante'><i class='fa-solid fa-user'></i></button>",
                    //     title: "Datos Estudiante",
                    //     className: "text-center",
                    // },
                ],
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json",
                },
            });
            riesgoaux = riesgo.toLowerCase();
            var titulo =
                "Estudiantes con riesgo " + riesgoaux + " por Ausentismo";
            $(
                '<div id="tituloTable" class="dataTables_title text-center"> <h4>' +
                    titulo +
                    "</h4></div>"
            ).insertBefore("#datatable");

            function obtenerData(tbody, table) {
                $(tbody).on("click", "button.data", function () {
                    var datos = table.row($(this).parents("tr")).data();
                    idHistorial = datos.Id_Banner;
                    dataAlumno(datos.Id_Banner);
                });
            }
            obtenerData("#datatable tbody", table);
            Swal.close();
        },
    });
}

function destruirTabla() {
    $("#colTabla").addClass("hidden");
    if ($.fn.DataTable.isDataTable("#datatable")) {
        $("#tituloTable").remove();
        table.destroy();
        $("#datatable").DataTable().destroy();
        $("#datatable thead").empty();
        $("#datatable tbody").empty();
        $("#datatable tfooter").empty();
        $("#datatable tbody").off("click", "button.data");
    }
}

var chartRiesgoIngreso;
var chartRiesgoNotas;

/**
 * Función para llenar modal con la data del estudiante seleccionado
 */

function dataAlumno(id) {
    $(".multi-collapse").removeClass("collapse show");
    $(".multi-collapse").addClass("collapse");

    if (filtro.cursos && filtro.cursos.length > 0) {
        (data = {
            idcurso: filtro.cursos,
            idBanner: id,
        }),
            (url = "../home/Moodle/dataAlumnoCurso");
    } else if (filtro.programa && filtro.programa.length > 0) {
        (data = {
            idBanner: id,
        }),
            (url = "../prueba/Moodle/datosEstudiante");
    } else if (filtro.facultades && filtro.facultades.length > 0) {
        data = {
            idBanner: id,
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

$("#navestudiantes").click(function () {
    vistaEstudiantes = 1;
    if (contador_riesgo_estudiantes == 0) {
        contador_riesgo_estudiantes++;

        riesgoEstudiantes();
    }
});

$("#navausentismo").click(function () {
    vistaEstudiantes = 0;
});

$("#descargarTodo").click(function () {
    var url = "../prueba/moodle/descargardatos";
    Swal.fire({
        title: "Descargando...",
        text: "Descargando la información solicitada, este proceso puede tardar unos segundos dependiendo de tu conexión a internet.",
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
            (url = "../Moodle/descargarriesgocursos");
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
                "Cadena",
                "Grupo",
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
                "Username",
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
                    item.codigomateria,
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
                    item.Cadena,
                    item.Grupo,
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
                    item.Username,
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
    var url = "../prueba/moodle/descargardatosflash";
    Swal.fire({
        title: "Descargando...",
        text: "Descargando la información solicitada, este proceso puede tardar unos segundos dependiendo de tu conexión a internet.",
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
            (url = "../Moodle/descargarriesgocursosflash");
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
                (url = "../prueba/Moodle/tablaCursos/cerrados");
        } else if (filtro.facultades && filtro.facultades.length > 0) {
            (data = {
                idfacultad: filtro.facultades,
                periodos: filtro.periodos,
            }),
                (url = "../prueba/Moodle/tablaCursos/cerrados");
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
                tablacursosMoodle = $("#tablaCursos").DataTable({
                    dom: "Bfrtip",
                    data: data,
                    responsive: true,
                    buttons: [
                  
                        {
                            extend: "excel",
                            title: "Reporte cursos activos - Hermes",
                            exportOptions: {
                                columns: [
                                    0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 12, 11, 12, 13, 14, 15, 16
                                ],
                            },
                        },
                       
                     
                    ],
                    pageLength: 10,
                    fixedHeader: true,
                    columnDefs: [
                        {
                            width: "50px",
                            targets: 0
                        },
                        {
                            width: "150px",
                            targets: 1
                        },
                        {
                            width: "100px",
                            targets: 2
                        },
                        {
                            width: "100px",
                            targets: 3
                        },
                        {
                            width: "80px",
                            targets: 4
                        },
                        {
                            width: "120px",
                            targets: 5
                        },
                        {
                            width: "80px",
                            targets: 6
                        },
                        {
                            width: "50px",
                            targets: 7
                        },
                        {
                            width: "100px",
                            targets: 8
                        },
                        {
                            width: "100px",
                            targets: 9
                        },
                        {
                            width: "100px",
                            targets: 10
                        },
                        {
                            width: "100px",
                            targets: 11
                        },
                        {
                            width: "100px",
                            targets: 12
                        }
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
                            data: "sinIngreso",
                            title: "Sin Ingreso",
                            className: "text-center",
                        },
                        {
                            data: "critica",
                            title: "Perdida critica",
                            className: "text-center",
                        },
                        {
                            data: "Perdida",
                            title: "Perdida",
                            className: "text-center",
                        },
                        {
                            data: "Aprobado",
                            title: "Aprobado",
                            className: "text-center",
                        },
                       
                        // {
                        //     defaultContent:
                        //         "<button type='button' class='descargar btn btn-warning'><i class='fa-solid fa-download'></i></button>",
                        //     title: "Descargar datos",
                        //     className: "text-center",
                        // },
                        {
                            data: "sinIngreso",
                            title: "Sin ingreso",
                            className: "text-center",
                            render: function (data, type, row) {
                                var partes = data.split("<br>");
                                return partes[0] || "";
                            },
                            visible: false,
                        },
                        {
                            data: "critica",
                            title: "Estudiantes en riesgo critico",
                            className: "text-center",
                            render: function (data, type, row) {
                                var partes = data.split("<br>");
                                return partes[0] || "";
                            },
                            visible: false,
                        },
                        {
                            data: "Perdida",
                            title: "Estudiantes en riesgo alto",
                            className: "text-center",
                            render: function (data, type, row) {
                                var partes = data.split("<br>");
                                return partes[0] || "";
                            },
                            visible: false,
                        },
                        {
                            data: "Aprobado",
                            title: "Estudiantes en riesgo medio",
                            className: "text-center",
                            render: function (data, type, row) {
                                var partes = data.split("<br>");
                                return partes[0] || "";
                            },
                            visible: false,
                        },
                      
                    ],
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json",
                    },
                });
                
                
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

            var Perdidacritica = 0;
            var Perdida = 0;
            var Aprobado = 0;
            var totalSinIngreso = 0;
            var totalcritico = 0;
            var sinActividad = 0;

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
                    Perdidacritica += 1;
                }
                if (obj.Riesgo == "medio") {
                    Perdida += 1;
                }
                if (obj.Riesgo == "bajo") {
                    Aprobado += 1;
                }
                if (obj.Riesgo == "Sin ingreso a plataforma") {
                    totalSinIngreso += 1;
                }
                if (obj.Riesgo == "Inactivo") {
                    sinActividad += 1;
                }
                if (obj.Riesgo == "critico") {
                    totalcritico += 1;
                }
            });

            var riesgos = [
                "Total riesgos - Critico: "+ totalcritico +" Alto: " +
                    Perdidacritica +
                    " Medio: " +
                    Perdida +
                    " Bajo: " +
                    Aprobado +
                    " Sin ingreso a plataforma: " +
                    totalSinIngreso + " Inactivos: " + sinActividad,
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
    graficoModalSello();
});

function destruirChart() {
    if (chartDatosMoodle !== undefined || chartDatosMoodle) {
        chartDatosMoodle.destroy();
    }
}

function graficoModalSello() {
    if (vistaEstudiantes == 0) {
        if (filtro.cursos && filtro.cursos.length > 0) {
            (data = {
                idcurso: filtro.cursos,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../home/sellocursos");
        } else if (filtro.programa && filtro.programa.length > 0) {
            (data = {
                programa: filtro.programa,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../prueba/Moodle/sellomoodle");
        } else if (filtro.facultades && filtro.facultades.length > 0) {
            (data = {
                idfacultad: filtro.facultades,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../prueba/Moodle/sellomoodle");
        }
    } else {
        if (filtro.cursos && filtro.cursos.length > 0) {
            (data = {
                idcurso: filtro.cursos,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../home/sellocursosestudiantes");
        } else if (filtro.programa && filtro.programa.length > 0) {
            (data = {
                programa: filtro.programa,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../prueba/Moodle/sellomoodleestudiantes");
        } else if (filtro.facultades && filtro.facultades.length > 0) {
            (data = {
                idfacultad: filtro.facultades,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../prueba/Moodle/sellomoodleestudiantes");
        }
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
    var url, data;

    if (vistaEstudiantes == 0) {
        if (filtro.cursos && filtro.cursos.length > 0) {
            (data = {
                idcurso: filtro.cursos,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../home/operadorescursosmoodle");
        } else if (filtro.programa && filtro.programa.length > 0) {
            (data = {
                programa: filtro.programa,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../prueba/Moodle/operadoresmoodle");
        } else if (filtro.facultades && filtro.facultades.length > 0) {
            (data = {
                idfacultad: filtro.facultades,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../prueba/Moodle/operadoresmoodle");
        }
    } else {
        if (filtro.cursos && filtro.cursos.length > 0) {
            (data = {
                idcurso: filtro.cursos,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../home/operadorescursosmoodleestudiantes");
        } else if (filtro.programa && filtro.programa.length > 0) {
            (data = {
                programa: filtro.programa,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../prueba/Moodle/operadoresmoodleestudiantes");
        } else if (filtro.facultades && filtro.facultades.length > 0) {
            (data = {
                idfacultad: filtro.facultades,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../prueba/Moodle/operadoresmoodleestudiantes");
        }
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
    var url, data;
    if (vistaEstudiantes == 0) {
        if (filtro.cursos && filtro.cursos.length > 0) {
            (data = {
                idcurso: filtro.cursos,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../home/tipoestudiantescursos");
        } else if (filtro.programa && filtro.programa.length > 0) {
            (data = {
                programa: filtro.programa,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../prueba/Moodle/tiposestudiantesmoodle");
        } else if (filtro.facultades && filtro.facultades.length > 0) {
            (data = {
                idfacultad: filtro.facultades,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../prueba/Moodle/tiposestudiantesmoodle");
        }
    } else {
        if (filtro.cursos && filtro.cursos.length > 0) {
            (data = {
                idcurso: filtro.cursos,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../home/tipoestudiantecursosestudiantes");
        } else if (filtro.programa && filtro.programa.length > 0) {
            (data = {
                programa: filtro.programa,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../prueba/Moodle/tiposestudiantesmoodleestudiantes");
        } else if (filtro.facultades && filtro.facultades.length > 0) {
            (data = {
                idfacultad: filtro.facultades,
                periodos: filtro.periodos,
                riesgo: riesgoDatos,
            }),
                (url = "../prueba/Moodle/tiposestudiantesmoodleestudiantes");
        }
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

var chartRiesgoAltoEstudiantes;
var chartRiesgoMedioEstudiantes;
var chartRiesgoBajoEstudiantes;
var chartRiesgoIngresoEstudiantes;

var chartRiesgoInactivosEstudiantes;



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

    var data;
    if (filtro.cursos && filtro.cursos.length > 0) {
        (data = {
            idcurso: filtro.cursos,
            periodos: filtro.periodos,
        }),
            url ="../prueba/moodle/tablariesgoacademicoestudiantes/" + riesgo;
    } else if (filtro.programa && filtro.programa.length > 0) {
        (data = {
            programa: filtro.programa,
            periodos: filtro.periodos,
        }),
            (url = "../Estudiantescerrados/" + riesgo);

    } else if (filtro.facultades && filtro.facultades.length > 0) {
        data = {
            idfacultad: filtro.facultades,
            periodos: filtro.periodos,
        };
        url = "../prueba/moodle/tablaRiesgoEstudiantes/" + riesgo;
    }

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
                if (estudiante.nota !== "Sin actividad") {
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
                        data: "cod_programa",
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
                        data: "SinActividad",
                        title: "Materias sin actividad",
                        className: "text-center",
                    },
                    {
                        data: "Perdidacritica",
                        title: "Materias con perdida crítica",
                        className: "text-center",
                    },
                    {
                        data: "Perdida",
                        title: "Materias con perdidas",
                        className: "text-center",
                    },
                    {
                        data: "Aprobado",
                        title: "Materias aprobadas",
                        className: "text-center",
                    },
                    {
                        data: "total_materias",
                        title: "Total materias cerradas",
                        className: "text-center",
                    },
                    {
                        data: "nota",
                        title: "Promedio materias cerradas",
                        className: "text-center",
                    },
                  
                    // {
                    //     defaultContent:
                    //         "<button type='button' id='btn-table' class='data btn btn-warning' data-toggle='modal' data-target='#modaldataEstudiante'><i class='fa-solid fa-user'></i></button>",
                    //     title: "Datos Estudiante",
                    //     className: "text-center",
                    // },
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
