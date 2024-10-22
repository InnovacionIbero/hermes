export const alertaNoData = async () => {
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "No se encontraron docentes.",
    });
};

export const alertaCupoAsginaturasLleno = () => {
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "No puedes exceder 10 asignaturas para un docente.",
    });
};

export const alertaCupoPreferentesLleno = () => {
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "No puedes exceder 3 asignaturas como preferencia para un docente.",
    });
};

export const alertaCupoDocenteActualizado = (cupo) => {
    Swal.fire({
        icon: "success",
        title: "Cupo actualizado",
        text: `El cupo del docente ha sido actualizado a ${cupo}.`,
    });
};

export const alertaCupoDocenteInsuficiente = () => {
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "El cupo del docente no puede ser igual a 0.",
    });
};

export const alertaInactivarDocente = async (activar) => {

    let text = '';

    text = activar ? "¿Desea activar el docente seleccionado?" : "¿Desea inactivar el docente seleccionado?";

    const result = await Swal.fire({
        title: text,
        icon: "info",
        showCancelButton: true,
        showCloseButton: true,
        cancelButtonColor: "#DC3545",
        cancelButtonText: "No, Cancelar",
        confirmButtonText: "Si",
    });

    return result.isConfirmed;
};


export const alertaDocenteActualizado = async () => {
    const result = await Swal.fire({
        icon: "success",
        title: "Docente actualizado",
        text: "El docente ha sido actualizado correctamente.",
        confirmButtonText: "Ok.",
    });

    return result.isConfirmed;
};

export const alertaCupoVacio = () => {
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "El docente debe tener al menos una asignatura asignada.",
    });
};

export const alertaDocenteCreado = async () => {
    const result = await Swal.fire({
        icon: "success",
        title: "Docente creado correctamente",
        text: "El docente ha sido creado correctamente.",
        confirmButtonText: "Ok.",
    });

    return result.isConfirmed;
};

export const alertaErrorCrearDocente = async (error) => {
    console.log(error);
    Swal.fire({
        icon: "error",
        title: "Oops...",
        text: error,
    });
};

export const alertaPreload = () => {
  Swal.fire({
      imageUrl:
          "https://moocs.ibero.edu.co/hermes/front/public/assets/images/preload.gif",
      showConfirmButton: false,
      allowOutsideClick: false,
      allowEscapeKey: false,
  });
}
