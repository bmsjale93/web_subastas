import Link from "next/link";

export default function AuctionCard({ id, title, value, endDate, process }) {
  // Define los colores de fondo según el estado del proceso de la subasta
  const processColors = {
    Activa: "bg-green-200 text-green-800",
    "Estudiando...": "bg-yellow-200 text-yellow-800",
    Terminada: "bg-red-200 text-red-800",
  };

  return (
    <div className="bg-white p-4 rounded-lg border border-gray-400 shadow-md flex flex-col justify-between transform transition-transform duration-300 hover:scale-105 hover:shadow-xl">
      {/* Contenedor principal con estilo y animaciones */}
      
      <h3 className="text-lg font-semibold">{title}</h3>{" "}
      {/* Título de la subasta */}
      
      <p className="text-gray-700 text-base">Valor Subasta: {value}</p>{" "}
      {/* Valor de la subasta */}
      
      <p className="text-gray-700 text-base mt-[-12px]">
        Fin de Subasta: {endDate}
      </p>{" "}
      {/* Fecha de fin de la subasta */}
      
      <div className="flex items-center justify-between mt-2">
        {/* Contenedor para el enlace y el estado del proceso */}
        <Link
          href={`/subastas/${id}`}
          className="bg-blue-700 hover:bg-black text-white font-bold py-2 px-3 rounded focus:outline-none focus:shadow-outline transition duration-300 ease-in-out"
        >
          Ver Subasta {/* Enlace a la página de detalles de la subasta */}
        </Link>
        <span className={`${processColors[process]} px-3 py-2 rounded-xl`}>
          {process}{" "}
          {/* Muestra el estado del proceso con el color correspondiente */}
        </span>
      </div>
    </div>
  );
}
