"use client"; // Indica que este es un componente del cliente

// Componente Button que permite la personalización a través de props
export default function Button({
  text = "Login", // Texto predeterminado del botón
  bgColor = "bg-blue-700", // Color de fondo predeterminado
  hoverColor = "hover:bg-black", // Color de fondo al pasar el cursor
  textColor = "text-white", // Color del texto predeterminado
  fontSize = "text-base", // Tamaño de fuente predeterminado
  padding = "py-3 px-4", // Padding predeterminado
  width = "w-full", // Ancho predeterminado
  onClick = () => {}, // Función de clic predeterminada
}) {
  return (
    <button
      className={`${bgColor} ${hoverColor} ${textColor} ${fontSize} ${padding} ${width} rounded-md focus:outline-none focus:shadow-outline transition duration-300 ease-in-out`}
      type="button"
      onClick={onClick} // Manejador de clics
    >
      {text} {/* Texto del botón */}
    </button>
  );
}
