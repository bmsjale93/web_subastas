"use client";
import { useParams } from "next/navigation";
import { useEffect, useState } from "react";
import Image from "next/image";
import { auctions } from "../../../constants/auctions";
import Countdown from "../../../components/countDown";
import BenefitCalculator from "../../../components/BenefitCalculator";
import dynamic from "next/dynamic";
import RadarChart from "../../../components/RadarChart";
import logo from "../../../../public/logo-fdm.png"

const GoogleMap = dynamic(() => import("../../../components/GoogleMap"), {
  ssr: false,
});

export default function AuctionDetail() {
  const params = useParams();
  const { id } = params;
  const [auction, setAuction] = useState(null);

  useEffect(() => {
    if (id) {
      const foundAuction = auctions.find((auction) => auction.id === id);
      setAuction(foundAuction);
    }
  }, [id]);

  if (!auction) {
    return <div>Cargando...</div>;
  }

  return (
    <div className="min-h-screen bg-white py-10">
      <div className="container mx-auto p-4">
        <div className="text-center mb-8">
          <Image
            src={logo}
            alt="Logo"
            className="mx-auto mb-4 w-24 h-24 sm:w-32 sm:h-32 md:w-32 md:h-32"
          />
          <h1 className="text-3xl font-bold">FDM REAL ESTATE</h1>
        </div>
        <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
          <div className="bg-blue-700 text-white p-4 rounded-lg shadow-md col-span-1 sm:col-span-2 lg:col-span-1">
            <h2 className="text-lg font-semibold">SUBASTA ACTUAL</h2>
            <p className="text-2xl">{auction.value}</p>
            <h2 className="text-lg font-semibold mt-4">
              CIERRE PARA LA SUBASTA
            </h2>
            <Countdown endDate={auction.endDate} />
          </div>
          <BenefitCalculator />
          <div className="bg-white p-4 rounded-lg shadow-md col-span-1">
            <h2 className="text-lg font-semibold">INFORMACIÓN DE LA SUBASTA</h2>
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
          <div className="bg-white p-4 rounded-lg shadow-md col-span-1">
            <h2 className="text-lg font-semibold">LOCALIZACIÓN</h2>
            <GoogleMap
              latitude={auction.coordinates.latitude}
              longitude={auction.coordinates.longitude}
            />
          </div>
          <div className="bg-white p-4 rounded-lg shadow-md col-span-2 lg:col-span-1">
            <h2 className="text-lg font-semibold">GALERÍA DE IMÁGENES</h2>
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
              {auction.images.map((src, index) => (
                <Image
                  key={index}
                  src={src}
                  alt={`Imagen ${index + 1}`}
                  className="rounded-lg"
                  width={200}
                  height={150}
                />
              ))}
            </div>
          </div>
          <div className="bg-white p-4 rounded-lg shadow-md col-span-1">
            <h2 className="text-lg font-semibold">CATASTRO</h2>
            <p>Referencia Catastral: {auction.cadastralReference}</p>
            <p>Clase: {auction.cadastralClass}</p>
            <p>Uso Principal: {auction.cadastralUse}</p>
            <p>Superficie Construida: {auction.constructedArea} m²</p>
            <p>Vivienda: {auction.propertyArea} m²</p>
            <p>Año Construcción: {auction.yearBuilt}</p>
            <p>
              Enlace Catastro:{" "}
              <a href={auction.cadastralLink} className="text-blue-500">
                Haz click aquí
              </a>
            </p>
          </div>
          <div className="bg-white p-4 rounded-lg shadow-md col-span-2 lg:col-span-1">
            <h2 className="text-lg font-semibold">VALORACIÓN DE LA VIVIENDA</h2>
            <RadarChart qualities={auction.qualities} />
          </div>
        </div>
      </div>
    </div>
  );
}
