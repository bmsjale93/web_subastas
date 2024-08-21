document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".eliminar-subasta").forEach(function (button) {
    button.addEventListener("click", function () {
      if (confirm("¿Estás seguro de que deseas eliminar esta subasta?")) {
        const subastaId = this.getAttribute("data-id");

        fetch("assets/php/eliminarSubasta.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({
            id_subasta: subastaId,
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              alert("Subasta eliminada con éxito");
              location.reload(); // Recargar la página para reflejar los cambios
            } else {
              alert("Hubo un error al eliminar la subasta: " + data.error);
            }
          })
          .catch((error) => {
            alert("Error en la solicitud: " + error.message);
          });
      }
    });
  });
});
