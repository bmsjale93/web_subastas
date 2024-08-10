export function renderImageGallery(images) {
  // Crear el contenedor principal de la galería
  const galleryContainer = document.createElement("div");
  galleryContainer.className =
    "bg-white p-6 rounded-[28px]";

  // Crear y añadir el título de la galería
  const galleryTitle = document.createElement("h2");
  galleryTitle.className =
    "text-xl text-center text-blue-700 font-bold mb-4";
  galleryTitle.textContent = "GALERÍA DE IMÁGENES";
  galleryContainer.appendChild(galleryTitle);

  // Crear el contenedor de la cuadrícula de imágenes
  const gridContainer = document.createElement("div");
  gridContainer.className =
    "grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2";

  // Variable para el modal
  let modalElement = null;

  // Función para abrir el modal
  const openModal = (src) => {
    if (!modalElement) {
      modalElement = document.createElement("div");
      modalElement.className =
        "fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75";
      modalElement.innerHTML = `
        <div class="relative">
            <button class="absolute top-2 right-2 text-white text-2xl">&times;</button>
            <img src="${src}" alt="Imagen en tamaño completo" class="rounded-lg max-w-full max-h-screen">
        </div>
      `;
      document.body.appendChild(modalElement);

      // Añadir el evento para cerrar el modal
      modalElement.querySelector("button").addEventListener("click", () => {
        document.body.removeChild(modalElement);
        modalElement = null;
      });
    }
  };

  // Crear cada imagen en la cuadrícula
  images.forEach((src, index) => {
    console.log(`Cargando imagen: ${src}`); // Verifica que las rutas de las imágenes sean correctas
    const imageWrapper = document.createElement("div");
    imageWrapper.className = "relative";

    const imageElement = document.createElement("img");
    imageElement.src = src; // Aquí se utiliza directamente la ruta proporcionada en el array
    imageElement.alt = `Imagen ${index + 1}`;
    imageElement.className =
      "rounded-lg cursor-pointer object-cover w-full h-auto"; // Ajusta la clase para asegurar que las imágenes se muestren con las proporciones correctas

    // Añadir evento para abrir el modal
    imageElement.addEventListener("click", () => openModal(src));

    imageWrapper.appendChild(imageElement);
    gridContainer.appendChild(imageWrapper);
  });

  // Añadir la cuadrícula de imágenes al contenedor principal
  galleryContainer.appendChild(gridContainer);

  return galleryContainer;
}
