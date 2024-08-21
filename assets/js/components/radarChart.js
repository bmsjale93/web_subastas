export function renderRadarChart(qualities) {
  const labels = Object.keys(qualities);
  const data = Object.values(qualities);

  const canvas = document.createElement("canvas");
  canvas.style.width = "100%";

  // Función para ajustar la altura del canvas según el tamaño de la pantalla
  function adjustCanvasHeight() {
    const screenWidth = window.innerWidth;

    if (screenWidth >= 1200) {
      // Pantallas grandes (escritorio)
      canvas.style.height = "700px";
    } else if (screenWidth >= 768) {
      // Pantallas medianas (tabletas, iPad)
      canvas.style.height = "500px";
    } else {
      // Pantallas pequeñas (móviles)
      canvas.style.height = "300px";
    }
  }

  // Llamar a la función para ajustar la altura al cargar la página
  adjustCanvasHeight();

  // Ajustar la altura cuando se redimensiona la ventana
  window.addEventListener("resize", adjustCanvasHeight);

  function loadChartScript(callback) {
    if (typeof Chart !== "undefined") {
      callback();
    } else {
      const script = document.createElement("script");
      script.src = "https://cdn.jsdelivr.net/npm/chart.js";
      script.onload = callback;
      document.head.appendChild(script);
    }
  }

  function initializeChart() {
    const ctx = canvas.getContext("2d");
    if (ctx) {
      new Chart(ctx, {
        type: "radar",
        data: {
          labels: labels,
          datasets: [
            {
              label: "Valoración",
              data: data,
              backgroundColor: "rgba(29, 78, 216, 0.28)",
              borderColor: "rgba(29, 78, 216, 1)",
              borderWidth: 2,
              pointBackgroundColor: "rgba(29, 78, 216, 1)",
              pointBorderColor: "#fff",
              pointHoverBackgroundColor: "#fff",
              pointHoverBorderColor: "rgba(54, 162, 235, 1)",
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: "top",
              labels: {
                font: {
                  size: 16,
                  family: "'Poppins', sans-serif",
                  weight: 500,
                },
                color: "#000",
              },
            },
            tooltip: {
              callbacks: {
                label: function (context) {
                  return context.dataset.label + ": " + context.raw;
                },
              },
            },
          },
          scales: {
            r: {
              beginAtZero: true,
              max: 10,
              ticks: {
                stepSize: 1,
                backdropColor: "rgba(255, 255, 255, 0)",
                color: "#000",
                font: {
                  size: 12,
                  family: "'Roboto', sans-serif",
                },
              },
              grid: {
                color: "rgba(200, 200, 200, 0.5)",
              },
              angleLines: {
                color: "rgba(200, 200, 200, 0.5)",
              },
              pointLabels: {
                font: {
                  size: 14,
                  weight: 500,
                  family: "'Poppins', sans-serif",
                },
                color: "#000",
              },
            },
          },
        },
      });
    } else {
      console.error("No se pudo obtener el contexto 2D del canvas.");
    }
  }

  loadChartScript(initializeChart);

  return canvas; // Devuelve el canvas en lugar del contenedor
}
