import Link from "next/link";

export default function AuctionCard({ id, title, value, endDate, process }) {
  // Color de fondo para cada proceso
  const processColors = {
    Activa: "bg-green-200 text-green-800",
    "Estudiando...": "bg-yellow-200 text-yellow-800",
    Terminada: "bg-red-200 text-red-800",
  };

  return (
    <div className="bg-white p-4 rounded-lg border border-gray-400 shadow-md flex flex-col justify-between transform transition-transform duration-300 hover:scale-105 hover:shadow-xl">
      <h3 className="text-lg font-semibold">{title}</h3>
      <p className="text-gray-700 text-base">Valor Subasta: {value}</p>
      <p className="text-gray-700 text-base mt-[-12px]">
        Fin de Subasta: {endDate}
      </p>
      <div className="flex items-center justify-between mt-2">
        <Link
          href={`/subastas/${id}`}
          className="bg-blue-700 hover:bg-black text-white font-bold py-2 px-3 rounded focus:outline-none focus:shadow-outline transition duration-300 ease-in-out"
        >
          Ver Subasta
        </Link>
        <span className={`${processColors[process]} px-3 py-2 rounded-xl`}>
          {process}
        </span>
      </div>
    </div>
  );
}
