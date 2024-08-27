document.addEventListener("DOMContentLoaded", function () {
  // Ocultar modales al cargar la página
  document.querySelectorAll(".modal").forEach((modal) => {
    modal.classList.add("hidden");
  });

  // Mostrar modal al hacer clic en el botón de activación
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

  // Cerrar modal al hacer clic en el botón de cerrar o en "Cancelar"
  document.querySelectorAll(".btn-close, .btn-secondary").forEach((button) => {
    button.addEventListener("click", function () {
      const modal = this.closest(".modal");
      if (modal) {
        modal.classList.remove("show");
        setTimeout(() => modal.classList.add("hidden"), 150); // Delay para la transición
        document.body.classList.remove("modal-open");
      }
    });
  });

  // Gestión de la eliminación de imágenes
  document.querySelectorAll(".eliminar-imagen").forEach((button) => {
    button.addEventListener("click", function (event) {
      event.preventDefault();
      const idImagen = this.getAttribute("data-id");
      agregarAEliminar("imagenes_a_eliminar", idImagen);
      // Eliminar visualmente la imagen del DOM
      this.closest(".relative").remove();
    });
  });

  // Gestión de la eliminación de documentos
  document.querySelectorAll(".eliminar-documento").forEach((button) => {
    button.addEventListener("click", function (event) {
      event.preventDefault();
      const idDocumento = this.getAttribute("data-id");
      agregarAEliminar("documentos_a_eliminar", idDocumento);
      // Eliminar visualmente el documento del DOM
      this.closest(".relative").remove();
    });
  });

  function agregarAEliminar(campoHiddenId, idElemento) {
    const campoHidden = document.getElementById(campoHiddenId);
    if (campoHidden) {
      campoHidden.value += campoHidden.value ? "," + idElemento : idElemento;
    }
  }

  // Manejo de área de drop para subir imágenes
  document.querySelectorAll('[id^="drop-area"]').forEach((dropArea) => {
    const id = dropArea.id.replace("drop-area", "");
    const fileInput = document.getElementById("nuevas_imagenes" + id);
    const previewContainer = document.getElementById("preview" + id);

    dropArea.addEventListener("click", () => fileInput.click());

    dropArea.addEventListener("dragover", (event) => {
      event.preventDefault();
      dropArea.classList.add("bg-gray-100");
    });

    dropArea.addEventListener("dragleave", () => {
      dropArea.classList.remove("bg-gray-100");
    });

    dropArea.addEventListener("drop", (event) => {
      event.preventDefault();
      dropArea.classList.remove("bg-gray-100");
      const files = event.dataTransfer.files;
      handleFiles(files, previewContainer);
    });

    fileInput.addEventListener("change", () =>
      handleFiles(fileInput.files, previewContainer)
    );
  });

  function handleFiles(files, previewContainer) {
    previewContainer.innerHTML = ""; // Limpiar previas
    if (files.length > 0) {
      for (const file of files) {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onloadend = () => {
          const img = document.createElement("img");
          img.src = reader.result;
          img.className = "w-full h-auto rounded";
          previewContainer.appendChild(img);
        };
      }
    }
  }

  // Verificación del formulario antes de enviarlo
  document.querySelectorAll("form").forEach((form) => {
    form.addEventListener("submit", function (event) {
      const fileInputs = form.querySelectorAll('input[type="file"]');
      let allFilesSelected = true;

      fileInputs.forEach((fileInput) => {
        if (fileInput.files.length === 0) {
          allFilesSelected = false;
          console.warn("No hay archivos seleccionados en", fileInput);
        } else {
          console.log("Archivos seleccionados en", fileInput, fileInput.files);
        }
      });

      if (!allFilesSelected) {
        event.preventDefault(); // Evitar el envío si no hay archivos seleccionados
        alert("Por favor, seleccione al menos un archivo antes de enviar.");
      }
    });
  });
});
