"use client";

import Image from "next/image";
import logo from "../../public/logo-fdm.png";
import Button from "@/components/Button";
import { useRouter } from "next/navigation";
import { useState } from "react";

export default function Login() {
  const router = useRouter();
  const [usuario, setUsuario] = useState("");
  const [contrasena, setContrasena] = useState("");
  const [error, setError] = useState("");

  const handleLogin = async (e) => {
    e.preventDefault();
    setError("");

    try {
      const res = await fetch("/api/login", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ usuario, contrasena }),
      });

      const data = await res.json();

      if (res.status === 200) {
        // Redirige a la página de subastas
        router.push("/subastas");
      } else {
        setError(data.message);
      }
    } catch (error) {
      console.error("Error al iniciar sesión:", error);
      setError("Error al iniciar sesión");
    }
  };

  return (
    <div className="min-h-screen flex items-center justify-center bg-gradient-to-b from-blue-800 to-black">
      <div className="bg-white shadow-lg rounded-[40px] overflow-hidden w-full max-w-lg flex flex-col md:flex-row mx-16 sm:mx-16">
        <div className="w-full py-16 flex flex-col items-center justify-center">
          <div className="text-center mb-6">
            <Image
              src={logo}
              alt="Logo"
              className="mx-auto mb-4 w-48 h-48 sm:w-40 sm:h-40 md:w-40 md:h-40"
            />
          </div>
          <form onSubmit={handleLogin}>
            <div className="mb-4">
              <label
                className="block text-gray-700 text-sm font-bold mb-2"
                htmlFor="username"
              >
                Usuario
              </label>
              <input
                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:shadow-outline"
                id="username"
                type="text"
                placeholder="Introduce tu usuario"
                value={usuario}
                onChange={(e) => setUsuario(e.target.value)}
              />
            </div>
            <div className="mb-6">
              <label
                className="block text-gray-700 text-sm font-bold mb-2"
                htmlFor="password"
              >
                Contraseña
              </label>
              <input
                className="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:shadow-outline"
                id="password"
                type="password"
                placeholder="Introduce tu contraseña"
                value={contrasena}
                onChange={(e) => setContrasena(e.target.value)}
              />
            </div>
            {error && (
              <p className="text-red-500 text-xs italic mb-4">{error}</p>
            )}
            <div className="flex items-center justify-between">
              <Button text="Iniciar Sesión" />
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}
