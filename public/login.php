<?php
session_start();
$mensaje=isset($_GET['error']) ? $_GET['error'] : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="./assets/css/bootstrap.css" rel="stylesheet">
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
            if($mensaje){
            ?>
            <div class="alert alert-danger alert-dismissible fade-show mb-4" role="alert">
                <?= htmlspecialchars($mensaje)?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"
                aria-label="Close"></button>
            </div>
            <?php } ?>
            <div class="mb-3">
                <label class="form-label">Correo electrónico</label>
                <input type="email" class="form-control" id="mail" name="mail" required>
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