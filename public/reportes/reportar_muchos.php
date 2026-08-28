<?php
session_start();
require_once __DIR__ . '/../../config/db.php';
if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] !=1 && $_SESSION['tip_usr'] !=2 && $_SESSION['tip_usr' ] != 3)){
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
                        GROUP_CONCAT(DISTINCT cc.correo SEPARATOR ', ') as correos,
                        CONCAT(c.no_cuenta, '-', REPLACE(c.contrato, 'C-', '')) as referencia_auto
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
$tecnicos = [];
$sql_tecnicos = "SELECT * FROM tecnicos";
$result_tecnicos = mysqli_query($conn,$sql_tecnicos);
if($result_tecnicos){
    while($row = mysqli_fetch_assoc($result_tecnicos)){
        $tecnicos[] = $row;
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
    <title>Reportar en Masa</title>
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
    <?php require_once __DIR__ . '/../gestion/menus.php'; ?>
    <div class="container mt-4">
        <h2 class="mb-4">Nuevos Reportes</h2>
        <p><span class="text-muted">Utiliza esta página si hay muchos servicios preventivos/correctivos de un mismo cliente</span></p>
        <a href="agregar_reporte.php" class="btn btn-sm btn-primary">
            <i class="bi bi-arrow-left"></i> Agregar Reporte
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
            <input type="hidden" name="accion" value="agregar_muchos">
            <input type="hidden" name="id_cliente" id="idCliente" value="0">
            <div class="form-section">
                <h5>Cliente</h5>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label">Seleccionar Cliente</label>
                        <div class="filtro-busqueda">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" class="form-control"  id="buscarCliente">
                                <button type="button" id="btnLimpiarBusqueda" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle"></i> Limpiar
                                </button>
                            </div>
                        </div>
                        <div class="resultados-busqueda" id="resultadosBusqueda" style="overflow-y: auto;max-height: 400px">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar</i>
                            </div>
                        </div>
                        <div class="alert alert-success mt-3" id="clienteSeleccionado" style="display:none;">
                            <div class="d-flex justify-content-between align-item-center">
                                <div>
                                    <i class="bi bi-check-circle"></i>
                                    <strong>Cliente Seleccionado: </strong><span id="nombreClienteSeleccionado"></span>
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
                <div class="form-section">
                    <h5>Detalles de los Reportes</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Servicio</label>
                            <select name="servicio" class="form-select">
                                <option value="SER-01">SER-01</option>
                                <option value="SER-02">SER-02</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="" class="form-label">Fecha</label>
                            <input type="date" name="fecha" value="<?= date('Y-m-d') ?>" class="form-control">
                        </div>
                        <div class="col-md-4">
                        <label class="form-label">Técnico</label>
                            <select name="id" class="form-select">
                                <?php foreach($tecnicos as $tecnico): ?>
                                    <option value="<?= $tecnico['id'] ?>">
                                        <?= htmlspecialchars($tecnico['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-section">
                    <h5>Equipos</h5>
                    <div id="equiposContainer">
                        <div class="equipo-item" id="equipo_0">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">Referencia</label>
                                    <input type="text" name="referencia[0][referencia]" id="referencia_0" class="form-control referencia_auto" readonly>
                                </div>
                                <div class="col-md-9">
                                    <label class="form-label">Buscar Equipo</label>
                                    <input type="text" id="buscarEquipo_0" class="form-control" onkeyup="buscarEquipos(this.value, 0)"  placeholder="Buscar por número de serie o modelo">
                                    <input type="hidden" name="equipos[0][id_equipo]" id="equipoId_0" value="">
                                </div>
                                <div class="col-md-12" id="resultadosEquipos_0"></div>
                                <div class="alert alert-success" id="equipoSeleccionado_0" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-check-circle"></i>
                                            <strong>Equipo seleccionado:</strong><span id="equipoInfo_0"></span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarSeleccionEquipo(0)">
                                                <i class="bi bi-x-circle"></i> Cambiar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="agregarEquipo()">
                            <i class="bi bi-plus-circle"></i> Agregar Equipo
                    </button>
                </div>
                <div class="mt-3 mb-3">
                    <button type="submit" class="btn btn-primary" id="btnGuardar">
                        <i class="bi bi-save"></i> Guardar Reportes
                    </button>
                    <a href="reportes.php" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
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
                    })
                }
            });
        const todosEquipos = <?= json_encode($todos_equipos) ?>;
        let clienteSeleccionado = null;
        let clienteIDSeleccionado = null;
        let clientesData = <?= json_encode($clientes) ?>;
        let equiposSeleccionados ={};
        let elementoSeleccionado = null;
        let contadorReportes = 1;
        const buscarClienteInput = document.getElementById('buscarCliente');
        const idClienteInput = document.getElementById('idCliente');
        const resultadosDiv = document.getElementById('resultadosBusqueda');
        const clienteSeleccionadoDiv = document.getElementById('clienteSeleccionado');
        const nombreClienteSpan = document.getElementById('nombreClienteSeleccionado');
        const referenciaInput = document.getElementById('referencia_0');
        const btnGuardar = document.getElementById('btnGuardar');

        function escapeHtml(text){
            if(!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        function agregarOtro(){
            const container = document.getElementById('equiposContainer');
            const index = contadorReportes;
            const html = `
                <div class="equipo-item" id="equipo_${index}">
                    <div class="row g-3">
                        <div class="col-md-11">
                            <select name="equipos[${index}][id_equipo]" class="form-select equipo-select">
                                <option value="">-- Ninguno --</option>
                            </select>
                        </div>
                        <div class="col-md-1 d-flex align-items-end">
                            <i class="bi bi-dash-circle btn-remover" onclick="removerEquipo(${index})" style="font-size: 24px;"></i>
                        </div>
                    </div>
                <div>
            `;
            container.insertAdjacentHTML('beforeend',html);
            if(clienteIDSeleccionado){
                filtrarEquiposPorCliente(clienteIDSeleccionado, index);
            }
            contadorReportes++;
        }
        function removerEquipo(index){
            const item = document.getElementById('equipo_'+index);
            const container = document.getElementById('equiposContainer');

            if(container.querySelectorAll('.equipo-item').length > 1){
                item.remove();
            }else{
                alert('Debe haber al menos un equipo.');
            }
        }
        function buscarClientes(termino){
            if(!resultadosDiv) return;
            if(termino.length < 2){
                resultadosDiv.innerHTML = `
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar</i>
                    </div>
                `;
                return;
            }
            const terminoLower = termino.toLowerCase();
            const resultados = clientesData.filter(cliente =>
                cliente.nombre.toLowerCase().includes(terminoLower) ||
                (cliente.no_cuenta && cliente.no_cuenta.toLowerCase().includes(terminoLower))
            );
            if(resultados.length === 0){
                resultadosDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                `;
                return;
            }
            
            let html = `<div class="row">`;
            resultados.forEach(cliente =>{
                const isSelected = (clienteIDSeleccionado === cliente.id_cliente);
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="card cliente-card ${isSelected ? 'cliente-seleccionado' : ''}"
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
                                        Cuenta: ${escapeHtml(cliente.no_cuenta)}
                                    </p>
                                `:''}
                                ${cliente.contrato ? `
                                    <p class="card-text small mb-1">
                                        Contrato: ${escapeHtml(cliente.contrato)}
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
        function buscarEquipos(termino, index){
            const resultadosDiv = document.getElementById('resultadosEquipos_' + index);
            if(termino.length < 2){
                resultadosDiv.innerHTML = `
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar</i>
                    </div>
                `;
                return;
            }
            const terminoLower = termino.toLowerCase();
            let resultados = todosEquipos;
            if(clienteIDSeleccionado){
                resultados = resultados.filter(equipo => equipo.id_cliente == clienteIDSeleccionado);
            }
            resultados = resultados.filter(equipo =>
                equipo.no_serie.toLowerCase().includes(terminoLower) ||
                (equipo.modelo && equipo.modelo.toLowerCase().includes(terminoLower))
            );
            const idSeleccionados = Object.values(equiposSeleccionados).filter(id => id > 0);
            resultados = resultados.filter(equipo => !idSeleccionados.includes(equipo.id_equipo));

            if(resultados.length === 0){
                resultadosDiv.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> No se encontraron equipos
                    </div>
                `;
                return;
            }
            let html = '<div class="row">';
            resultados.forEach(equipo => {
                html += `
                    <div class="col-md-4 lg-3 mb-2">
                        <div class="card equipo-card p-2" onclick="seleccionarEquipo(${equipo.id_equipo},${index})">
                            <div class="card-body p-2">
                                <strong>${escapeHtml(equipo.no_serie)}</strong>
                                <br><small class="text-muted">${escapeHtml(equipo.modelo)}</small>
                                <br><small class="text-primary">${escapeHtml(equipo.cliente_nombre)}</small>
                            </div>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            resultadosDiv.innerHTML = html;
        }
        function seleccionarEquipo(idEquipo, index){
            const equipo = todosEquipos.find(e => e.id_equipo == idEquipo);
            if(!equipo) return;

            equiposSeleccionados[index] = idEquipo;
            document.getElementById('equipoId_'+index).value = idEquipo;

            const infoDiv = document.getElementById('equipoSeleccionado_' + index);
            const infoSpan = document.getElementById('equipoInfo_' + index);
            infoSpan.innerHTML = `<strong>Serie:</strong> ${escapeHtml(equipo.no_serie)} | <strong>Modelo: </strong> ${escapeHtml(equipo.modelo)}`;
            infoDiv.style.display = 'block';

            document.getElementById('resultadosEquipos_'+index).innerHTML = '';
            document.getElementById('buscarEquipo_'+index).value = equipo.no_serie;

            const btnEliminar = document.querySelector(`#equipo_${index} .btn-danger`);
            if(btnEliminar) btnEliminar.style.display = 'block';
            validarFormulario();
        }
        function limpiarSeleccionEquipo(index){
            equiposSeleccionados[index] = null;
            document.getElementById('equipoId_'+index).value = '';
            document.getElementById('equipoSeleccionado_'+index).style.display = 'none';
            document.getElementById('buscarEquipo_'+index).value = '';
            document.getElementById('resultadosEquipos_'+index).innerHTML = `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar</i>
                </div>
            `;
            const btnEliminar = document.querySelector(`equipo_${index}.btn-danger`);
            if(btnEliminar) btnEliminar.style.display = 'none';
            validarFormulario();
        }
        function agregarEquipo(){
            const container = document.getElementById('equiposContainer');
            const index = contadorReportes;
            equiposSeleccionados[index] = null;

            const html = `
                <div class="equipo-item" id="equipo_${index}">
                    <div class="row g-2">
                        <div class="col-md-3">
                                    <label class="form-label">Referencia</label>
                                    <input type="text" name="referencia[${index}][referencia]" id="referencia_${index}" class="form-control referencia_auto">
                                </div>
                                <div class="col-md-9">
                                    <label class="form-label">Buscar Equipo</label>
                                    <input type="text" id="buscarEquipo_${index}" class="form-control" onkeyup="buscarEquipos(this.value, ${index})">
                                    <input type="hidden" name="equipos[${index}][id_equipo]" id="equipoId_${index}" value="">
                                </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger btn-sm" onclick="removerEquipo(${index})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <div class="col-md-12" id="resultadosEquipos_${index}">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar</i>
                            </div>
                        </div>
                        <div class="alert alert-success mt-3" id="equipoSeleccionado_${index}" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-check-circle"></i>
                                    <strong>Equipo seleccionado:</strong><span id="equipoInfo_${index}"></span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarSeleccionEquipo(${index})">
                                        <i class="bi bi-x-circle"></i> Cambiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend',html);
            contadorReportes++;
            if(clienteIDSeleccionado){
                const clienteSeleccionado = clientesData.find(cliente => cliente.id_cliente == clienteIDSeleccionado);
                if(clienteSeleccionado && clienteSeleccionado.referencia_auto){
                    document.getElementById('referencia_' + index).value = clienteSeleccionado.referencia_auto;
                }
            }
        }
        function removerEquipo(index){
            const item = document.getElementById('equipo_' + index);
            const container = document.getElementById('equiposContainer');
            if(container.querySelectorAll('.equipo-item').length > 1){
                delete equiposSeleccionados[index];
                item.remove();
                validarFormulario();
            }else{
                alert('Debe haber al menos un equipo');
            }
        }
        function filtrarEquiposPorCliente(clienteId){
            clienteIDSeleccionado = clienteID;
            document.querySelectorAll('.equipo-item').forEach((item,index) => {
                const resultadosDiv = document.getElementById('resultadosEquipos_' + index);
                if(resultadosDiv){
                    resultadosDiv.innerHTML = `
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar</i>
                        </div>
                    `;
                }
                const input = document.getElementById('buscarEquipo_'+index);
                if(input) input.value = '';
                limpiarSeleccionEquipo(index);
            });
        }
        function filtrarTodosEquipos(clienteId){
            document.querySelectorAll('.equipo-select').forEach((select,index) => {
                const item = select.closest('.equipo-item');
                const id = item ? item.id.replace('equipo_','') : index;
                filtrarEquiposPorCliente(clienteId,parseInt(id));
            });
        }
        function actualizarReferencias() {
            const clienteSeleccionado = clientesData.find(cliente => cliente.id_cliente == clienteIDSeleccionado);
            const referencia = clienteSeleccionado ? clienteSeleccionado.referencia_auto : '';
            document.querySelectorAll('.referencia_auto').forEach(function(input) {
                input.value = referencia;
            });
        }
        window.seleccionarCliente = function(id,nombre){
            clienteIDSeleccionado = id;
            if(idClienteInput) idClienteInput.value=id; 
            if(nombreClienteSpan) nombreClienteSpan.textContent = nombre;
            if(clienteSeleccionadoDiv) clienteSeleccionadoDiv.style.display='block';
            const clienteSeleccionado = clientesData.find(cliente => cliente.id_cliente == id);
            const referencia = clienteSeleccionado ? clienteSeleccionado.referencia_auto : '';
            document.querySelectorAll('.referencia_auto').forEach(function(input) {
                input.value = referencia;
            });
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
            filtrarTodosEquipos(id);
        };
        window.limpiarSeleccion = function(){
            if(elementoSeleccionado){
                elementoSeleccionado.classList.remove('cliente-seleccionado');
                const icon = elementoSeleccionado.querySelector('.bi-check-circle-fill');
                if(icon){
                    icon.remove();
                }
                elementoSeleccionado = null;
            }
            clienteIDSeleccionado = null;
            if(clienteSeleccionadoDiv) clienteSeleccionadoDiv.style.display = 'none';
            if(idClienteInput) idClienteInput.value=0;
            if(nombreClienteSpan) nombreClienteSpan.textContent = '';
            document.querySelectorAll('.referencia_auto').forEach(function(input) {
                input.value = '';
            });
            document.querySelectorAll('.equipo-select').forEach((select,index) => {
                select.innerHTML = '<option value="">-- Seleccione un cliente primero --</option>';
            });
        };
        function validarFormulario(){
            const titulo = document.querySelector('input[name="reporte"]');
            const referencia = referenciaInput.value;
            if(btnGuardar){
                btnGuardar.disabled = (titulo && titulo.value.trim() === '');
            }
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
                    <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar </option>
                </div>
            `;
        })
        const tituloInput = document.querySelector('input[name="reporte"]');
        if(tituloInput){
            tituloInput.addEventListener('input',validarFormulario);
        }
        validarFormulario();
    </script>
</body>
</html>