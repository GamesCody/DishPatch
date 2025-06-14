<?php
session_start();
require_once 'config.php';

// Sprawdzenie logowania
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit();
}

// ID rezerwacji z GET
$reservationId = $_GET['id'] ?? null;
if (!$reservationId) {
    echo "Brak ID rezerwacji.";
    exit();
}

// Pobierz dane rezerwacji
$stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ? AND user_id = ?");
$stmt->execute([$reservationId, $user_id]);
$reservation = $stmt->fetch();

if (!$reservation) {
    echo "Nie znaleziono rezerwacji.";
    exit();
}

// Pobierz nazwę restauracji (jeśli potrzebujesz)
$resName = '';
if ($reservation['restaurant_id']) {
    $resStmt = $pdo->prepare("SELECT name FROM restaurants WHERE id = ?");
    $resStmt->execute([$reservation['restaurant_id']]);
    $resData = $resStmt->fetch();
    $resName = $resData['name'] ?? '';
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Potwierdzenie rezerwacji – DishPatch</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f8f8; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .box { background: #fff; padding: 40px 60px; border-radius: 12px; box-shadow: 0 3px 14px #ddd; text-align: center; width: 480px; }
        h2 { color: #2d8f5a; margin-bottom: 24px; }
        .info { margin: 12px 0; font-size: 1.1em; }
        .label { font-weight: bold; margin-right: 6px; }
        a { display: inline-block; margin-top: 30px; text-decoration: none; color: #278e97; }
    </style>
</head>
<body>
<div class="box">
    <h2>Rezerwacja została powtórzona!</h2>

    <div class="info"><span class="label">Restauracja:</span><?= htmlspecialchars($resName) ?></div>
    <div class="info"><span class="label">Data i godzina:</span><?= date('d.m.Y H:i', strtotime($reservation['date_time'])) ?></div>
    <div class="info"><span class="label">Liczba gości:</span><?= (int)$reservation['guests'] ?></div>
    <div class="info"><span class="label">Dodano:</span><?= date('d.m.Y H:i', strtotime($reservation['created_at'])) ?></div>

    <a href="user.php">← Powrót do panelu</a>
</div>
</body>
