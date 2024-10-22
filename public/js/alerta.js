function alertaAsp() {
    Swal.fire({
        title: "Información Asp",
        text: "Marcación Aps",
        icon: "info",
        showCloseButton: true,
        showConfirmButton: true,
        confirmButtonColor: "#3085d6",
        html: `Apreciado usuario. 
        <p>Se les informa que esta en proceso de marcacion 3736 ASP se espera se verán en el transcurso del dia en hermes.</p>`,
    });
}


function Actualizacion_datos() {
    Swal.fire({
        title: "Actualizacion de Datos",
        text: "Actualizacion de Datos",
        icon: "info",
        showCloseButton: true,
        showConfirmButton: true,
        confirmButtonColor: "#3085d6",
        html: `Apreciado usuario. 
        <p>Se les informa que esta en proceso de marcacion 3736 ASP se espera se verán en el transcurso del dia en hermes.</p>`,
    });
}


function alertaPlaneacionprimerIngreso() {
    Swal.fire({
        title: "Sin datos",
        text: "Alerta modúlo planeación",
        icon: "info",
        showCloseButton: true,
        showConfirmButton: true,
        confirmButtonColor: "#3085d6",
        html: `Apreciado usuario. 
        <p>En este momento se están haciendo las revisiones de la planeación generada por Hermes del primer ingreso del 2024, una vez finalizada la información será publicada.</p>
        <p>Los datos asociados a las proyecciones estarán disponibles a partir del 25 julio y la planeacion final sera visible  desde el 16 de agosto.</p>`,
    });
}
function alertaPlaneacionsegundoIngreso() {
    Swal.fire({
        title: "Inicio de Planeacion",
        text: "Alerta modúlo planeación",
        icon: "info",
        showCloseButton: true,
        showConfirmButton: true,
        confirmButtonColor: "#3085d6",
        html: `Apreciado usuario. 
        <p>En este momento te informamos que la planificación del primer ingreso estará disponible hasta el día jueves 21 de marzo. Después de esa fecha, no habrá información disponible debido a que Hermes está próximo a iniciar las proyecciones del siguiente período y/o ciclo.</p>
        <p>Los datos asociados a las proyecciones del segundo ingreso serán visibles desde el día 25 de julio.</p>`,
    });
}

function alertaMAlertas() {
    Swal.fire({
        title: "Sin datos",
        text: "Alerta modúlo alertas tempranas",
        icon: "info",
        showCloseButton: true,
        showConfirmButton: true,
        confirmButtonColor: "#3085d6",
        html: `Apreciado usuario. 
        <p>En este momento no tenemos datos disponibles en el módulo de alertas tempranas debido a que Hermes está próximo a iniciar las proyecciones del siguiente periodo y/o ciclo en las cuales se identifican las alertas de los estudiantes activos.</p>
        <p>Los datos asociados a las proyecciones del 25 de julio estarán disponibles a partir del 16 de agosto.</p>`,
    });
}

function alertaAdmisones() {
    Swal.fire({
        title: "Sin datos",
        text: "Alerta modúlo admisones",
        icon: "info",
        showCloseButton: true,
        showConfirmButton: true,
        confirmButtonColor: "#3085d6",
        html: `Apreciado usuario. 
        <p>Hermes se permite informar que se ha habilitado la información del cuarto ingreso del 2024. Para ver las admisiones relacionadas, por favor seleccione los periodos correspondientes (202407, 202416, 202434, 202444 y 202455). </p>
        <p>Próximamente la información asociada a los periodos de agosto no será visible toda vez que las admisiones de estos periodos ya cerraron.</p>`,
    });
}

function alerta_seleccione_periodo() {
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "Verifica que tengas al menos un periodo activo o que estés seleccionado algún periodo.",
        confirmButtonColor: "#dfc14e",
    });
}

function alertaPreload() {
    Swal.fire({
        imageUrl:
            "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
        showConfirmButton: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
    });
}

function alertaPlaneacionUnaVezDia() {
    Swal.fire({
        icon: "info",
        title: "Actualización datos",
        text: "Recuerda que la información de la planeación se actualiza una vez por día.",
        showConfirmButton: true,
    });
}

function alertaDatos() {
    Swal.fire({
        icon: "info",
        title: "inconsistencias de datos",
        text: 'En este momento estamos presentando inconsistencia en la fuente de datos presenta. La situación ya fue reportada, ya se está trabajando en la solución.',
        showConfirmButton: true,
    });
}

function alertaPrepPlaneacion() {
    Swal.fire({
        icon: "info",
        title: "Planeación en proceso",
        text: "Apreciado usuario, el módulo de planeación no mostrará información a partir del 19 de octubre, puesto que se están actualizando los datos para la Programación de P4-P5-2024. Los datos estarán disponibles a partir del 21 de octubre de 2024.",
        showConfirmButton: true,
    });
}

function alertainfo2024Moodle() {
    Swal.fire({
        icon: "info",
        title: "Información 2024",
        text: "Apreciados usuarios a partir de mañana se mostrará la información correspondiente a los cursos del 2024. Te invitamos a que el día de hoy descargues la información que necesites.",
        showConfirmButton: true,
    });
}
