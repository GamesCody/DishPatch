<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit();
}

// Pobierz rezerwacje użytkownika
$stmt = $pdo->prepare("
    SELECT r.*, res.name AS restaurant_name
    FROM reservations r
    JOIN restaurants res ON r.restaurant_id = res.id
    WHERE r.user_id = ?
    ORDER BY r.reservation_time DESC
");
$stmt->execute([$user_id]);
$reservations = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Moje rezerwacje – DishPatch</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f8f8;
            padding: 40px;
            text-align: center;
        }
        .box {
            background: #fff;
            max-width: 800px;
            margin: 0 auto;
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 3px 14px #ddd;
        }
        h2 {
            color: #2d8f5a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 24px;
        }
        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid #ccc;
        }
        th {
            background: #eee;
        }
        .back {
            display: block;
            margin-top: 30px;
            color: #278e97;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="box">
    <h2>Moje rezerwacje</h2>

    <?php if (count($reservations) === 0): ?>
        <p>Nie masz jeszcze żadnych rezerwacji.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Data</th>
                <th>Gości</th>
                <th>Restauracja</th>
            </tr>
            <?php foreach ($reservations as $r): ?>
                <tr>
                    <td><?= $r['id'] ?></td>
                    <td><?= date('d.m.Y H:i', strtotime($r['date_time'])) ?></td>
                    <td><?= $r['guests'] ?></td>
                    <td><?= htmlspecialchars($r['restaurant_name']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <a href="user.php" class="back">← Powrót do panelu</a>
</div>
</body>
</html>
