<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurant') {
    header('Location: login.php');
    exit();
}
require_once 'config.php';
$rest_id = $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT r.id, r.email, l.restaurant_name, l.city, l.address, l.opening_hours, l.contact_email, l.phone FROM restaurants r LEFT JOIN locations l ON r.id = l.restaurant_id WHERE r.id = ?');
$stmt->execute([$rest_id]);
$rows = $stmt->fetchAll();
if ($rows) {
    echo '<div style="max-width:700px;margin:40px auto 0;background:#fff;padding:24px;border-radius:10px;box-shadow:0 2px 12px #eee;">';
    echo '<h3>Twoje dane restauratora i restauracji</h3>';
    echo '<table style="width:100%;border-collapse:collapse;">';
    echo '<tr><th>ID</th><th>Email</th><th>Nazwa restauracji</th><th>Miasto</th><th>Adres</th><th>Godziny</th><th>Email kontaktowy</th><th>Telefon</th></tr>';
    foreach($rows as $row) {
        echo '<tr>';
        echo '<td>'.$row['id'].'</td>';
        echo '<td>'.$row['email'].'</td>';
        echo '<td>'.$row['restaurant_name'].'</td>';
        echo '<td>'.$row['city'].'</td>';
        echo '<td>'.$row['address'].'</td>';
        echo '<td>'.$row['opening_hours'].'</td>';
        echo '<td>'.$row['contact_email'].'</td>';
        echo '<td>'.$row['phone'].'</td>';
        echo '</tr>';
    }
    echo '</table></div>';
}
// Obsługa sesji 15 minutowej
$sessionTimeout = 900; // 15 minut w sekundach
if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
} elseif (time() - $_SESSION['last_activity'] > $sessionTimeout) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit();
}
if (isset($_POST['renew_session'])) {
    $_SESSION['last_activity'] = time();
}
$_SESSION['last_activity'] = time();
include 'restaurant.html';
?>
