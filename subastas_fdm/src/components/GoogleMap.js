import { useEffect, useRef } from "react";

export default function GoogleMap({ latitude, longitude }) {
  const mapRef = useRef(null);

  useEffect(() => {
    const initializeMap = () => {
      if (mapRef.current) {
        const map = new window.google.maps.Map(mapRef.current, {
          center: { lat: latitude, lng: longitude },
          zoom: 15,
        });

        new window.google.maps.Marker({
          position: { lat: latitude, lng: longitude },
          map: map,
        });
      }
    };

    // Cargar el script de Google Maps si no está ya cargado
    if (!window.google) {
      const script = document.createElement("script");
      script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyAasBL6-2h6lWlG8U9Ew3VD4-QkVvGePdA`;
      script.async = true;
      script.defer = true;
      script.onload = initializeMap;
      document.head.appendChild(script);
    } else {
      initializeMap();
    }
  }, [latitude, longitude]);

  return <div ref={mapRef} style={{ width: "100%", height: "300px" }} />;
}
