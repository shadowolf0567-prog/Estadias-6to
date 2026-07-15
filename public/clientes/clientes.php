<?php 
session_start();
require_once __DIR__ .'/../../config/db.php';
if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] !=1 && $_SESSION['tip_usr'] !=2)){
header('Location: ../login.php?error='.urldecode('Acceso denegado'));
exit;
}
function buscar_clientes($termino = ''){
    global $conn;
    if(!$conn){
        return[];
    }
    if(empty($termino)){
        $sql = "SELECT * FROM clientes ORDER BY id_cliente DESC";
        $resultado = mysqli_query($conn,$sql);
    }else{
        $termino=mysqli_real_escape_string($conn,$termino);
        $sql="SELECT * FROM clientes WHERE nombre LIKE '%$termino%' OR no_cuenta LIKE '%$termino%' OR direccion LIKE '%$termino%'";
        $resultado=mysqli_query($conn,$sql);
        if(!$resultado){
            echo "Error SQL: ".mysqli_error($conn);
            return [];
        }
    }
    $clientes=[];
    if($resultado && mysqli_num_rows($resultado)>0){
        while($fila = mysqli_fetch_assoc($resultado)){
            $clientes[]=$fila;
        }
    }
    return $clientes;
}
$busqueda = isset($_GET['buscar']) ? trim($_GET['buscar']) : '';
$clientes=buscar_clientes($busqueda);
?>
<?php 
function resaltar_coincidencia($texto,$busqueda){
    if(!empty($busqueda) || empty($texto)){
        return htmlspecialchars($texto);
    }
    $texto= htmlspecialchars($texto);
    $busqueda=htmlspecialchars($busqueda);
    return preg_replace('/('.preg_quote($busqueda,'/').')/i','<span class="resaltar">$1</span>',$texto);
} 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clientes</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/responsives.css">
</head>
<body>
    <?php require_once __DIR__.'/../gestion/menu.php'; ?>
    <div class="container mt-4">
        <h2 class="mb-4">Clientes</h2>
        <div class="busqueda-container">
            <form method="GET" action="" id="formBusqueda">
                <div class="row g-3">
                    <div class="col-md-10">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input type="text"
                            class="form-control form-control-md"
                            name="buscar" id="buscarInput"
                            placeholder="Buscar por Nombre, Número de Cuenta o Dirección"
                            value="<?php echo htmlspecialchars($busqueda); ?>"
                            autocomplete="off">
                            <?php if(!empty($busqueda)): ?>
                                <a href="clientes.php" class="btn btn-outline-secondary">
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
            <div class="mt-3">
                <i class="bi bi-info-circle"></i>
                Mostrando <strong><?php echo count($clientes); ?></strong>
                clientes
                <?php if(!empty($busqueda)): ?>
                    para "<strong><?php echo htmlspecialchars($busqueda); ?></strong>"
                <?php endif; ?>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Nombre</th>
                        <th>No. Cuenta</th>
                        <th>Dirección</th>
                        <th>Opciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($clientes)>0): ?>
                        <?php foreach($clientes as $cliente): ?>
                            <tr>
                                <td><?= htmlspecialchars($cliente['nombre']); ?></td>
                                <td><?= htmlspecialchars($cliente['no_cuenta']); ?></td>
                                <td><?= htmlspecialchars($cliente['direccion']); ?></td>
                                <td>
                                <form action="../lib/gestion_clientes.php" method="post" style="display: inline-block;"
                                onsubmit="return confirm('¿Quieres eliminar a este cliente')">
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id_cliente" value="<?php echo $cliente['id_cliente']; ?>">
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                                </form>
                                <a href="ver_cliente.php?id=<?= $cliente['id_cliente']; ?>" class=" btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> Ver Datos
                                </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                <div class="alert alert-warning m-3">
                                    <i class="bi bi-exclamation-triangle"></i>
                                    No se encontraron clientes
                                    <?php if(!empty($busqueda)): ?>
                                        para "<strong><?php echo htmlspecialchars($busqueda) ?></strong>"
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script src="../assets/js/bootstrap.min.js"></script>
</body>
</html>