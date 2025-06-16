<?php
session_start();
require_once 'config.php';

// reCAPTCHA config
$recaptcha_secret = '...'; 

// Obsługa logowania klasycznego
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // reCAPTCHA
    $recaptcha = $_POST['g-recaptcha-response'] ?? '';
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha");
    $responseKeys = json_decode($response, true);
    if (!$responseKeys['success']) {
        echo '<p>Błąd reCAPTCHA. Spróbuj ponownie.</p>';
        exit();
    }
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    // Sprawdź usera
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        if (!$user['is_active']) {
            echo '<p>Konto nieaktywne. Sprawdź e-mail i kliknij w link aktywacyjny.</p>';
            exit();
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = 'user';
        $_SESSION['session_start'] = time(); // Ustaw czas startu sesji przy logowaniu
        header('Location: user.php');
        exit();
    }
    // Sprawdź restaurację
    $stmt = $pdo->prepare('SELECT * FROM restaurants WHERE email = ?');
    $stmt->execute([$email]);
    $rest = $stmt->fetch();
    if ($rest && password_verify($password, $rest['password'])) {
        $_SESSION['user_id'] = $rest['id'];
        $_SESSION['role'] = 'restaurant';
        header('Location: restaurant.php');
        exit();
    }
    echo '<p>Błędny email lub hasło.</p>';
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>DishPatch - Logowanie</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f8f8; }
        .logo { display: flex; align-items: center; justify-content: center; margin-bottom: 24px; }
        .logo img { height: 96px; cursor: pointer; }
        .container { max-width: 400px; margin: 60px auto; background: #fff; padding: 32px; border-radius: 8px; box-shadow: 0 2px 12px #eee; }
        h2 { color: #2d8f5a; text-align: center; }
        input, button { width: 100%; padding: 12px; margin: 10px 0; border-radius: 5px; border: 1px solid #ccc; }
        button { background: #2d8f5a; color: #fff; border: none; cursor: pointer; }
        button:hover { background: #226b41; }
    </style>
</head>
<body>
<?php if (isset($_GET['msg'])): ?>
  <div id="popup-msg" style="position:fixed;top:32px;left:50%;transform:translateX(-50%);background:#2d8f5a;color:#fff;padding:18px 32px;border-radius:12px;z-index:9999;font-size:1.1em;box-shadow:0 2px 12px #0008;animation:fadeIn 0.5s;">
    <?= htmlspecialchars($_GET['msg']) ?>
  </div>
  <script>
    setTimeout(function(){
      var el = document.getElementById('popup-msg');
      if(el) el.style.display = 'none';
    }, 5000);
  </script>
  <style>@keyframes fadeIn{from{opacity:0;}to{opacity:1;}}</style>
<?php endif; ?>
    <div class="container">
        <a href="index.php" class="logo"><img src="images/logo.png" alt="logo"></a>
        <h2>Logowanie</h2>
        <form action="login.php" method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Hasło" required>
            <div class="g-recaptcha" data-sitekey="..."></div>
            <button type="submit">Zaloguj się</button>
        </form>
        <button onclick="history.back()" style="display:block;margin:32px auto 0 auto;padding:10px 28px;background:#ff5252;color:#fff;border:none;border-radius:6px;font-size:1rem;cursor:pointer;">Powrót</button>
    </div>
    <footer style="text-align:center;margin-top:40px;color:#888;font-size:1rem;">&copy; DishPatch 2025</footer>
</body>
</html>
