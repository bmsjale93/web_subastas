export function renderRadarChart(qualities) {
  const labels = Object.keys(qualities);
  const data = Object.values(qualities);

  const canvas = document.createElement("canvas");
  canvas.style.width = "100%";

  function adjustCanvasHeight() {
    const screenWidth = window.innerWidth;

    if (screenWidth >= 1200) {
      canvas.style.height = "700px";
    } else if (screenWidth >= 768) {
      canvas.style.height = "500px";
    } else {
      canvas.style.height = "600px";
    }
  }

  adjustCanvasHeight();
  window.addEventListener("resize", adjustCanvasHeight);

  function loadChartScript(callback) {
    if (typeof Chart !== "undefined") {
      loadChartDataLabels(callback);
    } else {
      const script = document.createElement("script");
      script.src =
        "https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js";
      script.onload = () => loadChartDataLabels(callback);
      document.head.appendChild(script);
    }
  }

  function loadChartDataLabels(callback) {
    if (typeof ChartDataLabels !== "undefined") {
      Chart.register(ChartDataLabels); // Registrar el plugin con Chart.js
      callback();
    } else {
      const script = document.createElement("script");
      script.src =
        "https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0";
      script.onload = () => {
        Chart.register(ChartDataLabels); // Registrar el plugin después de que se haya cargado
        callback();
      };
      document.head.appendChild(script);
    }
  }

  function initializeChart() {
    const ctx = canvas.getContext("2d");
    if (ctx) {
      new Chart(ctx, {
        type: "polarArea",
        data: {
          labels: labels,
          datasets: [
            {
              label: "Valoración",
              data: data,
              backgroundColor: labels.map((_, index) => {
                const colors = [
                  "rgba(255, 99, 132, 0.7)", // Rojo
                  "rgba(54, 162, 235, 0.7)", // Azul
                  "rgba(255, 206, 86, 0.7)", // Amarillo
                  "rgba(75, 192, 192, 0.7)", // Verde
                  "rgba(153, 102, 255, 0.7)", // Morado
                  "rgba(255, 159, 64, 0.7)", // Naranja
                  "rgba(99, 255, 132, 0.7)", // Verde Claro
                  "rgba(162, 54, 235, 0.7)", // Azul Morado
                  "rgba(206, 255, 86, 0.7)", // Verde Amarillo
                  "rgba(192, 75, 192, 0.7)", // Rosa
                  "rgba(102, 153, 255, 0.7)", // Azul Pastel
                  "rgba(159, 64, 255, 0.7)", // Morado Claro
                ];
                return colors[index % colors.length];
              }),
              borderColor: "rgba(255, 255, 255, 1)",
              borderWidth: 2,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          scales: {
            r: {
              beginAtZero: true,
              max: 10,
              ticks: {
                stepSize: 1,
                backdropColor: "rgba(255, 255, 255, 0)",
                color: "#444",
                font: {
                  size: 14,
                  family: "'Poppins', sans-serif",
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
                  size: 18,
                  weight: 600,
                  family: "'Poppins', sans-serif",
                },
                color: "#FFF",
              },
            },
          },
          plugins: {
            legend: {
              display: true,
              position: "top",
              align: "center",
              labels: {
                boxWidth: 20,
                font: {
                  size: 14,
                  family: "'Poppins', sans-serif",
                },
              },
            },
            tooltip: {
              callbacks: {
                label: function (context) {
                  return `${context.label}: ${context.raw}`;
                },
              },
            },
            datalabels: {
              display: true,
              color: "#fff",
              font: {
                weight: "bold",
                size: 14,
              },
              formatter: function (value) {
                return value;
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

  return canvas;
}
