<?php
session_start();
require_once 'config.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit();
}

$type = $_GET['type'] ?? 'order';
if ($type !== 'order') {
    header("Location: user.php");
    exit();
}

// Obsługa POST – powtórzenie konkretnego zamówienia
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'])) {
    $order_id = (int)$_POST['order_id'];

    // Pobierz zamówienie użytkownika
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
    $stmt->execute([$order_id, $user_id]);
    $originalOrder = $stmt->fetch();

    if ($originalOrder) {
        // Utwórz nowe zamówienie
        $insert = $pdo->prepare("INSERT INTO orders (user_id, restaurant_id, order_time, status, created_at)
                                 VALUES (?, ?, NOW(), 'pending', NOW())");
        $insert->execute([$user_id, $originalOrder['restaurant_id']]);
        $newOrderId = $pdo->lastInsertId();

        // Skopiuj pozycje
        $items = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
        $items->execute([$order_id]);

        $insertItem = $pdo->prepare("INSERT INTO order_items (order_id, dish_name, quantity, price)
                                     VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $insertItem->execute([
                $newOrderId,
                $item['dish_name'],
                $item['quantity'],
                $item['price']
            ]);
        }

        header("Location: orders_summary.php?order_id={$newOrderId}&repeated=1");
        exit();
    }

    header("Location: repeat_last.php?type=order&error=not_found");
    exit();
}

// Pobierz wszystkie zamówienia danego użytkownika
$orders = $pdo->prepare("
    SELECT o.*, r.name AS restaurant_name
    FROM orders o
    JOIN restaurants r ON o.restaurant_id = r.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$orders->execute([$user_id]);
$orders = $orders->fetchAll();
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Powtórz zamówienie – DishPatch</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f8f8f8;
            margin: 0;
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
            margin-top: 0;
            color: #2d8f5a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid #ccc;
        }
        th {
            background: #eee;
        }
        form {
            margin: 0;
        }
        button {
            padding: 8px 16px;
            background: #2d8f5a;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background: #226b41;
        }
        .back {
            display: block;
            margin-top: 32px;
            color: #777;
            text-decoration: none;
        }
        ul {
            margin: 8px 0 0;
            padding: 0 0 0 18px;
            text-align: left;
        }
        ul li {
            margin-bottom: 4px;
        }
        .total {
            font-weight: bold;
            text-align: right;
            margin-top: 4px;
        }
    </style>
</head>
<body>
<div class="box">
    <h2>Powtórz zamówienie</h2>

    <?php if (!$orders): ?>
        <p>Nie masz jeszcze żadnych zamówień.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Restauracja</th>
                    <th>Akcja</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= $order['id'] ?></td>
                        <td><?= date('d.m.Y H:i', strtotime($order['created_at'])) ?></td>
                        <td><?= htmlspecialchars($order['restaurant_name']) ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                <button type="submit">Zamów ponownie</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="background:#f9f9f9;">
                            <?php
                            $itemsStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
                            $itemsStmt->execute([$order['id']]);
                            $items = $itemsStmt->fetchAll();
                            $total = 0;
                            ?>
                            <?php if ($items): ?>
                                <ul>
                                    <?php foreach ($items as $item):
                                        $subtotal = $item['price'] * $item['quantity'];
                                        $total += $subtotal;
                                    ?>
                                        <li>
                                            🍽️ <?= htmlspecialchars($item['dish_name']) ?> —
                                            <?= (int)$item['quantity'] ?> × <?= number_format($item['price'], 2) ?> zł
                                            = <?= number_format($subtotal, 2) ?> zł
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <div class="total">Razem: <?= number_format($total, 2) ?> zł</div>
                            <?php else: ?>
                                <em>Brak pozycji w zamówieniu.</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="user.php" class="back">← Powrót do panelu</a>
</div>
</body>
</html>
