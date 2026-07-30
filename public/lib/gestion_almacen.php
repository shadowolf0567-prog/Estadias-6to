<?php
require_once __DIR__ . '/../../config/db.php';
function agregar ($nombre,$serie,$cantidad,$info_adicional){
    global $conn;
    if(!$conn){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la base de datos'
        ];
    }
    $sql = "INSERT INTO almacen(nombre,serie,cantidad,info_adicional)
            VALUES (?,?,?,?)";
    $insert_preparado = mysqli_prepare($conn,$sql);
    if(!$insert_preparado){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la preparacion'
        ];
    }
    mysqli_stmt_bind_param($insert_preparado,'ssis',$nombre,$serie,$cantidad,$info_adicional);
    $query_ok = mysqli_stmt_execute($insert_preparado);
    if(!$query_ok){
        $error = mysqli_stmt_error($insert_preparado);
        mysqli_stmt_close($insert_preparado);
        return[
            'estatus' => 'error',
            'mensaje' => 'Error al insertar'
        ];
    }
    $rows_ok = mysqli_affected_rows($conn);
    mysqli_stmt_close($insert_preparado);
    if($query_ok && $rows_ok > 0){
        return[
            'estatus' => 'msg',
            'mensaje' => 'Equipo registrado correctamente'
        ];
    }
}
function editar($id,$nombre,$serie,$cantidad,$info_adicional){
    global $conn;
    if(!$conn){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error de conexión a la base de datos'
        ];
    }
    $sql = "UPDATE almacen SET nombre = ?,serie =? ,cantidad = ?,info_adicional = ? WHERE id= ?";
    $stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt, 'ssisi', $nombre, $serie, $cantidad, $info_adicional,$id);

    if(mysqli_stmt_execute($stmt)){
        $rows_affected = mysqli_affected_rows($conn);
        mysqli_stmt_close($stmt);
        if($rows_affected > 0){
            return[
            'estatus' => 'msg',
            'mensaje' => 'Recurso acrualizado correctamente'
                ];
            }else{
                return[
                    'estatus' => 'info',
                    'mensaje' => 'No se realizaron cambios'
                ];
            }
        }else{
            $error = mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            return[
                'estatus' => 'error',
                'mensaje' => 'Error al actualizar: '.$error 
            ];
        }
        
}
function eliminar($id){
    global $conn;
    $sql = "DELETE FROM almacen WHERE id = ?";
    $delete_preparado = mysqli_prepare($conn,$sql);
    if(!$delete_preparado){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }
    mysqli_stmt_bind_param($delete_preparado,'i',$id);
    $query_ok = mysqli_stmt_execute($delete_preparado);
    $rows_ok = mysqli_affected_rows($conn);
    mysqli_stmt_close($delete_preparado);
    if($query_ok && $rows_ok){
        return[
            'estatus' => 'msg',
            'mensaje' => 'Producto eliminado exitosamente'
        ];
    }else{
        return[
            'estatus' => 'error',
            'mensaje' => 'No se pudo eliminar el producto'
        ];
    }
}
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['accion'])){
        $accion = $_POST['accion'];
        switch($accion){
            case 'agregar':
                if(isset($_POST['nombre']) && !empty($_POST['nombre'])){
                    $nombre = trim($_POST['nombre'] ?? '');
                    $serie = trim($_POST['serie'] ?? '');
                    $cantidad = intval($_POST['cantidad']);
                    $info_adicional = trim($_POST['info_adicional'] ?? '');

                    $resultado = agregar($nombre,$serie,$cantidad,$info_adicional);
                    if($resultado['estatus'] === 'msg'){
                        header('Location: ../gestion/almacen.php?msg='.urlencode($resultado['mensaje']));
                    }else{
                        header('Location: ../gestion/almacen.php?error='.urlencode($resultado['mensaje']));
                    }
                    exit;
                }
                break;
            case 'editar':
                if(isset($_POST['id']) && isset($_POST['nombre']) && !empty($_POST['nombre'])){
                    $id = intval($_POST['id']);
                    $nombre = trim($_POST['nombre']);
                    $serie = trim($_POST['serie']);
                    $cantidad = intval($_POST['cantidad']);
                    $info_adicional = trim($_POST['info_adicional']);
                    $resultado = editar($id,$nombre,$serie,$cantidad,$info_adicional);
                    if($resultado['estatus'] === 'msg' || $resultado['estatus'] === 'info'){
                        header('Location: ../gestion/almacen.php?msg='.urlencode($resultado['mensaje']));
                    }else{
                        header('Location: ../gestion/almacen.php?error='.urlencode($resultado['mensaje']));
                        exit;
                    }
                }
                break;
            case 'eliminar':
                if(isset($_POST['id'])){
                    $id = intval($_POST['id']);
                    $resultado = eliminar($id);
                    header('Location: ../gestion/almacen.php?'.$resultado['estatus'].'='.urlencode($resultado['mensaje']));
                    exit;
                }
                break;
            default:
                header('Location: ../gestion/almacen.php?error='.urlencode('Acción no valida'));
                exit;
        }
    }
}
?>