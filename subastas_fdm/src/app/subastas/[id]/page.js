"use client"; // Indica que este archivo debe ser renderizado en el lado del cliente
import { useParams } from "next/navigation"; // Hook para obtener parámetros de la URL
import { useEffect, useState } from "react"; // Hooks de React para efectos y estado
import Image from "next/image"; // Componente de Next.js para manejar imágenes optimizadas
import { auctions } from "../../../constants/auctions"; // Importa una lista de subastas
import Countdown from "../../../components/countDown"; // Importa el componente Countdown
import BenefitCalculator from "../../../components/BenefitCalculator"; // Importa el componente BenefitCalculator
import dynamic from "next/dynamic"; // Importa para cargar componentes dinámicamente
import RadarChart from "../../../components/RadarChart"; // Importa el componente RadarChart
import ImageGallery from "../../../components/ImageGallery"; // Importa el componente ImageGallery
import Button from "../../../components/Button"; // Importa el componente Button
import Header from "../../../components/Header"; // Importa el componente Header

// Dinámicamente importa el componente GoogleMap para renderización del lado del cliente
const GoogleMap = dynamic(() => import("../../../components/GoogleMap"), {
  ssr: false, // Desactiva la renderización en el servidor para este componente
});

export default function AuctionDetail() {
  // Obtiene los parámetros de la URL
  const params = useParams();
  const { id } = params; // Extrae el ID del parámetro
  const [auction, setAuction] = useState(null); // Define el estado para la subasta

  // Efecto para buscar la subasta cuando el ID cambia
  useEffect(() => {
    if (id) {
      const foundAuction = auctions.find((auction) => auction.id === id); // Busca la subasta por ID
      setAuction(foundAuction); // Actualiza el estado con la subasta encontrada
    }
  }, [id]);

  // Si la subasta no está cargada, muestra un mensaje de carga
  if (!auction) {
    return <div>Cargando...</div>;
  }

  return (
    <div className="min-h-screen bg-gray-100">
      <Header /> {/* Componente de cabecera */}
      <div className="container mx-auto p-4 py-10">
        {/* Grid layout para el contenido */}
        <div className="grid gap-4 grid-cols-1 lg:grid-cols-4">
          {/* PRIMERA COLUMNA */}
          <div className="lg:col-span-1">
            <div className="bg-blue-700 text-white py-6 pb-3 pt-8 rounded-[28px] shadow-md mb-4">
              <h2 className="text-[20px] text-center font-bold">
                SUBASTA ACTUAL
              </h2>
              <p className="text-[36px] text-center font-semibold">
                {auction.value}
              </p>
            </div>

            <div className="bg-white text-blue-700 py-4 rounded-[28px] shadow-md mb-4 border-3 border-blue-700">
              <h2 className="text-[20px] text-center font-bold">
                CIERRE SUBASTA
              </h2>
              <div className="text-[40px] text-center font-semibold">
                <Countdown endDate={auction.endDate} />{" "}
                {/* Componente de cuenta atrás */}
              </div>
            </div>

            <div className="bg-white rounded-[28px] mb-4">
              <BenefitCalculator auction={auction} />{" "}
              {/* Componente calculadora de beneficios */}
            </div>

            <div className="bg-white text-blue-700 px-2 py-6 pb-3 pt-8 rounded-[28px] shadow-md mb-4 border-3 border-blue-700">
              <h2 className="text-[20px] text-center font-bold mb-2">
                VALORACIÓN DE LA VIVIENDA
              </h2>
              <RadarChart qualities={auction.qualities} />{" "}
              {/* Componente de gráfico radar */}
            </div>
          </div>

          {/* SEGUNDA COLUMNA */}
          <div className="lg:col-span-1">
            <div className="bg-white p-6 rounded-[28px] border-3 border-blue-700">
              <h2 className="text-[20px] text-center text-blue-700 font-bold mb-4">
                INFORMACIÓN
              </h2>
              <div className="bg-white rounded-lg">
                <div className="mb-4 rounded-xl">
                  <p>Dirección: {auction.address}</p>
                  <p>Código Postal: {auction.postalCode}</p>
                  <p>Localidad: {auction.city}</p>
                  <p>Provincia: {auction.province}</p>
                  <p>Fecha Inicio: {auction.startDate}</p>
                  <p>Fecha Conclusión: {auction.endDate}</p>
                  <p>Tipo de Subasta: {auction.auctionType}</p>
                  <p>
                    Enlace Subasta:{" "}
                    <a href={auction.link} className="text-blue-500">
                      Haz click aquí
                    </a>
                  </p>
                  <p>Valor Subasta: {auction.value}</p>
                  <p>Tasación: {auction.appraisalValue}</p>
                  <p>Importe Depósito: {auction.deposit}</p>
                  <p>Puja Mínima: {auction.minBid}</p>
                  <p>Tramos entre Pujas: {auction.bidSteps}</p>
                </div>
                <div className="text-center">
                  <Button text="DESCARGA PDF DE COMPRA Y VENTA" />{" "}
                  {/* Botón de descarga */}
                </div>
              </div>
            </div>
          </div>

          {/* TERCERA COLUMNA */}
          <div className="lg:col-span-2">
            <div className="text-blue-700 p-6 rounded-[28px] mb-4 bg-white border-3 border-blue-700">
              <h2 className="text-[20px] text-center font-bold mb-4">
                LOCALIZACIÓN SUBASTA
              </h2>
              <GoogleMap
                latitude={auction.coordinates.latitude}
                longitude={auction.coordinates.longitude}
              />{" "}
              {/* Componente de mapa de Google */}
            </div>
            <ImageGallery images={auction.images} />{" "}
            {/* Componente de galería de imágenes */}
          </div>
        </div>
      </div>
    </div>
  );
}
