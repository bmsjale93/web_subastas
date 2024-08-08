// pages/api/login.js
import pool from "../../utils/db";
import bcrypt from "bcryptjs";

export default async function handler(req, res) {
  if (req.method === "POST") {
    const { usuario, contrasena } = req.body;

    if (!usuario || !contrasena) {
      return res
        .status(400)
        .json({ message: "Usuario y contraseña son requeridos" });
    }

    try {
      const [rows] = await pool.query(
        "SELECT * FROM USUARIOS WHERE usuario = ?",
        [usuario]
      );

      console.log("Usuarios encontrados:", rows);

      if (rows.length === 0) {
        return res.status(401).json({ message: "Usuario no encontrado" });
      }

      const user = rows[0];
      console.log("Usuario encontrado:", user);

      const isPasswordMatch = await bcrypt.compare(contrasena, user.contrasena);
      console.log("Contraseña coincide:", isPasswordMatch);

      if (!isPasswordMatch) {
        return res.status(401).json({ message: "Contraseña incorrecta" });
      }

      res.status(200).json({ message: "Inicio de sesión exitoso" });
    } catch (error) {
      console.error("Error al iniciar sesión:", error);
      res.status(500).json({ message: "Error del servidor" });
    }
  } else {
    res.status(405).json({ message: "Método no permitido" });
  }
}