<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if(isset($_SESSION['id_usr'])){
    // ✅ Limpiar session_id de la BD
    $sql = "UPDATE usuarios SET session_id = NULL, session_time = NULL WHERE id_usr = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['id_usr']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}

session_destroy();
header('Location: login.php?msg=' . urlencode('Sesión cerrada correctamente'));
exit;
?>