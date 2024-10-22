
$(document).ready(function() {
    var chartEstudiantes;
  
});


/**
 * Método que muestra el total de estudiantes activos e inactivos
 */
 

 function graficoEstudiantes(filtro) {
    //alert('entro');
    var url, data;

     if (programasSeleccionados.length > 0 && programasSeleccionados.length < totalProgramas) {
         url = "{{ route('estudiantes.activos.programa') }}",
             data = {
                 programa: programasSeleccionados,
                 periodos: periodosSeleccionados
             }
     } else {
         if (facultadesSeleccionadas.length > 0) {
             url = "{{ route('estudiantes.activos.facultad') }}",
                 data = {
                     idfacultad: facultadesSeleccionadas,
                     periodos: periodosSeleccionados
                 }
         } else {
             url = "{{ route('estudiantes.activos') }}",
                 data = ''
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
                 return elemento.estado;
             });

             var valores = data.data.map(function(elemento) {
                 return parseFloat(elemento.TOTAL);
             });

             var suma = valores.reduce(function(acumulador, valorActual) {
                 return acumulador + valorActual;
             }, 0);
             
             // Crear el gráfico circular
             var ctx = document.getElementById('estudiantes').getContext('2d');
             chartEstudiantes = new Chart(ctx, {
                 type: 'pie',
                 data: {
                     labels: labels.map(function(label, index) {
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
     });
 }