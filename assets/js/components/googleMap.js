import { GOOGLE_MAPS_API_KEY } from "../../../config.api.js";

export function renderGoogleMap(container, lat, lng) {
  if (typeof lat !== "number" || typeof lng !== "number") {
    console.error("Latitud y longitud no son válidas:", lat, lng);
    return;
  }

  function initializeMap() {
    if (window.google && window.google.maps) {
      const map = new window.google.maps.Map(container, {
        center: { lat: lat, lng: lng },
        zoom: 15,
      });

      new window.google.maps.Marker({
        position: { lat: lat, lng: lng },
        map: map,
      });
    } else {
      console.error("Google Maps JavaScript API not loaded properly.");
    }
  }

  if (window.google && window.google.maps) {
    // Si Google Maps ya está cargado, inicializa el mapa
    initializeMap();
  } else {
    // Carga el script de Google Maps
    const script = document.getElementById("googleMaps");

    if (!script) {
      const newScript = document.createElement("script");
      newScript.src = `https://maps.googleapis.com/maps/api/js?key=${GOOGLE_MAPS_API_KEY}&libraries=places`;
      newScript.id = "googleMaps";
      newScript.async = true;
      newScript.defer = true;
      newScript.onload = initializeMap;
      newScript.onerror = () => {
        console.error("Error loading Google Maps script");
      };
      document.head.appendChild(newScript);
    } else {
      script.onload = initializeMap;
    }
  }
}
