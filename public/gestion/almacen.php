<?php
session_start();
require_once __DIR__ .'/../../config/db.php';

if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] !=1 && $_SESSION['tip_usr'] !=2 && $_SESSION['tip_usr' ] != 3)){
    header('Location: ../login.php?error=' . urlencode('Acceso denegado'));
    exit;
}
$inventario = [];
$sql_inv = "SELECT * FROM almacen ORDER BY id DESC";
$resultado = mysqli_query($conn, $sql_inv);
if($resultado){
    while($row = mysqli_fetch_assoc($resultado)){
        $inventario[] = $row;
    }
}
function buscar($termino = ''){
    global $conn;
    if(!$conn){
        return[];
    }
    if(empty($termino)){
        $sql = "SELECT * FROM almacen ORDER BY id DESC";
        $resultado = mysqli_query($conn,$sql);
    }else{
        $termino = mysqli_real_escape_string($conn,$termino);
        $sql = "SELECT * from almacen WHERE nombre LIKE '%$termino%'";
        $resultado = mysqli_query($conn,$sql);
        if(!$resultado){
            return[];
        }
    }
    $productos = [];
    if($resultado && mysqli_num_rows($resultado) > 0){
        while($fila = mysqli_fetch_assoc($resultado)){
            $productos[] = $fila;
        }
    }
    return $productos;
}
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$productos = buscar($busqueda);
$mostrar = (!empty($busqueda)) ? $productos : $inventario;
function resaltar_coincidencias($texto, $busqueda) {
    if(empty($busqueda) || empty($texto)){
        return htmlspecialchars($texto);
    }
    $texto = htmlspecialchars($texto);
    $busqueda = htmlspecialchars($busqueda);
    return preg_replace('/(' . preg_quote($busqueda, '/') . ')/i', '<span class="resaltar">$1</span>', $texto);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario</title>
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
        <div class="busqueda-container">
            <form action="" method="get" id="formBusqueda">
                <div class="row g-3">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text" name="buscar" id="buscarInput" 
                            class="form-control" value="<?= htmlspecialchars($busqueda); ?>"
                            autocomplete="off">
                            <?php if(!empty($busqueda)): ?>
                                <a href="almacen.php" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Limpiar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-md w-100">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
            <?php if(!empty($busqueda)): ?>
                <div class="mt-2">
                    <i class="bi bi-info-circle"></i> 
                    Mostrando <strong><?= count($productos) ?></strong> resultado(s) para "<strong><?= htmlspecialchars($busqueda) ?></strong>"
                </div>
            <?php endif; ?>
        </div>
        <button type="button" class="btn btn-success  mt-2" data-bs-toggle="modal" data-bs-target="#modalAgregar">
            <i class="bi bi-plus-circle"></i> Agregar
        </button>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Serie</th>
                        <th>Info. Adicional</th>
                        <th>Cantidad</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($mostrar) > 0): ?>
                        <?php foreach($mostrar as $inv): ?>
                            <tr>         
                                <td><?= htmlspecialchars($inv['nombre']) ?></td>
                                <td><?= htmlspecialchars($inv['serie']) ?></td>
                                <td><?= htmlspecialchars($inv['info_adicional'] ?: '') ?></td>
                                <td><?= htmlspecialchars($inv['cantidad']) ?></td>
                                <td>
                                    <form action="../lib/gestion_almacen.php" method="post">
                                        <input type="hidden" name="accion" value="eliminar">
                                        <input type="hidden" name="id" value="<?= $inv['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger btn-accion">
                                            <i class="bi bi-trash"></i> 
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-sm btn-warning btn-accion"
                                        data-bs-toggle="modal" data-bs-target="#modalEditar"
                                        onclick="editarInventario(<?= $inv['id'] ?>, '<?= addslashes($inv['nombre']) ?>', '<?= addslashes($inv['serie']) ?>', '<?= addslashes($inv['info_adicional']) ?>')">
                                        <i class="bi bi-pencil"></i> Editar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p class="mt-2">No hay recursos en el inventario</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="modalAgregar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        Agregar Recurso
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../lib/gestion_almacen.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="agregar">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Serie</label>
                            <input type="text" name="serie" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Información Adicional</label>
                            <textarea name="info_adicional" class="form-control"></textarea>
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
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id" id="edit_id">
                        <div class="mb-3">
                            <label class="form-label">Nombre</label>
                            <input type="text" name="nombre" class="form-control" id="edit_nombre">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Serie</label>
                            <input type="text" name="serie" id="edit_serie" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Cantidad</label>
                            <input type="number" name="cantidad" class="form-control" id="edit_cantidad" min="0">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Información Adicional</label>
                            <textarea name="info_adicional" id="edit_info_adicional" class="form-control"></textarea>
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
    <script>
        function editarInventario(id,nombre,serie,cantidad,info_adicional){
            document.getElementById('edit_id').value=id;
            document.getElementById('edit_nombre').value = nombre;
            document.getElementById('edit_serie').value = serie || '';
            document.getElementById('edit_cantidad').value = cantidad || 0;
            document.getElementById('edit_info_adicional').value = info_adicional || '';
        }
    </script>
</body>
</html>