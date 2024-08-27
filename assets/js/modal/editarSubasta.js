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

  // Gestión de la eliminación de videos
  document.querySelectorAll(".eliminar-video").forEach((button) => {
    button.addEventListener("click", function (event) {
      event.preventDefault();
      const idVideo = this.getAttribute("data-id");
      agregarAEliminar("videos_a_eliminar", idVideo);
      // Eliminar visualmente el video del DOM
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
    const fileInputImages = document.getElementById("nuevas_imagenes" + id); // Para imágenes
    const previewContainerImages = document.getElementById("preview" + id);

    if (fileInputImages) {
      dropArea.addEventListener("click", () => {
        fileInputImages.click();
      });

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
        handleFiles(files, previewContainerImages, "image");
      });

      fileInputImages.addEventListener("change", () =>
        handleFiles(fileInputImages.files, previewContainerImages, "image")
      );
    }
  });

  // Manejo de área de drop para subir videos
  document.querySelectorAll('[id^="drop-area-videos"]').forEach((dropArea) => {
    const id = dropArea.id.replace("drop-area-videos", "");
    const fileInputVideos = document.getElementById("nuevos_videos" + id); // Para videos
    const previewContainerVideos = document.getElementById(
      "preview-videos" + id
    );

    if (fileInputVideos) {
      dropArea.addEventListener("click", () => {
        fileInputVideos.click();
      });

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
        handleFiles(files, previewContainerVideos, "video");
      });

      fileInputVideos.addEventListener("change", () =>
        handleFiles(fileInputVideos.files, previewContainerVideos, "video")
      );
    }
  });

  function handleFiles(files, previewContainer, fileType) {
    previewContainer.innerHTML = ""; // Limpiar previas

    // Crear un nuevo DataTransfer para gestionar los archivos seleccionados
    const dataTransfer = new DataTransfer();

    if (files.length > 0) {
      for (const file of files) {
        if (
          (fileType === "image" && file.type.startsWith("image/")) ||
          (fileType === "video" && file.type.startsWith("video/"))
        ) {
          dataTransfer.items.add(file);

          // Manejo de previsualización
          const reader = new FileReader();
          reader.readAsDataURL(file);
          reader.onloadend = () => {
            if (fileType === "image") {
              const img = document.createElement("img");
              img.src = reader.result;
              img.className = "w-full h-auto rounded";
              previewContainer.appendChild(img);
            } else if (fileType === "video") {
              const video = document.createElement("video");
              video.src = reader.result;
              video.controls = true;
              video.className = "w-full h-auto rounded";
              previewContainer.appendChild(video);
            }
          };
        }
      }

      if (fileType === "image") {
        document.getElementById(
          "nuevas_imagenes" + previewContainer.id.replace("preview", "")
        ).files = dataTransfer.files;
      } else if (fileType === "video") {
        document.getElementById(
          "nuevos_videos" + previewContainer.id.replace("preview-videos", "")
        ).files = dataTransfer.files;
      }
    }
  }
});
