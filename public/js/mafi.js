//alertaAsp();
var contadorGraficos = [];
            var contadorMafi = 0;            
            var contadorGraficoMetas = 0;

            $(document).find('#Admisiones').addClass('activo');

            var chartEstudiantes;
            function graficoEstudiantes(filtros) {
                var url, data;
                if (filtros.programa && filtros.programa.length > 0) {
                    url = "../home/estudiantesPrograma",
                    data = {
                        programa: filtros.programa,
                        periodos: filtros.periodos
                    }
                } else if (filtros.facultades && filtros.facultades.length > 0) {
                    url = "../home/estudiantesFacultad",
                    data = {
                        idfacultad: filtros.facultades,
                        periodos: filtros.periodos
                    }
                }
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'post',
                    url: url,
                    data: data,
                    success: function (data) {
                        try {
                            data = jQuery.parseJSON(data);
                        } catch {
                            data = data;
                        }
                        var labels = data.data.map(function (elemento) {
                            return elemento.estado;
                        });

                        var valores = data.data.map(function (elemento) {
                            return parseFloat(elemento.TOTAL);
                        });

                        var suma = valores.reduce(function (acumulador, valorActual) {
                            return acumulador + valorActual;
                        }, 0);

                        // Crear el gráfico circular
                        var ctx = document.getElementById('estudiantes').getContext('2d');
                        chartEstudiantes = new Chart(ctx, {
                            type: 'pie',
                            data: {
                                labels: labels.map(function (label, index) {
                                    label = label.toUpperCase();
                                    if (label != 'TOTAL') {
                                        return label + 'S: ' + valores[index];
                                    } else {
                                        return label + ': ' + suma;
                                    }
                                }),
                                datasets: [{
                                    label: 'Gráfico Circular',
                                    data: valores,
                                    backgroundColor: ['rgba(223, 193, 78, 1)', 'rgba(74, 72, 72, 0.5)']
                                }]
                            },
                            options: {
                                maintainAspectRatio: false,
                                responsive: true,
                                plugins: {
                                    datalabels: {
                                        color: 'black',
                                        font: {
                                            weight: 'bold',
                                            size: 12
                                        },
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
                                        text: 'TOTAL ESTUDIANTES: ' + suma,
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
                        if (chartEstudiantes.data.labels.length == 0 && chartEstudiantes.data.datasets[0].data.length == 0) {
                            $('#colEstudiantes').addClass('hidden');
                            contadorGraficos.push(0);
                        } else {
                            $('#colEstudiantes').removeClass('hidden');
                            contadorGraficos.push(1);
                        }
                    }
                })
            }

            var chartSelloPrimerIngreso;
            var chartEstudiantesActivos;
            function graficoSelloFinanciero(filtros) {
                var url, data;
                if (filtros.programa && filtros.programa.length > 0) { 
                    url = "../home/estudiantesSelloPrograma/" + tabla,
                    data = {
                            programa: filtros.programa,
                            periodos: filtros.periodos
                        }
                } else if(filtros.facultades && filtros.facultades.length > 0){
                    url = "../home/estudiantesSelloFacultad/" + tabla,
                    data = {
                            idfacultad: filtros.facultades,
                            periodos: filtros.periodos
                        }
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
                            $('#colSelloFinanciero').addClass('hidden');
                        } else {
                            // Crear el gráfico circular
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
                                        textMargin: 2
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
                })
            }

            var chartRetencion;
            function graficoRetencion(filtros){
                var url, data;
                if (filtros.programa && filtros.programa.length > 0) {   
                    url = "../home/estudiantesRetencionPrograma/" + tabla,
                    data = {
                            programa: filtros.programa,
                            periodos: filtros.periodos
                        }
                } else if(filtros.facultades && filtros.facultades.length > 0){
                    url = "../home/estudiantesRetencionFacultad/" + tabla,
                    data = {
                            idfacultad: filtros.facultades,
                            periodos: filtros.periodos
                        }
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
                            if (elemento.autorizado_asistir !== null && elemento.TOTAL !== null && elemento.TOTAL !== undefined && elemento.TOTAL !== 0) {
                                if (elemento.autorizado_asistir.startsWith('ACTIVO EN ')) {
                                    labels.push(elemento.autorizado_asistir.replace('ACTIVO EN ', '').trim());
                                } else {
                                    labels.push(elemento.autorizado_asistir);
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
                                        label = 'RETENCION';
                                        return label;
                                    }else
                                    {
                                        return 'ACT. ' +label + ':' + valores[index];
                                    }
                                    
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

            var chartTipoEstudiante;
            function graficoTipoDeEstudiante(filtros){
                var url, data;
                if (filtros.programa && filtros.programa.length > 0) {   
                    url = "../home/tiposPrograma/" + tabla,
                    data = {
                            programa: filtros.programa,
                            periodos: filtros.periodos
                        }
                } else if(filtros.facultades && filtros.facultades.length > 0){
                    url = "../home/tiposEstudiantes/" + tabla,
                    data = {
                            idfacultad: filtros.facultades,
                            periodos: filtros.periodos
                        }
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
                            return elemento.tipoestudiante;
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
                                labels: labels.map(function(label, index) {
                                    if (label.includes("ESTUDIANTE ")) {
                                        label = label.replace(/ESTUDIANTE\S*/i, "");
                                    }
                                    return label;
                                }),
                                datasets: [{
                                    label: '',
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

            function graficoSelloPrimerIngreso(filtros){
                var url, data;
                if (filtros.programa && filtros.programa.length > 0) {   
                    url = "../home/estudiantesPrimerIngresoPrograma/" + tabla,
                    data = {
                            programa: filtros.programa,
                            periodos: filtros.periodos
                        }
                } else if(filtros.facultades && filtros.facultades.length > 0){
                    url = "../home/estudiantesPrimerIngresoFacultad/" + tabla,
                    data = {
                            idfacultad: filtros.facultades,
                            periodos: filtros.periodos
                        }
                }
                //console.log(url);
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
            function graficoSelloAntiguos(filtros){
                var url, data;
                if (filtros.programa && filtros.programa.length > 0) {   
                    url = "../home/estudiantesAntiguosPrograma/" + tabla,
                    data = {
                            programa: filtros.programa,
                            periodos: filtros.periodos
                        }
                } else if(filtros.facultades && filtros.facultades.length > 0){
                    url = "../home/estudiantesAntiguosFacultad/" + tabla,
                    data = {
                            idfacultad: filtros.facultades,
                            periodos: filtros.periodos
                        }
                }
                //console.log(url);
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

            var chartOperadores;
            function graficoOperadores(filtros){
                var url, data;
                if (filtros.programa && filtros.programa.length > 0) {   
                    url = "../home/operadoresPrograma/" + tabla,
                    data = {
                            programa: filtros.programa,
                            periodos: filtros.periodos
                        }
                } else if(filtros.facultades && filtros.facultades.length > 0){
                    url = "../home/operadoresFacultad/" + tabla,
                    data = {
                            idfacultad: filtros.facultades,
                            periodos: filtros.periodos
                        }
                }
                //console.log(url);
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
            function graficoProgramas(filtros){
                var url, data;
                if (filtros.programa && filtros.programa.length > 0) {   
                    url = "../home/estudiantesPorPrograma/" + tabla,
                    data = {
                            programa: filtros.programa,
                            periodos: filtros.periodos
                        }
                } else if(filtros.facultades && filtros.facultades.length > 0){
                    url = "../home/estudiantesProgramasFacultad/" + tabla,
                    data = {
                            idfacultad: filtros.facultades,
                            periodos: filtros.periodos
                        }
                }
                //console.log(url);
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
                            return elemento.codprograma;
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
                        }
                        Swal.close();
                    }
                });
            }

            function graficoMetas(filtros){
                var url, data;
                if (filtros.programa && filtros.programa.length > 0) {   
                    url = "../home/mafi/graficoMetasPrograma",
                    data = {
                            programa: filtros.programa,
                            periodos: filtros.periodos
                        }
                } else if(filtros.facultades && filtros.facultades.length > 0){
                    url = "../home/mafi/graficoMetasFacultad",
                    data = {
                            idfacultad: filtros.facultades,
                            periodos: filtros.periodos
                        }
                }
                //console.log(url);
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
                        let sumValues = 0;
                        let sumValuesSello = 0;
                       
                        if (data.metas != null && data.metas != undefined) { 
                        contadorGraficoMetas = 1;               
                        var labels = [];
                        var values = [];
                        var valuesSello = [];
                        var valuesRetencion = [];
                        Object.keys(data.metas).forEach(meta => {
                            labels.push(meta);
                            values.push(data.metas[meta]);
                            valuesSello.push(data.matriculaSello[meta]);
                            valuesRetencion.push(data.matriculaRetencion[meta]);
                            const value = parseFloat(data.metas[meta]);
                            sumValues += isNaN(value) ? 0 : value;
                            const valueSello = parseFloat(data.matriculaSello[meta]);
                            sumValuesSello += isNaN(valueSello) ? 0 : valueSello;
                        });
                    
                        var ctx = document.getElementById('graficoMetas').getContext('2d');
                        chartMetas = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                        label: 'Sello',
                                        data: valuesSello,
                                        backgroundColor: ['rgba(223, 193, 78, 1)'],
                                        datalabels: {
                                            anchor: 'middle',
                                            align: 'center'
                                        },
                                        stack: 'Stack 0',
                                    },
                                    {
                                        label: 'Metas',
                                        data: values,
                                        backgroundColor: ['rgba(186,186,186,1)'],
                                        datalabels: {
                                            anchor: 'end',
                                            align: 'top',
                                        },
                                        stack: 'Stack 0',
                                    },
                                ]
                            },
                            options: {
                                maintainAspectRatio: false,
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        stacked: false,
                                    }
                                },
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
                        if (chartMetas.data.labels.length == 0 && chartMetas.data.datasets[0].data.length == 0) {
                            $('#colMetas').addClass('hidden');
                            contadorGraficos.push(0);
                        } else {
                            $('#colMetas').removeClass('hidden');
                            contadorGraficos.push(1);
                        }
                    }
                    else {
                        contadorGraficoMetas = 0;
                        $('#colMetas').addClass('hidden');  
                    }
                       
                    }
                });
            }

            $('#descargarMafi').on('click', function(e) {
                //console.log(filtro);
                Swal.fire({
                    title: 'Descargar datos',
                    text: "La datos generados se actualizan una vez al día, para obtener la información completamente actualizada dirigete directamente a Banner",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Descargar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        Swal.fire({
                            title: "Descargando...",
                            text: "Descargando la información solictada, este proceso puede tardar unos segundos dependiendo de tu conexión a internet.",
                            imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        });
                        ExcelBanner(filtro);
                    }
                })
            });

            function ExcelBanner(filtros){
                var url, data;
                if (filtros.programa && filtros.programa.length > 0) {   
                    url = "../home/dataMafiPrograma",
                    data = {
                            programa: filtros.programa,
                            periodos: filtros.periodos
                        }
                } else if(filtros.facultades && filtros.facultades.length > 0){
                    url = "../home/dataMafiFacultad",
                    data = {
                            idfacultad: filtros.facultades,
                            periodos: filtros.periodos
                        }
                }
                // console.log(url);
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
                        var newData = [];
                        var headers = ['Id Banner', 'Documento' , 'Nombre completo', 'Codigo Programa', 'Estado', 'Sello', 'Tipo de estudiante', 'Periodo'];
                        newData.push(headers);
                        data.forEach(function(item) {
                            var fila = [
                                item.IDBANNER,
                                item.IDENTIFICACION,
                                item.PRIMER_NOMBRE + ' ' + item.SEGUNDO_NOMBRE + ' '+ item.PRIMER_APELLIDO + ' ' +item.SEGUNDO_APELLIDO,
                                item.CODPROGRAMA,
                                item.ESTADO,
                                item.SELLO,
                                item.TIPOESTUDIANTE,
                                item.PERIODO
                            ];
                            newData.push(fila);
                        });
                        var wb = XLSX.utils.book_new();
                        var ws = XLSX.utils.aoa_to_sheet(newData);
                        XLSX.utils.book_append_sheet(wb, ws, "Informe");
                        XLSX.writeFile(wb, "informe banner.xlsx");
                        Swal.close();
                    }
                });
            }

            /**Modal estudiantes*/

            var chartTiposEstudiantesTotal
            $('#botonModalTiposEstudiantes').on("click", function(e) {
                e.preventDefault();
                if (chartTiposEstudiantesTotal) {
                    chartTiposEstudiantesTotal.destroy();
                }
                // periodos = filtro.periodos;
                // console.log(periodos);
                tiposEstudiantesTotal(filtro);
            });

            function tiposEstudiantesTotal(filtros) {
                Swal.fire({      
                            imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
                            showConfirmButton: false,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        });
                var url, data;
                if (filtros.programa && filtros.programa.length > 0) {   
                    url = "../home/tiposEsudiantesProgramaTotal/" + tabla,
                    data = {
                            programa: filtros.programa,
                            periodos: filtros.periodos
                        }
                } else if(filtros.facultades && filtros.facultades.length > 0){
                    url = "../home/tiposEsudiantesProgramaTotal/" + tabla,
                    data = {
                            idfacultad: filtros.facultades,
                            periodos: filtros.periodos
                        }
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
                            return elemento.tipoestudiante;
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

            /**Modal grafica operadores*/
            var chartOperadoresTotal;
            $('#botonModalOperador').on("click", function(e) {
                e.preventDefault();
                if (chartOperadoresTotal) {
                    chartOperadoresTotal.destroy();
                }
                graficoOperadoresTotal(filtro);
            });

            function graficoOperadoresTotal(filtros) {
                console.log(filtros);
                Swal.fire({                 
                    imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
                var url, data;
                if (filtros.programa && filtros.programa.length > 0) {   
                    url = "../home/operadoresProgramaTotal/" + tabla,
                    data = {
                            programa: filtros.programa,
                            periodos: filtros.periodos
                        }
                } else if(filtros.facultades && filtros.facultades.length > 0){
                    url = "../home/operadoresFacultadTotal/" + tabla,
                    data = {
                            idfacultad: filtros.facultades,
                            periodos: filtros.periodos
                        }
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

            //Modal grafico programas
            var chartProgramasTotal;
            $('#botonModalProgramas').on("click", function(e) {
                e.preventDefault();
                if (chartProgramasTotal) {
                    chartProgramasTotal.destroy();
                }
                graficoProgramasTotal(filtro);
            });

            function graficoProgramasTotal(filtros) {
                Swal.fire({
                    imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
                var url, data;
                if (filtros.programa && filtros.programa.length > 0) {   
                    url = "../home/estudiantesPorProgramaTotal/" + tabla,
                    data = {
                            programa: filtros.programa,
                            periodos: filtros.periodos
                        }
                } else if(filtros.facultades && filtros.facultades.length > 0){
                    url = "../home/estudiantesFacultadTotal/" + tabla,
                    data = {
                            idfacultad: filtros.facultades,
                            periodos: filtros.periodos
                        }
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
                            return elemento.codprograma;
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

            /** modal graficos metas*/
            var chartMetasTotal;
            $('#botonModalMetas').on("click", function(e) {
                e.preventDefault();
                if (chartMetasTotal) {
                    chartMetasTotal.destroy();
                }
                graficoMetasTotal(filtro);
            });

            var chartMetas;
            function graficoMetasTotal(filtros) {
                Swal.fire({       
                    imageUrl: "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
                var url, data;
                if (filtros.programa && filtros.programa.length > 0) {   
                    url = "../home/mafi/graficoMetasProgramasTotal",
                    data = {
                            programa: filtros.programa,
                            periodos: filtros.periodos
                        }
                } else if(filtros.facultades && filtros.facultades.length > 0){
                    url = "../home/mafi/graficoMetasFacultadTotal",
                    data = {
                            idfacultad: filtros.facultades,
                            periodos: filtros.periodos
                        }
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
                        var values = [];
                        var valuesSello = [];
                        var valuesRetencion = [];
                        let sumValues = 0;
                        let sumValuesSello = 0;

                        Object.keys(data.metas).forEach(meta => {
                            labels.push(meta);
                            values.push(data.metas[meta]);
                            valuesSello.push(data.matriculaSello[meta]);
                            valuesRetencion.push(data.matriculaRetencion[meta]);
                            const value = parseFloat(data.metas[meta]);
                            sumValues += isNaN(value) ? 0 : value;
                            const valueSello = parseFloat(data.matriculaSello[meta]);
                            sumValuesSello += isNaN(valueSello) ? 0 : valueSello;
                        });

                        const porcentajeCumplimiento = ((sumValuesSello/sumValues)*100).toFixed(2);
                        
                        $('#metascumplidastotales').empty();
                        $('#metascumplidastotales').append('Metas totales: ' + sumValues + ' Porcentaje cumplido: ' +  porcentajeCumplimiento + '%');
                        var ctx = document.getElementById('metasTotal').getContext('2d');
                        chartMetasTotal = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: labels,
                                datasets: [{
                                        label: 'Sello',
                                        data: valuesSello,
                                        backgroundColor: ['rgba(223, 193, 78, 1)'],
                                        datalabels: {
                                            anchor: 'middle',
                                            align: 'center'
                                        },
                                        stack: 'Stack 0',
                                    },

                                    {
                                        label: 'Metas',
                                        data: values,
                                        backgroundColor: ['rgba(186,186,186,1)'],
                                        datalabels: {
                                            anchor: 'end',
                                            align: 'top',
                                        },
                                        stack: 'Stack 0',
                                    },
                                ]
                            },
                            options: {
                                maintainAspectRatio: false,
                                responsive: true,
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        stacked: false,
                                    }
                                },
                                plugins: {
                                    formatter: function(value, context) {
                                        if (context.dataset.label == 'Retencion' && value == 0) {
                                            return '';
                                        }
                                    },
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

            $("#generarExcel").on("click", function() {
                var url, data;
                filtros = filtro;
                if (filtros.programa && filtros.programa.length > 0) {   
                    url = "../home/mafi/graficoMetasProgramasTotal",
                    data = {
                            programa: filtros.programa,
                            periodos: filtros.periodos
                        }
                } else if(filtros.facultades && filtros.facultades.length > 0){
                    url = "../home/mafi/graficoMetasFacultadTotal",
                    data = {
                            idfacultad: filtros.facultades,
                            periodos: filtros.periodos
                        }
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
                        var newData = [];
                        var headers = ["Codigo Programa", "Programa", "Meta", "Sello", "% Ejecución"];

                        var col1 = [];
                        var col2 = [];
                        var col3 = [];
                        var col4 = [];

                        Object.keys(data.metas).forEach(meta => {
                            col1.push(meta);
                            col2.push(data.nombres[meta]);
                            col3.push(data.metas[meta]);
                            col4.push(data.matriculaSello[meta]);
                        });

                        var porcentaje;
                        newData.push(headers);
                        for (var i = 0; i < col1.length; i++) {
                            porcentaje = (((col4[i]) / col3[i]) * 100).toFixed(2);
                            if (porcentaje > 100) {
                                porcentaje = 'Meta Superada';
                            }

                            var row = [col1[i], col2[i], col3[i], col4[i], porcentaje];
                            newData.push(row);
                        }
                        var wb = XLSX.utils.book_new();
                        var ws = XLSX.utils.aoa_to_sheet(newData);
                        XLSX.utils.book_append_sheet(wb, ws, "Metas");

                        // Generar el archivo Excel y descargarlo
                        XLSX.writeFile(wb, "Metas.xlsx");
                    }
                });
            });

            function destruirGraficos() {

                if (chartEstudiantesActivos !== undefined || chartEstudiantesActivos) {
                    chartEstudiantes.destroy();
                }
                
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

                if (chartMetas !== undefined || chartMetas) {
                    chartMetas.destroy();
                }

            }

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

          