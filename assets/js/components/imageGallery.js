export function renderImageGallery(mediaItems) {
  // Crear un nuevo contenedor para la galería
  const galleryContainer = document.createElement("div");
  galleryContainer.className = "relative w-full";

  const sliderContainer = document.createElement("div");
  sliderContainer.className = "relative w-full overflow-hidden rounded-xl shadow-lg";

  const mediaTrack = document.createElement("div");
  mediaTrack.className = "flex transition-transform duration-500 ease-in-out";

  let modalElement = null;
  let currentMediaIndex = 0;

  const updateModalMedia = (src, type) => {
    const mediaContainer = modalElement.querySelector(".media-container");
    mediaContainer.innerHTML = ""; // Limpiar contenido anterior

    if (type === "image") {
      const imageElement = document.createElement("img");
      imageElement.src = src;
      imageElement.alt = `Media tipo ${type}`;
      imageElement.className = "rounded-lg max-w-full max-h-screen";
      mediaContainer.appendChild(imageElement);
    } else if (type === "video") {
      const videoElement = document.createElement("video");
      videoElement.src = src;
      videoElement.controls = true;
      videoElement.className = "rounded-lg max-w-full max-h-screen";

      // Reproducir o pausar video al hacer clic en él
      videoElement.addEventListener("click", () => {
        if (videoElement.paused) {
          videoElement.play();
        } else {
          videoElement.pause();
        }
      });

      mediaContainer.appendChild(videoElement);
    }
  };

  const closeModal = () => {
    if (modalElement) {
      const videoElement = modalElement.querySelector("video");
      if (videoElement) {
        videoElement.pause();
        videoElement.currentTime = 0;
      }
      document.body.removeChild(modalElement);
      modalElement = null;
    }
  };

  const openModal = (index) => {
    currentMediaIndex = index;
    const { src, type } = mediaItems[currentMediaIndex];

    if (!modalElement) {
      modalElement = document.createElement("div");
      modalElement.className = "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75";
      modalElement.innerHTML = `
        <div class="relative max-w-3xl mx-auto">
            <button id="closeModal" class="absolute top-2 right-2 bg-white bg-opacity-80 text-black text-2xl rounded-full w-10 h-10 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <button id="prevMedia" class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 text-black text-2xl rounded-full w-10 h-10 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <div class="media-container"></div>
            <button id="nextMedia" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 text-black text-2xl rounded-full w-10 h-10 flex items-center justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </button>
        </div>
      `;
      document.body.appendChild(modalElement);

      modalElement.querySelector("#closeModal").addEventListener("click", closeModal);

      modalElement.querySelector("#prevMedia").addEventListener("click", () => {
        currentMediaIndex = (currentMediaIndex > 0) ? currentMediaIndex - 1 : mediaItems.length - 1;
        updateModalMedia(mediaItems[currentMediaIndex].src, mediaItems[currentMediaIndex].type);
      });

      modalElement.querySelector("#nextMedia").addEventListener("click", () => {
        currentMediaIndex = (currentMediaIndex < mediaItems.length - 1) ? currentMediaIndex + 1 : 0;
        updateModalMedia(mediaItems[currentMediaIndex].src, mediaItems[currentMediaIndex].type);
      });

      document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
          closeModal();
        }
      });
    }

    updateModalMedia(src, type);
  };

  mediaItems.forEach(({ src, type }, index) => {
    if (typeof src !== "string") {
      console.error('El valor de "src" no es una cadena:', src);
      return;
    }

    const mediaWrapper = document.createElement("div");
    mediaWrapper.className = "w-full flex-shrink-0 relative cursor-pointer";

    if (type === "image") {
      const imageElement = document.createElement("img");
      imageElement.src = src;
      imageElement.alt = `Media ${index + 1}`;
      imageElement.className = "object-cover w-full h-auto rounded-lg";
      mediaWrapper.appendChild(imageElement);
    } else if (type === "video") {
      const videoWrapper = document.createElement("div");
      videoWrapper.className = "relative w-full h-auto";

      const videoElement = document.createElement("video");
      videoElement.src = src;
      videoElement.className = "object-cover w-full h-auto rounded-lg";
      videoElement.muted = true; // Silenciar el video en la galería
      videoElement.loop = true; // Hacer que el video se reproduzca en bucle

      const playIcon = document.createElement("div");
      playIcon.className = "absolute inset-0 flex items-center justify-center text-white text-4xl cursor-pointer";
      playIcon.innerHTML = `
        <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="w-16 h-16" viewBox="0 0 16 16">
          <path d="M4.5 3.5v9l7-4.5-7-4.5z"/>
        </svg>
      `;

      playIcon.addEventListener("click", () => {
        if (videoElement.paused) {
          videoElement.play();
          playIcon.style.display = "none";
        } else {
          videoElement.pause();
          playIcon.style.display = "flex";
        }
      });

      videoWrapper.appendChild(videoElement);
      videoWrapper.appendChild(playIcon);
      mediaWrapper.appendChild(videoWrapper);
    }

    mediaWrapper.addEventListener("click", () => openModal(index));
    mediaTrack.appendChild(mediaWrapper);
  });

  sliderContainer.appendChild(mediaTrack);
  galleryContainer.appendChild(sliderContainer);

  // Flechas de navegación
  const prevButton = document.createElement("button");
  prevButton.className = "absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 text-black text-2xl rounded-full w-10 h-10 flex items-center justify-center z-10";
  prevButton.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
  `;

  const nextButton = document.createElement("button");
  nextButton.className = "absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 text-black text-2xl rounded-full w-10 h-10 flex items-center justify-center z-10";
  nextButton.innerHTML = `
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
  `;

  prevButton.addEventListener("click", () => {
    currentMediaIndex = (currentMediaIndex > 0) ? currentMediaIndex - 1 : mediaItems.length - 1;
    mediaTrack.style.transform = `translateX(-${currentMediaIndex * 100}%)`;
    updateIndicators();
  });

  nextButton.addEventListener("click", () => {
    currentMediaIndex = (currentMediaIndex < mediaItems.length - 1) ? currentMediaIndex + 1 : 0;
    mediaTrack.style.transform = `translateX(-${currentMediaIndex * 100}%)`;
    updateIndicators();
  });

  galleryContainer.appendChild(prevButton);
  galleryContainer.appendChild(nextButton);

  // Crear contenedor de indicadores
  const indicatorsContainer = document.createElement("div");
  indicatorsContainer.className = "absolute bottom-2 left-1/2 transform -translate-x-1/2 flex space-x-2";

  // Crear indicadores
  mediaItems.forEach((_, idx) => {
    const indicator = document.createElement("div");
    indicator.className = "w-2 h-2 rounded-full bg-white bg-opacity-50 cursor-pointer";
    if (idx === currentMediaIndex) {
      indicator.classList.add("bg-opacity-100");
    }
    indicator.addEventListener("click", () => {
      currentMediaIndex = idx;
      mediaTrack.style.transform = `translateX(-${currentMediaIndex * 100}%)`;
      updateIndicators();
    });
    indicatorsContainer.appendChild(indicator);
  });

  const updateIndicators = () => {
    indicatorsContainer.childNodes.forEach((dot, idx) => {
      dot.classList.toggle("bg-opacity-100", idx === currentMediaIndex);
      dot.classList.toggle("bg-opacity-50", idx !== currentMediaIndex);
    });
  };

  galleryContainer.appendChild(indicatorsContainer);

  return galleryContainer;
}
