document.addEventListener("DOMContentLoaded", () => {
    const recalcularBtn = document.getElementById("recalcular-btn");
    const calcularResultadoBtn = document.getElementById("calcular-resultado-btn");

    function formatearValor(valor) {
        return valor.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
    }

    function convertirAFloat(valor) {
        if (typeof valor === 'string') {
            valor = valor.replace(/\./g, '').replace(',', '.').replace('€', '').trim();
        }
        return parseFloat(valor) || 0;
    }

    function calcularAjusteAntiguedad(anoConstruccion, valorEstimado) {
        const antiguedad = 2024 - anoConstruccion;
        if (antiguedad > 40) {
            return valorEstimado * 0.7;
        } else if (antiguedad > 35) {
            return valorEstimado * 0.725;
        } else if (antiguedad > 30) {
            return valorEstimado * 0.75;
        } else if (antiguedad > 25) {
            return valorEstimado * 0.775;
        } else if (antiguedad > 20) {
            return valorEstimado * 0.8;
        } else if (antiguedad > 15) {
            return valorEstimado * 0.9;
        } else if (antiguedad > 10) {
            return valorEstimado * 0.95;
        } else {
            return valorEstimado;
        }
    }

    function recalcularValores() {
        const precioMetroVivienda = convertirAFloat(document.getElementById("precio-metro-vivienda").value);
        const precioMetroTrastero = convertirAFloat(document.getElementById("precio-metro-trastero").value);
        const precioMetroGaraje = convertirAFloat(document.getElementById("precio-metro-garaje").value);

        const valorVivienda = metrosVivienda * precioMetroVivienda;
        const valorTerraza = metrosTerraza * precioMetroVivienda * 0.5;
        const valorZonasComunes = metrosZonasComunes * precioMetroVivienda * 0.4;
        const valorGaraje = metrosGaraje * precioMetroGaraje;
        const valorTrastero = metrosTrastero * precioMetroTrastero;

        console.log("Valor Trastero Calculado:", valorTrastero);

        let precioVentaEstimado = valorVivienda + valorTerraza + valorZonasComunes + valorGaraje + valorTrastero;

        precioVentaEstimado = calcularAjusteAntiguedad(anoConstruccion, precioVentaEstimado);

        document.getElementById("precio-venta").textContent = formatearValor(precioVentaEstimado);
    }


    if (recalcularBtn) {
        recalcularBtn.addEventListener("click", recalcularValores);
    }

    if (calcularResultadoBtn) {
        calcularResultadoBtn.addEventListener("click", () => {
            const purchasePrice = convertirAFloat(document.getElementById("precio-compra").value);
            const precioVenta = convertirAFloat(document.getElementById("precio-venta").textContent);

            const itp = document.getElementById("itp").checked ? purchasePrice * 0.075 : 0;
            const gastosNotarialesCompra = document.getElementById("gastos-notariales-compra").checked ? 1000 : 0;
            const registroPropiedadCompra = document.getElementById("registro-propiedad-compra").checked ? 500 : 0;
            const gastosAdministrativosCompra = document.getElementById("gastos-administrativos-compra").checked ? 800 : 0;
            const gastosAnadidos = document.getElementById("gastos-anadidos").checked ? purchasePrice * 0.07 : 0;
            const comisionVenta = document.getElementById("comision-venta").checked ? precioVenta * 0.03 : 0;
            const gastosDesalojo = document.getElementById("gastos-desalojo").checked ? 5000 : 0;
            const cargaSubastasGasto = document.getElementById("carga_subastas").checked ? convertirAFloat(cargaSubastas) : 0;

            const totalGastos = itp + gastosNotarialesCompra + registroPropiedadCompra + gastosAdministrativosCompra + gastosAnadidos + comisionVenta + gastosDesalojo + cargaSubastasGasto;

            const totalCompraTrasGastos = purchasePrice + totalGastos;

            document.getElementById("resultados-calculadora").innerHTML = `
                <div class="col-6 text-left">
                    <div class="bg-gray-400 p-3 rounded-lg mb-2 shadow-md">
                        <h5 class="text-sm font-semibold text-white">COMPRA TRAS GASTOS:</h5>
                        <p class="text-xl font-bold text-white">${formatearValor(totalCompraTrasGastos)}</p>
                    </div>
                    <div class="bg-gray-400 p-3 rounded-lg shadow-md">
                        <h5 class="text-sm font-semibold text-white">TOTAL GASTOS (SIN COMPRA):</h5>
                        <p class="text-xl font-bold text-white">${formatearValor(totalGastos)}</p>
                    </div>
                </div>
                <div class="col-6 text-right">
                    <div class="bg-green-600 text-white p-3 rounded-lg mb-2 shadow-md">
                        <h5 class="text-sm font-semibold">BENEFICIO (€):</h5>
                        <p class="text-xl font-bold">${formatearValor(precioVenta - totalCompraTrasGastos)}</p>
                    </div>
                    <div class="bg-green-600 text-white p-3 rounded-lg shadow-md">
                        <h5 class="text-sm font-semibold">BENEFICIO (%):</h5>
                        <p class="text-xl font-bold">${((precioVenta - totalCompraTrasGastos) / totalCompraTrasGastos * 100).toFixed(2)}%</p>
                    </div>
                </div>
            `;
        });
    }
});
