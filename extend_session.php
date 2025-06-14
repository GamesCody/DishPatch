<?php
// extend_session.php – resetuje czas sesji użytkownika
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    echo json_encode(['success'=>false]);
    exit();
}
$_SESSION['session_start'] = time();
echo json_encode(['success'=>true, 'session_start'=>$_SESSION['session_start']]);
