import { useEffect, useRef } from "react";

// Componente GoogleMap que muestra un mapa de Google con un marcador en una ubicación específica
export default function GoogleMap({ latitude, longitude }) {
  const mapRef = useRef(null); // Referencia para el elemento del DOM que contendrá el mapa

  useEffect(() => {
    // Función para inicializar el mapa de Google
    const initializeMap = () => {
      if (mapRef.current && window.google && window.google.maps) {
        // Crea un nuevo mapa de Google centrado en las coordenadas proporcionadas
        const map = new window.google.maps.Map(mapRef.current, {
          center: { lat: latitude, lng: longitude },
          zoom: 15,
        });

        // Añade un marcador en las coordenadas proporcionadas
        new window.google.maps.Marker({
          position: { lat: latitude, lng: longitude },
          map: map,
        });
      }
    };

    // Función para cargar el script de Google Maps si no está ya cargado
    const loadScript = () => {
      const existingScript = document.getElementById("googleMaps");

      if (!existingScript) {
        // Crea un nuevo elemento de script para cargar Google Maps
        const script = document.createElement("script");
        script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyAasBL6-2h6lWlG8U9Ew3VD4-QkVvGePdA&libraries=places`;
        script.id = "googleMaps";
        script.async = true;
        script.defer = true;
        script.onload = () => {
          initializeMap(); // Inicializa el mapa una vez que el script se ha cargado
        };
        script.onerror = () => {
          console.error("Error loading Google Maps script"); // Maneja errores de carga del script
        };
        document.head.appendChild(script); // Añade el script al head del documento
      } else {
        if (existingScript.getAttribute("data-loaded")) {
          initializeMap(); // Inicializa el mapa si el script ya está cargado
        } else {
          existingScript.onload = initializeMap; // Añade un evento onload para inicializar el mapa cuando el script se cargue
        }
      }
    };

    loadScript(); // Llama a la función para cargar el script de Google Maps
  }, [latitude, longitude]); // Vuelve a ejecutar el efecto si cambian las coordenadas

  return <div ref={mapRef} style={{ width: "100%", height: "300px" }} />; // Devuelve un div que contendrá el mapa
}
