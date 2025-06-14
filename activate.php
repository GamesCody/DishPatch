<?php
// activate.php – obsługa aktywacji konta użytkownika
require_once 'config.php';
$token = $_GET['token'] ?? '';
if (!$token) {
    header('Location: login.php?msg=Brak+tokena+aktywacyjnego.');
    exit();
}
$stmt = $pdo->prepare('SELECT id, is_active FROM users WHERE activation_token = ?');
$stmt->execute([$token]);
$user = $stmt->fetch();
if (!$user) {
    header('Location: login.php?msg=Nieprawidłowy+link+aktywacyjny.');
    exit();
}
if ($user['is_active']) {
    header('Location: login.php?msg=Konto+jest+już+aktywne.');
    exit();
}
$stmt = $pdo->prepare('UPDATE users SET is_active = 1, activation_token = NULL WHERE id = ?');
$stmt->execute([$user['id']]);
header('Location: login.php?msg=Konto+zostało+aktywowane!+Możesz+się+zalogować.');
exit();
?>
