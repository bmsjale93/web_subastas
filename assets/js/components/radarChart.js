export function renderRadarChart(qualities) {
  const labels = Object.keys(qualities);
  const data = Object.values(qualities);

  const container = document.createElement("div");
  container.className = "relative h-80 w-full bg-white";

  const canvas = document.createElement("canvas");
  container.appendChild(canvas);

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
                  size: 14,
                },
                color: "#1D4ED8",
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
                color: "#333",
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
                },
                color: "#333",
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

  return container;
}
