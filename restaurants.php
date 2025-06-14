<?php
require_once 'config.php';
header('Content-Type: application/json');
$stmt = $pdo->query('SELECT l.id, l.restaurant_name, l.city, l.lat, l.lng, l.phone, l.contact_email, l.address, l.opening_hours, l.order_url FROM locations l');
$restaurants = $stmt->fetchAll();
echo json_encode($restaurants);
