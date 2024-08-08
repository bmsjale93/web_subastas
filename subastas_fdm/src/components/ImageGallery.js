import { useState } from "react";
import Image from "next/image";

// Componente ImageGallery que muestra una galería de imágenes con funcionalidad de modal
export default function ImageGallery({ images }) {
  const [selectedImage, setSelectedImage] = useState(null); // Estado para la imagen seleccionada en el modal

  // Función para abrir el modal con la imagen seleccionada
  const openModal = (src) => {
    setSelectedImage(src);
  };

  // Función para cerrar el modal
  const closeModal = () => {
    setSelectedImage(null);
  };

  return (
    <div className="bg-white p-6 rounded-[28px] border-3 border-blue-700">
      <h2 className="text-[20px] text-center text-blue-700 font-bold mb-4">
        GALERÍA DE IMÁGENES
      </h2>
      {/* Grid para mostrar las imágenes */}
      <div className="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 auto-rows-fr">
        {images.map((src, index) => (
          <div key={index} onClick={() => openModal(src)} className="relative">
            <div className="w-full h-0 pb-[100%] relative">
              <Image
                src={src}
                alt={`Imagen ${index + 1}`}
                className="rounded-lg cursor-pointer object-cover absolute inset-0 w-full h-full"
                layout="fill"
              />
            </div>
          </div>
        ))}
      </div>
      {/* Modal para mostrar la imagen seleccionada en tamaño completo */}
      {selectedImage && (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75">
          <div className="relative">
            <button
              className="absolute top-2 right-2 text-white text-2xl"
              onClick={closeModal} // Cierra el modal
            >
              &times;
            </button>
            <Image
              src={selectedImage}
              alt="Imagen en tamaño completo"
              className="rounded-lg"
              width={800}
              height={600}
            />
          </div>
        </div>
      )}
    </div>
  );
}
