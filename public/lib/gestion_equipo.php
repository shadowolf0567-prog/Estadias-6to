<?php
require_once __DIR__ . '/../../config/db.php';
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    header('Location: ../gestion/equipos.php');
    exit;
}
function mostrar_equipos(){
    global $conn;
    if(!$conn){
        return[];
    }
    $sql="SELECT * FROM equipos";
    $select_preparado=mysqli_prepare($conn,$sql);
    mysqli_stmt_execute($select_preparado);
    $resultado=mysqli_stmt_get_result($select_preparado);
    $equipos=array();
    while($fila_bd=mysqli_fetch_assoc($resultado)){
        $equipos[]=$fila_bd;
    }
    mysqli_stmt_close($select_preparado);
    return $equipos;
}
function agregar_equipo($no_serie,$modelo,$accesorios,$inicio_contrato,$fin_contrato){
    global $conn;
    if(!$conn){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error de conexión a la base de datos'
        ];
    }
    $sql='INSERT INTO equipos (no_serie,modelo,accesorios,inicio_contrato,fin_contrato) VALUES (?,?,?,?,?)';
    $insert_preparado=mysqli_prepare($conn,$sql);
    if(!$insert_preparado){
        return[
            'estatus'=>'error',
            'mensaje'=>'Error en la preparación:'.mysqli_error($conn)
            ];
    }
    mysqli_stmt_bind_param($insert_preparado,'sssss',$no_serie,$modelo,$accesorios,$inicio_contrato,$fin_contrato);
    $query_ok=mysqli_stmt_execute($insert_preparado);
    if(!$query_ok){
        $error=mysqli_stmt_error($insert_preparado);
        mysqli_stmt_close($insert_preparado);
        return[
            'estatus'=>'error',
            'mensaje'=>'Error al insertar: '.$error
        ];
    }
    $rows_ok = mysqli_affected_rows($conn);
    mysqli_stmt_close($insert_preparado);
    if($query_ok && $rows_ok > 0){
        return[
            'estatus' => 'msg',
            'mensaje' => 'Equipo registrado correctamente'
        ];
    }else{
        return[
            'estatus' => 'error',
            'mensaje' => 'Error al ingresar el equipo'
        ];
    }
}
function agregar_cliente_completo($nombre,$no_cuenta,$direccion,$inicio_contrato,$fin_contrato,$telefonos = [],$correos=[]){
    global $conn;
    if(!$conn){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error de conexión a la base de datos'
        ];
    }
    $sql = "INSERT INTO clientes(nombre,no_cuenta,direccion,inicio_contrato,fin_contrato)
            VALUES (?,?,?,?,?)";
    $stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt,'sssss',$nombre,$no_cuenta,$direccion,$inicio_contrato,$fin_contrato);
    if(!mysqli_stmt_execute($stmt)){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error al insertar cliente'
        ];
    }
    $id_cliente=mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
    if(!empty($telefono)){
        $sql_telefono = "INSERT INTO clientes_telefonos(id_cliente,telefono) VALUES (?,?)";
        $stmt_telefono=mysqli_prepare($conn,$sql_telefono);

        foreach($telefono as $telefono){
            mysqli_stmt_bind_param($stmt_telefono,'is',$id_cliente,$telefono['numero']);
            mysqli_stmt_execute($stmt_telefono);
        }
        mysqli_stmt_close($stmt_telefono);
    }
    if(!empty($correos)){
        $sql_correo = "INSERT INTO clientes_correo (id_cliente,correo) VALUES (?,?)";
        $stmt_correo = mysqli_prepare($conn,$sql_correo);
        foreach($correos as $correo){
            mysqli_stmt_bind_param($stmt_correo,'is',$id_cliente,$correo['direccion']);
        }
        mysqli_stmt_close($stmt_correo);
    }
    return[
        'estatus' => 'msg',
        'mensaje' => 'Cliente agregado correctamente',
        'id_cliente' => $id_cliente
    ];
}
function editar_equipo($id_equipo,$no_serie,$modelo,$inicio_contrato,$fin_contrato,$id_cliente = null){
    global $conn;
    if(!$conn){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error de conexión a la base de datos'
        ];
    }
    if(empty($id_cliente)){
        $sql = "UPDATE equipos SET no_serie = ?,modelo=?, inicio_contrato=?, fin_contrato=?, id_cliente=NULL WHERE id_equipo=?";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, 'ssssi',$no_serie,$modelo,$inicio_contrato,$fin_contrato,$id_equipo);
    }else{
        $sql = "UPDATE equipos SET no_serie=?,modelo=?,inicio_contrato=?,fin_contrato=?,id_cliente=? WHERE id_equipo = ?";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt,'ssssii',$no_serie, $modelo,$inicio_contrato,$fin_contrato, $id_cliente, $id_equipo);
    }
    if(!$stmt){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la preparación'
        ];
    }
    $query_ok=mysqli_stmt_execute($stmt);

    if(!$query_ok){
        $error=mysqli_stmt_error($stmt);
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
function eliminar_equipo($id_equipo){
    global $conn;
    $sql="DELETE FROM equipos WHERE id_equipo=?";
    $delete_preparado=mysqli_prepare($conn,$sql);
    if(!$delete_preparado){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }
    mysqli_stmt_bind_param($delete_preparado,'i',$id_equipo);
    $query_ok=mysqli_stmt_execute($delete_preparado);
    $rows_ok=mysqli_affected_rows($conn);
    mysqli_stmt_close($delete_preparado);
    if($query_ok && $rows_ok > 0){
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
function agregar_equipo_con_cliente($no_serie,$modelo,$accesorios, $inicio_contrato, $fin_contrato,$id_cliente = null){
    global $conn;
    if(!$conn){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error de conexión a la base de datos'
        ];
    }
    if(empty($id_cliente) || $id_cliente == 0){
        $sql='INSERT INTO equipos (no_serie,modelo,accesorios,inicio_contrato,fin_contrato) VALUES(?,?,?,?,?)';
        $stmt=mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, 'sssss', $no_serie,$modelo,$accesorios,$inicio_contrato,$fin_contrato);
    }else{
        $sql = 'INSERT INTO equipos(no_serie,modelo,accesorios,inicio_contrato,fin_contrato,id_cliente) VALUES(?,?,?,?,?,?)';
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, 'sssssi', $no_serie,$modelo,$accesorios,$inicio_contrato,$fin_contrato,$id_cliente);
        $mensaje = 'Equipo agregado correctamente';
    }
    if(!$stmt){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la preparación: '
        ];
    }
    $query_ok=mysqli_stmt_execute($stmt);
    if(!$query_ok){
        $error=mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        return[
            'estatus' => 'error',
            'mensaje' => 'Error al insertar: '.$error
        ];
    }
    $rows_ok=mysqli_affected_rows($conn);
    mysqli_stmt_close($stmt);
    if($rows_ok>0){
        $mensaje=empty($id_cliente) || $id_cliente == 0 ?
        'Producto agregado correctamente':
        'Producto agregado y vinculado al cliente correctamente';

        return[
            'estatus' => 'msg',
            'mensaje' => $mensaje
        ];
    }else{
        return[
            'estatus' => 'error',
            'mensaje' => 'No se pudo insertar el producto'
        ];
    }
}
function editar_cliente_completo($id_cliente,$nombre,$no_cuenta,$direccion,$inicio_contrato,$fin_contrato,$telefonos=[],$correos=[]){
    global $conn;
    if(!$conn){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error de conexión a la base de datos'
        ];
    }
    $sql = "UPDATE clientes SET nombres = ?,no_cuenta=?,direcciones=?,inicio_contrato =?,fin_contrato = ? WHERE id_cliente = ?";
    $stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt,'sssssi',$nombre,$no_cuenta,$direccion,$inicio_contrato,$fin_contrato,$id_cliente);

    if(!mysqli_stmt_execute($stmt)){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error al actualizar cliente'
        ];
    }
    mysqli_stmt_close($stmt);
}
function obtener_cliente_completo($id_cliente){
    global $conn;
    $sql = "SELECT * FROM clientes WHERE id_cliente = ?";
    $stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt,'i',$id_cliente);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $cliente = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if(!$cliente) return null;

    $sql_telefono="SELECT id,telefono,correo WHERE id_cliente = ?";
    $stmt_telefonos = mysqli_prepare($conn,$sql_telefono);
    mysqli_stmt_bind_param($stmt_telefonos,'i',$id_cliente);
    mysqli_stmt_execute($stmt_telefonos);
    $result_telefono=mysqli_stmt_get_result($stmt_telefonos);
    $telefonos = [];
    while($row = mysqli_fetch_assoc($result_telefono)){
        $telefonos[] = $row;
    }
    mysqli_stmt_close($stmt_telefonos);
    return $cliente;
}
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(isset($_POST['accion'])){
        $accion = $_POST['accion'];
        switch($accion){
            case 'agregar':
                if(isset($_POST['no_serie'],$_POST['modelo'],$_POST['accesorios'])){
                    $no_serie=trim($_POST['no_serie']);
                    $modelo=trim($_POST['modelo']);
                    $accesorios=trim($_POST['accesorios']);
                    $inicio_contrato=trim($_POST['inicio_contrato']);
                    $fin_contrato=trim($_POST['fin_contrato']);

                    $resultado=agregar_equipo($no_serie,$modelo,$accesorios,$inicio_contrato,$fin_contrato);
                    header('Location: ../gestion/agregar_equipo.php?'.$resultado['estatus'].'='.urlencode($resultado['mensaje']));
                    exit;
                }
                break;
                case 'editar':
                    if(isset($_POST['id_equipo'],$_POST['no_serie'],$_POST['modelo'],$_POST['inicio_contrato'],$_POST['fin_contrato'])){
                        $id_equipo=intval($_POST['id_equipo']);
                        $no_serie=trim($_POST['no_serie']);
                        $modelo=trim($_POST['modelo']);
                        $inicio_contrato=trim($_POST['inicio_contrato']);
                        $fin_contrato=trim($_POST['fin_contrato']);
                        $id_cliente=isset($_POST['id_cliente']) && !empty($_POST['id_cliente']) ? intval($_POST['id_cliente']) : null;

                        if(empty($no_serie)){
                            header('Location: ../gestion/editar_equipo.php?id_equipo='.$id_equipo.'&error='.urlencode('El número de serie es obligatorio'));
                            exit;
                        }

                        $resultado=editar_equipo($id_equipo,$no_serie,$modelo,$inicio_contrato,$fin_contrato,$id_cliente);
                        if($resultado['estatus'] === 'msg' || $resultado['estatus'] === 'info'){
                            header('Location: ../gestion/editar_equipo.php?id_equipo='.$id_equipo.'&msg='.urlencode($resultado['mensaje']));
                        }else{
                            header('Location: ../gestion/editar_equipo.php?id_equipo='.$id_equipo.'&error='.urlencode($resultado['mensaje']));
                            exit;
                        }
                    }
                    break;
                case 'eliminar':
                    if(isset($_POST['id_equipo'])){
                        $id_equipo = intval($_POST['id_equipo']);
                        $resultado = eliminar_equipo($id_equipo);
                        header('Location: ../gestion/equipos.php?'.$resultado['estatus'].'='.urlencode($resultado['mensaje']));
                        exit;
                    }
                    break;
                case 'agregar_con_cliente':
                    if(isset($_POST['no_serie'],$_POST['modelo'],$_POST['accesorios'],$_POST['inicio_contrato'],$_POST['fin_contrato'])){
                        $no_serie=trim($_POST['no_serie']);
                        $modelo=trim($_POST['modelo']);
                        $accesorios=trim($_POST['accesorios']);
                        $inicio_contrato=trim($_POST['inicio_contrato']);
                        $fin_contrato=trim($_POST['fin_contrato']);
                        $modo_cliente=$_POST['modo_cliente'] ?? 'existente';

                        $id_cliente=null;
                        if(isset($_POST['id_cliente']) && !empty($_POST['id_cliente']) && $_POST['id_cliente'] > 0){
                            $id_cliente = intval($_POST['id_cliente']);
                        }
                        if(empty($no_serie)){
                            header('Location: ../gestion/agregar_equipo.php?error='.urlencode('El número de serie es obligatorio'));
                            exit;
                        }
                        $resultado = agregar_equipo_con_cliente($no_serie,$modelo,$accesorios,$inicio_contrato,$fin_contrato,$id_cliente);
                        if($resultado['estatus']==='msg'){
                            header('Location: ../gestion/agregar_equipo.php?msg='.urlencode($resultado['mensaje']));
                        }else{
                            header('Location: ../gestion/agregar_equipo.php?error='.urlencode($resultado['mensaje']));
                        }
                        exit;
                    }
                    break;
                default:
                    header('Location: ../gestion/equipos.php?error='.urlencode('Acción no válida'));
                    exit;
        }
    }
}
?>