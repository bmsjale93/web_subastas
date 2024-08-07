// components/Button.js
"use client"; // Indica que este es un componente del cliente

export default function Button({
  text = "Login",
  bgColor = "bg-blue-700",
  hoverColor = "hover:bg-black",
  textColor = "text-white",
  fontSize = "text-base",
  padding = "py-3 px-4",
  width = "w-full",
  onClick = () => {},
}) {
  return (
    <button
      className={`${bgColor} ${hoverColor} ${textColor} ${fontSize} ${padding} ${width} rounded-md focus:outline-none focus:shadow-outline transition duration-300 ease-in-out`}
      type="button"
      onClick={onClick}
    >
      {text}
    </button>
  );
}
