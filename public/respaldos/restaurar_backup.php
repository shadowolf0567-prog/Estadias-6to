<?php
session_start();
require_once __DIR__ . '/../../config/db.php';

if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] !=1 && $_SESSION['tip_usr'] !=2 )){
    header('Location: ../login.php?error=' .urlencode('Acceso denegado'));
    exit;
}

$archivo = null;
if(isset($_GET['archivo']) && !empty($_GET['archivo'])){
    $archivo = basename($_GET['archivo']);
} elseif(isset($_GET['nombre']) && !empty($_GET['nombre'])){
    $archivo = basename($_GET['nombre']);
} elseif(isset($_GET['file']) && !empty($_GET['file'])){
    $archivo = basename($_GET['fila']);
}

if(!$archivo){
    header('Location: backup.php?error=' . urlencode('Archivo no especificado'));
    exit;
}

$ruta_archivo = __DIR__ . "/../../backups/" . $archivo;

if(!file_exists($ruta_archivo)){
    header('Location: backup.php?error=' . urlencode('El archivo no existe'));
    exit;
}

function restaurar_backup($ruta_archivo, $conn){
    $sql_content = file_get_contents($ruta_archivo);

    if($sql_content === false){
        return ['success' => false, 'error' => 'No se pudo leero el archivo'];
    }

    mysqli_query($conn,"SET FOREIGN_KEY_CHECKS = 0");
    
    $comandos_iniciales = [
        "SET FOREIGN_KEY_CHECKS = 0",
        "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'",
        "SET time_zone = '+00:00'"
    ];

    foreach($comandos_iniciales as $comando){
    @mysqli_query($conn, $comando);
    }

    $errores = [];
    $queries = [];
    $buffer = '';
    $len = strlen($sql_content);
    $in_string = false;
    $string_char = '';
    $in_comment = false;

    for($i = 0; $i < $len; $i++){
        $char = $sql_content[$i];
        $next_char = [$i + 1 < $len] ? $sql_content[$i + 1] : '';

        if(!$in_string && !$in_comment && $char == '-' && $next_char == '-'){
            $in_comment = true;
            continue;
        }

        if($in_comment && $char == "\n"){
            $in_comment = false;
            continue;
        }

        if($in_comment){
            continue;
        }

        if(!$in_string && ($char == "'" || $char == '"')){
            $in_string = true;
            $string_char = $char;
        } elseif($in_string && $char == $string_char && ($i > 0 && $sql_content[$i-1] != '\\')){
            $in_string = false;
        } elseif(!$in_string && $char == ';'){
            $query = trim($buffer);
            if(!empty($query)){
                $queries[] = $query;
            }
            $buffer = '';
            continue;
        }
        $buffer .= $char;
    }

    $query = trim($buffer);
    if(!empty($query)){
        $queries[] = $query;
    }

    $total = count($queries);
    $procesadas = 0;
    $errores_detalle = [];

    foreach($queries as $index => $query){
        if(empty($query) || strlen(trim($query)) < 3){
            continue;
        }

        if(mysqli_query($conn, $query)){
            $procesadas ++;
        }else{
            $error_msg = mysqli_error($conn);

            $ignorar_errores = [
                'already_exists',
                'Duplicate entry',
                'doesn\'t exist',
                'Unknown table'
            ];
            $ignorar = false;
            foreach($ignorar_errores as $ignorar_texto){
                if(Stripos($error_msg, $ignorar_texto) !== false){
                    $ignorar = true;
                    $procesadas++;
                    break;
                }
            }

            if(!$ignorar){
                $errores_detalle[] = "Error en query " . ($index + 1) . ": " .$error_msg;
            }
        }
    }

    mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

    if(count($errores_detalle) > 0){
        return [
            'success' => false,
            'error' => "Se procesaron $procesadas de $total queries.<br>Errores: " . implode("<br>", array_slice($errores_detalle, 0, 10))
        ];
    }

    return ['success' => true, 'mensaje' => "Base de datos restaurada correctamente. $procesadas queries ejecutadas."];
}

$resultado = restaurar_backup($ruta_archivo, $conn);

if($resultado['success']){
    header('Location: backup.php?msg=' . urlencode($resultado['mensaje']));
} else {
    header('Location: backup.php?error=' . urlencode($resultado['error']));
}
?>