<?php
session_start();
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
    .session-timer {
      font-size: 2rem;
      color: #278e97;
      margin: 40px auto;
      text-align: center;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <header>
    <h1>Witaj w panelu użytkownika!</h1>
  </header>
  <main>
    <div class="session-timer" id="sessionTimer">
      Pozostały czas sesji: <span id="timer">--:--</span>
    </div>
    <div style="text-align:center; margin-top:30px;">
      <a href="../index.php">Wyloguj się</a>
    </div>
  </main>
  <script>
    const SESSION_TIME = 300;
    let sessionSeconds = SESSION_TIME;
    function resetSessionTimer() {
      sessionSeconds = SESSION_TIME;
    }
    ['mousemove', 'keydown', 'mousedown', 'touchstart'].forEach(event => {
      document.addEventListener(event, resetSessionTimer);
    });
    function updateTimer() {
      const min = Math.floor(sessionSeconds / 60);
      const sec = sessionSeconds % 60;
      document.getElementById('timer').textContent =
        String(min).padStart(2, '0') + ':' + String(sec).padStart(2, '0');
      if (sessionSeconds <= 0) {
        document.getElementById('sessionTimer').textContent = 'Sesja wygasła. Zaloguj się ponownie.';
        setTimeout(() => { window.location.href = '../index.php'; }, 2000);
        return;
      }
      sessionSeconds--;
    }
    updateTimer();
    setInterval(updateTimer, 1000);
  </script>
</body>
</html>
