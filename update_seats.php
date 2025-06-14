<?php
header('Content-Type: application/json');

// Pobierz dane z żądania
$data = json_decode(file_get_contents('php://input'), true);

if (
    !isset($data['restaurant_id']) || !is_numeric($data['restaurant_id']) ||
    !isset($data['selectedSeats']) || !is_array($data['selectedSeats']) ||
    !isset($data['time_slot']) ||
    !isset($data['nazwisko']) ||
    !isset($data['email'])
) {
    echo json_encode(['error' => 'Nieprawidłowe dane wejściowe.']);
    exit;
}

$restaurant_id = intval($data['restaurant_id']);
$selectedSeats = array_map('intval', $data['selectedSeats']);
$time_slot = $data['time_slot'];
$user_lastname = $data['nazwisko'];
$user_email = $data['email'];
$user_phone = isset($data['phone']) ? $data['phone'] : null;

if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $time_slot)) {
    echo json_encode(['error' => 'Nieprawidłowy format godziny']);
    exit;
}

try {
    $conn = new mysqli("localhost", "root", "", "foodapp");
    if ($conn->connect_error) {
        throw new Exception("Błąd połączenia z bazą danych: " . $conn->connect_error);
    }

    $placeholders = implode(',', array_fill(0, count($selectedSeats), '?'));
    $types = 'is' . str_repeat('i', count($selectedSeats));

    $stmt = $conn->prepare("
        SELECT seat_number 
        FROM restaurant_seats 
        WHERE restaurant_id = ? AND time_slot = ? AND seat_number IN ($placeholders) AND is_occupied = 1
    ");
    $params = array_merge([$restaurant_id, $time_slot], $selectedSeats);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $occupied = [];
        while ($row = $result->fetch_assoc()) {
            $occupied[] = $row['seat_number'];
        }
        echo json_encode(['error' => 'Niektóre miejsca są już zajęte: ' . implode(', ', $occupied)]);
        exit;
    }

    $updateStmt = $conn->prepare("
        UPDATE restaurant_seats 
        SET is_occupied = 1, nazwisko = ?, email = ?, phone = ?
        WHERE restaurant_id = ? AND time_slot = ? AND seat_number = ?
    ");

    foreach ($selectedSeats as $seat) {
        $updateStmt->bind_param("sssisi", $user_lastname, $user_email, $user_phone, $restaurant_id, $time_slot, $seat);
        $updateStmt->execute();
    }

    $display_time = substr($time_slot, 0, 5);

    echo json_encode([
        'success' => true,
        'message' => count($selectedSeats) . ' miejsce(a) zostało pomyślnie zarezerwowane na godzinę ' . $display_time . ' dla ' . $user_lastname . '.',
        'user_info' => [
            'lastname' => $user_lastname,
            'email' => $user_email
        ]
    ]);

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} finally {
    if (isset($conn)) $conn->close();
}
?>
