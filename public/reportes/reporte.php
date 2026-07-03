<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] !=1 && $_SESSION['tip_usr'] != 2)){
    header('Location: ../login.php?error=' .urlencode('Acceso denegado'));
    exit;
}

$clientes = [];
$sql_clientes = "SELECT id_cliente,nombre,no_cuenta,telefono,correo FROM clientes ORDER BY nombre ASC";
$result_clientes = mysqli_query($conn, $sql_clientes);
if($result_clientes){
    while($row = mysqli_fetch_assoc($result_clientes)){
        $clientes[] = $row;
    }
}

$todos_equipos = [];
$sql_equipos = "SELECT e.id_equipo, e.no_serie, e.modelo, c.nombre as cliente_nombre, e.id_cliente
                FROM equipos e
                LEFT JOIN clientes c ON e.id_cliente = c.id_cliente
                ORDER BY e.no_serie ASC";
$result_equipos = mysqli_query($conn,$sql_equipos);
if($result_equipos){
    while($row = mysqli_fetch_assoc($result_equipos)){
        $todos_equipos[] = $row;
    }
}

$error=isset($_GET['error']) ? $_GET['error'] : '';
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Reporte</title>
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
</head>
<body>
    <?php require_once __DIR__ . '/../gestion/menu.php'; ?>
    <div class="container mt-4">
        <h2 class="mb-4">Nuevo Reporte</h2>
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
        <form action="../lib/gestion_reportes.php" method="post" id="formReporte">
            <input type="hidden" name="accion" value="agregar">
            <input type="hidden" name="id_cliente" id="idCliente" value="0">

            <div class="form-section">
                <h5>Cliente que reporta</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Seleccionar cliente</label>
                        <div class="filtro-busqueda">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="buscarCliente" class="form-control">
                                <button type="button" id="btnLimpiarBusqueda" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Limpiar
                                </button>
                            </div>
                        </div>
                        <div class="resultados-busqueda" id="listaClientes" style="max-height: 400px; overflow-y: auto;">
                            <?php if(count($clientes) > 0): ?>
                                <div class="row">
                                    <?php foreach($clientes as $cliente): ?>
                                        <div class="col-md-6 md-3 cliente-item"
                                        data-nombre="<?= strtolower($cliente['nombre']) ?>"
                                        data-correo="<?= strtolower($cliente['correo']) ?>"
                                        data-telefono="<?= htmlspecialchars(($cliente['telefono'])) ?>">
                                            <div class="card cliente-card" data-cliente-id="<?= $cliente['id_cliente'] ?>"
                                            onclick="seleccionarCliente(this, <?= $cliente['id_cliente'] ?>,'<?= htmlspecialchars($cliente['nombre']) ?>')">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between">
                                                        <h6 class="card-title"><i class="bi bi-person-circle"></i>
                                                            <?= htmlspecialchars($cliente['nombre']) ?></h6>
                                                        <i class="bi bi-check-circle-fill text-success" style="display: none;"></i>
                                                    </div>
                                                    <p class="card-text small"><?= htmlspecialchars($cliente['correo'] ?: 'No registrado') ?></p>
                                                    <p class="card-text small"><?= htmlspecialchars($cliente['telefono'] ?: 'No registrado') ?></p>
                                                    <span class="badge bg-secondary">ID: <?= $cliente['id_cliente'] ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">No hay clientes registrados.
                                    <a href="agregar_equipo.php?tab=nuevo">Crea uno nuevo</a>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="alert alert-success mt-3" id="clienteSeleccionado" style="display:none;">
                            <div class="d-flex justify-content-between align-item-center">
                                <div>
                                    <i class="bi bi-check-circle"></i>
                                    <strong>Cliente Seleccionado: </strong> <span id="nombreClienteSeleccionado"></span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarSeleccion()">
                                    <i class="bi bi-x-circle"></i> Cambiar
                                </button>
                            </div>
                        </div>
                        <input type="hidden" name="telefono_cliente" id="telefono_cliente">
                        <input type="hidden" name="contacto_cliente" id="contacto_cliente">
                    </div>
                </div>
            </div>
            <div class="form-section">
                <h5>Equipo Relacionado</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Seleccionar Equipo</label>
                        <select name="id_equipo" id="id_equipo" class="form-select">
                            <option value="">-- Seleccione un cliente primero --</option>
                        </select>
                    </div>
                </div>
                <div class="info-equipo" id="infoEquipo" style="display: none;">
                    <strong><i class="bi bi-info-circle"></i> Información del Equipo:</strong>
                    <span id="infoEquipoTexto"></span>
                </div>
            </div>
            <div class="form-section">
                <h5>Detalles del Reporte</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Titulo del reporte</label>
                        <input type="text" name="reporte" class="form-control" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Descripción del reporte</label>
                        <textarea name="descripcion" class="form-control" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-control">
                    </div>
                </div>
            </div>
            <div class="mt-3 mb-3">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Guardar Reporte
                </button>
                <a href="reportes.php" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script>
        const todosEquipos = <?= json_encode($todos_equipos) ?>;

        let clienteSeleccionado = null;
        let clienteIDSeleccionado = null;

        const buscarClienteInput = document.getElementById('buscarCliente');
        const idClienteInput = document.getElementById('idCliente');
        const clienteSeleccionadoDiv = document.getElementById('clienteSeleccionado');
        const nombreClienteSpan = document.getElementById('nombreClienteSeleccionado');
        const equipoSelect = document.getElementById('id_equipo');
        const infoEquipoDiv = document.getElementById('infoEquipo');
        const infoEquipoTexto = document.getElementById('infoEquipoTexto');
        const telefonoInput = document.getElementById('telefono_cliente');
        const contactoInput = document.getElementById('contacto_cliente');

        if(buscarClienteInput){
            buscarClienteInput.addEventListener('input',function (){
               const termino=this.value.toLowerCase();
               document.querySelectorAll('.cliente-item').forEach(item => {
                const nombre = item.getAttribute('data-nombre') || '';
                const correo = item.getAttribute('data-correo') || '';
                item.style.display = (nombre.includes(termino) || correo.includes(termino)) ? '' : 'none';
                });
            });
        }

        const limpiarBtn = document.getElementById('btnLimpiarBusqueda');
        if(limpiarBtn){
            limpiarBtn.addEventListener('click',function(){
                if(buscarClienteInput){
                    buscarClienteInput.value = '';
                    buscarClienteInput.dispatchEvent(new Event('input'));
                }
            });
        }
        window.seleccionarCliente = function(element, id, nombre){
            if(clienteSeleccionado){
                clienteSeleccionado.classList.remove('cliente-seleccionado');
                const icon = clienteSeleccionado.querySelector('.bi-check-circle-fill');
                if(icon) icon.style.display = 'none';
            }

            element.classList.add('cliente-seleccionado');
            const checkIcon = element.querySelector('.bi-check-circle-fill');
            if(checkIcon) checkIcon.style.display = 'inline-block';

            clienteSeleccionado = element;
            clienteIDSeleccionado = id;
            idClienteInput.value = id;
            nombreClienteSpan.textContent = nombre;
            clienteSeleccionadoDiv.style.display = 'block';

            const telefono = element.closest('.cliente-item').getAttribute('data-telefono');
            if(telefono && telefonoInput) telefonoInput.value = telefono;

            filtrarEquiposPorCliente(id);
        };
        window.limpiarSeleccion = function(){
            if(clienteSeleccionado){
                clienteSeleccionado.classList.remove('cliente-seleccionado');
                const icon = clienteSeleccionado.querySelector('.bi-check-circle-fill');
                if(icon) icon.style.display = 'none';
                clienteSeleccionado = null;
            }
            clienteIDSeleccionado = null;
            idClienteInput.value = 0;
            clienteSeleccionadoDiv. style.display = 'none';
            if(telefonoInput) telefonoInput.value = '';

            equipoSelect.innerHTML = '<option value="">-- Seleccione un cliente primero --</option>';
            infoEquipoDiv.style.display = 'none';
        };
        function filtrarEquiposPorCliente(clienteId){
            equipoSelect.innerHTML = '<option value="">-- Ninguno --</option>';
            infoEquipoDiv.style.display = 'none';

            if(!clienteId){
                equipoSelect.innerHTML = '<option value="">-- Seleccione un cliente primero --</option>';
                return;
            }
            const equiposFiltrados = todosEquipos.filter(equipo => equipo.id_cliente == clienteId);

            if(equiposFiltrados.length === 0){
                equipoSelect.innerHTML = '<option value="">-- Este cliente no tiene equipos registrados --</option>';
                return;
            }

            let html = '<option value="">-- Ninguno --</option>';
            equiposFiltrados.forEach(equipo => {
                html += `<option value="${equipo.id_equipo}" data-serie="${escapeHtml(equipo.no_serie)}" data-modelo="${escapeHtml(equipo.modelo)}">
                                Serie: ${escapeHtml(equipo.no_serie)} - Modelo: ${escapeHtml(equipo.modelo)}
                        </option> `;
            });
            equipoSelect.innerHTML = html;
        }

        if(equipoSelect){
            equipoSelect.addEventListener('change',function(){
                const selectedOption = this.options[this.selectedIndex];
                const serie = selectedOption.getAttribute('data-serie');
                const modelo = selectedOption.getAttribute('data-modelo');
                if(serie && modelo){
                    infoEquipoTexto.innerHTML = `
                        <strong>Serie</strong> ${escapeHtml(serie)} |
                        <strong>Modelo</strong> ${escapeHtml(modelo)}
                    `;
                    infoEquipoDiv.style.display = 'block';
                }else{
                    infoEquipoDiv.style.display = 'none';
                }
            });
        }
        function escapeHtml(text){
                    if(!text) return '';
                    const div = document.createElement('div');
                    div.textContent = text;
                    return div.innerHTML;
        }
    </script>
</body>
</html>