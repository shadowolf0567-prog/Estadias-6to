<?php
session_start();
require_once __DIR__ .'/../../config/db.php';
header('Content-Type: application/json');
if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] !=1 && $_SESSION['tip_usr'] !=2)){
    echo json_encode(['success' => false, 'error' => 'Acceso denegado']);
    exit;
}

$data=json_decode(file_get_contents('php://input'), true);

if(!$data || empty($data['nombre'])){
    echo json_encode(['success' => false, 'error' => 'Nombre requerido']);
    exit;
}

$nombre=trim($data['nombre']);
$no_cuenta=trim($data['no_cuenta'] ?? '');
$direccion=trim($data['direccion'] ?? '');

$sql="INSERT INTO clientes (nombre,no_cuenta,direccion) VALUES (?,?,?)";
$stmt=mysqli_prepare($conn,$sql);
mysqli_stmt_bind_param($stmt, 'sss', $nombre,$no_cuenta,$direccion);

if(!mysqli_stmt_execute($stmt)){
    echo json_encode(['success' => false, 'error' => mysqli_error($conn)]);
    exit;
}
$id_cliente = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

if(isset($data['telefonos']) && is_array($data['telefonos']) && count($data['telefonos']) > 0){
    $sql_telefono = "INSERT INTO telefonos(id_cliente,telefono,contacto,es_principal) VALUES (?,?,?,?)";
    $stmt_telefono = mysqli_prepare($conn,$sql_telefono);
    foreach($data['telefonos'] as $telefono){
        if(!empty($telefono['numero'])){
            $contacto = $telefono['contacto'] ?? '';
            $es_principal = isset($telefono['es_principal']) ? 1 : 0;
            mysqli_stmt_bind_param($stmt_telefono,'issi',$id_cliente,$telefono['numero'],$contacto,$es_principal);
            mysqli_stmt_execute($stmt_telefono);
        }
    }
    mysqli_stmt_close($stmt_telefono);
}
if(isset($data['correos']) && is_array($data['correos']) && count($data['correos']) > 0){
    $sql_correo = "INSERT INTO correos (id_cliente,correo,contacto,es_principal) VALUES (?,?,?,?)";
    $stmt_correo = mysqli_prepare($conn,$sql_correo);
    foreach($data['correos'] as $correo){
        if(!empty($correo['direccion'])){
            $contacto = $correo['contacto'] ?? '';
            $es_principal = isset($correo['es_principal']) ? 1 : 0;
            mysqli_stmt_bind_param($stmt_correo,'issi',$id_cliente,$correo['direccion'],$contacto,$es_principal);
            mysqli_stmt_execute($stmt_correo);
        }
    }
    mysqli_stmt_close($stmt_correo);
}
echo json_encode([
    'success' => true,
    'id_cliente' => $id_cliente,
    'message' => 'Cliente creado exitosamente'
]);

mysqli_close($conn);
?>
