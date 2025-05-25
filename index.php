<?php
include 'includes/connect.php';
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <link rel="icon" href="assets/graphics/images/favicon.png" sizes="48x48" type="image/png" />
  <title>Dish Patch</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <link rel="stylesheet" href="css/style.css"/>
</head>

<body>
    <header>
      <img id="logo" src="assets/graphics/images/logo.png" alt="logo"> 
      <div class="login">
        <div class="licon" onclick="toggleLoginMenu()">
          <img id="loginIcon" src="assets/graphics/icons/black/acc_ico_bl.png" alt="login">
        </div>
        <div class="tooltip">Moje konto</div>
        <div class="form" id="logreg">
          <button id="login" class="button">Zaloguj się</button>
          <button id="register" class="button">Załóż konto</button>
        </div>
      </div>

      <div id="log" class="form">
        <form id="logowanie">
          <label class="label" for="login/email">Podaj adres e-mail:</label><br>
          <input class="input" type="text" id="email" name="email"><br>
          <label class="label" for="password">Hasło:</label><br>
          <input class="input" type="password" id="password" name="password">
          <input class="button" type="submit" value="Zaloguj">
        </form>
      </div>
      <div id="reg" class="form">
        <form id="rejestracja">
          <label class="label" for="name">Podaj Imię</label><br>
          <input class="input" type="text" id="name" name="name"><br>
          <label class="label" for="surname">Podaj Nazwisko</label><br>
          <input class="input" type="text" id="surname" name="surname"><br>
          <label class="label" for="login/email">Podaj adres e-mail:</label><br>
          <input class="input" type="text" id="email" name="email"><br>
          <label class="label" for="password">Hasło:</label><br>
          <input class="input" type="password" id="password" name="password">
          <label class="label" for="rep_password">Powtórz hasło:</label><br>
          <input class="input" type="password" id="rep_password" name="rep_password">
          <input class="button" type="submit" value="Zaloguj">
        </form>
      </div>
    </header>
  <div id="controls">
    <h2>Wybierz miasto</h2>
    <select id="citySelect">
      <option value="">-- Wybierz miasto --</option>
      <option value="warszawa">Warszawa</option>
      <option value="krakow">Kraków</option>
      <option value="czestochowa">Częstochowa</option>
    </select>

    <div id="restaurantList"></div>
  </div>
  <footer>
  <div>&copy; DishPatch 2025</div>
  </footer>

  <div id="map"></div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="js/script.js"></script>
  

</body>
</html>
