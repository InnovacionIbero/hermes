//--- definimos las variables
var programasSeleccionados = [];
var facultadesSeleccionadas = [];

// Deshabilitar los checkboxes cuando comienza una solicitud AJAX
$(document).ajaxStart(function () {
    $('div #facultades input[type="checkbox"]').prop("disabled", true);
    $('.periodos input[type="checkbox"]').prop("disabled", true);
    $('div #programas input[type="checkbox"]').prop("disabled", true);
    $("#generarReporte").prop("disabled", true);
});

// Volver a habilitar los checkboxes cuando finaliza una solicitud AJAX
$(document).ajaxStop(function () {
    $('div #facultades input[type="checkbox"]').prop("disabled", false);
    $('.periodos input[type="checkbox"]').prop("disabled", false);
    $('div #programas input[type="checkbox"]').prop("disabled", false);
    $("#generarReporte").prop("disabled", false);
});

/**
 * Método que trae toda la informacion de los filtros
 */
function filtros(url_periodo) {
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
                    // $('#Continua').append(`<label"> <input type="checkbox" value="${value.periodo}" checked> ${value.periodo}</label><br>`);
                    $("#Continua").append(`<div class="checkbox-wrapper mb-1">
                        <input class="inp-cbx" id="cbx-${value.periodo}" type="checkbox" value="${value.periodo}" checked>
                        <label class="cbx" for="cbx-${value.periodo}"><span>
                                <svg width="12px" height="10px" viewbox="0 0 12 10">
                                    <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                </svg></span><span>${value.periodo}</span>
                        </label>
                    </div>`);
                }
                if (value.nivelFormacion == "PROFESIONAL") {
                    profesional = 1;
                    //$('#Pregrado').append(`<label"> <input type="checkbox" value="${value.periodo}" checked> ${value.periodo}</label><br>`);
                    $("#Pregrado").append(`<div class="checkbox-wrapper mb-1">
                    <input class="inp-cbx" id="cbx-${value.periodo}" type="checkbox" value="${value.periodo}" checked>
                    <label class="cbx" for="cbx-${value.periodo}"><span>
                            <svg width="12px" height="10px" viewbox="0 0 12 10">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span><span>${value.periodo}</span>
                    </label>
                </div>`);
                }
                if (value.nivelFormacion == "ESPECIALISTA") {
                    especialista = 1;
                    //$('#Esp').append(`<label"> <input type="checkbox" value="${value.periodo}" checked> ${value.periodo}</label><br>`);
                    $("#Esp").append(`<div class="checkbox-wrapper mb-1">
                    <input class="inp-cbx" id="cbx-${value.periodo}" type="checkbox" value="${value.periodo}" checked>
                    <label class="cbx" for="cbx-${value.periodo}"><span>
                            <svg width="12px" height="10px" viewbox="0 0 12 10">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span><span>${value.periodo}</span>
                    </label>
                </div>`);
                }
                if (value.nivelFormacion == "MAESTRIA") {
                    maestria = 1;
                    //$('#Maestria').append(`<label"> <input type="checkbox" value="${value.periodo}" checked> ${value.periodo}</label><br>`);
                    $("#Maestria").append(`<div class="checkbox-wrapper mb-1">
                    <input class="inp-cbx" id="cbx-${value.periodo}" type="checkbox" value="${value.periodo}" checked>
                    <label class="cbx" for="cbx-${value.periodo}"><span>
                            <svg width="12px" height="10px" viewbox="0 0 12 10">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span><span>${value.periodo}</span>
                    </label>
                </div>`);
                }
            });

            //--- si trae mas de 1 facultad
            if (data.facultades.length > 1) {
                //--- insertamos las facultades
                $.each(data.facultades, function (index, value) {
                    //$('#facultades').append(`<label"> <input type="checkbox" value="${value.nombre}" checked> ${value.nombre}</label><br>`);
                    $("#facultades").append(`<div class="checkbox-wrapper mb-1">
                    <input class="inp-cbx" id="cbx-${value.nombre}" type="checkbox" value="${value.nombre}" checked>
                    <label class="cbx" for="cbx-${value.nombre}"><span>
                            <svg width="12px" height="10px" viewbox="0 0 12 10">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span><span>${value.nombre}</span>
                    </label>
                </div>`);
                });
                //--- si solo tiene una facultad escondemmos el filtro de facultad
            } else {
                $("#cardFacultades").addClass("hidden");
            }

            //--- si trae mas de 1 facultad
            /*if(data.facultades.length > 1){
                //--- insertamos las facultades
                $.each(data.facultades, function(index, value) {
                                

                    $('#facultades').append(`<label"> <input type="checkbox" value="${value.nombre}" checked> ${value.nombre}</label><br>`);

                });
             //--- si solo tiene una facultad escondemmos el filtro de facultad   
            }else{
                $('#cardFacultades').addClass('hidden');
            }*/

            //--- insertamos las facultades
            $.each(data.programas, function (index, value) {
                //$('#programas').append(`<label"> <input type="checkbox" value="${value.codprograma}" checked> ${value.codprograma}-${value.programa}</label><br>`);
                $("#programas").append(`<div class="checkbox-wrapper mb-1">
                <li id="Checkbox${value.codprograma}" data-codigo="${value.codprograma}">    
                <input class="inp-cbx" id="cbx-${value.codprograma}" type="checkbox" name="programa[]" value="${value.codprograma}" checked>
                    <label class="cbx" for="cbx-${value.codprograma}"><span>
                            <svg width="12px" height="10px" viewbox="0 0 12 10">
                                <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                            </svg></span><span>${value.programa}</span>
                    </label>
                    </li>
                </div>`);
            });

            continua = 0;
            Pregrado = 0;
            Esp = 0;
            Maestria = 0;
            programas = 0;

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

// Función para obtener los periodos
function getPeriodos() {
    periodosSeleccionados = [];
    var checkboxesSeleccionados = $(
        "#Continua, #Pregrado, #Esp, #Maestria"
    ).find('input[type="checkbox"]:checked');
    checkboxesSeleccionados.each(function () {
        periodosSeleccionados.push($(this).val());
    });
    return periodosSeleccionados;
}

$("body").on(
    "change",
    '#facultades input[type="checkbox"], .periodos, .todos',
    function () {
        if ($("#titulosDivPrograma").text() == "Cursos") {
        } else {
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

            checkboxesPeriodosSeleccionados = $(
                "#Continua, #Pregrado, #Esp, #Maestria"
            ).find('input[type="checkbox"]:checked');
            if (checkboxesPeriodosSeleccionados.length > 0) {
                checkboxesPeriodosSeleccionados.each(function () {
                    //  var valorCheckbox = $(this).val().slice(-2);
                    formData.append("periodos[]", $(this).val());
                });

                formData.append("tabla", tabla);

                $.ajax({
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    type: "post",
                    url: get_program,
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
                            $("#programas").append(
                                `<li id="Checkbox${value.codprograma}" data-codigo="${value.codprograma}"><label"> <input id="checkboxProgramas" type="checkbox" name="programa[]" value="${value.codprograma}" checked> ${value.codprograma}-${value.programa}</label></li>`
                            );
                        });
                        Swal.close();
                    },
                });
            } else {
                alerta_seleccione_periodo();
            }
        }
    }
);
