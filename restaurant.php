<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'restaurant') {
    header('Location: login.php');
    exit();
}
require_once 'config.php';
$rest_id = $_SESSION['user_id'];

// Pobierz dane restauracji
$stmt = $pdo->prepare('SELECT r.id, r.email, l.restaurant_name, l.city, l.address, l.opening_hours, l.contact_email, l.phone FROM restaurants r LEFT JOIN locations l ON r.id = l.restaurant_id WHERE r.id = ?');
$stmt->execute([$rest_id]);
$restaurant_data = $stmt->fetch();

// Obsługa usuwania rezerwacji
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_reservation_id'])) {
    $del_id = intval($_POST['delete_reservation_id']);
    $del_stmt = $pdo->prepare('UPDATE restaurant_seats SET is_occupied = 0, nazwisko = NULL, email = NULL, phone = NULL WHERE id = ? AND restaurant_id = ?');
    $del_stmt->execute([$del_id, $rest_id]);
    header("Location: restaurant.php");
    exit();
}

// Pobierz rezerwacje
$stmt2 = $pdo->prepare('SELECT id, time_slot, seat_number, nazwisko, email, phone FROM restaurant_seats WHERE restaurant_id = ? AND is_occupied = 1 ORDER BY time_slot, seat_number');
$stmt2->execute([$rest_id]);
$reservations = $stmt2->fetchAll();

// Obsługa sesji 15 minutowej
$sessionTimeout = 900;
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
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Restauracji</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        h1, h2, h3 {
            color: #333;
            margin-bottom: 20px;
        }

        h1 {
            text-align: center;
            color: white;
            margin-bottom: 30px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .restaurant-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .info-item {
            padding: 10px;
            background: white;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .info-label {
            font-weight: bold;
            color: #666;
            font-size: 12px;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .info-value {
            color: #333;
            font-size: 14px;
        }

        /* Tabela rezerwacji */
        .reservations-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .reservations-table th,
        .reservations-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .reservations-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }

        .reservations-table tr:hover {
            background-color: #f5f5f5;
        }

        .delete-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            transition: background 0.3s ease;
        }

        .delete-btn:hover {
            background: #c82333;
        }

        /* Nowa sekcja rezerwacji wizualnej */
        .visual-reservation {
            margin-top: 30px;
        }

        .time-selector {
            margin: 30px 0;
            padding: 20px;
            background: #f1f3f4;
            border-radius: 10px;
        }

        .time-slider-container {
            position: relative;
            margin: 20px 0;
        }

        .time-slider {
            width: 100%;
            height: 8px;
            border-radius: 5px;
            background: #ddd;
            outline: none;
            -webkit-appearance: none;
        }

        .time-slider::-webkit-slider-thumb {
            appearance: none;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            background: #007bff;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        }

        .time-slider::-moz-range-thumb {
            width: 25px;
            height: 25px;
            border-radius: 50%;
            background: #007bff;
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        }

        .time-labels {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 12px;
            color: #666;
        }

        .current-time {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin: 15px 0;
            text-align: center;
        }

        .user-form {
            background: #f1f3f4;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            flex: 1;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: bold;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s ease;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #007bff;
        }

        .seats-container {
            display: grid;
            grid-template-columns: repeat(5, 60px);
            gap: 10px;
            justify-content: center;
            margin: 30px 0;
        }

        .seat {
            width: 60px;
            height: 60px;
            background-color: #28a745;
            border: 2px solid #444;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            position: relative;
        }

        .seat:hover:not(.occupied) {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .seat.occupied {
            background-color: #dc3545;
            cursor: not-allowed;
        }

        .seat.selected {
            background-color: #007bff;
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0,123,255,0.5);
        }

        .legend {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .legend-color {
            width: 20px;
            height: 20px;
            border-radius: 4px;
            border: 1px solid #333;
        }

        .legend-free { background-color: #28a745; }
        .legend-occupied { background-color: #dc3545; }
        .legend-selected { background-color: #007bff; }

        .confirm-btn {
            margin-top: 30px;
            padding: 15px 30px;
            font-size: 18px;
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        .confirm-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }

        .confirm-btn:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .message {
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }

        .error-message {
            color: #dc3545;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
        }

        .success-message {
            color: #155724;
            background: #d4edda;
            border: 1px solid #c3e6cb;
        }

        .no-seats-message {
            color: #856404;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
        }

        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #dc3545;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        .logout-btn:hover {
            background: #c82333;
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
            }
            
            .seats-container {
                grid-template-columns: repeat(3, 60px);
            }
            
            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <a href="logout.php" class="logout-btn">Wyloguj</a>
    
    <div class="container">
        <h1>Panel Restauracji</h1>
        
        <!-- Dane restauracji -->
        <div class="card">
            <h2>Informacje o restauracji</h2>
            <div class="restaurant-info">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">ID</div>
                        <div class="info-value"><?= htmlspecialchars($restaurant_data['id']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($restaurant_data['email']) ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Nazwa restauracji</div>
                        <div class="info-value"><?= htmlspecialchars($restaurant_data['restaurant_name'] ?? 'Brak danych') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Miasto</div>
                        <div class="info-value"><?= htmlspecialchars($restaurant_data['city'] ?? 'Brak danych') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Adres</div>
                        <div class="info-value"><?= htmlspecialchars($restaurant_data['address'] ?? 'Brak danych') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Godziny otwarcia</div>
                        <div class="info-value"><?= htmlspecialchars($restaurant_data['opening_hours'] ?? 'Brak danych') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Email kontaktowy</div>
                        <div class="info-value"><?= htmlspecialchars($restaurant_data['contact_email'] ?? 'Brak danych') ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Telefon</div>
                        <div class="info-value"><?= htmlspecialchars($restaurant_data['phone'] ?? 'Brak danych') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista rezerwacji -->
        <div class="card">
            <h2>Aktualne rezerwacje</h2>
            <?php if ($reservations): ?>
                <table class="reservations-table">
                    <thead>
                        <tr>
                            <th>Godzina</th>
                            <th>Miejsce</th>
                            <th>Nazwisko</th>
                            <th>Email</th>
                            <th>Telefon</th>
                            <th>Akcja</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservations as $res): ?>
                            <tr>
                                <td><?= htmlspecialchars($res['time_slot']) ?></td>
                                <td><?= htmlspecialchars($res['seat_number']) ?></td>
                                <td><?= htmlspecialchars($res['nazwisko']) ?></td>
                                <td><?= htmlspecialchars($res['email']) ?></td>
                                <td><?= htmlspecialchars($res['phone'] ?? 'Brak') ?></td>
                                <td>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="delete_reservation_id" value="<?= intval($res['id']) ?>">
                                        <button type="submit" class="delete-btn" onclick="return confirm('Na pewno usunąć tę rezerwację?')">Usuń</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; color: #666; padding: 20px;">Brak rezerwacji</p>
            <?php endif; ?>
        </div>

        <!-- Nowa wizualna rezerwacja -->
        <div class="card">
            <div class="visual-reservation">
                <h2>Dodaj nową rezerwację</h2>
                
                <div class="user-form">
                    <h3>Dane klienta</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="userLastname">Nazwisko *</label>
                            <input type="text" id="userLastname" placeholder="Wprowadź nazwisko" required>
                        </div>
                        <div class="form-group">
                            <label for="userEmail">Email *</label>
                            <input type="email" id="userEmail" placeholder="klient@email.com" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="userPhone">Telefon (opcjonalnie)</label>
                            <input type="tel" id="userPhone" placeholder="+48 123 456 789">
                        </div>
                    </div>
                </div>

                <div class="time-selector">
                    <h3>Wybierz godzinę rezerwacji</h3>
                    <div class="time-slider-container">
                        <input type="range" id="timeSlider" class="time-slider" min="0" max="4" value="0" step="1">
                        <div class="time-labels">
                            <span>12:00</span>
                            <span>14:00</span>
                            <span>16:00</span>
                            <span>18:00</span>
                            <span>20:00</span>
                        </div>
                    </div>
                    <div class="current-time" id="currentTime">12:00</div>
                </div>

                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color legend-free"></div>
                        <span>Wolne</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color legend-occupied"></div>
                        <span>Zajęte</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color legend-selected"></div>
                        <span>Wybrane</span>
                    </div>
                </div>

                <div id="errorMessage" class="message error-message" style="display: none;"></div>
                <div id="successMessage" class="message success-message" style="display: none;"></div>
                <div id="loadingMessage" style="margin: 20px 0; text-align: center;">
                    <div class="loading"></div> Ładowanie miejsc...
                </div>
                <div id="noSeatsMessage" class="message no-seats-message" style="display: none;">
                    Brak dostępnych miejsc dla wybranej godziny.
                </div>
                
                <div class="seats-container" id="seatsContainer" style="display: none;"></div>
                <button id="confirmBtn" class="confirm-btn" style="display: none;" disabled>Zatwierdź rezerwację</button>
            </div>
        </div>
    </div>

    <script>
        const seatsContainer = document.getElementById('seatsContainer');
        const confirmBtn = document.getElementById('confirmBtn');
        const loadingMessage = document.getElementById('loadingMessage');
        const errorMessage = document.getElementById('errorMessage');
        const successMessage = document.getElementById('successMessage');
        const noSeatsMessage = document.getElementById('noSeatsMessage');
        const timeSlider = document.getElementById('timeSlider');
        const currentTimeEl = document.getElementById('currentTime');
        
        const userLastnameInput = document.getElementById('userLastname');
        const userEmailInput = document.getElementById('userEmail');
        const userPhoneInput = document.getElementById('userPhone');

        const currentRestaurantId = <?= $rest_id ?>;
        const timeSlots = ['12:00', '14:00', '16:00', '18:00', '20:00'];
        let selectedSeats = [];
        let currentTimeSlot = timeSlots[0];

        function showError(message) {
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
            successMessage.style.display = 'none';
            loadingMessage.style.display = 'none';
        }

        function showSuccess(message) {
            successMessage.textContent = message;
            successMessage.style.display = 'block';
            errorMessage.style.display = 'none';
            setTimeout(() => {
                location.reload(); // Odśwież stronę po 2 sekundach
            }, 2000);
        }

        function hideMessages() {
            errorMessage.style.display = 'none';
            successMessage.style.display = 'none';
        }

        function showNoSeats() {
            noSeatsMessage.style.display = 'block';
            seatsContainer.style.display = 'none';
            confirmBtn.style.display = 'none';
        }

        function hideNoSeats() {
            noSeatsMessage.style.display = 'none';
        }

        function validateForm() {
            const lastname = userLastnameInput.value.trim();
            const email = userEmailInput.value.trim();
            
            if (!lastname) {
                showError('Proszę wprowadzić nazwisko.');
                return false;
            }
            
            if (!email) {
                showError('Proszę wprowadzić adres email.');
                return false;
            }
            
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                showError('Proszę wprowadzić prawidłowy adres email.');
                return false;
            }
            
            return true;
        }

        function updateConfirmButton() {
            const hasSeats = selectedSeats.length > 0;
            const hasValidData = userLastnameInput.value.trim() && userEmailInput.value.trim();
            
            confirmBtn.disabled = !hasSeats || !hasValidData;
            confirmBtn.textContent = selectedSeats.length > 0 
                ? `Zatwierdź rezerwację (${selectedSeats.length} miejsc na ${currentTimeSlot})` 
                : 'Zatwierdź rezerwację';
        }

        function updateTimeDisplay() {
            const sliderValue = parseInt(timeSlider.value);
            currentTimeSlot = timeSlots[sliderValue];
            currentTimeEl.textContent = currentTimeSlot;
            loadSeats();
        }

        function loadSeats() {
            loadingMessage.style.display = 'block';
            seatsContainer.style.display = 'none';
            confirmBtn.style.display = 'none';
            hideMessages();
            hideNoSeats();
            selectedSeats = [];

            fetch(`get_seats.php?restaurant_id=${currentRestaurantId}&time_slot=${currentTimeSlot}:00`)
                .then(res => res.json())
                .then(data => {
                    if (data.error) {
                        showError(data.error);
                        return;
                    }

                    loadingMessage.style.display = 'none';

                    if (data.length === 0) {
                        showNoSeats();
                        return;
                    }

                    seatsContainer.style.display = 'grid';
                    confirmBtn.style.display = 'block';
                    seatsContainer.innerHTML = '';

                    data.forEach((seatData) => {
                        const seat = document.createElement('div');
                        seat.classList.add('seat');
                        if (seatData.is_occupied) {
                            seat.classList.add('occupied');
                        }

                        seat.dataset.seatNumber = seatData.seat_number;
                        seat.textContent = seatData.seat_number;

                        seat.addEventListener('click', () => {
                            if (!seat.classList.contains('occupied')) {
                                const seatNumber = parseInt(seat.dataset.seatNumber);
                                
                                if (seat.classList.contains('selected')) {
                                    seat.classList.remove('selected');
                                    selectedSeats = selectedSeats.filter(s => s !== seatNumber);
                                } else {
                                    seat.classList.add('selected');
                                    selectedSeats.push(seatNumber);
                                }
                                
                                updateConfirmButton();
                            }
                        });

                        seatsContainer.appendChild(seat);
                    });

                    updateConfirmButton();
                })
                .catch(err => {
                    console.error('Błąd:', err);
                    showError('Nie udało się załadować miejsc. Sprawdź połączenie z serwerem.');
                });
        }

        // Event listeners
        timeSlider.addEventListener('input', updateTimeDisplay);
        userLastnameInput.addEventListener('input', updateConfirmButton);
        userEmailInput.addEventListener('input', updateConfirmButton);

        confirmBtn.addEventListener('click', () => {
            if (!validateForm()) {
                return;
            }
            
            if (selectedSeats.length === 0) {
                showError('Wybierz przynajmniej jedno miejsce.');
                return;
            }

            confirmBtn.disabled = true;
            confirmBtn.textContent = 'Rezerwuję...';

            const reservationData = {
                selectedSeats: selectedSeats,
                restaurant_id: currentRestaurantId,
                time_slot: currentTimeSlot + ':00',
                nazwisko: userLastnameInput.value.trim(),
                email: userEmailInput.value.trim(),
                phone: userPhoneInput.value.trim() || null
            };

            fetch('update_seats.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(reservationData)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showSuccess(`Sukces! ${data.message}`);
                    userLastnameInput.value = '';
                    userEmailInput.value = '';
                    userPhoneInput.value = '';
                    selectedSeats = [];
                } else {
                    showError(`Błąd: ${data.error}`);
                    confirmBtn.disabled = false;
                    updateConfirmButton();
                }
            })
            .catch(err => {
                console.error('Błąd:', err);
                showError('Wystąpił błąd podczas rezerwacji. Spróbuj ponownie.');
                confirmBtn.disabled = false;
                updateConfirmButton();
            });
        });

        // Inicjalizacja
        loadSeats();
    </script>
</body>
</html>