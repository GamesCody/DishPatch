<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Walidacja danych wejściowych
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirm = isset($_POST['confirm']) ? $_POST['confirm'] : '';

$errors = [];

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Nieprawidłowy adres e-mail.';
}
if (strlen($password) < 6) {
    $errors[] = 'Hasło musi mieć co najmniej 6 znaków.';
}
if ($password !== $confirm) {
    $errors[] = 'Hasła nie są zgodne.';
}

if (empty($errors)) {
    // Sprawdź, czy e-mail nie jest już zajęty przez innego użytkownika
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
    $stmt->execute([$email, $user_id]);
    if ($stmt->fetch()) {
        $errors[] = 'Podany e-mail jest już zajęty.';
    } else {
        // Aktualizuj dane użytkownika
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('UPDATE users SET email = ?, password = ? WHERE id = ?');
        $stmt->execute([$email, $hash, $user_id]);
        $_SESSION['success_msg'] = 'Dane konta zostały zaktualizowane.';
        // NIE resetuj czasu sesji po zmianie danych!
        // header('Location: settings.php');
        // exit();
    }
}

// Jeśli są błędy, przekieruj z powrotem z komunikatem
$_SESSION['error_msg'] = implode(' ', $errors);
header('Location: settings.php');
exit();
