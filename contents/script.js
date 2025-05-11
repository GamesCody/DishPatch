const cityCoords = {
  warszawa: [52.2297, 21.0122],
  krakow: [50.0647, 19.945],
  czestochowa: [50.8099, 19.1195]
};
//lista restauracji z koordynatami po wyborze z listy, heh
const restaurantData = {
  warszawa: [
    { name: "Restauracja Polska", coords: [52.2297, 21.0122], link: "/rezerwacja/polska" },
    { name: "Pizza Max", coords: [52.2300, 21.0150], link: "/rezerwacja/pizzamax" }
  ],
  krakow: [
    { name: "Pierogarnia Krakowska", coords: [50.0650, 19.9455], link: "/rezerwacja/pierogi" }
  ],
  czestochowa: [{name:"Czeski Film Pub & Restauracja", coords: [50.811623846244665, 19.114154577299644], link: "/rezerwacja/pub"}]
};

const map = L.map('map').setView([52.2297, 21.0122], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap'
}).addTo(map);

const citySelect = document.getElementById('citySelect');
const restaurantList = document.getElementById('restaurantList');
let currentMarkers = [];

citySelect.addEventListener('change', () => {
  const city = citySelect.value;
  restaurantList.innerHTML = ''; 
  currentMarkers.forEach(marker => map.removeLayer(marker));
  currentMarkers = [];

  if (city && cityCoords[city]) {
    map.setView(cityCoords[city], 13);
    const restaurants = restaurantData[city] || [];

    if (restaurants.length === 0) {
      restaurantList.innerHTML = '<p>Brak dostępnych restauracji w tym mieście.</p>';
    } else {
      // wyświetlamy restauracje
      restaurants.forEach(res => {
        const card = document.createElement('div');
        card.className = 'restaurant-card';
        card.innerHTML = `
          <h3>${res.name}</h3>
          <a href="${res.link}" class="btn">Zarezerwuj stolik</a>
          <button class="btn order-btn" data-name="${res.name}">Zamów jedzenie</button>
        `;

        // karta restauracji z listy
        restaurantList.appendChild(card);

        //marker na mapie
        const marker = L.marker(res.coords).addTo(map)
          .bindPopup(`<b>${res.name}</b><br><a href="${res.link}">Zarezerwuj</a>`);
        currentMarkers.push(marker);
      });
    }
  }
});
