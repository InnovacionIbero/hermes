/**
 * 
 * @param {object} data 
 * @param {String} url 
 * @returns 
 */
export const enviarAjax = (data = '', url) => {
    return new Promise((resolve, reject) => {
      $.ajax({
        headers: {
          "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        },
        type: "post",
        url: url,
        data: data,
        success: function(data) {
          resolve(data);
        },
        error: function(xhr, status, error) {
          console.error("Error en la petición:", error);
          console.error("Estado:", status);
          console.error("Detalle del error:", xhr.responseText);
          reject(error);
        }
      });
    });
  };