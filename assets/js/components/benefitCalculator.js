document.addEventListener("DOMContentLoaded", () => {
    const recalcularBtn = document.getElementById("recalcular-btn");
    const calcularResultadoBtn = document.getElementById("calcular-resultado-btn");

    // Función para formatear los valores en el formato deseado
    function formatearValor(valor) {
        return valor.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
    }

    // Función para convertir cualquier valor de entrada a número
    function convertirAFloat(valor) {
        if (typeof valor === 'string') {
            valor = valor.replace(/\./g, '').replace(',', '.').replace('€', '').trim();
        }
        return parseFloat(valor) || 0;
    }

    if (recalcularBtn) {
        recalcularBtn.addEventListener("click", () => {
            const precioVivienda = convertirAFloat(document.getElementById("precio-vivienda").value);
            const precioTrastero = convertirAFloat(document.getElementById("precio-trastero").value);
            const precioGaraje = convertirAFloat(document.getElementById("precio-garaje").value);

            const precioVentaEstimado = (precioVivienda * metrosVivienda) +
                                        (precioTrastero * metrosTrastero) +
                                        (precioGaraje * metrosGaraje);

            document.getElementById("precio-venta").textContent = formatearValor(precioVentaEstimado);
        });
    }

    if (calcularResultadoBtn) {
        calcularResultadoBtn.addEventListener("click", () => {
            const purchasePrice = convertirAFloat(document.getElementById("precio-compra").value);
            const precioVenta = convertirAFloat(document.getElementById("precio-venta").textContent);

            const itp = document.getElementById("itp").checked ? purchasePrice * 0.1 : 0;
            const gastosNotarialesCompra = document.getElementById("gastos-notariales-compra").checked ? 1000 : 0;
            const registroPropiedadCompra = document.getElementById("registro-propiedad-compra").checked ? 500 : 0;
            const gastosAdministrativosCompra = document.getElementById("gastos-administrativos-compra").checked ? 800 : 0;
            const gastosAnadidos = document.getElementById("gastos-anadidos").checked ? purchasePrice * 0.07 : 0;

            const totalCompraTrasGastos = purchasePrice + itp + gastosNotarialesCompra + registroPropiedadCompra + gastosAdministrativosCompra + gastosAnadidos;

            const irpf = document.getElementById("irpf").checked ? precioVenta * 0.21 : 0;
            const comisionVenta = document.getElementById("comision-venta").checked ? precioVenta * 0.03 : 0;
            const gastosNotarialesVenta = document.getElementById("gastos-notariales-venta").checked ? 1000 : 0;
            const registroPropiedadVenta = document.getElementById("registro-propiedad-venta").checked ? 500 : 0;
            const gastosRegistro = document.getElementById("gastos-registro").checked ? 500 : 0;

            const totalCostosVenta = irpf + comisionVenta + gastosNotarialesVenta + registroPropiedadVenta + gastosRegistro;

            const beneficio = precioVenta - totalCompraTrasGastos - totalCostosVenta;
            const beneficioPorcentaje = (beneficio / totalCompraTrasGastos) * 100;

            document.getElementById("resultados-calculadora").innerHTML = `
                <div class="col-6 text-left">
                    <div class="bg-gray-400 p-3 rounded-lg mb-2 shadow-md">
                        <h5 class="text-sm font-semibold text-white">COMPRA TRAS GASTOS:</h5>
                        <p class="text-xl font-bold text-white">${formatearValor(totalCompraTrasGastos)}</p>
                    </div>
                    <div class="bg-gray-400 p-3 rounded-lg shadow-md">
                        <h5 class="text-sm font-semibold text-white">COSTOS DE VENTA:</h5>
                        <p class="text-xl font-bold text-white">${formatearValor(totalCostosVenta)}</p>
                    </div>
                </div>
                <div class="col-6 text-right">
                    <div class="bg-green-600 text-white p-3 rounded-lg mb-2 shadow-md">
                        <h5 class="text-sm font-semibold">BENEFICIO (€):</h5>
                        <p class="text-xl font-bold">${formatearValor(beneficio)}</p>
                    </div>
                    <div class="bg-green-600 text-white p-3 rounded-lg shadow-md">
                        <h5 class="text-sm font-semibold">BENEFICIO (%):</h5>
                        <p class="text-xl font-bold">${beneficioPorcentaje.toFixed(2)}%</p>
                    </div>
                </div>
            `;
        });
    }
});
