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
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : null;
if($equipo){
    $sql_reportes_equipos = "SELECT r.id_reporte, r.fecha, r.estado,
                                (SELECT COUNT(*) FROM reportes_componentes rc
                                WHERE rc.id_reporte = r.id_reporte) as total_componentes,
                                (SELECT COUNT(*) FROM reportes_componentes rc
                                WHERE rc.id_reporte = r.id_reporte
                                AND (rc.tipo = 'SER-01')) as preventivos,
                                (SELECT COUNT(*) FROM reportes_componentes rc
                                WHERE rc.id_reporte = r.id_reporte
                                AND (rc.tipo = 'SER-02')) as correctivos,
                                (SELECT COUNT(*) FROM reportes_componentes rc
                                WHERE rc.id_reporte = r.id_reporte
                                AND (rc.tipo = 'SER-03' OR rc.tipo = 'componente')) as total_componentes
                                FROM reportes r
                                WHERE id_equipo = ?";
    $params = [$equipo['id_equipo']];
    $types = "i";
    if(!empty($mes)){
        $sql_reportes_equipos .= " AND month(r.fecha) = ? and year(r.fecha) = year(curdate())";
        $params[] = $mes;
        $types .= "i";
    }
    $sql_reportes_equipos .= " ORDER BY r.fecha DESC";

    $stmt_re = mysqli_prepare($conn,$sql_reportes_equipos);
    mysqli_stmt_bind_param($stmt_re,$types, ...$params);
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
    <link rel="stylesheet" href="../assets/css/responsive.css">
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
            <div class="col-md-12">
                <form action="" method="get" class="mb-3">
                    <input type="hidden" name="id" value="<?= $equipo['id_equipo'] ?>">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label for="" class="form-label">Filtrar por mes</label>
                            <select name="mes" class="form-select" onchange="this.form.submit()">
                                <option value="">-- Todos --</option>
                                <option value="1"<?= ($mes == 1) ? 'selected' : '' ?>>Enero</option>
                                <option value="2"<?= ($mes == 2) ? 'selected' : '' ?>>Febrero</option>
                                <option value="3"<?= ($mes == 3) ? 'selected' : '' ?>>Marzo</option>
                                <option value="4"<?= ($mes == 4) ? 'selected' : '' ?>>Abril</option>
                                <option value="5"<?= ($mes == 5) ? 'selected' : '' ?>>Mayo</option>
                                <option value="6"<?= ($mes == 6) ? 'selected' : '' ?>>Junio</option>
                                <option value="7"<?= ($mes == 7) ? 'selected' : '' ?>>Julio</option>
                                <option value="8"<?= ($mes == 8) ? 'selected' : '' ?>>Agosto</option>
                                <option value="9"<?= ($mes == 9) ? 'selected' : '' ?>>Septiembre</option>
                                <option value="10"<?= ($mes == 10) ? 'selected' : '' ?>>Octubre</option>
                                <option value="11"<?= ($mes == 11) ? 'selected' : '' ?>>Noviembre</option>
                                <option value="12"<?= ($mes == 12) ? 'selected' : '' ?>>Diciembre</option>
                            </select>
                        </div>
                        <?php if(!empty($mes)): ?>
                            <div class="col-md-2">
                                <a href="ver_equipo.php?id=<?= $equipo['id_equipo'] ?>" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Limpiar
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
            <div class="col-md-12">
                <div class="card info-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            Reportes del Equipo
                            <span class="badge bg-light text-dark ms-2">
                                <?= count($reportes_equipos) ?> reporte(s)
                            </span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if(count($reportes_equipos) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Fecha</th>
                                            <th>Componentes/Refacciones</th>
                                            <th>Preventivos</th>
                                            <th>Correctivos</th>
                                            <th>Estado</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($reportes_equipos as $reporte): ?>
                                            <tr>
                                                <td><?= date('d/m/Y', strtotime($reporte['fecha'])) ?></td>
                                                <td><?= htmlspecialchars($reporte['total_componentes']) ?></td>
                                                <td><?= htmlspecialchars($reporte['preventivos']) ?></td>
                                                <td><?= htmlspecialchars($reporte['correctivos']) ?></td>
                                                <td><?= htmlspecialchars($reporte['estado']) ?></td>
                                                <td>
                                                    <a href="../reportes/ver_reporte.php?id=<?= $reporte['id_reporte'] ?>" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i> Ver Reporte
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center p-4">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p class="mt-2 text-muted">
                                    No hay reportes registrados en este mes
                                </p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>