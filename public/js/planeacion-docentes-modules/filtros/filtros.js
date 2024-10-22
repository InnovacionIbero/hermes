import { enviarAjax } from "../utils/enviar-ajax.js";

export const traerDataFiltros = async (url) => {
    let datos = await enviarAjax("", url);

    if (datos) {
        if (datos.facultades) {
            $.each(datos.facultades, function (index, value) {
                $("#facultades").append(
                    `<label> <input type="checkbox" value="${value.nombre}" data-codfacultad="${value.codFacultad}" data-transversal="${value.transversal}" checked> ${value.nombre}</label><br>`
                );
            });
        }
        if (datos.programas) {
            $.each(datos.programas, function (index, value) {
                $("#programas").append(
                    `<li id="Checkbox${value.codprograma}" data-codigo="${value.codprograma}"><label"> <input id="checkboxProgramas" type="checkbox" name="programa[]" value="${value.codprograma}" checked> ${value.codprograma}-${value.programa}</label></li>`
                );
            });
        }
        if (datos.cursos) {
            $.each(datos.cursos, function (index, value) {
                $("#cursos").append(
                    `<li id="Checkbox${value.curso}" data-codigo="${value.codigoCurso}"><label"> <input id="checkboxCursos" type="checkbox" name="curso[]" value="${value.codigoCurso}" checked> ${value.codigoCurso} - ${value.curso}</label></li>`
                );
            });
        }
    } else {
        $("#card-programas").hide();
        $("#generarReporte").hide();
    }
};

export class EstadoFiltros {
    constructor() {
        this.programasSeleccionados = [];
        this.facultadesSeleccionadas = [];
        this.todosProgramas = [];
        this.actualizarEstado();
    }

    async actualizarEstado() {
        let checkboxesFacultades = $(
            '#facultades input[type="checkbox"]:checked'
        );
        let checkboxesProgramas = $(
            '#programas input[type="checkbox"]:checked'
        );
        let checboxesCursos = $('#cursos input[type="checkbox"]:checked');

        this.facultadesSeleccionadas = [];
        this.programasSeleccionados = [];
        this.cursosFacultadTransversal = [];

        if (checkboxesFacultades.length > 0) {
            checkboxesFacultades.each((index, checkbox) => {
                let object = {
                    nombre: $(checkbox).val(),
                    codFacultad: $(checkbox).data("codfacultad"),
                    transversal: $(checkbox).data("transversal"),
                };

                this.facultadesSeleccionadas.push(object);
            });
        }

        if (checkboxesProgramas.length > 0) {
            checkboxesProgramas.each((index, checkbox) => {
                this.programasSeleccionados.push($(checkbox).val());
            });
        }

        if (checboxesCursos.length > 0) {
            checboxesCursos.each((index, checkbox) => {
                this.cursosFacultadTransversal.push($(checkbox).val());
            });
        }
    }

    obtenerProgramas() {
        let checkboxesTodosProgramas = $('#programas input[type="checkbox"]');

        if (checkboxesTodosProgramas.length > 0) {
            checkboxesTodosProgramas.each((index, checkbox) => {
                let valor = $(checkbox).val(); // Obtener el valor del input
                let texto = $(checkbox).parent().text().trim(); // Obtener el texto asociado y eliminar espacios

                let programa = texto.split("-")[1].trim();

                this.todosProgramas.push({
                    codprograma: valor,
                    programa: programa,
                });
            });
        }

        return {
            programas:
                this.todosProgramas.length > 0 ? this.todosProgramas : "",
        };
    }

    obtenerFiltros() {
        return {
            facultades:
                this.facultadesSeleccionadas.length > 0
                    ? this.facultadesSeleccionadas
                    : "",
            programas:
                this.programasSeleccionados.length > 0
                    ? this.programasSeleccionados
                    : "",
            cursos: this.cursosFacultadTransversal,
        };
    }
}

export const actualizarProgramas = async (filtros, url) => {
    $("#programas").empty();

    if (filtros.facultades.length == 0) {
        return;
    }

    let data = await enviarAjax(filtros, url);

    data.programas.forEach((element) => {
        $("#programas").append(
            `<li id="Checkbox${element.codprograma}" data-codigo="${element.codprograma}"><label"> <input id="checkboxProgramas" type="checkbox" name="programa[]" value="${element.codprograma}" checked> ${element.codprograma}-${element.programa}</label></li>`
        );
    });
};

export const seleccionarMenuNav = (event) => {
    $(".menuMoodle").removeClass("active");
    $(".content").hide();

    let target = $(event.currentTarget).attr("href").substring(1);

    $("#" + target).show();

    $("#nav" + target).addClass("active");
};

export const buscarDato = (event, transversal = false) => {
    let divBuscar = transversal ? $("#cursos") : $("#programas");

    let query = $(event.target).val().toLowerCase();
    divBuscar.find("li").each(function () {
        let label = $(this);
        let etiqueta = label.text().toLowerCase();

        if (etiqueta.includes(query)) {
            label.css("display", ""); 
        } else {
            label.css("display", "none"); 
        }
    });
}