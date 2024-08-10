document.getElementById("loginForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const usuario = document.getElementById("username").value;
  const contrasena = document.getElementById("password").value;
  const errorElement = document.getElementById("error");
  errorElement.classList.add("hidden");
  errorElement.textContent = "";

  // Validación estática
  if (usuario === "admin" && contrasena === "password123") {
    // Redirige a la página de subastas
    window.location.href = "subastas.html";
  } else {
    // Muestra un mensaje de error
    errorElement.textContent = "Usuario o contraseña incorrectos";
    errorElement.classList.remove("hidden");
  }
});

// Configuración dinámica del botón (opcional)
const button = document.getElementById("loginButton");

// Puedes cambiar las propiedades aquí si es necesario
button.style.backgroundColor = "#1D4ED8"; // Azul Tailwind
button.addEventListener("mouseover", () => {
  button.style.backgroundColor = "#000"; // Negro
});
button.addEventListener("mouseout", () => {
  button.style.backgroundColor = "#1D4ED8"; // Azul Tailwind
});
