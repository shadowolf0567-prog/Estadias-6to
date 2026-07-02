<?php
if($_SERVER['REQUEST_METHOD']==='POST'){
    session_start();
    if(isset($_POST['mail'],$_POST['pass'])){
        require_once __DIR__ . '/../../config/db.php';
        $mail=trim($_POST['mail']);
        $password=trim($_POST['pass']);
        $sql="SELECT id_usr,nom_usr,mail,pass,tip_usr FROM usuarios WHERE mail = ?";
        $query_preparado=mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($query_preparado,"s",$mail);
        mysqli_stmt_execute($query_preparado);
        $resultado=mysqli_stmt_get_result($query_preparado);
        if($resultado && mysqli_num_rows($resultado)==1){
            $usuario=mysqli_fetch_assoc($resultado);
            if(password_verify($password,$usuario['pass']) || $usuario['pass']==$password){
                $_SESSION['id_usr']=$usuario['id_usr'];
                $_SESSION['nom_usr']=$usuario['nom_usr'];
                $_SESSION['mail']=$usuario['mail'];
                $_SESSION['tip_usr']=$usuario['tip_usr'];
                switch($usuario['tip_usr']){
                    case 1:
                        mysqli_close($conn);
                        header("Location: ../gestion/clientes.php");
                        exit;
                    case 2:
                        mysqli_close($conn);
                        header("Location: ../gestion/reportes.php");
                        exit;
                    case 3:
                        mysqli_close($conn);
                        header("Location: ../gestion/agregar_clientes.php");
                        exit;
                    default:
                        mysqli_close($conn);
                        header('Location: ../login.php?error='.urlencode('Usuario no reconocido'));
                        exit;
                    }
                }else{
                    header('Location: ../login.php?error='.urldecode('Correo no registrado'));
                    exit;
                }
            }else{
                header('Location: ../login.php?error='.urldecode('Completa el formulario'));
                exit;
            }
        }else{
            header('Location: ../login.php?error='.urldecode('Acceso no permitido'));
            exit;
        }
    }
//                 if($usuario['tip_usr']==1){
//                     mysqli_close($conn);
//                     header("Location: ../gestion/dashboard.php");
//                     exit;
//                 }else{
//                     mysqli_close($conn);
//                     header('Location: ../login.php?error=' . urldecode('Usuario no encontrado'));
//                     exit;
//                 }
//             }else{
//                 header('Location: ../login.php?error='.urldecode('Credenciales incorrectas'));
//                 exit;
//             }
//         }else{
//             header('Location: ../login.php?error='.urldecode('Correo no registrado'));
//             exit;
//         }
//     }else{
//         header('Location: ../login.php?error='.urldecode('Completa el formulario'));
//         exit;
//     }
// }else{
//     header('Location: ../login.php?error='.urldecode('Acceso no permitido'));
//     exit;
// }
?>