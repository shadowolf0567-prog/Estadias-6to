<?php
require_once __DIR__ . '/../../config/db.php';
function agregar_clientes($nombre,$no_cuenta,$direccion){
    global $conn;
    $sql="INSERT INTO clientes (nombre,no_cuenta,direccion) VALUES (?,?,?,?,?)";
    $insert_preparado=mysqli_prepare($conn,$sql);
    if(!$insert_preparado){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la base de datos'
        ];
    }
    mysqli_stmt_bind_param($insert_preparado,'sss',$nombre,$no_cuenta,$direccion);
    $query_ok=mysqli_stmt_execute($insert_preparado);
    $rows_ok=mysqli_affected_rows($conn);
    mysqli_stmt_close($insert_preparado);
    if($query_ok && $rows_ok > 0){
        return[
            'estatus' => 'msg',
            'mensaje' => 'Cliente registrado exitosamente'
        ];
    }else{
        return[
            'estatus' => 'error',
            'mensaje' => 'Error al ingresar al cliente'
        ];
    }
}
function mostrar_clientes(){
    global $conn;
    $sql = "SELECT * FROM clientes";
    $select_preparado=mysqli_prepare($conn,$sql);
    mysqli_stmt_execute($select_preparado);
    $resultado=mysqli_stmt_get_result($select_preparado);
    $clientes=array();
    while($fila_bd=mysqli_fetch_assoc($resultado)){
        $clientes[]=$fila_bd;
    }
    mysqli_stmt_close($select_preparado);
    return $clientes;
}
function eliminar_cliente($id_cliente){
    global $conn;
    $sql="DELETE FROM clientes WHERE id_cliente=?";
    $delete_preparado=mysqli_prepare($conn,$sql);
    if(!$delete_preparado){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución en la base de datos'
        ];
    }
    mysqli_stmt_bind_param($delete_preparado,'i',$id_cliente);
    $query_ok=mysqli_stmt_execute($delete_preparado);
    $rows_ok=mysqli_affected_rows($conn);
    mysqli_stmt_close($delete_preparado);
    if($query_ok && $rows_ok){
        return[
            'estatys' => 'msg',
            'mensaje' => 'Producto eliminado exitosamente'
        ];
    }else{
        return[
        'estatus' => 'error',
        'mensaje' => 'No se pudo eliminar al cliente'
        ];
    }
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

    $sql_telefonos = "SELECT id, telefono, es_principal FROM telefonos WHERE id_cliente = ?";
    $stmt_telefono = mysqli_prepare($conn, $sql_telefonos);
    mysqli_stmt_bind_param($stmt_telefono,'i',$id_cliente);
    mysqli_stmt_execute($stmt_telefono);
    $result_telefonos = mysqli_stmt_get_result($stmt_telefono);
    $telefonos = [];
    while($row = mysqli_fetch_assoc($result_telefonos)){
        $telefonos[] = $row;
    }
    mysqli_stmt_close($stmt_telefono);

    $sql_correos = "SELECT id , correo, es_principal FROM correos WHERE id_cliente = ?";
    $stmt_correos = mysqli_prepare($conn,$sql_correos);
    mysqli_stmt_bind_param($stmt_correos,'i',$id_cliente);
    mysqli_stmt_execute($stmt_correos);
    $result_correos = mysqli_stmt_get_result($stmt_correos);
    $correos = [];
    while($row = mysqli_fetch_assoc($result_correos)){
        $correos[] = $row;
    }
    mysqli_stmt_close($stmt_correos);

    $cliente['telefonos'] = $telefonos;
    $cliente['correos'] = $correos;
    return $cliente;
}
function agregar_cliente_completo($nombre,$no_cuenta,$direccion,$telefonos = [],$correos = []){
    global $conn;
    if(!$conn){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error de conexión' 
        ];
    }
    $sql = "INSERT INTO clientes(nombre,no_cuenta,direccion)
            VALUES (?,?,?)";
    $stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt,'sss',$nombre,$no_cuenta,$direccion);

    if(!mysqli_stmt_execute($stmt)){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error al insertar cliente'
        ];
    }
    $id_cliente = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    if(!empty($telefonos)){
        $sql_telefono = "INSERT INTO telefonos(id_cliente,telefono,es_principal)
                            VALUES (?,?,?)";
        $stmt_telefono = mysqli_prepare($conn, $sql_telefono);
        foreach($telefonos as $telefono){
            $es_principal = $telefono['es_principal'] ?? false;
            mysqli_stmt_bind_param($stmt_telefono, 'iss', $id_cliente,$telefono['numero'], $es_principal);
            mysqli_stmt_execute($stmt_telefono);
        }
        mysqli_stmt_close($stmt_telefono);
    }
    if(!empty($correos)){
        $sql_correo = "INSERT INTO correos(id_cliente,correo,es_principal)
                        VALUES (?,?,?)";
        $stmt_correo= mysqli_prepare($conn,$sql_correo);
        foreach($correos as $correo){
            $es_principal=$correo['es_principal'] ?? false;
            mysqli_stmt_bind_param($stmt_correo,'iss',$id_cliente,$correo['direccion'],$es_principal);
            mysqli_stmt_execute($stmt_correo);
        }
        mysqli_stmt_close($stmt_correo);
    }
    return[
        'estatus' => 'msg',
        'mensaje' => 'Cliente agregado correctamente',
        'id_cliente' => $id_cliente
    ];

}
function editar_cliente_completo($id_cliente,$nombre,$no_cuenta,$direccion,$telefonos = [],$correos = []){
    global $conn;
    if(!$conn){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error de conexión'
        ];
    }
    $sql = "UPDATE clientes SET nombre=?,no_cuenta=?,direccion=? WHERE id_cliente =?";
    $stmt=mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt,'sssi',$nombre,$no_cuenta,$direccion,$id_cliente);

    if(!mysqli_stmt_execute($stmt)){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error al actualizar cliente'
        ];
    }
    mysqli_stmt_close($stmt);
    $sql_delete = "DELETE FROM telefonos WHERE id_cliente = ?";
    $stmt_delete = mysqli_prepare($conn,$sql_delete);
    mysqli_stmt_bind_param($stmt_delete,'i',$id_cliente);
    mysqli_stmt_execute($stmt_delete);
    mysqli_stmt_close($stmt_delete);

    if(!empty($telefonos)){
        $sql_telefono = "INSERT INTO telefonos(id_cliente,telefono,es_principal) VALUES (?,?,?)";
        $stmt_telefono = mysqli_prepare($conn,$sql_telefono);
        foreach($telefonos as $telefono){
            $es_principal=  $telefono['es_principal'] ?? false;
            mysqli_stmt_bind_param($stmt_telefono, 'isi',$id_cliente,$telefono['numero'],$es_principal);
            mysqli_stmt_execute($stmt_telefono);
        }
        mysqli_stmt_close($stmt_telefono);
    }
    $sql_delete_correos = "DELETE FROM correos WHERE id_cliente = ?";
    $stmt_delete_correos=mysqli_prepare($conn,$sql_delete_correos);
    mysqli_stmt_bind_param($stmt_delete_correos,'i',$id_cliente);
    mysqli_stmt_execute($stmt_delete_correos);
    mysqli_stmt_close($stmt_delete_correos);

    if(!empty($correos)){
        $sql_correo = "INSERT INTO correos (id_cliente,correo,es_principal) VALUES (?,?,?)";
        $stmt_correo = mysqli_prepare($conn,$sql_correo);

        foreach($correos as $correo){
            $es_principal = $correo['es_principal'] ?? false;
            mysqli_stmt_bind_param($stmt_correo,'isi',$id_cliente,$correo['direccion'],$es_principal);
            mysqli_stmt_execute($stmt_correo);
        }
        mysqli_stmt_close($stmt_correo);
    }
    return[
        'estatus' => 'msg',
        'mensaje' => 'Cliente actualizado correctamente'
    ];
}
function agregar_equipo_con_cliente($no_serie,$modelo,$inicio_contrato,$fin_contrato,$id_cliente = null){
    global $conn;
    if(!$conn){
        return[
            'estatus' => 'error',
            'menssaje' => 'Error de conexión a la base de datos'
        ];
    }
    if(empty($id_cliente) || $id_cliente == 0){
        $sql = 'INSERT INTO equipos (no_serie,modelo,inicio_contrato,fin_contrato) VALUES(?,?,?,?)';
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt,'ssss',$no_serie,$modelo,$inicio_contrato,$fin_contrato);
    }else{
        $sql = "INSERT INTO equipos(no_serie,modelo,inicio_contrato,fin_contrato,id_cliente) VALUES (?,?,?,?,?)";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt,'ssssi',$no_serie,$modelo,$inicio_contrato,$fin_contrato,$id_cliente);
        $mensaje = 'Equipo agregado correctamente';
    }
    if(!$stmt){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la preparación'
        ];
    }
    $query_ok=mysqli_stmt_execute($stmt);
    if(!$query_ok){
        $error = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        return[
            'estatus' => 'error',
            'mensaje' => 'Error al insertar: '.$error
        ];
    }
    $rows_ok = mysqli_affected_rows($conn);
    mysqli_stmt_close($stmt);
    if($rows_ok > 0){
        $mensaje = empty($id_cliente) || $id_cliente == 0 ?
        'Producto agregado correctamente':
        'Producto agregado y vinculado correctamente';

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
if($_SERVER['REQUEST_METHOD']==='POST'){
    if(isset($_POST['accion'])){
        $accion=$_POST['accion'];
        switch($accion){
            case 'agregar_completo':
                if(isset($_POST['id_cliente']) && isset($_POST['nombre'])){
                    $nombre = trim($_POST['nombre']);
                    $no_cuenta = trim($_POST['no_cuenta'] ?? '');
                    $direccion=trim($_POST['direccion'] ?? '');

                    $telefonos = [];
                    if(isset($_POST['telefonos']) && is_array($_POST['telefonos'])){
                        foreach($_POST['telefonos'] as $telefono){
                            if(!empty($telefono['numero'])){
                                $telefonos[] = [
                                    'numero' => trim($telefono['numero']),
                                    'es_principal' => isset($telefono['es_principal']) ? true : false
                                ];
                            }
                        }
                    }
                    $correos = [];
                    if(isset($_POST['correos']) && is_array($_POST['correos'])){
                        foreach($_POST['correos'] as $correo){
                            if(!empty($correo['direccion'])){
                                $correos[] = [
                                    'direccion' => trim($correo['direccion']),
                                    'es_principal' => isset($correo['es_principal']) ? true : false
                                ];
                            }
                        }
                    }
                    $resultado = agregar_cliente_completo($nombre,$no_cuenta,$direccion,$telefonos,$correos);
                    if($resultado['estatus'] === 'msg'){
                        header('Location: ../gestion/agregar_clientes.php?&msg='.urlencode($resultado['mensaje']));
                    }else{
                        header('Location: ../gestion/agregar_clientes.php?&error='.urlencode(($resultado['mensaje'])));
                    }
                    exit;

                }
                break;
            case 'editar_completo':
                if(isset($_POST['id_cliente']) && isset($_POST['nombre'])){
                    $id_cliente = intval($_POST['id_cliente']);
                    $nombre = trim($_POST['nombre'] ?? '');
                    $no_cuenta = trim($_POST['no_cuenta'] ?? '');
                    $direccion = trim($_POST['direccion'] ?? '');

                    $telefonos = [];
                    if(isset($_POST['telefonos']) && is_array($_POST['telefonos'])){
                        foreach($_POST['telefonos'] as $telefono){
                            if(!empty($telefono['numero'])){
                                $telefonos[] = [
                                    'numero' => trim($telefono['numero']),
                                    'es_principal' => isset($telefono['es_principal']) ? true : false
                                ];
                            }
                        }
                    }
                    $correos = [];
                    if(isset($_POST['correos']) && is_array($_POST['correos'])){
                        foreach($_POST['correos'] as $correo){
                            if(!empty($correo['direccion'])){
                                $correos[] = [
                                    'direccion' => trim($correo['direccion']),
                                    'es_principal' => isset($correo['es_principal']) ? true : false
                                ];
                            }
                        }
                    }
                    $resultado = editar_cliente_completo($id_cliente,$nombre,$no_cuenta,$direccion,$telefonos,$correos);
                    if($resultado['estatus'] === 'msg'){
                        header('Location: ../gestion/editar_clientes.php?id_cliente='.$id_cliente.'&msg='.urlencode($resultado['mensaje']));
                    }else{
                        header('Location: ../gestion/editar_clientes.php?id_cliente='.$id_cliente,'&error='.urlencode($resultado['mensaje']));
                    }
                    exit;
                }
                break;
            case 'eliminar':
                if(isset($_POST['id_cliente'])){
                    $id_cliente=intval($_POST['id_cliente']);
                    $resultado=eliminar_cliente($id_cliente);
                    header('Location: ../gestion/clientes.php?'.$resultado['estatus'].'='.urlencode($resultado['mensaje']));
                    exit;
                }
                break;
            case 'agregar_con_cliente':
                if(isset($_POST['no_serie'],$_POST['modelo'],$_POST['inicio_contrato'],$_POST['fin_contrato'])){
                    $no_serie = trim($_POST['no_serie']);
                    $modelo = trim($_POST['modelo']);
                    $inicio_contrato = trim($_POST['inicio_contrato']);
                    $fin_contrato = trim($_POST['fin_contrato']);
                    $modo_cliente = $_POST['modo_cliente'] ?? 'existente';

                    $id_cliente = null;
                    if(isset($_POST['id_cliente']) && !empty($_POST['id_cliente']) && $_POST['id_cliente'] > 0){
                        $id_cliente = intval($_POST['id_cliente']);
                    }
                    if(empty($no_serie)){
                        header('Location: ../gestion/agregar_equipo.php?error='.urlencode('El número de serie es obligatorio'));
                        exit;
                    }
                    $resultado = agregar_equipo_con_cliente($no_serie,$modelo,$inicio_contrato,$fin_contrato,$id_cliente);
                    if($resultado['estatus'] === 'msg'){
                        header('Location: ../gestion/agregar_equipo.php?msg='.urlencode($resultado['mensaje']));
                    }else{
                        header('Location: ../gestion/agregar_equipo.php?error='.urlencode($resultado['mensaje']));
                    }
                    exit;
                }
                break;

            default:
                header('Location: ../gestion/clientes.php?error='.urlencode('Acción no valida'));
                exit;
        }
    }
}
?>