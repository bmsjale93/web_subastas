document.addEventListener("DOMContentLoaded", () => {
  let currentStep = 1;
  const totalSteps = 8;

  // Función para mostrar el paso actual
  function showStep(step) {
    document.querySelectorAll(".step").forEach((stepEl) => {
      stepEl.classList.add("hidden");
    });
    document.querySelector("#step" + step).classList.remove("hidden");
  }

  // Actualiza la barra de progreso según el paso actual
  function updateProgressBar() {
    const progressBar = document.getElementById("progressBar");
    const progressPercentage = (currentStep / totalSteps) * 100;
    progressBar.style.width = progressPercentage + "%";
  }

  function validateStep(step) {
    const stepElement = document.getElementById("step" + step);
    const inputs = stepElement.querySelectorAll(
      "input[required], select[required]"
    );

    for (const input of inputs) {
      if (!input.value.trim()) {
        input.focus();
        alert("Por favor, complete todos los campos requeridos.");
        return false;
      }
    }
    return true;
  }

  // Manejo del botón "Siguiente"
  function nextStep(event) {
    event.preventDefault();

    if (validateStep(currentStep)) {
      if (currentStep < totalSteps) {
        currentStep++;
        showStep(currentStep);
        updateProgressBar();
      }
      updateButtons();
    }
  }

  // Manejo del botón "Atrás"
  function prevStep(event) {
    event.preventDefault(); // Prevenir cualquier acción predeterminada
    event.stopPropagation(); // Detener la propagación para prevenir el cierre del modal

    if (currentStep > 1) {
      currentStep--;
      showStep(currentStep);
      updateProgressBar();
    }
    updateButtons();
  }

  // Actualiza la visibilidad y estado de los botones
  function updateButtons() {
    const prevBtn = document.getElementById("prevStepBtn");
    const nextBtn = document.getElementById("nextStepBtn");
    const submitBtn = document.getElementById("submitBtn");

    // Deshabilitar el botón "Atrás" si estamos en el primer paso
    prevBtn.disabled = currentStep === 1;

    // Mostrar u ocultar los botones de "Siguiente" y "Enviar"
    nextBtn.classList.toggle("hidden", currentStep === totalSteps);
    submitBtn.classList.toggle("hidden", currentStep !== totalSteps);
  }

  showStep(currentStep);
  updateButtons();

  document.getElementById("prevStepBtn").addEventListener("click", prevStep);
  document.getElementById("nextStepBtn").addEventListener("click", nextStep);

  // Escuchar el evento submit para validar y enviar el formulario
  const form = document.getElementById("crearSubastaForm");
  form.addEventListener("submit", function (event) {
    if (currentStep !== totalSteps || !validateStep(totalSteps)) {
      event.preventDefault();
      alert("Complete todos los pasos antes de enviar.");
    }
  });

  const modalElement = document.getElementById("crearSubastaModal");

  modalElement.addEventListener("hidden.bs.modal", function () {
    document.body.classList.remove("modal-open");
    document.body.style.overflow = "";
    document.body.style.paddingRight = "";

    currentStep = 1;
    showStep(currentStep);
    updateProgressBar();
    updateButtons();
  });

  const dropZones = document.querySelectorAll(".drop-zone");

  dropZones.forEach((zone) => {
    const inputElement = zone.querySelector('input[type="file"]');

    zone.addEventListener("click", () => inputElement.click());

    zone.addEventListener("dragover", (e) => {
      e.preventDefault();
      zone.classList.add("bg-blue-50");
    });

    zone.addEventListener("dragleave", () => {
      zone.classList.remove("bg-blue-50");
    });

    zone.addEventListener("drop", (e) => {
      e.preventDefault();
      zone.classList.remove("bg-blue-50");
      inputElement.files = e.dataTransfer.files;

      const fileList = Array.from(inputElement.files)
        .map((file) => file.name)
        .join(", ");
      zone.querySelector(
        "p"
      ).textContent = `Archivos seleccionados: ${fileList}`;
    });
  });
});
