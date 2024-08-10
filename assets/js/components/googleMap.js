export function renderGoogleMap(lat, lng) {
  const mapContainer = document.createElement("div");
  mapContainer.style.height = "300px";
  mapContainer.style.width = "100%";

  function initializeMap() {
    if (window.google && window.google.maps) {
      const map = new window.google.maps.Map(mapContainer, {
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

  function loadScript() {
    const existingScript = document.getElementById("googleMaps");

    if (!existingScript) {
      const script = document.createElement("script");
      script.src = `https://maps.googleapis.com/maps/api/js?key=AIzaSyAasBL6-2h6lWlG8U9Ew3VD4-QkVvGePdA&libraries=places`;
      script.id = "googleMaps";
      script.async = true;
      script.defer = true;
      script.onload = initializeMap;
      script.onerror = () => {
        console.error("Error loading Google Maps script");
      };
      document.head.appendChild(script);
    } else {
      existingScript.onload = initializeMap;
    }
  }

  loadScript();

  return mapContainer;
}
