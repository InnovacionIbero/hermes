$(document).ready(function () {
    //--- definimos las variables
    var programasSeleccionados = [];
    var facultadesSeleccionadas = [];

    // Deshabilitar los checkboxes cuando comienza una solicitud AJAX
    $(document).ajaxStart(function () {
        $('div #facultades input[type="checkbox"]').prop("disabled", true);
        $('div #programas input[type="checkbox"]').prop("disabled", true);
        $(".todos").prop("disabled", true);
        $('.periodos input[type="checkbox"]').prop("disabled", true);
        $("#generarReporte").prop("disabled", true);
    });

    // Volver a habilitar los checkboxes cuando finaliza una solicitud AJAX
    $(document).ajaxStop(function () {
        $('div #facultades input[type="checkbox"]').prop("disabled", false);
        $('div #programas input[type="checkbox"]').prop("disabled", false);
        $('.periodos input[type="checkbox"]').prop("disabled", false);
        $(".todos").prop("disabled", false);

        $("#generarReporte").prop("disabled", false);
    });

    var buscador = $("#buscadorProgramas");
    var listaProgramas = $(".listaProgramas");
    var divProgramas = $("#programas");

    // Buscador de programas
    buscador.on("input", function () {
        console.log("entro");
        $('#programas input[type="checkbox"]').prop("checked", false);
        $("#todosPrograma").prop("checked", false);
        var query = $(this).val().toLowerCase();
        divProgramas.find("li").each(function () {
            var label = $(this);
            var etiqueta = label.text().toLowerCase();
            var $checkbox = label.find('input[type="checkbox"]');

            if (etiqueta.includes(query)) {
                label.css("display", ""); // Mostrar el elemento si coincide con la búsqueda
            } else {
                label.css("display", "none"); // Ocultar el elemento si no coincide con la búsqueda
            }
        });
    });

    // alertaPreload();
    setTimeout(function () {
        $("#generarReporte").click();
    }, 2000);

    //--- al dar click en ganerar repoprte
    $("#generarReporte").on("click", function () {
        //--- trae los periodos  seleccionados
        var periodosSeleccionados = [];

        var checkboxesSeleccionados = $(
            "#Continua, #Pregrado, #Esp, #Maestria"
        ).find('input[type="checkbox"]:checked');

        //--- traemos los campos seleccionados de programas y facultades
        var checkboxesProgramas = $(
            '#programas input[type="checkbox"]:checked'
        );

        var checkboxesfacultades = $(
            '#facultades input[type="checkbox"]:checked'
        );

        if (checkboxesSeleccionados.length > 0) {
            checkboxesSeleccionados.each(function () {
                periodosSeleccionados.push($(this).val());
            });

            //--- se verifica que tenga elmenos 1 periodo seleccionado
            if (periodosSeleccionados.length > 0) {
                filtro = [];

                //--- primero verificamos si hay algun programa marcado
                if (checkboxesProgramas.length > 0) {
                    programasSeleccionados = [];

                    checkboxesProgramas.each(function () {
                        programasSeleccionados.push($(this).val());
                    });

                    if ($("#titulosDivPrograma").text() == "Cursos") {
                        filtro["cursos"] = programasSeleccionados;
                        filtro["periodos"] = periodosSeleccionados;
                    } else {
                        filtro["programa"] = programasSeleccionados;
                        filtro["periodos"] = periodosSeleccionados;
                    }

                    //--- guardamos los datos en un array para enviarlo a las funciones necesarias

                    //--- si no hay programa marcado verificamos la facultad
                } else if (checkboxesfacultades.length > 0) {
                    facultadesSeleccionadas = [];
                    checkboxesfacultades.each(function () {
                        facultadesSeleccionadas.push($(this).val());
                    });
                    //--- guardamos los datos en un array para enviarlo a las funciones necesarias

                    filtro["programa"] = programasSeleccionados;
                    filtro["facultades"] = facultadesSeleccionadas;
                } else {
                    alerta_seleccione_periodo();
                }
            } else {
                //--- si no tiene periodos seleccionados  mandamos la alerta
                alerta_seleccione_periodo();
            }

            if (tabla == "Mafi") {
                //---- llamamos las funciones de mafi
                llamadoFuncionesMafi(filtro);
            } else if (tabla == "planeacion") {
            
                //destruirTable();
                //---- llamamos las funciones de planeacion
                llamadoFuncionesPlaneacion(filtro);
            } else if (tabla == "moodle") {
                $(".content").hide();
                $("#ausentismoMoocs").show();
                $("#navcursosmoocs").removeClass("active");
                $("#navcursos").removeClass("active");
                $("#navausentismocursos").removeClass("active");
                $("#navestudiantes").removeClass("active");
                $("#navausentismoMoocs").addClass("active");
                contador_tabla_cursos = 0;
                //---- llamamos las funciones de moodle
                llamadoFuncionesMoodle(filtro);
            }  else if (tabla == "moodlecerrados") {
             
                $(".content").hide();
                $("#ausentismo").show();
                $("#navcursos").removeClass("active");
                $("#navestudiantes").removeClass("active");
                $("#navausentismo").addClass("active");
                contador_tabla_cursos = 0;
                //---- llamamos las funciones de moodle
                llamadoFuncionesMoodlecerrado(filtro);
            }

        } else {
            $formacion_c=$(document).find("#formacion_c");
            $Pregrado_c=$(document).find("#Pregrado_c");
            
           if($formacion_c.hasClass('hidden') &&  $Pregrado_c.hasClass('hidden')) {
              
                 // El div está vacío
                 alertaPrepPlaneacion()
                // alertaDatos();
              
            } else {
               // alertaDatos();
                alertaPrepPlaneacion()
                //alerta_seleccione_periodo();
            }
            //--- si no tiene periodos seleccionados  mandamos la alerta
            //
           
        }
    });

    //---- al dar click para check box
    $("#todosContinua").change(function () {
        if ($(this).is(":checked")) {
            $("#Continua input[type='checkbox']").prop("checked", true);
        } else {
            $("#Continua input[type='checkbox']").prop("checked", false);
        }
    });

    $("#todosPregrado").change(function () {
        if ($(this).is(":checked")) {
            $("#Pregrado input[type='checkbox']").prop("checked", true);
        } else {
            $("#Pregrado input[type='checkbox']").prop("checked", false);
        }
    });

    $("#todosEsp").change(function () {
        if ($(this).is(":checked")) {
            $("#Esp input[type='checkbox']").prop("checked", true);
        } else {
            $("#Esp input[type='checkbox']").prop("checked", false);
        }
    });

    $("#todosMaestria").change(function () {
        if ($(this).is(":checked")) {
            $("#Maestria input[type='checkbox']").prop("checked", true);
        } else {
            $("#Maestria input[type='checkbox']").prop("checked", false);
        }
    });

    $("#todosFacultad").change(function () {
        if ($(this).is(":checked")) {
            $("#facultades input[type='checkbox']").prop("checked", true);
        } else {
            $("#facultades input[type='checkbox']").prop("checked", false);
        }
    });

    $("#todosPrograma").change(function () {
        if ($(this).is(":checked")) {
            $("#programas input[type='checkbox']").prop("checked", true);
        } else {
            $("#programas input[type='checkbox']").prop("checked", false);
        }
    });

    $("body").on("change",'#facultades input[type="checkbox"], .periodos, .todos',function () 
    {
        $("#programas").empty();
        //alertaPreload();
        var formData = new FormData();
        var checkboxesSeleccionados = $(
            '#facultades input[type="checkbox"]:checked'
        );

        if ($('#facultades input[type="checkbox"]:checked').length > 0) {
            // Enviamos las facultades
            checkboxesSeleccionados.each(function () {
                var labelText = $(this).parent().text().trim();
                formData.append("codfacultad[]", labelText);
            });
        } else {
            formData.append("codfacultad[]", "");
        }
        checkboxesPeriodosSeleccionados = $("#Continua, #Pregrado, #Esp, #Maestria").find('input[type="checkbox"]:checked');

        if (checkboxesPeriodosSeleccionados.length > 0) {
            checkboxesPeriodosSeleccionados.each(function () {
                formData.append("periodos[]", $(this).val());
            });

            formData.append("tabla", tabla);
            if(tabla == 'moodlecerrados'){
                get_program= get_program_filtro
            }
            let urlFiltros = get_program
            if ($("#titulosDivPrograma").text() == "Cursos") {
                urlFiltros = get_cursos
            }
            $.ajax({
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                type: "post",
                url: urlFiltros,
                data: formData,
                cache: false,
                contentType: false,
                processData: false,

                success: function (datos) {
                    try {
                        datos = jQuery.parseJSON(datos);
                    } catch {
                        datos = datos;
                    }

                    $.each(datos, function (key, value) {
                        if(urlFiltros == get_cursos)
                        {
                            $("#programas").append(
                                `<li id="Checkbox${value.programa}" data-codigo="${value.programa}"><label"> <input id="checkboxProgramas" type="checkbox" name="programa[]" value="${value.programa}" checked>${value.programa}</label></li>`
                            );
                        }else{
                            $("#programas").append(
                           `<li id="Checkbox${value.codprograma}" data-codigo="${value.codprograma}"><label"> <input id="checkboxProgramas" type="checkbox" name="programa[]" value="${value.codprograma}" checked> ${value.codprograma}-${value.programa}</label></li>`
                            );
                        }
                    });      
                    Swal.close();
                        },
            });
        } else {
            alerta_seleccione_periodo();
        }
    });


    filtros();
});

/**
 * Método que trae toda la informacion de los filtros
 */

function filtros() {
    $.ajax({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        url: url_periodo,
        data: {
            tabla: tabla,
        },
        method: "post",
        async: false,
        success: function (data) {
            //--- insertamos los periodos
            $.each(data.periodo, function (index, value) {
                if (value.nivelFormacion == "EDUCACION CONTINUA") {
                    continua = 1;
                    $("#Continua").append(
                        `<label"> <input type="checkbox" value="${value.periodo}" checked> ${value.periodo}</label><br>`
                    );
                }
                if (value.nivelFormacion == "PROFESIONAL") {
                    profesional = 1;
                    $("#Pregrado").append(
                        `<label"> <input type="checkbox" value="${value.periodo}" checked> ${value.periodo}</label><br>`
                    );
                }
                if (value.nivelFormacion == "ESPECIALISTA") {
                    especialista = 1;
                    $("#Esp").append(
                        `<label"> <input type="checkbox" value="${value.periodo}" checked> ${value.periodo}</label><br>`
                    );
                }
                if (value.nivelFormacion == "MAESTRIA") {
                    maestria = 1;
                    $("#Maestria").append(
                        `<label"> <input type="checkbox" value="${value.periodo}" checked> ${value.periodo}</label><br>`
                    );
                }
            });

            //--- si trae mas de 1 facultad
            if (data.facultades.length > 1) {
                //--- insertamos las facultades
                $.each(data.facultades, function (index, value) {
                    $("#facultades").append(`<label"> <input type="checkbox" value="${value.id}" checked> ${value.nombre}</label><br>`);
                });
                //--- si solo tiene una facultad escondemmos el filtro de facultad
            } else {
                $("#cardFacultades").addClass("hidden");
            }

            
            if (tabla == "moodle" || tabla == "moodlecerrados" || tabla == "planeacion") {
                //--- insertamos las facultades
                if (data.nombresCursos && data.nombresCursos.length > 1) {
                    $('#Admisiones').hide();
                    $("#titulosDivPrograma").empty();

                    $("#titulosDivPrograma").append("Cursos");

                    $.each(data.nombresCursos, function (index, value) {
                        $("#programas").append(`<li id="Checkbox${value}" data-codigo="${value}"><label"> <input id="checkboxProgramas" type="checkbox" name="programa[]" value="${value}" checked>${value}</label></li>`);
                    });
                } else {
                    $.each(data.programas, function (index, value) {
                        $("#programas").append(`<li id="Checkbox${value.codprograma}" data-codigo="${value.codprograma}"><label"> <input id="checkboxProgramas" type="checkbox" name="programa[]" value="${value.codprograma}" checked> ${value.codprograma}-${value.programa}</label></li>`);
                    });
                }
            } else {
                $.each(data.programas, function (index, value) {
                    $("#programas").append(`<li id="Checkbox${value.codprograma}" data-codigo="${value.codprograma}"><label"> <input id="checkboxProgramas" type="checkbox" name="programa[]" value="${value.codprograma}" checked> ${value.codprograma}-${value.programa}</label></li>`);
                });
            }

            if ($("#Continua").text().trim() === "") {
                $("#formacion_c").addClass("hidden");
            }
            if ($("#Pregrado").text().trim() === "") {
                $("#Pregrado_c").addClass("hidden");
            }
            if ($("#Esp").text().trim() === "") {
                $("#Especializacion_c").addClass("hidden");
            }
            if ($("#Maestria").text().trim() === "") {
                $("#Maestrias_c").addClass("hidden");
            }
            if ($("#programas").text().trim() === "") {
                $("#Programas_c").addClass("hidden");
            }
        },
    });
}

//--- funcion que me trae toda la data necesaria para mafi
function llamadoFuncionesMafi(filtro) {
    
    
    //--- llamamos el preload
    alertaPreload(filtro);
    destruirGraficos();
    //--- funciones de los graficos
    graficoEstudiantes(filtro);
    graficoSelloFinanciero(filtro);
    graficoRetencion(filtro);
    graficoSelloPrimerIngreso(filtro);
    graficoSelloAntiguos(filtro);
    graficoTipoDeEstudiante(filtro);
    graficoOperadores(filtro);
    graficoProgramas(filtro);
    graficoMetas(filtro);
}

//--- funcion que me trae toda la data necesaria para mafi
function llamadoFuncionesPlaneacion(filtro) {
   // alertaPrepPlaneacion();



//     //--- llamamos el preload
    alertaPreload();
//    
//  alertaPlaneacion();
//  alertaPrepPlaneacion()
    destruirTable()
    destruirGraficos();
    graficoSelloFinanciero(filtro);
    graficoRetencion(filtro);
    graficoSelloPrimerIngreso(filtro);
    graficoSelloAntiguos(filtro);
    graficoTipoDeEstudiante(filtro);
    graficoOperadores(filtro);
    graficoProgramas(filtro);

}

//--- funcion que me trae toda la data necesaria para mafi
function llamadoFuncionesMoodle($filtro) {
    //--- llamamos el preload

    //alertaDatos();
    alertaPreload();
    //alertaDatos();
    //--- funciones de los graficos
    riesgoconMoocs($filtro);
    riesgossinMoocs($filtro);
    destruirTablaCurso();
    destruirTablaCursoMoocs();
    riesgoEstudiantes()

   
}

//--- funcion que me trae toda la data necesaria para mafi
function llamadoFuncionesMoodlecerrado($filtro) {
    //--- llamamos el preload

    //alertaDatos();
   alertaPreload();
    //alertaDatos();
    //--- funciones de los graficos
    riesgo($filtro);
   riesgoEstudiantes()

   
}

// Función para obtener los periodos
function getPeriodos() {
    periodosSeleccionados = [];
    var checkboxesSeleccionados = $("#Continua, #Pregrado, #Esp, #Maestria").find('input[type="checkbox"]:checked');
    checkboxesSeleccionados.each(function () {
        periodosSeleccionados.push($(this).val());
    });
    return periodosSeleccionados;
}
