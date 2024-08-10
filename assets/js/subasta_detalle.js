import { renderCountdown } from "./components/countdown.js";
import { renderBenefitCalculator } from "./components/benefitCalculator.js";
import { renderGoogleMap } from "./components/googleMap.js";
import { renderRadarChart } from "./components/radarChart.js";
import { renderImageGallery } from "./components/imageGallery.js";

const auctions = [
  {
    id: "SUB0001",
    title: "CALLE ANCORA, 4",
    value: "152.000,00€",
    endDate: "2024-08-24T18:00:00Z",
    link: "/subastas/SUB0001",
    process: "Terminada",
    address: "CL ANCORA 4 Nº- 8 Esc.1 Ptl:01 EDF ANCORA 1ª FASE",
    postalCode: "11500",
    city: "El Puerto Santa María",
    province: "Cádiz",
    startDate: "2024-07-19",
    auctionType: "SUBASTA BOE",
    auctionValue: "152.000,00 €",
    appraisalValue: "0,00 €",
    deposit: "7.600,00 €",
    minBid: "Sin puja mínima",
    bidSteps: "3.040,00 €",
    cadastralReference: "4146502QA4544E0129WJ",
    cadastralClass: "URBANO",
    cadastralUse: "RESIDENCIAL",
    constructedArea: "92",
    propertyArea: "82",
    garageArea: 15,
    storageRoomArea: 10,
    squareMeterValue: 2000,
    garageSquareMeterValue: 1500,
    storageRoomSquareMeterValue: 1000,
    yearBuilt: "2003",
    cadastralLink: "#",
    coordinates: {
      latitude: 36.59349,
      longitude: -6.23291,
    },
    qualities: {
      "Fachada y Exteriores": 10,
      "Techo y Canaletas": 10,
      "Ventanas y Puertas": 8,
      "Jardín y Terrenos": 10,
      Estructuras: 9,
      Instalaciones: 10,
      Vecindario: 9,
      Seguridad: 9,
      "Ruido y Olores": 9,
      Estacionamiento: 7,
      Localización: 9,
      "Estado Inquilino": 3,
      "Tipo Vivienda": 10,
    },
    images: [
      "/assets/img/ANCORA/foto-vivienda-1.png",
      "/assets/img/ANCORA/foto-vivienda-2.png",
      "/assets/img/ANCORA/foto-vivienda-3.png",
      "/assets/img/ANCORA/foto-vivienda-4.png",
    ],
  },
  // Otros objetos de subastas...
];

function getQueryParams() {
  const params = new URLSearchParams(window.location.search);
  return params.get("id");
}

function loadAuctionDetails() {
  console.log("loadAuctionDetails function called");

  const auctionId = getQueryParams();
  const auction = auctions.find((a) => a.id === auctionId);

  if (!auction) {
    document.body.innerHTML =
      "<div>No se encontró la subasta. Por favor, verifica el ID.</div>";
    return;
  }

  document.getElementById("header").innerHTML = renderHeader();

  const columna1 = document.getElementById("columna1");
  const columna2 = document.getElementById("columna2");
  const columna3 = document.getElementById("columna3");

  if (columna1 && columna2 && columna3) {
    columna1.appendChild(renderColumna1(auction));
    columna2.innerHTML = renderColumna2(auction);
    columna3.appendChild(renderColumna3(auction));
  } else {
    console.error("No se encontraron las columnas en el DOM.");
  }
}

function renderHeader() {
  return `
        <div class="bg-white shadow-md py-4 flex items-center justify-between px-4">
            <button class="text-black p-2 rounded-full hover:bg-gray-200" onclick="window.history.back()">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <div class="flex-grow text-center">
                <img src="assets/img/logo-fdm.png" alt="Logo" class="mx-auto" width="80" height="80">
            </div>
            <div class="w-6 h-6"></div>
        </div>
    `;
}

function renderColumna1(auction) {
  const columna1Element = document.createElement("div");

  columna1Element.innerHTML = `
    <div class="bg-blue-700 text-white py-6 pb-8 pt-8 rounded-xl shadow-md mb-4">
      <h2 class="text-2xl text-center font-bold">SUBASTA ACTUAL</h2>
      <p class="text-4xl text-center font-semibold">${auction.value}</p>
    </div>
    <div class="bg-white text-blue-700 py-4 rounded-xl shadow-md mb-4 border-3 border-blue-700">
      <h2 class="text-2xl text-center font-bold">CIERRE SUBASTA</h2>
      <div id="countdown-container" class="text-4xl text-center font-semibold"></div>
    </div>
  `;

  // Crear e insertar la calculadora de beneficios
  const benefitCalculatorElement = renderBenefitCalculator(auction);
  columna1Element.appendChild(benefitCalculatorElement);

  // Crear el contenedor para el radar chart con las clases de Tailwind CSS
  const radarChartContainer = document.createElement("div");
  radarChartContainer.className =
    "bg-white text-blue-700 px-2 py-6 pb-3 pt-8 rounded-xl shadow-md mb-4 border-3 border-blue-700";

  const radarChartTitle = document.createElement("h2");
  radarChartTitle.className = "text-xl text-center font-bold mb-2";
  radarChartTitle.textContent = "VALORACIÓN DE LA VIVIENDA";

  // Añadir el título al contenedor del radar chart
  radarChartContainer.appendChild(radarChartTitle);

  // Añadir el gráfico de radar al contenedor
  const radarChartElement = renderRadarChart(auction.qualities);
  radarChartContainer.appendChild(radarChartElement);

  // Añadir el contenedor del radar chart a la columna
  columna1Element.appendChild(radarChartContainer);

  // Obtener el contenedor del countdown
  const countdownContainer = columna1Element.querySelector(
    "#countdown-container"
  );

  // Insertar el countdown dinámico en el DOM
  const countdownElement = renderCountdown(auction.endDate);
  countdownContainer.appendChild(countdownElement);

  return columna1Element;
}


function renderColumna2(auction) {
  return `
        <div class="bg-white p-6 rounded-xl border-3 border-blue-700">
            <h2 class="text-xl text-center text-blue-700 font-bold mb-4">INFORMACIÓN</h2>
            <div class="bg-white rounded-lg">
                <div class="mb-4 rounded-xl">
                    <p>Dirección: ${auction.address}</p>
                    <p>Código Postal: ${auction.postalCode}</p>
                    <p>Localidad: ${auction.city}</p>
                    <p>Provincia: ${auction.province}</p>
                    <p>Fecha Inicio: ${auction.startDate}</p>
                    <p>Fecha Conclusión: ${auction.endDate}</p>
                    <p>Tipo de Subasta: ${auction.auctionType}</p>
                    <p>Enlace Subasta: <a href="${auction.link}" class="text-blue-500">Haz click aquí</a></p>
                    <p>Valor Subasta: ${auction.value}</p>
                    <p>Tasación: ${auction.appraisalValue}</p>
                    <p>Importe Depósito: ${auction.deposit}</p>
                    <p>Puja Mínima: ${auction.minBid}</p>
                    <p>Tramos entre Pujas: ${auction.bidSteps}</p>
                </div>
                <div class="text-center">
                    <button class="bg-blue-700 hover:bg-black text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full transition duration-300 ease-in-out">
                        DESCARGA PDF DE COMPRA Y VENTA
                    </button>
                </div>
            </div>
        </div>
    `;
}

function renderColumna3(auction) {
  const columna3Element = document.createElement("div");
  columna3Element.className =
    "text-blue-700 p-6 rounded-xl mb-4 bg-white border-3 border-blue-700";

  const titleElement = document.createElement("h2");
  titleElement.className = "text-xl text-center font-bold mb-4";
  titleElement.textContent = "LOCALIZACIÓN SUBASTA";

  const mapElement = renderGoogleMap(
    auction.coordinates.latitude,
    auction.coordinates.longitude
  );
  columna3Element.appendChild(titleElement);
  columna3Element.appendChild(mapElement);

  const galleryElement = renderImageGallery(auction.images); // Se asegura de que el array de imágenes se pase correctamente
  columna3Element.appendChild(galleryElement);

  return columna3Element;
}



loadAuctionDetails();
