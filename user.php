<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit();
}
require_once 'config.php';
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT username, email FROM users WHERE id = ?');
$stmt->execute([$user_id]);
$u = $stmt->fetch();

if (!isset($_SESSION['login_time'])) {
    $_SESSION['login_time'] = time();
}
$login_time = $_SESSION['login_time'];
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>DishPatch - Panel użytkownika</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
<style>
    body {
        font-family: Arial, sans-serif;
        background: #f8f8f8;
        margin: 0;
        padding: 0;
    }

    .logo {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 24px;
    }

    .logo img {
        height: 120px; /* powiększono z 96px */
        cursor: pointer;
    }

    .container {
        max-width: 640px;              /* delikatnie zwiększona szerokość */
        margin: 40px auto;             /* mniejsze marginesy */
        background: #ffffff;
        padding: 40px 32px;            /* więcej przestrzeni wewnątrz */
        border-radius: 12px;           /* delikatniejsze zaokrąglenie */
        box-shadow: 0 4px 16px rgba(0,0,0,0.05); /* bardziej miękki cień */
        text-align: center;            /* wyrównanie całej zawartości do środka */
    }

    h2 {
        color: #2d8f5a;
        margin-top: 0;
        margin-bottom: 16px;
        font-size: 1.8rem;
    }

    .logout-btn {
        margin-top: 32px;
        background: #e74c3c;
        color: #fff;
        border: none;
        padding: 12px 24px;
        border-radius: 6px;
        font-size: 1rem;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .logout-btn:hover {
        background: #c0392b;
    }

    @media (max-width: 640px) {
        .container {
            margin: 20px;
            padding: 24px;
        }

        .logo img {
            height: 120px;
        }

        h2 {
            font-size: 1.5rem;
        }
    }
</style>
</head>
<body>
    <div class="container">
        <a href="index.php" class="logo"><img src="images/logo.png" alt="logo"></a>
        <h2>Witaj w panelu użytkownika!</h2>
        <p>Tu możesz zamawiać jedzenie i rezerwować stoliki.</p>
        <table style="width:100%;border-collapse:collapse;margin:24px 0;">
    <tr style="background:#f0f0f0;">
      <th style="padding:12px;">Imię</th>
      <th style="padding:12px;">Email</th>
    </tr>
    <tr>
      <td style="padding:12px;text-align:center;">
        <?php if ($u && !empty($u['username'])) echo htmlspecialchars($u['username']); else echo '<span style="color:#aaa;">Brak danych</span>'; ?>
      </td>
      <td style="padding:12px;text-align:center;">
        <?php if ($u && !empty($u['email'])) echo htmlspecialchars($u['email']); else echo '<span style="color:#aaa;">Brak danych</span>'; ?>
      </td>
    </tr>
  </table>
        <nav style="max-width:600px;margin:30px auto 0 auto;text-align:center;">
            <ul style="list-style:none;padding:0;margin:0;display:flex;flex-direction:column;gap:12px;">
                <li>
                  <a href="repeat_last.php?type=order"
                    style="display:inline-block;padding:12px 24px;background:#2d8f5a;
                    color:#fff;border-radius:6px;text-decoration:none;font-weight:bold;">
                    Zamów ponownie
                  </a>
                </li>
                <li><a href="user_orders.php" style="display:inline-block;padding:12px 24px;background:#278e97;color:#fff;border-radius:6px;text-decoration:none;font-weight:bold;">Moje zamówienia</a></li>
                <li><a href="user_reservations.php" style="display:inline-block;padding:12px 24px;background:#00b894;color:#fff;border-radius:6px;text-decoration:none;font-weight:bold;">Moje rezerwacje</a></li>
                <li><a href="settings.php" style="display:inline-block;padding:12px 24px;background:#ff9800;color:#fff;border-radius:6px;text-decoration:none;font-weight:bold;">Ustawienia konta</a></li>
            </ul>
        </nav>
        <form action="logout.php" method="post">
            <button type="submit" class="logout-btn">Wyloguj się</button>
        </form>
    </div>
    <!-- Pole wyszukiwania posiłków z przyciskiem szukaj dla usera nad mapą -->
    <div style="max-width:900px;margin:24px auto 0 auto;display:flex;justify-content:center;gap:12px;">
        <input id="meal-search-user" type="text" placeholder="Wyszukaj posiłek..." style="width:100%;max-width:400px;padding:12px 18px;font-size:1.1em;border-radius:8px;border:1px solid #ccc;box-shadow:0 2px 8px #eee;outline:none;">
        <button id="meal-search-btn-user" style="padding:12px 24px;font-size:1.1em;border-radius:8px;background:#2d8f5a;color:#fff;border:none;cursor:pointer;">Szukaj</button>
    </div>
    <!-- KOPIA LANDING PAGE: MAPA I WYBÓR RESTAURACJI -->
    <div style="max-width:900px;margin:30px auto 0;">
        <label for="city-select-user" style="font-size:1.1em;font-weight:bold;">Wybierz miasto:</label>
        <select id="city-select-user" style="margin-left:12px;padding:8px 16px;border-radius:6px;">
            <option value="">-- wybierz --</option>
        </select>
    </div>
    <div id="cards-container-user" style="max-width:900px;margin:20px auto 0;display:flex;flex-wrap:wrap;gap:20px;"></div>
    <div id="map-user" style="height: 500px; width: 100%; margin: 40px auto 0; max-width: 900px; border-radius: 12px; box-shadow: 0 2px 12px #eee;"></div>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
    let allRestaurantsUser = [];
    let mapUser, markersUser = [];
    fetch('restaurants.php')
      .then(res => res.json())
      .then(restaurants => {
        allRestaurantsUser = restaurants;
        // Wypełnij select miast
        const cities = [...new Set(restaurants.map(r => r.city))].sort();
        const select = document.getElementById('city-select-user');
        cities.forEach(city => {
          const opt = document.createElement('option');
          opt.value = city;
          opt.textContent = city;
          select.appendChild(opt);
        });
        // Inicjalizacja mapy
        mapUser = L.map('map-user').setView([51.9194, 19.1451], 6);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 18,
          attribution: '© OpenStreetMap, Leaflet'
        }).addTo(mapUser);
        // Markery
        updateMarkersUser(restaurants);
        select.addEventListener('change', e => {
          const city = e.target.value;
          if(city) {
            const filtered = allRestaurantsUser.filter(r => r.city === city);
            updateMarkersUser(filtered);
            showCardsUser(filtered);
          } else {
            updateMarkersUser(allRestaurantsUser);
            showCardsUser([]);
          }
        });
      });
    function updateMarkersUser(restaurants) {
      if(markersUser.length) markersUser.forEach(m => mapUser.removeLayer(m));
      markersUser = [];
      restaurants.forEach((r, i) => {
        let popupHtml = `<b>${r.restaurant_name}</b><br>${r.city}<br>${r.address ? r.address + '<br>' : ''}` +
          `${r.opening_hours ? 'Godziny: ' + r.opening_hours + '<br>' : ''}` +
          `${r.contact_email ? 'Email: ' + r.contact_email + '<br>' : ''}` +
          `${r.phone ? 'Tel: ' + r.phone + '<br>' : ''}` +
          (r.order_url ?
            `<a href="${r.order_url}" target="_blank"><button style='margin:6px 0 0 0;padding:8px 16px;background:#2d8f5a;color:#fff;border:none;border-radius:5px;cursor:pointer;'>Zamów</button></a>` :
            `<button style='margin:6px 0 0 0;padding:8px 16px;background:#aaa;color:#fff;border:none;border-radius:5px;cursor:not-allowed;opacity:0.7;' disabled title='skontaktuj się  z restaturacją telefonicznie'>Zamów</button>`
          ) +
          `<button onclick="handleReserveClickUser()" style='margin:6px 0 0 8px;padding:8px 16px;background:#226b41;color:#fff;border:none;border-radius:5px;cursor:pointer;'>Zarezerwuj stolik</button>` +
          `<button onclick="mapUser.setView([${r.lat}, ${r.lng}], 16);" style='margin:6px 0 0 8px;padding:8px 16px;background:#ff9800;color:#fff;border:none;border-radius:5px;cursor:pointer;'>Pokaż na mapie</button>`;
        let marker = L.marker([r.lat, r.lng]).addTo(mapUser).bindPopup(popupHtml);
        markersUser.push(marker);
      });
    }
    function showCardsUser(restaurants) {
      const cont = document.getElementById('cards-container-user');
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
            `<a href="${r.order_url}" target="_blank"><button style='margin:6px 0 0 0;padding:8px 16px;background:#2d8f5a;color:#fff;border:none;border-radius:5px;cursor:pointer;'>Zamów</button></a>` :
            `<button style='margin:6px 0 0 0;padding:8px 16px;background:#aaa;color:#fff;border:none;border-radius:5px;cursor:not-allowed;opacity:0.7;' disabled title='skontaktuj się  z restaturacją telefonicznie'>Zamów</button>`
          ) +
          `<button onclick="handleReserveClickUser()" style='margin:6px 0 0 8px;padding:8px 16px;background:#226b41;color:#fff;border:none;border-radius:5px;cursor:pointer;'>Zarezerwuj stolik</button>` +
          `<button onclick="mapUser.setView([${r.lat}, ${r.lng}], 16);" style='margin:6px 0 0 8px;padding:8px 16px;background:#ff9800;color:#fff;border:none;border-radius:5px;cursor:pointer;'>Pokaż na mapie</button>` +
        '</div>'
      ).join('');
    }
    // Dodaj funkcję przekierowującą:
    function handleReserveClickUser() {
      // Pobierz aktualnie wybraną restaurację z selecta
      const selectedCity = document.getElementById('city-select-user').value;
      if (!selectedCity) {
        alert('Najpierw wybierz miasto z listy!');
        return;
      }
      
      // Znajdź pierwszą restaurację z wybranego miasta
      const restaurant = allRestaurantsUser.find(r => r.city === selectedCity);
      if (restaurant) {
        window.location.href = `rezerwacja.html?restaurant_id=${restaurant.id}`;
      } else {
        window.location.href = 'rezerwacja.html';
      }
    }
    </script>
    <!-- ZEGAR SESJI -->
    <div id="session-timer" style="position:fixed;top:18px;right:32px;background:#2d8f5a;color:#fff;padding:8px 18px;border-radius:8px;font-size:1.1em;z-index:999;box-shadow:0 2px 8px #aaa;letter-spacing:1px;">Czas sesji: <span id="timer-value">00:00</span></div>
    <div id="extend-session-popup" style="display:none;position:fixed;top:80px;right:24px;z-index:10000;background:#fffbe6;border:2px solid #ff9800;padding:22px 32px;border-radius:12px;box-shadow:0 2px 12px #ff9800b0;font-size:1.1em;color:#856404;text-align:center;">
      <span>Twoja sesja wygaśnie za mniej niż 5 minut.<br>Przedłużyć sesję?</span><br>
      <button id="extend-session-btn" style="margin-top:16px;padding:10px 24px;background:#00b894;color:#fff;border:none;border-radius:6px;font-weight:bold;cursor:pointer;">Przedłuż sesję</button>
    </div>
    <script>
    // ZEGAR SESJI
    (function() {
      let sessionStart = <?php echo isset($_SESSION['session_start']) ? $_SESSION['session_start'] : (isset($_SESSION['login_time']) ? $_SESSION['login_time'] : 'null'); ?>;
      let maxSession = 900; // sekund
      let extendPopupShown = false;
      function updateTimer() {
        if (!sessionStart) return;
        let now = Math.floor(Date.now() / 1000);
        let elapsed = now - sessionStart;
        let left = maxSession - elapsed;
        if (left <= 0) {
          document.getElementById('timer-value').textContent = '00:00';
          window.location.href = 'logout.php?timeout=1';
          return;
        }
        let min = Math.floor(left / 60);
        let sec = left % 60;
        document.getElementById('timer-value').textContent = (min<10?'0':'')+min+":"+(sec<10?'0':'')+sec;
        // Pokaż popup przy <= 5 minutach
        if (left <= 300 && !extendPopupShown) {
          document.getElementById('extend-session-popup').style.display = 'block';
          extendPopupShown = true;
        }
        if (left > 300 && extendPopupShown) {
          document.getElementById('extend-session-popup').style.display = 'none';
          extendPopupShown = false;
        }
      }
      setInterval(updateTimer, 1000);
      updateTimer();
      // Obsługa przycisku przedłużenia sesji
      document.getElementById('extend-session-btn').onclick = function() {
        fetch('extend_session.php', {method:'POST', credentials:'same-origin'})
          .then(r => r.json())
          .then(data => {
            if(data.success) {
              sessionStart = Math.floor(Date.now() / 1000);
              extendPopupShown = false;
              document.getElementById('extend-session-popup').style.display = 'none';
            }
          });
      };
    })();
    </script>
    <script>
      const loginTime = <?= json_encode($login_time) ?>;
      function pad(n) { return n < 10 ? '0' + n : n; }
      function updateTimer() {
        const now = Math.floor(Date.now() / 1000);
        let diff = now - loginTime;
        const min = Math.floor(diff / 60);
        const sec = diff % 60;
        document.getElementById('timer').textContent = pad(min) + ':' + pad(sec);
      }
      updateTimer();
      setInterval(updateTimer, 1000);
    </script>
    <script>
    // Dodaj obsługę wyszukiwania posiłku dla usera
    function filterByMealUser() {
      const meal = document.getElementById('meal-search-user').value.trim().toLowerCase();
      const city = document.getElementById('city-select-user').value;
      let filtered = allRestaurantsUser;
      if (city) filtered = filtered.filter(r => r.city === city);
      if (meal) {
        filtered = filtered.filter(r => (r.dishes||[]).some(d => d.toLowerCase().includes(meal)));
      }
      showCardsUser(filtered);
      document.getElementById('map-user').style.display = '';
    }
    document.getElementById('meal-search-btn-user').addEventListener('click', filterByMealUser);
    document.getElementById('meal-search-user').addEventListener('keydown', function(e) {
      if (e.key === 'Enter') filterByMealUser();
    });
    </script>
</body>
</html>

