export function renderBenefitCalculator(auction) {
  const container = document.createElement("div");
  container.className =
    "bg-white p-6 rounded-xl shadow-md mb-4 border-3 border-blue-700";

  container.innerHTML = `
    <h3 class="text-xl text-center font-bold text-blue-700">CALCULADORA BENEFICIO</h3>
    <div class="mb-4">
        <label class="block text-gray-700 text-sm text-center font-bold mb-2 mt-4">Ingresa un Precio de Compra:</label>
        <input type="number" id="purchasePrice" class="appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" />
    </div>
    <button class="bg-blue-700 hover:bg-black text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full transition duration-300 ease-in-out">
        Calcular Resultado
    </button>
    <div id="benefitResults" class="mt-4"></div>
  `;

  const purchasePriceInput = container.querySelector("#purchasePrice");
  const benefitResults = container.querySelector("#benefitResults");
  const button = container.querySelector("button");

  button.addEventListener("click", () => {
    const purchasePrice = parseFloat(purchasePriceInput.value) || 0;
    const additionalCostsPercentage = 7;
    const notaryCosts = 1000;
    const registryCosts = 500;
    const administrativeCosts = 800;

    // Calcula los costos adicionales
    const additionalCosts =
      (purchasePrice * additionalCostsPercentage) / 100 +
      notaryCosts +
      registryCosts +
      administrativeCosts;
    const totalPurchaseCost = purchasePrice + additionalCosts;

    // Asegurarse de que todos los valores están presentes y son números válidos
    const constructedArea = parseFloat(auction.constructedArea) || 0;
    const squareMeterValue = parseFloat(auction.squareMeterValue) || 0;
    const garageArea = parseFloat(auction.garageArea) || 0;
    const garageSquareMeterValue =
      parseFloat(auction.garageSquareMeterValue) || 0;
    const storageRoomArea = parseFloat(auction.storageRoomArea) || 0;
    const storageRoomSquareMeterValue =
      parseFloat(auction.storageRoomSquareMeterValue) || 0;

    // Calcula el precio de venta recomendado
    const calculatedSalePrice =
      constructedArea * squareMeterValue +
      garageArea * garageSquareMeterValue +
      storageRoomArea * storageRoomSquareMeterValue;

    // Asegurarse de que el precio de venta recomendado es un número válido
    const salePriceIsValid =
      !isNaN(calculatedSalePrice) && calculatedSalePrice > 0;

    // Cálculo del beneficio y porcentaje
    const calculatedBenefit = calculatedSalePrice - totalPurchaseCost;
    const calculatedBenefitPercent =
      totalPurchaseCost !== 0
        ? (calculatedBenefit / totalPurchaseCost) * 100
        : 0;

    // Mostrar los resultados
    benefitResults.innerHTML = `
      <p class="text-base font-medium">Compra tras Gastos Añadidos: ${totalPurchaseCost.toFixed(
        2
      )}€</p>
      <p class="text-base font-medium">% de Valor de la Subasta: ${
        salePriceIsValid
          ? ((totalPurchaseCost / calculatedSalePrice) * 100).toFixed(2)
          : "0.00"
      }%</p>
      <p class="text-base font-medium">Precio Venta Recomendado: ${calculatedSalePrice.toFixed(
        2
      )}€</p>
      <div>
        <p class="text-green-600 rounded mt-2 text-base font-semibold">Beneficio (€): ${calculatedBenefit.toFixed(
          2
        )}€</p>
        <p class="text-green-600 rounded text-base font-semibold">Beneficio (%): ${calculatedBenefitPercent.toFixed(
          2
        )}%</p>
      </div>
    `;
  });

  return container;
}
