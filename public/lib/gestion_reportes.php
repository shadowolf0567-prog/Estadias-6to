<?php
require_once __DIR__ . '/../../config/db.php';
function agregar_reportes_con_reportes($fecha,$referencia,$id_cliente = null,$id_equipo =  null,$componentes=[],$tecnico = null){
    global $conn;
    $sql="INSERT INTO reportes(fecha,referencia,id_cliente,id_equipo,estado,id)
     VALUES (?,?,?,?,'pendiente',?)";
    $insert_preparado=mysqli_prepare($conn,$sql);
    if(!$insert_preparado){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la base de datos'
        ];
    }
    mysqli_stmt_bind_param($insert_preparado,'ssiii',
    $fecha,$referencia,$id_cliente,$id_equipo,$tecnico);
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
        $sql_comp = "INSERT INTO reportes_componentes(id_reporte,componente,descripcion,tipo)
                    VALUES (?,?,?,?)";
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
            $descripcion = trim($comp['descripcion'] ?? '');

            if(empty($componente) || $tipo == 'SER-01' || $tipo == 'SER-02'){
                if($tipo == 'SER-01'){
                    $componente = 'Servicio Preventivo';
                } elseif($tipo == 'SER-02'){
                    $componente = 'Servicio Correctivo';
                }elseif($tipo == 'SER-03'){
                    $componente = 'Reparación';
                }
            }
            if(empty($tipo)){
                if(strpos($componente, 'Preventivo') !== false){
                    $tipo = 'SER-01';
                } elseif(strpos($componente, 'Correctivo') !== false){
                    $tipo = 'SER-02';
                } elseif(strpos($componente, 'Reparación') !== false){
                    $tipo = 'SER-03';
                } else{
                    $tipo = 'componente';
                }
            }
            
            if(!empty($componente)){
                mysqli_stmt_bind_param($stmt_comp, 'isss', $id_reporte, $componente, $descripcion, $tipo);
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
function editar_reporte_con_componentes($id_reporte,$referencia,$fecha,$id_cliente = null,$id_equipo = null,$tecnico=null,$componentes = []){
    global $conn;
    $sql="UPDATE reportes SET
    referencia = ?,fecha=?,id_cliente = ?,id_equipo =?,id=? WHERE id_reporte =?";
    $update_preparado=mysqli_prepare($conn,$sql);
    if(!$update_preparado){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }
    
    mysqli_stmt_bind_param($update_preparado,"sssiii",$referencia,$fecha,$id_cliente,$id_equipo,$tecnico,$id_reporte);
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
        $sql_comp = "INSERT INTO reportes_componentes (id_reporte,componente,descripcion,tipo)
                    VALUES (?,?,?,?)";
        $stmt_comp = mysqli_prepare($conn, $sql_comp);

        if($stmt_comp){
            foreach($componentes as $comp){
                    $componente = trim($comp['nombre'] ?? $comp['componente']) ?? '';
                    $tipo = trim($comp['tipo'] ?? '');
                    $descripcion = trim($comp['descripcion']);

                    if(empty($componente)){
                        if($tipo == 'SER-01'){
                            $componente = 'Servicio Preventivo';
                        }elseif($tipo == 'SER-02'){
                            $componente = 'Servicio Correctivo';
                        }elseif($tipo == 'SER-03'){
                            $componente = 'Reparación';
                        }
                    }if(empty($tipo)){
                        if(strpos($componente, 'Preventivo') !== false){
                            $tipo = 'SER-01';
                        } elseif(strpos($componente, 'Correctivo') !== false){
                            $tipo = 'SER-02';
                        } elseif(strpos($componente, 'Reparación') !== false){
                            $tipo = 'SER-03';
                        } else{
                            $tipo = 'componente';
                        }
                    }
                    if(!empty($componente)){
                        mysqli_stmt_bind_param($stmt_comp,'isss',$id_reporte,$componente,$descripcion,$tipo);
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
     if(empty($fecha_atencion) || $fecha_atencion == '0000-00-00'){
        $fecha_atencion = null;
    }
    $sql = "UPDATE reportes SET estado='atendido', 
            acciones=?, 
            fecha_atencion = ? WHERE id_reporte=?";
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
function reabrir_reporte($id_reporte, $fecha = null, $acciones= '', $observaciones = ''){
    global $conn;
    if(empty($fecha)){
        $fecha = date('Y-m-d');
    }
    $estado = "SELECT estado FROM reportes WHERE id_reporte = ?";
    $stmt_check = mysqli_prepare($conn,$estado);
    mysqli_stmt_bind_param($stmt_check, 'i', $id_reporte);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    $reporte = mysqli_fetch_assoc($result_check);
    mysqli_stmt_close($stmt_check);
    if(!$reporte){
        return[
            'estatus' => 'error',
            'mensaje' => 'Reporte'.$id_reporte.' no encontrado'
        ];
    }
    if($reporte['estado'] != 'incompleto'){
        $sql = "UPDATE reportes SET estado = 'incompleto' WHERE id_reporte = ?";
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
        if(!$query_ok || $rows_ok == 0){
            return[
                'estatus' => 'error',
                'mensaje' => 'No se pudo actualizar el reporte #' . $id_reporte . ' (filas afectadas: ' . $rows_ok . ')'
            ];
        }
    }
        
    if(!empty($acciones) || !empty($observaciones)){
        $sql_rea = "INSERT INTO reabiertos(reportes,fechas,acciones,observaciones)
                    VALUES (?,?,?,?)";
        $stmt_rea = mysqli_prepare($conn,$sql_rea);
        if(!$stmt_rea){
            return[
                'estatus' => 'error',
                'mensaje' => 'Error al preparar reporte'
            ];
        }
        if($stmt_rea){
            mysqli_stmt_bind_param($stmt_rea, 'isss', $id_reporte,$fecha,$acciones,$observaciones);
            $result_rea = mysqli_stmt_execute($stmt_rea);
            mysqli_stmt_close($stmt_rea);
            if(!$result_rea){
                return[
                    'estatus' => 'error',
                    'mensaje' => 'Error al guardar reporte: '.mysqli_stmt_error($stmt_rea)
                ];
            }
        }
    }
    return[
        'estatus' => 'msg',
        'mensaje' => 'Reporte reabierto con éxito'
    ];
    
}

function editar_atendidos($id_reporte,$fecha,$referencia,$fecha_atencion,$acciones,$observaciones_atencion,$id_cliente = null,$id_equipo = null,$tecnico = null,$componentes=[]){
    global $conn;
    if(empty($fecha_atencion) || $fecha_atencion == '0000-00-00'){
        $fecha_atencion = null;
    }
    $sql = "UPDATE reportes SET fecha=?,referencia=?,fecha_atencion=?,acciones=?,observaciones_atencion = ?,
            id_cliente = ?, id_equipo = ?, id = ? WHERE id_reporte = ?";
    $update_preparado = mysqli_prepare($conn,$sql);
    if(!$update_preparado){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }
    mysqli_stmt_bind_param($update_preparado,"sssssiiii",$fecha,$referencia,$fecha_atencion,$acciones,$observaciones_atencion,$id_cliente,$id_equipo,$tecnico,$id_reporte);
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
        $sql_comp = "INSERT INTO reportes_componentes (id_reporte,componente,descripcion,tipo)
                    VALUES (?,?,?,?)";
        $stmt_comp = mysqli_prepare($conn,$sql_comp);
        if($stmt_comp){
            foreach($componentes as $comp){
                    $componente = trim($comp['nombre'] ?? $comp['componente'] ?? '');
                    $tipo = trim($comp['tipo'] ?? '');
                    $descripcion = trim($comp['descripcion']);

                    if(empty($componente)){
                        if($tipo == 'SER-01'){
                            $componente = 'Servicio Preventivo';
                        } elseif($tipo == 'SER-02'){
                            $componente = 'Servicio Correctivo';
                        } elseif($tipo == 'SER-03'){
                            $componente = 'Reparación';
                        }
                    }
                    if(empty($tipo)){
                        if(strpos($componente, 'Preventivo') !== false){
                            $tipo = 'SER-01';
                        } elseif(strpos($componente, 'Correctivo') !== false){
                            $tipo = 'SER-02';
                        } elseif(strpos($componente, 'Reparación') !== false){
                            $tipo = 'SER-03';
                        } else{
                            $tipo = 'componente';
                        }
                    }
                    if(!empty($componente)){
                        mysqli_stmt_bind_param($stmt_comp,'isss',$id_reporte,$componente,$descripcion,$tipo);
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
function agregar_en_masa($reportes,$id_cliente = null){
    global $conn;
    if(!$conn){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error de conexión a la base de datos'
        ];
    }
    $reportes_registrados = 0;
    $errores =[];
    foreach($reportes as $reporte){
        if(empty($reporte['fecha']) || empty($reporte['id_equipo'])){
            $errores[] = 'Fecha o equipo vacío en uno de los reportes';
            continue;
        }
        $fecha = trim($reporte['fecha']);
        $tecnico = trim($reporte['tecnico'] ?? '');
        $id_equipo = intval($reporte['id_equipo']);
        $servicio = trim($reporte['servicio'] ?? 'SER-01');
        $referencia = trim($reporte['referencia']) ?? '';

        if($servicio == 'SER-01'){
            $nombre_servicio = 'Servicio Preventivo';
        }else{
            $nombre_servicio = 'Servicio Correctivo';
        }

        $sql="INSERT INTO reportes(fecha,tecnico,referencia,id_equipo,id_cliente,estado)
                VALUES (?,?,?,?,?,'pendiente')";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt,'sssii',$fecha,$tecnico,$referencia,$id_equipo,$id_cliente);
        if(!$stmt){
            $errores[] = 'Error en la preparación';
            continue;
        }
        if(mysqli_stmt_execute($stmt)){
            $id_reporte = mysqli_insert_id($conn);
            $sql_comp = "INSERT INTO reportes_componentes(id_reporte, componente,cantidad,tipo,descripcion)
                            VALUES (?,?,1,?,' ')";
            $stmt_comp = mysqli_prepare($conn,$sql_comp);
            mysqli_stmt_bind_param($stmt_comp,'iss',$id_reporte,$nombre_servicio,$servicio);
            mysqli_stmt_execute($stmt_comp);
            mysqli_stmt_close($stmt_comp);
            $reportes_registrados++;
        }else{
            $errores[] = 'Error al insertar reporte para equipo ID ' . $id_equipo;
        }
        mysqli_stmt_close($stmt);
    }
    if($reportes_registrados > 0){
        $mensaje = $reportes_registrados . ' reporte(s) registrado(s) correctamente';
        if(!empty($errores)){
            $mensaje .= '. Errores: ' . implode(', ', $errores);
        }
        return[
            'estatus' => 'msg',
            'mensaje' => $mensaje,
            'registrados' => $reportes_registrados,
            'errores' => $errores
        ];
    }else{
        return[
            'estatus' => 'error',
            'mensaje' => 'No se pudo registrar nungún reporte'
        ];
    }
}
function editar_reabiertos($id,$fechas,$acciones,$observaciones){
    global $conn;
    $sql = "UPDATE reabiertos SET fechas = ?,acciones = ?, observaciones = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    if(!$stmt){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }
    mysqli_stmt_bind_param($stmt,'sssi',$fechas,$acciones,$observaciones,$id);
    $query_ok = mysqli_stmt_execute($stmt);
    if(!$query_ok){
        $error = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        return[
            'estatus' => 'error',
            'mensaje' => 'Error al actualizar: '.$error
        ];
    }
    $rows_ok = mysqli_affected_rows($conn);
    mysqli_stmt_close($stmt);
    if($rows_ok > 0){
        return[
            'estatus' => 'msg',
            'mensaje' => 'Equipo actualizado correctamente'
        ];
    }else{
        return[
            'estatus' => 'info',
            'mensaje' => 'No se realizaron cambios'
        ];
    }
}
function eliminar_reabierto($id){
    global $conn;
    $sql = "DELETE FROM reabiertos WHERE id = ?";
    $stmt = mysqli_prepare($conn,$sql);
    if(!$stmt){
        return[
            'estatus' => 'msg',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }
    mysqli_stmt_bind_param($stmt,'i',$id);
    $query_ok = mysqli_stmt_execute($stmt);
    $rows_ok=mysqli_affected_rows($conn);
    mysqli_stmt_close($stmt);
    if($query_ok && $rows_ok > 0){
        return[
            'estatus' => 'msg',
            'mensaje' => 'Eliminado correctamente'
        ];
    }else{
        return[
            'estatus' => 'error',
            'mensaje' => 'No se pudo eliminar'
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
                    $referencia = trim($_POST['referencia']);
                    $id_cliente=!empty($_POST['id_cliente']) ? intval($_POST['id_cliente']) : null;
                    $id_equipo=!empty($_POST['id_equipo']) ? intval($_POST['id_equipo']): null;
                    $tecnico = !empty($_POST['id']) ? intval($_POST['id']): null;

                    $componentes = [];
                    if(isset($_POST['componentes']) && is_array($_POST['componentes'])){
                        foreach($_POST['componentes'] as $comp){
                            $nombre = trim($comp['nombre'] ?? $comp['componente'] ?? '');
                            $tipo = trim($comp['tipo'] ?? '');
                            $descripcion = trim($comp['descripcion'] ?? '');

                            if(empty($nombre)){
                                if($tipo == 'SER-01'){
                                    $nombre = 'Servicio Preventivo';
                                } elseif($tipo == 'SER-02'){
                                    $nombre = 'Servicio Correctivo';
                                }elseif($tipo == 'SER-03'){
                                    $nombre = 'Reparación';
                                }
                            }
                            
                            if(!empty($nombre)){
                                $componentes[] = [
                                    'nombre' => $nombre,
                                    'descripcion' => $descripcion
                                ];
                            }

                        }
                    }

                    $resultado=agregar_reportes_con_reportes($fecha,$referencia,$id_cliente,$id_equipo,$componentes,$tecnico);
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
                    $referencia = trim($_POST['referencia']);
                    $fecha=trim($_POST['fecha']);
                    $id_cliente=!empty($_POST['id_cliente']) ? intval($_POST['id_cliente']) : null;
                    $id_equipo=!empty($_POST['id_equipo']) ? intval($_POST['id_equipo']) : null;
                    $tecnico=trim($_POST['id']) ? intval($_POST['id']) : null;

                    $componentes = [];
                    if(isset($_POST['componentes']) && is_array(($_POST['componentes']))){
                        foreach($_POST['componentes'] as $comp){
                            $componente = trim($comp['nombre'] ?? $comp['componente']);
                            $tipo = trim($comp['tipo'] ?? '');
                            $descripcion = trim($comp['descripcion'] ?? '');

                            if(empty($componente)){
                                if($tipo == 'SER-01'){
                                    $componente = 'Servicio Preventivo';
                                } elseif($tipo == 'SER-02'){
                                    $componente = 'Servicio Correctivo';
                                }elseif($tipo == 'SER-03'){
                                    $componente = 'Reparación';
                                }
                            }
                            if(!empty($componente)){
                                $componentes[] = [
                                    'nombre' => $componente,
                                    'descripcion' => $descripcion
                                ];
                            }
                        }
                    }
                    
                    $resultado=editar_reporte_con_componentes($id_reporte,$referencia,$fecha,$id_cliente,$id_equipo,$tecnico,$componentes);
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
                    $observaciones = isset($_POST['acciones']) ? trim($_POST['acciones']) : '';
                    $fecha_atencion = isset($_POST['fecha_atencion']) ? trim($_POST['fecha_atencion']) : '';
                    if(empty($fecha_atencion)){
                        $fecha_atencion = null;
                    }
                    $resultado = marcar_atendido($id_reporte,$observaciones,$fecha_atencion);
                    header('Location: ../reportes/ver_reporte.php?id='.$id_reporte.'&'.$resultado['estatus']. '='. urlencode($resultado['mensaje']));
                    exit;
                }
                break;

            case 'reabrir':
                if(isset($_POST['id_reporte'])){
                    $id_reporte = intval($_POST['id_reporte']);
                    $fecha = trim($_POST['fechas']);
                    $acciones = trim($_POST['acciones'] ?? '');
                    $observaciones = trim($_POST['observaciones']);
                    $resultado = reabrir_reporte($id_reporte,$fecha,$acciones,$observaciones);
                    header('Location: ../reportes/ver_reporte.php?id='.$id_reporte.$resultado['estatus'].'='.urlencode($resultado['mensaje']));
                    exit;
                }
                break;
            case 'editar_atendido':
                if(isset($_POST['id_reporte'],$_POST['fecha'])){
                    $id_reporte = intval($_POST['id_reporte']);
                    $fecha = trim($_POST['fecha']);
                    $referencia = trim($_POST['referencia']);
                    $fecha_atencion = trim($_POST['fecha_atencion']);
                    if(empty($fecha_atencion)){
                        $fecha_atencion = null;
                    }
                    $acciones = trim($_POST['acciones']);
                    $observaciones_atencion = trim($_POST['observaciones_atencion']);
                    $id_cliente = !empty($_POST['id_cliente']) ? intval($_POST['id_cliente']) : null;
                    $id_equipo = !empty($_POST['id_equipo']) ? intval($_POST['id_equipo']) : null;
                    $tecnico = !empty($_POST['id']) ? intval($_POST['id']) : null;
                    
                    $componentes = [];
                    if(isset($_POST['componentes']) && is_array(($_POST['componentes']))){
                        foreach($_POST['componentes'] as $comp){
                            $componente = trim($comp['nombre'] ?? $comp['componente'] ?? '');
                            $tipo = trim($comp['tipo'] ?? '');
                            $descripcion = trim($comp['descripcion'] ?? '');

                            if(empty($componente)){
                                if($tipo == 'SER-01'){
                                    $componente = 'Servicio Preventivo';
                                } elseif($tipo == 'SER-02'){
                                    $componente = 'Servicio Correctivo';
                                }elseif($tipo == 'SER-03'){
                                    $componente = 'Reparación';
                                }
                            }
                            if(!empty($componente)){
                                $componentes[] = [
                                    'nombre' => $componente,
                                    'descripcion' => $descripcion
                                ];
                            }
                        }
                    }
                    $resultado = editar_atendidos($id_reporte,$fecha,$referencia,$fecha_atencion,$acciones,$observaciones_atencion,$id_cliente,$id_equipo,$tecnico,$componentes);
                    if($resultado['estatus'] === 'msg'){
                        header('Location: ../reportes/ver_reporte.php?id=' .$id_reporte.'&msg='.urlencode($resultado['mensaje']));
                    }else{
                        header('Location: ../reportes/editar_atendido.php?id_reporte=' .$id_reporte. '&error=' .urlencode($resultado['mensaje']));
                    }
                    exit;
                }
                break;
            case 'agregar_muchos':
                if(isset($_POST['equipos']) && is_array($_POST['equipos']) && !empty($_POST['equipos'])){
                    $id_cliente = null;
                    if(isset($_POST['id_cliente']) && !empty($_POST['id_cliente']) && $_POST['id_cliente'] > 0){
                        $id_cliente = intval($_POST['id_cliente']);
                    }
                    $fecha  = trim($_POST['fecha'] ?? date('Y-m-d'));
                    $tecnico = trim($_POST['tecnico'] ?? '');
                    $servicio = trim($_POST['servicio'] ?? 'SER-01');
                    $referencia = '';
                    if(isset($_POST['referencia']) && is_array($_POST['referencia'])){
                        foreach($_POST['referencia'] as $ref_array){
                            if(!empty($ref_array['referencia'])){
                                $referencia = trim($ref_array['referencia']);
                                break;
                            }
                        }
                    } else if(isset($_POST['referencia']) && is_string($_POST['referencia'])) {
                        $referencia = trim($_POST['referencia']);
                    }

                    $reportes = [];
                    foreach($_POST['equipos'] as $equipo){
                        if(!empty($equipo['id_equipo'])){
                            $reportes[] = [
                                'fecha' => $fecha,
                                'tecnico' => $tecnico,
                                'id_equipo' => intval($equipo['id_equipo']),
                                'servicio' => $servicio,
                                'referencia' => $referencia
                            ];
                        }
                    }
                    if(empty($reportes)){
                        header('Location: ../reportes/reportar_muchos.php?error='.urlencode('Debe seleccionar al menos un equipo'));
                        exit;
                    }
                    $resultado = agregar_en_masa($reportes,$id_cliente);
                    if($resultado['estatus'] === 'msg'){
                        header('Location: ../reportes/reportar_muchos.php?msg=' .urlencode($resultado['mensaje']));
                    }else{
                        header('Location: ../reportes/reportar_muchos.php?error='.urlencode($resultado['mensaje']));
                    }
                    exit;
                }else{
                    header('Location: ../reportes/reportar_muchos.php?error='.urlencode('No se recibieron equipos para reportar'));
                    exit;
                }
                break;
            case 'editar_reabierto':
                if(isset($_POST['id'],$_POST['fechas'])){
                    $id_reporte = intval($_POST['id_reporte']);
                    $id = intval($_POST['id']);
                    $fechas = trim($_POST['fechas']);
                    $acciones = trim($_POST['acciones']);
                    $observaciones = trim($_POST['observaciones']);
                    $resultado = editar_reabiertos($id,$fechas,$acciones,$observaciones);
                    if($resultado['estatus'] === 'msg' || $resultado['estatus'] === 'info'){
                        header('Location: ../reportes/ver_reporte.php?id='.$id_reporte.'&msg'.urlencode($resultado['mensaje']));
                    }else{
                        header('Location: ../reportes/ver_reporte.php?id='.$id_reporte.'&error'.urlencode($resultado['mensaje']));
                    }
                    exit;
                }
                break;
            case 'borrar':
                if(isset($_POST['id']) && isset($_POST['id_reporte'])){
                    $id_reporte = intval($_POST['id_reporte']);
                    $id = intval($_POST['id']);
                    $resultado = eliminar_reabierto($id);
                    if($resultado['estatus'] === 'msg'){
                        header('Location: ../reportes/ver_reporte.php?id='.$id_reporte.'&msg='.urlencode($resultado['mensaje']));
                    }else{
                        header('Location: ../reportes/ver_reporte.php?id='.$id_reporte.'&msg='.urlencode($resultado['mensaje']));
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