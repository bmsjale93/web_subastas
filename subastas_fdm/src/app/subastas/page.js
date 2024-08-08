import React from "react"; // Importa React para poder usar JSX
import AuctionCard from "../../components/AuctionCard"; // Importa el componente AuctionCard desde el directorio de componentes
import { auctions } from "../../constants/auctions"; // Importa una lista de subastas desde el directorio de constantes
import Image from "next/image"; // Importa el componente Image de Next.js para manejar imágenes optimizadas
import logo from "../../../public/logo-fdm.png"; // Importa el logo de la carpeta pública

// Componente principal que muestra la lista de subastas
export default function Auctions() {
  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-b from-blue-800 to-black py-10">
      {/* Contenedor principal con un fondo degradado y un espaciado vertical */}
      <div className="container mx-auto p-4 bg-white rounded-lg shadow-lg">
        {/* Contenedor para centrar y estilizar el contenido */}
        <div className="text-center mb-8">
          {/* Contenedor para el logo con margen inferior */}
          <Image
            src={logo} // Fuente de la imagen del logo
            alt="Logo" // Texto alternativo para accesibilidad
            className="mx-auto mb-4 w-48 h-48 sm:w-40 sm:h-40 md:w-40 md:h-40" // Clases de Tailwind CSS para centrar y dimensionar el logo
          />
        </div>
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
          {/* Grid para organizar las tarjetas de subastas con espacios entre ellas */}
          {auctions.map((auction) => (
            // Mapea a través de la lista de subastas y crea una tarjeta para cada una
            <AuctionCard
              key={auction.id} // Identificador único para cada tarjeta, importante para la optimización de React
              id={auction.id} // Pasa el ID de la subasta como prop al componente AuctionCard
              title={auction.title} // Pasa el título de la subasta como prop
              value={auction.value} // Pasa el valor de la subasta como prop
              endDate={auction.endDate} // Pasa la fecha de finalización de la subasta como prop
              process={auction.process} // Pasa el estado del proceso de la subasta como prop
            />
          ))}
        </div>
      </div>
    </div>
  );
}
