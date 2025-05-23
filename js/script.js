const cityCoords = {
  warszawa: [52.2297, 21.0122],
  krakow: [50.0647, 19.945],
  czestochowa: [50.8099, 19.1195]
};

const restaurantData = {
  warszawa: [
    { name: "Restauracja Polska", coords: [52.2297, 21.0122], link: "/rezerwacja/polska" },
    { name: "Pizza Max", coords: [52.2300, 21.0150], link: "/rezerwacja/pizzamax" }
  ],
  krakow: [
    { name: "Pierogarnia Krakowska", coords: [50.0650, 19.9455], link: "/rezerwacja/pierogi" }
  ],
  czestochowa: [
    { name: "Czeski Film Pub & Restauracja", coords: [50.8116, 19.1141], link: "/rezerwacja/pub" }
  ]
};

// Sprawdzenie czy elementy DOM istnieją
const mapElement = document.getElementById('map');
const citySelect = document.getElementById('citySelect');
const restaurantList = document.getElementById('restaurantList');
const controlsElement = document.getElementById('controls');

if (!mapElement || !citySelect || !restaurantList || !controlsElement) {
  console.error('Nie znaleziono wymaganych elementów DOM');
}

// Inicjalizacja mapy z obsługą błędów
let map;
try {
  map = L.map('map').setView([52.2297, 21.0122], 13);
  
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);
} catch (error) {
  console.error('Błąd inicjalizacji mapy:', error);
}

let currentMarkers = [];
let userLocationMarker = null;
let routeControl = null;
let userCoords = null;

// Funkcja obliczająca dystans w kilometrach (wzór Haversine)
function calculateDistance(lat1, lon1, lat2, lon2) {
  const R = 6371; // promień Ziemi w km
  const dLat = (lat2 - lat1) * Math.PI / 180;
  const dLon = (lon2 - lon1) * Math.PI / 180;
  const a = 
    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
    Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
    Math.sin(dLon / 2) * Math.sin(dLon / 2);
  const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
  return (R * c).toFixed(2);
}

// Funkcja usuwająca poprzednie informacje o odległości
function removeDistanceInfo() {
  const oldInfo = document.querySelector('#controls .distance-info');
  if (oldInfo) {
    oldInfo.remove();
  }
}

// Funkcja czyszcząca markery z mapy
function clearMarkers() {
  currentMarkers.forEach(marker => {
    if (map && map.hasLayer(marker)) {
      map.removeLayer(marker);
    }
  });
  currentMarkers = [];
}

// Funkcja usuwająca trasę z mapy
function clearRoute() {
  if (routeControl && map) {
    map.removeControl(routeControl);
    routeControl = null;
  }
}

// Funkcja dodająca marker lokalizacji użytkownika
function addUserLocationMarker(lat, lon) {
  if (userLocationMarker && map) {
    map.removeLayer(userLocationMarker);
  }
  
  if (map) {
    // Tworzenie niestandardowego markera dla lokalizacji użytkownika
    const userIcon = L.divIcon({
      className: 'user-location-marker',
      html: '<div style="background-color: #4285f4; width: 15px; height: 15px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 6px rgba(0,0,0,0.3);"></div>',
      iconSize: [21, 21],
      iconAnchor: [10, 10]
    });
    
    userLocationMarker = L.marker([lat, lon], { icon: userIcon })
      .addTo(map)
      .bindPopup('<b>Twoja lokalizacja</b>')
      .openPopup();
  }
}

// Funkcja tworząca trasę do wybranego miasta
function createRoute(userLat, userLon, cityCoords) {
  if (!map) return;
  
  // Usuń poprzednią trasę
  clearRoute();
  
  try {
    // Tworzenie kontrolki trasy z użyciem Leaflet Routing Machine
    routeControl = L.Routing.control({
      waypoints: [
        L.latLng(userLat, userLon),
        L.latLng(cityCoords[0], cityCoords[1])
      ],
      routeWhileDragging: false,
      addWaypoints: false,
      createMarker: function() { return null; }, // Nie tworzymy dodatkowych markerów
      lineOptions: {
        styles: [{ color: '#6FA1EC', weight: 4, opacity: 0.7 }]
      },
      show: false, // Ukryj panel z instrukcjami
      collapsible: true
    }).on('routesfound', function(e) {
      const routes = e.routes;
      const summary = routes[0].summary;
      
      // Wyświetl informacje o trasie
      const routeInfo = document.createElement('div');
      routeInfo.className = 'route-info';
      routeInfo.innerHTML = `
        <p style="margin: 5px 0; color: #2c5aa0;"><strong>Trasa:</strong></p>
        <p style="margin: 5px 0;">📏 Dystans: ${(summary.totalDistance / 1000).toFixed(2)} km</p>
        <p style="margin: 5px 0;">⏱️ Czas podróży: ${Math.round(summary.totalTime / 60)} min</p>
      `;
      routeInfo.style.marginTop = '10px';
      routeInfo.style.padding = '10px';
      routeInfo.style.backgroundColor = '#f5f5f5';
      routeInfo.style.borderRadius = '5px';
      routeInfo.style.border = '1px solid #ddd';
      
      controlsElement.appendChild(routeInfo);
      
      // Dopasuj widok mapy do trasy
      const group = new L.featureGroup([
        L.marker([userLat, userLon]),
        L.marker(cityCoords)
      ]);
      map.fitBounds(group.getBounds().pad(0.1));
    }).addTo(map);
    
  } catch (error) {
    console.warn('Leaflet Routing Machine nie jest dostępne. Tworząca prostą linię.');
    
    // Fallback: prosta linia między punktami
    const routeLine = L.polyline([
      [userLat, userLon],
      cityCoords
    ], {
      color: '#6FA1EC',
      weight: 4,
      opacity: 0.7,
      dashArray: '10, 10'
    }).addTo(map);
    
    currentMarkers.push(routeLine);
    
    // Dopasuj widok do linii
    map.fitBounds(routeLine.getBounds().pad(0.1));
    
    // Wyświetl podstawowe informacje
    const distance = calculateDistance(userLat, userLon, cityCoords[0], cityCoords[1]);
    const routeInfo = document.createElement('div');
    routeInfo.className = 'route-info';
    routeInfo.innerHTML = `
      <p style="margin: 5px 0; color: #2c5aa0;"><strong>Odległość w linii prostej:</strong></p>
      <p style="margin: 5px 0;">📏 ${distance} km</p>
    `;
    routeInfo.style.marginTop = '10px';
    routeInfo.style.padding = '10px';
    routeInfo.style.backgroundColor = '#f5f5f5';
    routeInfo.style.borderRadius = '5px';
    routeInfo.style.border = '1px solid #ddd';
    
    controlsElement.appendChild(routeInfo);
  }
}

// Funkcja wyświetlająca restauracje
function displayRestaurants(city) {
  const restaurants = restaurantData[city] || [];
  
  if (restaurants.length === 0) {
    restaurantList.innerHTML = '<p>Brak dostępnych restauracji w tym mieście.</p>';
    return;
  }
  
  restaurantList.innerHTML = '';
  
  restaurants.forEach(restaurant => {
    const card = document.createElement('div');
    card.className = 'restaurant-card';
    card.innerHTML = `
      <h3>${restaurant.name}</h3>
      <a href="${restaurant.link}" class="btn">Zarezerwuj stolik</a>
      <button class="btn order-btn" data-name="${restaurant.name}">Zamów jedzenie</button>
    `;
    restaurantList.appendChild(card);
    
    // Dodanie markera na mapę
    if (map) {
      const marker = L.marker(restaurant.coords).addTo(map)
        .bindPopup(`<b>${restaurant.name}</b><br><a href="${restaurant.link}">Zarezerwuj</a>`);
      currentMarkers.push(marker);
    }
  });
}

// Funkcja pobierająca lokalizację użytkownika
function getUserLocationAndShowRoute(city) {
  if (!navigator.geolocation) {
    console.warn('Geolokalizacja nie jest obsługiwana przez tę przeglądarkę');
    return;
  }
  
  // Dodaj komunikat o ładowaniu
  const loadingInfo = document.createElement('p');
  loadingInfo.className = 'distance-info loading';
  loadingInfo.textContent = 'Pobieranie lokalizacji...';
  loadingInfo.style.marginTop = '10px';
  loadingInfo.style.color = '#666';
  controlsElement.appendChild(loadingInfo);
  
  navigator.geolocation.getCurrentPosition(
    (position) => {
      const userLat = position.coords.latitude;
      const userLon = position.coords.longitude;
      const [cityLat, cityLon] = cityCoords[city];
      const distance = calculateDistance(userLat, userLon, cityLat, cityLon);
      
      // Zapisz współrzędne użytkownika
      userCoords = [userLat, userLon];
      
      // Usuń komunikat o ładowaniu
      const loading = document.querySelector('.loading');
      if (loading) loading.remove();
      
      // Dodaj marker lokalizacji użytkownika
      addUserLocationMarker(userLat, userLon);
      
      // Utwórz trasę do miasta
      createRoute(userLat, userLon, cityCoords[city]);
      
      // Wyświetl odległość
      const distanceInfo = document.createElement('p');
      distanceInfo.className = 'distance-info';
      distanceInfo.textContent = `Odległość od miasta ${city.charAt(0).toUpperCase() + city.slice(1)}: ${distance} km`;
      distanceInfo.style.marginTop = '10px';
      distanceInfo.style.fontWeight = 'bold';
      distanceInfo.style.color = '#2c5aa0';
      
      controlsElement.appendChild(distanceInfo);
    },
    (error) => {
      console.error('Błąd pobierania lokalizacji użytkownika:', error.message);
      
      // Usuń komunikat o ładowaniu
      const loading = document.querySelector('.loading');
      if (loading) loading.remove();
      
      // Wyświetlenie przyjaznego komunikatu dla użytkownika
      const errorInfo = document.createElement('p');
      errorInfo.className = 'distance-info error';
      let errorMessage = 'Nie udało się pobrać Twojej lokalizacji';
      
      switch(error.code) {
        case error.PERMISSION_DENIED:
          errorMessage = 'Odmówiono dostępu do lokalizacji. Sprawdź ustawienia przeglądarki.';
          break;
        case error.POSITION_UNAVAILABLE:
          errorMessage = 'Lokalizacja jest niedostępna.';
          break;
        case error.TIMEOUT:
          errorMessage = 'Przekroczono czas oczekiwania na lokalizację.';
          break;
      }
      
      errorInfo.textContent = errorMessage;
      errorInfo.style.marginTop = '10px';
      errorInfo.style.color = '#d32f2f';
      errorInfo.style.fontSize = '14px';
      
      controlsElement.appendChild(errorInfo);
    },
    {
      timeout: 15000,
      enableHighAccuracy: true,
      maximumAge: 300000 // 5 minut
    }
  );
}

// Obsługa przycisków zamówienia
function handleOrderButtons() {
  restaurantList.addEventListener('click', (event) => {
    if (event.target.classList.contains('order-btn')) {
      const restaurantName = event.target.getAttribute('data-name');
      alert(`Funkcja zamówienia dla restauracji "${restaurantName}" zostanie wkrótce wdrożona!`);
    }
  });
}

// Główna funkcja obsługująca zmianę miasta
function handleCityChange() {
  const city = citySelect.value;
  
  // Czyszczenie poprzednich danych
  restaurantList.innerHTML = '';
  clearMarkers();
  clearRoute();
  removeDistanceInfo();
  
  // Usuń poprzednie informacje o trasie
  const oldRouteInfo = document.querySelector('.route-info');
  if (oldRouteInfo) oldRouteInfo.remove();
  
  if (city && cityCoords[city]) {
    // Wyświetlenie restauracji
    displayRestaurants(city);
    
    // Jeśli mamy już lokalizację użytkownika, użyj jej
    if (userCoords) {
      addUserLocationMarker(userCoords[0], userCoords[1]);
      createRoute(userCoords[0], userCoords[1], cityCoords[city]);
      
      const distance = calculateDistance(userCoords[0], userCoords[1], cityCoords[city][0], cityCoords[city][1]);
      const distanceInfo = document.createElement('p');
      distanceInfo.className = 'distance-info';
      distanceInfo.textContent = `Odległość od miasta ${city.charAt(0).toUpperCase() + city.slice(1)}: ${distance} km`;
      distanceInfo.style.marginTop = '10px';
      distanceInfo.style.fontWeight = 'bold';
      distanceInfo.style.color = '#2c5aa0';
      controlsElement.appendChild(distanceInfo);
    } else {
      // Pobierz lokalizację i pokaż trasę
      getUserLocationAndShowRoute(city);
    }
  } else {
    // Jeśli nie wybrano miasta, pokaż tylko lokalizację użytkownika
    if (map && userCoords) {
      map.setView(userCoords, 13);
    } else {
      map.setView([52.2297, 21.0122], 6); // Widok na Polskę
    }
  }
}

// Inicjalizacja event listenerów
if (citySelect) {
  citySelect.addEventListener('change', handleCityChange);
}

// Inicjalizacja obsługi przycisków zamówienia
handleOrderButtons();

// Automatyczne pobranie lokalizacji przy załadowaniu strony
document.addEventListener('DOMContentLoaded', () => {
  // Pobierz lokalizację użytkownika na start
  if (navigator.geolocation && map) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        userCoords = [position.coords.latitude, position.coords.longitude];
        addUserLocationMarker(userCoords[0], userCoords[1]);
        map.setView(userCoords, 10);
      },
      (error) => {
        console.log('Nie można pobrać lokalizacji na start:', error.message);
      },
      { timeout: 10000, enableHighAccuracy: false }
    );
  }
  
  // Jeśli jest wybrane miasto, załaduj je
  if (citySelect && citySelect.value) {
    handleCityChange();
  }
});

// Dodaj style CSS dla markera użytkownika (jeśli nie są zdefiniowane)
const style = document.createElement('style');
style.textContent = `
  .user-location-marker {
    background: transparent;
  }
  .route-info {
    font-size: 14px;
  }
  .distance-info.loading {
    font-style: italic;
  }
`;
document.head.appendChild(style);