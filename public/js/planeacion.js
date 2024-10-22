// Botón flotante para la busqueda de algún estudiante
const showSearchButton = document.getElementById('show-search-button');
const searchContainer = document.getElementById('search-container');
//alertaAsp();
var operador = ''

showSearchButton.addEventListener('click', () => {
    if (searchContainer.style.display === 'none' || searchContainer.style.display === '') {
        searchContainer.style.display = 'block';
    } else {
        searchContainer.style.display = 'none';
    }
});

//alertaPlaneacionsegundoIngreso()
$('#slideplaneacion').removeAttr('hidden');

$(document).find('#Planeación').addClass('activo');

var contadorGraficos = [];
var contadorPlaneacion = 0;

//alertaPlaneacion()
//alertaPlaneacionUnaVezDia();

function planeacionVacia() {
    Swal.fire({
        icon: 'info',
        title: 'No hay datos disponibles',
        text: 'Por el momento no hay datos disponibles de planeación, intenta más tarde',
        confirmButtonColor: '#3085d6',
    }); 
}

//setTimeout(planeacionVacia, 3000)

function destruirGraficos() {
    if (chartEstudiantesActivos !== undefined || chartEstudiantesActivos) {
        chartEstudiantesActivos.destroy();
    }

    if (chartRetencion !== undefined || chartRetencion) {
        chartRetencion.destroy();
    }

    if (chartSelloPrimerIngreso !== undefined || chartSelloPrimerIngreso) {
        chartSelloPrimerIngreso.destroy();
    }

    if (chartSelloAntiguos !== undefined || chartSelloAntiguos) {
        chartSelloAntiguos.destroy();
    }

    if (chartTipoEstudiante !== undefined || chartTipoEstudiante) {
        chartTipoEstudiante.destroy();
    }

    if (chartOperadores !== undefined || chartOperadores) {
        chartOperadores.destroy();
    }

    if (chartProgramas !== undefined || chartProgramas) {
        chartProgramas.destroy();
    }
}

// Actualizar títulos de los gráficos
function limpiarTitulos() {
    var elementosTitulos = $('#tituloEstadoFinanciero, #tituloRetencion, #tituloEstudiantesNuevos, #tituloTipos, #tituloOperadores, #tituloProgramas, #tituloOperadoresTotal, #tituloTiposTotal, #tituloProgramasTotal').find("strong");
    var parteEliminar = ': ';
    elementosTitulos.each(function() {
        var contenidoActual = $(this).text();
        var contenidoLimpio = contenidoActual.replace(new RegExp(parteEliminar + '.*'), '');
        $(this).text(contenidoLimpio);
    });
    var parteTituloEliminar = 'Periodo: ';
    var titulosPeriodos = $('.tituloPeriodo').find("strong");
    titulosPeriodos.each(function() {
        var contenidoActual = $(this).text();
        var contenidoLimpio = contenidoActual.replace(new RegExp(parteTituloEliminar + '.*'), '');
        $(this).text(contenidoLimpio);
    });
}

function estadoUsuarioPrograma() {
    limpiarTitulos();
    var periodos = getPeriodos();
    $("#mensaje").empty();
    if (programasSeleccionados.length > 1) {
        var programasArray = Object.values(programasSeleccionados);
        var programasFormateados = programasArray.join(' - ');
        var textoNuevo = "<h4><strong>Informe programas: " + programasFormateados + "</strong></h4>";
        $('#tituloEstadoFinanciero strong, #tituloRetencion strong, #tituloEstudiantesNuevos strong, #tituloTipos strong, #tituloOperadores strong, #tituloProgramas strong').append(': ' + programasFormateados);
    } else {
        var textoNuevo = "<h4><strong>Informe programa " + programasSeleccionados + "</strong></h4>";
        $('#tituloEstadoFinanciero strong, #tituloRetencion strong, #tituloEstudiantesNuevos strong, #tituloTipos strong, #tituloOperadores strong, #tituloProgramas strong').append(': ' + programasSeleccionados);
    }
    $("#mensaje").html(textoNuevo);
}

function estadoUsuarioFacultad() {
    limpiarTitulos();
    var periodos = getPeriodos();
    $("#mensaje").empty();
    var facultadesArray = Object.values(facultadesSeleccionadas);
    var facultadesFormateadas = facultadesArray.map(function(facultad) {
        return facultad.toLowerCase().replace(/facultad de |fac /gi, '').trim();
    }).join(' - ');

    var periodosArray = Object.values(periodos);
    var periodosFormateados = periodosArray.map(function(periodo) {
        return periodo.replace(/2023/, '').trim();
    }).join(' - ');

    if (facultadesSeleccionadas.length > 1) {
        var textoNuevo = "<h4><strong>Informe facultades: " + facultadesFormateadas + "</strong></h4>";
        $('#tituloEstadoFinanciero strong, #tituloRetencion strong, #tituloEstudiantesNuevos strong, #tituloTipos strong, #tituloOperadores strong, #tituloProgramas strong').append(': ' + facultadesFormateadas);
    } else {

        var textoNuevo = "<h4><strong>Informe facultad: " + facultadesFormateadas + "</strong></h4>";
        $('#tituloEstadoFinanciero strong, #tituloRetencion strong, #tituloEstudiantesNuevos strong, #tituloTipos strong, #tituloOperadores strong, #tituloProgramas strong').append(': ' + facultadesFormateadas);
    }
    $('.tituloPeriodo strong').append('Periodo: ' + periodosFormateados);
    $("#mensaje").show();
    $("#mensaje").html(textoNuevo);
}

function Contador() {
    totalFacultades = $('#facultades input[type="checkbox"]').length;
    totalProgramas = $('#programas input[type="checkbox"]').length;
    totalPeriodos = $('#programas input[type="checkbox"]').length;
}

var chartEstudiantesActivos;
function graficoSelloFinanciero(filtro) {
    if(filtro.cursos && filtro.cursos.length > 0)
    {
        data = {
            idcurso: filtro.cursos,
            periodos: filtro.periodos
        },
        url = '../home/selloEstudiantesCursos'
    }else if (filtro.programa && filtro.programa.length > 0) {
        data = {
            programa: filtro.programa,
            periodos: filtro.periodos
        },
        url = "../planeacion/estudiantesactivos"
    } else if (filtro.facultades && filtro.facultades.length > 0) {
        data = {
            idfacultad: filtro.facultades,
            periodos: filtro.periodos
        },
        url = "../planeacion/estudiantesactivos"
    }
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: url,
        data: data,
        success: function(data) {
            try {
                data = parseJSON(data);
            } catch {
                data = data;
            }
            var labels = [];
            var valores = [];
            var contieneSoloCeros = true;
            for (var propiedad in data) {
                if (data.hasOwnProperty(propiedad)) {
                    labels.push(propiedad + ': ' + data[propiedad]);
                    valores.push(data[propiedad]);
                    if (data[propiedad] !== 0) {
                        contieneSoloCeros = false;
                    }
                }
            }

            var suma = valores.reduce(function(acumulador, valorActual) {
                return acumulador + valorActual;
            }, 0);

            if (contieneSoloCeros) {
                contadorGraficos.push(0);
                //planeacionVacia();
                alertaPrepPlaneacion();
                $('#colSelloFinanciero').addClass('hidden');
            } else {
                var ctx = document.getElementById('activos').getContext('2d');
                chartEstudiantesActivos = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Gráfico Circular',
                            data: valores,
                            backgroundColor: ['rgba(74, 72, 72, 0.5)', 'rgba(223, 193, 78, 1)', 'rgba(56,101,120,1)', 'rgba(208,171,75, 1)']
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            datalabels: {
                                color: 'black',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                },
                                formatter: function(value, context) {
                                    return context.chart.data.datasets[0].data[context.dataIndex] >= 10 ? value : '';
                                }
                            },
                            labels: {
                                render: 'percenteaje',
                                size: '14',
                                fontStyle: 'bolder',
                                position: 'border',
                                textMargin: 6
                            },
                            legend: {
                                position: 'right',
                                align: 'left',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            title: {
                                display: true,
                                text: 'TOTAL SELLO: ' + suma,
                                font: {
                                    size: 14,
                                    Style: 'bold',
                                },
                                position: 'bottom'
                            }
                        },
                    },
                    plugins: [ChartDataLabels]
                });
                if (chartEstudiantesActivos.data.labels.length == 0 && chartEstudiantesActivos.data.datasets[0].data.length == 0) {
                    $('#colSelloFinanciero').addClass('hidden');
                    contadorGraficos.push(0);
                } else {
                    $('#colSelloFinanciero').removeClass('hidden');
                    contadorGraficos.push(1);
                }
            }

        }
    });
}

var chartRetencion;

function graficoRetencion(filtro) {
    var data;
 
    if(filtro.cursos && filtro.cursos.length > 0)
    {  
        data = {
            idcurso: filtro.cursos,
            periodos: filtro.periodos
        },
        url = '../home/selloRetencion'

    }
    else if (filtro.programa && filtro.programa.length > 0) {
        data = {
            programa: filtro.programa,
            periodos: filtro.periodos
        },
        url = '../planeacion/estudiantesretencion'
        
    } else if (filtro.facultades && filtro.facultades.length > 0) {
        data = {
            idfacultad: filtro.facultades,
            periodos: filtro.periodos
        },
        url = '../planeacion/estudiantesretencion'
    }
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: url,
        data: data,
        success: function(data) {
            try {
                data = jQuery.parseJSON(data);
            } catch {
                data = data;
            }
                
            var labels = [];
            var valores = [];

            data.data.forEach(function(elemento) {
                if (elemento.TOTAL !== null && elemento.TOTAL !== undefined && elemento.TOTAL !== 0) {
                    if (elemento.autorizado_asistir == null || elemento.autorizado_asistir == 'NULL') {
                        labels.push('SIN AUTORIZADO ASISTIR');
                    } else {
                        if (elemento.autorizado_asistir.startsWith('ACTIVO EN ')) {
                            labels.push(elemento.autorizado_asistir.replace('ACTIVO EN ', '').trim());
                        } else {
                            labels.push(elemento.autorizado_asistir);
                        }
                    }

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
                yMax = (maxValorAux - maxValor) < 600 ? maxValorAux + 1000 : maxValorAux;
            }
            // Crear el gráfico circular
            var ctx = document.getElementById('retencion').getContext('2d');
            chartRetencion = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels.map(function(label, index) {
                        if (label == '') {
                            label = 'SIN MARCACIÓN'
                        }
                        return label + ': ' + valores[index];
                    }),
                    datasets: [{
                        data: valores,
                        backgroundColor: ['rgba(74, 72, 72, 1)', 'rgba(223, 193, 78, 1)', 'rgba(208,171,75, 1)',
                            'rgba(208,171,75, 1)'
                        ],
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                        }
                    }]
                },
                options: {
                    scales: {
                        y: {
                            max: yMax,
                            beginAtZero: true
                        }
                    },
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {

                        datalabels: {
                            color: 'black',
                            font: {
                                weight: 'semibold'
                            },
                            formatter: Math.round
                        },
                        legend: {
                            display: false,
                        }
                    },
                },
                plugins: [ChartDataLabels],
            });
            if (chartRetencion.data.labels.length == 0 && chartRetencion.data.datasets[0].data.length == 0) {
                $('#colRetencion').addClass('hidden');
                contadorGraficos.push(0);
            } else {
                $('#colRetencion').removeClass('hidden');
                contadorGraficos.push(1);
            }
        }
    });
}

var chartSelloPrimerIngreso;

function graficoSelloPrimerIngreso(filtro) {
    var data;
    if(filtro.cursos && filtro.cursos.length > 0)
    {
        data = {
            idcurso: filtro.cursos,
            periodos: filtro.periodos
        },
        url = '../home/primerIngresoCursos'
    } else if (filtro.programa && filtro.programa.length > 0) {
        data = {
            programa: filtro.programa,
            periodos: filtro.periodos
        },
        url = '../planeacion/estudiantesprimeringreso'
    } else if (filtro.facultades && filtro.facultades.length > 0) {
        data = {
            idfacultad: filtro.facultades,
            periodos: filtro.periodos
        },
        url = '../planeacion/estudiantesprimeringreso'
    }
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: url,
        data: data,
        success: function(data) {
            var labels = [];
            var valores = [];
            var contieneSoloCeros = true;

            for (var propiedad in data) {
                if (data.hasOwnProperty(propiedad)) {
                    labels.push(propiedad + ': ' + data[propiedad]);
                    valores.push(data[propiedad]);
                    if (data[propiedad] !== 0) {
                        contieneSoloCeros = false;
                    }
                }
            }

            var suma = valores.reduce(function(acumulador, valorActual) {
                return acumulador + valorActual;
            }, 0);

            if (contieneSoloCeros) {
                contadorGraficos.push(0);
                $('#colPrimerIngreso').addClass('hidden');
            } else {
                var ctx = document.getElementById('primerIngreso').getContext('2d');
                chartSelloPrimerIngreso = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels.map(function(label, index) {
                            if (label == 'TOTAL') {
                                return label + ': ' + suma;
                            } else {
                                return label;
                            }
                        }),
                        datasets: [{
                            label: 'Gráfico Circular',
                            data: valores,
                            backgroundColor: ['rgba(74, 72, 72, 0.5)', 'rgba(223, 193, 78, 1)', 'rgba(56,101,120,1)', 'rgba(208,171,75, 1)']
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        layout: {
                            padding: {
                                left: 20,
                            },
                        },
                        plugins: {
                            datalabels: {
                                color: 'black',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                },
                                formatter: function(value, context) {
                                    return context.chart.data.datasets[0].data[context.dataIndex] >= 10 ? value : '';
                                }
                            },
                            labels: {
                                render: 'percenteaje',
                                size: '14',
                                fontStyle: 'bolder',
                                position: 'border',
                                textMargin: 2
                            },
                            legend: {
                                position: 'right',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            title: {
                                display: true,
                                text: 'TOTAL SELLO ESTUDIANTES PRIMER INGRESO: ' + suma,
                                font: {
                                    size: 14,
                                    Style: 'bold',
                                },
                                position: 'bottom'
                            }
                        },
                    },
                    plugins: [ChartDataLabels]
                });
                if (chartSelloPrimerIngreso.data.labels.length == 0 && chartSelloPrimerIngreso.data.datasets[0].data.length == 0) {
                    $('#colPrimerIngreso').addClass('hidden');
                    contadorGraficos.push(0);
                } else {
                    $('#colPrimerIngreso').removeClass('hidden');
                    contadorGraficos.push(1);
                }
            }

        }
    });
}

var chartSelloAntiguos;

function graficoSelloAntiguos(filtro) {
    var data;

    if(filtro.cursos && filtro.cursos.length > 0)
    {
        data = {
            idcurso: filtro.cursos,
            periodos: filtro.periodos
        },
        url = '../home/estudiantesAntiguosCursos'
    }else  if (filtro.programa && filtro.programa.length > 0) {
        data = {
            programa: filtro.programa,
            periodos: filtro.periodos
        }
        url = '../planeacion/estudiantesantiguos'
    } else if (filtro.facultades && filtro.facultades.length > 0) {
        data = {
            idfacultad: filtro.facultades,
            periodos: filtro.periodos
        },
        url = '../planeacion/estudiantesantiguos';
    }
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: url,
        data: data,
        success: function(data) {
            var labels = [];
            var valores = [];
            var contieneSoloCeros = true;
            for (var propiedad in data) {
                if (data.hasOwnProperty(propiedad)) {
                    labels.push(propiedad + ': ' + data[propiedad]);
                    valores.push(data[propiedad]);
                    if (data[propiedad] !== 0) {
                        contieneSoloCeros = false;
                    }
                }
            }

            var suma = valores.reduce(function(acumulador, valorActual) {
                return acumulador + valorActual;
            }, 0);

            if (contieneSoloCeros) {
                contadorGraficos.push(0);
                $('#colAntiguos').addClass('hidden');
            } else {
                var ctx = document.getElementById('antiguos').getContext('2d');
                chartSelloAntiguos = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: labels.map(function(label, index) {
                            if (label == 'TOTAL') {
                                return label + ': ' + suma;
                            } else {
                                return label;
                            }
                        }),
                        datasets: [{
                            label: 'Gráfico Circular',
                            data: valores,
                            backgroundColor: ['rgba(74, 72, 72, 0.5)', 'rgba(223, 193, 78, 1)', 'rgba(56,101,120,1)', 'rgba(208,171,75, 1)']
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        responsive: true,
                        layout: {
                            padding: {
                                left: 20,
                            },
                        },
                        plugins: {
                            datalabels: {
                                color: 'black',
                                font: {
                                    weight: 'bold',
                                    size: 12
                                },
                                formatter: function(value, context) {
                                    return context.chart.data.datasets[0].data[context.dataIndex] >= 10 ? value : '';
                                }
                            },
                            labels: {
                                render: 'percenteaje',
                                size: '14',
                                fontStyle: 'bolder',
                                position: 'border',
                                textMargin: 2
                            },
                            legend: {
                                position: 'right',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        size: 12
                                    }
                                }
                            },
                            title: {
                                display: true,
                                text: 'TOTAL SELLO ESTUDIANTES ANTIGUOS: ' + suma,
                                font: {
                                    size: 14,
                                    Style: 'bold',
                                },
                                position: 'bottom'
                            }
                        },
                    },
                    plugins: [ChartDataLabels]
                });
                if (chartSelloAntiguos.data.labels.length == 0 && chartSelloAntiguos.data.datasets[0].data.length == 0) {
                    $('#colAntiguos').addClass('hidden');
                    contadorGraficos.push(0);
                } else {
                    $('#colAntiguos').removeClass('hidden');
                    contadorGraficos.push(1);
                }
            }

        }
    });
}

var chartTipoEstudiante;

function graficoTipoDeEstudiante(filtro) {
    var data;
    if(filtro.cursos && filtro.cursos.length > 0)
    {
        data = {
            idcurso: filtro.cursos,
            periodos: filtro.periodos
        },
        url = '../home/tiposEstudiantesCursos'
    }else if (filtro.programa && filtro.programa.length > 0) {
        data = {
            programa: filtro.programa,
            periodos: filtro.periodos
        }
        url ='../planeacion/tiposestudiantes'
    } else if (filtro.facultades && filtro.facultades.length > 0) {
        data = {
            idfacultad: filtro.facultades,
            periodos: filtro.periodos
        }
        url ='../planeacion/tiposestudiantes'
    }
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: url,
        data: data,
        success: function(data) {
            try {
                data = jQuery.parseJSON(data);
            } catch {
                data = data;
            }
            var labels = data.data.map(function(elemento) {
                return elemento.tipo_estudiante;
            });
            var valores = data.data.map(function(elemento) {
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
                yMax = (maxValorAux - maxValor) < 600 ? maxValorAux + 1000 : maxValorAux;
            }
            // Crear el gráfico circular
            var ctx = document.getElementById('tipoEstudiante').getContext('2d');
            chartTipoEstudiante = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels.map(function(label) {
                        if(label !=undefined){
                            if (label.includes("ESTUDIANTE ")) {
                                label = label.replace(/ESTUDIANTE\S*/i, "");
                            }
                        }
                        
                        return label;
                    }),
                    datasets: [{
                        label: '',
                        data: valores,
                        backgroundColor: ['rgba(74, 72, 72, 1)',
                            'rgba(223, 193, 78, 1)',
                            'rgba(208,171,75, 1)',
                            'rgba(186,186,186,1)', 'rgba(56,101,120,1)',
                            'rgba(229,137,7,1)'
                        ],
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                        }
                    }]
                },
                options: {
                    scales: {
                        y: {
                            max: yMax,
                            beginAtZero: true
                        }
                    },
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {

                        datalabels: {
                            color: 'black',
                            font: {
                                weight: 'semibold'
                            },
                            formatter: Math.round
                        },
                        legend: {
                            display: false,
                        }
                    },
                },
                plugins: [ChartDataLabels],
            });
            if (chartTipoEstudiante.data.labels.length == 0 && chartTipoEstudiante.data.datasets[0].data.length == 0) {
                $('#colTipoEstudiantes').addClass('hidden');
                contadorGraficos.push(0);
            } else {
                $('#colTipoEstudiantes').removeClass('hidden');
                contadorGraficos.push(1);
            }
        }
    });
}

var chartOperadores;

function graficoOperadores(filtro) {
    var data;

    if(filtro.cursos && filtro.cursos.length > 0)
    {
        data = {
            idcurso: filtro.cursos,
            periodos: filtro.periodos
        },
        url = '../home/operadoresCursos'
    }else if (filtro.programa && filtro.programa.length > 0) {
        data = {
            programa: filtro.programa,
            periodos: filtro.periodos
        }
        url =  '../planeacion/operadores'
    } else if (filtro.facultades && filtro.facultades.length > 0) {
        data = {
            idfacultad: filtro.facultades,
            periodos: filtro.periodos
        }
        url =  '../planeacion/operadores'
    }
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: url,
        data: data,
        success: function(data) {
            try {
                data = jQuery.parseJSON(data);
            } catch {
                data = data;
            }
            var labels = [];
            var valores = [];

            data.data.forEach(function(elemento) {
                if (elemento.operador !== null && elemento.TOTAL !== null &&
                    elemento.TOTAL !== undefined && elemento.TOTAL !== 0) {
                    labels.push(elemento.operador);
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
                yMax = (maxValorAux - maxValor) < 600 ? maxValorAux + 1000 : maxValorAux;
            }
            // Crear el gráfico de barras
            var ctx = document.getElementById('operadores').getContext('2d');
            chartOperadores = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels.map(function(label, index) {
                        if (label == '') {
                            label = 'IBERO';
                        }
                        return label;
                    }),
                    datasets: [{
                        label: 'Operadores con mayor cantidad de estudiantes',
                        data: valores,
                        backgroundColor: ['rgba(74, 72, 72, 1)', 'rgba(223, 193, 78, 1)', 'rgba(208,171,75, 1)',
                            'rgba(186,186,186,1)', 'rgba(56,101,120,1)', 'rgba(229,137,7,1)'
                        ],
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                        }
                    }]
                },
                options: {
                    scales: {
                        y: {
                            max: yMax,
                            beginAtZero: true
                        }
                    },
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        datalabels: {
                            color: 'black',
                            font: {
                                weight: 'semibold'
                            },
                            formatter: Math.round
                        },
                        legend: {
                            display: false,
                        }
                    },
                },
                plugins: [ChartDataLabels]
            });
            if (chartOperadores.data.labels.length == 0 && chartOperadores.data.datasets[0].data.length == 0) {
                $('#colOperadores').addClass('hidden');
                contadorGraficos.push(0);
            } else {
                $('#colOperadores').removeClass('hidden');
                contadorGraficos.push(1);
            }
        }
    });
}

var chartProgramas;

function graficoProgramas(filtro) {
    var data;

    if(filtro.cursos && filtro.cursos.length > 0)
    {
        data = {
            idcurso: filtro.cursos,
            periodos: filtro.periodos
        },
        url = '../home/estudiantesProgramasCursos'
    }else if (filtro.programa && filtro.programa.length > 0) {
        data = {
            programa: filtro.programa,
            periodos: filtro.periodos
        }
        url = '../planeacion/estudiantesprograma'
    } else if (filtro.facultades && filtro.facultades.length > 0) {
        data = {
            idfacultad: filtro.facultades,
            periodos: filtro.periodos
        }
        url ='../planeacion/estudiantesprograma'
    }

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: url,
        data: data,
        success: function(data) {
            try {
                data = jQuery.parseJSON(data);
            } catch {
                data = data;
            }


            var labels = data.data.map(function(elemento) {
                return elemento.programa;
            });
            var valores = data.data.map(function(elemento) {
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
                yMax = (maxValorAux - maxValor) < 600 ? maxValorAux + 1000 : maxValorAux;
            }
            // Crear el gráfico circular
            var ctx = document.getElementById('estudiantesProgramas').getContext('2d');
            chartProgramas = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels.map(function(label, index) {
                        if (label == '') {
                            label = 'IBERO';
                        }
                        return label;
                    }),
                    datasets: [{
                        label: 'Programas con mayor cantidad de estudiantes',
                        data: valores,
                        backgroundColor: ['rgba(74, 72, 72, 1)', 'rgba(223, 193, 78, 1)', 'rgba(208,171,75, 1)',
                            'rgba(186,186,186,1)', 'rgba(56,101,120,1)', 'rgba(229,137,7,1)'
                        ],
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                        },
                    }]
                },
                options: {
                    scales: {
                        y: {
                            max: yMax,
                            beginAtZero: true
                        }
                    },
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        datalabels: {
                            color: 'black',
                            font: {
                                weight: 'semibold'
                            },
                            formatter: Math.round
                        },
                        legend: {
                            display: false,
                        }
                    },
                },
                plugins: [ChartDataLabels]
            });
            if (chartProgramas.data.labels.length == 0 && chartProgramas.data.datasets[0].data.length == 0) {
                $('#colProgramas').addClass('hidden');
                contadorGraficos.push(0);
            } else {
                $('#colProgramas').removeClass('hidden');
                contadorGraficos.push(1);
                /// comentar para evitar cierre de modal inicial
                Swal.close();
            }
        }
    });
}

$('#botonModalOperador').on("click", function(e) {
    e.preventDefault();
    if (chartOperadoresTotal) {
        chartOperadoresTotal.destroy();
    }
    var periodos = getPeriodos();
    graficoOperadoresTotal(periodos);
});

$('#botonModalProgramas').on("click", function(e) {
    e.preventDefault();
    if (chartProgramasTotal) {
        chartProgramasTotal.destroy();
    }
    var periodos = getPeriodos();
    graficoProgramasTotal(periodos);
});

$('#botonModalTiposEstudiantes').on("click", function(e) {
    e.preventDefault();
    if (chartTiposEstudiantesTotal) {
        chartTiposEstudiantesTotal.destroy();
    }
    var periodos = getPeriodos();
    tiposEstudiantesTotal(periodos);
});

var chartTiposEstudiantesTotal

function tiposEstudiantesTotal() {
    
    /* Swal.fire({            
        imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false
    }); */
    var data;

    if(filtro.cursos && filtro.cursos.length > 0)
    {
        data = {
            idcurso: filtro.cursos,
            periodos: filtro.periodos
        },
        url = '../home/tiposEstudiantesCursosTotal'
    }else if (filtro.programa && filtro.programa.length > 0) {
        data = {
            programa: filtro.programa,
            periodos: filtro.periodos
        }
        url = '../planeacion/tiposestudiantestotal'
    } else if (filtro.facultades && filtro.facultades.length > 0) {
        data = {
            idfacultad: filtro.facultades,
            periodos: filtro.periodos
        }
        url = '../planeacion/tiposestudiantestotal'
    }
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: url,
        data: data,
        success: function(data) {
            data = jQuery.parseJSON(data);
            var labels = data.data.map(function(elemento) {
                return elemento.tipo_estudiante;
            });
            var valores = data.data.map(function(elemento) {
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
                yMax = (maxValorAux - maxValor) < 600 ? maxValorAux + 1000 : maxValorAux;
            }
            // Crear el gráfico de barras
            var ctx = document.getElementById('tiposEstudiantesTotal').getContext('2d');
            chartTiposEstudiantesTotal = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels.map(function(label, index) {
                        return label;
                    }),
                    datasets: [{
                        label: 'Tipos de esudiantes',
                        data: valores,
                        backgroundColor: ['rgba(74, 72, 72, 1)', 'rgba(223, 193, 78, 1)', 'rgba(208,171,75, 1)',
                            'rgba(186,186,186,1)', 'rgba(56,101,120,1)', 'rgba(229,137,7,1)'
                        ],
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                        }
                    }]
                },
                options: {
                    scales: {
                        y: {
                            max: yMax,
                            beginAtZero: true
                        }
                    },
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        datalabels: {
                            color: 'black',
                            font: {
                                weight: 'light',
                                size: 8
                            },
                            formatter: Math.round
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                },
                plugins: [ChartDataLabels]
            });
            Swal.close();
        }
    });
}

var chartOperadoresTotal;

function graficoOperadoresTotal() {
    
    Swal.fire({
        imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    var data;
    if(filtro.cursos && filtro.cursos.length > 0)
    {
        data = {
            idcurso: filtro.cursos,
            periodos: filtro.periodos
        },
        url = '../home/operadoresCursosTotal'
    }else if (filtro.programa && filtro.programa.length > 0) {
        data = {
            programa: filtro.programa,
            periodos: filtro.periodos
        }
        url = '../planeacion/operadorestotal'
    } else if (filtro.facultades && filtro.facultades.length > 0) {
        data = {
            idfacultad: filtro.facultades,
            periodos: filtro.periodos
        }
        url = '../planeacion/operadorestotal'
    }
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: url,
        data: data,
        success: function(data) {
            try {
                data = jQuery.parseJSON(data);
            } catch {
                data = data;
            }
            var labels = [];
            var valores = [];

            data.data.forEach(function(elemento) {
                if (elemento.operador !== null && elemento.TOTAL !== null && elemento.TOTAL !== undefined && elemento.TOTAL !== 0) {
                    labels.push(elemento.operador);
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
                yMax = (maxValorAux - maxValor) < 600 ? maxValorAux + 1000 : maxValorAux;
            }
            // Crear el gráfico de barras
            var ctx = document.getElementById('operadoresTotal').getContext('2d');
            chartOperadoresTotal = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels.map(function(label, index) {
                        if (label == '') {
                            label = 'IBERO';
                        }
                        return label;
                    }),
                    datasets: [{
                        label: 'Operadores ordenados de forma descendente',
                        data: valores,
                        backgroundColor: ['rgba(74, 72, 72, 1)', 'rgba(223, 193, 78, 1)', 'rgba(208,171,75, 1)',
                            'rgba(186,186,186,1)', 'rgba(56,101,120,1)', 'rgba(229,137,7,1)'
                        ],
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                        }
                    }]
                },
                options: {
                    scales: {
                        y: {
                            max: yMax,
                            beginAtZero: true
                        }
                    },
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        datalabels: {
                            color: 'black',
                            font: {
                                weight: 'light',
                                size: 8
                            },
                            formatter: Math.round
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                },
                plugins: [ChartDataLabels]
            });
            Swal.close();
        }
    });
}

var chartProgramasTotal;

function graficoProgramasTotal() {
    Swal.fire({
        imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    var data;

    if( filtro.cursos && filtro.cursos.length > 0)
    {
        data = {
            idcurso: filtro.cursos,
            periodos: filtro.periodos
        },
        url = '../home/estudiantesProgramasCursosTotal'
    }else if (filtro.programa && filtro.programa.length > 0) {
        data = {
            programa: filtro.programa,
            periodos: filtro.periodos
        }
        url = '../planeacion/estudiantesprogramatotal'
    } else if (filtro.facultades && filtro.facultades.length > 0) {
        data = {
            idfacultad: filtro.facultades,
            periodos: filtro.periodos
        }
        url = '../planeacion/estudiantesprogramatotal'
    }
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: url,
        data: data,
        success: function(data) {
            try{
                data = jQuery.parseJSON(data);
            }
            catch{
                data = data;
            }
            var labels = data.data.map(function(elemento) {
                return elemento.programa;
            });
            var valores = data.data.map(function(elemento) {
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
                yMax = (maxValorAux - maxValor) < 600 ? maxValorAux + 1000 : maxValorAux;
            }
            // Crear el gráfico circular
            var ctx = document.getElementById('programasTotal').getContext('2d');
            chartProgramasTotal = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels.map(function(label, index) {
                        return label;
                    }),
                    datasets: [{
                        label: 'Programas',
                        data: valores,
                        backgroundColor: ['rgba(74, 72, 72, 1)', 'rgba(223, 193, 78, 1)', 'rgba(208,171,75, 1)',
                            'rgba(186,186,186,1)', 'rgba(56,101,120,1)', 'rgba(229,137,7,1)'
                        ],
                        datalabels: {
                            anchor: 'end',
                            align: 'top',
                        }
                    }]
                },
                options: {
                    scales: {
                        y: {
                            max: yMax,
                            beginAtZero: true
                        }
                    },
                    maintainAspectRatio: false,
                    responsive: true,
                    plugins: {
                        datalabels: {
                            color: 'black',
                            font: {
                                weight: 'light',
                                size: 8
                            },
                            formatter: Math.round
                        },
                        legend: {
                            position: 'bottom',
                            labels: {
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                },
                plugins: [ChartDataLabels]
            });
            Swal.close();
        }
    });
}

$('#botondataTable').on("click", function(e) {
    Swal.fire({
        imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    e.preventDefault();
    destruirTable();
    dataTable();
});

var programaMalla = ''
var nombreProgramaMalla = ''

function dataTable() {
    $('#colTabla').removeClass('hidden');
    var table;
    var data;
    var url;

    if(filtro.cursos && filtro.cursos.length > 0)
    {
        data = {
            idcurso: filtro.cursos,
            periodos: filtro.periodos
        },
        url = '../home/tablaProgramasCursos'
        var datos = $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            url: url,
            data: data,
            success: function(data) {
                try {
                    data = parseJSON(data);
                } catch {
                    data = data;
                }
                var dataTableData = [];
                data.forEach(function(value) {
                    var rowData = [
                        value.Codmateria,
                        value.nombreMateria,
                        value.codprograma,
                        value.nombreprograma,
                        value.facultad,
                        value.total,
                        value.sello,
                        value.ASP,
                        value.retencion,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null
                    ];
                    dataTableData.push(rowData);
                })
                    
                
    
                table = $('#datatable').DataTable({
                    "data": dataTableData,
                    'pageLength': 10,
                    "order": [2, 'desc'],
                    "dom": 'Bfrtip',
                    "buttons": [
                        {
                            extend: 'excel',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20] 
                            }
                        },
                        ],
                    "columns": [{
                            title: 'Codigo de Materia'
                        },
                        {
                            title: 'Materia'
                        },
                        {
                            title: 'Codigo programa'
                        },
                        {
                            title: 'Nombreprograma',
                            visible: false
                        },
                        {
                            title: 'Facultad',
                            visible: false
                        },
                        {
                            title: 'Matrículas planeadas',
                            className: 'dt-center'
                        },
                        {
                            title: 'Con Sello Financiero',
                            className: 'dt-center'
                        },
                        {
                            title: 'ASP',
                            className: 'dt-center'
                        },
                        {
                            title: 'Retencion',
                            className: 'dt-center'
                        },
                        {
                            title: 'Id Banner tutor 1',
                            visible: false
                        },
                        {
                            title: 'nombre tutor 1',
                            visible: false
                        },
                        {
                            title: 'correo tutor 1',
                            visible: false
                        },
                        {
                            title: 'cupo tutor 1',
                            visible: false
                        },
                        {
                            title: 'Id Banner tutor 2',
                            visible: false
                        },
                        {
                            title: 'nombre tutor 2',
                            visible: false
                        },
                        {
                            title: 'correo tutor 2',
                            visible: false
                        },
                        {
                            title: 'cupo tutor 2',
                            visible: false
                        },
                        {
                            title: 'Id Banner tutor 3',
                            visible: false
                        },
                        {
                            title: 'nombre tutor 3',
                            visible: false
                        },
                        {
                            title: 'correo tutor 3',
                            visible: false
                        },
                        {
                            title: 'cupo tutor 3',
                            visible: false
                        },
                    ]
                });

                Swal.close();
            }
    
        });


    }else{
        if (filtro.programa.length > 0) {
            data = {
                programa: filtro.programa,
                periodos: filtro.periodos
            }
            url = '../planeacion/tablaprogramas';
        } else if (filtro.facultades.length > 0) {
            data = {
                idfacultad: filtro.facultades,
                periodos: filtro.periodos
            }
            url = '../planeacion/tablafacultad';
        }
        var datos = $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            url: url,
            data: data,
            success: function(data) {
                try {
                    data = parseJSON(data);
                } catch {
                    data = data;
                }
                var dataTableData = [];
                for (const programaKey in data) {
                    if (data.hasOwnProperty(programaKey)) {
                        const programa = data[programaKey];
                        var rowData = [
                            programaKey,
                            programa.programa,
                            programa.Total,
                            programa.Sello,
                            programa.ASP,
                            programa.Retencion,
                        ];
                        dataTableData.push(rowData);
                    }
                }
    
                table = $('#datatable').DataTable({
                    "data": dataTableData,
                    'pageLength': 10,
                    "order": [2, 'desc'],
                    "dom": 'Bfrtip',
                    "buttons": [
                      
                        {
                            extend: 'excel',
                            exportOptions: {
                                columns: [0, 1, 2, 3, 4, 5] 
                            }
                        }
                      
                        ],
                    "columns": [{
                            title: 'Código de programa'
                        },
                        {
                            title: 'Programa'
                        },
                        {
                            title: 'Estudiantes planeados',
                            className: 'dt-center'
                        },
                        {
                            title: 'Con Sello Financiero',
                            className: 'dt-center'
                        },
                        {
                            title: 'ASP',
                            className: 'dt-center'
                        },
                        {
                            title: 'Retencion',
                            className: 'dt-center'
                        },
                        {
                            defaultContent: "<button type='button' id='btn-table' class='estudiantes btn btn-warning' data-toggle='modal' data-target='#modalEstudiantesPlaneados'><i class='fa-regular fa-circle-user'></i></button>",
                            title: 'Estudiantes planeados',
                            className: 'dt-center'
                        },
                        {
                            defaultContent: "<button type='button' id='btn-table' class='malla btn btn-warning' data-toggle='modal' data-target='#modalMallaCurricular'><i class='fa-solid fa-bars'></i></button>",
                            title: 'Malla Curricular',
                            className: 'dt-center'
                        },
                    ]
                });

                function tablaMalla(tbody, table) {
                    Swal.fire({
                        imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                    $(tbody).on("click", "button.malla", function() {
                        Swal.fire({
                            imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        });
                        var datos = table.row($(this).parents("tr")).data();
                        programaMalla = datos[0];
                        nombreProgramaMalla = datos[1];
                        operador = ''
                        mallaPrograma(programaMalla, nombreProgramaMalla, filtro.periodos, operador);
                    })
                }
    
                function tablaEstudiantes(tbody, table) {
                    $(tbody).on("click", "button.estudiantes", function() {
                        Swal.fire({
                            imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        });
                        var datos = table.row($(this).parents("tr")).data();
                        var programaMalla = datos[0];
                        var nombreProgramaMalla = datos[1];
                        estudiantesPlaneados(programaMalla, nombreProgramaMalla);
                    })
                }
    
                tablaMalla("#datatable tbody", table);
                tablaEstudiantes("#datatable tbody", table);
                Swal.close();
            }
    
        });
    }

    

}

function mallaPrograma(programa, nombrePrograma, periodosFiltro, operador) { 
    limpiarModalMalla();
    if(programa == 'PLIV')
    {
        $('#buttonicetex').removeAttr('hidden')
        $('#buttonicetex').show();
    }else{
        $('#buttonicetex').hide();
    }

    $('#tituloMalla').empty();
    $('#tituloMalla').append('Materias programa ' + nombrePrograma);
    var datos = $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "../planeacion/mallaCurricular",
        data: {
            programa: programa,
            periodos: periodosFiltro,
            operador: operador
        },
        method: 'post',
        success: function(data) {
            if(data){
            try {
                data = parseJSON(data);
            } catch {
                data = data;
            }
            var dataTableData = [];
            for (const cursoKey in data) {
                if (data.hasOwnProperty(cursoKey)) {
                    const curso = data[cursoKey];
                    var rowData = [
                        curso.Periodo,
                        nombrePrograma,
                        programa,
                        curso.Semestre,
                        curso.nombreMateria,
                        curso.codMateria,
                        curso.Ciclo,
                        curso.Creditos,
                        curso.Sello,
                        curso.ASP,
                        curso.Retencion,
                        curso.Total,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null,
                        null
                    ];
                    dataTableData.push(rowData);
                }
            }

            table = $('#mallaCurricular').DataTable({
                "dom": 'Bfrtip',
                "data": dataTableData,
                'pageLength': 5,
                "buttons": [
                    {
                        extend: 'excel',
                        title: 'Informe ' + nombrePrograma + ' - Grupos ' + (operador == '' ? 'Ibero' : operador),          
                    },
                ],
                "order": [
                    [1, 'asc'] 
                ],
                "columns": [
                    {
                        title:' Periodo',
                        className: 'dt-center'
                    },
                    {
                        title: 'Programa',
                        visible: false
                    },
                    {
                        title: 'Cod_Progr',
                        visible: false
                    },
                    
                    {
                        title: 'Semestre'
                    },
                    {
                        title: 'Curso',
                    },
                    {
                        title: 'CodMateria'
                    },
                    {
                        title:'Ciclo'
                    }, 
                    {
                        title: 'Creditos',
                        className: 'dt-center'
                    },       
                    {
                        title: 'Sellos',
                        className: 'dt-center'
                    },
                    {
                        title: 'Asp',
                        className: 'dt-center'
                    },
                    {
                        title: 'Retencion',
                        className: 'dt-center'
                    },
                    {
                        title: 'Total',
                        className: 'dt-center'
                    },
                    {
                        title: 'Grupo',
                        render: function(data, type, row) {
                            if (type === 'display') {
                                var totalEst = parseFloat(row[9]) + parseFloat(row[10]);
                                var curso = (totalEst / 85).toFixed(2);
                                return curso;
                            }
                            return data;
                        },
                        visible: false
                    },
                    {
                        title: 'Id Banner tutor 1',
                        visible: false
                    },
                    {
                        title: 'tutor 1',
                        visible: false
                    },
                    {
                        title: 'correo tutor 1',
                        visible: false
                    },
                    {
                        title: 'cupo tutor 1',
                        visible: false
                    },
                    {
                        title: 'Id Banner tutor 2',
                        visible: false
                    },
                    {
                        title: 'tutor 2',
                        visible: false
                    },
                    {
                        title: 'correo tutor 2',
                        visible: false
                    },
                    {
                        title: 'cupo tutor 2',
                        visible: false
                    },
                    {
                        title: 'Id Banner tutor 3',
                        visible: false
                    },
                    {
                        title: 'tutor 3',
                        visible: false
                    },
                    {
                        title: 'correo tutor 3',
                        visible: false
                    },
                    {
                        title: 'cupo tutor 3',
                        visible: false
                    },
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                },
                
            });
            Swal.close();
        }else{
            Swal.fire({
                icon: 'info',
                title: 'No hay datos por mostrar',
                text: 'Por el momento no hay datos disponibles para este operador.',
                confirmButtonColor: '#3085d6',
            });
        }
        }
    });
}

function estudiantesPlaneados(programa, nombrePrograma) {
    limpiarModalEstudiantes();
    $('#tituloEstudiantes').empty();
    $('#estudiantesPlaneados').empty();
    $('#tituloEstudiantes').append('Estudiantes planeados ' + nombrePrograma + ' - ' + programa);
    var mensaje = 'Cargando, por favor espere...';

    $('#estudiantesPlaneados').append(mensaje);
    var datos = $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "../planeacion/estudiantesMateria",
        data: {
            programa: programa
        },
        method: 'post',
        success: function(data) {
            try {
                data = parseJSON(data);
            } catch {
                data = data;
            }
            $('#estudiantesPlaneados').empty();
            tablaEstudiantes = $('#estudiantesPlaneados').DataTable({
                "dom": 'Bfrtip',
                "data": data,
                "buttons": [
                   'excel', 'pdf', 'print'
                ],
                "columns": [{
                        title: 'Codigo Banner',
                        data: 'codBanner'
                    },
                    {
                        title: 'Codigo Materia',
                        data: 'codMateria'
                    },
                    {
                        title: 'Materia',
                        data: 'curso'
                    },
                    {
                        title: 'Créditos',
                        data: 'creditos',
                        className: 'dt-center'
                    },
                    {
                        defaultContent: "<button type='button' id='btn-table' class='datosEstudiante btn btn-warning' data-toggle='modal' data-target='#modaldataEstudiantePlaneacion'><i class='fa-solid fa-folder'></i></button>",
                        title: 'Datos del estudiante',
                        className: 'dt-center'
                    },
                ],
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                },
            });
            function tablaEstudiante(tbody, table) {
                $(tbody).on("click", "button.datosEstudiante", function() {
                   // $(document).find('#cerrarModalPlaneados').click();
                   Swal.fire({
                        imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
                        showConfirmButton: false,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    });
                    var datos = tablaEstudiantes.row($(this).parents("tr")).data();
                    var idBanner = datos['codBanner'];
                    datosEstudiante(idBanner);
                })
            }
            tablaEstudiante("#estudiantesPlaneados tbody", tablaEstudiantes);
            Swal.close();
        }
    });
    
}

function datosEstudiante(idBanner){
    limpiarModalDatosEstudiante();
    var datos = $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: "../planeacion/datosEstudiante",
        data: {
            idBanner: idBanner
        },
        method: 'post',
        success: function(data) {
            try {
                data = parseJSON(data);
            } catch {
                data = data;
            }
            if(data){
            if(data.infoEstudiante){
                /**Información estudiante */
                $('#tituloEstudiantePlaneacion strong').append('Datos estudiante: ' + data.infoEstudiante.Nombre + ' ' + data.infoEstudiante.Apellido + ' - ' + data.infoEstudiante.Id_Banner);
                $('#nombreModalPlaneacion').append('<strong>' + data.infoEstudiante.Nombre + ' ' + data.infoEstudiante.Apellido + '</strong>');
                $('#idModalPlaneacion').append('<strong>' + data.infoEstudiante.Id_Banner + '</strong>');
                $('#facultadModalPlaneacion').append('<strong>' + data.infoEstudiante.Facultad + '</strong>');
                $('#programaModalPlaneacion').append('<strong>' + data.infoEstudiante.Programa + '</strong>');

                /** Segunda Card */
                $('#documentoModalPlaneacion').append('<strong> Documento de identidad: </strong>' + data.infoEstudiante.No_Documento);
                $('#correoModalPlaneacion').append('<strong> Correo institucional: </strong>' + data.infoEstudiante.Email);
                $('#selloModalPlaneacion').append('<strong> Sello financiero: </strong>' + data.infoEstudiante.Sello);
                $('#estadoModalPlaneacion').append('<strong> Estado: </strong>' + data.infoEstudiante.Estado_Banner);
                $('#tipoModalPlaneacion').append('<strong> Tipo estudiante: </strong>' + data.infoEstudiante.Tipo_Estudiante);
                if (data.infoEstudiante.Autorizado_ASP !== undefined && data.infoEstudiante.Autorizado_ASP !== null) {
                    $('#autorizadoModal').append('<strong> Autorizado: </strong>' + data.infoEstudiante.Autorizado_ASP);
                }

                if (data.infoEstudiante.Operador !== undefined && data.infoEstudiante.Operador !== null) {
                    $('#operadorModal').append('<strong> Operador: </strong>' + data.infoEstudiante.Operador);
                }
                $('#convenioModal').append('<strong> Convenio: </strong>' + data.infoEstudiante.Convenio);
            }
            else{
                // Swal.fire({
                //     icon: 'info',
                //     text: 'No hay datos disponibles',
                //     confirmButtonColor: '#3085d6',
                // }).then((result) => {
                //     if (result.isConfirmed) {
                //         // Cierra el modal con el ID modaldataEstudiante
                //         $('#modaldataEstudiante').modal('hide');
                //     }
                // });
            }
                /** DataTable Moodle */    
                $('#datatableMoodle').empty();
                if(data.materiasMoodle.length>0){
                    tablaMateriasMoodle = $('#datatableMoodle').DataTable({
                    "data": data.materiasMoodle,
                    "order": [3, 'asc'],
                    "columns": [{
                            title: 'Código Materia',
                            data: 'codigoMateria'
                        },
                        {
                            title: 'Materia',
                            data: 'materia'
                        },
                        {
                            title: 'Créditos',
                            data: 'creditos',
                            className: 'dt-center'
                        },
                        {
                            title: 'Semestre',
                            data:'semestre',
                            className: 'dt-center'
                        }
                    ],
                    "footer": true,
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                    },
                    "searching": false,
                    });
        
                    $('#datatableMoodle').append('<h6>No hay datos de Moodle</h6>')
                }   
                $('#totalCredMoodle').append('Total Créditos Moodle: ' + data.totalMoodle);
                
                $('#datatablePlaneacion').empty();
                if(data.materiasPlaneadas.length>0){
                    tablaMateriasPlaneacion = $('#datatablePlaneacion').DataTable({
                    "data": data.materiasPlaneadas,
                    "order": [[3, 'desc'], [4, 'desc'], [5, 'desc']],
                    "columns": [
                        {
                            title: 'Código Materia',
                            data: 'codigoCurso'
                        },
                        {
                            title: 'Materia',
                            data: 'curso'
                        },
                        {
                            title: 'Créditos',
                            data: 'creditos',
                            className: 'dt-center'
                        },
                        {
                            data:'orden',
                            visible:false
                        },
                        {
                            data:'ciclo',
                            visible:false
                        },
                        {
                            title: 'Semestre',
                            data:'semestre',
                            className: 'dt-center'
                        }
                    ],
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                    },
                    "searching": false,
                    
                    });
                }else{
                    $('#datatablePlaneacion').append('<h6>No hay datos de Planeación</h6>')
                }
                $('#totalCredPlaneacion').append('Total Créditos Planeación: ' + data.totalPlaneacion);

                $('#datatableHistorial').empty();
                if(data.historialAcademico.length>0){
                    tablaMateriasHistorial = $('#datatableHistorial').DataTable({
                        "data": data.historialAcademico,
                        "order": [3, 'asc'],
                        "columns": [
                            
                            {
                                title: 'Código Materia',
                                data: 'id_curso'
                            },
                            {
                                title: 'Materia',
                                data: 'materia'
                            },
                            {
                                title: 'Créditos',
                                data: 'creditos',
                                className: 'dt-center'
                            },
                            {
                                title: 'Semestre',
                                data: 'semestre',
                                className: 'dt-center'
                            },
                            {
                                title: 'Nota',
                                data: 'calificacion',
                                className: 'dt-center'
                            }
                        ],
                        "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                    });
                }else{
                    $('#datatableHistorial').append('<h6>No hay datos de Historial Académico</h6>')
                }
                $('#totalCredHistorial').append('Total Créditos Historial: ' + data.totalHistorial);

                Swal.close();
        }
        else{
                Swal.close();
                Swal.fire({
                        icon: 'info',
                        title:'No hay datos disponibles',
                        confirmButtonColor: '#3085d6',
                    })
                $(document).find('#cerrarModal').click();    
            }
        }   
    });  
          
}

function limpiarModalDatosEstudiante(){
    $('#tituloEstudiantePlaneacion strong, #nombreModalPlaneacion, #idModalPlaneacion, #facultadModalPlaneacion, #programaModalPlaneacion, #documentoModalPlaneacion, #correoModalPlaneacion, #selloModalPlaneacion, #estadoModalPlaneacion, #tipoModalPlaneacion, #autorizadoModalPlaneacion, #operadorModalPlaneacion, #convenioModalPlaneacion, #tabla tbody, #totalCredMoodle, #totalCredPlaneacion, #totalCredHistorial').empty();
          
    if ($.fn.DataTable.isDataTable('#datatableMoodle')) {
        $("#datatableMoodle").remove();
        tablaMateriasMoodle.destroy();
        $('#datatableMoodle').DataTable().destroy();
        $('#datatableMoodle thead').empty();
        $('#datatableMoodle tbody').empty();
        $('#datatableMoodle tfooter').empty();
    }

    if ($.fn.DataTable.isDataTable('#datatablePlaneacion')) {
        $("#datatablePlaneacion").remove();
        tablaMateriasPlaneacion.destroy();
        $('#datatablePlaneacion').DataTable().destroy();
        $('#datatablePlaneacion thead').empty();
        $('#datatablePlaneacion tbody').empty();
        $('#datatablePlaneacion tfooter').empty();
    }

    if ($.fn.DataTable.isDataTable('#datatableHistorial')) {
        $("#datatableHistorial").remove();
        tablaMateriasHistorial.destroy();
        $('#datatableHistorial').DataTable().destroy();
        $('#datatableHistorial thead').empty();
        $('#datatableHistorial tbody').empty();
        $('#datatableHistorial tfooter').empty();
    }

}

function limpiarModalMalla() {
    if ($.fn.DataTable.isDataTable('#mallaCurricular')) {
        $("#mallaCurricular").remove();
        table.destroy();
        $('#mallaCurricular').DataTable().destroy();
        $('#mallaCurricular thead').empty();
        $('#mallaCurricular tbody').empty();
        $('#mallaCurricular tfooter').empty();
    }
}

function limpiarModalEstudiantes() {
    if ($.fn.DataTable.isDataTable('#estudiantesPlaneados')) {
        $("#estudiantesPlaneados").remove();
        tablaEstudiantes.destroy();
        $('#estudiantesPlaneados').DataTable().destroy();
        $('#estudiantesPlaneados thead').empty();
        $('#estudiantesPlaneados tbody').empty();
        $('#estudiantesPlaneados tfooter').empty();
    }
}

function limpiarModalBuscador() {
    $('#primerApellido').empty();
    $('#Sello').empty();
    $('#Operador').empty();
    $('#tipEstudiante').empty();
    $('#tituloTablaBuscar').addClass('hidden');
    if ($.fn.DataTable.isDataTable('#buscarEstudiante')) {
        $("#buscarEstudiante").remove();
        estudiante.destroy();
        $('#buscarEstudiante').DataTable().destroy();
        $('#buscarEstudiante thead').empty();
        $('#buscarEstudiante tbody').empty();
        $('#buscarEstudiante tfooter').empty();
    }
}

function destruirTable() {
    $('#colTabla').addClass('hidden');
    if ($.fn.DataTable.isDataTable('#datatable')) {
        $('#datatable').dataTable().fnDestroy();
        $('#datatable thead').empty();
        $('#datatable tbody').empty();
        $('#datatable tfooter').empty();
        $("#datatable tbody").off("click", "button.malla");
        $("#datatable tbody").off("click", "button.estudiantes");
        $("#datatable tbody").off("click", "button.buscar");
    }
}

$("#formBuscar").submit(function(e) {
    limpiarModalBuscador();
    e.preventDefault();
    var id = $("#idBanner").val();
    var url, data;
    data = {
        id: id,
        programa: programaEstudiante
    };
    url = "../planeacion/materiasEstudiante";
    var datos = $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: url,
        data: data,
        success: function(data) {
            try {
                data = parseJSON(data);
            } catch {
                data = data;
            }
            if (data.length === 0) {
                $('#divTablaBuscador').empty();
                $('#divTablaBuscador').append('<h5 class="text-center"><strong>No hay datos por mostrar</strong></h5><br><h6>Verifica si el estudiante pertenece a este programa y si se encuentra programado para este ciclo. </h6>');
            } else {

                if (data.materias == 'Vacio') {
                    $('#divTablaBuscador').empty();
                    $('#dataEstudiante').removeClass('hidden');
                    $('#primerApellido').append('Primer Apellido: ' + data.estudiante.primer_apellido);
                    $('#Sello').append('Sello financiero: ' + data.estudiante.sello);
                    $('#Operador').append('Operador: ' + data.estudiante.operador);
                    $('#tipEstudiante').append('Tipo estudiante: ' + data.estudiante.tipoestudiante);
                    $('#divTablaBuscador').append('<h5 class="text-center"><strong>- por mostrar</strong></h5><br><h6>El estudiante si pertenece a este programa pero no tiene materias planeadas. </h6>');
                } else {    
                    $('#divTablaBuscador').show();
                    ['#primerApellido', '#Sello', '#Operador', '#tipEstudiante'].forEach(selector => {
                        $(selector).empty();
                    });
                    $('#dataEstudiante').removeClass('hidden');
                    $('#tituloTablaBuscar').removeClass('hidden');
                    
                    if (data.estudiante.primer_apellido !== undefined && data.estudiante.primer_apellido !== null) {
                        $('#primerApellido').append('Primer Apellido: ' + data.estudiante.primer_apellido);
                    }

                    if (data.estudiante.sello !== undefined && data.estudiante.sello !== null) {
                        $('#Sello').append('Sello financiero: ' + data.estudiante.sello);
                    }

                    if (data.estudiante.operador !== undefined && data.estudiante.operador !== null) {
                        $('#Operador').append('Operador: ' + data.estudiante.operador);
                    }

                    if (data.estudiante.tipoestudiante !== undefined && data.estudiante.tipoestudiante !== null) {
                        $('#tipEstudiante').append('Tipo estudiante: ' + data.estudiante.tipoestudiante);
                    }

                    var dataTableData = [];
                    data.materias.forEach(function(curso) {

                            var rowData = [
                                curso.codMateria,
                                curso.curso,
                                curso.semestre,
                            ];
                            dataTableData.push(rowData);
                    });

                    estudiante = $('#buscarEstudiante').DataTable({
                        "data": dataTableData,
                        "pageLength": 10,
                        "columns": [
                            {
                                title: 'Código de materia',
                            },
                            {
                                title: 'Nombre materia',
                            },
                            {
                                title: 'Semestre',
                                className: 'dt-center'
                            },
                        ],
                            "language": {
                            "url": "//cdn.datatables.net/plug-ins/1.10.15/i18n/Spanish.json"
                        },
                    });
                }
            }
        }
    });
});

$(document).on('click', '.modal_dta_estudiante', function() {
    $('#modaldataEstudiante').modal('hide');
});

$('#buscarEstudiantePlaneacion').on('click', function(e){
    Swal.fire({     
        imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    e.preventDefault();
    var inputValue = $('#buscarId').val();
    limpiarModalDatosEstudiante();
    datosEstudiante(inputValue);
});


$('.operadorplaneacion').on('click', function(){
    Swal.fire({
        imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false
    });
    operador = $(this).data('operador');
    limpiarModalMalla()
    mallaPrograma(programaMalla, nombreProgramaMalla, filtro.periodos, operador) 
});
