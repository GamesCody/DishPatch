<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Ustawienia konta – DishPatch</title>
  <link rel="stylesheet" href="../css/style.css" />
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(to bottom right, #00c9a7, #92fe9d);
      margin: 0;
      padding: 40px;
      color: #003f3f;
    }

    h1 {
      text-align: center;
      margin-bottom: 30px;
    }

    form {
      max-width: 500px;
      margin: 0 auto;
      background-color: #ffffffcc;
      padding: 25px;
      border-radius: 10px;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }

    label {
      font-weight: bold;
      display: block;
      margin-top: 15px;
    }

    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 10px;
      border-radius: 6px;
      border: 1px solid #ccc;
      margin-top: 5px;
    }

    button {
      margin-top: 20px;
      padding: 12px 20px;
      background-color: #00b894;
      color: white;
      border: none;
      border-radius: 6px;
      font-weight: bold;
      cursor: pointer;
    }

    button:hover {
      background-color: #01967a;
    }

    .back-link {
      display: block;
      text-align: center;
      margin-top: 30px;
      color: #00796b;
      font-weight: bold;
    }

    .success-msg {
      background: #d4edda;
      color: #155724;
      padding: 12px 18px;
      border-radius: 8px;
      margin-bottom: 18px;
      text-align: center;
      font-weight: bold;
    }

    .error-msg {
      background: #f8d7da;
      color: #721c24;
      padding: 12px 18px;
      border-radius: 8px;
      margin-bottom: 18px;
      text-align: center;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <h1>Ustawienia konta</h1>

  <?php
  session_start();
  $success = isset($_SESSION['success_msg']) ? $_SESSION['success_msg'] : '';
  $error = isset($_SESSION['error_msg']) ? $_SESSION['error_msg'] : '';
  unset($_SESSION['success_msg'], $_SESSION['error_msg']);
  ?>

  <?php if ($success): ?>
    <div class="success-msg">
      <?= htmlspecialchars($success) ?>
    </div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="error-msg">
      <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <form method="POST" action="update_account.php">
    <label for="email">Zmień adres e-mail:</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Zmień hasło:</label>
    <input type="password" id="password" name="password" required>

    <label for="confirm">Potwierdź nowe hasło:</label>
    <input type="password" id="confirm" name="confirm" required>

    <button type="submit">Zapisz zmiany</button>
  </form>

  <a class="back-link" href="user.php">← Powrót do panelu</a>

</body>
</html>