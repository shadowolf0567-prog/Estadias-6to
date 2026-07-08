<?php
session_start();
require_once __DIR__ .'/../../config/db.php';

if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] !=1 && $_SESSION['tip_usr'] !=2)){
    header('Location: ../login.php?error=' . urlencode('Acceso denegado'));
    exit;
}

function buscar_reportes_filtros($filtros = [], $estado = 'pendiente'){
    global $conn;
    if(!$conn) return [];

    $sql = "SELECT r.*,
            c.nombre as cliente_nombre,
            c.no_cuenta as cliente_cuenta,
            e.no_serie as equipo_serie,
            e.modelo as equipo_modelo
        FROM reportes r
        LEFT JOIN clientes c ON r.id_cliente = c.id_cliente
        LEFT JOIN equipos e ON r.id_equipo = e.id_equipo
        WHERE r.estado = ?";

    $params = [$estado];
    $types = "s";

    if(!empty($filtros['reporte'])){
        $sql .= " AND r.reporte LIKE ?";
        $params[] = "%" . $filtros['reporte'] . "%";
        $types .= "s";
    }
    if(!empty($filtros['cliente'])){
        $sql .= " AND c.nombre LIKE ?";
        $params[] = "%" . $filtros['cliente'] . "%";
        $types .= "s";
    }
    if(!empty($filtros['no_serie'])){
        $sql .= " AND e.no_serie LIKE ?";
        $params[] = "%" . $filtros['no_serie'] . "%";
        $types .= "s";
    }

    $sql .= " ORDER BY r.id_reporte DESC";

    $stmt = mysqli_prepare($conn,$sql);
    if(!empty($params)){
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }

    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $reportes = [];
    while($fila = mysqli_fetch_assoc($resultado)){
        $reportes[] = $fila;
    }

    mysqli_stmt_close($stmt);
    return $reportes;
}

function contar_reportes_por_estado(){
    global $conn;
    $sql = "SELECT estado,COUNT(*) as total FROM reportes
            GROUP BY estado";
    $resultado = mysqli_query($conn,$sql);
    $contadores = ['pendiente' => 0, 'atendido' => 0];
    while($fila = mysqli_fetch_assoc($resultado)){
        $contadores[$fila['estado']] = $fila['total'];
    }
    return $contadores;
}

$clientes =[];
$sql_clientes = "SELECT id_cliente, nombre FROM clientes
                ORDER BY nombre ASC";
$result_clientes = mysqli_query($conn,$sql_clientes);
while($row = mysqli_fetch_assoc($result_clientes)){
    $clientes[] = $row;
}

$equipos = [];
$sql_equipos = "SELECT id_equipo, no_serie FROM equipos
                ORDER BY no_serie ASC";
$result_equipos = mysqli_query($conn,$sql_equipos);
while($row = mysqli_fetch_assoc($result_equipos)){
    $equipos[] =$row;
}

$tab_activa = isset($_GET['tab']) ? $_GET['tab'] : 'pendiente';
$filtros = [
    'reporte' => isset($_GET['reporte']) ? trim($_GET['reporte']) : '',
    'descripcion' => isset($_GET['descripcion']) ? trim($_GET['descripcion']) : '',
    'fecha_desde' => isset($_GET['fecha_desde']) ? trim($_GET['fecha_desde']) : '',
    'fecha_hasta' => isset($_GET['fecha_hasta']) ? trim($_GET['fecha_hasta']) : '',
    'tecnico' => isset($_GET['tecnico']) ? trim($_GET['tecnico']) : '',
    'cliente' => isset($_GET['cliente']) ? trim($_GET['cliente']) : '',
    'no_serie' => isset($_GET['no_serie']) ? trim($_GET['no_serie']) : ''
];

$reportes = buscar_reportes_filtros($filtros, $tab_activa);
$contadores = contar_reportes_por_estado();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes</title>
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/responsives.css">
    <style>
        .filtros-card{
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .nav-tabs .nav-link.pendiente.active {
            background-color: #dc3545;
            color: white;
        }
        .nav-tabs .nav-link.atendido.active {
            background-color: #28a745;
            color: white;
        }
        .badge-filtro{
            background-color: #e9ecef;
            color: #495057;
            padding: 5px 10px;
            border-radius: 20px;
            margin-right: 8px;
            margin-bottom: 5px;
            display: inline-block;
        }
        .badge-estado{
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
        }
        .badge-pendiente{
            background-color: #dc3545;
            color: white;
        }
        .badge-atendido{
            background-color: #28a745;
            color: white;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../gestion/menu.php'; ?>
    <div class="container mt-4">
        <h2 class="mb-4">Reportes</h2>
        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_GET['msg']) ?></div>
        <?php endif; ?>
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a href="?tab=pendiente" 
                class="nav-link pendiente <?= $tab_activa == 'pendiente' ? 'active' : '' ?>">
                    <i class="bi bi-clock-history"></i> Pendientes
                    <span class="badge bg-danger"><?= $contadores['pendiente'] ?></span>
                </a>
            </li>
            <li class="nav-item">
                <a href="?tab=atendido" 
                class="nav-link atendido <?= $tab_activa == 'atendido' ? 'active' : '' ?>">
                    <i class="bi bi-check-circle"></i> Atendidos
                    <span class="badge bg-success"><?= $contadores['atendido'] ?></span>
                </a>
            </li>
        </ul>
        <div class="filtros-card">
            <h5 class="mb-3"><i class="bi bi-funnel"></i> Filtros de búsqueda</h5>
            <form action="" method="get" id="formFiltros">
                <input type="hidden" name="tab" value="<?= $tab_activa ?>">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Título del reporte</label>
                        <input type="text" name="reporte" class="form-control"
                        value="<?= htmlspecialchars($filtros['reporte']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cliente</label>
                        <input type="text" name="cliente" class="form-control"
                        value="<?= htmlspecialchars($filtros['cliente']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Número de Serie</label>
                        <input type="text" name="no_serie" class="form-control"
                        value="<?= htmlspecialchars($filtros['no_serie']) ?>">
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                        <a href="?tab=<?= $tab_activa ?>" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Limpiar filtros
                        </a>
                    </div>
                </div>
            </form>
            <?php if(!empty($filtros['reporte']) || !empty($filtros['descripcion']) || !empty($filtros['fecha_desde']) ||
                    !empty($filtros['fecha_hasta']) || !empty($filtros['tecnico']) || !empty($filtros['cliente']) ||
                    !empty($filtros['no_serie'])): ?>
                <div class="mt-3 pt-2 border-top">
                    <span class="text-muted me-2">Filtros activos:</span>
                    <?php if(!empty($filtros['reporte'])): ?>
                        <span class="badge-filtro">Titulo: 
                            <?= htmlspecialchars($filtros['reporte']) ?>
                            <i class="bi bi-x-circle" onclick="removerFiltro('reporte')"></i>
                        </span>
                    <?php endif; ?>
                    <?php if(!empty($filtros['descripcion'])): ?>
                        <span class="badge-filtro">Descripción: 
                            <?= htmlspecialchars($filtros['descripcion']) ?>
                            <i class="bi bi-x-circle" onclick="removerFiltro('descripcion')"></i>
                        </span>
                    <?php endif; ?>
                    <?php if(!empty($filtros['tecnico'])): ?>
                        <span class="badge-filtro">Técnico: 
                            <?= htmlspecialchars($filtros['tecnico']) ?>
                            <i class="bi bi-c-circle" onclick="removerFiltro('tecnico')"></i>
                        </span>
                    <?php endif; ?>
                    <?php if(!empty($filtros['cliente'])): ?>
                        <span class="badge-filtro">Cliente:
                            <?= htmlspecialchars($filtros['cliente']) ?>
                            <i class="bi bi-x-circle" onclick="removerFiltro('cliente')"></i>
                        </span>
                    <?php endif; ?>
                    <?php if(!empty($filtros['no_serie'])): ?>
                        <span class="badge-filtro">No. Serie: 
                            <?= htmlspecialchars($filtros['no_serie']) ?>
                            <i class="bi bi-x-circle" onclick="removerFiltro('no_serie')"></i>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div><i class="bi bi-info-circle"></i> Mostrando <strong><?= count($reportes) ?></strong> reporte(s)</div>
            <a href="agregar_reporte.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Nuevo Reporte</a>
        </div>
        <?php if($tab_activa == 'pendiente'): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Título</th>
                            <th>Cliente</th>
                            <th>No. Serie</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($reportes) > 0): ?>
                            <?php foreach($reportes as $reporte): ?>
                                <tr class="<?= $reporte['estado'] == 'atendido' ? 'atendido-row' : '' ?>">
                                    <td><?= htmlspecialchars(substr($reporte['reporte'],0,40)) ?></td>
                                    <td><?= htmlspecialchars($reporte['cliente_nombre'] ?: '-') ?></td>
                                    <td><?= htmlspecialchars($reporte['equipo_serie'] ?: '-')  ?></td>
                                    <td><?= date('d/m/Y', strtotime($reporte['fecha'])) ?></td>
                                    <td>
                                        <span class="badge-estado badge-pendiente">
                                            <i class="bi bi-clock"></i> Pendiente
                                        </span>
                                    </td>
                                    <td>
                                        <form action="../lib/gestion_reportes.php" method="post"
                                        style="display: inline-block;" onsubmit="return confirm('¿Eliminar este reporte?')">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id_reporte" value="<?= $reporte['id_reporte'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger" title="eliminar">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                        </form>
                                        <a href="ver_reporte.php?id=<?= $reporte['id_reporte'] ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> Ver Reporte
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size:400px;"></i>
                                    <p class="mt-2">No se encontraron reportes con los filtros utilizados.</p>
                                    <a href="?tab=<?= $tab_activa ?>" class="btn btn-sm btn-primary">Limpiar filtros</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Título</th>
                            <th>Cliente</th>
                            <th>No. Serie</th>
                            <th>Fecha</th>
                            <th>Técnico</th>
                            <th>Refacción</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(count($reportes) > 0): ?>
                            <?php foreach($reportes as $reporte): ?>
                                <tr class="<?= $reporte['estado'] == 'atendido' ? 'atendido-row' : '' ?>">                                    
                                    <td><?= htmlspecialchars(substr($reporte['reporte'],0,40)) ?>...</td>
                                    <td>
                                        <?= htmlspecialchars($reporte['cliente_nombre']) ?>
                                    </td>
                                    <td>
                                        <?php if($reporte['equipo_serie']): ?>
                                            <?= htmlspecialchars($reporte['equipo_serie']) ?>
                                        <?php else: ?>
                                            <span class="text-muted">No especificado</span>
                                        <?php endif; ?>        
                                    </td>
                                    <td><?= date('d/m/Y',strtotime($reporte['fecha'])) ?></td>
                                    <td>
                                        <?php if($reporte['tecnico']): ?>
                                            <span class="tecnico-badge"><?= htmlspecialchars($reporte['tecnico']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($reporte['refaccion']): ?>
                                            <?= htmlspecialchars(substr($reporte['refaccion'],0,25)) ?>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge-estado badge-atendido">
                                            <i class="bi bi-check-circle"></i> Atendido
                                        </span>
                                    </td>
                                    <td>
                                        <form action="../lib/gestion_reportes.php" method="post"
                                         style="display: inline-block;" onsubmit="return confirm('¿Eliminar este reporte?')">
                                            <input type="hidden" name="accion" value="eliminar2">
                                            <input type="hidden" name="id_reporte" value="<?= $reporte['id_reporte'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Eliminar">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </button>
                                        </form>
                                        <a href="ver_reporte.php?id=<?= $reporte['id_reporte'] ?>" 
                                        class="btn btn-sm btn-info" title="Ver_detalle">
                                        <i class="bi bi-eye"></i> Ver Reporte
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 48px"></i>
                                    <p class="mt-2">No se encontraron reportes con los filtros seleccionados.</p>
                                    <a href="?tab=<?= $tab_activa ?>" class="btn btn-sm btn-primary">Limpiar filtros</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
    <script>
        function removerFiltro(campo){
            const url = new URL(window.location.href);
            url.searchParams.delete(campo);
            window.location.href = url.toString();
        }
    </script>
</body>
</html>
