<?php
session_start();
require_once __DIR__ . '/../lib/validar_sesion.php';
require_once __DIR__ . '/../../config/db.php';

if(!$conn){
    die("Error de conexión a la base de datos");
}
$inventario = [];
$sql_inv = "SELECT * FROM almacen ORDER BY id DESC";
$resultado = mysqli_query($conn, $sql_inv);
if($resultado){
    while($row = mysqli_fetch_assoc($resultado)){
        $inventario[] = $row;
    }
}
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
                        <?php foreach($inventario as $inv): ?>
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
                            <input type="number" name="cantidad" class="form-control" id="edit_cantidad">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Información Adicional</label>
                            <input type="text" name="info_adicional" id="edit_info_adicional" class="form-control">
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
            document.getElementById('edit_cantidad').value = cantidad;
            document.getElementById('edit_info_adicional').value = info_adicional || '';
        }
    </script>
</body>
</html>