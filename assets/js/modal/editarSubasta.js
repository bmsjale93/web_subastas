document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".modal").forEach((modal) => {
    modal.classList.add("hidden");
  });

  document.querySelectorAll('[data-bs-toggle="modal"]').forEach((button) => {
    button.addEventListener("click", function () {
      const target = this.getAttribute("data-bs-target");
      const modal = document.querySelector(target);
      if (modal) {
        modal.classList.remove("hidden");
        modal.classList.add("show");
        document.body.classList.add("modal-open");
      }
    });
  });

  document.querySelectorAll(".btn-close, .btn-secondary").forEach((button) => {
    button.addEventListener("click", function () {
      const modal = this.closest(".modal");
      if (modal) {
        modal.classList.remove("show");
        setTimeout(() => modal.classList.add("hidden"), 150); // Allow some delay for transition
        document.body.classList.remove("modal-open");
      }
    });
  });

  // Gestión de la eliminación de imágenes
  document.querySelectorAll(".eliminar-imagen").forEach((button) => {
    button.addEventListener("click", function (event) {
      event.preventDefault();

      const idImagen = this.getAttribute("data-id");
      const imagenContainer = this.closest(".relative");
      agregarAEliminar("imagenes_a_eliminar", idImagen);
      imagenContainer.remove(); // Eliminar visualmente la imagen del DOM
    });
  });

  // Gestión de la eliminación de documentos
  document.querySelectorAll(".eliminar-documento").forEach((button) => {
    button.addEventListener("click", function (event) {
      event.preventDefault();

      const idDocumento = this.getAttribute("data-id");
      const documentoContainer = this.closest(".relative");
      agregarAEliminar("documentos_a_eliminar", idDocumento);
      documentoContainer.remove(); // Eliminar visualmente el documento del DOM
    });
  });

  function agregarAEliminar(campoHiddenId, idElemento) {
    const campoHidden = document.getElementById(campoHiddenId);
    if (campoHidden) {
      campoHidden.value += campoHidden.value ? "," + idElemento : idElemento;
    }
  }
});
