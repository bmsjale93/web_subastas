// utils/hashPassword.js
const bcrypt = require("bcryptjs");

const usuario = "admin";
const contrasena = "password123";

bcrypt.hash(contrasena, 10, (err, hash) => {
  if (err) {
    console.error("Error al hashear la contraseña:", err);
  } else {
    console.log(
      `INSERT INTO USUARIOS (usuario, contrasena) VALUES ('${usuario}', '${hash}');`
    );
  }
});
