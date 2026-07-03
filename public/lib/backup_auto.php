<?php
/**
 * backup_auto.php - Respaldo automático para MariaDB
 * Ejecutar desde el Programador de Tareas de Windows
 * 
 * Ruta: C:\Apache24\htdocs\Emipac\public\lib\backup_auto.php
 */

// Configuración
date_default_timezone_set('America/Mexico_City');

// ============================================
// 1. CONFIGURACIÓN DE BASE DE DATOS
// ============================================
$host = 'localhost';
$user = 'root';
$pass = '12345';
$dbname = 'emipac';

$fecha = date('Y-m-d_H-i-s');
$ruta_base = __DIR__ . '/../backups/';
$archivo_backup = $ruta_base . "backup_auto_{$fecha}.sql";

// ============================================
// 2. RUTA DE MYSQLDUMP (AJUSTAR SEGÚN INSTALACIÓN)
// ============================================
// Prueba estas rutas en orden hasta encontrar la correcta
$rutas_posibles = [
    'C:\Program Files\MariaDB 10.11\bin\mysqldump.exe',
    'C:\Program Files\MariaDB\bin\mysqldump.exe',
    'C:\Program Files (x86)\MariaDB\bin\mysqldump.exe',
    'C:\xampp\mysql\bin\mysqldump.exe',
    'C:\wamp64\bin\mariadb\mariadb10.11.2\bin\mysqldump.exe',
    'C:\laragon\bin\mariadb\mariadb-10.11.2-winx64\bin\mysqldump.exe',
];

$mysqldump = null;
foreach($rutas_posibles as $ruta){
    if(file_exists($ruta)){
        $mysqldump = $ruta;
        break;
    }
}

// Si no encuentra mysqldump, intentar con el comando global
if(!$mysqldump){
    $test = shell_exec('mysqldump --version 2>&1');
    if(strpos($test, 'mysqldump') !== false){
        $mysqldump = 'mysqldump';
    }
}

if(!$mysqldump){
    die("❌ Error: No se encontró mysqldump. Verifica la ruta.\n");
}

// ============================================
// 3. CREAR CARPETA SI NO EXISTE
// ============================================
if(!is_dir($ruta_base)){
    mkdir($ruta_base, 0777, true);
}

// ============================================
// 4. EJECUTAR MYSQLDUMP
// ============================================
$comando = "\"$mysqldump\" --user={$user} --password={$pass} --host={$host} --default-character-set=utf8mb4 --no-tablespaces {$dbname} > \"{$archivo_backup}\" 2>&1";
exec($comando, $output, $resultado);

// ============================================
// 5. VERIFICAR Y COMPRIMIR
// ============================================
if(file_exists($archivo_backup) && filesize($archivo_backup) > 0){
    // Comprimir
    $archivo_comprimido = $archivo_backup . '.gz';
    $contenido = file_get_contents($archivo_backup);
    $comprimido = gzencode($contenido, 9);
    file_put_contents($archivo_comprimido, $comprimido);
    unlink($archivo_backup);
    
    // Eliminar backups antiguos (más de 30 días)
    $archivos = glob($ruta_base . "backup_auto_*.sql.gz");
    foreach($archivos as $archivo){
        if(filemtime($archivo) < strtotime('-30 days')){
            unlink($archivo);
        }
    }
    
    // Log de éxito
    $log = $ruta_base . "backup_log.txt";
    $mensaje = date('Y-m-d H:i:s') . " - ✅ Backup creado: " . basename($archivo_comprimido) . " - Tamaño: " . round(filesize($archivo_comprimido)/1024, 2) . " KB\n";
    file_put_contents($log, $mensaje, FILE_APPEND);
    
    echo $mensaje;
} else {
    // Log de error
    $log = $ruta_base . "backup_error_log.txt";
    $mensaje = date('Y-m-d H:i:s') . " - ❌ ERROR: No se pudo crear el backup\n";
    file_put_contents($log, $mensaje, FILE_APPEND);
    
    // Mostrar error detallado
    echo "❌ Error al crear backup\n";
    echo "Comando ejecutado: $comando\n";
    echo "Código de salida: $resultado\n";
    echo "Salida: " . implode("\n", $output) . "\n";
}
?>