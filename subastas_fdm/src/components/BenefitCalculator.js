import { useState } from "react";

export default function BenefitCalculator({ auction }) {
  const [purchasePrice, setPurchasePrice] = useState(0);
  const [totalCost, setTotalCost] = useState(0);
  const [salePrice, setSalePrice] = useState(0); // Inicializa el precio de venta en 0
  const [benefit, setBenefit] = useState(null);
  const [benefitPercent, setBenefitPercent] = useState(null);

  const handleCalculate = () => {
    const additionalCostsPercentage = 7; // 7% de gastos añadidos
    const notaryCosts = 1000; // 1000€ de gastos notariales
    const registryCosts = 500; // 500€ de registro de la propiedad
    const administrativeCosts = 800; // 800€ de gastos administrativos

    // Calcula los costos adicionales
    const additionalCosts =
      (purchasePrice * additionalCostsPercentage) / 100 +
      notaryCosts +
      registryCosts +
      administrativeCosts;
    const totalPurchaseCost = purchasePrice + additionalCosts;

    // Calcula el precio de venta recomendado
    const calculatedSalePrice =
      auction.constructedArea * auction.squareMeterValue +
      (auction.garageArea * auction.garageSquareMeterValue || 0) +
      (auction.storageRoomArea * auction.storageRoomSquareMeterValue || 0);

    // Calcula el beneficio y el porcentaje de beneficio
    const calculatedBenefit = calculatedSalePrice - totalPurchaseCost;
    const calculatedBenefitPercent =
      (calculatedBenefit / totalPurchaseCost) * 100;

    // Actualiza los estados con los valores calculados
    setTotalCost(totalPurchaseCost.toFixed(2));
    setSalePrice(calculatedSalePrice.toFixed(2));
    setBenefit(calculatedBenefit.toFixed(2));
    setBenefitPercent(calculatedBenefitPercent.toFixed(2));
  };

  return (
    <div className="bg-white p-6 rounded-[28px] shadow-md mb-4 border-3 border-blue-700">
      <h3 className="text-[25px] text-center font-bold text-blue-700">
        CALCULADORA BENEFICIO
      </h3>
      <div className="mb-4">
        {/* Campo de entrada para el precio de compra */}
        <label
          className="block text-gray-700 text-sm text-center font-bold mb-2 mt-4"
          htmlFor="purchasePrice"
        >
          Ingresa un Precio de Compra:
        </label>
        <input
          type="number"
          id="purchasePrice"
          value={purchasePrice}
          onChange={(e) => setPurchasePrice(parseFloat(e.target.value))}
          className="appearance-none border rounded w-full py-3 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
        />
      </div>
      <button
        onClick={handleCalculate}
        className="bg-blue-700 hover:bg-black text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline w-full transition duration-300 ease-in-out"
      >
        Calcular Resultado
      </button>
      {benefit !== null && (
        <div className="mt-4">
          {/* Muestra los resultados del cálculo */}
          <p className="text-base font-medium">
            Compra tras Gastos Añadidos: {totalCost}€
          </p>
          <p className="text-base font-medium">
            % de Valor de la Subasta:{" "}
            {((totalCost / salePrice) * 100).toFixed(2)}%
          </p>
          <p className="text-base font-medium">
            Precio Venta Recomendado: {salePrice}€
          </p>
          <div>
            <p className="text-green-600 rounded mt-2 text-base font-semibold">
              Beneficio (€): {benefit}€
            </p>
            <p className="text-green-600 rounded text-base font-semibold">
              Beneficio (%): {benefitPercent}%
            </p>
          </div>
        </div>
      )}
    </div>
  );
}
