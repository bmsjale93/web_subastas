export function renderImageGallery(images) {
  const galleryContainer = document.createElement("div");
  galleryContainer.className =
    "bg-white p-6 rounded-xl border-3 border-blue-700";

  const galleryTitle = document.createElement("h2");
  galleryTitle.className = "text-xl text-center text-blue-700 font-bold mb-4";
  galleryTitle.textContent = "GALERÍA DE IMÁGENES";
  galleryContainer.appendChild(galleryTitle);

  const gridContainer = document.createElement("div");
  gridContainer.className =
    "grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2";

  let modalElement = null;
  let currentImageIndex = 0;

  const updateModalImage = (src) => {
    const imageElement = modalElement.querySelector("img");
    imageElement.src = src;
  };

  const openModal = (index) => {
    currentImageIndex = index;
    if (!modalElement) {
      modalElement = document.createElement("div");
      modalElement.className =
        "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75";
      modalElement.innerHTML = `
        <div class="relative">
            <button id="closeModal" class="absolute top-2 right-2 bg-white bg-opacity-80 text-black text-2xl rounded-full w-10 h-10 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
            <button id="prevImage" class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 text-black text-2xl rounded-full w-10 h-10 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <img src="${images[currentImageIndex]}" alt="Imagen en tamaño completo" class="rounded-lg max-w-full max-h-screen">
            <button id="nextImage" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 text-black text-2xl rounded-full w-10 h-10 flex items-center justify-center">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </button>
        </div>
      `;
      document.body.appendChild(modalElement);

      modalElement
        .querySelector("#closeModal")
        .addEventListener("click", () => {
          document.body.removeChild(modalElement);
          modalElement = null;
        });

      modalElement.querySelector("#prevImage").addEventListener("click", () => {
        currentImageIndex =
          currentImageIndex > 0 ? currentImageIndex - 1 : images.length - 1;
        updateModalImage(images[currentImageIndex]);
      });

      modalElement.querySelector("#nextImage").addEventListener("click", () => {
        currentImageIndex =
          currentImageIndex < images.length - 1 ? currentImageIndex + 1 : 0;
        updateModalImage(images[currentImageIndex]);
      });
    } else {
      updateModalImage(images[currentImageIndex]);
    }
  };

  images.forEach((src, index) => {
    const imageWrapper = document.createElement("div");
    imageWrapper.className = "relative";

    const imageElement = document.createElement("img");
    imageElement.src = src;
    imageElement.alt = `Imagen ${index + 1}`;
    imageElement.className =
      "rounded-lg cursor-pointer object-cover w-full h-auto";

    imageElement.addEventListener("click", () => openModal(index));

    imageWrapper.appendChild(imageElement);
    gridContainer.appendChild(imageWrapper);
  });

  galleryContainer.appendChild(gridContainer);

  return galleryContainer;
}
