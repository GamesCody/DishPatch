<?php
header('Content-Type: application/json');

if (!isset($_GET['restaurant_id']) || !is_numeric($_GET['restaurant_id'])) {
    echo json_encode(['error' => 'Brak ID restauracji']);
    exit;
}

if (!isset($_GET['time_slot'])) {
    echo json_encode(['error' => 'Brak godziny rezerwacji']);
    exit;
}

$restaurant_id = intval($_GET['restaurant_id']);
$time_slot = $_GET['time_slot'];

if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $time_slot)) {
    echo json_encode(['error' => 'Nieprawidłowy format godziny']);
    exit;
}

try {
    $conn = new mysqli("localhost", "root", "", "dishpatch");

    if ($conn->connect_error) {
        throw new Exception("Błąd połączenia: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("
        SELECT seat_number, is_occupied, nazwisko, email
        FROM restaurant_seats 
        WHERE restaurant_id = ? AND time_slot = ?
        ORDER BY seat_number
    ");
    $stmt->bind_param("is", $restaurant_id, $time_slot);
    $stmt->execute();
    $result = $stmt->get_result();

    $seats = [];
    while ($row = $result->fetch_assoc()) {
        $seats[] = [
            'seat_number' => $row['seat_number'],
            'is_occupied' => (bool)$row['is_occupied'],
            'reserved_by' => $row['is_occupied'] ? [
                'nazwisko' => $row['nazwisko'],
                'email' => $row['email']
            ] : null
        ];
    }

    if (empty($seats)) {
        $check_stmt = $conn->prepare("SELECT id FROM restaurants WHERE id = ?");
        $check_stmt->bind_param("i", $restaurant_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows === 0) {
            echo json_encode(['error' => 'Restauracja nie istnieje']);
            exit;
        }

        for ($i = 1; $i <= 10; $i++) {
            $insert_stmt = $conn->prepare("
                INSERT INTO restaurant_seats (restaurant_id, seat_number, time_slot, is_occupied, nazwisko, email) 
                VALUES (?, ?, ?, 0, NULL, NULL)
            ");
            $insert_stmt->bind_param("iis", $restaurant_id, $i, $time_slot);
            $insert_stmt->execute();

            $seats[] = [
                'seat_number' => $i,
                'is_occupied' => false,
                'reserved_by' => null
            ];
        }
    }

    echo json_encode($seats);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
