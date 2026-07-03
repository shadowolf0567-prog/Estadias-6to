<?php
session_start();
require_once __DIR__ .'/../../config/db.php';

if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] !=1 && $_SESSION['tip_usr'] !=2)){
    header('Location: ../login.php?error='.urlencode('Acceso denegado'));
    exit;
}

$archivo = null;
if(isset($_GET['archivo']) && !empty($_GET['archivo'])){
    $archivo = basename($_GET['archivo']);
}elseif(isset($_GET['nombre']) && !empty($_GET['nombre'])){
    $archivo = basename($_GET['nombre']);
}elseif(isset($_GET['file']) && !empty($_GET['file'])){
    $archivo = basename($_GET['file']);
}

if(!$archivo){
    header('Location: backup.php?error=' . urlencode('Archivo no especificado'));
    exit;
}

$ruta_archivo = __DIR__ . "/../../backups/" . $archivo;

if(!file_exists($ruta_archivo)){
    header('Location: backup.php?error=' .urlencode('El archivo no existe '.$archivo));
    exit;
}
if(unlink($ruta_archivo)){
    header('Location: backup.php?msg='.urlencode('Respaldo eliminado correctamente: ' . $archivo));
}else{
    header('Location: backup.php?error=' . urlencode('No se pudo eliminar el archivo: ' . $archivo));
}
exit;
?>