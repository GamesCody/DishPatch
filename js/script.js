const cityCoords = {
  warszawa: [52.2297, 21.0122],
  krakow: [50.0647, 19.945],
  czestochowa: [50.8099, 19.1195]
};

const restaurantData = {
  warszawa: [
    { name: "Restauracja Polska", coords: [52.2297, 21.0122], link: "/rezerwacja/polska", hours: "10:00-22:00" },
    { name: "Pizza Max", coords: [52.2300, 21.0150], link: "/rezerwacja/pizzamax", hours: "11:00-23:00" },
    { name: "Sushi Sakura", coords: [52.2285, 21.0080], link: "/rezerwacja/sakura", hours: "12:00-21:00" },
    { name: "Burger House", coords: [52.2320, 21.0180], link: "/rezerwacja/burgerhouse", hours: "09:00-20:00" }
  ],
  krakow: [
    { name: "Pierogarnia Krakowska", coords: [50.0650, 19.9455], link: "/rezerwacja/pierogi", hours: "10:00-22:00" },
    { name: "Wawel Bistro", coords: [50.0614, 19.9372], link: "/rezerwacja/wawel", hours: "11:00-21:00" },
    { name: "Kraków Grill", coords: [50.0670, 19.9420], link: "/rezerwacja/grill", hours: "12:00-23:00" },
    { name: "Cafe Rynek", coords: [50.0640, 19.9450], link: "/rezerwacja/cafe", hours: "08:00-20:00" }
  ],
  czestochowa: [
    { name: "Czeski Film Pub & Restauracja", coords: [50.8116, 19.1141], link: "/rezerwacja/pub", hours: "10:00-22:00" },
    { name: "Jasna Góra Bistro", coords: [50.8120, 19.1150], link: "/rezerwacja/jasnagora", hours: "09:00-21:00" },
    { name: "Pizza Często", coords: [50.8100, 19.1120], link: "/rezerwacja/pizzaczesto", hours: "11:00-23:00" },
    { name: "Restauracja Aleja", coords: [50.8130, 19.1160], link: "/rezerwacja/aleja", hours: "12:00-22:00" }
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
      <div class="restaurant-hours">Godziny otwarcia: ${restaurant.hours}</div>
    `;
    restaurantList.appendChild(card);

    // Dodanie markera na mapę
    if (map) {
      const marker = L.marker(restaurant.coords).addTo(map)
        .bindPopup(`<b>${restaurant.name}</b><br>Godziny otwarcia: ${restaurant.hours}<br><a href="${restaurant.link}">Zarezerwuj</a>`);
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


// AJAX obsługa rejestracji (nowy formularz)
const regForm = document.getElementById('registrationForm');
if (regForm) {
  regForm.addEventListener('submit', function (e) {
    e.preventDefault();
    // Pobierz token reCAPTCHA v2 z pola formularza
    const recaptchaToken = document.querySelector('#registrationForm [name="g-recaptcha-response"]').value;
    if (!recaptchaToken) {
      alert('Potwierdź reCAPTCHA.');
      return;
    }
    const formData = new FormData(regForm);
    formData.append('action', 'register');
    formData.append('recaptcha', recaptchaToken);
    fetch('api/auth.php', {
      method: 'POST',
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        if (data.success) {
          regForm.reset();
          document.getElementById('registerForm').style.visibility = 'hidden';
        }
      })
      .catch(() => alert('Błąd połączenia z serwerem.'));
  });
}

// AJAX obsługa logowania
const logForm = document.querySelector('form#loginForm');
if (logForm) {
  logForm.addEventListener('submit', function (e) {
    e.preventDefault();
    // Pobierz token reCAPTCHA v2 z pola formularza
    const recaptchaToken = document.querySelector('#loginForm [name="g-recaptcha-response"]').value;
    if (!recaptchaToken) {
      alert('Potwierdź reCAPTCHA.');
      return;
    }
    const formData = new FormData(logForm);
    formData.append('action', 'login');
    formData.append('recaptcha', recaptchaToken);
    fetch('api/auth.php', {
      method: 'POST',
      body: formData
    })
      .then(res => res.json())
      .then(data => {
        alert(data.message);
        if (data.success) {
          logForm.reset();
          document.getElementById('loginForm').style.visibility = 'hidden';
          window.location.href = 'subsites/user.html';
        }
      })
      .catch(() => alert('Błąd połączenia z serwerem.'));
  });
}

function openMenu() {
    document.getElementById('menu').style.visibility = 'visible';
}
function closeMenu() {
    document.getElementById('menu').style.visibility = 'hidden';
}
function openContactInfo() {
    document.getElementById('contactCard').style.visibility = 'visible';
}
function closeContactInfo() {
    document.getElementById('contactCard').style.visibility = 'hidden';
}
function openRegisterForm() {
    document.getElementById('registerForm').style.visibility = 'visible';
}
function closeRegisterForm() {
    document.getElementById('registerForm').style.visibility = 'hidden';
}
function openLoginForm() {
    document.getElementById('loginForm').style.visibility = 'visible';
}
function closeLoginForm() {
    document.getElementById('loginForm').style.visibility = 'hidden';
}

function openNewTab() {
    window.open("http://localhost/DishPatch/subsites/about.html", "_blank");
}

function togglePassword(passwordInputId, btn) {
  const passwordInput = document.getElementById(passwordInputId);
  if (!passwordInput) return;
  if (passwordInput.type === 'password') {
    passwordInput.type = 'text';
    btn.textContent = '🙈';
  } else {
    passwordInput.type = 'password';
    btn.textContent = '👁️';
  }
}