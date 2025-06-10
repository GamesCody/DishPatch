<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'dishpatch'; // lub inna nazwa Twojej bazy

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die('Błąd połączenia z bazą danych: ' . $conn->connect_error);
}
?>