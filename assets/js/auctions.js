// Definición de las subastas (similar al archivo constants/auctions.js)
const auctions = [
  {
    id: "SUB0001",
    title: "CALLE ANCORA, 4",
    value: "152.000,00€",
    endDate: "2024-08-24T18:00:00Z",
    process: "Terminada",
    // Otros campos...
  },
  {
    id: "SUB0002",
    title: "AVENIDA DE LAS CIVILIZACIONES, Nº 28",
    value: "100.154,34 €",
    endDate: "5/08/2024",
    process: "Terminada",
    // Otros campos...
  },
  // Añadir aquí el resto de subastas...
];

const processColors = {
  Activa: "bg-green-200 text-green-800",
  "Estudiando...": "bg-yellow-200 text-yellow-800",
  Terminada: "bg-red-200 text-red-800",
};

// Función para crear una tarjeta de subasta
function createAuctionCard(auction) {
  const card = document.createElement("div");
  card.className =
    "bg-white p-4 rounded-lg border border-gray-400 shadow-md flex flex-col justify-between transform transition-transform duration-300 hover:scale-105 hover:shadow-xl";

  card.innerHTML = `
    <h3 class="text-lg font-semibold">${auction.title}</h3>
    <p class="text-gray-700 text-base">Valor Subasta: ${auction.value}</p>
    <p class="text-gray-700 text-base mt-[-12px]">Fin de Subasta: ${new Date(
      auction.endDate
    ).toLocaleDateString()}</p>
    <div class="flex items-center justify-between mt-2">
      <a href="subasta_detalle.html?id=${
        auction.id
      }" class="bg-blue-700 hover:bg-black text-white font-bold py-2 px-3 rounded focus:outline-none focus:shadow-outline transition duration-300 ease-in-out">
        Ver Subasta
      </a>
      <span class="${processColors[auction.process]} px-3 py-2 rounded-xl">
        ${auction.process}
      </span>
    </div>
  `;

  return card;
}

// Selecciona el contenedor de la lista de subastas
const auctionsList = document.getElementById("auctionsList");

// Itera sobre las subastas y añade cada una al DOM
auctions.forEach((auction) => {
  const auctionCard = createAuctionCard(auction);
  auctionsList.appendChild(auctionCard);
});
