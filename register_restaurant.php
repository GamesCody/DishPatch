<?php
// register_restaurant.php – obsługa rejestracji restauracji z reCAPTCHA
require_once 'config.php';
$recaptcha_secret = '6LdDEWArAAAAANjWOCPd4TkckLyzFETLeN-P_gIo';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $restaurant_name = $_POST['restaurant_name'] ?? '';
    $city = $_POST['city'] ?? '';
    $address = $_POST['address'] ?? '';
    $opening_hours = $_POST['opening_hours'] ?? '';
    $contact_email = $_POST['contact_email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $recaptcha = $_POST['g-recaptcha-response'] ?? '';
    if (!$restaurant_name || !$email || !$password || !$city) {
        echo 'Wszystkie wymagane pola muszą być wypełnione!';
        exit();
    }
    // Walidacja reCAPTCHA
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha");
    $responseKeys = json_decode($response, true);
    if (!$responseKeys['success']) {
        echo 'Błąd reCAPTCHA. Spróbuj ponownie.';
        exit();
    }
    // Sprawdź czy email już istnieje
    $stmt = $pdo->prepare('SELECT id FROM restaurants WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo 'Restauracja o tym adresie email już istnieje!';
        exit();
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO restaurants (restaurant_name, city, address, opening_hours, contact_email, phone, email, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    if ($stmt->execute([$restaurant_name, $city, $address, $opening_hours, $contact_email, $phone, $email, $hash])) {
        header('Location: login.php');
        exit();
    } else {
        echo 'Błąd rejestracji.';
    }
}
?>
<!-- Istniejący kod HTML (formularz) -->
<form action="register_restaurant.php" method="POST">
    <input type="text" name="restaurant_name" placeholder="Nazwa restauracji" required>
    <input type="text" name="city" placeholder="Miasto" required>
    <input type="text" name="address" placeholder="Adres" required>
    <input type="text" name="opening_hours" placeholder="Godziny otwarcia" required>
    <input type="text" name="contact_email" placeholder="Email kontaktowy" required>
    <input type="text" name="phone" placeholder="Telefon" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Hasło" required>
    <div class="g-recaptcha" data-sitekey="YOUR_SITE_KEY"></div>
    <button type="submit">Zarejestruj</button>
</form>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
