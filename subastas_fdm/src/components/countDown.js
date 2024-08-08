import { useEffect, useState } from "react";

// Componente Countdown que muestra una cuenta regresiva hasta una fecha de finalización
export default function Countdown({ endDate }) {
  // Función para calcular el tiempo restante hasta la fecha de finalización
  const calculateTimeLeft = () => {
    const difference = +new Date(endDate) - +new Date(); // Diferencia entre la fecha de finalización y la fecha actual
    let timeLeft = {};

    if (difference > 0) {
      // Si la diferencia es positiva, calcula los días, horas, minutos y segundos restantes
      timeLeft = {
        dias: Math.floor(difference / (1000 * 60 * 60 * 24)),
        horas: Math.floor((difference / (1000 * 60 * 60)) % 24),
        minutos: Math.floor((difference / 1000 / 60) % 60),
        segundos: Math.floor((difference / 1000) % 60),
      };
    }

    return timeLeft; // Retorna el tiempo restante
  };

  const [timeLeft, setTimeLeft] = useState(calculateTimeLeft()); // Estado para almacenar el tiempo restante

  useEffect(() => {
    // Efecto para actualizar el tiempo restante cada segundo
    const timer = setTimeout(() => {
      setTimeLeft(calculateTimeLeft()); // Actualiza el estado con el nuevo tiempo restante
    }, 1000);

    return () => clearTimeout(timer); // Limpia el temporizador cuando el componente se desmonte
  }, [timeLeft]);

  // Función para formatear el tiempo con dos dígitos
  const formatTime = (value) => {
    return String(value).padStart(2, "0");
  };

  return (
    <div>
      {/* Muestra la cuenta regresiva si hay tiempo restante, de lo contrario muestra "Subasta finalizada" */}
      {timeLeft.dias !== undefined ? (
        <span>
          {formatTime(timeLeft.dias)}:{formatTime(timeLeft.horas)}:
          {formatTime(timeLeft.minutos)}:{formatTime(timeLeft.segundos)}
        </span>
      ) : (
        <span>Subasta finalizada</span>
      )}
    </div>
  );
}
