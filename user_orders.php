<?php
session_start();
require_once 'config.php';

// Sprawdzenie logowania
$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit();
}

// Pobierz zamówienia użytkownika
$stmt = $pdo->prepare("
    SELECT o.*, l.restaurant_name 
    FROM orders o 
    JOIN locations l ON o.restaurant_id = l.restaurant_id 
    WHERE o.user_id = ? 
    ORDER BY o.created_at DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Moje zamówienia – DishPatch</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f8f8;
            padding: 40px;
            text-align: center;
        }
        .box {
            max-width: 900px;
            margin: auto;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 3px 14px #ddd;
        }
        h2 {
            color: #2d8f5a;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #eee;
        }
        .back {
            display: inline-block;
            margin-top: 24px;
            text-decoration: none;
            color: #278e97;
        }
    </style>
</head>
<body>
<div class="box">
    <h2>Moje zamówienia</h2>

    <?php if (empty($orders)): ?>
        <p>Brak złożonych zamówień.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Restauracja</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                        <td><?= htmlspecialchars($order['restaurant_name']) ?></td>
                        <td><?= ucfirst($order['status']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="user.php" class="back">← Powrót do panelu</a>
</div>
</body>
</html>
