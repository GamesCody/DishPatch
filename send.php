<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if ($name && $email && $message) {
        // Wyślij maila do dispatch.sapport@gmail.com
        $to = "dishpatch.sapport@gmail.com";
        $subject = "Nowa wiadomość z formularza kontaktowego DishPatch";
        $body = "Imię i nazwisko: $name\nEmail: $email\nTreść wiadomości:\n$message";
        $headers = "From: noreply@" . $_SERVER['SERVER_NAME'] . "\r\n" .
                   "Reply-To: $email\r\n" .
                   "Content-Type: text/plain; charset=UTF-8\r\n";
        mail($to, $subject, $body, $headers);

        // Zapisz do bazy jak dotychczas
        $conn = mysqli_connect("localhost", "root", "", "dishpatch");

        if (!$conn) {
            die("Błąd połączenia z bazą danych: " . mysqli_connect_error());
        }

        $sql = "INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sss", $name, $email, $message);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);

        // Informacja po sukcesie
        header("Location: thankyou.html");
        exit;
    } else {
        echo "Wszystkie pola są wymagane.";
    }
} else {
    echo "Nieprawidłowe żądanie.";
}
?>