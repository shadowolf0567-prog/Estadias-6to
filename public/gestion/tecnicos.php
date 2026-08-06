<?php
session_start();
require_once __DIR__ . '/../lib/validar_sesion.php';
require_once __DIR__ . '/../../config/db.php';
if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] !=1 && $_SESSION['tip_usr'] !=2 && $_SESSION['tip_usr' ] != 3)){
    header('Location: ../clientes/clientes.php?error=' .urlencode('Acceso denegado'));
    exit;
}
if(!$conn){
    die("Error de conexión a la base de datos");
}
$inventario = [];
$sql_inv = "SELECT distinct t.*, count(distinct fecha) FROM tecnicos t
        INNER JOIN reportes r
    ON t.id = r.id
WHERE t.id > 4
    GROUP BY nombre
        ORDER BY id ASC";
$resultado = mysqli_query($conn, $sql_inv);
if($resultado){
    while($row = mysqli_fetch_assoc($resultado)){
        $inventario[] = $row;
    }
}
$mes = isset($_GET['mes']) ? intval($_GET['mes']) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/responsibe.css">
    <style>
        .table-responsive {
            margin-top: 20px;
        }
        .btn-accion {
            margin: 0 2px;
        }
        .info-adicional {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .resaltar {
            background-color: yellow;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/menu.php'; ?>
    <div class="container mt-4">
        <h2 class="mb-4">Inventario</h2>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAgregar">
            <i class="bi bi-plus-circle"></i> Agregar
        </button>
        <div class="col-md-12">
            <form action="" method="get" class="mb-3">
                <input type="hidden">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="" class="form-label">Filtrar por mes</label>
                        <select name="mes" class="form-select" onchange="this.form.submit()">
                            <option value="hoy">-- Este Mes --</option>
                            <option value="1" <?= ($mes == 1) ? 'selected' : '' ?>>Enero</option>
                            <option value="2" <?= ($mes == 2) ? 'selected' : '' ?>>Febrero</option>
                            <option value="3" <?= ($mes == 3) ? 'selected' : '' ?>>Marzo</option>
                            <option value="4" <?= ($mes == 4) ? 'selected' : '' ?>>Abril</option>
                            <option value="5" <?= ($mes == 5) ? 'selected' : '' ?>>Mayo</option>
                            <option value="6" <?= ($mes == 6) ? 'selected' : '' ?>>Junio</option>
                            <option value="7" <?= ($mes == 7) ? 'selected' : '' ?>>Julio</option>
                            <option value="8" <?= ($mes == 8) ? 'selected' : '' ?>>Agosto</option>
                            <option value="9" <?= ($mes == 9) ? 'selected' : '' ?>>Septiembre</option>
                            <option value="10" <?= ($mes == 10) ? 'selected' : '' ?>>Octubre</option>
                            <option value="11" <?= ($mes == 11) ? 'selected' : '' ?>>Noviembre</option>
                            <option value="12" <?= ($mes == 12) ? 'selected' : '' ?>>Diciembre</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Reportes del mes</th>
                        <th>Reportes del año</th>
                    </tr>
                </thead>
                <tbody>
                        <?php foreach($inventario as $inv): ?>
                            <tr>         
                                <td><?= htmlspecialchars($inv['nombre']) ?></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="modalAgregar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content"> 
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        Agregar Técnico
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../lib/gestion_almacen.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="tecnico">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control">
                        </div>
                    </div>
                    <div class="modal-footer"> 
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalEditar" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title">
                        Editar 
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../lib/gestion_almacen.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="editarTecnico">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" id="edit_nombre">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">
                            <i class="bi bi-pencil"></i> Editar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>