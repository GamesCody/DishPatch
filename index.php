<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DishPatch - Strona Główna</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <style>
        body { font-family: Arial, sans-serif; background: #f8f8f8; }
        header { background: linear-gradient(90deg, #00ff9e, #278e97); box-shadow: 0 2px 8px #eee; display: flex; align-items: center; justify-content: space-between; padding: 0 40px; height: 120px; }
        .logo { display: flex; align-items: center; }
        .logo img { height: 96px; cursor: pointer; }
        nav { display: flex; gap: 20px; }
        .menu-btn { background: #2d8f5a; color: #fff; border: none; padding: 12px 24px; border-radius: 6px; font-size: 1rem; cursor: pointer; transition: background 0.2s; }
        .menu-btn:hover { background: #226b41; }
        .dropdown { position: relative; display: inline-block; }
        .dropdown-content { display: none; position: absolute; background: #fff; min-width: 180px; box-shadow: 0 8px 16px rgba(0,0,0,0.1); z-index: 1; border-radius: 6px; }
        .dropdown-content button { width: 100%; text-align: left; border-radius: 0; border: none; background: none; color: #2d8f5a; padding: 12px 20px; font-size: 1rem; cursor: pointer; }
        .dropdown-content button:hover { background: #f0f0f0; }
        .dropdown:hover .dropdown-content { display: block; }
    </style>
</head>
<body>
    <header>
        <a href="index.php" class="logo"><img src="images/logo.png" alt="logo"></a>
        <div id="random-meal" style="flex:1;text-align:center;font-size:2.1rem;font-weight:bold;color:#000;"></div>
        <nav>
            <a href="dodatki/about.html" class="menu-btn" style="background:#fff;color:#2d8f5a;border:1px solid #2d8f5a;">O nas</a>
            <a href="contact.php" class="menu-btn" style="background:#fff;color:#2d8f5a;border:1px solid #2d8f5a;">Kontakt</a>
            <div class="dropdown">
                <button class="menu-btn">Zarejestruj się</button>
                <div class="dropdown-content">
                    <form action="register_user.html" method="get" style="margin:0;">
                        <button type="submit">Jako użytkownik</button>
                    </form>
                    <form action="register_restaurant.html" method="get" style="margin:0;">
                        <button type="submit">Jako restauracja</button>
                    </form>
                </div>
            </div>
            <form action="login.php" method="get" style="display:inline; margin:0;">
                <button class="menu-btn" type="submit">Zaloguj się</button>
            </form>
        </nav>
    </header>
    <!-- Pole wyszukiwania posiłków -->
    <div style="max-width:900px;margin:24px auto 0 auto;display:flex;justify-content:center;">
        <input id="meal-search" type="text" placeholder="Wyszukaj posiłek..." style="width:100%;max-width:400px;padding:12px 18px;font-size:1.1em;border-radius:8px;border:1px solid #ccc;box-shadow:0 2px 8px #eee;outline:none;">
    </div>
    <!-- Możesz dodać tu dalszą część landing page -->
    <div style="max-width:900px;margin:30px auto 0;">
        <label for="city-select" style="font-size:1.1em;font-weight:bold;">Wybierz miasto:</label>
        <select id="city-select" style="margin-left:12px;padding:8px 16px;border-radius:6px;">
            <option value="">-- wybierz --</option>
        </select>
    </div>
    <div id="cards-container" style="max-width:900px;margin:20px auto 0;display:flex;flex-wrap:wrap;gap:20px;"></div>
    <div id="map" style="height: 500px; width: 100%; margin: 40px auto 0; max-width: 900px; border-radius: 12px; box-shadow: 0 2px 12px #eee;"></div>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    const meals = [
        'Pizza Margherita', 'Sushi', 'Pierogi ruskie', 'Borszcz ukraiński', 'Burger wołowy',
        'Pad Thai', 'Tacos', 'Sałatka grecka', 'Kebab', 'Ramen',
        'Spaghetti Carbonara', 'Ceviche', 'Chaczapuri', 'Pho', 'Curry',
        'Falafel', 'Gulasz', 'Zupa Tom Yum', 'Tortilla', 'Sernik'
    ];
    let idx = 0;
    const mealDiv = document.getElementById('random-meal');
    function showMeal() {
        mealDiv.style.opacity = 0;
        setTimeout(() => {
            mealDiv.textContent = meals[idx];
            mealDiv.style.opacity = 1;
            idx = (idx + 1) % meals.length;
        }, 400);
    }
    mealDiv.style.transition = 'opacity 0.4s';
    showMeal();
    setInterval(showMeal, 2000 / 0.65);

    let allRestaurants = [];
    let map, markers = [];
    fetch('restaurants.php')
      .then(res => res.json())
      .then(restaurants => {
        allRestaurants = restaurants;
        // Wypełnij select miast
        const cities = [...new Set(restaurants.map(r => r.city))].sort();
        const select = document.getElementById('city-select');
        cities.forEach(city => {
          const opt = document.createElement('option');
          opt.value = city;
          opt.textContent = city;
          select.appendChild(opt);
        });
        // Inicjalizacja mapy
        map = L.map('map').setView([51.9194, 19.1451], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 18,
          attribution: '© OpenStreetMap, Leaflet'
        }).addTo(map);
        // Markery
        updateMarkers(restaurants);
        select.addEventListener('change', e => {
          const city = e.target.value;
          if(city) {
            const filtered = allRestaurants.filter(r => r.city === city);
            updateMarkers(filtered);
            showCards(filtered);
          } else {
            updateMarkers(allRestaurants);
            showCards([]);
          }
        });
      });
    function updateMarkers(restaurants) {
      if(markers.length) markers.forEach(m => map.removeLayer(m));
      markers = [];
      restaurants.forEach((r, i) => {
        let popupHtml = `<b>${r.restaurant_name}</b><br>${r.city}<br>${r.address ? r.address + '<br>' : ''}` +
          `${r.opening_hours ? 'Godziny: ' + r.opening_hours + '<br>' : ''}` +
          `${r.contact_email ? 'Email: ' + r.contact_email + '<br>' : ''}` +
          `${r.phone ? 'Tel: ' + r.phone + '<br>' : ''}` +
          (r.order_url ?
            `<a href="${r.order_url}" target="_blank"><button style='margin:6px 0 0 0;padding:8px 16px;background:#2d8f5a;color:#fff;border:none;border-radius:5px;cursor:pointer;'>Zamów</button></a>` :
            `<button style='margin:6px 0 0 0;padding:8px 16px;background:#aaa;color:#fff;border:none;border-radius:5px;cursor:not-allowed;opacity:0.7;' disabled title='skontaktuj się  z restaturacją telefonicznie'>Zamów</button>`
          ) +
          `<button onclick=\"alert('Rezerwacja stolika w przygotowaniu!')\" style='margin:6px 0 0 8px;padding:8px 16px;background:#226b41;color:#fff;border:none;border-radius:5px;cursor:pointer;'>Zarezerwuj stolik</button>`;
        let marker = L.marker([r.lat, r.lng]).addTo(map).bindPopup(popupHtml);
        markers.push(marker);
      });
    }
    function isLoggedIn() {
      // Prosta detekcja: jeśli na stronie głównej nie ma sesji, nie ma usera
      // Możesz to rozbudować np. o sprawdzanie ciasteczka lub tokena
      return false;
    }
    function showCards(restaurants) {
      const cont = document.getElementById('cards-container');
      if (!restaurants.length) { cont.innerHTML = ''; return; }
      cont.innerHTML = restaurants.map(r => `
        <div style="background:#fff;padding:20px 24px;border-radius:10px;box-shadow:0 2px 8px #eee;min-width:260px;max-width:320px;flex:1;">
          <h3 style='margin:0 0 8px 0;color:#2d8f5a;'>${r.restaurant_name}</h3>
          <div style='color:#444;font-size:1.1em;'>${r.city}${r.address ? ', ' + r.address : ''}</div>
          <div style='margin:8px 0;'>${r.opening_hours ? 'Godziny: ' + r.opening_hours + '<br>' : ''}
          ${r.contact_email ? 'Email: ' + r.contact_email + '<br>' : ''}
          ${r.phone ? 'Tel: ' + r.phone + '<br>' : ''}</div>
          ` +
          (r.order_url ?
            `<button onclick="handleOrderClick()" style='margin:6px 0 0 0;padding:8px 16px;background:#2d8f5a;color:#fff;border:none;border-radius:5px;cursor:pointer;'>Zamów</button>` :
            `<button style='margin:6px 0 0 0;padding:8px 16px;background:#aaa;color:#fff;border:none;border-radius:5px;cursor:not-allowed;opacity:0.7;' disabled title='skontaktuj się  z restaturacją telefonicznie'>Zamów</button>`
          ) +
          `<button onclick="handleReserveClick()" style='margin:6px 0 0 8px;padding:8px 16px;background:#226b41;color:#fff;border:none;border-radius:5px;cursor:pointer;'>Zarezerwuj stolik</button>` +
          `<button onclick="map.setView([${r.lat}, ${r.lng}], 16);" style='margin:6px 0 0 8px;padding:8px 16px;background:#ff9800;color:#fff;border:none;border-radius:5px;cursor:pointer;'>Pokaż na mapie</button>
        </div>
      `).join('');
    }
    function handleOrderClick() {
      alert('Aby zamówić jedzenie, musisz się zalogować lub zarejestrować!');
    }
    function handleReserveClick() {
      alert('Aby zarezerwować stolik, musisz się zalogować lub zarejestrować!');
    }
    </script>
    <footer style="text-align:center;margin:40px 0 0 0;color:#888;font-size:1rem;">&copy; DishPatch 2025</footer>
</body>
</html>
