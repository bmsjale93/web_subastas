import { Radar } from "react-chartjs-2"; // Importa el componente Radar de react-chartjs-2
import {
  Chart as ChartJS,
  RadialLinearScale,
  PointElement,
  LineElement,
  Filler,
  Tooltip,
  Legend,
} from "chart.js"; // Importa los módulos necesarios de chart.js

// Registra los componentes de ChartJS necesarios para el gráfico radar
ChartJS.register(
  RadialLinearScale,
  PointElement,
  LineElement,
  Filler,
  Tooltip,
  Legend
);

// Componente RadarChart que muestra un gráfico radar
export default function RadarChart({ qualities }) {
  // Datos del gráfico
  const data = {
    labels: Object.keys(qualities), // Etiquetas basadas en las claves de qualities
    datasets: [
      {
        label: "Valoración",
        data: Object.values(qualities), // Datos basados en los valores de qualities
        backgroundColor: "rgba(29, 78, 216, 0.28)", // Color de fondo del área del radar
        borderColor: "rgba(29, 78, 216, 1)", // Color del borde del área del radar
        borderWidth: 2, // Ancho del borde
        pointBackgroundColor: "rgba(29, 78, 216, 1)", // Color de fondo de los puntos
        pointBorderColor: "#fff", // Color del borde de los puntos
        pointHoverBackgroundColor: "#fff", // Color de fondo de los puntos al pasar el ratón
        pointHoverBorderColor: "rgba(54, 162, 235, 1)", // Color del borde de los puntos al pasar el ratón
      },
    ],
  };

  // Opciones de configuración del gráfico
  const options = {
    responsive: true,
    maintainAspectRatio: false, // No mantener la relación de aspecto
    plugins: {
      legend: {
        position: "top", // Posición de la leyenda
        labels: {
          font: {
            size: 14, // Tamaño de fuente de las etiquetas de la leyenda
          },
          color: "#1D4ED8", // Color de las etiquetas de la leyenda
        },
      },
      tooltip: {
        callbacks: {
          label: function (context) {
            return `${context.dataset.label}: ${context.raw}`; // Formato de las etiquetas de los tooltips
          },
        },
      },
    },
    scales: {
      r: {
        beginAtZero: true, // Comenzar desde cero
        max: 10, // Valor máximo de la escala
        ticks: {
          stepSize: 1, // Tamaño del paso de las ticks
          backdropColor: "rgba(255, 255, 255, 0)", // Color de fondo de las ticks
          color: "#333", // Color de las ticks
        },
        grid: {
          color: "rgba(200, 200, 200, 0.5)", // Color de la cuadrícula
        },
        angleLines: {
          color: "rgba(200, 200, 200, 0.5)", // Color de las líneas angulares
        },
        pointLabels: {
          font: {
            size: 14, // Tamaño de fuente de las etiquetas de los puntos
          },
          color: "#333", // Color de las etiquetas de los puntos
        },
      },
    },
  };

  return (
    <div className="relative h-80 w-full">
      {/* Renderiza el gráfico radar */}
      <Radar data={data} options={options} />
    </div>
  );
}
