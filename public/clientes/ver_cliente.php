<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] != 1 && $_SESSION['tip_usr'] !=2)){
    header('Location: ../login.php?error='.urlencode('Acceso denegado'));
    exit;
}

if(!isset($_GET['id']) || empty($_GET['id'])){
    header('Location: clientes.php?error='.urlencode('ID de cliente no especificado'));
    exit;
}
$id_cliente = intval($_GET['id']);
$sql = "SELECT c.*,
                GROUP_CONCAT(DISTINCT CONCAT_WS('||', t.telefono, t.contacto) SEPARATOR ';;') as telefonos_raw,
                GROUP_CONCAT(DISTINCT CONCAT_WS('||', cc.correo, cc.contacto) SEPARATOR ';;') as correos_raw
        FROM clientes c
        LEFT JOIN telefonos t ON c.id_cliente = t.id_cliente
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

$telefonos = [];
if(!empty($cliente['telefonos_raw'])){
    $items = explode(';;',$cliente['telefonos_raw']);
    foreach($items as $item){
        $parts = explode('||',$item);
        if(count($parts) >= 2){
            $telefonos[] = [
                'telefono' => $parts[0],
                'contacto' => $parts[1] ?: '',
            ];
        }
    }
}
$cliente['telefonos'] = $telefonos;

$correos =[];
if(!empty($cliente['correos_raw'])){
    $items = explode(';;',$cliente['correos_raw']);
    foreach($items as $item){
        $parts = explode('||',$item);
        if(count($parts) >= 2){
            $correos[] = [
                'correo' => $parts[0],
                'contacto' => $parts[1] ?: '',
            ];
        }
    }
}
$cliente['correos'] = $correos;

$equipos_cliente = [];
if($cliente['id_cliente']){
    $sql_equipos_cliente = "SELECT id_equipo, modelo,no_serie,
                                    (SELECT COUNT(*) FROM reportes r WHERE r.id_equipo = e.id_equipo) as total_reportes
                            FROM equipos e
                            WHERE id_cliente = ?
                            ORDER BY no_serie ASC";
    $stmt_eq = mysqli_prepare($conn,$sql_equipos_cliente);
    mysqli_stmt_bind_param($stmt_eq, "i", $cliente['id_cliente']);
    mysqli_stmt_execute($stmt_eq);
    $result_eq = mysqli_stmt_get_result($stmt_eq);
    while($row = mysqli_fetch_assoc($result_eq)){
        $equipos_cliente[] = $row;
    }
    mysqli_stmt_close($stmt_eq);
}
$componentes = [];
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : null;
if($id_cliente){    
    $sql_componentes = "SELECT r.fecha,rc.descripcion, e.modelo,e.no_serie, rc.componente
                        FROM reportes_componentes rc
                        INNER JOIN reportes r ON rc.id_reporte = r.id_reporte
                        LEFT JOIN clientes c ON r.id_cliente = c.id_cliente
                        LEFT JOIN equipos e ON r.id_equipo = e.id_equipo
                        WHERE 1=1
                        AND rc.componente != 'Servicio Preventivo'
                        AND rc.componente != 'Servicio Correctivo'
                        AND c.id_cliente = ?";
    
    $params = [$cliente['id_cliente']];
    $types = "i";

    if(!empty($mes)){
        $sql_componentes .= " AND MONTH(r.fecha) = ? AND YEAR(r.fecha) = YEAR(CURDATE())";
        $params[] = $mes;
        $types .= "i";
    }
    $sql_componentes .= " ORDER BY r.fecha DESC";

    $stmt_rc = mysqli_prepare($conn, $sql_componentes);
    mysqli_stmt_bind_param($stmt_rc,$types, ...$params);
    mysqli_stmt_execute($stmt_rc);
    $result_rc = mysqli_stmt_get_result($stmt_rc);

    while($row = mysqli_fetch_assoc($result_rc)){
        $componentes[] = $row;
    }
    mysqli_stmt_close($stmt_rc);
}
$total_componentes = count($componentes);
$componentes_distintos = count(array_unique(array_column($componentes, 'componente')));
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Cliente</title>
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <style>
        .info-card{
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .info-card .card-header{
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: bold;
        }
        .info-label{
            font-weight: 600;
            color: #495057;
            width: 160px;
            display: inline-block;
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
    <?php require_once __DIR__ .'/../gestion/menu.php'; ?>

    <div class="container mt-4">
        <div class="mb-3">
            <a href="clientes.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <a href="editar_clientes.php?id_cliente=<?= $cliente['id_cliente'] ?>" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
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
                                <p><span class="info-label">Encargado: </span><?= htmlspecialchars($cliente['encargado']) ?: 'No registrado' ?></p>
                                <p><span class="info-label">Número de Cuenta: </span><?= htmlspecialchars(($cliente['no_cuenta'] ?: 'No registrado')) ?></p>
                                <p><span class="info-label">Contrato: </span><?= htmlspecialchars($cliente['contrato'] ?: 'No registrado') ?></p>
                                <p><span class="info-label">Dirección: </span><?= htmlspecialchars($cliente['direccion'] ?: 'No registrado') ?></p>
                                <div class="col-md-12">
                                    <div class="card info-card">
                                        <div class="card-header">Teléfonos</div>
                                        <div class="card-body">
                                            <?php if(count($cliente['telefonos']) > 0): ?>
                                                <?php foreach($cliente['telefonos'] as $telefono): ?>
                                                        <i class="bi bi-telephone"></i> <?= htmlspecialchars($telefono['telefono']) ?>  
                                                        <?php if(!empty($telefono['contacto'])): ?>
                                                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($telefono['contacto']) ?>
                                                        <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p class="text-muted">No hay teléfonos registrados</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="card info-card">
                                        <div class="card-header">Correos Electrónicos</div>
                                        <div class="card-body">
                                            <?php if(count($cliente['correos']) > 0): ?>
                                                <?php foreach($cliente['correos'] as $correo): ?>
                                                    <i class="bi bi-envelope"></i> <?= htmlspecialchars($correo['correo']) ?>
                                                    <?php if(!empty($correo['contacto'])): ?>
                                                        <i class="bi bi-person-circle"></i><?= htmlspecialchars($correo['contacto']) ?>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <p class="text-muted">No hay correos registrados</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <form action="" method="get" class="mb-3">
                <input type="hidden" name="id" value="<?= $cliente['id_cliente'] ?>">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Filtrar por mes</label>
                        <select name="mes" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Todos --</option>
                            <option value="1" <?= ($mes == 1) ? 'selected' : '' ?>>Enero</option>
                            <option value="2" <?= ($mes == 2) ? 'selected' : '' ?>>Febrero</option>
                            <option value="3" <?= ($mes == 3) ? 'selected' : '' ?>>Marzo</option>
                            <option value="4" <?= ($mes == 4) ? 'selected' : '' ?>>Abril</option>
                            <option value="5" <?= ($mes == 5) ? 'selected' : '' ?>>Mayo</option>
                            <option value="6" <?= ($mes == 6) ? 'selected' : '' ?>>Junio</option>
                            <option value="7" <?= ($mes == 7) ? 'selected' : '' ?>>Julio</option>
                            <option value="8" <?= ($mes == 8) ? 'selected' : '' ?>>Agosto</option>
                            <option value="9" <?= ($mes == 9) ? 'selected' : '' ?>>Septiembre</option>
                            <option value="10"<?= ($mes == 10) ? 'selected' : '' ?>>Octubre</option>
                            <option value="11"<?= ($mes == 11) ? 'selected' : '' ?>>Noviembre</option>
                            <option value="12"<?= ($mes == 12) ? 'selected' : '' ?>>Diciembre</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter"></i> Filtrar
                        </button>
                    </div>
                    <?php if(!empty($mes)): ?>
                        <div class="col-md-2">
                            <a href="ver_cliente.php?id=<?= $cliente['id_cliente'] ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Limpiar
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
            <div class="card md-12">
                <div class="card-info-card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            Componentes Solicitados
                            <span class="badge bg-light text-dark ms-2">
                                <?= count($componentes) ?> registros
                            </span>
                        </h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if(count($componentes) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Nombre</th>
                                        <th>Modelo</th>
                                        <th>Serie</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($componentes as $comp): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($comp['fecha'])) ?></td>
                                            <td><?= htmlspecialchars($comp['descripcion']) ?></td>
                                            <td><?= htmlspecialchars($comp['modelo']) ?></td>
                                            <td><?= htmlspecialchars($comp['no_serie']) ?: '-' ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-4">
                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                            <p class="mt-2 text-muted">
                                No hay componentes registrados para este cliente este mes
                            </p>
                        </div>
                    <?php endif; ?>
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
                                    <div class="card-header">Información del Equipo</div>
                                    <div class="card-body">
                                        <p><span class="info-label">Modelo:</span><?= htmlspecialchars($equipo['modelo'] ?: 'Sin modelo') ?></p>
                                        <p><span class="info-label">Número de Serie: </span><?= htmlspecialchars($equipo['no_serie']) ?></p>
                                        <hr>
                                        <p>
                                            <span class="info-label">Reportes:</span>
                                            <?php if($total_reportes > 0): ?>
                                                <a href="../reportes/reportes.php?tab=pendiente&id_equipo=<?= $equipo['id_equipo'] ?>">
                                                    <?= $total_reportes ?>
                                                </a>
                                            <?php else: ?>
                                                <span>0 reportes</span>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                    <div class="card-footer">
                                        <a href="../equipos/ver_equipo.php?id=<?= $equipo['id_equipo'] ?>" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Ver Equipo
                                        </a>
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