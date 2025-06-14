<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = 'dishpatch.sapport@gmail.com';
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');
    $subject = "Wiadomość z formularza kontaktowego (O nas)";
    $body = "Imię i nazwisko: $name\nEmail: $email\n\nWiadomość:\n$message";
    $headers = "From: $email\r\nReply-To: $email\r\nContent-Type: text/plain; charset=UTF-8";
    if (mail($to, $subject, $body, $headers)) {
        header('Location: dodatki/thankyou.html');
        exit();
    } else {
        echo '<p style="color:red;">Nie udało się wysłać wiadomości. Spróbuj ponownie później.</p>';
    }
}
?>
