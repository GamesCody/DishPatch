<?php
require_once 'config.php';
header('Content-Type: application/json');

// Pobierz restauracje z lokalizacjami
$stmt = $pdo->query('SELECT l.id, l.restaurant_name, l.city, l.lat, l.lng, l.phone, l.contact_email, l.address, l.opening_hours, l.order_url FROM locations l');
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pobierz dania dla wszystkich restauracji
$ids = array_column($restaurants, 'id');
if (count($ids) > 0) {
    $in = str_repeat('?,', count($ids) - 1) . '?';
    $sql = "SELECT rd.restaurant_id, d.name FROM restaurant_dishes rd JOIN dishes d ON rd.dish_id = d.id WHERE rd.restaurant_id IN ($in)";
    $stmt2 = $pdo->prepare($sql);
    $stmt2->execute($ids);
    $dishes = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    // Grupuj dania po restauracji
    $dishesByRestaurant = [];
    foreach ($dishes as $row) {
        $dishesByRestaurant[$row['restaurant_id']][] = $row['name'];
    }
    // Dodaj dania do restauracji
    foreach ($restaurants as &$rest) {
        $rest['dishes'] = $dishesByRestaurant[$rest['id']] ?? [];
    }
}

// Zwróć dane
echo json_encode($restaurants);
