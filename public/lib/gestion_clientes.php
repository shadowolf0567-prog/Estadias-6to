<?php
require_once __DIR__ . '/../../config/db.php';
function obtener_cliente_completo($id_cliente){
    global $conn;
    $sql = "SELECT c.* FROM clientes c WHERE c.id_cliente = ?";
    $stmt = mysqli_prepare($conn,$sql);
    mysqli_stmt_bind_param($stmt, 'i', $id_cliente);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $cliente = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if(!$cliente) return null;

    $sql_telefonos = "SELECT id, telefono, contacto, es_principal FROM telefonos WHERE id_cliente = ? ORDER BY es_principal DESC";
    $stmt_telefonos = mysqli_prepare($conn,$sql_telefonos);
    mysqli_stmt_bind_param($stmt_telefonos, 'i', $id_cliente);
    mysqli_stmt_execute($stmt_telefonos);
    $result_telefono = mysqli_stmt_get_result($stmt_telefonos);
    $telefonos =[];
    while($row = mysqli_fetch_assoc($result_telefono)){
        $telefonos[] = $row;
    }
    mysqli_stmt_close($stmt_telefonos);

    if(!$cliente) return null;

    $sql_correos = "SELECT id, correo, contacto, es_principal FROM correos WHERE id_cliente = ? ORDER BY es_principal DESC";
    $stmt_correos = mysqli_prepare($conn,$sql_correos);
    mysqli_stmt_bind_param($stmt_correos, 'i', $sql_correos);
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

function guardar_cliente_completo($id_cliente, $nombre, $no_cuenta, $direccion, $telefonos = [], $correos = []){
    global $conn;
    if(!$conn){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error de conexión a la base de datos'
        ];
    }
    $es_nuevo = empty($id_cliente);
    if($es_nuevo){
        $sql = "INSERT INTO clientes(nombre,no_cuenta,direccion)
                VALUES (?,?,?)";
        $stmt = mysqli_prepare($conn,$sql);
        mysqli_stmt_bind_param($stmt, 'sssss', $nombre, $no_cuenta, $direccion);
        if(!mysqli_stmt_execute($stmt)){
            return[
                'estatus' => 'error',
                'mensaje' => 'Error al insertar cliente'
            ];
        }
        $id_cliente = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt);
    }else{
        $sql = "UPDATE clientes SET nombre = ?, no_cuenta = ?, direccion=? WHERE id_cliente = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt,'sssi',$nombre, $no_cuenta, $direccion, $id_cliente);

        if(!mysqli_stmt_execute($stmt)){
            return[
                'estatus' => 'error',
                'mensaje' => 'Error al actualizar el cliente'
            ];
        }
        mysqli_stmt_close($stmt);

        $sql_delete = "DELETE FROM telefonos WHERE id_cliente = ?";
        $stmt_delete = mysqli_prepare($conn,$sql_delete);
        mysqli_stmt_bind_param($stmt_delete, 'i', $id_cliente);
        mysqli_stmt_execute($stmt_delete);
        mysqli_stmt_close($stmt_delete);

        $sql_delete_correos = "DELETE FROM correos WHERE id_cliente = ?";
        $stmt_delete_correos = mysqli_prepare($conn, $sql_delete_correos);
        mysqli_stmt_bind_param($stmt_delete_correos,'i',$id_cliente);
        mysqli_stmt_execute($stmt_delete_correos);
        mysqli_stmt_close($stmt_delete_correos);
    }

    if(!empty($telefonos)){
        $sql_telefono = "INSERT INTO telefonos (id_cliente,telefono,contacto,es_principal) VALUES (?,?,?,?)";
        $stmt_telefonos = mysqli_prepare($conn,$sql_telefono);

        foreach($telefonos as $telefono){
            if(!empty($telefono['numero'])){
                $contacto = $telefono['contacto'] ?? '';
                $es_principal = isset($telefono['es_principal']) ? 1 : 0;
                mysqli_stmt_bind_param($stmt_telefonos, 'issi', $id_cliente, $telefono['numero'], $contacto, $es_principal);
                mysqli_stmt_execute($stmt_telefonos);
            }
        }
        mysqli_stmt_close($stmt_telefonos);
    }
    if(!empty($correos)){
        $sql_correo = "INSERT INTO correos (id_cliente, correo,contacto,es_principal) VALUES (?,?,?,?)";
        $stmt_correos = mysqli_prepare($conn,$sql_correo);

        foreach($correos as $correo){
            if(!empty($correo['direccion'])){
                $titular = $correo['titular'] ?? '';
                $es_principal = isset($correo['es_principal']) ? 1 : 0;
                mysqli_stmt_bind_param($stmt_correos, 'issi', $id_cliente,$correo['direccion'],$titular,$es_principal);
            }
        }
        mysqli_stmt_close($stmt_correos);
    }
    return[
        'estatus' => 'msg',
        'mensaje' => $es_nuevo ? 'Cliente agregado correctamente' : 'Cliente actualizado correctamente',
        'id_cliente' => $id_cliente
    ];
}

function eliminar_cliente($id_cliente){
    global $conn;

    $sql = "DELETE FROM clientes WHERE id_cliente = ?";
    $delete_preparado = mysqli_prepare($conn,$sql);
    if(!$delete_preparado){
        return[
            'estatus' => 'error',
            'mensaje' => 'Error en la ejecución de la base de datos'
        ];
    }
    mysqli_stmt_bind_param($delete_preparado,'i',$id_cliente);
    $query_ok = mysqli_stmt_execute($delete_preparado);
    $rows_ok = mysqli_affected_rows($conn);
    mysqli_stmt_close($delete_preparado);
    if($query_ok && $rows_ok > 0){
        return[
            'estatus' => 'msg',
            'mensaje' => 'Cliente eliminado exitosamente'
        ];
    }else{
        return[
            'estatus' => 'error',
            'mensaje' => 'No se pudo eliminar al cliente'
        ];
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['accion'])){
        $accion = $_POST['accion'];

        switch($accion){
            case 'agregar_completo':
                if(isset($_POST['nombre']) && !empty($_POST['nombre'])){
                    $nombre = trim($_POST['nombre']);
                    $no_cuenta = trim($_POST['no_cuenta'] ?? '');
                    $direccion = trim($_POST['direccion'] ?? '');

                    $telefonos = [];
                    if(isset($_POST['telefonos']) && is_array($_POST['telefonos'])){
                        foreach($_POST['telefonos'] as $telefono){
                            if(!empty($telefono['numero'])){
                                $telefonos[] = [
                                    'numero' => trim($telefono['numero']),
                                    'contacto' =>  trim($telefono['contacto'] ?? ''),
                                    'es_principal' => isset($telefono['es_principal']) ? true:false
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
                                    'contacto' => trim($correo['contacto'] ?? ''),
                                    'es_principal' => isset($correo['es_principal']) ? true : false
                                ];
                            }
                        }
                    }
                    $resultado = guardar_cliente_completo(null,$nombre,$no_cuenta,$direccion,$telefonos,$correos);

                    if($resultado['estatus'] === 'msg'){
                        header('Location: ../clientes/clientes.php?msg='.urlencode($resultado['mensaje']));
                    }else{
                        header('Location: ../clientes/agregar_cliente.php?error='.urlencode($resultado['mensaje']));
                    }
                    exit;
                }
                break;
            case 'editar_completo':
                if(isset($_POST['id_cliente']) && isset($_POST['nombre'])){
                    $id_cliente = intval($_POST['id_cliente']);
                    $nombre = trim($_POST['nombre']);
                    $no_cuenta = trim($_POST['no_cuenta'] ?? '');
                    $direccion = trim($_POST['direccion'] ?? '');

                    $telefonos = [];
                    if(isset($_POST['telefonos']) && is_array($_POST['telefonos'])){
                        foreach($_POST['telefonos'] as $telefono){
                            if(!empty($telefono['numero'])){
                                $telefonos[] =[
                                    'numero' => trim($telefono['numero']),
                                    'contacto' => trim($telefono['contacto'] ?? ''),
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
                                    'contacto' => trim($correo['contacto'] ?? ''),
                                    'es_principal' => isset($correo['es_principal']) ? true : false
                                ];
                            }
                        }
                    }
                    $resultado = guardar_cliente_completo($id_cliente,$nombre,$no_cuenta,$direccion,$telefonos,$correos);

                    if($resultado['estatus'] === 'msg'){
                        header('Location: ../clientes/clientes.php?msg='.urlencode($resultado['mensaje']));
                    }else{
                        header('Location: ../clientes/editar_cliente.php?id_cliente=' .$id_cliente. '$error=' . urlencode($resultado['mensaje']));
                    }
                    exit;
                }
                break;
            case 'eliminar':
                if(isset($_POST['id_cliente'])){
                    $id_cliente = intval($_POST['id_cliente']);
                    $resultado = eliminar_cliente($id_cliente);

                    if($resultado['estatus'] === 'msg'){
                        header('Location: ../clientes/clientes.php?msg='.urlencode($resultado['mensaje']));
                    }else{
                        header('Location: ../cliente(clientes.php?error='.urlencode($resultado['mensaje']));
                    }
                    exit;
                }
                break;
            default:
                header('Location: ../clientes/clientes.php?error='.urlencode('Accion no válida'));
                exit;
        }
    }
}
?>