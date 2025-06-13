<?php
// api/auth.php
// Kompleksowa obsługa rejestracji, logowania, Google Sign-In, sesji

session_start();
header('Content-Type: application/json');

// reCAPTCHA backend validation
function verify_recaptcha($token) {
    $secret = '6LcewVorAAAAAIZmiZq6lgUrnLFOU6KdQdiYpoK3'; // Twój secret key
    $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret . '&response=' . $token);
    $result = json_decode($response, true);
    return $result['success'] ?? false;
}

require_once '../includes/connect.php';

$action = $_POST['action'] ?? ($_GET['action'] ?? '');

switch ($action) {
    case 'register':
        $recaptcha = $_POST['recaptcha'] ?? '';
        if (!$recaptcha || !verify_recaptcha($recaptcha)) {
            echo json_encode(['success' => false, 'message' => 'Błąd reCAPTCHA.']);
            exit;
        }
        $first_name = $_POST['firstName'] ?? '';
        $last_name = $_POST['lastName'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $address = $_POST['address'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $rep_password = $_POST['confirmPassword'] ?? '';
        if (!$first_name || !$last_name || !$phone || !$address || !$email || !$password || !$rep_password) {
            echo json_encode(['success' => false, 'message' => 'Wypełnij wszystkie wymagane pola.']);
            exit;
        }
        if ($password !== $rep_password) {
            echo json_encode(['success' => false, 'message' => 'Hasła nie są zgodne.']);
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Nieprawidłowy adres e-mail.']);
            exit;
        }
        $stmt = $conn->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Użytkownik o tym adresie e-mail już istnieje.']);
            exit;
        }
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $activation_token = bin2hex(random_bytes(32));
        $stmt = $conn->prepare('INSERT INTO users (name, surname, phone, address, email, password, is_active, activation_token, created_at) VALUES (?, ?, ?, ?, ?, ?, 0, ?, NOW())');
        $stmt->bind_param('sssssss', $first_name, $last_name, $phone, $address, $email, $hash, $activation_token);
        if ($stmt->execute()) {
            // Wysyłka maila z linkiem aktywacyjnym
            $activation_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . "/auth.php?action=activate&token=$activation_token";
            $subject = 'Aktywacja konta DishPatch';
            $body = "Cześć $first_name!\n\nAby aktywować konto, kliknij w poniższy link:\n$activation_link\n\nJeśli to nie Ty zakładałeś konto, zignoruj tę wiadomość.";
            $headers = 'From: dishpatch.sapport@gmail.com' . "\r\n";
            mail($email, $subject, $body, $headers);
            echo json_encode(['success' => true, 'message' => 'Rejestracja zakończona sukcesem. Sprawdź e-mail i aktywuj konto.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Błąd rejestracji: ' . $stmt->error]);
        }
        break;
    case 'activate':
        $token = $_GET['token'] ?? '';
        if (!$token) {
            echo json_encode(['success' => false, 'message' => 'Brak tokena aktywacyjnego.']);
            exit;
        }
        $stmt = $conn->prepare('SELECT id, is_active FROM users WHERE activation_token = ?');
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $is_active);
            $stmt->fetch();
            if ($is_active) {
                echo json_encode(['success' => false, 'message' => 'Konto już aktywne.']);
                exit;
            }
            $stmt2 = $conn->prepare('UPDATE users SET is_active = 1, activation_token = NULL WHERE id = ?');
            $stmt2->bind_param('i', $id);
            if ($stmt2->execute()) {
                echo json_encode(['success' => true, 'message' => 'Konto zostało aktywowane. Możesz się zalogować.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Błąd aktywacji: ' . $stmt2->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Nieprawidłowy token aktywacyjny.']);
        }
        break;
    case 'login':
        $recaptcha = $_POST['recaptcha'] ?? '';
        if (!$recaptcha || !verify_recaptcha($recaptcha)) {
            echo json_encode(['success' => false, 'message' => 'Błąd reCAPTCHA.']);
            exit;
        }
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        if (!$email || !$password) {
            echo json_encode(['success' => false, 'message' => 'Podaj e-mail i hasło.']);
            exit;
        }
        $stmt = $conn->prepare('SELECT id, name, surname, password, is_active FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $db_name, $db_surname, $db_password, $is_active);
            $stmt->fetch();
            if (!$is_active) {
                echo json_encode(['success' => false, 'message' => 'Konto nieaktywne.']);
                exit;
            }
            if (password_verify($password, $db_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $db_name;
                $_SESSION['user_surname'] = $db_surname;
                $_SESSION['last_activity'] = time();
                echo json_encode(['success' => true, 'message' => 'Zalogowano.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Nieprawidłowe hasło.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Nie znaleziono użytkownika.']);
        }
        break;
    case 'google':
        $data = json_decode(file_get_contents('php://input'), true);
        $credential = $data['credential'] ?? '';
        if (!$credential) {
            echo json_encode(['success' => false, 'message' => 'Brak tokena Google.']);
            exit;
        }
        $google_api_url = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $credential;
        $google_response = file_get_contents($google_api_url);
        $google_data = json_decode($google_response, true);
        if (!isset($google_data['email'])) {
            echo json_encode(['success' => false, 'message' => 'Nieprawidłowy token Google.']);
            exit;
        }
        $email = $google_data['email'];
        $name = $google_data['given_name'] ?? '';
        $surname = $google_data['family_name'] ?? '';
        $google_id = $google_data['sub'] ?? '';
        $stmt = $conn->prepare('SELECT id, name, surname, is_active FROM users WHERE google_id = ? OR email = ?');
        $stmt->bind_param('ss', $google_id, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $db_name, $db_surname, $is_active);
            $stmt->fetch();
            if (!$is_active) {
                echo json_encode(['success' => false, 'message' => 'Konto nieaktywne.']);
                exit;
            }
            $_SESSION['user_id'] = $id;
            $_SESSION['user_name'] = $db_name;
            $_SESSION['user_surname'] = $db_surname;
            $_SESSION['last_activity'] = time();
            echo json_encode(['success' => true, 'message' => 'Zalogowano przez Google.']);
        } else {
            $stmt = $conn->prepare('INSERT INTO users (name, surname, email, google_id, is_active, created_at) VALUES (?, ?, ?, ?, 1, NOW())');
            $stmt->bind_param('ssss', $name, $surname, $email, $google_id);
            if ($stmt->execute()) {
                $id = $stmt->insert_id;
                $_SESSION['user_id'] = $id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_surname'] = $surname;
                $_SESSION['last_activity'] = time();
                echo json_encode(['success' => true, 'message' => 'Zarejestrowano i zalogowano przez Google.']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Błąd rejestracji przez Google: ' . $stmt->error]);
            }
        }
        break;
    case 'logout':
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Wylogowano.']);
        break;
    case 'session':
        if (isset($_SESSION['user_id'])) {
            echo json_encode([
                'logged_in' => true,
                'user_id' => $_SESSION['user_id'],
                'user_name' => $_SESSION['user_name'],
                'user_surname' => $_SESSION['user_surname']
            ]);
        } else {
            echo json_encode(['logged_in' => false]);
        }
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Nieznana akcja.']);
}
