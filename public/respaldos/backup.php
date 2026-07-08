<?php 
session_start();
require_once __DIR__.'/../../config/db.php';

if(!isset($_SESSION['tip_usr']) || $_SESSION['tip_usr'] != 1 && $_SESSION['tip_usr'] !=2 ){
    header('Location: ../login.php?error='. urlencode('Acceso denegado'));
    exit;
}

function crear_backup(){
    global $conn;

    $fecha = date('Y-m-d_H-i-s');
    $nombre_archivo = "backup_emipac_{$fecha}.sql";
    $ruta_backup = __DIR__ . "/../../backups/".$nombre_archivo;

    if(!is_dir(__DIR__ . "/../../backups")){
        mkdir(__DIR__ . "/../../backups", 0777, true);
    }
    $tablas = [];
    $sql = "SHOW TABLES";
    $result = mysqli_query($conn,$sql);

    if(!$result){
        return ['success' => false, 'error' => 'Error al obtener tablas: ' . mysqli_error($conn)]; 
    }

    while($row = mysqli_fetch_array($result)){
        $tablas[] = $row[0];
    }

    $backup_contenido = "-- Backup de Base de Datos Emipac\n";
    $backup_contenido .= "-- Fecha: " . date('Y-m-d H:i:s') . "\n";
    $backup_contenido .= "DROP DATABASE IF EXISTS emipac;\n";
    $backup_contenido .= "CREATE DATABASE emipac;\n";
    $backup_contenido .= "USE emipac;\n";
    $backup_contenido .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

    foreach($tablas as $tabla){
        $sql_estructura = "SHOW CREATE TABLE `$tabla`";
        $result_estructura=mysqli_query($conn,$sql_estructura);
        $row_estructura = mysqli_fetch_assoc($result_estructura);

        $backup_contenido .= "-- Estructura de tabla: $tabla\n";
        $backup_contenido .= "DROP TABLE IF EXISTS `$tabla`;\n";
        $backup_contenido .= $row_estructura['Create Table'] . ";\n\n";

        $sql_datos = "SELECT * FROM `$tabla`";
        $result_datos = mysqli_query($conn, $sql_datos);

        if(mysqli_num_rows($result_datos) > 0){
            $backup_contenido .= "--Datos de tabla: $tabla\n";

            while($fila = mysqli_fetch_assoc($result_datos)){
                $columnas = array_keys($fila);
                $valores = array_values($fila);

                $valores_escapados = array_map(function($valor) use ($conn){
                    if($valor === null) return 'NULL';
                    return "'" . mysqli_real_escape_string($conn, $valor) . "'";
                }, $valores);
                $backup_contenido .= "INSERT INTO `$tabla` (`" . implode("`, `",$columnas) . "`) VALUES (" . implode(", ", $valores_escapados) . ");\n";
            }
            $backup_contenido .= "\n";
        }
    }

    $backup_contenido .= "SET FOREIGN_KEY_CHECKS=1;\n";

    if(file_put_contents($ruta_backup, $backup_contenido)){
        return[
            'success' => true,
            'archivo' => $nombre_archivo,
            'ruta' => $ruta_backup,
            'tamaño' => round(filesize($ruta_backup) / 1024, 2) . ' KB'
        ];
    } else {
        return ['success' => false, 'error' => 'No se pudo guardar el archivo de backup'];
    }
}
if(isset($_GET['accion']) && $_GET['accion'] == 'crear_backup') {
    $resultado = crear_backup();

    if($resultado['success']) {
        header('Location: backup.php?msg=' . urlencode('Backup creado: ' . $resultado['archivo'] . ' - Tamaño ' . $resultado['tamaño']));
    } else {
        header('Location: backup.php?error=' . urlencode($resultado['error']));
    }
    exit;
}

$backups = [];
$ruta_backups = __DIR__ . "/../../backups/";
if(is_dir($ruta_backups)){
    $archivos = scandir($ruta_backups);
    foreach($archivos as $archivo){
        if($archivo != '.' && $archivo != '..' && pathinfo($archivo, PATHINFO_EXTENSION) == 'sql'){
            $ruta_completa = $ruta_backups . $archivo;
            $backups[] = [
                'nombre' => $archivo,
                'tamaño' => round(filesize($ruta_completa) / 1024, 2),
                'fecha' => date('Y-m-d H:i:s',filemtime($ruta_completa)),
                'ruta' => $ruta_completa
            ];
        }
    }
    usort($backups, function ($a, $b) {
        return strtotime($b['fecha']) - strtotime($a['fecha']);
    });
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Respaldo de Base de Datos</title>
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/responsive.css">
    <style>
        .backup-card{
            transition: transform 0.3s;
            margin-bottom: 20px;
        }
        .backup-card:hover{
            transform: translateY(-5px);
        }
        .tamaño-badge{
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../gestion/menu.php'; ?>

    <div class="container mt-4">
        <h2 class="mb-4">
            <i class="bi bi-database"></i> Respaldo de Base de Datos
        </h2>
        <?php if($mensaje): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <div class="card mb-4">
            <div class="card-body text-center">
                <h5 class="card-title">
                    <i class="bi bi-save"></i> Crear Nuevo Respaldo
                </h5>
                <p class="card-text">
                    Genera un respaldo completo de toda la base de datos incluyendo estructura y datos.
                </p>
                <a href="?accion=crear_backup" class="btn btn-primary btn-lg"
                onclick="return confirm('¿Deseas crear un nuevo respaldo? Esto puede tomar unos segundos.');">
                    <i class="bi bi-database-add"></i> Crear Respaldo Ahora
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="bi bi-archive"></i> Respaldos Guardados
                </h5>
            </div>
            <div class="card-body">
                <?php if(count($backups) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre del Archivo</th>
                                    <th>Fecha de Creación</th>
                                    <th>Tamaño</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($backups as $backup): ?>
                                    <tr>
                                        <td>
                                            <i class="bi bi-filetype-sql"></i>
                                            <?= htmlspecialchars($backup['nombre']) ?>
                                        </td>
                                        <td>
                                            <i class="bi bi-calendar"></i> <?= htmlspecialchars($backup['fecha']) ?>
                                        </td>
                                        <td><span class="badge bg-info">
                                            <?= $backup['tamaño'] ?> KB
                                        </span></td>
                                        <td>
                                            <a href="descargar_backup.php?archivo=<?= urlencode($backup['nombre']) ?>"
                                            class="btn btn-sm btn-success">
                                                <i class="bi bi-download"></i> Descargar
                                            </a>
                                            <a href="restaurar_backup.php?archivo=<?= urlencode($backup['nombre']) ?>"
                                            class="btn btn-sm btn-warning"
                                            onclick="return confirm('Restaurar este respaldo? Se perderán los cambios actuales.');">
                                                <i class="bi bi-arrow-repeat"></i> Restaurar
                                            </a>
                                            <a href="eliminar_backup.php?archivo=<?= urlencode($backup['nombre']) ?>" class="btn btn-sm btn-danger"
                                            onclick="return confirm('¿Eliminar este respaldo?');">
                                                <i class="bi bi-trash"></i> Eliminar
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle" style="font-size: 48px;"></i>
                        <h5>No hay respaldos guardados</h5>
                        <p>Haz clic en "Crear Respaldo" para generar tu primer respaldo.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>