<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

header('Content-Type: application/json');

if(!isset($_SESSION['id_usr']) || !isset($_SESSION['session_token'])){
    echo json_encode(['valido' => false]);
    exit;
}

$id_usr = $_SESSION['id_usr'];
$token_actual = $_SESSION['session_token'];

$sql = "SELECT session_tokens FROM usuarios WHERE id_usr = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_usr);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);
mysqli_close($conn);

if($usuario && !empty($usuario['session_tokens'])){
    $tokens = json_decode($usuario['session_tokens'], true);
    
    if(in_array($token_actual, $tokens)){
        echo json_encode(['valido' => true]);
    } else {
        echo json_encode(['valido' => false]);
    }
} else {
    echo json_encode(['valido' => false]);
}
?>