<?php
session_start();
require_once __DIR__ .'/../../config/db.php';
if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] != 1 && $_SESSION['tip_usr'] != 2)){
    header('Location: ../login.php?error='.urlencode('Acceso denegado'));
    exit;
}
if(!isset($_GET['id']) || empty($_GET['id'])){
    header('Location: equipos.php?error='.urlencode('ID de equipo no especificado'));
    exit;
}
$id_equipo = intval($_GET['id']);
$sql = "SELECT e.*, c.id_cliente,c.nombre,c.no_cuenta
        FROM equipos e
        INNER JOIN clientes c
        ON e.id_cliente = c.id_cliente
        WHERE e.id_equipo = ?";
$stmt = mysqli_prepare($conn,$sql);
mysqli_stmt_bind_param($stmt,'i',$id_equipo);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$equipo = mysqli_fetch_assoc($resultado);
mysqli_stmt_close($stmt);

if(!$equipo){
    header('Location: equipos.php?error='.urlencode('Equipo no encontrado'));
    exit;
}

$reportes_equipos = [];
if($equipo['id_equipo']){
    $sql_reportes_equipos = "SELECT id_reporte, reporte, fecha, estado, fecha_atencion, descripcion
                                FROM reportes r
                                WHERE id_equipo = ?";
    $stmt_re = mysqli_prepare($conn,$sql_reportes_equipos);
    mysqli_stmt_bind_param($stmt_re,"i",$equipo['id_equipo']);
    mysqli_stmt_execute($stmt_re);
    $result_re = mysqli_stmt_get_result($stmt_re);
    while($row = mysqli_fetch_assoc($result_re)){
        $reportes_equipos[] = $row;
    }
    mysqli_stmt_close($stmt_re);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Equipo</title>
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
</head>
<body>
    <?php require_once __DIR__ . '/../gestion/menu.php'; ?>
    <div class="container mt-4">
        <div class="mb-3">
            <a href="equipos.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <a href="editar_equipo.php?id_equipo=<?= $equipo['id_equipo'] ?>" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar Equipo
            </a>
        </div>
        <div class="cliente-header">
            <div class="d-flex justify-content-between-align-items-center">
                <div>
                    <h2 class="mb-0">Datos del Equipo</h2>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card info-card">
                    <div class="card-header">Información del Equipo</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p><span class="info-label">Modelo: </span><?= htmlspecialchars($equipo['modelo']) ?></p>
                                <p><span class="info-label">Número de Serie: </span><?= htmlspecialchars($equipo['no_serie']) ?></p>
                                <p><span class="info-label">Inicio de Contrato: </span><?= htmlspecialchars($equipo['inicio_contrato']) ?></p>
                                <p><span class="info-label">Fin de Contrato: </span><?= htmlspecialchars($equipo['fin_contrato']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card info-card">
                    <div class="card-header">Información del Cliente</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <p><span class="info-label">Cliente: </span><?= htmlspecialchars($equipo['nombre']) ?></p>
                                <p><span class="info-label">Número de Cuenta: </span><?= htmlspecialchars($equipo['no_cuenta']) ?></p>
                                <a href="../clientes/ver_cliente.php?id=<?= $equipo['id_cliente'] ?>" class="btn btn-info">
                                    <i class="bi bi-eye"></i> Ver Cliente
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 mt-3">
                <h3 class="mb-3">Reportes del Equipo</h3>
                <?php if(count($reportes_equipos) > 0): ?>
                    <div class="row">
                        <?php foreach($reportes_equipos as $reportes): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card info-card equipo-card h-100">
                                    <div class="card-header"><?= htmlspecialchars($reportes['reporte']) ?></div>
                                    <div class="card-body">
                                        <p><span class="info-label">Fecha: </span><?= htmlspecialchars($reportes['fecha']) ?></p>
                                        <?php if($reportes['estado'] == 'atendido'): ?>
                                            <p><span class="info-label">Fecha de Atención: </span><?= htmlspecialchars($reportes['fecha_atencion']) ?></p>
                                        <?php endif; ?>
                                        <p><span class="info-label">Estado: </span><?= htmlspecialchars($reportes['estado']) ?></p>
                                        <p><span class="info-label">Descripción del Problema: </span><?= htmlspecialchars($reportes['descripcion']) ?></p>
                                        <a href="../reportes/ver_reporte.php?id=<?= $reportes['id_reporte'] ?>" class="btn btn-info">
                                            <i class="bi bi-eye"></i> Ver Reporte
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle" style="font-size: 36px;"></i>
                        <h5>Este equipo no tiene ningún reporte</h5>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>