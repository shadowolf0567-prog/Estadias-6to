<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] !=1 && $_SESSION['tip_usr'] != 2)){
    header('Location: ../login.php?error=' .urlencode('Acceso denegado'));
    exit;
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
$clientes = [];
$sql_clientes = "SELECT c.*,
                        GROUP_CONCAT(DISTINCT ct.telefono SEPARATOR ', ') as telefonos,
                        GROUP_CONCAT(DISTINCT cc.correo SEPARATOR ', ') as correos
                FROM clientes c
                LEFT JOIN telefonos ct ON c.id_cliente = ct.id_cliente
                LEFT JOIN correos cc ON c.id_cliente = cc.id_cliente
                GROUP BY c.id_cliente
                ORDER BY c.nombre ASC";
$result_clientes = mysqli_query($conn,$sql_clientes);
if($result_clientes){
    while($row = mysqli_fetch_assoc($result_clientes)){
        $clientes[] = $row;
    }
}
$cliente = "SELECT c.*,r.* FROM clientes c
            INNER JOIN reportes r
            ON r.id_cliente = c.id_cliente";
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
    <link rel="stylesheet" href="../assets/css/responsives.css">
    <style>
        .form-section{
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .info-equipo{
            background: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
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
        .resultados-busqueda{
            max-height: 400px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../gestion/menu.php'; ?>
    <div class="container mt-4">
        <h2 class="mb-4">Nuevo Reporte</h2>
        <a href="reportar_muchos.php" class="btn btn-sm btn-primary">
            <i class="bi bi-arrow-left"></i> Reportar Muchos
        </a>
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
                        <div class="resultados-busqueda" id="resultadosBusqueda" style="overflow-y: auto; max-height: 400px; " >
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar</i>
                            </div>
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
                <h5>Componentes</h5>
                <div id="componentesContainer">
                    <div class="componente-item" id="componente_0">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label">Tipo</label>
                                <select name="componentes[0][tipo]" class="form-select" onchange="mostrarSeccion(this,0)">
                                    <option value="">-- Ninguno --</option>
                                    <option value="SER-01">SER-01</option>
                                    <option value="SER-02">SER-02</option>
                                    <option value="SER-03">SER-03</option>
                                    <option value="componente">Componente</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Nombre</label>
                                <input type="text" id="nombre_0" name="componentes[0][nombre]" class="form-control" placeholder="Nombre del componente">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Cantidad</label>
                                <input type="number" name="componentes[0][cantidad]" class="form-control" value="1" min="1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">&nbsp;</label>
                                <i class="bi bi-dash-circle btn-remover" onclick="removerComponente(this)" style="display: block; margin-top: 5px; font-size: 24px;"></i>
                            </div>
                        </div>
                        <div class="row g-2 mt-2" id="seccionDescripcion_0" style="display: none;">
                            <div class="col-md-12">
                                <label class="form-label">Descripción</label>
                                <textarea name="componentes[0][descripcion]" class="form-control" rows="2" placeholder="Escribe el nombre del componente o refacción"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="agregarComponente()">
                    <i class="bi bi-plus-circle"></i> Agregar componente
                </button>
            </div>
            <div class="form-section">
                <h5>Detalles del Reporte</h5>
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Referencia</label>
                        <input type="text" name="referencia" id="" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Fecha</label>
                        <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Técnico</label>
                        <input type="text" name="tecnico" class="form-control">
                    </div>
                </div>
            </div>
            <div class="mt-3 mb-3">
                <button type="submit" class="btn btn-primary" id="btnGuardar">
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
        document.addEventListener('DOMContentLoaded',function(){
            const buscarInput = document.getElementById('buscarCliente');
            if(buscarInput){
                buscarInput.addEventListener('keydown',function(e){
                    if(e.key === 'Enter'){
                        e.preventDefault();
                        e.stopPropagation();
                        return false;
                    }
                });
            }
        });
        const todosEquipos = <?= json_encode($todos_equipos) ?>;

        let clienteSeleccionado = null;
        let clienteIDSeleccionado = null;
        let clientesData = <?= json_encode($clientes) ?>;
        let elementoSeleccionado =null;
        let contadorComponentes = 1;
        const buscarClienteInput = document.getElementById('buscarCliente');
        const idClienteInput = document.getElementById('idCliente');
        const resultadosDiv = document.getElementById('resultadosBusqueda');
        const clienteSeleccionadoDiv = document.getElementById('clienteSeleccionado');
        const nombreClienteSpan = document.getElementById('nombreClienteSeleccionado');
        const equipoSelect = document.getElementById('id_equipo');
        const infoEquipoDiv = document.getElementById('infoEquipo');
        const infoEquipoTexto = document.getElementById('infoEquipoTexto');
        const btnGuardar = document.getElementById('btnGuardar');

        function escapeHtml(text){
            if(!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        function mostrarSeccion(select, index){
            const seccion = document.getElementById('seccionDescripcion_' + index);
            const nombreInput = document.getElementById('nombre_' + index);
            if(seccion){
                if(select.value === 'componente' || select.value === 'SER-03'){
                    seccion.style.display = 'block';
                }else{
                    seccion.style.display = 'none';
                }
            }
            if(nombreInput) {
                switch(select.value){
                    case 'SER-01':
                        nombreInput.value = 'Servicio Preventivo';
                        nombreInput.readOnly=true;
                    break;
                    case 'SER-02':
                        nombreInput.value = 'Servicio Correctivo';
                        nombreInput.readOnly=true;
                    break;
                    case 'SER-03':
                        nombreInput.value = 'Entrega Refacción/Consumible';
                        nombreInput.readOnly = true;
                    break;
                    default:
                        if(nombreInput.value === 'Servicio Preventivo' || 
                            nombreInput.value === 'Servicio Correctivo' ||
                            nombreInput.value === 'Entrega Refacción/Consumible') {
                                nombreInput.value = '';
                            }
                            nombreInput.readOnly = false;
                            nombreInput.style.backgroundColor = '';
                            nombreInput.placeholder = 'Nombre del componente/servicio';
                            break;
                }
            }
        }
        function agregarComponente(){
            const container = document.getElementById('componentesContainer');
            const html = `
                <div class="componente-item" id="componente_${contadorComponentes}">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Tipo</label>
                            <select name ="componentes[${contadorComponentes}][tipo]" class="form-select" onchange="mostrarSeccion(this,${contadorComponentes})">
                                <option value="">-- Ninguno --</option>
                                <option value="SER-01">SER-01</option>
                                <option value="SER-02">SER-02</option>
                                <option value="SER-03">SER-03</option>
                                <option value="componente">Componente</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nombre</label>
                            <input type="text" id="nombre_${contadorComponentes}" name="componentes[${contadorComponentes}][nombre]" class="form-control" placeholder="Nombre del componente/servicio">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Cantidad</label>
                            <input type="number" name="componentes[${contadorComponentes}][cantidad]" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <i class="bi bi-dash-circle btn-remover" onclick="removerComponente(this)" style="display: block; margin-top: 5px; font-size: 24px"></i>
                        </div>
                    </div>
                    <div class="row g-2 mt-2" id="seccionDescripcion_${contadorComponentes}" style="display: none;">
                        <div class="col-md-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="componentes[${contadorComponentes}][descripcion]" class="form-control" rows="2" placeholder="Escribe el nombre del componente o refacción"></textarea>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', html);
            contadorComponentes++;

        }
        function removerComponente(element){
            const item = element.closest('.componente-item');
            const container = document.getElementById('componentesContainer');
            if(container.children.length > 1){
                item.remove();
            }else{
                alert('Debe haber al menos un componente');
            }
        }
        
        function buscarClientes(termino){
            if(!resultadosDiv) return;
            if(termino.length < 2){
                resultadosDiv.innerHTML = `
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"> Escribe al menor 2 caracteres para comenzar a buscar</i>
                    </div>
                `;
                return;
            }
            const terminoLower = termino.toLowerCase();
            const resultados = clientesData.filter(cliente =>
                cliente.nombre.toLowerCase().includes(terminoLower) ||
                (cliente.no_cuenta && cliente.no_cuenta.toLowerCase().includes(terminoLower)) ||
                (cliente.correo && cliente.correo.toLowerCase().includes(terminoLower))
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
                        <div class="card cliente-card ${isSelected ? 'cliente-seleccionado' : ''}"
                            data-cliente-id="${cliente.id_cliente}"
                            onclick="seleccionarCliente(${cliente.id_cliente},'${escapeHtml(cliente.nombre)}')">
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
                                        Cuenta: ${escapeHtml(cliente.no_cuenta)}
                                    </p>
                                `:''}
                                ${cliente.contrato ? `
                                    <p class="card-text small mb-1">
                                        Contrato: ${escapeHtml(cliente.contrato)}
                                    </p>
                                `:''}
                                ${cliente.telefonos ? `
                                    <p class="card-text small mb-1">
                                        <i class="bi bi-telephone"></i> ${escapeHtml(cliente.telefonos)}
                                    </p>
                                `:''}
                                ${cliente.correos ? `
                                    <p class="card-text small mb-1">
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
        function filtrarEquiposPorCliente(clienteId,mantenerEquipoActual = false){
            let equipoSeleccionadoId = mantenerEquipoActual ? equipoActualId : null;
            if(equipoSelect){
                equipoSelect.innerHTML='<option value="">-- Ninguno --</option>';
            }
            if(infoEquipoDiv) infoEquipoDiv.style.display='none';
            if(!clienteId){
                if(equipoSelect) equipoSelect.innerHTML = '<option value="">-- Seleccione un cliente primero --</option>';
                return;
            }
            const equiposFiltrados = todosEquipos.filter(equipo => equipo.id_cliente == clienteId);
            if(equiposFiltrados.length === 0){
                if(equipoSelect) equipoSelect.innerHTML == '<option value="">-- Este cliente no tiene equipos registrados --</option>';
                return;
            }
            let html = '<option value="">-- Ninguno --</option>';
            equiposFiltrados.forEach(equipo => {
                const selected = (equipoSeleccionadoId == equipo.id_equipo);
                html += `<option value="${equipo.id_equipo}"
                            data-serie="${escapeHtml(equipo.no_serie)}"
                            data-modelo="${escapeHtml(equipo.modelo)}"
                            ${selected ? 'selected' : ''}>
                            Serie: ${escapeHtml(equipo.no_serie)} - Modelo: ${escapeHtml(equipo.modelo)}
                         </option>`; 
            });
            if(equipoSelect) equipoSelect.innerHTML = html;
            if(equipoSeleccionadoId && infoEquipoTexto){
                const equipoSeleccionado = equiposFiltrados.find (e => e.id_equipo == equipoSeleccionadoId);
                if(equipoSeleccionado){
                    infoEquipoTexto.innerHTML = `
                                <strong>Serie:</strong> ${escapeHtml(equipoSeleccionado.no_serie)} |
                                <strong>Modelo:</strong> ${escapeHtml(equipoSeleccionado.modelo)}
                            `;
                            if(infoEquipoDiv) infoEquipoDiv.style.display = 'block';
                }
            }
        }
        window.seleccionarCliente = function(id,nombre){
            clienteIDSeleccionado = id;
            if(idClienteInput) idClienteInput.value=id;
            if(nombreClienteSpan) nombreClienteSpan.textContent = nombre;
            if(clienteSeleccionadoDiv) clienteSeleccionadoDiv.style.display='block';
            if(elementoSeleccionado){
                elementoSeleccionado.classList.remove('cliente-seleccionado');
                const iconAntiguo = elementoSeleccionado.querySelector('.bi-check-circle-fill');
                if(iconAntiguo) iconAntiguo.remove();
            }
            const selectedCard = document.querySelector(`.cliente-card[data-cliente-id="${id}"]`);
            if(selectedCard){
                selectedCard.classList.add('cliente-seleccionado');
                elementoSeleccionado = selectedCard;
                const headerDiv = selectedCard.querySelector('.d-flex');
                if(headerDiv && !selectedCard.querySelector('.bi-check-circle-fill')){
                    const checkIcon = document.createElement('i');
                    checkIcon.className = 'bi bi-check-circle-fill text-success';
                    headerDiv.appendChild(checkIcon);
                }
            }
            filtrarEquiposPorCliente(id,false);
        };
        window.limpiarSeleccion=function(){
            if(elementoSeleccionado){
                elementoSeleccionado.classList.remove('cliente-seleccionado');
                const icon = elementoSeleccionado.querySelector('.bi-check-circle-fill');
                if(icon){
                    icon.remove();
                }
                elementoSeleccionado= null;
            }
            clienteIDSeleccionado = null;
            if(clienteSeleccionadoDiv) clienteSeleccionadoDiv.style.display = 'none';
            if(idClienteInput) idClienteInput.value=0;
            if(nombreClienteSpan) nombreClienteSpan.textContent = '';
            if(telefonoInput) telefonoInput.value = '';
            if(equipoSelect){
                equipoSelect.innerHTML = '<option value="">-- Seleccione un cliente primero --</option>';
            }
            if(infoEquipoDiv) infoEquipoDiv.style.display = 'none';
        };
        function validarFormulario(){
            const titulo = document.querySelector('input[name="reporte"]');
            if(btnGuardar){
                btnGuardar.disabled = (titulo && titulo.value.trim() === '');
            }
        }
        let timeoutId = null;
        if(buscarClienteInput){
            buscarClienteInput.addEventListener('input',function(){
                clearTimeout(timeoutId);
                const termino=this.value.trim();
                timeoutId=setTimeout(() => {
                    buscarClientes(termino);
                }, 300);
            });
        }
        document.getElementById('btnLimpiarBusqueda').addEventListener('click',function(){
            buscarClienteInput.value = '';
            resultadosDiv.innerHTML = `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para buscar</i>
                </div>
            `;
        })
        if(equipoSelect){
            equipoSelect.addEventListener('change',function(){
                const selectedOption = this.options[this.selectedIndex];
                const serie = selectedOption.getAttribute('data-serie');
                const modelo=selectedOption.getAttribute('data-modelo');
                if(serie && modelo && infoEquipoTexto && infoEquipoDiv){
                    infoEquipoTexto.innerHTML = `<strong>Serie:</strong> ${escapeHtml(serie)} | <strong>Modelo:</strong> ${escapeHtml(modelo)}`;
                    infoEquipoDiv.style.display = 'block';
                }else if(infoEquipoDiv){
                    infoEquipoDiv.style.display = 'none';
                }
            });
        }
        const tituloInput = document.querySelector('input[name="reporte"]');
        if(tituloInput){
            tituloInput.addEventListener('input',validarFormulario);
        }
        validarFormulario();
    </script>
</body>
</html>