<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] !=1 && $_SESSION['tip_usr'] != 2)){
    header('Location: ../login.php?error='.urlencode('Acceso denegado'));
    exit;
}
if(!isset($_GET['id']) || empty($_GET['id'])){
    header('Location: clientes.php?error='.urlencode('ID de cliente no especificada'));
    exit;
}
$id_cliente = intval($_GET['id']);
$sql = "SELECT c.*,
                GROUP_CONCAT(DISTINCT ct.telefono SEPARATOR ', ') as telefonos,
                GROUP_CONCAT(DISTINCT cc.correo SEPARATOR ', ') as correos
        FROM clientes c
        LEFT JOIN telefonos ct ON c.id_cliente = ct.id_cliente
        LEFT JOIN correos cc ON c.id_cliente = cc.id_cliente
        WHERE c.id_cliente = ?
        GROUP BY c.id_cliente";
$stmt = mysqli_prepare($conn,$sql);
mysqli_stmt_bind_param($stmt, 'i', $id_cliente);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$cliente = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if(!$cliente){
    header('Location: clientes.php?error='.urlencode('Cliente no encontrado'));
    exit;
}

$equipos_cliente=[];
if($cliente['id_cliente']){
    $sql_equipos_cliente = "SELECT e.id_equipo,modelo,inicio_contrato,fin_contrato,no_serie,
                                (SELECT COUNT(*) FROM reportes r WHERE r.id_equipo = e.id_equipo) as total_reportes
                            FROM equipos e
                            WHERE e.id_cliente = ?
                            ORDER BY no_serie ASC";
    $stmt_eq = mysqli_prepare($conn,$sql_equipos_cliente);
    mysqli_stmt_bind_param($stmt_eq,"i",$cliente['id_cliente']);
    mysqli_stmt_execute($stmt_eq);
    $result_eq = mysqli_stmt_get_result($stmt_eq);
    while($row = mysqli_fetch_assoc($result_eq)){
        $equipos_cliente[] = $row;
    }
    mysqli_stmt_close($stmt_eq);
}
$telefonos = [];
if($cliente['id_cliente']){
    $sql_telefonos = "SELECT telefono, contacto
                        FROM telefonos
                        WHERE id_cliente = ?";
    $stmt_tel = mysqli_prepare($conn,$sql_telefonos);
    mysqli_stmt_bind_param($stmt_tel,"i",$cliente['id_cliente']);
    mysqli_stmt_execute($stmt_tel);
    $result_tel = mysqli_stmt_get_result($stmt_tel);
    while($row = mysqli_fetch_assoc($result_tel)){
        $telefono[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Cliente</title>
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <style>
        .info-card{
            border:none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .info-card .card-header{
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: bold;
        }
        .info-label{
            font-weight: 600;
            color: #495067;
            width: 160px;
            display: inline-block;
        }
        .cliente-header{
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }
        .equipo-card{
            transition: transform 0.2s;
        }
        .equipo-card:hover{
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ .'/menu.php'; ?>
    <div class="container mt-4">
        <div class="mb-3">
            <a href="clientes.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <a href="editar_clientes.php?id_cliente=<?= $cliente['id_cliente'] ?>" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar Cliente
            </a>
        </div>
        <div class="cliente-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">Datos del Cliente</h2>
                    <p class="mb-0 mt-2"><strong><?= htmlspecialchars($cliente['nombre']) ?></strong></p>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card info-card">
                    <div class="card-header">Información del Cliente</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p><span class="info-label">Número de Cuenta: </span><?= htmlspecialchars($cliente['no_cuenta']) ?></p>
                                <p><span class="info-label">Dirección: </span><?= htmlspecialchars($cliente['direccion']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card info-card">
                    <div class="card-header">Teléfonos</div>
                    <div class="card-body">
                        <?php if($cliente['telefonos']): ?>
                            <?php foreach(explode(', ,  ',$cliente['telefonos']) as $telefono): ?>
                                    <?= htmlspecialchars($telefono) ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No hay telefonos registrados</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card info-card">
                    <div class="card-header">Correos Electrónicos</div>
                    <div class="card-body">
                        <?php if($cliente['correos']): ?>
                            <?php foreach(explode(', ,  ',$cliente['correos']) as $correo): ?>
                                    <?= htmlspecialchars($correo) ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">No hay correos registrados</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-3">
                <h3 class="mb-3">Equipos del Cliente</h3>
                <?php if(count($equipos_cliente) > 0): ?>
                    <div class="row">
                        <?php foreach($equipos_cliente as $equipo): ?>
                            <?php $total_reportes = intval($equipo['total_reportes'] ?? 0); ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card info-card equipo-card h-100">
                                    <div class="card-header"><?= htmlspecialchars($equipo['modelo']) ?></div>
                                    <div class="card-body">
                                        <p><span class="info-label">Número de Serie: </span><?= htmlspecialchars($equipo['no_serie']) ?></p>
                                        <p><span class="info-label">Inicio de Contrato: </span><?= htmlspecialchars($equipo['inicio_contrato']) ?></p>
                                        <p><span class="info-label">Fin de Contrato: </span><?= htmlspecialchars($equipo['fin_contrato'] ?: 'No especificado') ?></p>
                                        <hr>
                                        <p><span class="info-label">Reportes: </span><?= htmlspecialchars($equipo['total_reportes']) ?></p>
                                        <div class="card-footer">
                                            <a href="ver_equipo.php?id=<?= $equipo['id_equipo'] ?>" class="btn btn-sm btn-info">
                                                <i class="bi bi-eye"></i> Ver Equipo
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle" style="font-size: 36px;"></i>
                        <h5>No hay equipos registrados para este cliente</h5>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>