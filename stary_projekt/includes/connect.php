<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "DishPatch";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}
?>

