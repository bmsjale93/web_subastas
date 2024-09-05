document.addEventListener("DOMContentLoaded", function () {
  const calendarGrid = document.getElementById("calendarDates");
  const currentMonth = document.getElementById("currentMonth");
  const subastaFechas = JSON.parse(
    document.getElementById("subastaFechas").value
  );

  let currentDate = new Date();

  function renderCalendar(date) {
    const year = date.getFullYear();
    const month = date.getMonth();
    const today = new Date();
    const firstDay = new Date(year, month, 1).getDay();
    const lastDate = new Date(year, month + 1, 0).getDate();

    // Set the month and year in the header
    currentMonth.textContent = date.toLocaleString("es-ES", {
      month: "long",
      year: "numeric",
    });

    // Clear the previous calendar
    calendarGrid.innerHTML = "";

    // Create empty divs before the first day of the month
    for (let i = 0; i < firstDay; i++) {
      const emptyDiv = document.createElement("div");
      emptyDiv.classList.add("p-2");
      calendarGrid.appendChild(emptyDiv);
    }

    // Fill in the days of the month
    for (let i = 1; i <= lastDate; i++) {
      const fullDate = `${year}-${String(month + 1).padStart(2, "0")}-${String(
        i
      ).padStart(2, "0")}`;
      const dateElement = document.createElement("div");
      dateElement.classList.add(
        "p-2",
        "rounded-full",
        "cursor-pointer",
        "hover:bg-blue-400",
        "hover:text-white",
        "text-gray-700",
        "text-center"
      );

      // Resaltar día actual sin usar toISOString()
      if (
        fullDate ===
        `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(
          2,
          "0"
        )}-${String(today.getDate()).padStart(2, "0")}`
      ) {
        dateElement.classList.add("border", "border-blue-700", "today");
      }

      // Resaltar días con subastas
      if (subastaFechas.includes(fullDate)) {
        dateElement.classList.add("bg-blue-700", "text-white");
      }

      dateElement.innerText = i;

      // Agregar evento de clic para filtrar subastas
      dateElement.addEventListener("click", function () {
        const todayFormatted = `${today.getFullYear()}-${String(
          today.getMonth() + 1
        ).padStart(2, "0")}-${String(today.getDate()).padStart(2, "0")}`;

        if (fullDate === todayFormatted) {
          showAllSubastas();
        } else {
          filterSubastasByDate(fullDate);
        }
      });

      calendarGrid.appendChild(dateElement);
    }
  }

  document.getElementById("prevMonth").addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar(currentDate);
  });

  document.getElementById("nextMonth").addEventListener("click", () => {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar(currentDate);
  });

  function filterSubastasByDate(date) {
    const subastaItems = document.querySelectorAll(".subasta-item");
    subastaItems.forEach((item) => {
      if (item.dataset.date === date) {
        item.style.display = "";
      } else {
        item.style.display = "none";
      }
    });
  }

  function showAllSubastas() {
    const subastaItems = document.querySelectorAll(".subasta-item");
    subastaItems.forEach((item) => {
      item.style.display = "";
    });
  }

  renderCalendar(currentDate);
});
