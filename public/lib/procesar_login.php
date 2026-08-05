<?php
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    
    if(isset($_POST['mail'], $_POST['pass'])){
        require_once __DIR__ . '/../../config/db.php';
        
        $mail = trim($_POST['mail']);
        $password = trim($_POST['pass']);
        
        $sql = "SELECT id_usr, nom_usr, mail, pass, tip_usr FROM usuarios WHERE mail = ?";
        $query_preparado = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($query_preparado, "s", $mail);
        mysqli_stmt_execute($query_preparado);
        $resultado = mysqli_stmt_get_result($query_preparado);
        
        if($resultado && mysqli_num_rows($resultado) == 1){
            $usuario = mysqli_fetch_assoc($resultado);
            
            if($usuario['pass'] == $password){
                
                session_destroy();
                session_start();
                
                $_SESSION['id_usr'] = $usuario['id_usr'];
                $_SESSION['nom_usr'] = $usuario['nom_usr'];
                $_SESSION['mail'] = $usuario['mail'];
                $_SESSION['tip_usr'] = $usuario['tip_usr'];
                $_SESSION['login_time'] = time();
                
                $session_id = session_id();
                $now = date('Y-m-d H:i:s');
                
                $sql_update = "UPDATE usuarios SET session_id = ?, session_time = ? WHERE id_usr = ?";
                $stmt_update = mysqli_prepare($conn, $sql_update);
                mysqli_stmt_bind_param($stmt_update, 'ssi', $session_id, $now, $usuario['id_usr']);
                mysqli_stmt_execute($stmt_update);
                mysqli_stmt_close($stmt_update);
                switch($usuario['tip_usr']){
                    case 1:
                        header("Location: ../clientes/clientes.php");
                        exit;
                    case 2:
                        header("Location: ../reportes/reportes.php");
                        exit;
                    case 3:
                        header("Location: ../gestion/pruebas.php");
                        exit;
                    default:
                        header('Location: ../login.php?error=' . urlencode('Usuario no reconocido'));
                        exit;
                }
            } else {
                mysqli_close($conn);
                header('Location: ../login.php?error=' . urlencode('Contraseña incorrecta'));
                exit;
            }
        } else {
            mysqli_close($conn);
            header('Location: ../login.php?error=' . urlencode('Correo no registrado'));
            exit;
        }
    } else {
        header('Location: ../login.php?error=' . urlencode('Completa todos los campos'));
        exit;
    }
}
?>