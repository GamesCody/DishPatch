<?php
// register_user.php – obsługa rejestracji użytkownika z reCAPTCHA
require_once 'config.php';
$recaptcha_secret = '6LdDEWArAAAAANjWOCPd4TkckLyzFETLeN-P_gIo';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $recaptcha = $_POST['g-recaptcha-response'] ?? '';
    // Walidacja pól
    if (!$username || !$email || !$password) {
        echo 'Wszystkie pola są wymagane!';
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
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo 'Użytkownik o tym adresie email już istnieje!';
        exit();
    }
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $token = bin2hex(random_bytes(32));
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password, is_active, activation_token) VALUES (?, ?, ?, 0, ?)');
    if ($stmt->execute([$username, $email, $hash, $token])) {
        // Wyślij maila z linkiem aktywacyjnym
        $activation_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/activate.php?token=$token";
        $subject = "Aktywacja konta DishPatch";
        $message = "Cześć $username!\n\nAby aktywować konto, kliknij w poniższy link:\n$activation_link\n\nJeśli to nie Ty zakładałeś konto, zignoruj tę wiadomość.";
        $headers = "From: noreply@dishpatch.local";
        mail($email, $subject, $message, $headers);
        header('Location: login.php?msg=Rejestracja+zakończona!+Sprawdź+e-mail+i+aktywuj+konto.');
        exit();
    } else {
        echo 'Błąd rejestracji.';
    }
}
?>

