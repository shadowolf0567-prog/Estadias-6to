<?php
require_once __DIR__ . '/../../config/db.php';
function agregar_reportes($fecha,$tecnico,/*$refaccion,*/$descripcion,$id_cliente = null,$id_equipo =  null){
    global $conn;
    $sql="INSERT INTO reportes(fecha,tecnico,/*refaccion,*/descripcion,id_cliente,id_equipo,estado)
     VALUES (?,?,?,?/*,?*/,?,'pendiente')";
    $insert_preparado=mysqli_prepare($conn,$sql);
    if(!$insert_preparado){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la base de datos'
        ];
    }
    mysqli_stmt_bind_param($insert_preparado,'sssii',
    $fecha,$tecnico,/*$refaccion,*/$descripcion,$id_cliente,
    $id_equipo);
    /*Fatal error: Uncaught mysqli_sql_exception: Cannot add or update a child
     row: a foreign key constraint fails (`emipac`.`reportes`, CONSTRAINT
     `reportes_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` 
     (`id_cliente`) ON DELETE SET NULL) in 
     C:\Apache24\htdocs\Emipac\public\lib\gestion_reportes.php:17 
     Stack trace: #0 C:\Apache24\htdocs\Emipac\public\lib\gestion_reportes.php(17): 
     mysqli_stmt_execute(Object(mysqli_stmt)) #1 
     C:\Apache24\htdocs\Emipac\public\lib\gestion_reportes.php(231): 
     agregar_reportes('2026-07-07', '', '', 'kljk', 92, 87) #2 {main} thrown in 
     C:\Apache24\htdocs\Emipac\public\lib\gestion_reportes.php on line 17*/
    $query_ok=mysqli_stmt_execute($insert_preparado);
    $rows_ok=mysqli_affected_rows($conn);
    mysqli_stmt_close($insert_preparado);
    if($query_ok && $rows_ok >0) {
        return[
            'estatus' => 'msg',
            'mensaje' => 'Reporte generado con éxito'
        ];
    }else{
        return[
            'estatus' => 'error',
            'mensaje' => 'Error al generar el reporte'
        ];
    }
}
function editar_reporte($id_reporte,$fecha,$descripcion,$id_cliente = null,$id_equipo = null){
    global $conn;
    $sql="UPDATE reportes SET
    fecha=?,
    descripcion=?,
    id_cliente = ?,
    id_equipo =? WHERE id_reporte =?";
    $update_preparado=mysqli_prepare($conn,$sql);
    if(!$update_preparado){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }
    
    mysqli_stmt_bind_param($update_preparado,"ssiii",$fecha,$descripcion,$id_cliente,$id_equipo,$id_reporte);
    $query_ok=mysqli_stmt_execute($update_preparado);
    $rows_ok=mysqli_affected_rows($conn);
    mysqli_stmt_close($update_preparado);
    if($query_ok && $rows_ok>0){
        return[
            'estatus' => 'msg',
            'mensaje' => 'Reporte editado exitosamente'
        ];
    }else{
        return[
            'estatus' => 'error',
            'mensaje' => 'No se pudo editar el reporte'
        ];
    }

}
function eliminar_reporte($id_reporte){
    global $conn;
    $sql="DELETE FROM reportes WHERE id_reporte=?";
    $delete_preparado=mysqli_prepare($conn,$sql);
    if(!$delete_preparado){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }
    mysqli_stmt_bind_param($delete_preparado,'i',$id_reporte);
    $query_ok=mysqli_stmt_execute($delete_preparado);
    $rows_ok=mysqli_affected_rows($conn);
    mysqli_stmt_close($delete_preparado);
    if($query_ok && $rows_ok >0){
        return[
            'estatus' => 'msg',
            'mensaje' => 'Reporte eliminado exitosamente'
        ];
    }else{
        return[
            'estatus' => 'error',
            'mensaje' => 'No se pudo eliminar el reporte'
        ];
    }
}
function eliminar_reportes($id_reporte){
    global $conn;
    $sql="DELETE FROM reportes WHERE id_reporte = ?";
    $delete_preparado=mysqli_prepare($conn,$sql);
    if(!$delete_preparado){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecuricón de la base de datos'
        ];
    }
    mysqli_stmt_bind_param($delete_preparado,'i',$id_reporte);
    $query_ok=mysqli_stmt_execute($delete_preparado);
    $rows_ok=mysqli_affected_rows($conn);
    mysqli_stmt_close($delete_preparado);
    if($query_ok && $rows_ok > 0){
        return[
            'estatus' => 'msg',
            'mensaje' => 'Reporte eliminado exitosamente'
        ];
    }else{
        return[
            'estatus' => 'error',
            'mensaje' => 'No se pudo eliminar el reporte'
        ];
    }
}
function mostrar_reportes(){
    global $conn;
    if(!$conn){
        return[];
    }
    $sql="SELECT * FROM reportes";
    $select_preparado=mysqli_prepare($conn,$sql);
    mysqli_stmt_execute($select_preparado);
    $resultado=mysqli_stmt_get_result($select_preparado);
    $reportes=array();
    while($fila_bd=mysqli_fetch_assoc($resultado)){
        $reportes[]=$fila_bd;
    }
    mysqli_stmt_close($select_preparado);
    return $reportes;
}
function marcar_atendido($id_reporte, $observaciones = '', $tecnico = '', /*$refaccion = '',*/$fecha_atencion = '',$acciones = ''){
    global $conn;
    $sql = "UPDATE reportes SET estado='atendido', 
            fecha_atencion= NOW(), 
            observaciones_atencion=?, 
            tecnico=COALESCE(NULLIF(?,''), tecnico), 
            /*refaccion = COALESCE(NULLIF(?, ''), refaccion),*/
            fecha_atencion = ?, acciones= ? WHERE id_reporte=?";
    $stmt = mysqli_prepare($conn,$sql);
    if(!$stmt){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la preparación'.mysqli_error($conn)
        ];
    }
    mysqli_stmt_bind_param($stmt, 'ssssi', $observaciones, $tecnico, /*$refaccion,*/$fecha_atencion,$acciones,$id_reporte);
    $query_ok = mysqli_stmt_execute($stmt);
    $rows_ok=mysqli_affected_rows($conn);
    mysqli_stmt_close($stmt);
    if($query_ok && $rows_ok > 0){
        return[
            'estatus' => 'msg',
            'mensaje' => 'Reporte marcado como atendido'
        ];
    } else{
        return[
            'estatus' => 'error',
            'mensaje' => 'No se pudo marcar como atendido'
        ];
    }
}
function reabrir_reporte($id_reporte){
    global $conn;
    $sql = "UPDATE reportes SET estado = 'pendiente', fecha_atencion = NULL, observaciones_atencion = NULL, tecnico = NULL, /*refaccion = NULL,*/ acciones = NULL WHERE id_reporte = ?";
    $stmt = mysqli_prepare($conn,$sql);
    if(!$stmt){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la preparación: '.mysqli_error($conn)
        ];
    }
    mysqli_stmt_bind_param($stmt, 'i',$id_reporte);
    $query_ok = mysqli_stmt_execute($stmt);
    $rows_ok = mysqli_affected_rows($conn);
    mysqli_stmt_close($stmt);
    if($query_ok && $rows_ok > 0){
        return[
            'estatus' => 'msg',
            'mensaje' => 'Reporte reabierto correctamente'
        ];
    }else{
        return[
            'estatus' => 'error',
            'mensaje' => 'No se pudo reabrir el reporte'
        ];
    }
}

function editar_atendidos($id_reporte,$fecha,$descripcion,$tecnico,/*$refaccion,*/$fecha_atencion,$observaciones_atencion,$acciones,$id_cliente = null,$id_equipo = null){
    global $conn;
    $sql = "UPDATE reportes SET fecha=?,descripcion=?,
            tecnico=?,/*refaccion=?,*/fecha_atencion=?,observaciones_atencion = ?,acciones=?,
            id_cliente = ?, id_equipo = ? WHERE id_reporte = ?";
    $update_preparado = mysqli_prepare($conn,$sql);
    if(!$update_preparado){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }
    mysqli_stmt_bind_param($update_preparado,"ssssssiii",$fecha,$descripcion,$tecnico,/*$refaccion,*/$fecha_atencion,$observaciones_atencion,$acciones,$id_cliente,$id_equipo,$id_reporte);
    $query_ok = mysqli_stmt_execute($update_preparado);
    $rows_ok = mysqli_affected_rows($conn);
    mysqli_stmt_close($update_preparado);
    if($query_ok && $rows_ok > 0){
        return[
            'estatus' => 'msg',
            'mensaje' => 'Reporte editado exitosamente'
        ];
    }else{
        return[
            'estatus' => 'error',
            'mensaje' => 'No se pudo editar el reporte'
        ];
    }
}
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['accion'])){
        $accion=$_POST['accion'];
        switch($accion){
            case 'agregar':
                if(isset($_POST['fecha'])){
                    $fecha=trim($_POST['fecha']);
                    $tecnico=trim($_POST['tecnico'] ?? '');
                    /*$refaccion=trim($_POST['refaccion'] ?? '');*/
                    $descripcion=trim($_POST['descripcion']);
                    $id_cliente=!empty($_POST['id_cliente']) ? intval($_POST['id_cliente']) : null;
                    $id_equipo=!empty($_POST['id_equipo']) ? intval($_POST['id_equipo']): null;

                    $resultado=agregar_reportes($fecha,$tecnico/*,$refaccion*/,$descripcion,$id_cliente,$id_equipo);
                    header('Location: ../reportes/agregar_reporte.php?'.$resultado['estatus'].'='.urlencode($resultado['mensaje']));
                    exit;
                }
                break;
            case 'editar':
                if(isset($_POST['id_reporte'],$_POST['fecha'],$_POST['descripcion'])){
                    $id_reporte=intval($_POST['id_reporte']);
                    $fecha=trim($_POST['fecha']);
                    $descripcion=trim($_POST['descripcion']);
                    $id_cliente=!empty($_POST['id_cliente']) ? intval($_POST['id_cliente']) : null;
                    $id_equipo=!empty($_POST['id_equipo']) ? intval($_POST['id_equipo']) : null;

                    $resultado=editar_reporte($id_reporte,$fecha,$descripcion,$id_cliente,$id_equipo);
                    header('Location: ../reportes/ver_reporte.php?id='.$id_reporte.'&'.$resultado['estatus'].'='.urlencode($resultado['mensaje']));
                    exit;
                }else{
                    header('Location: ../reportes/editar_reportes.php?error='.urlencode('Faltan campos'));
                    exit;
                }
                break;
            case 'eliminar':
                if(isset($_POST['id_reporte'])){
                    $id_reporte = intval($_POST['id_reporte']);
                    $resultado=eliminar_reporte($id_reporte);
                    header('Location: ../reportes/reportes.php?'.$resultado['estatus'].'='.urlencode($resultado['mensaje']));
                    exit;
                }
                break;
            case 'eliminar2':
                if(isset($_POST['id_reporte'])){
                    $id_reporte = intval($_POST['id_reporte']);
                    $resultado = eliminar_reporte($id_reporte);
                    header('Location: ../reportes/reportes.php?tab=atendido&'.$resultado['estatus'].'='.urlencode(($resultado['mensaje'])));
                    exit;
                }
                break;
            case 'marcar_atendido':
                if(isset($_POST['id_reporte'])){
                    $id_reporte=intval($_POST['id_reporte']);
                    $observaciones = isset($_POST['observaciones_atencion']) ? trim($_POST['observaciones_atencion']) : '';
                    $tecnico = isset($_POST['tecnico']) ? trim($_POST['tecnico']) : '';
                    // $refaccion = isset($_POST['refaccion']) ? trim($_POST['refaccion']) : '';
                    $fecha_atencion = isset($_POST['fecha_atencion']) ? trim($_POST['fecha_atencion']) : '';
                    $acciones = isset($_POST['acciones']) ? trim($_POST['acciones']) : '';
                    $resultado = marcar_atendido($id_reporte,$observaciones,$tecnico, /*$refaccion,*/ $fecha_atencion, $acciones);
                    header('Location: ../reportes/reportes.php?tab=atendido&'.$resultado['estatus']. '='. urlencode($resultado['mensaje']));
                    exit;
                }
                break;

            case 'reabrir':
                if(isset($_POST['id_reporte'])){
                    $id_reporte = intval($_POST['id_reporte']);
                    $resultado = reabrir_reporte($id_reporte);
                    header('Location: ../reportes/reportes.php?tab=pendiente&'.$resultado['estatus'].'='.urlencode($resultado['mensaje']));
                    exit;
                }
                break;
            case 'editar_atendido':
                if(isset($_POST['id_reporte'],$_POST['fecha'],$_POST['descripcion'])){
                    $id_reporte = intval($_POST['id_reporte']);
                    $fecha = trim($_POST['fecha']);
                    $descripcion = trim($_POST['descripcion']);
                    $tecnico = trim($_POST['tecnico']);
                    // $refaccion = trim($_POST['refaccion']);
                    $fecha_atencion = trim($_POST['fecha_atencion']);
                    $observaciones_atencion = trim($_POST['observaciones_atencion']);
                    $acciones = trim($_POST['acciones']);
                    $id_cliente = !empty($_POST['id_cliente']) ? intval($_POST['id_cliente']) : null;
                    $id_equipo = !empty($_POST['id_equipo']) ? intval($_POST['id_equipo']) : null;
                    $resultado = editar_atendidos($id_reporte,$fecha,$descripcion,$tecnico,/*$refaccion,*/$fecha_atencion,$observaciones_atencion,$acciones,$id_cliente,$id_equipo);
                    header('Location: ../reportes/ver_reporte.php?id='.$id_reporte.'&'.$resultado['estatus'].'='.urlencode($resultado['mensaje']));
                    exit;
                }else{
                    header('Location: ../reportes/editar_reportes.php?error='.urlencode('Faltan campos'));
                    exit;
                }
            default:
            header('Location: ../reportes/reportes.php?error='.urlencode('Acción no valida'));
            exit;
        }
    }
}
?>