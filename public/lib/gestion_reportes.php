<?php
require_once __DIR__ . '/../../config/db.php';
function agregar_reportes_con_reportes($fecha,$tecnico,$id_cliente = null,$id_equipo =  null,$componentes=[]){
    global $conn;
    $sql="INSERT INTO reportes(fecha,tecnico,id_cliente,id_equipo,estado)
     VALUES (?,?,?,?,'pendiente')";
    $insert_preparado=mysqli_prepare($conn,$sql);
    if(!$insert_preparado){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la base de datos'
        ];
    }
    mysqli_stmt_bind_param($insert_preparado,'ssii',
    $fecha,$tecnico,$id_cliente,$id_equipo);
    $query_ok=mysqli_stmt_execute($insert_preparado);
    $id_reporte = mysqli_insert_id($conn);
    $rows_ok = mysqli_affected_rows($conn);
    mysqli_stmt_close($insert_preparado);

    if(!$query_ok && $rows_ok == 0) {
        return[
            'estatus' => 'error',
            'mensaje' => 'Error al generar reporte'
        ];
        
    }
    if(!empty($componentes) && is_array($componentes)){
        $sql_comp = "INSERT INTO reportes_componentes(id_reporte,componente,cantidad,descripcion,tipo)
                    VALUES (?,?,?,?,?)";
        $stmt_comp = mysqli_prepare($conn, $sql_comp);
        if(!$stmt_comp){
            return[
                'estatus' => 'error',
                'mensaje' => 'Error al preparar componentes'
            ];
        }
        foreach($componentes as $comp){
            $componente = trim($comp['nombre'] ?? $comp['componente'] ?? '');
            $tipo = trim($comp['tipo'] ?? '');
            $cantidad = isset($comp['cantidad']) ? intval($comp['cantidad']) : 1;
            $descripcion = trim($comp['descripcion'] ?? '');

            if(empty($componente) || $tipo == 'SER-01' || $tipo == 'SER-02'){
                if($tipo == 'SER-01'){
                    $componente = 'Servicio Preventivo';
                } elseif($tipo == 'SER-02'){
                    $componente = 'Servicio Correctivo';
                }elseif($tipo == 'SER-03'){
                    $componente == 'Entrega Refacción/Consumible';
                }
            }
            if(empty($tipo)){
                if(strpos($componente, 'Preventivo') !== false){
                    $tipo = 'SER-01';
                } elseif(strpos($componente, 'Correctivo') !== false){
                    $tipo = 'SER-02';
                } elseif(strpos($componente, 'Entrega Refacción/Consumible') !== false){
                    $tipo = 'SER-03';
                } else{
                    $tipo = 'componente';
                }
            }
            
            if(!empty($componente)){
                mysqli_stmt_bind_param($stmt_comp, 'isiss', $id_reporte, $componente, $cantidad, $descripcion, $tipo);
                $result_comp = mysqli_stmt_execute($stmt_comp);
                if(!$result_comp){
                    return[
                        'estatus' => 'error',
                        'mensaje' => 'Error al guardar componente: ' . mysqli_stmt_error($stmt_comp)
                    ];
                }
            }
        }
        mysqli_stmt_close($stmt_comp);
    }
    return[
        'estatus' => 'msg',
        'mensaje' => 'Reporte generado con éxito'
    ];
}
function editar_reporte_con_componentes($id_reporte,$fecha,$tecnico,$id_cliente = null,$id_equipo = null,$componentes = []){
    global $conn;
    $sql="UPDATE reportes SET
    fecha=?,tecnico = ?,id_cliente = ?,id_equipo =? WHERE id_reporte =?";
    $update_preparado=mysqli_prepare($conn,$sql);
    if(!$update_preparado){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }
    
    mysqli_stmt_bind_param($update_preparado,"ssiii",$fecha,$tecnico,$id_cliente,$id_equipo,$id_reporte);
    $query_ok=mysqli_stmt_execute($update_preparado);
    mysqli_stmt_close($update_preparado);
    if(!$query_ok){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error al actualizar el reporte'
        ];
    }
    $sql_delete = "DELETE FROM reportes_componentes WHERE id_reporte = ?";
    $stmt_delete = mysqli_prepare($conn,$sql_delete);
    mysqli_stmt_bind_param($stmt_delete, 'i', $id_reporte);
    mysqli_stmt_execute($stmt_delete);
    mysqli_stmt_close($stmt_delete);
    if(!empty($componentes) && is_array($componentes)){
        $sql_comp = "INSERT INTO reportes_componentes (id_reporte,componente,cantidad,descripcion,tipo)
                    VALUES (?,?,?,?,?)";
        $stmt_comp = mysqli_prepare($conn, $sql_comp);

        if($stmt_comp){
            foreach($componentes as $comp){
                    $componente = trim($comp['nombre'] ?? $comp['componente']) ?? '';
                    $tipo = trim($comp['tipo'] ?? '');
                    $cantidad = isset($comp['cantidad']) ? intval($comp['cantidad']) : 1;
                    $descripcion = trim($comp['descripcion']);

                    if(empty($componente)){
                        if($tipo == 'SER-01'){
                            $componente = 'Servicio Preventivo';
                        }elseif($tipo == 'SER-02'){
                            $componente = 'Servicio Correctivo';
                        }elseif($tipo == 'SER-03'){
                            $componente = 'Entrega Refacción/Consumible';
                        }
                    }if(empty($tipo)){
                        if(strpos($componente, 'Preventivo') !== false){
                            $tipo = 'SER-01';
                        } elseif(strpos($componente, 'Correctivo') !== false){
                            $tipo = 'SER-02';
                        } elseif(strpos($componente, 'Entrega Refacción/Consumible') !== false){
                            $tipo = 'SER-03';
                        } else{
                            $tipo = 'componente';
                        }
                    }
                    if(!empty($componente)){
                        mysqli_stmt_bind_param($stmt_comp,'isiss',$id_reporte,$componente,$cantidad,$descripcion,$tipo);
                        mysqli_stmt_execute($stmt_comp);
                    }
            }
            mysqli_stmt_close($stmt_comp);
        }
    }
    return[
        'estatus' => 'msg',
        'mensaje' => 'Reporte actualizado con éxito'
    ];

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
function marcar_atendido($id_reporte, $observaciones = '',$fecha_atencion = ''){
    global $conn;
    $sql = "UPDATE reportes SET estado='atendido', 
            observaciones_atencion=?, 
            fecha_atencion = ?WHERE id_reporte=?";
    $stmt = mysqli_prepare($conn,$sql);
    if(!$stmt){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la preparación'.mysqli_error($conn)
        ];
    }
    mysqli_stmt_bind_param($stmt, 'ssi', $observaciones, $fecha_atencion,$id_reporte);
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
    $sql = "UPDATE reportes SET estado = 'pendiente', fecha_atencion = NULL, observaciones_atencion = NULL, acciones = NULL WHERE id_reporte = ?";
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

function editar_atendidos($id_reporte,$fecha,$tecnico,$fecha_atencion,$observaciones_atencion,$id_cliente = null,$id_equipo = null,$componentes=[]){
    global $conn;
    $sql = "UPDATE reportes SET fecha=?,tecnico=?,fecha_atencion=?,observaciones_atencion = ?,
            id_cliente = ?, id_equipo = ? WHERE id_reporte = ?";
    $update_preparado = mysqli_prepare($conn,$sql);
    if(!$update_preparado){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }
    mysqli_stmt_bind_param($update_preparado,"ssssiii",$fecha,$tecnico,$fecha_atencion,$observaciones_atencion,$id_cliente,$id_equipo,$id_reporte);
    $query_ok = mysqli_stmt_execute($update_preparado);
    mysqli_stmt_close($update_preparado);
    if(!$query_ok){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error al actualizar el reporte'
        ];
    }
    $sql_delete = "DELETE FROM reportes_componentes WHERE id_reporte = ?";
    $stmt_delete = mysqli_prepare($conn, $sql_delete);
    mysqli_stmt_bind_param($stmt_delete, 'i', $id_reporte);
    mysqli_stmt_execute($stmt_delete);
    mysqli_stmt_close($stmt_delete);

    if(!empty($componentes) && is_array($componentes)){
        $sql_comp = "INSERT INTO reportes_componentes (id_reporte,componente,cantidad,descripcion,tipo)
                    VALUES (?,?,?,?,?)";
        $stmt_comp = mysqli_prepare($conn,$sql_comp);
        if($stmt_comp){
            foreach($componentes as $comp){
                    $componente = trim($comp['nombre'] ?? $comp['componente'] ?? '');
                    $tipo = trim($comp['tipo'] ?? '');
                    $cantidad = isset($comp['cantidad']) ? intval($comp['cantidad']) : 1;
                    $descripcion = trim($comp['descripcion']);

                    if(empty($componente)){
                        if($tipo == 'SER-01'){
                            $componente = 'Servicio Preventivo';
                        } elseif($tipo == 'SER-02'){
                            $componente = 'Servicio Correctivo';
                        } elseif($tipo == 'SER-03'){
                            $componente = 'Entrega Refacción/Consumible';
                        }
                    }
                    if(empty($tipo)){
                        if(strpos($componente, 'Preventivo') !== false){
                            $tipo = 'SER-01';
                        } elseif(strpos($componente, 'Correctivo') !== false){
                            $tipo = 'SER-02';
                        } elseif(strpos($componente, 'Entrega Refacción/Consumible') !== false){
                            $tipo = 'SER-03';
                        } else{
                            $tipo = 'componente';
                        }
                    }
                    if(!empty($componente)){
                        mysqli_stmt_bind_param($stmt_comp,'isiss',$id_reporte,$componente,$cantidad,$descripcion,$tipo);
                        mysqli_stmt_execute($stmt_comp);
                    }
            }
            mysqli_stmt_close($stmt_comp);
        }
    }
    return[
        'estatus' => 'msg',
        'mensaje' => 'Reporte editado exitosamente'
    ];
}
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['accion'])){
        $accion=$_POST['accion'];
        switch($accion){
            case 'agregar':
                if(isset($_POST['fecha'])){
                    $fecha=trim($_POST['fecha']);
                    $tecnico=trim($_POST['tecnico'] ?? '');
                    $id_cliente=!empty($_POST['id_cliente']) ? intval($_POST['id_cliente']) : null;
                    $id_equipo=!empty($_POST['id_equipo']) ? intval($_POST['id_equipo']): null;

                    $componentes = [];
                    if(isset($_POST['componentes']) && is_array($_POST['componentes'])){
                        foreach($_POST['componentes'] as $comp){
                            $nombre = trim($comp['nombre'] ?? $comp['componente'] ?? '');
                            $tipo = trim($comp['tipo'] ?? '');
                            $cantidad = isset($comp['cantidad']) ? intval($comp['cantidad']) : 1;
                            $descripcion = trim($comp['descripcion'] ?? '');

                            if(empty($nombre)){
                                if($tipo == 'SER-01'){
                                    $nombre = 'Servicio Preventivo';
                                } elseif($tipo == 'SER-02'){
                                    $nombre = 'Servicio Correctivo';
                                }elseif($tipo == 'SER-03'){
                                    $nombre = 'Entrega Refacción/Consumible';
                                }
                            }
                            
                            if(!empty($nombre)){
                                $componentes[] = [
                                    'nombre' => $nombre,
                                    'cantidad' => $cantidad,
                                    'descripcion' => $descripcion
                                ];
                            }

                        }
                    }

                    $resultado=agregar_reportes_con_reportes($fecha,$tecnico,$id_cliente,$id_equipo,$componentes);
                    if($resultado['estatus'] === 'msg'){
                        header('Location: ../reportes/agregar_reporte.php?msg='.urlencode($resultado['mensaje']));
                    }else{
                        header('Location: ../reportes/agregar_reporte.php?error='.urlencode(($resultado['mensaje'])));
                    }
                    exit;
                }
                break;
            case 'editar':
                if(isset($_POST['id_reporte'],$_POST['fecha'])){
                    $id_reporte=intval($_POST['id_reporte']);
                    $fecha=trim($_POST['fecha']);
                    $tecnico=trim($_POST['tecnico']);
                    $id_cliente=!empty($_POST['id_cliente']) ? intval($_POST['id_cliente']) : null;
                    $id_equipo=!empty($_POST['id_equipo']) ? intval($_POST['id_equipo']) : null;

                    $componentes = [];
                    if(isset($_POST['componentes']) && is_array(($_POST['componentes']))){
                        foreach($_POST['componentes'] as $comp){
                            $componente = trim($comp['nombre'] ?? $comp['componente']);
                            $tipo = trim($comp['tipo'] ?? '');
                            $cantidad = isset($comp['cantidad']) ? intval($comp['cantidad']) : 1;
                            $descripcion = trim($comp['descripcion'] ?? '');

                            if(empty($componente)){
                                if($tipo == 'SER-01'){
                                    $componente = 'Servicio Preventivo';
                                } elseif($tipo == 'SER-02'){
                                    $componente = 'Servicio Correctivo';
                                }elseif($tipo == 'SER-03'){
                                    $componente = 'Entrega Refacción/Consumible';
                                }
                            }
                            if(!empty($componente)){
                                $componentes[] = [
                                    'nombre' => $componente,
                                    'cantidad' => $cantidad,
                                    'descripcion' => $descripcion
                                ];
                            }
                        }
                    }
                    
                    $resultado=editar_reporte_con_componentes($id_reporte,$fecha,$tecnico,$id_cliente,$id_equipo,$componentes);
                    header('Location: ../reportes/ver_reporte.php?id='.$id_reporte.'&'.$resultado['estatus'].'='.urlencode($resultado['mensaje']));
                    if($resultado['estatus'] === 'msg'){
                        header('Location: ../reportes/ver_reporte.php?id='.$id_reporte.'&msg=' .urlencode($resultado['mensaje']));
                    }else{
                        header('Location: ../reportes/editar_reportes.php?id_reporte='.$id_reporte.'$error='.urlencode($resultado['mensaje']));
                    }
                    exit;
                }
                break;
            case 'eliminar':
                if(isset($_POST['id_reporte'])){
                    $id_reporte = intval($_POST['id_reporte']);

                    $sql_delete = "DELETE FROM reportes_componentes WHERE id_reporte = ?";
                    $stmt_delete = mysqli_prepare($conn,$sql_delete);
                    mysqli_stmt_bind_param($stmt_delete,'i',$id_reporte);
                    mysqli_stmt_execute($stmt_delete);
                    mysqli_stmt_close($stmt_delete);

                    $sql = "DELETE FROM reportes WHERE id_reporte = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt,'i',$id_reporte);
                    $query_ok = mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    if($query_ok){
                        header('Location: ../reportes/reportes.php?msg='.urlencode('Reporte eliminado correctamente'));
                    }else{
                        header('Location: ../reportes/reportes.php?error='.urlencode('Error al eliminar reporte'));
                    }
                    exit;
                }
                break;
            case 'eliminar2':
                if(isset($_POST['id_reporte'])){
                    $id_reporte = intval($_POST['id_reporte']);

                    $sql_delete = "DELETE FROM reportes_componentes WHERE id_reporte = ?";
                    $stmt_delete = mysqli_prepare($conn,$sql_delete);
                    mysqli_stmt_bind_param($stmt_delete,'i',$id_reporte);
                    mysqli_stmt_execute($stmt_delete);
                    mysqli_stmt_close($stmt_delete);

                    $sql = "DELETE FROM reportes WHERE id_reporte = ?";
                    $stmt = mysqli_prepare($conn, $sql);
                    mysqli_stmt_bind_param($stmt,'i',$id_reporte);
                    $query_ok = mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    if($query_ok){
                        header('Location: ../reportes/reportes.php?msg='.urlencode('Reporte eliminado correctamente'));
                    }else{
                        header('Location: ../reportes/reportes.php?error='.urlencode('Error al eliminar reporte'));
                    }
                    exit;
                }
                break;
            case 'marcar_atendido':
                if(isset($_POST['id_reporte'])){
                    $id_reporte=intval($_POST['id_reporte']);
                    $observaciones = isset($_POST['observaciones_atencion']) ? trim($_POST['observaciones_atencion']) : '';
                    $fecha_atencion = isset($_POST['fecha_atencion']) ? trim($_POST['fecha_atencion']) : '';
                    $acciones = isset($_POST['acciones']) ? trim($_POST['acciones']) : '';
                    $resultado = marcar_atendido($id_reporte,$observaciones,$fecha_atencion, $acciones);
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
                if(isset($_POST['id_reporte'],$_POST['fecha'])){
                    $id_reporte = intval($_POST['id_reporte']);
                    $fecha = trim($_POST['fecha']);
                    $tecnico = trim($_POST['tecnico']);
                    $fecha_atencion = trim($_POST['fecha_atencion']);
                    $observaciones_atencion = trim($_POST['observaciones_atencion']);
                    $id_cliente = !empty($_POST['id_cliente']) ? intval($_POST['id_cliente']) : null;
                    $id_equipo = !empty($_POST['id_equipo']) ? intval($_POST['id_equipo']) : null;
                    
                    $componentes = [];
                    if(isset($_POST['componentes']) && is_array(($_POST['componentes']))){
                        foreach($_POST['componentes'] as $comp){
                            $componente = trim($comp['nombre'] ?? $comp['componente'] ?? '');
                            $tipo = trim($comp['tipo'] ?? '');
                            $cantidad = isset($comp['cantidad']) ? intval($comp['cantidad']) : 1;
                            $descripcion = trim($comp['descripcion'] ?? '');

                            if(empty($componente)){
                                if($tipo == 'SER-01'){
                                    $componente = 'Servicio Preventivo';
                                } elseif($tipo == 'SER-02'){
                                    $componente = 'Servicio Correctivo';
                                }elseif($tipo == 'SER-03'){
                                    $componente = 'Entrega Refacción/Consumible';
                                }
                            }
                            if(!empty($componente)){
                                $componentes[] = [
                                    'nombre' => $componente,
                                    'cantidad' => $cantidad,
                                    'descripcion' => $descripcion
                                ];
                            }
                        }
                    }
                    $resultado = editar_atendidos($id_reporte,$fecha,$tecnico,$fecha_atencion,$observaciones_atencion,$id_cliente,$id_equipo,$componentes);
                    if($resultado['estatus'] === 'msg'){
                        header('Location: ../reportes/ver_reporte.php?id=' .$id_reporte.'&msg='.urlencode($resultado['mensaje']));
                    }else{
                        header('Location: ../reportes/editar_atendido.php?id_reporte=' .$id_reporte. '&error=' .urlencode($resultado['mensaje']));
                    }
                    exit;
                }
                break;
            default:
            header('Location: ../reportes/reportes.php?error='.urlencode('Acción no valida'));
            exit;
        }
    }
}
?>