<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $to = 'mail@gmail.com'; // Zmień na swój adres email
    
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $message = htmlspecialchars($_POST['message'] ?? '');
    $subject = "Wiadomość z formularza kontaktowego DishPatch";
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
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>Kontakt - DishPatch</title>
    <link rel="icon" type="image/png" href="images/favicon.png">
    <style>body{font-family:Arial,sans-serif;background:#f8f8f8;max-width:700px;margin:40px auto;padding:32px;border-radius:10px;box-shadow:0 2px 12px #eee;}h1{color:#2d8f5a;}a{color:#2d8f5a;text-decoration:none;}form{margin-top:24px;}input,textarea{width:100%;padding:10px;margin:8px 0;border-radius:5px;border:1px solid #ccc;}button{background:#2d8f5a;color:#fff;border:none;padding:12px 24px;border-radius:6px;font-size:1rem;cursor:pointer;}button:hover{background:#226b41;}</style>
</head>
<body>
    <a href="index.php"><img src="images/logo.png" alt="logo" style="height:64px;display:block;margin:auto;"></a>
    <h1>Kontakt</h1>
    <p>Masz pytania lub sugestie? Skontaktuj się z nami!</p>
    <form method="post">
        <input type="text" name="name" placeholder="Imię i nazwisko" required>
        <input type="email" name="email" placeholder="Twój email" required>
        <textarea name="message" placeholder="Wiadomość" rows="5" required></textarea>
        <button type="submit">Wyślij</button>
    </form>
    <button onclick="history.back()" style="display:block;margin:32px auto 0 auto;padding:10px 28px;background:#2d8f5a;color:#fff;border:none;border-radius:6px;font-size:1rem;cursor:pointer;">Powrót</button>
    <footer style="text-align:center;margin-top:40px;color:#888;font-size:1rem;">&copy; DishPatch 2025</footer>
</body>
</html>
