document.getElementById("loginForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const usuario = document.getElementById("username").value;
  const contrasena = document.getElementById("password").value;
  const errorElement = document.getElementById("error");
  errorElement.classList.add("hidden");
  errorElement.textContent = "";

  // Crear una solicitud AJAX para enviar los datos al servidor
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "login.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

  xhr.onreadystatechange = function () {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      try {
        const response = JSON.parse(xhr.responseText);
        if (response.success) {
          // Redirigir a la página de subastas si el login es exitoso
          window.location.href = "subastas.php";
        } else {
          // Mostrar el mensaje de error
          errorElement.textContent =
            response.message || "Ocurrió un error inesperado";
          errorElement.classList.remove("hidden");
        }
      } catch (error) {
        // Manejar el caso donde la respuesta no sea JSON
        errorElement.textContent =
          "Error al procesar la respuesta del servidor.";
        errorElement.classList.remove("hidden");
      }
    }
  };

  // Enviar los datos al servidor
  xhr.send(
    `usuario=${encodeURIComponent(usuario)}&contrasena=${encodeURIComponent(
      contrasena
    )}`
  );
});
