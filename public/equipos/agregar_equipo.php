<?php 
session_start();
require_once __DIR__ .'/../../config/db.php';
if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] != 1 && $_SESSION['tip_usr'] != 2)){
    header('Location: ../login.php?error='.urlencode('Acceso denegado'));
    exit;
}

$error = isset($_GET['error']) ? $_GET['error'] : '';
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';

$clientes = [];
$sql_clientes = "SELECT c.*,
                        GROUP_CONCAT(DISTINCT ct.telefono SEPARATOR ', ') as telefonos,
                        GROUP_CONCAT(DISTINCT cc.correo SEPARATOR ', ') as correos
                    FROM clientes c
                    LEFT JOIN telefonos ct ON c.id_cliente = ct.id_cliente
                    LEFT JOIN correos cc ON c.id_cliente = cc.id_cliente
                    GROUP BY c.id_cliente
                    ORDER BY c.nombre ASC";
$result_clientes = mysqli_query($conn , $sql_clientes);
if($result_clientes){
    while($row = mysqli_fetch_assoc($result_clientes)){
        $clientes[] = $row;
    }
}

$tab_activa = isset($_GET['tab']) ? $_GET['tab'] : 'existente';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Equipo</title>
    <link rel="stylesheet" href="../assets/css/bootstrap-icons.css">
    <link rel="stylesheet" href="../assets/css/bootstrap.css">
    <link rel="stylesheet" href="../assets/css/responsives.css">
    <style>
        .nav-tabs .nav-link.existente.active{
            background-color: #0d6efd;
            color: white;
        }
        .nav-tabs .nav-link.nuevo.active{
            background-color: #198754;
            color: white;
        }
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
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .paso{
            display: none;
        }
        .paso.activo{
            display: block;
        }
        .loading-spinner{
            display: none;
            text-align: center;
            margin-top: 15px;
            padding: 10px;
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
    <?php require_once __DIR__ .'/../gestion/menu.php'; ?>
    <div class="container mt-4">
        <h2 class="text-center mb-4">Registrar Nuevo Equipo</h2>

        <?php if($mensaje): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"> <?= htmlspecialchars($mensaje) ?></i>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle"> <?= htmlspecialchars($error) ?></i>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <a href="?tab=existente" class="nav-link existente <?= $tab_activa == 'existente' ? 'active' : '' ?>">
                    <i class="bi bi-person-badge"></i> Cliente Existente
                </a>
            </li>
            <li class="nav-item">
                <a href="?tab=nuevo" class="nav-link nuevo <?= $tab_activa == 'nuevo' ? 'active' : '' ?>">
                    <i class="bi bi-person-plus"></i> Cliente Nuevo
                </a>
            </li>
        </ul>
        <form action="../lib/gestion_clientes.php" id="formEquipo" method="post">
        <input type="hidden" name="accion" value="agregar_con_cliente">
        <input type="hidden" name="modo_cliente" id="modoCliente" value="<?= $tab_activa ?>">
        <input type="hidden" name="id_cliente" id="idCliente" value="0">
        
        <div class="paso <?= $tab_activa == 'existente' ? 'activo' : '' ?>" id="pasoExistente">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Seleccionar Cliente Existente</h5>
                </div>
                <div class="card-body">
                    <div class="filtro-busqueda">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" id="buscarCliente" class="form-control" autocomplete="off">
                            <button type="button" class="btn btn-outline-secondary" id="btnLimpiarBusqueda">
                                <i class="bi bi-x-circle"></i> Limpiar
                            </button>
                        </div>
                    </div>
                    <div class="resultados-busqueda" id="resultadosBusqueda" style="overflow-y:auto; max-height: 400px;">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar</i>
                        </div>
                    </div>
                    <div class="alert alert-success mt-3" id="clienteSeleccionado" style="display:none;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-check-circle"></i>
                                <strong>Cliente Seleccionado: </strong> <span id="nombreClienteSeleccionado"></span>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarSeleccion()">
                                <i class="bi bi-x-circle"></i> Cambiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="paso <?= $tab_activa == 'nuevo' ? 'activo' : '' ?>" id="pasoNuevo">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Registrar Nuevo Cliente</h5>
                </div>
                <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label>Nombre</label>
                                <input type="text" id="nuevo_nombre" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label>Número de Cuenta</label>
                                <input type="text" id="nuevo_no_cuenta" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label>Dirección</label>
                                <textarea id="nuevo_direccion" class="form-control"></textarea>                
                            </div>
                        </div>
                        <div class="mt-3">
                            <label>Teléfono</label>
                            <div id="telefonosContainer">
                                <div class="telefono-item">
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <input type="text" class="form-control" id="nuevo_telefono" data-tipo="telefono" class="form-control">
                                            </div>
                                            <div class="col-md-4">
                                                <input type="text" name="contacto_telefono" data-tipo="telefono-contacto" id="" class="form-control" placeholder="Titular">
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-check mt-2">
                                                    <input type="checkbox" data-tipo="telefono-principal" class="form-check-input" checked>
                                                    <label class="form-check-label">Principal</label>
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <i class="bi bi-dash-circle btn-remover" onclick="removerItem(this, 'telefonosContainer')" style="font-size: 24px;"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="agregarItem('telefonosContainer')">
                                <i class="bi bi-plus-circle"></i> Agregar teléfono
                            </button>
                        </div>
                        <div class="mt-3">
                            <label>Correos Electrónicos</label>
                            <div id="correosContainer">
                                <div class="correo-item">
                                    <div class="row g-2">
                                        <div class="col-md-4">
                                            <input type="email" data-tipo="correo" class="form-control">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="text" name="contacto_correo" id="" data-tipo="correo-contacto" class="form-control" placeholder="Titular">
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check mt-2">
                                                <input type="checkbox" data-tipo="correo-principal" class="form-check-input" checked>
                                                <label class="form-check-label">Principal</label>
                                            </div>
                                        </div>
                                        <div class="col-md-1">
                                            <i class="bi bi-dash-circle btn-remover" onclick="removerItem(this, 'correosContainer')" style="font-size: 24px;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="agregarItem('correosContainer')">
                                <i class="bi bi-plus-circle"></i> Agregar Correo
                            </button>
                        </div>
                    <div class="alert alert-success mt-3" id="clienteCreadoMsg" style="display: none;">
                        <i class="bi bi-check-circle"></i> Cliente creado exitosamente
                    </div>
                    <div class="loading-spinner" id="loadingSpinner">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Cargando</span>
                        </div>
                        <span>Creando cliente, por favor espere</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">Datos del Equipo</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>Número de Serie</label>
                        <input type="text" name="no_serie" id="no_serie" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label>Modelo</label>
                        <input type="text" name="modelo" id="modelo" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Inicio de Contrato</label>
                        <input type="date" name="inicio_contrato" id="inicio_contrato" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Fin de Contrato</label>
                        <input type="date" name="fin_contrato" id="fin_contrato" class="form-control">
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <button type="button" class="btn btn-primary" id="btnGuardar">
                <i class="bi bi-save"></i> Agregar Equipo
            </button>
            <a href="./equipos.php" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancelar
            </a>
        </div>
        </form>
    </div>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script>
        let clienteSeleccionado = null;
        let clienteIDSeleccionado = null;
        let clientesData = <?= json_encode($clientes) ?>;
        let elementoSeleccionado = null;
        const buscarClienteInput = document.getElementById('buscarCliente');
        const idClienteInput = document.getElementById('idCliente');
        const resultadosDiv = document.getElementById('resultadosBusqueda');
        const clienteSeleccionadoDiv = document.getElementById('clienteSeleccionado');
        const nombreClienteSpan = document.getElementById('nombreClienteSeleccionado');
        const btnGuardar = document.getElementById('btnGuardar');
        let telefonoIndex = 1;
        let correoIndex = 1;
        document.addEventListener('DOMContentLoaded',function(){
            if(buscarClienteInput){
                buscarClienteInput.addEventListener('keydown',function(e){
                    if(e.key === 'Enter'){
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                });
            }
        });

        function agregarItem(containerId){
            const container = document.getElementById(containerId);
            const isTelefono = containerId === 'telefonosContainer';
            const itemClass = isTelefono ? 'telefono-item' : 'correo-item';
            const tipo = isTelefono ? 'telefono' : 'correo';

            const html = `
                <div class="${itemClass}">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type = "${isTelefono ? 'text' : 'email'}" class="form-control" placeholder="${isTelefono ? 'Número de teléfono' : 'Correo electrónico'}" data-tipo="${tipo}">
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Titular" data-tipo="${tipo}-titular">
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mt-2">
                                <input type="checkbox" class="form-check-input" data-tipo="${tipo}-principal" checked>
                                <label class="form-check-label">Principal</label>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <i class="bi bi-dash-circle btn-remover" onclick="removerItem(this, '${containerId}')" style="font-size: 24px;"></i>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend',html);
        }
        function removerItem(element, containerId){
            const container = document.getElementById(containerId);
            const item = element.closest('.telefono-item, .correo-item');
            if(container.children.length > 1){
                item.remove();
            }else{
                alert('Debe haber al menos un elemento.');
            }
        }
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
                (cliente.correos && cliente.correos.toLowerCase().includes(terminoLower)) ||
                (cliente.telefonos && cliente.telefonos.toLowerCase().includes(terminoLower))
            );
            if(resultados.length === 0){
                resultadosDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"> No se encontraron clientes con "${termino}"</i>
                    </div>
                `;
                return;
            }
            let html = '<div class="row">';
            resultados.forEach(cliente => {
                const isSelected = (clienteIDSeleccionado === cliente.id_cliente);
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card cliente-card ${isSelected ? 'cliente-seleccionado' : ''}"
                            data-cliente-id ="${cliente.id_cliente}"
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
                                `:''}
                                ${cliente.telefonos ? `
                                    <p class="card-text small mb-1">
                                        <i class="bi bi-telephone"></i> Teléfonos: ${escapeHtml(cliente.telefonos)}
                                    </p>
                                `:''}
                                ${cliente.correos ? `
                                    <p class="card-text small">
                                        <i class="bi bi-envelope"></i> ${escapeHtml(cliente.correos)}
                                    </p>
                                `:''}
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
            clienteSeleccionado = id;
            idClienteInput.value = id;
            nombreClienteSpan.textContent = nombre;
            clienteSeleccionadoDiv.style.display = 'block';

            document.querySelectorAll('.cliente-card').forEach(card => {
                card.classList.remove('cliente-seleccionado');
                const checkIcon = card.querySelector('.bi-check-circle-fill');
                if(checkIcon) checkIcon.remove();
            });
            const selectedCard = document.querySelector(`.cliente-card[data-cliente-id="${id}"]`);
            if(selectedCard) {
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
            if(buscarClienteInput && buscarClienteInput.value.length >= 2){
                buscarClientes(buscarClienteInput.value);
            }
            validarFormulario();
        }
        
        let timeoutId = null;
        if(buscarClienteInput){
            buscarClienteInput.addEventListener('input',function() {
                clearTimeout(timeoutId);
                const termino = this.value.trim();
                timeoutId = setTimeout(() => {
                    buscarClientes(termino);
                }, 300);
            });
        }
        document.getElementById('btnLimpiarBusqueda').addEventListener('click',function(){
            buscarClienteInput.value = '';
            resultadosDiv.innerHTML = `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar</i> 
                </div>
            `;
        });

        function recolectarDatosContacto(){
            const telefonos = [];
            document.querySelectorAll('#telefonosContainer .telefono-item').forEach(item => {
                const numero = item.querySelector('input[data-tipo="telefono"]');
                const contacto = item.querySelector('input[data-tipo="telefono-contacto"]');
                const principal = item.querySelector('input[data-tipo="telefono-principal"]');
                if(numero && numero.value.trim() !== ''){
                    telefonos.push({
                        numero: numero.value.trim(),
                        contacto: contacto ? contacto.value.trim() : '',
                        es_principal: principal ? principal.checked : false
                    });
                }
            });
            
            const correos = [];
            document.querySelectorAll('#correosContainer .correo-item').forEach(item => {
                const correo = item.querySelector('input[data-tipo="correo"]');
                const contacto = item.querySelector('input[data-tipo="correo-contacto"]');
                const principal = item.querySelector('input[data-tipo="correo-principal"]');
                if(correo && correo.value.trim() !== ''){
                    correos.push({
                        direccion: correo.value.trim(),
                        contacto: contacto ? contacto.value.trim() : '',
                        es_principal: principal ? principal.checked : false
                    });
                }
            });
            return { telefonos, correos };
        }

        document.getElementById('btnGuardar').addEventListener('click',function(){
            const modo = document.getElementById('modoCliente').value;
            const noSerie = document.getElementById('no_serie').value.trim();
            if(noSerie === ''){
                alert('El número de serie es obligatorio');
                return;
            }
            if(modo === 'existente'){
                if(!clienteIDSeleccionado){
                    alert('Debes seleccionar un cliente existente');
                    return;
                }
                document.getElementById('formEquipo').submit();
            }else{
                const nombre = document.getElementById('nuevo_nombre').value.trim();
                if(nombre === ''){
                    alert('El nombre del cliente es obligatorio');
                    return;
                }
                const datosContacto = recolectarDatosContacto();
                const loadingDiv = document.getElementById('loadingSpinner');
                const btnGuardar = document.getElementById('btnGuardar');
                loadingDiv.style.display = 'block';
                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<i class="bi bi-hourglass-split"></i> Procesando';

                const datosCliente = {
                    nombre: nombre,
                    no_cuenta: document.getElementById('nuevo_no_cuenta').value.trim(),
                    direccion: document.getElementById('nuevo_direccion').value.trim(),
                    telefonos: datosContacto.telefonos,
                    correos: datosContacto.correos
                };
                fetch('../clientes/crear_cliente_ajax.php',{
                    method: "POST",
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(datosCliente)
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success){
                        const nuevoIdCliente = data.id_cliente;
                        document.getElementById('idCliente').value = nuevoIdCliente;
                        document.getElementById('clienteCreadoMsg').style.display = 'block';
                        setTimeout(() => {
                            document.getElementById('formEquipo').submit();
                        }, 500);
                    }else{
                        alert('Error al crear clientes: ' + data.error);
                        loadingDiv.style.display = 'none';
                        btnGuardar.disabled = false;
                        btnGuardar.innerHTML = '<i class= "bi bi-save"></i> Agregar Equipo';
                    }
                })
                .catch(error => {
                    alert('Error de conexión: ' + error);
                    loadingDiv.style.display = 'none';
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = '<i class="bi bi-save"></i> Agregar Equipo';
                });
            }
        });

        function validarFormulario(){
            const noSerie = document.getElementById('no_serie').value.trim();
            if(btnGuardar) btnGuardar.disabled = (noSerie === '');
        }
        document.getElementById('no_serie').addEventListener('input',validarFormulario);

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