// components/BenefitCalculator.js
import { useState } from "react";

export default function BenefitCalculator() {
  const [purchasePrice, setPurchasePrice] = useState(0);
  const [benefit, setBenefit] = useState(null);
  const [benefitPercent, setBenefitPercent] = useState(null);

  const handleCalculate = () => {
    const additionalCosts = 136241.6; // Constante
    const salePrice = 650250.0; // Constante

    const totalCost = purchasePrice + additionalCosts;
    const calculatedBenefit = salePrice - totalCost;
    const calculatedBenefitPercent = (calculatedBenefit / totalCost) * 100;

    setBenefit(calculatedBenefit.toFixed(2));
    setBenefitPercent(calculatedBenefitPercent.toFixed(2));
  };

  return (
    <div className="p-4 border rounded-lg shadow-md">
      <h3 className="text-lg font-semibold">Calculadora Beneficio</h3>
      <div className="mb-4">
        <label
          className="block text-gray-700 text-sm font-bold mb-2"
          htmlFor="purchasePrice"
        >
          Ingresa un Precio de Compra:
        </label>
        <input
          type="number"
          id="purchasePrice"
          value={purchasePrice}
          onChange={(e) => setPurchasePrice(parseFloat(e.target.value))}
          className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
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
          <p>Beneficio (€): {benefit}</p>
          <p>Beneficio (%): {benefitPercent}%</p>
        </div>
      )}
    </div>
  );
}
