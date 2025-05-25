<?php
$host = "localhost";      // adres serwera MySQL, zwykle localhost
$user = "root";           // nazwa użytkownika MySQL
$password = "";           // hasło do MySQL (domyślnie puste w XAMPP)
$dbname = "DishPatch";         // nazwa Twojej bazy danych

// Tworzymy połączenie
$conn = new mysqli($host, $user, $password, $dbname);

// Sprawdzamy czy połączenie się powiodło
if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}
echo "Połączenie z bazą danych powiodło się!";

// Zamykamy połączenie
$conn->close();
?>
