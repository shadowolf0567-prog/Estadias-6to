<?php
session_start();
require_once __DIR__ .'/../../config/db.php';
if(!isset($_SESSION['tip_usr']) || ($_SESSION['tip_usr'] != 1 && $_SESSION['tip_usr'] != 2)){
    header('Location: reportes.php?error='.urlencode('Acceso denegado'));
    exit;
}
if(!isset($_GET['id_reporte']) || empty($_GET['id_reporte'])){
    header('Location:reportes.php?error='.urlencode('Reporte inexistente'));
    exit;
}
$id_reporte = intval($_GET['id_reporte']);
$sql="SELECT r.*, c.id_cliente, c.nombre as cliente_nombre FROM reportes r
        LEFT JOIN clientes c ON r.id_cliente = c.id_cliente
        WHERE r.id_reporte = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $id_reporte);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$reporte = mysqli_fetch_assoc($result);

if(!$reporte){
    header('Location: reportes.php?error=' .urlencode('Reporte no encontrado'));
    exit;
}
$todos_equipos = [];
$sql_equipos = "SELECT e.id_equipo, e.no_serie, e.modelo, c.nombre as cliente_nombre, 
                e.id_cliente FROM equipos e LEFT JOIN clientes c 
                ON e.id_cliente = c.id_cliente ORDER BY e.no_serie ASC";
$result_equipos = mysqli_query($conn, $sql_equipos);
while($row = mysqli_fetch_assoc($result_equipos)){
    $todos_equipos[] = $row;
}
$componentes = [];
$sql_comp = "SELECT id_reporte_componente as id, componente, cantidad,descripcion,tipo
            FROM reportes_componentes
            WHERE id_reporte = ?";
$stmt_comp = mysqli_prepare($conn,$sql_comp);
mysqli_stmt_bind_param($stmt_comp,"i",$id_reporte);
mysqli_stmt_execute($stmt_comp);
$result_comp = mysqli_stmt_get_result($stmt_comp);
while($row = mysqli_fetch_assoc($result_comp)){
    if(empty($row['tipo'])){
        if(strpos($row['componente'], 'Preventivo') !== false){
            $row['tipo'] = 'SER-01';
        }elseif(strpos($row['componente'], 'Correctivo') !== false){
            $row['tipo'] = 'SER-02';
        }elseif(strpos($row['componente'], 'Entrega Refacción/Consumible') !== false){
            $row['tipo'] = 'SER-03';
        }elseif(strpos($row['componente'],'Componente') !== false || strpos($row['componente'], 'componente') !== false){
            $row['tipo'] = 'componente';
        }
    }
    $componentes[] = $row;
}
$total_componentes = count($componentes);

$equipos_cliente_actual = [];
if($reporte['id_cliente']){
    $sql_equipos_cliente = "SELECT id_equipo, no_serie, modelo FROM equipos
                            WHERE id_cliente = ? ORDER BY no_serie ASC";
    $stmt_eq = mysqli_prepare($conn,$sql_equipos_cliente);
    mysqli_stmt_bind_param($stmt_eq,"i",$reporte['id_cliente']);
    mysqli_stmt_execute($stmt_eq);
    $result_eq = mysqli_stmt_get_result($stmt_eq);
    while($row = mysqli_fetch_assoc($result_eq)){
        $equipos_cliente_actual[] = $row;
    }
    mysqli_stmt_close($stmt_eq);
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
$error=isset($_GET['error']) ? $_GET['error'] : '';
$mensaje = isset($_GET['msg']) ? $_GET['msg'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Reporte</title>
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
            background-color: #d3edda;
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
        .cliente-actual{
            background-color: #e3f2fd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php require_once __DIR__ . '/../gestion/menu.php';?>
    <div class="container mt-4">
        <h2 class="mb-4">Editar Reporte</h2>
        <?php if($mensaje): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-echeck-circle"></i> <?= htmlspecialchars($mensaje) ?>
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
            <input type="hidden" name="accion" value="editar_atendido">
            <input type="hidden" name="id_reporte" value="<?= $reporte['id_reporte'] ?>">
            <input type="hidden" name="id_cliente" value="<?= $reporte['id_cliente'] ?>" id="idCliente">
            <div class="form-section">
                <h5>Cliente que reporta</h5>
                <div class="cliente-actual">
                    <strong><i class="bi bi-info-circle"></i> Cliente actual</strong>
                    <?php if($reporte['cliente_nombre']): ?>
                            <?= htmlspecialchars($reporte['cliente_nombre']) ?>
                    <?php else: ?>
                        <span class="badge bg-secondary">Sin Cliente Asignado</span>
                    <?php endif; ?>
                </div>
                <div class="row g-3 mt-2">
                    <div class="col-md-12">
                        <h5>Cambiar cliente</h5>
                        <div class="filtro-busqueda">
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input type="text" id="buscarCliente" class="form-control">
                                <button type="button" class="btn btn-outline-secondary" id="btnLimpiarBusqueda">
                                    <i class="bi bi-x-circle"></i> Limpiar
                                </button>
                            </div>
                        </div>
                        <div class="resultados-busqueda" id="resultadosBusqueda" style=" overflow-y: auto; max-height: 400px;">
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar</i>
                            </div>
                        </div>
                        <div class="alert alert-success mt-3" id="clienteSeleccionado" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="bi bi-check-circle"></i>
                                    <strong>Cliente Seleccionado: </strong><span id="nombreClienteSeleccionado"></span>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarSeleccion()">
                                    <i class="bi bi-x-circle"></i> Cambiar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-section">
                <h5>Equipo Relacionado</h5>
                <div id="equiposContainer">
                    <div class="equipo-item" id="equipo_0">
                        <div class="row g-3">
                            <div class="col-md-10">
                                <label class="form-label">Buscar Equipo</label>
                                <input type="text" id="buscarEquipo_0" class="form-control" onkeyup="buscarEquipo(this.value,0)">
                                <input type="hidden" name="id_equipo" id="equipoId_0" value="<?= $reporte['id_equipo'] ?>">
                            </div>
                            <div class="col-md-12" id="resultadosEquipos_0">
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar</i>
                                </div>
                            </div>
                            <div class="col-md-12" id="equipoSeleccionado_0" style="display: <?= $reporte['id_equipo'] ? 'block' : 'none' ?>;">
                                <div class="alert alert-success">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="bi bi-check-circle"></i>
                                            <strong>Equipo seleccionado:</strong><span id="equipoInfo_0"></span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limpiarSeleccionEquipos(0)">
                                            <i class="bi bi-x-circle"></i> Cambiar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-section">
                <h5>Componentes</h5>
                <div id="componentesContainer">
                    <?php if(count($componentes) > 0): ?>
                        <?php foreach($componentes as $index => $comp): ?>
                            <div class="componente-item" id="componente_<?= $index ?>">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <label class="form-label">Tipo</label>
                                        <select name="componentes[<?= $index ?>][tipo]" class="form-select" onchange="mostrarSeccion(this, <?= $index ?>)">
                                            <option value="">-- Ninguno --</option>
                                            <option value="SER-01" <?= ($comp['tipo'] == 'SER-01') ? 'selected' : '' ?>>SER-01</option>
                                            <option value="SER-02" <?= ($comp['tipo'] == 'SER-02') ? 'selected' : '' ?>>SER-02</option>
                                            <option value="SER-03" <?= ($comp['tipo'] == 'SER-03') ? 'selected' : '' ?>>SER-03</option>
                                            <option value="componente" <?= ($comp['tipo'] == 'componente') || empty($comp['tipo']) ? 'selected' : ''?>>Componente</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" name="componentes[<?= $index ?>][nombre]" class="form-control" value="<?= htmlspecialchars($comp['componente']) ?>" id="nombre_<?= $index ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Cantidad</label>
                                        <input type="number" name="componentes[<?= $index ?>][cantidad]" class="form-control" value="<?= $comp['cantidad'] ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <i class="bi bi-dash-circle btn-remover" onclick="removerComponente(this)" style="display: block; margin-top: 5px; font-size: 24px;"></i>
                                    </div>
                                </div>
                                <div class="row g-2 mt-2" id="seccionDescripcion_<?= $index ?>" style="display: <?= (strpos($comp['componente'],'componente') !== false || strpos($comp['componente'],'Componente') !== false) ? 'block' : 'none' ?>;">
                                    <div class="col-md-12">
                                        <label class="form-label">Descripción</label>
                                        <textarea name="componentes[<?= $index ?>][descripcion]" class="form-control" placeholder="Escribe el nombre del componente o refacción"><?= htmlspecialchars($comp['descripcion']) ?></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
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
                                    <input type="text" id="nombre_0" name="componentes[0][nombre]" class="form-control" placeholder="Nombre del componente/servicio">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Cantidad</label>
                                    <input type="number" name="componentes[0][cantidad]" class="form-control" value="1" min="1">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <i class="bi bi-dash-circle btn-remover" onclick="removerComponente(this)" style="display: block; margin-top: 5px;"></i>
                                </div>
                            </div>
                            <div class="row g-2 mt-2" id="seccionDescripcion_0" style="display: none;">
                                <div class="col-md-12">
                                    <label class="form-label">Descripción</label>
                                    <textarea name="componentes[0][descripcion]" class="form-control" rows="2" placeholder="Escribe el nombre del componente o refacción"></textarea>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>          
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="agregarComponente()">
                    <i class="bi bi-plus-circle"></i> Agregar componente
                </button>
            </div>
            <div class="form-section">
                <h5>Detalles del Reporte</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Referencia</label>
                        <input type="text" name="referencia" id="" class="form-control" value="<?= htmlspecialchars($reporte['referencia']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha de creación del Reporte</label>
                        <input type="date" name="fecha" class="form-control" value="<?= htmlspecialchars($reporte['fecha']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Fecha de atención</label>
                        <input type="date" name="fecha_atencion" class="form-control" value="<?= htmlspecialchars($reporte['fecha_atencion']) ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Técnico</label>
                        <input type="text" name="tecnico" class="form-control" value="<?= htmlspecialchars($reporte['tecnico']) ?>">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Observaciones</label>
                        <textarea name="observaciones_atencion" class="form-control"><?= htmlspecialchars($reporte['observaciones_atencion']) ?></textarea>
                    </div>
                </div>
            </div>
            <div class="mt-3 mb-3">
                <button type="submit" class="btn btn-primary" id="btnGuardar">
                    <i class="bi bi-save"></i> Guardar Cambios
                </button>
                <a href="ver_reporte.php?id=<?= $reporte['id_reporte'] ?>" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
    <script src="../assets/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded',function() {
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
        let clienteIDSeleccionado = <?= $reporte['id_cliente'] ?: 'null' ?>;
        let clientesData = <?= json_encode($clientes) ?>;
        let contadorComponentes = <?= count($componentes) > 0 ? count($componentes) : 1 ?>;
        let equiposSeleccionados = {};
        const todosEquipos = <?= json_encode($todos_equipos) ?>;
        const equipoActualId = <?= $reporte['id_equipo'] ?: 'null' ?>;
        const clienteActualId = <?= $reporte['id_cliente'] ?: 'null' ?>;
        const buscarClienteInput= document.getElementById('buscarCliente');
        const idClienteInput = document.getElementById('idCliente');
        const resultadosDiv = document.getElementById('resultadosBusqueda');
        const clienteSeleccionadoDiv = document.getElementById('clienteSeleccionado');
        const nombreClienteSpan = document.getElementById('nombreClienteSeleccionado');
        const btnGuardar = document.getElementById('btnGuardar');
        let elementoSeleccionado = null;

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
                        nombreInput.readoOnly=true;
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
            const index = contadorComponentes;
            const html = `
                <div class="componente-item" id="componente_${index}">
                    <div class="row g-2">
                        <div class="col-md-4">
                            <label class="form-label">Tipo</label>
                            <select name ="componentes[${index}][tipo]" class="form-select" onchange="mostrarSeccion(this,${index})">
                                <option value="">-- Ninguno --</option>
                                <option value="SER-01">SER-01</option>
                                <option value="SER-02">SER-02</option>
                                <option value="SER-03">SER-03</option>
                                <option value="componente">Componente</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Nombre</label>
                            <input type="text" id="nombre_${index}" name="componentes[${index}][nombre]" class="form-control" placeholder="Nombre del componente/servicio">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Cantidad</label>
                            <input type="number" name="componentes[${index}][cantidad]" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <i class="bi bi-dash-circle btn-remover" onclick="removerComponente(this)" style="display: block; margin-top: 5px; font-size: 24px"></i>
                        </div>
                    </div>
                    <div class="row g-2 mt-2" id="seccionDescripcion_${index}" style="display: none;">
                        <div class="col-md-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="componentes[${index}][descripcion]" class="form-control" rows="2" placeholder="Escribe el nombre del componente o refacción"></textarea>
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
        document.addEventListener('DOMContentLoaded',function() {
            setTimeout(function() {
                document.querySelectorAll('.componente-item select[name*="[tipo]"]').forEach(function(select){
                    var index = select.name.match(/\[(\d+)\]/);
                    if(index){
                        mostrarSeccion(select, parseInt(index[1]));
                    }
                });
            }, 100);
        });
        function buscarClientes(termino){
            if(!resultadosDiv) return;
            if(termino.length < 2){
                resultadosDiv.innerHTML = `
                    <div class= "alert alert-info">
                        <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar</i> 
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
                                ` : ''}
                                ${cliente.contrato ? `
                                    <p class="card-text small mb-1">
                                        Contrato: ${escapeHtml(cliente.contrato)}
                                    </p>
                                `:''}
                                ${cliente.telefonos ? `
                                    <p class="card-text small mb-1">
                                        <i class="bi bi-telephone"></i> ${escapeHtml(cliente.telefonos)}
                                    </p>
                                ` : ''}
                                ${cliente. correos ? `
                                    <p class="card-text small mb-1">
                                        <i class="bi bi-envelope"></i> ${escapeHtml(cliente.correos)}
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
        function buscarEquipo(termino, index){
            const resultadosDiv = document.getElementById('resultadosEquipos_'+index);
            if(termino.length < 2){
                resultadosDiv.innerHTML = `
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar </i>
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
                    <div class="col-md-4 col-lg-3 mb-2">
                        <div class="card equipo-card p-2" onclick="seleccionarEquipo(${equipo.id_equipo},${index})">
                            <div class="card-body p-2">
                                <strong>${escapeHtml(equipo.no_serie)}</strong>
                                <br><small class="text-muted">${escapeHtml(equipo.modelo)}</small>
                                <br<small class="text-primary">${escapeHtml(equipo.cliente_nombre)}</small>
                            </div>
                        </div>
                    </div>
                `;      
            });
            html += '</div>';
            resultadosDiv.innerHTML = html;
        }
        function seleccionarEquipo(idEquipo,index){
            const equipo = todosEquipos.find(e => e.id_equipo == idEquipo);
            if(!equipo) return;
            equiposSeleccionados[index] = idEquipo;
            document.getElementById('equipoId_'+index).value=idEquipo;
            const infoDiv = document.getElementById('equipoSeleccionado_'+index);
            const infoSpan = document.getElementById('equipoInfo_'+index);
            infoSpan.innerHTML = `<strong>Serie: </strong> ${escapeHtml(equipo.no_serie)} | <strong>Modelo: ${escapeHtml(equipo.modelo)}`;
            infoDiv.style.display = 'block';
            document.getElementById('resultadosEquipos_'+index).innerHTML = '';
            document.getElementById('buscarEquipo_'+index).value=equipo.no_serie;
            validarFormulario();
        }
        function limpiarSeleccionEquipos(index){
            equiposSeleccionados[index] = null;
            document.getElementById('equipoId_'+index).value = '';
            document.getElementById('equipoSeleccionado_'+index).style.display = 'none';
            document.getElementById('buscarEquipo_'+index).value='';
            document.getElementById('resultadosEquipos_'+index).innerHTML = `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar</i>
                </div>
            `;
            validarFormulario();
        }
        function filtrarEquiposPorCliente(clienteId, mantenerEquipoActual = false){
            clienteIDSeleccionado = clienteId;
            document.querySelectorAll('.equipo-item').forEach((item,index) => {
                const resultadosDiv = document.getElementById('resultadosEquipos_' +index);
                if(resultadosDiv){
                    resultadosDiv.innerHTML = `
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar </i>
                        </div>
                    `;
                }
                const input = document.getElementById('buscarEquipo_'+index);
                if(input) input.value = '';
                limpiarSeleccionEquipos(index);
            });
        }
        window.seleccionarCliente = function(id,nombre){
            clienteIDSeleccionado = id;
            if(idClienteInput) idClienteInput.value = id;
            if(nombreClienteSpan) nombreClienteSpan.textContent = nombre;
            if(clienteSeleccionadoDiv) clienteSeleccionadoDiv.style.display = 'block';

            if(elementoSeleccionado){
                elementoSeleccionado.classList.remove('cliente-seleccionado');
                const iconAntiguo = elementoSeleccionado.querySelector('.bi-check-circle-fill');
                if(iconAntiguo) iconAntiguo.remove();
            }
            const selectedCard = document.querySelector(`.cliente-card[data-cliente-id="${id}"]`);
            if(selectedCard) {
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
            if(idClienteInput) idClienteInput.value = 0;
            if(nombreClienteSpan) nombreClienteSpan.textContent = '';
            document.querySelectorAll('.equipo-item').forEach((item,index) => {
                const input = document.getElementById('buscarEquipo_'+index);
                if(input) input.value = '';
                limpiarSeleccionEquipos(index);
            });
        };
        function validarFormulario() {
            const equipo = document.getElementById('equipoId_0');
            if(btnGuardar) {
                btnGuardar.disabled = (!equipo || equipo.value === '');
            }
        }
        let timeoutId = null;
        if(buscarClienteInput){
            buscarClienteInput.addEventListener('input',function(){
                clearTimeout(timeoutId);
                const termino = this.value.trim();
                timeoutId=setTimeout(() =>{
                    buscarClientes(termino);
                }, 300);
            });
        }
        document.getElementById('btnLimpiarBusqueda').addEventListener('click',function(){
            buscarClienteInput.value='';
            resultadosDiv.innerHTML = `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"> Escribe al menos 2 caracteres para comenzar a buscar </i>
                </div>
            `;
        })
        if(equipoActualId){
            const equipoActual = todosEquipos.find(e => e.id_equipo == equipoActualId);
            if(equipoActual){
                const infoSpan = document.getElementById('equipoInfo_0');
                if(infoSpan){
                    infoSpan.innerHTML = `<strong>Serie: </strong> ${escapeHtml(equipoActual.no_serie)} | <strong>Modelo: </strong> ${escapeHtml(equipoActual.modelo)}`;
                }
                const infoDiv = document.getElementById('equipoSeleccionado_0');
                if(infoDiv) infoDiv.style.display = 'block';
                const input = document.getElementById('buscarEquipo_0');
                if(input) input.value = equipoActual.no_serie;
                const hidden = document.getElementById('equipoId_0');
                if(hidden) hidden.value = equipoActual.id_equipo;
            }
        }
        validarFormulario();
    </script>
</body>
</html>