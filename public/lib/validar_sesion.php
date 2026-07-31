<?php
if(!isset($_SESSION['id_usr'])){
    header('Location: ../login.php?error=' . urlencode('Debes iniciar sesión'));
    exit;
}
require_once __DIR__ . '/../../config/db.php';

if(!$conn){
    die("Error de conexión a la base de datos");
}

$id_usr = $_SESSION['id_usr'];
$session_id_actual = session_id();
$sql = "SELECT session_id FROM usuarios WHERE id_usr = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_usr);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$usuario = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if($usuario && !empty($usuario['session_id'])){
    if($usuario['session_id'] !== $session_id_actual){
        session_destroy();
        header('Location: ../login.php?error=' . urlencode('Sesión iniciada en otro navegador'));
        exit;
    }
}

// ✅ Si no hay session_id en BD, guardarlo
if(!$usuario || empty($usuario['session_id'])){
    $sql_update = "UPDATE usuarios SET session_id = ? WHERE id_usr = ?";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, 'si', $session_id_actual, $id_usr);
    mysqli_stmt_execute($stmt_update);
    mysqli_stmt_close($stmt_update);
}
if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] != 1 && $_SESSION['tip_usr'] != 2 && $_SESSION['tip_usr'] != 3)){
    session_destroy();
    header('Location: ../login.php?error=' . urlencode('Acceso denegado'));
    exit;
}

// ✅ Expirar sesión después de 8 horas
if(isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 28800){
    session_destroy();
    header('Location: ../login.php?error=' . urlencode('Sesión expirada'));
    exit;
}
?>