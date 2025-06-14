<?php
session_start();
require_once 'config.php';

// Sprawdź, czy użytkownik jest zalogowany
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit();
}

// Sprawdź, czy podano ID zamówienia
$orderId = $_GET['order_id'] ?? null;
if (!$orderId) {
    echo "Brak ID zamówienia.";
    exit();
}

// Pobierz zamówienie
$orderStmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$orderStmt->execute([$orderId, $user_id]);
$order = $orderStmt->fetch();

if (!$order) {
    echo "Nie znaleziono zamówienia.";
    exit();
}

// Pobierz pozycje zamówienia
$itemsStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemsStmt->execute([$orderId]);
$items = $itemsStmt->fetchAll();

// Oblicz łączną kwotę
$total = 0;
foreach ($items as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Podsumowanie zamówienia – DishPatch</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f8f8f8; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .box { background: #fff; padding: 40px 60px; border-radius: 12px; box-shadow: 0 3px 14px #ddd; text-align: center; width: 480px; }
        h2 { color: #2d8f5a; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 8px 12px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f0f0f0; }
        .total { font-weight: bold; font-size: 1.1em; margin-top: 20px; }
        a { display: inline-block; margin-top: 30px; text-decoration: none; color: #278e97; }
    </style>
</head>
<body>
<div class="box">
    <h2>Twoje zamówienie zostało powtórzone!</h2>

    <div><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></div>
    <div><strong>Data:</strong> <?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></div>

    <table>
        <thead>
        <tr>
            <th>Danie</th>
            <th>Ilość</th>
            <th>Cena</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($items as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['dish_name']) ?></td>
                <td><?= (int)$item['quantity'] ?></td>
                <td><?= number_format($item['price'], 2, ',', ' ') ?> zł</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total">Razem: <?= number_format($total, 2, ',', ' ') ?> zł</div>

    <a href="user.php">← Powrót do panelu</a>
</div>
</body>
</html>
