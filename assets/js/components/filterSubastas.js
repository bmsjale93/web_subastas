// filterSubastas.js
document.addEventListener("DOMContentLoaded", function () {
  const subastaItems = document.querySelectorAll(".subasta-item");
  const locationFilter = document.getElementById("locationFilter");
  const valueFilter = document.getElementById("valueFilter");
  const resetFiltersButton = document.getElementById("resetFilters");
  const applyFiltersButton = document.getElementById("applyFilters");
  const valorSeleccionado = document.getElementById("valorSeleccionado");

  function applyFilters() {
    const selectedLocation = locationFilter.value;
    const selectedValue = parseFloat(valueFilter.value);

    subastaItems.forEach((item) => {
      const itemLocation = item.getAttribute("data-location");
      const itemValue = parseFloat(
        item.getAttribute("data-value").replace(",", "")
      );

      let showItem = true;

      if (selectedLocation && selectedLocation !== itemLocation) {
        showItem = false;
      }

      if (itemValue > selectedValue) {
        showItem = false;
      }

      item.style.display = showItem ? "" : "none";
    });

    // Actualizar la visualización del valor seleccionado
    valorSeleccionado.textContent = selectedValue.toLocaleString("es-ES", {
      style: "currency",
      currency: "EUR",
    });
  }

  // Evento de clic para aplicar los filtros
  applyFiltersButton.addEventListener("click", applyFilters);

  // Evento para actualizar el valor seleccionado en tiempo real mientras se ajusta el rango
  valueFilter.addEventListener("input", function () {
    valorSeleccionado.textContent = parseFloat(
      valueFilter.value
    ).toLocaleString("es-ES", {
      style: "currency",
      currency: "EUR",
    });
  });

  // Reset filters when the user clicks the reset button
  resetFiltersButton.addEventListener("click", function () {
    locationFilter.value = "";
    valueFilter.value = valueFilter.max;

    // Mostrar todas las subastas
    subastaItems.forEach((item) => {
      item.style.display = "";
    });

    // Actualizar la visualización del valor seleccionado al valor máximo
    valorSeleccionado.textContent = parseFloat(valueFilter.max).toLocaleString(
      "es-ES",
      {
        style: "currency",
        currency: "EUR",
      }
    );
  });

  // Establecer el valor inicial del filtro de rango al cargar la página
  valueFilter.value = valueFilter.max;
  valorSeleccionado.textContent = parseFloat(valueFilter.max).toLocaleString(
    "es-ES",
    {
      style: "currency",
      currency: "EUR",
    }
  );

  // Inicialmente mostrar todas las subastas
  subastaItems.forEach((item) => {
    item.style.display = "";
  });
});
