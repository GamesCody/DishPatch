<?php
<<<<<<< HEAD
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'dishpatch'; // lub inna nazwa Twojej bazy

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Błąd połączenia z bazą danych: ' . $conn->connect_error);
}
?>
=======
$host = "localhost";
$user = "root";
$pass = "";
$db = "DishPatch";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}
?>
>>>>>>> 5b57ec690160374c0b6f3e37eea7db78fae3cd5f
