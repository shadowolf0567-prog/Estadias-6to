<?php 
session_start();
require_once __DIR__ . '/../../config/db.php';

if(!isset($_SESSION['tip_usr']) || $_SESSION['tip_usr'] !=1 && $_SESSION['tip_usr'] != 2){
    header('Location: ../login.php?error='.urlencode('Acceso denegado'));
    exit;
}
if(!isset($_GET['archivo']) || empty($_GET['archivo'])){
    header('Location: backup.php?error=' . urlencode('Archivo no especificado'));
    exit;
}

$archivo = basename($_GET['archivo']);
$ruta_archivo = __DIR__ . "/../../backups/" . $archivo;

if(!file_exists($ruta_archivo)){
    header('Location: backup.php?error=' . urlencode('El archivo no existe'));
    exit;
}
header('Content-Type: applcation/sql');
header('Content-Disposition: attachment; filename="' . $archivo . '"');
header('Content-Length: ' . filesize($ruta_archivo));
header('Cache-Control: no-cache');

readfile($ruta_archivo);
exit;
?>