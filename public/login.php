<?php
session_start();
require_once __DIR__ . '/../config/db.php';
$error = isset($_GET['error']) ? $_GET['error'] : '';
// if($_SERVER['REQUEST_METHOD'] === 'POST'){
//     $usuario = trim($_POST['usuario'] ?? '');
//     $password = trim($_POST['password'] ?? '');
    
//     $sql = "SELECT id, nombre, usuario, password, tip_usr FROM usuarios WHERE usuario = ?";
//     $stmt = mysqli_prepare($conn, $sql);
//     mysqli_stmt_bind_param($stmt, 's', $usuario);
//     mysqli_stmt_execute($stmt);
//     $result = mysqli_stmt_get_result($stmt);
//     $user = mysqli_fetch_assoc($result);
    
//     $usuario_valido = false;
//     if($usuario_valido){
//         session_destroy();
//         session_start();
        
//         $_SESSION['id_usuario'] = $id_usuario;
//         $_SESSION['nombre'] = $nombre;
//         $_SESSION['tip_usr'] = $tip_usr;
//         $_SESSION['login_time'] = time();
//         $_SESSION['sesion_activa'] = true;
        
//         if($tip_usr == 1 || $tip_usr == 2){
//             header('Location: ../gestion/dashboard.php');
//         } else {
//             header('Location: ../gestion/equipos.php');
//         }
//         exit;
//     } else {
//         header('Location: login.php?error=' . urlencode('Usuario o contraseña incorrectos'));
//         exit;
//     }
// }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="./assets/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/responsives.css">
    <link rel="stylesheet" href="./assets/css/bootstrap-icons.css">
    <style>
        .login_form{
            width: 40%;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container my-5">
        <h2 class="text-center">Iniciar Sesión</h2>
        <form action="lib/procesar_login.php" method="post" class="login-form mx-auto p-4 border rounded bg-white">
            <?php
            if($error){
            ?>
            <div class="alert alert-danger alert-dismissible fade-show mb-4" role="alert">
                <?= htmlspecialchars($error)?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"
                aria-label="Close"></button>
            </div>
            <?php } ?>
            <div class="mb-3">
                <label class="form-label">Usuario</label>
                <input type="text" class="form-control" id="mail" name="mail" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña</label>
                <input type="password" class="form-control" id="pass" name="pass" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Ingresar</button>
        </form>
    </div>
    <script src="./assets/js/bootstrap.min.js"></script>
</body>
</html>