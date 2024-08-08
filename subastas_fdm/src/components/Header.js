import Image from "next/image";
import { useEffect, useState } from "react";
import logo from "../../public/logo-fdm.png";

// Componente Header que muestra un logo y un botón para volver atrás en el historial del navegador
export default function Header() {
  const [isClient, setIsClient] = useState(false); // Estado para verificar si se está en el lado del cliente

  useEffect(() => {
    setIsClient(true); // Establece isClient a true cuando el componente se monta en el cliente
  }, []);

  const handleBack = () => {
    if (isClient) {
      window.history.back(); // Navega hacia atrás en el historial del navegador si está en el cliente
    }
  };

  return (
    <div className="bg-white shadow-md py-4 flex items-center justify-between px-4">
      <button
        className="text-black p-2 rounded-full hover:bg-gray-200"
        onClick={handleBack} // Maneja el clic para volver atrás
      >
        {/* Icono de flecha hacia la izquierda */}
        <svg
          xmlns="http://www.w3.org/2000/svg"
          className="h-6 w-6"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            strokeLinecap="round"
            strokeLinejoin="round"
            strokeWidth="2"
            d="M15 19l-7-7 7-7"
          />
        </svg>
      </button>
      <div className="flex-grow text-center">
        {/* Muestra el logo */}
        <Image
          src={logo}
          alt="Logo"
          className="mx-auto"
          width={80}
          height={80}
        />
      </div>
      {/* Elemento vacío para ocupar el espacio del botón en el diseño */}
      <div className="w-6 h-6"></div>
    </div>
  );
}
