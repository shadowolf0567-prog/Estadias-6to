<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../lib/gestion_clientes.php';
if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] != 1 && $_SESSION['tip_usr'] != 2)){
    header('Location: ../login.php?error='.urlencode('Acceso denegado'));
    exit;
}
if(!isset($_GET['id_cliente']) || empty($_GET['id_cliente'])){
    header('Location: cliente.php?error='.urlencode('ID de cliente no especificada'));
    exit;
}
$id_cliente = intval($_GET['id_cliente']);
$cliente=  obtener_cliente_completo($id_cliente);
if(!$cliente){
    header('Location: clientes.php?error='.urlencode('Cliente no encontrado'));
    exit;
}
$error = isset($_GET['error']) ? $_GET['error'] : '';
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cliente</title>
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <style>
        .form-section{
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .telefono-item, .correo-item{
            background: white;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }
        .btn-remover{
            color: #dc3545;
            cursor: pointer;
        }
        .btn-remover:hover{
            color: #a71d2a;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ .'/../gestion/menu.php';?>
    <div class="container mt-4">
        <h2>Editar Cliente</h2>
        <?php if($mensaje): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <form action="../lib/gestion_clientes.php" method="post" id="formCliente">
            <input type="hidden" name="accion" value="editar_conpleto">
            <input type="hidden" name="id_cliente" value="<?= $cliente['id_cliente'] ?>">
            <div class="form-section">
                <h5>Datos del Cliente</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" required value="<?= htmlspecialchars($cliente['nombre']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label>Número de Cuenta</label>
                        <input type="text" name="no_cuenta" class="form-control" value="<?= htmlspecialchars($cliente['no_cuenta']) ?>" required>
                    </div>
                    <div class="col-md-12">
                        <label>Dirección</label>
                        <textarea name="direccion" class="form-control" required><?= htmlspecialchars($cliente['direccion']) ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label>Inicio de Contrato</label>
                        <input type="date" name="inicio_contrato" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Fin de Contrato</label>
                        <input type="date" name="fin_contrato" class="form-control">
                    </div>
                </div>
            </div>
            <div class="form-section">
                <h5>Teléfonos</h5>
                <div id="telefonosContainer">
                    <?php if(count($cliente['telefonos']) > 0): ?>
                        <?php foreach($cliente['telefonos'] as $index => $telefono): ?>
                            <div class="telefono-item">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <input type="text" name="telefonos[<?= $index ?>][numero]" class="form-control" value="<?= htmlspecialchars($telefono['telefono']) ?>">
                                    </div>
                                    <div class="col-md-4">
                                        <input type="text" name="telefonos[<?= $index ?>][contacto]" class="form-control" value="<?= htmlspecialchars($telefono['telefono']) ?>">
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check mt-2">
                                            <input type="checkbox" name="telefonos[<?= $index ?>][es_principal]" class="form-check-input" value="1" <?= $telefono['es_principal'] ? 'checked' : '' ?>>
                                            <label>Principal</label>
                                        </div>
                                    </div>
                                    <div class="col-md 1">
                                        <i class="bi bi-dash-circle btn-remover" onclick="removerItem(this, 'telefonosContainer')" style="font-size: 24px;"></i>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="telefono-item">
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <input type="text" name="telefonos[0][numero]" class="form-control" placeholder="Número de teléfono">
                                </div>
                                <div class="col-md-4">
                                    <input type="text" name="telefonos[0][contacto]" class="form-control" placeholder="Titular">
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check mt-2">
                                        input:checkbox
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif ?>
                </div>
            </div>
        </form>
    </div>
</body>
</html>