export function renderCountdown(endDate) {
  const countdownElement = document.createElement("span");

  const calculateTimeLeft = () => {
    const difference = +new Date(endDate) - +new Date();
    let timeLeft = {};

    if (difference > 0) {
      timeLeft = {
        dias: Math.floor(difference / (1000 * 60 * 60 * 24)),
        horas: Math.floor((difference / (1000 * 60 * 60)) % 24),
        minutos: Math.floor((difference / 1000 / 60) % 60),
        segundos: Math.floor((difference / 1000) % 60),
      };
    } else {
      timeLeft = null;
    }

    return timeLeft;
  };

  const formatTime = (value) => String(value).padStart(2, "0");

  // Declaración de intervalId antes de la función updateCountdown
  let intervalId;

  const updateCountdown = () => {
    const timeLeft = calculateTimeLeft();

    if (timeLeft) {
      countdownElement.textContent = `${formatTime(
        timeLeft.dias
      )}d ${formatTime(timeLeft.horas)}h ${formatTime(
        timeLeft.minutos
      )}m ${formatTime(timeLeft.segundos)}s`;
    } else {
      countdownElement.textContent = "Subasta finalizada";
      clearInterval(intervalId);
    }
  };

  updateCountdown();
  intervalId = setInterval(updateCountdown, 1000); // Inicialización de intervalId

  return countdownElement;
}
