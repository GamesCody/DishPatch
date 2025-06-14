<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit();
}

// Pobierz rezerwacje użytkownika z restaurant_seats po emailu
$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : null;
if (!$user_email) {
    // Pobierz email z bazy na podstawie user_id
    $stmt = $pdo->prepare('SELECT email FROM users WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $row = $stmt->fetch();
    $user_email = $row ? $row['email'] : null;
    $_SESSION['email'] = $user_email;
}
$stmt = $pdo->prepare("
    SELECT s.*, l.restaurant_name
    FROM restaurant_seats s
    JOIN locations l ON s.restaurant_id = l.restaurant_id
    WHERE s.email = ?
    ORDER BY s.reservation_date DESC, s.time_slot DESC
");
$stmt->execute([$user_email]);
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
                    <td><?= date('d.m.Y H:i', strtotime($r['reservation_date'] . ' ' . $r['time_slot'])) ?></td>
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
