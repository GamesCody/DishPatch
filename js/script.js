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

if (!mapElement || !citySelect || !restaurantList) {
  console.error('Nie znaleziono wymaganych elementów DOM');
}

// Inicjalizacja mapy
let map;
try {
  map = L.map('map').setView([52.2297, 21.0122], 6);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);
} catch (error) {
  console.error('Błąd inicjalizacji mapy:', error);
}

let currentMarkers = [];

// Czyści wszystkie poprzednie markery
function clearMarkers() {
  currentMarkers.forEach(marker => {
    if (map && map.hasLayer(marker)) {
      map.removeLayer(marker);
    }
  });
  currentMarkers = [];
}

// Wyświetla restauracje i markery
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

// Obsługa kliknięć przycisków "Zamów"
function handleOrderButtons() {
  restaurantList.addEventListener('click', (event) => {
    if (event.target.classList.contains('order-btn')) {
      const restaurantName = event.target.getAttribute('data-name');
      alert(`Funkcja zamówienia dla restauracji "${restaurantName}" zostanie wkrótce wdrożona!`);
    }
  });
}

// Obsługa zmiany miasta
function handleCityChange() {
  const city = citySelect.value;

  restaurantList.innerHTML = '';
  clearMarkers();

  if (city && cityCoords[city]) {
    map.setView(cityCoords[city], 13);
    displayRestaurants(city);
  } else {
    map.setView([52.2297, 21.0122], 6); // Widok ogólny na Polskę
  }
}

// Inicjalizacja
document.addEventListener('DOMContentLoaded', () => {
  if (citySelect && citySelect.value) {
    handleCityChange();
  }
});

if (citySelect) {
  citySelect.addEventListener('change', handleCityChange);
}

handleOrderButtons();

// Dodaj proste style (opcjonalnie)
const style = document.createElement('style');
style.textContent = `
  .restaurant-card {
    margin-bottom: 15px;
    padding: 10px;
    background: #f9f9f9;
    border: 1px solid #ddd;
    border-radius: 5px;
  }
  .restaurant-card .btn {
    margin-right: 10px;
    padding: 6px 12px;
    background: #2ca081;
    color: #fff;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    text-decoration: none;
  }
  .restaurant-card .btn:hover {
    background: #1e3e72;
  }
`;

document.head.appendChild(style);

//wyświetl okno do logowania

document.getElementById('loginIcon').addEventListener('click', function () {
  const loginregister = document.getElementById('logreg');
  loginregister.classList.toggle('visible');
});

document.getElementById('login').addEventListener('click', function () {
  const loginForm = document.getElementById('log');
  loginForm.classList.toggle('visible');
});

document.getElementById('register').addEventListener('click', function () {
  const registerForm = document.getElementById('reg');
  registerForm.classList.toggle('visible');
});

