<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] !=1 && $_SESSION['tip_usr'] !=2 && $_SESSION['tip_usr' ] != 3)){
    header('Location: ../login.php?error=' . urlencode('Acceso denegado'));
    exit;
}

if(!isset($_GET['id']) || empty($_GET['id'])){
    header('Location: reportes.php?error=' . urlencode('ID de reporte no especificado'));
    exit;
}

$id_reporte = intval($_GET['id']);

$sql = "SELECT r.*, t.nombre as tecnico,
               c.id_cliente, c.nombre as cliente_nombre, c.no_cuenta as cliente_cuenta, c.encargado as encargado,
               e.id_equipo, e.no_serie as equipo_serie, e.modelo as equipo_modelo, t.nombre as tecnico
        FROM reportes r
        LEFT JOIN clientes c ON r.id_cliente = c.id_cliente
        LEFT JOIN equipos e ON r.id_equipo = e.id_equipo
        LEFT JOIN tecnicos t ON r.id = t.id
        WHERE r.id_reporte = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $id_reporte);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);
$reporte = mysqli_fetch_assoc($resultado);

$componentes_reporte = [];
            if($reporte['id_reporte']){
                $sql_comp = "SELECT rc.*, rc.componente as componente_nombre,
                            rc.descripcion as componente_descripcion
                            FROM reportes_componentes rc
                            WHERE rc.id_reporte = ?";
                $stmt_comp = mysqli_prepare($conn,$sql_comp);
                mysqli_stmt_bind_param($stmt_comp,'i',$reporte['id_reporte']);
                mysqli_stmt_execute($stmt_comp);
                $result_comp = mysqli_stmt_get_result($stmt_comp);
                while($row=mysqli_fetch_assoc($result_comp)){
                    $componentes_reporte[] = $row;
                }
                mysqli_stmt_close($stmt_comp);
            }

if(!$reporte){
    header('Location: reportes.php?error=' . urlencode('Reporte no encontrado'));
    exit;
}
$id_reabierto = intval($_GET['id']);
$sql_rea = "SELECT * from reabiertos WHERE id = ?";
$stmt_rea = mysqli_prepare($conn,$sql_rea);
mysqli_stmt_bind_param($stmt_rea,"i",$id_reabierto);
mysqli_stmt_execute($stmt_rea);
$result_rea = mysqli_stmt_get_result($stmt_rea);
$reabiertos = mysqli_fetch_assoc($result_rea);

$atenciones = [];
$sql_at = "SELECT * from reabiertos WHERE reportes = ?";
$stmt_at = mysqli_prepare($conn,$sql_at);
mysqli_stmt_bind_param($stmt_at,'i',$reporte['id_reporte']);
mysqli_stmt_execute($stmt_at);
$result_at = mysqli_stmt_get_result($stmt_at);
while($row = mysqli_fetch_assoc($result_at)){
    $atenciones[] = $row;
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Reporte</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/ressponsibe.css">
    <style>
        body { background-color: #f4f6f9; }
        .reporte-header {
            padding: 20px;
            border-radius: 10px 10px 0 0;
        }
        .info-card {
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .info-card .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            font-weight: bold;
        }
        .estado-badge {
            font-size: 14px;
            padding: 8px 15px;
            border-radius: 20px;
        }
        .estado-pendiente{ 
            background-color: #dc3545; 
            color: white; 
        }
        .estado-atendido { 
            background-color: #28a745; 
            color: white; 
        }
        .estado-incompleto{
            background-color: #ffc107;
            color: #212529;
        }
        .atencion-box {
            background-color: #e8f5e9;
            border-left: 4px solid #28a745;
            padding: 15px;
            border-radius: 5px;
            margin-top: 10px;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
            width: 140px;
            display: inline-block;
        }
        @media (max-width: 768px) {
            .info-label {
                width: 100%;
                display: block;
                margin-bottom: 3px;
            }
            .reporte-header h2 {
                font-size: 1.3rem;
            }
            .btn-group-responsive {
                display: flex;
                flex-wrap: wrap;
                gap: 5px;
            }
            .btn-group-responsive .btn {
                flex: 1;
                min-width: 60px;
                font-size: 0.8rem;
                padding: 5px 10px;
            }
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../gestion/menu.php'; ?>
    
    <div class="container mt-4">
        <div class="no-print mb-3">
            <a href="reportes.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <?php if($reporte['estado'] != 'atendido'): ?>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalAtender">
                    <i class="bi bi-check-circle"></i> Marcar Atendido
                </button>
                <a href="editar_reportes.php?id_reporte=<?= $reporte['id_reporte'] ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar
                </a>
            <?php else: ?>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalIncompleto">
                    <i class="bi bi-arrow-repeat"></i> Sin Concluir
                </button>
                <a href="editar_atendido.php?id_reporte=<?= $reporte['id_reporte'] ?>" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Editar
                </a>
            <?php endif; ?>
        </div>
        
        <div class="reporte-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h2 class="mb-0">Reporte</h2>
                    <span class="text-muted">Referencia: <?= htmlspecialchars($reporte['referencia'] ?? '-') ?></span>
                </div>
                <div class="text-end mt-2 mt-md-0">
                    <div class="estado-badge estado-<?= $reporte['estado'] ?>">
                        <?= ucfirst($reporte['estado']) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card info-card">
                    <div class="card-header">Cliente que reporta</div>
                    <div class="card-body">
                        <?php if($reporte['cliente_nombre']): ?>
                            <p><span class="info-label">Empresa:</span> <?= htmlspecialchars($reporte['cliente_nombre']) ?></p>
                            <p><span class="info-label">Encargado:</span> <?= htmlspecialchars($reporte['encargado']) ?? ''?></p>
                            <p><span class="info-label">Cuenta:</span> <?= htmlspecialchars($reporte['cliente_cuenta'] ?: 'No registrada') ?></p>
                        <?php else: ?>
                            <p class="text-muted">No se especificó cliente</p>
                        <?php endif; ?>
                        <a href="../clientes/ver_cliente.php?id=<?= $reporte['id_cliente'] ?>" class="btn btn-info">
                            <i class="bi bi-eye"></i> Ver Cliente
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card info-card">
                    <div class="card-header">Equipo relacionado</div>
                    <div class="card-body">
                        <?php if($reporte['equipo_serie']): ?>
                            <p><span class="info-label">Serie:</span> <?= htmlspecialchars($reporte['equipo_serie']) ?></p>
                            <p><span class="info-label">Modelo:</span> <?= htmlspecialchars($reporte['equipo_modelo']) ?></p>
                        <?php else: ?>
                            <p class="text-muted">No se especificó equipo</p>
                        <?php endif; ?>
                        <a href="../equipos/ver_equipo.php?id=<?=$reporte['id_equipo'] ?>" class="btn btn-info">
                            <i class="bi bi-eye"></i> Ver Equipo
                        </a>
                    </div>
                </div>
            </div>
                <div class="col-md-12 mt-3">
                    <div class="card info-card">
                        <div class="card-header">
                            <div class="row g-3">
                                <div class="col-md-4">
                                Detalles del Reporte
                            </div>
                            <?php if($reporte['estado'] == 'atendido'): ?>
                            <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <p><span class="info-label">Fecha de apertura:</span> <?= date('d/m/Y', strtotime($reporte['fecha'])) ?></p>
                            <?php if(count($componentes_reporte) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Componente</th>
                                                <th>Descripción</th>
                                                <th>Técnico</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($componentes_reporte as $comp): ?>
                                                <tr>
                                                    <td><strong><?= htmlspecialchars($comp['componente_nombre']) ?></strong></td>
                                                    <td><?= htmlspecialchars($comp['componente_descripcion'] ?? '-') ?></td>
                                                    <td><?= htmlspecialchars($reporte['tecnico']) ?: '-' ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        <?php if($reporte['estado'] == 'atendido' || $reporte['estado'] == 'incompleto'): ?>
                        <div class="col-12">
                            <hr>
                            <div class="row g-3">
                                <?php foreach($atenciones as $atencion): ?>
                                <div class="col-md-4">
                                    <p><span class="info-label">Fecha: </span><?= date('d/m/Y', strtotime($atencion['fechas'])) ?></p>
                                    <p><span class="info-label">¿Qué se hizo?</span> <?= htmlspecialchars($atencion['acciones']) ?></p>
                                    <p><span class="info-label">Observaciones: </span></p>
                                    <div class="bg-white p-2 rounded"><?= nl2br(htmlspecialchars($atencion['observaciones'])) ?></div>
                                    <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $atencion['id'] ?>">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="../lib/gestion_reportes.php" method="post" onsubmit="return confirm('¿Quieres eliminar esto?')" style="display:inline-block;">
                                        <input type="hidden" name="accion" value="borrar">
                                        <input type="hidden" name="id" value="<?= $atencion['id'] ?>">
                                        <input type="hidden" name="id_reporte" value="<?= $reporte['id_reporte'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="modal fade" id="modalEditar<?= $atencion['id'] ?>">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header bg-warning">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-pencil"></i> Editar
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="../lib/gestion_reportes.php" method="post">
                                                <div class="modal-body">
                                                    <input type="hidden" name="accion" value="editar_reabierto">
                                                    <input type="hidden" name="id" value="<?= $atencion['id'] ?>">
                                                    <input type="hidden" name="id_reporte" value="<?= $reporte['id_reporte'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Fecha:</label>
                                                        <input type="date" name="fechas" class="form-control" value="<?= $atencion['fechas'] ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">¿Qué se hizo?</label>
                                                        <input type="text" name="acciones" class="form-control" value="<?= htmlspecialchars($atencion['acciones'] ?? '') ?>">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Observaciones</label>
                                                        <textarea name="observaciones" class="form-control" rows="3"><?= htmlspecialchars($atencion['observaciones'] ?? '') ?></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-warning">Guardar Cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                                <?php if($reporte['estado'] === 'atendido'): ?>
                                    <div class="col-md-4">
                                        <div class="card-info-card bg-success-subtle" style="border-radius: 8px; padding: 10px;">
                                            <h6><i class="bi bi-check-circle"></i> Reporte Atendido</h6>
                                            <p><span class="info-label">Fecha: </span><?= date('d/m/Y', strtotime($reporte['fecha_atencion']))?></p>
                                            <p><span class="info-label">¿Qué se hizo?</span> <?= htmlspecialchars($reporte['acciones']) ?: '-' ?></p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalAtender">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-check-circle"></i> Marcar como Atendido
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../lib/gestion_reportes.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="marcar_atendido">
                        <input type="hidden" name="id_reporte" value="<?= $reporte['id_reporte'] ?>">
                        <p>¿Confirmas que este reporte ha sido atendido?</p>
                        <div class="mb-3">
                            <label class="form-label">Fecha de atención:</label>
                            <input type="date" name="fecha_atencion" class="form-control" value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">¿Qué se hizo?:</label>
                            <textarea name="acciones" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Marcar Atendido</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalIncompleto">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">
                        <i class="bi bi-arrow-repeat"> Marcar Incompleto/Reabierto </i>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="../lib/gestion_reportes.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="reabrir">
                        <input type="hidden" name="id_reporte" value="<?= $reporte['id_reporte'] ?>">
                        <p></p>
                        <div class="mb-3">
                            <label class="form-label">Fecha de atención</label>
                            <input type="date" name="fechas" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">¿Qué se hizo?</label>
                            <input type="text" name="acciones" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea name="observaciones" class="form-control" placeholder="Observaciones realizadas durante el servicio"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Marcar Incompleto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script>
        function abrirModalAtender() {
            var modal = new bootstrap.Modal(document.getElementById('modalAtender'));
            modal.show();
        }
        
        function abrirModalReabrir() {
            var modal = new bootstrap.Modal(document.getElementById('modalReabrir'));
            modal.show();
        }
        
        document.addEventListener('keydown', function(e) {
            if(e.key === 'Escape') {
                var modales = document.querySelectorAll('.modal.show');
                modales.forEach(function(modal) {
                    var bsModal = bootstrap.Modal.getInstance(modal);
                    if(bsModal) bsModal.hide();
                });
            }
        });
        function abrirModalIncompleto(id_reporte) {
        // Asignar el ID al campo oculto
            document.getElementById('modal_reabrir').value = id_reporte;
            var modal = new bootstrap.Modal(document.getElementById('modalIncompleto'));
            modal.show();
        }
    </script>
</body>
</html>