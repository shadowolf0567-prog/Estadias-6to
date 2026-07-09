<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] != 1 && $_SESSION['tip_usr'] != 2)){
    header('Location: ../login.php?error=' . urlencode('Acceso denegado'));
    exit;
}

function buscar_equipo($filtros = []){
    global $conn;

    if(!$conn){
        return [];
    }
    $sql = "SELECT e.*, c.nombre as cliente_nombre
            FROM equipos e
            LEFT JOIN clientes c ON e.id_cliente = c.id_cliente
            WHERE 1=1";
    $params = [];
    $types = "";

    if(!empty($filtros['no_serie'])){
        $sql .= " AND e.no_serie LIKE ?";
        $params[] = "%" . $filtros ['no_serie'] . "%";
        $types .= "s";
    }

    if(!empty($filtros['modelo'])){
        $sql .= " AND e.modelo LIKE ?";
        $params[] ="%" . $filtros['modelo'] . "%";
        $types .= "s";
    }

    if(!empty($filtros['id_cliente'])){
        $sql .= " AND e.id_cliente = ?";
        $params[] = $filtros['id_cliente'];
        $types .= "i";
    }

    if(!empty($filtros['cliente'])){
        $sql .= " AND c.nombre LIKE ?";
        $params[] = "%" . $filtros['cliente'] . "%";
        $types .= "s";
    }

    $sql .= " ORDER BY e.id_equipo DESC";

    $stmt = mysqli_prepare($conn,$sql);
    if(!empty($params)){
        mysqli_stmt_bind_param($stmt,$types,...$params);
    }

    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);

    $equipos = [];
    while($fila = mysqli_fetch_assoc($resultado)){
        $equipos[] = $fila;
    }
    mysqli_stmt_close($stmt);
    return $equipos;
}

$clientes = [];
$sql_clientes = "SELECT id_cliente,nombre FROM clientes
                ORDER BY nombre ASC";
$result_clientes = mysqli_query($conn, $sql_clientes);
if($result_clientes){
    while($row = mysqli_fetch_assoc($result_clientes)){
        $clientes[] = $row;
    }
}

$filtros = [
    'no_serie' => isset($_GET['no_serie']) ? trim($_GET['no_serie']) : '',
    'modelo' => isset($_GET['modelo']) ? trim($_GET['modelo']) : '',
    'id_cliente' => isset($_GET['id_cliente']) ? intval($_GET['id_cliente']) : '',
    'cliente' => isset($_GET['cliente']) ? trim($_GET['cliente']) : ''
];

$equipos = buscar_equipo($filtros);
$total_resultados = count($equipos);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Equipos</title>
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
        .btn-filtrar{
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
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
        .badge-filtro i {
            cursor: pointer;
            margin-left: 8px;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../gestion/menu.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">Equipos</h2>
        <div class="filtros-card">
            <h5 class="mb-3">
                <i class="bi bi-funnel"></i> Filtros de búsqueda
            </h5>
            <form action="" method="get" id="formFiltros">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Número de Serie</label>
                        <input type="text" name="no_serie" class="form-control"
                                value="<?= htmlspecialchars($filtros['no_serie']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Modelo</label>
                        <input type="text" name="modelo" class="form-control"
                                value="<?= htmlspecialchars($filtros['modelo']) ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Cliente</label>
                        <input type="text" name="cliente" class="form-control"
                        value="<?= htmlspecialchars($filtros['cliente']) ?>">
                    </div>
                    <div class="col-12 mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                        <a href="equipos.php" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Limpiar filtros
                        </a>
                    </div>
                </div>
            </form>
            <?php if(!empty($filtros['no_serie']) || !empty($filtros['modelo']) || !empty($filtros['id_cliente']) || !empty($filtros['cliente'])): ?>
                <div class="mt-3 pt-2 border-top">
                    <span class="text-muted me-2">Filtros activos:</span>
                    <?php if(!empty($filtros['no_serie'])): ?>
                        <span class="badge-filtro">
                            Serie: <?= htmlspecialchars($filtros['no_serie']) ?>
                            <i class="bi bi-x-circle" onclick="removerFiltro('no_serie')"></i>
                        </span>
                    <?php endif; ?>
                    <?php if(!empty($filtros['modelo'])): ?>
                        <span class="badge-filtro">
                            Modelo: <?= htmlspecialchars($filtros['modelo']) ?>
                            <i class="bi bi-x-circle" onclick="removerFiltro('modelo')"></i>
                        </span>
                    <?php endif; ?>
                    <?php if(!empty($filtros['id_cliente'])): ?>
                        <span class="badge-filtro">
                            Cliente: <?= htmlspecialchars($filtros['id_cliente']) ?>
                            <i class="bi bi-x-circle" onclick="removerFiltro('id_cliente')"></i>
                        </span>
                    <?php endif; ?>
                    <?php if(!empty($filtros['cliente'])): ?>
                        <span class="badge-filtro">
                            Cliente: <?= htmlspecialchars($filtros['cliente']) ?>
                            <i class="bi bi-x-circle" onclick="removerFiltro('cliente')"></i>
                        </span>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <i class="bi bi-info-circle"></i>
                Mostrando <strong><?= $total_resultados ?></strong> equipo(s)
            </div>
            <a href="agregar_equipo.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Nuevo Equipo
            </a>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Número de Serie</th>
                        <th>Modelo</th>
                        <th>Cliente</th>
                        <th>Inicio Contrato</th>
                        <th>Fin Contrato</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($total_resultados >0): ?>
                        <?php foreach($equipos as $equipo): ?>
                            <tr>
                                <td><?= htmlspecialchars($equipo['no_serie']) ?></td>
                                <td><?= htmlspecialchars($equipo['modelo'] ?: '-') ?></td>
                                <td>
                                    <?php if($equipo['cliente_nombre']): ?>
                                        <span>
                                            <?= htmlspecialchars($equipo['cliente_nombre']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span>Sin cliente</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y',strtotime($equipo['inicio_contrato'])) ?></td>
                                <td><?= date('d/m/Y',strtotime($equipo['fin_contrato'])) ?></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-danger"
                                    onclick="eliminarEquipo(<?= $equipo['id_equipo'] ?>, '<?= addslashes($equipo['no_serie']) ?>')">
                                        <i class="bi bi-trash"></i> Eliminar
                                    </button>
                                    <a href="ver_equipo.php?id=<?= $equipo['id_equipo'] ?>" class="btn btn-sm btn-info">
                                        <i class="bi bi-eye"></i> Ver Equipo
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="alert alert-warning m-3">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    No se encontraaron clientes
                                    <?php if(!empty($busqueda)): ?>
                                        para "<strong><?php echo htmlspecialchars($busqueda) ?></strong>"
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script>
        function removerFiltro(campo){
            const url = new URL(window.location.href);
            url.searchParams.delete(campo);
            window.location.href = url.toString();
        }

        function eliminarEquipo(id,serie){
            if(confirm(`¿Eliminar el equipo "${serie}"?`)){
                const form=document.createElement('form');
                form.method = 'POST';
                form.action = '../lib/gestion_equipo.php';
                form.innerHTML = `
                <input type="hidden" name = "accion" value="eliminar"> 
                <input type="hidden" name="id_equipo" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
</body>
</html>