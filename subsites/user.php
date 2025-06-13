<?php
session_start();

// Jeśli użytkownik nie jest zalogowany, przekieruj go do strony logowania
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel użytkownika</title>
  <link rel="stylesheet" href="../css/style.css">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      background: linear-gradient(to bottom right, #00c9a7, #92fe9d);
      color: #003f3f;
    }

    header {
      padding: 20px;
      text-align: left;
    }

    h1 {
      margin: 0 20px;
    }

    .session-timer {
      font-size: 2rem;
      color: #278e97;
      margin: 40px auto 10px;
      text-align: center;
      font-weight: bold;
    }

    #timer {
      display: inline-block;
      min-width: 60px;
    }
    .menu {
      text-align: center;
      margin-top: 20px;
    }

    .menu ul {
      list-style: none;
      padding: 0;
    }

    .menu li {
      margin: 10px 0;
    }

    .menu a {
      color: #00796b;
      font-weight: bold;
      text-decoration: none;
      font-size: 1.1rem;
    }

    .menu a:hover {
      color: #004d40;
    }
  </style>
</head>
<body>

<header style="display: flex; align-items: center; justify-content: center; padding: 20px; position: relative;">
  <!-- Logo po lewej -->
  <img src="../assets/graphics/images/logo.png" alt="DishPatch Logo" style="height: 70px; position: absolute; left: 20px; top: 50%; transform: translateY(-50%);">
  <!-- Nagłówek na środku -->
  <h1 style="margin: 0; font-size: 2.5rem;">Witaj w panelu użytkownika!</h1>
</header>

  <main>
    <!-- Sekcja z licznikiem czasu sesji -->
    <div class="session-timer" id="sessionTimer">
      Pozostały czas sesji: <span id="timer">--:--</span>
    </div>

    <!-- Menu nawigacyjne -->
    <div class="menu">
      <a href="../index.php">Wyloguj się</a>
      <ul>
        <li><a href="#">Zamów/zarezerwuj ponownie</a></li>
        <li><a href="#">Moje zamówienia</a></li>
        <li><a href="#">Moje rezerwacje</a></li>
        <li><a href="settings.php">Ustawienia konta</a></li>
      </ul>
    </div>
  </main>

  <script>
    // ✅ TUTAJ MOŻESZ USTAWIĆ CZAS TRWANIA SESJI (w sekundach)
    // Przykład: 300 = 5 minut, 600 = 10 minut, 1200 = 20 minut
    const SESSION_TIME = 300;

    // Zmienna trzymająca aktualny stan czasu
    let sessionSeconds = SESSION_TIME;

    // Funkcja resetująca licznik – wywoływana przy aktywności użytkownika
    function resetSessionTimer() {
      sessionSeconds = SESSION_TIME;
    }

    // Reaguj na aktywność i resetuj licznik
    ['mousemove', 'keydown', 'mousedown', 'touchstart'].forEach(event => {
      document.addEventListener(event, resetSessionTimer);
    });

    // Funkcja odliczająca czas i aktualizująca widok
    function updateTimer() {
      const min = Math.floor(sessionSeconds / 60);
      const sec = sessionSeconds % 60;
      document.getElementById('timer').textContent =
        String(min).padStart(2, '0') + ':' + String(sec).padStart(2, '0');

      // Gdy licznik dojdzie do zera – informuj użytkownika i przekieruj
      if (sessionSeconds <= 0) {
        document.getElementById('sessionTimer').textContent = 'Sesja wygasła. Zaloguj się ponownie.';
        setTimeout(() => { window.location.href = '../index.php'; }, 2000);
        return;
      }

      sessionSeconds--;
    }

    // Uruchom licznik
    updateTimer();
    setInterval(updateTimer, 1000); // Odświeżaj co 1 sekundę
  </script>

</body>
</html>
