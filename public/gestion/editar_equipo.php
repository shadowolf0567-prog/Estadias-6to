<?php
session_start();
require_once __DIR__ .'/../../config/db.php';

if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] != 1 && $_SESSION['tip_usr'] != 2)){
    header('Location: ../login.php?error=' .urlencode('Acceso denegado'));
    exit;
}

if(!isset($_GET['id_equipo']) || empty($_GET['id_equipo'])){
    header('Location: equipos.php?error='.urlencode('ID de equipo inexistente'));
    exit;
}

$id_equipo = intval($_GET['id_equipo']);

if(!$conn){
    header('Location: equipos.php?error='.urlencode('Error de conexión a la base de datos'));
    exit;
}

$sql = "SELECT e.*, c.id_cliente, c.nombre as cliente_nombre, c.no_cuenta
        FROM equipos e
        LEFT JOIN clientes c ON e.id_cliente = c.id_cliente
        WHERE e.id_equipo = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_equipo);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$equipo = mysqli_fetch_assoc($result);

if(!$equipo){
    header('Location: equipos.php?error='.urlencode('Equipo no encontrado'));
    exit;
}

$clientes = [];
$sql_clientes = "SELECT c.*,
                        GROUP_CONCAT(DISTINCT ct.telefono SEPARATOR ', ') as telefonos,
                        GROUP_CONCAT(DISTINCT cc.correo SEPARATOR ', ') as correos
                FROM clientes c
                LEFT JOIN telefonos ct ON c.id_cliente = ct.id_cliente
                LEFT JOIN correos cc ON c.id_cliente = cc.id_cliente
                GROUP BY c.id_cliente
                ORDER BY c.nombre ASC";
$result_clientes = mysqli_query($conn, $sql_clientes);
if($result_clientes){
    while($row = mysqli_fetch_assoc($result_clientes)){
        $clientes[] = $row;
    }
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Equipo</title>
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <style>
        .cliente-card{
            cursor: pointer;
            transition: all 0.3s;
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .cliente-card:hover{
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .cliente-seleccionado{
            background-color: #d4edda;
            border: 2px solid #28a745;
        }
        .filtro-busqueda{
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .cliente-actual{
            background-color: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .resultados-busqueda{
            max-height: 300px;
            overflow-y: auto;
        }
        .form-section{
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/menu.php'; ?>
    <div class="container mt-4">
        <h2 class="mb-3">Datos del Equipo</h2>
        <?php if($mensaje): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i> <?= htmlspecialchars($mensaje) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <form action="../lib/gestion_equipo.php" id="editForm" method="post">
            <input type="hidden" name="accion" value="editar">
            <input type="hidden" name="id_equipo" value="<?= $equipo['id_equipo'] ?>">
            <input type="hidden" name="id_cliente" id="idCliente" value="<?= $equipo['id_cliente'] ?>">

            <div class="form-section">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Número de Serie</label>
                        <input type="text" name="no_serie" id="no_serie" class="form-control"
                                value="<?= htmlspecialchars($equipo['no_serie']) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Modelo</label>
                        <input type="text" name="modelo" id="modelo" class="form-control"
                                value="<?= htmlspecialchars($equipo['modelo']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label>Inicio Contrato</label>
                        <input type="date" name="inicio_contrato" id="inicio_contrato" class="form-control"
                        value="<?= htmlspecialchars($equipo['inicio_contrato']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label>Fin Contrato</label>
                        <input type="date" name="fin_contrato" id="fin_contrato" class="form-control"
                        value="<?= htmlspecialchars($equipo['fin_contrato']) ?>">
                    </div>
                </div>
            </div>
            <div class="form-section">
                <h5>Cliente</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <div class="cliente-actual">
                            <strong><i class="bi bi-info-circle"></i> Cliente actual: </strong>
                            <?php if($equipo['cliente_nombre']): ?>
                                    <?= htmlspecialchars($equipo['cliente_nombre']) ?>
                                    <?php if($equipo['no_cuenta']): ?>
                                        (Cuenta: <?= htmlspecialchars($equipo['no_cuenta']) ?>)
                                    <?php endif; ?>
                            <?php else: ?>
                                <span class="badge bg-secondary">Sin Cliente Asignado</span>
                            <?php endif; ?>
                        </div>
                        <div class="filtro-busqueda">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input type="text" id="buscarCliente" class="form-control">
                                <button type="button" id="btnLimpiarBusqueda" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Limpiar
                                </button>
                            </div>
                        </div>
                        <div class="resultados-busqueda" id="resultadosBusqueda">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> Escribe al menos 2 caracteres para buscar
                            </div>
                        </div>
                        <div class="alert alert-success mt-3" id="clienteSeleccionado" style="display:none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong><i class="bi bi-check-circle"></i> Nuevo cliente seleccionado: </strong>
                                    <span id="nombreClienteSeleccionado"></span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarSeleccion()">
                                    <i class="bi bi-x-circle"></i> Cambiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 mb-4">
                <div style="display: flex; gap: 15px;">
                    <button type="submit" class="btn btn-primary" id="btnGuardar">
                        <i class="bi bi-save"></i> Guardar Cambios
                    </button>
                    <a href="./ver_equipo.php?id=<?= $equipo['id_equipo'] ?>" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
            </div>
        </form>
    </div>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script>

        document.addEventListener('DOMContentLoaded',function() {
            const buscarInput = document.getElementById('buscarCliente');
            if(buscarInput){
                buscarInput.addEventListener('keydown',function(e) {
                    if(e.key === 'Enter'){
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                });
            }
        });
        let clienteSeleccionado = null;
        let clienteIDSeleccionado = <?= $equipo['id_cliente'] ?: 'null' ?>;
        let clientesData = <?= json_encode($clientes) ?>;

        const buscarInput = document.getElementById('buscarCliente');
        const resultadosDiv = document.getElementById('resultadosBusqueda');
        const clienteSeleccionadoDiv = document.getElementById('clienteSeleccionado');
        const nombreClienteSpan = document.getElementById('nombreClienteSeleccionado');
        const idClienteInput = document.getElementById('idCliente');
        const btnGuardar = document.getElementById('btnGuardar');

        function buscarClientes(termino){
            if(termino.length < 2){
                resultadosDiv.innerHTML = `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para buscar</i> 
                    </div>
                `;
                return;
            }

            const terminoLower = termino.toLowerCase();
            const resultados = clientesData.filter(cliente =>
                cliente.nombre.toLowerCase().includes(terminoLower) ||
                (cliente.no_cuenta && cliente.no_cuenta.toLowerCase().includes(terminoLower)) ||
                (cliente.telefonos && cliente.telefonos.toLowerCase().includes(terminoLower)) ||
                (cliente.correos && cliente.correos.toLowerCase().includes(termino))
            );
            if(resultados.length === 0){
                resultadosDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> No se encontraron clientes con "${termino}"
                    </div>
                `;
                return;
            } 
            let html = '<div class="row">';
            resultados.forEach(cliente =>{
                const isSelected = (clienteIDSeleccionado === cliente.id_cliente);
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card cliente-card ${isSelected ? 'cliente-seleccionado' : '' }"
                            data-cliente-id="${cliente.id_cliente}"
                            onclick="seleccionarCliente(${cliente.id_cliente}, '${escapeHtml(cliente.nombre)}')">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <h6 class="card-title">
                                        <i class="bi bi-person-circle"></i>
                                        ${escapeHtml(cliente.nombre)}
                                    </h6>
                                    ${isSelected ? '<i class="bi bi-check-circle-fill text-success"></i>' : ''}
                                </div>
                                ${cliente.no_cuenta ? `
                                    <p class="card-text small mb-1">
                                        Número de Cuenta: ${escapeHtml(cliente.no_cuenta)}
                                    </p>
                                ` : ''}
                                ${cliente.telefonos ? `
                                    <p class="card-text small mb-1">
                                        <i class="bi bi-telephone"></i> Teléfono: ${escapeHtml(cliente.telefonos)}
                                    </p>
                                ` : ''}
                                ${cliente.correos ? `
                                    <p class="card-text small mb-1">
                                        <i class="bi bi-envelope"></i> Correo: ${escapeHtml(cliente.correos)}
                                    </p>
                                ` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            resultadosDiv.innerHTML = html;
        }
        function seleccionarCliente(id,nombre){
            clienteIDSeleccionado = id;
            clienteSeleccionado= id;
            idClienteInput.value = id;
            nombreClienteSpan.textContent = nombre;
            clienteSeleccionadoDiv.style.display = 'block';

            document.querySelectorAll('.cliente-card').forEach(card => {
                card.classList.remove('cliente-seleccionado');
                const checkIcon = card.querySelector('.bi-check-circle-fill');
                if(checkIcon) checkIcon.remove();
            });

            const selectedCard = document.querySelector(`.cliente-card[data-cliente-id="${id}"]`);
            if(selectedCard){
                selectedCard.classList.add('cliente-seleccionado');
                const headerDiv = selectedCard.querySelector('.d-flex');
                if(headerDiv && !selectedCard.querySelector('.bi-check-circle-fill')){
                    const checkIcon = document.createElement('i');
                    checkIcon.className = 'bi bi-check-circle-fill text-success';
                    headerDiv.appendChild(checkIcon);
                }
            }
            validarFormulario();
        }

        function limpiarSeleccion(){
            clienteIDSeleccionado = null;
            clienteSeleccionado = null;
            idClienteInput.value = '';
            clienteSeleccionadoDiv.style.display = 'none';

            document.querySelectorAll('.cliente-card').forEach(card => {
                card.classList.remove('cliente-seleccionado');
                const checkIcon = card.querySelector('.bi-check-circle-fill');
                if(checkIcon) checkIcon.remove();
            });

            if(buscarInput.value.length >= 2){
                buscarClientes(buscarInput.value);
            }

            validarFormulario();
        }

        let timeoutId = null;
        buscarInput.addEventListener('input', function(){
            clearTimeout(timeoutId);
            const termino = this.value.trim();
            timeoutId = setTimeout(() => {
                buscarClientes(termino);
            }, 300);
        });

        document.getElementById('btnLimpiarBusqueda').addEventListener('click',function(){
            buscarInput.value = '';
            resultadosDiv.innerHTML = `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Escribe al menos 2 caracteres para buscar clientes
                </div>
            `;
        });

        function validarFormulario(){
            const noSerie = document.querySelector('input[name="no_serie"]').value.trim();
            if(btnGuardar) btnGuardar.disabled = (noSerie === '');
        }

        document.querySelector('input[name="no_serie"]').addEventListener('input',validarFormulario);

        function escapeHtml(text){
            if(!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        if(clienteIDSeleccionado){
            const clienteActual = clientesData.find(c => c.id_cliente === clienteIDSeleccionado);
            if(clienteActual){
                seleccionarCliente(clienteActual.id_cliente, clienteActual.nombre);
            }
        }

        validarFormulario();
    </script>
</body>
</html>